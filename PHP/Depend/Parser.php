<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id: Parser.php 675 2009-03-05 07:40:28Z mapi $
 * @link      http://pdepend.org/
 */

require_once 'PHP/Depend/ConstantsI.php';
require_once 'PHP/Depend/BuilderI.php';
require_once 'PHP/Depend/TokenizerI.php';
require_once 'PHP/Depend/Code/Value.php';
require_once 'PHP/Depend/Util/Log.php';
require_once 'PHP/Depend/Util/Type.php';
require_once 'PHP/Depend/Parser/SymbolTable.php';
require_once 'PHP/Depend/Parser/TokenStack.php';
require_once 'PHP/Depend/Parser/MissingValueException.php';
require_once 'PHP/Depend/Parser/TokenStreamEndException.php';
require_once 'PHP/Depend/Parser/UnexpectedTokenException.php';

/**
 * The php source parser.
 *
 * With the default settings the parser includes annotations, better known as
 * doc comment tags, in the generated result. This means it extracts the type
 * information of @var tags for properties, and types in @return + @throws tags
 * of functions and methods. The current implementation tries to ignore all
 * scalar types from <b>boolean</b> to <b>void</b>. You should disable this
 * feature for project that have more or less invalid doc comments, because it
 * could produce invalid results.
 *
 * <code>
 *   $parser->setIgnoreAnnotations();
 * </code>
 *
 * <b>Note</b>: Due to the fact that it is possible to use the same name for
 * multiple classes and interfaces, and there is no way to determine to which
 * package it belongs, while the parser handles class, interface or method
 * signatures, the parser could/will create a code tree that doesn't reflect the
 * real source structure.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Parser implements PHP_Depend_ConstantsI
{
    /**
     * Regular expression for inline type definitions in regular comments. This
     * kind of type is supported by IDEs like Netbeans or eclipse.
     */
    const REGEXP_INLINE_TYPE = '(^\s*/\*\s*
                                 @var\s+
                                   \$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\s+
                                   (.*?)
                                \s*\*/\s*$)ix';

    /**
     * Regular expression for types defined in <b>throws</b> annotations of
     * method or function doc comments.
     */
    const REGEXP_THROWS_TYPE = '(\*\s*
                                 @throws\s+
                                   ([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)
                                )ix';

    /**
     * Regular expression for types defined in annotations like <b>return</b> or
     * <b>var</b> in doc comments of functions and methods.
     */
    const REGEXP_RETURN_TYPE = '(\*\s*
                                 @return\s+
                                  (array\(\s*
                                    (\w+\s*=>\s*)?
                                    ([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\|]*)\s*
                                  \)
                                  |
                                  ([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\|]*))\s+
                                )ix';

    /**
     * Regular expression for types defined in annotations like <b>return</b> or
     * <b>var</b> in doc comments of functions and methods.
     */
    const REGEXP_VAR_TYPE = '(\*\s*
                              @var\s+
                               (array\(\s*
                                 (\w+\s*=>\s*)?
                                 ([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\|]*)\s*
                               \)
                               |
                               ([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\|]*))\s+
                             )ix';

    /**
     * Internal state flag, that will be set to <b>true</b> when the parser has
     * prefixed a qualified name with the actual namespace.
     *
     * @var boolean $_namespacePrefixReplaced
     */
    private $_namespacePrefixReplaced = false;

    /**
     * The name of the last detected namespace.
     *
     * @var string $_namespaceName
     */
    private $_namespaceName = null;

    /**
     * Last parsed package tag.
     *
     * @var string $_packageName
     */
    private $_packageName = self::DEFAULT_PACKAGE;

    /**
     * The package defined in the file level comment.
     *
     * @var string $_globalPackageName
     */
    private $_globalPackageName = self::DEFAULT_PACKAGE;

    /**
     * The used code tokenizer.
     *
     * @var PHP_Depend_TokenizerI $_tokenizer
     */
    private $_tokenizer = null;

    /**
     * The used data structure builder.
     *
     * @var PHP_Depend_BuilderI $_builder
     */
    private $_builder = null;

    /**
     * The currently parsed file instance.
     *
     * @var PHP_Depend_Code_File $_sourceFile
     */
    private $_sourceFile = null;

    /**
     * The symbol table used to handle PHP 5.3 use statements.
     *
     * @var PHP_Depend_Parser_SymbolTable $_useSymbolTable
     */
    private $_useSymbolTable = null;

    /**
     * The last parsed doc comment or <b>null</b>.
     *
     * @var string $_docComment
     */
    private $_docComment = null;

    /**
     * Bitfield of last parsed modifiers.
     *
     * @var integer $_modifiers
     */
    private $_modifiers = 0;

    /**
     * The actually parsed class or interface instance.
     *
     * @var PHP_Depend_Code_AbstractClassOrInterface $_classOrInterface
     */
    private $_classOrInterface = null;

    /**
     * If this property is set to <b>true</b> the parser will ignore all doc
     * comment annotations.
     *
     * @var boolean $_ignoreAnnotations
     */
    private $_ignoreAnnotations = false;

    /**
     * Stack with all active token scopes.
     *
     * @var array(PHP_Depend_Token) $_tokenStack
     */
    private $_tokenStack = null;

    /**
     * Constructs a new source parser.
     *
     * @param PHP_Depend_TokenizerI $tokenizer The used code tokenizer.
     * @param PHP_Depend_BuilderI   $builder   The used node builder.
     */
    public function __construct(
        PHP_Depend_TokenizerI $tokenizer,
        PHP_Depend_BuilderI $builder
    ) {
        $this->_tokenizer = $tokenizer;
        $this->_builder   = $builder;

        $this->_tokenStack     = new PHP_Depend_Parser_TokenStack();
        $this->_useSymbolTable = new PHP_Depend_Parser_SymbolTable(true);
    }

    /**
     * Sets the ignore annotations flag. This means that the parser will ignore
     * doc comment annotations.
     *
     * @return void
     */
    public function setIgnoreAnnotations()
    {
        $this->_ignoreAnnotations = true;
    }

    /**
     * Parses the contents of the tokenizer and generates a node tree based on
     * the found tokens.
     *
     * @return void
     */
    public function parse()
    {
        // Get currently parsed source file
        $this->_sourceFile = $this->_tokenizer->getSourceFile();

        // Debug currently parsed source file.
        PHP_Depend_Util_Log::debug('Processing file ' . $this->_sourceFile);

        $this->_useSymbolTable->createScope();

        $this->reset();

        $tokenType = $this->_tokenizer->peek();
        while ($tokenType !== self::T_EOF) {

            switch ($tokenType) {

            case self::T_COMMENT:
                $this->_consumeToken(self::T_COMMENT);
                break;

            case self::T_DOC_COMMENT:
                $comment = $this->_consumeToken(self::T_DOC_COMMENT)->image;

                $this->_packageName = $this->_parsePackageAnnotation($comment);
                $this->_docComment  = $comment;
                break;

            case self::T_INTERFACE:
                $this->_parseInterfaceDeclaration();
                break;

            case self::T_CLASS:
            case self::T_FINAL:
            case self::T_ABSTRACT:
                $this->_parseClassDeclaration();
                break;

            case self::T_FUNCTION:
                $this->_parseFunctionOrClosureDeclaration();
                break;

            case self::T_USE:
                // Parse a use statement. This method has no return value but it
                // creates a new entry in the symbol map.
                $this->_parseUseDeclarations();
                break;

            case self::T_NAMESPACE:
                $this->_parseNamespaceDeclaration();
                break;

            default:
                // Consume whatever token
                $this->_consumeToken($tokenType);
                $this->reset();
                break;
            }

            $tokenType = $this->_tokenizer->peek();
        }

        $this->_useSymbolTable->destroyScope();
    }

    /**
     * Resets some object properties.
     *
     * @param integer $modifiers Optional default modifiers.
     *
     * @return void
     */
    protected function reset($modifiers = 0)
    {
        $this->_packageName = self::DEFAULT_PACKAGE;
        $this->_docComment  = null;
        $this->_modifiers   = $modifiers;
    }

    /**
     * Parses the dependencies in a interface signature.
     *
     * @return PHP_Depend_Code_Interface
     */
    private function _parseInterfaceDeclaration()
    {
        $this->_tokenStack->push();

        // Consume interface keyword
        $startLine = $this->_consumeToken(self::T_INTERFACE)->startLine;

        // Remove leading comments and get interface name
        $this->_consumeComments();
        $localName = $this->_consumeToken(self::T_STRING)->image;

        $qualifiedName = $this->_createQualifiedTypeName($localName);

        $interface = $this->_builder->buildInterface($qualifiedName);
        $interface->setSourceFile($this->_sourceFile);
        $interface->setDocComment($this->_docComment);
        $interface->setUserDefined();

        // Strip comments and fetch next token type
        $this->_consumeComments();
        $tokenType = $this->_tokenizer->peek();

        // Check for extended interfaces
        if ($tokenType === self::T_EXTENDS) {
            $this->_consumeToken(self::T_EXTENDS);
            $this->_parseInterfaceList($interface);
        }
        // Handle interface body
        $this->_parseClassOrInterfaceBody($interface);

        $interface->setTokens($this->_tokenStack->pop());

        // Reset parser settings
        $this->reset();

        return $interface;
    }

    /**
     * Parses the dependencies in a class signature.
     *
     * @return PHP_Depend_Code_Class
     */
    private function _parseClassDeclaration()
    {
        $this->_tokenStack->push();

        // Parse optional class modifiers
        $startLine = $this->_parseClassModifiers();

        // Consume class keyword and read class start line
        $token = $this->_consumeToken(self::T_CLASS);

        // Check for previous read start line
        if ($startLine === -1) {
            $startLine = $token->startLine;
        }

        // Remove leading comments and get class name
        $this->_consumeComments();
        $localName = $this->_consumeToken(self::T_STRING)->image;

        $qualifiedName = $this->_createQualifiedTypeName($localName);

        $class = $this->_builder->buildClass($qualifiedName);
        $class->setSourceFile($this->_sourceFile);
        $class->setModifiers($this->_modifiers);
        $class->setDocComment($this->_docComment);
        $class->setUserDefined();

        $this->_consumeComments();
        $tokenType = $this->_tokenizer->peek();
        
        if ($tokenType === self::T_EXTENDS) {
            $this->_consumeToken(self::T_EXTENDS);
            $this->_consumeComments();

            $class->setParentClassReference(
                $this->_builder->buildClassReference(
                    $this->_parseQualifiedName()
                )
            );

            $this->_consumeComments();
            $tokenType = $this->_tokenizer->peek();
        }

        if ($tokenType === self::T_IMPLEMENTS) {
            $this->_consumeToken(self::T_IMPLEMENTS);
            $this->_parseInterfaceList($class);
        }
        
        // Handle class body
        $this->_parseClassOrInterfaceBody($class);

        $class->setTokens($this->_tokenStack->pop());

        // Reset parser settings
        $this->reset();

        return $class;
    }

    /**
     * This method parses an optional class modifier. Valid class modifiers are
     * <b>final</b> or <b>abstract</b>. The return value of this method is the
     * start line number of a detected modifier. If no modifier was found, this
     * method will return <b>-1</b>.
     *
     * @return integer
     */
    private function _parseClassModifiers()
    {
        // Strip optional comments
        $this->_consumeComments();
        
        // Get next token type and check for abstract
        $tokenType = $this->_tokenizer->peek();
        if ($tokenType === self::T_ABSTRACT) {
            // Consume abstract keyword and get line number
            $line = $this->_consumeToken(self::T_ABSTRACT)->startLine;
            // Add explicit abstract modifier
            $this->_modifiers |= self::IS_EXPLICIT_ABSTRACT;
        } else if ($tokenType === self::T_FINAL) {
            // Consume final keyword and get line number
            $line = $this->_consumeToken(self::T_FINAL)->startLine;
            // Add final modifier
            $this->_modifiers |= self::IS_FINAL;
        } else {
            $line = -1;
        }
        
        // Strip optional comments
        $this->_consumeComments();

        return $line;
    }

    /**
     * This method parses a list of interface names as used in the <b>extends</b>
     * part of a interface declaration or in the <b>implements</b> part of a
     * class declaration.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $abstractType The declaring
     *        type instance.
     *
     * @return void
     */
    private function _parseInterfaceList(
        PHP_Depend_Code_AbstractClassOrInterface $abstractType
    ) {
        while (true) {
            $this->_consumeComments();

            $abstractType->addInterfaceReference(
                $this->_builder->buildInterfaceReference(
                    $this->_parseQualifiedName()
                )
            );

            $this->_consumeComments();

            $tokenType = $this->_tokenizer->peek();

            // Check for opening interface body
            if ($tokenType === self::T_CURLY_BRACE_OPEN) {
                break;
            }

            $this->_consumeToken(self::T_COMMA);
            $this->_consumeComments();
        }
    }

    /**
     * Parses a class/interface body.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $type The context class
     *        or interface instance.
     *
     * @return void
     */
    private function _parseClassOrInterfaceBody(
        PHP_Depend_Code_AbstractClassOrInterface $type
    ) {
        $this->_classOrInterface = $type;

        // Consume comments and read opening curly brace
        $this->_consumeComments();
        $this->_consumeToken(self::T_CURLY_BRACE_OPEN);

        $defaultModifier = self::IS_PUBLIC;
        if ($type instanceof PHP_Depend_Code_Interface) {
            $defaultModifier |= self::IS_ABSTRACT;
        }
        $this->reset();

        $tokenType = $this->_tokenizer->peek();

        while ($tokenType !== self::T_EOF) {

            switch ($tokenType) {

            case self::T_ABSTRACT:
            case self::T_PUBLIC:
            case self::T_PRIVATE:
            case self::T_PROTECTED:
            case self::T_STATIC:
            case self::T_FINAL:
            case self::T_FUNCTION:
            case self::T_VARIABLE:
            case self::T_VAR:

                $methodOrProperty = $this->_parseMethodOrPropertyDeclaration(
                    $defaultModifier
                );

                if ($methodOrProperty instanceof PHP_Depend_Code_Method) {
                    $type->addMethod($methodOrProperty);
                } else {
                    $type->addChild($methodOrProperty);
                }
                
                $this->reset();
                break;

            case self::T_CONST:
                $type->addConstant($this->_parseTypeConstant());
                $this->reset();
                break;

            case self::T_CURLY_BRACE_CLOSE:
                $this->_consumeToken(self::T_CURLY_BRACE_CLOSE);

                $this->reset();

                // Reset context class or interface instance
                $this->_classOrInterface = null;

                // Stop processing
                return;

            case self::T_COMMENT:
                $this->_consumeToken(self::T_COMMENT);
                break;

            case self::T_DOC_COMMENT:
                // Read comment token
                $token = $this->_consumeToken(self::T_DOC_COMMENT);

                $this->_docComment = $token->image;
                break;

            default:
                throw new PHP_Depend_Parser_UnexpectedTokenException(
                    $this->_tokenizer
                );
            }

            $tokenType = $this->_tokenizer->peek();
        }

        throw new PHP_Depend_Parser_TokenStreamEndException($this->_tokenizer);
    }

    /**
     * This method will parse a list of modifiers and a following property or
     * method.
     *
     * @param integer $modifiers Optional default modifiers for the property
     *        or method node that will be parsed.
     *
     * @return PHP_Depend_Code_Method|PHP_Depend_Code_Property
     * @since 0.9.6
     */
    private function _parseMethodOrPropertyDeclaration($modifiers = 0)
    {
        $this->_tokenStack->push();

        $tokenType = $this->_tokenizer->peek();
        while ($tokenType !== self::T_EOF) {
            switch ($tokenType) {

            case self::T_PRIVATE:
                $modifiers |= self::IS_PRIVATE;
                $modifiers = $modifiers & ~self::IS_PUBLIC;
                break;

            case self::T_PROTECTED:
                $modifiers |= self::IS_PROTECTED;
                $modifiers = $modifiers & ~self::IS_PUBLIC;
                break;

            case self::T_VAR:
            case self::T_PUBLIC:
                $modifiers |= self::IS_PUBLIC;
                break;

            case self::T_STATIC:
                $modifiers |= self::IS_STATIC;
                break;

            case self::T_ABSTRACT:
                $modifiers |= self::IS_ABSTRACT;
                break;

            case self::T_FINAL:
                $modifiers |= self::IS_FINAL;
                break;

            case self::T_FUNCTION:
                $method = $this->_parseMethodDeclaration();
                $method->setModifiers($modifiers);
                $method->setTokens($this->_tokenStack->pop());
                return $method;

            case self::T_VARIABLE:
                $declaration = $this->_parseFieldDeclaration();
                $declaration->setModifiers($modifiers);
                $declaration->setTokens($this->_tokenStack->pop());

                return $declaration;
/*
                x
                $property = $this->_parsePropertyDeclaration();
                $property->setModifiers($modifiers);
                $property->setTokens($this->_tokenStack->pop());

                $this->_consumeComments();
                $this->_consumeToken(self::T_SEMICOLON);

                return $property;
*/
            default:
                break 2;
            }

            $this->_consumeToken($tokenType);
            $this->_consumeComments();
            
            $tokenType = $this->_tokenizer->peek();
        }
        throw new PHP_Depend_Parser_UnexpectedTokenException($this->_tokenizer);
    }

    /**
     * This method will parse a class field declaration with all it's variables.
     *
     * <code>
     * // Simple field declaration
     * class Foo {
     *     protected $foo;
     * }
     *
     * // Field declaration with multiple properties
     * class Foo {
     *     protected $foo = 23
     *               $bar = 42,
     *               $baz = null;
     * }
     * </code>
     *
     * @return PHP_Depend_Code_FieldDeclaration
     * @since 0.9.6
     */
    private function _parseFieldDeclaration()
    {
        $declaration = $this->_builder->buildFieldDeclaration();
        $declaration->setComment($this->_docComment);
        $declaration->setClassOrInterfaceReference(
            $this->_parseFieldDeclarationClassOrInterfaceReference()
        );
        
        $this->_consumeComments();
        $tokenType = $this->_tokenizer->peek();
        
        while ($tokenType !== self::T_EOF) {

            $declaration->addChild(
                $this->_parseVariableDeclarator()
            );

            $this->_consumeComments();
            $tokenType = $this->_tokenizer->peek();

            if ($tokenType !== self::T_COMMA) {
                break;
            }
            $this->_consumeToken(self::T_COMMA);

            $this->_consumeComments();
            $tokenType = $this->_tokenizer->peek();
        }
        $this->_consumeToken(self::T_SEMICOLON);

        return $declaration;
    }

    /**
     * This method parses a simple function or a PHP 5.3 lambda function or
     * closure.
     *
     * @return PHP_Depend_Code_AbstractCallable
     * @since 0.9.5
     */
    private function _parseFunctionOrClosureDeclaration()
    {
        $this->_tokenStack->push();

        // Read function keyword
        $this->_consumeToken(self::T_FUNCTION);

        // Remove leading comments
        $this->_consumeComments();

        // Check for closure or function
        if ($this->_tokenizer->peek() === self::T_PARENTHESIS_OPEN) {
            $callable = $this->_parseClosureDeclaration();
        } else {
            $callable = $this->_parseFunctionDeclaration();
        }

        $callable->setSourceFile($this->_sourceFile);
        $callable->setDocComment($this->_docComment);
        $callable->setTokens($this->_tokenStack->pop());
        $this->_prepareCallable($callable);

        $this->reset();

        return $callable;
    }

    /**
     * This method parses a function declaration.
     *
     * @return PHP_Depend_Code_Function
     * @since 0.9.5
     */
    private function _parseFunctionDeclaration()
    {
        // Remove leading comments
        $this->_consumeComments();

        $returnsReference = false;
        
        // Check for returns reference token
        if ($this->_tokenizer->peek() === self::T_BITWISE_AND) {
            $this->_consumeToken(self::T_BITWISE_AND);
            $this->_consumeComments();

            $returnsReference = true;
        }

        // Next token must be the function identifier
        $functionName = $this->_consumeToken(self::T_STRING)->image;

        $function = $this->_builder->buildFunction($functionName);
        $this->_parseCallableDeclaration($function);

        if ($returnsReference === true) {
            $function->setReturnsReference();
        }

        // First check for an existing namespace
        if ($this->_namespaceName !== null) {
            $packageName = $this->_namespaceName;
        } else if ($this->_packageName !== self::DEFAULT_PACKAGE) {
            $packageName = $this->_packageName;
        } else {
            $packageName = $this->_globalPackageName;
        }
        $this->_builder->buildPackage($packageName)->addFunction($function);

        return $function;
    }

    /**
     * This method parses a method declaration.
     *
     * @return PHP_Depend_Code_Method
     * @since 0.9.5
     */
    private function _parseMethodDeclaration()
    {
        // Read function keyword
        $this->_consumeToken(self::T_FUNCTION);
        $this->_consumeComments();

        $returnsReference = false;

        // Check for returns reference token
        if ($this->_tokenizer->peek() === self::T_BITWISE_AND) {
            $this->_consumeToken(self::T_BITWISE_AND);
            $this->_consumeComments();

            $returnsReference = true;
        }

        // Next token must be the function identifier
        $methodName = $this->_consumeToken(self::T_STRING)->image;

        $method = $this->_builder->buildMethod($methodName);
        $method->setDocComment($this->_docComment);
        $method->setSourceFile($this->_sourceFile);

        $this->_parseCallableDeclaration($method);
        $this->_prepareCallable($method);

        if ($returnsReference === true) {
            $method->setReturnsReference();
        }

        return $method;
    }

    /**
     * This method parses a PHP 5.3 closure or lambda function.
     *
     * @return PHP_Depend_Code_Closure
     * @since 0.9.5
     */
    private function _parseClosureDeclaration()
    {
        $closure = $this->_builder->buildClosure();

        $this->_parseParameterList($closure);

        $this->_consumeComments();
        if ($this->_tokenizer->peek() === self::T_USE) {
            $this->_parseBoundVariables($closure);
        }
        
        $this->_parseCallableBody($closure);

        return $closure;
    }

    /**
     * Parses a function or a method and adds it to the parent context node.
     *
     * @param PHP_Depend_Code_AbstractCallable $callable The context callable.
     *
     * @return void
     */
    private function _parseCallableDeclaration(
        PHP_Depend_Code_AbstractCallable $callable
    ) {
        $this->_parseParameterList($callable);
        $this->_consumeComments();
        
        if ($this->_tokenizer->peek() === self::T_CURLY_BRACE_OPEN) {
            // Get function body dependencies
            $this->_parseCallableBody($callable);
        } else {
            $this->_consumeToken(self::T_SEMICOLON);
        }
    }

    /**
     * Extracts all dependencies from a callable signature.
     *
     * @param PHP_Depend_Code_AbstractCallable $function The context callable.
     *
     * @return void
     * @since 0.9.5
     */
    private function _parseParameterList(
        PHP_Depend_Code_AbstractCallable $function
    ) {
        $this->_consumeComments();
        $this->_consumeToken(self::T_PARENTHESIS_OPEN);
        $this->_consumeComments();

        $tokenType = $this->_tokenizer->peek();

        // Check for function without parameters
        if ($tokenType === self::T_PARENTHESIS_CLOSE) {
            $this->_consumeToken(self::T_PARENTHESIS_CLOSE);
            return;
        }

        $position   = 0;
        $parameters = array();

        while ($tokenType !== self::T_EOF) {

            // Create a new token stack instance
            $this->_tokenStack->push();

            $parameter = $this->_parseParameter();
            $parameter->setPosition(count($parameters));

            // Destroy actual token scope
            $parameter->setTokens($this->_tokenStack->pop());

            // Add new parameter to function
            $function->addParameter($parameter);

            // Store parameter for later isOptional calculation.
            $parameters[] = $parameter;

            $this->_consumeComments();

            $tokenType = $this->_tokenizer->peek();

            // Check for following parameter
            if ($tokenType !== self::T_COMMA) {
                break;
            }

            // It must be a comma
            $this->_consumeToken(self::T_COMMA);
        }

        $optional = true;
        foreach (array_reverse($parameters) as $parameter) {
            if ($parameter->isDefaultValueAvailable() === false) {
                $optional = false;
            }
            $parameter->setOptional($optional);
        }

        $this->_consumeToken(self::T_PARENTHESIS_CLOSE);
    }

    /**
     * This method parses a single function or method parameter and returns the
     * corresponding ast instance. Additionally this method fills the tokens
     * array with all found tokens.
     * 
     * @return PHP_Depend_Code_Parameter
     */
    private function _parseParameter()
    {
        $parameterRef   = false;
        $parameterType  = null;
        $parameterArray = false;
        $parameterClass = null;

        $this->_consumeComments();
        $tokenType = $this->_tokenizer->peek();

        // Check for class/interface type hint
        if ($tokenType === self::T_STRING || $tokenType === self::T_BACKSLASH) {
            // Get type identifier
            $parameterType = $this->_parseQualifiedName();

            // Remove ending comments
            $this->_consumeComments();

            // Get next token type
            $tokenType = $this->_tokenizer->peek();
        } else if ($tokenType === self::T_ARRAY) {
            // Mark as array parameter
            $parameterArray = true;

            // Consume array token and remove comments
            $this->_consumeToken(self::T_ARRAY);
            $this->_consumeComments();

            // Get next token type
            $tokenType = $this->_tokenizer->peek();
        } else if ($tokenType === self::T_SELF) {
            // TODO: Question: NO STATIC???
            // || $tokenType === self::T_STATIC
            //
            // Consume token and remove comments
            $this->_consumeToken($tokenType);
            $this->_consumeComments();

            // Store actual context class as parameter type
            $parameterClass = $this->_classOrInterface;
        }

        // Check for parameter by reference
        if ($tokenType === self::T_BITWISE_AND) {
            // Set by ref flag
            $parameterRef = true;

            // Consume bitwise and token
            $this->_consumeToken(self::T_BITWISE_AND);
            $this->_consumeComments();

            // Get next token type
            $tokenType = $this->_tokenizer->peek();
        }

        // Next token must be the parameter variable
        $token = $this->_consumeToken(self::T_VARIABLE);
        $this->_consumeComments();

        $parameter = $this->_builder->buildParameter($token->image);
        $parameter->setPassedByReference($parameterRef);
        $parameter->setArray($parameterArray);

        if ($parameterType !== null) {
            $parameter->setClassReference(
                $this->_builder->buildClassOrInterfaceReference($parameterType)
            );
        } else if ($parameterClass !== null) {
            $parameter->setClass($parameterClass);
        }

        // Check for a default value
        if ($this->_tokenizer->peek() === self::T_EQUAL) {
            $this->_consumeToken(self::T_EQUAL);
            $this->_consumeComments();

            $parameter->setValue($this->_parseStaticValueOrStaticArray());
        }

        return $parameter;
    }

    /**
     * Extracts all dependencies from a callable body.
     *
     * @param PHP_Depend_Code_AbstractCallable $callable The context callable.
     *
     * @return void
     */
    private function _parseCallableBody(
        PHP_Depend_Code_AbstractCallable $callable
    ) {
        $this->_useSymbolTable->createScope();
        
        $curly = 0;

        $tokenType = $this->_tokenizer->peek();

        while ($tokenType !== self::T_EOF) {

            switch ($tokenType) {
        
            case self::T_CATCH:
                // Consume catch keyword and the opening parenthesis
                $this->_consumeToken(self::T_CATCH);
                $this->_consumeComments();
                $this->_consumeToken(self::T_PARENTHESIS_OPEN);

                $callable->addDependencyClassReference(
                    $this->_builder->buildClassOrInterfaceReference(
                        $this->_parseQualifiedName()
                    )
                );
                break;

            case self::T_NEW:
                // Consume the
                $this->_consumeToken(self::T_NEW);
                $this->_consumeComments();

                // Peek next token and look for a static type identifier
                $peekType = $this->_tokenizer->peek();

                // If this is a dynamic instantiation, do not add dependency.
                // Something like: $bar instanceof $className
                if ($peekType === self::T_STRING
                    || $peekType === self::T_BACKSLASH
                    || $peekType === self::T_NAMESPACE
                ) {
                    $callable->addDependencyClassReference(
                        $this->_builder->buildClassReference(
                            $this->_parseQualifiedName()
                        )
                    );
                }
                break;

            case self::T_INSTANCEOF:
                $this->_consumeToken(self::T_INSTANCEOF);
                $this->_consumeComments();

                // Peek next token and look for a static type identifier
                $peekType = $this->_tokenizer->peek();

                // If this is a dynamic instantiation, do not add dependency.
                // Something like: $bar instanceof $className
                if ($peekType === self::T_STRING
                    || $peekType === self::T_BACKSLASH
                    || $peekType === self::T_NAMESPACE
                ) {
                    $callable->addDependencyClassReference(
                        $this->_builder->buildClassOrInterfaceReference(
                            $this->_parseQualifiedName()
                        )
                    );
                }
                break;

            case self::T_STRING:
            case self::T_BACKSLASH:
            case self::T_NAMESPACE:
                $qualifiedName = $this->_parseQualifiedName();

                // Remove comments
                $this->_consumeComments();

                // Test for static method, property or constant access
                if ($this->_tokenizer->peek() !== self::T_DOUBLE_COLON) {
                    break;
                }

                // Consume double colon and optional comments
                $this->_consumeToken(self::T_DOUBLE_COLON);
                $this->_consumeComments();

                // Get next token type
                $tokenType = $this->_tokenizer->peek();

                // T_STRING == method or constant, T_VARIABLE == property
                if ($tokenType === self::T_STRING
                    || $tokenType === self::T_VARIABLE
                ) {
                    $this->_consumeToken($tokenType);

                    $callable->addDependencyClassReference(
                        $this->_builder->buildClassOrInterfaceReference(
                            $qualifiedName
                        )
                    );
                }
                break;

            case self::T_CURLY_BRACE_OPEN:
                $this->_consumeToken(self::T_CURLY_BRACE_OPEN);
                ++$curly;
                break;

            case self::T_CURLY_BRACE_CLOSE:
                $this->_consumeToken(self::T_CURLY_BRACE_CLOSE);
                --$curly;
                break;

            case self::T_DOUBLE_QUOTE:
                $this->_consumeToken(self::T_DOUBLE_QUOTE);
                $this->_skipEncapsultedBlock(self::T_DOUBLE_QUOTE);
                break;

            case self::T_STATIC:
                $declaration = $this->_parseStaticVariableDeclaration();
                if ($declaration !== null) {
                    $callable->addChild($declaration);
                }
                break;

            case self::T_BACKTICK:
                $this->_consumeToken(self::T_BACKTICK);
                $this->_skipEncapsultedBlock(self::T_BACKTICK);
                break;

            case self::T_FUNCTION:
                $this->_parseFunctionOrClosureDeclaration();
                break;

            case self::T_COMMENT:
                $token = $this->_consumeToken(self::T_COMMENT);

                // Check for inline type definitions like: /* @var $o FooBar */
                if (preg_match(self::REGEXP_INLINE_TYPE, $token->image, $match)) {
                    $callable->addDependencyClassReference(
                        $this->_builder->buildClassOrInterfaceReference($match[1])
                    );
                }
                break;

            default:
                $this->_consumeToken($tokenType);
                break;
            }

            if ($curly === 0) {
                $this->_useSymbolTable->destroyScope();

                // Stop processing
                return;
            }

            $tokenType = $this->_tokenizer->peek();
        }

        throw new PHP_Depend_Parser_TokenStreamEndException($this->_tokenizer);
    }

    /**
     * Parses a list of bound closure variables.
     *
     * @param PHP_Depend_Code_Closure $closure The parent closure instance.
     *
     * @return void
     * @since 0.9.5
     */
    private function _parseBoundVariables(
        PHP_Depend_Code_Closure $closure
    ) {
        // Consume use keyword
        $this->_consumeComments();
        $this->_consumeToken(self::T_USE);

        // Consume opening parenthesis
        $this->_consumeComments();
        $this->_consumeToken(self::T_PARENTHESIS_OPEN);

        while ($this->_tokenizer->peek() !== self::T_EOF) {
            // Consume leading comments
            $this->_consumeComments();

            // Check for by-ref operator
            if ($this->_tokenizer->peek() === self::T_BITWISE_AND) {
                $this->_consumeToken(self::T_BITWISE_AND);
                $this->_consumeComments();
            }

            // Read bound variable
            $this->_consumeToken(self::T_VARIABLE);
            $this->_consumeComments();

            // Check for further bound variables
            if ($this->_tokenizer->peek() === self::T_COMMA) {
                $this->_consumeToken(self::T_COMMA);
                continue;
            }
            break;
        }

        // Consume closing parenthesis
        $this->_consumeComments();
        $this->_consumeToken(self::T_PARENTHESIS_CLOSE);
    }

    /**
     * Parses a php class/method name chain.
     *
     * <code>
     * PHP\Depend\Parser::parse();
     * </code>
     *
     * @return string
     */
    private function _parseQualifiedName()
    {
        $fragments = $this->_parseQualifiedNameRaw();
        
        // Check for fully qualified name
        if ($fragments[0] === '\\') {
            return join('', $fragments);
        }

        // Search for an use alias
        $mapsTo = $this->_useSymbolTable->lookup($fragments[0]);
        if ($mapsTo !== null) {
            // Remove alias and add real namespace
            array_shift($fragments);
            array_unshift($fragments, $mapsTo);
        } else if ($this->_namespaceName !== null 
            && $this->_namespacePrefixReplaced === false
        ) {
            // Prepend current namespace
            array_unshift($fragments, $this->_namespaceName, '\\');
        }
        return join('', $fragments);
    }

    /**
     * This method parses a qualified PHP 5.3 class, interface and namespace
     * identifier and returns the collected tokens as a string array.
     *
     * @return array(string)
     * @since 0.9.5
     */
    private function _parseQualifiedNameRaw()
    {
        // Reset namespace prefix flag
        $this->_namespacePrefixReplaced = false;

        // Consume comments and fetch first token type
        $this->_consumeComments();
        $tokenType = $this->_tokenizer->peek();

        $qualifiedName = array();

        // Check for local name
        if ($tokenType === self::T_STRING) {
            $qualifiedName[] = $this->_consumeToken(self::T_STRING)->image;

            $this->_consumeComments();
            $tokenType = $this->_tokenizer->peek();

            // Stop here for simple identifier
            if ($tokenType !== self::T_BACKSLASH) {
                return $qualifiedName;
            }
        } else if ($tokenType === self::T_NAMESPACE) {
            // Consume namespace keyword
            $this->_consumeToken(self::T_NAMESPACE);
            $this->_consumeComments();

            // Add current namespace as first token
            $qualifiedName = array((string) $this->_namespaceName);

            // Set prefixed flag to true
            $this->_namespacePrefixReplaced = true;
        }

        do {
            // Next token must be a namespace separator
            $this->_consumeToken(self::T_BACKSLASH);
            $this->_consumeComments();

            // Next token must be a namespace identifier
            $token = $this->_consumeToken(self::T_STRING);
            $this->_consumeComments();

            // Append to qualified name
            $qualifiedName[] = '\\';
            $qualifiedName[] = $token->image;

            // Get next token type
            $tokenType = $this->_tokenizer->peek();
        } while ($tokenType === self::T_BACKSLASH);

        return $qualifiedName;
    }

    /**
     * This method parses a PHP 5.3 namespace declaration.
     *
     * @return void
     * @since 0.9.5
     */
    private function _parseNamespaceDeclaration()
    {
        // Consume namespace keyword and strip optional comments
        $this->_consumeToken(self::T_NAMESPACE);
        $this->_consumeComments();

        // Lookup next token type
        $tokenType = $this->_tokenizer->peek();

        // Search for a namespace identifier
        if ($tokenType === self::T_STRING) {
            // Reset namespace property
            $this->_namespaceName = null;

            // Read qualified namespace identifier
            $qualifiedName = $this->_parseQualifiedName();

            // Consume optional comments an check for namespace scope
            $this->_consumeComments();

            if ($this->_tokenizer->peek() === self::T_CURLY_BRACE_OPEN) {
                // Consume opening curly brace
                $this->_consumeToken(self::T_CURLY_BRACE_OPEN);
            } else {
                // Consume closing semicolon token
                $this->_consumeToken(self::T_SEMICOLON);
            }

            // Create a package for this namespace
            $this->_namespaceName = $qualifiedName;
            $this->_builder->buildPackage($qualifiedName);
        } else if ($tokenType === self::T_BACKSLASH) {
            // Same namespace reference, something like:
            //   new namespace\Foo();
            // or:
            //   $x = namespace\foo::bar();

            // Now parse a qualified name
            $this->_parseQualifiedNameRaw();
        } else {
            // Consume opening curly brace
            $this->_consumeToken(self::T_CURLY_BRACE_OPEN);

            // Create a package for this namespace
            $this->_namespaceName = '';
            $this->_builder->buildPackage('');
        }

        $this->reset();
    }

    /**
     * This method parses a list of PHP 5.3 use declarations and adds a mapping
     * between short name and full qualified name to the use symbol table.
     *
     * <code>
     * use \foo\bar as fb,
     *     \foobar\Bar;
     * </code>
     *
     * @return void
     * @since 0.9.5
     */
    private function _parseUseDeclarations()
    {
        // Consume use keyword
        $this->_consumeToken(self::T_USE);
        $this->_consumeComments();

        // Parse all use declarations
        $this->_parseUseDeclaration();
        $this->_consumeComments();

        // Consume closing semicolon
        $this->_consumeToken(self::T_SEMICOLON);

        // Reset any previous state
        $this->reset();
    }

    /**
     * This method parses a single use declaration and adds a mapping between
     * short name and full qualified name to the use symbol table. 
     *
     * @return void
     * @since 0.9.5
     */
    private function _parseUseDeclaration()
    {
        $fragments = $this->_parseQualifiedNameRaw();
        $this->_consumeComments();

        if ($this->_tokenizer->peek() === self::T_AS) {
            $this->_consumeToken(self::T_AS);
            $this->_consumeComments();

            $image = $this->_consumeToken(self::T_STRING)->image;
            $this->_consumeComments();
        } else {
            $image = end($fragments);
        }

        // Add mapping between image and qualified name to symbol table
        $this->_useSymbolTable->add($image, join('', $fragments));

        // Check for a following use declaration
        if ($this->_tokenizer->peek() === self::T_COMMA) {
            // Consume comma token and comments
            $this->_consumeToken(self::T_COMMA);
            $this->_consumeComments();

            $this->_parseUseDeclaration();
        }
    }

    /**
     * This method parses a class or interface constant.
     *
     * @return PHP_Depend_Code_Constant
     * @since 0.9.5
     */
    private function _parseTypeConstant()
    {
        $this->_tokenStack->push();

        // Consume const keyword
        $this->_consumeToken(self::T_CONST);

        // Remove leading comments and read constant name
        $this->_consumeComments();
        $token = $this->_consumeToken(self::T_STRING);

        $constant = $this->_builder->buildTypeConstant($token->image);
        $constant->setDocComment($this->_docComment);
        $constant->setSourceFile($this->_sourceFile);

        $this->_consumeComments();
        $this->_consumeToken(self::T_EQUAL);

        $this->_parseStaticValue();

        $constant->setTokens($this->_tokenStack->pop());

        $this->_consumeComments();
        $this->_consumeToken(self::T_SEMICOLON);

        return $constant;
    }

    /**
     * This method will parse a static variable declaration.
     *
     * <code>
     * function foo()
     * {
     *     // First declaration
     *     static $foo;
     *     // Second declaration
     *     static $bar = array();
     *     // Third declaration
     *     static $baz    = array(),
     *            $foobar = null,
     *            $barbaz;
     * }
     * </code>
     *
     * @return PHP_Depend_Code_StaticVariableDeclaration
     * @since 0.9.6
     */
    private function _parseStaticVariableDeclaration()
    {
        $this->_tokenStack->push();

        $token = $this->_consumeToken(self::T_STATIC);
        $this->_consumeComments();

        $tokenType = $this->_tokenizer->peek();
        if ($tokenType === self::T_PARENTHESIS_OPEN
            || $tokenType === self::T_DOUBLE_COLON
        ) {
            $this->_tokenStack->pop();
            return;
        }

        $staticDeclaration = $this->_builder->buildStaticVariableDeclaration(
            $token->image
        );

        while ($tokenType !== self::T_EOF) {
            $staticDeclaration->addChild(
                $this->_parseVariableDeclarator()
            );
            
            $this->_consumeComments();

            // Semicolon terminates static declaration
            $tokenType = $this->_tokenizer->peek();
            if ($tokenType === self::T_SEMICOLON) {
                break;
            }
            // We are here, so there must be a next declarator
            $this->_consumeToken(self::T_COMMA);
        }

        $staticDeclaration->setTokens($this->_tokenStack->pop());

        return $staticDeclaration;
    }

    /**
     * This method will parse a variable declarator.
     *
     * <code>
     * // Parameter declarator
     * function foo($x = 23) {
     * }
     * // Property declarator
     * class Foo{
     *     protected $bar = 42;
     * }
     * // Static declarator
     * function baz() {
     *     static $foo;
     * }
     * </code>
     *
     * @return PHP_Depend_Code_VariableDeclarator
     * @since 0.9.6
     */
    private function _parseVariableDeclarator()
    {
        $this->_consumeComments();

        $this->_tokenStack->push();

        $name = $this->_consumeToken(self::T_VARIABLE)->image;
        $this->_consumeComments();

        $declarator = $this->_builder->buildVariableDeclarator($name);

        if ($this->_tokenizer->peek() === self::T_EQUAL) {
            $this->_consumeToken(self::T_EQUAL);
            $this->_consumeComments();

            $declarator->setValue($this->_parseStaticValueOrStaticArray());
        }

        $declarator->setTokens($this->_tokenStack->pop());

        return $declarator;
    }

    /**
     * This method will parse a static value or a static array as it is
     * used as default value for a parameter or property declaration.
     *
     * @return PHP_Depend_Code_Value
     * @since 0.9.6
     */
    private function _parseStaticValueOrStaticArray()
    {
        $this->_consumeComments();
        if ($this->_tokenizer->peek() === self::T_ARRAY) {
            return $this->_parseStaticArray();
        }
        return $this->_parseStaticValue();
    }

    /**
     * This method will parse a static default value as it is used for a
     * parameter, property or constant declaration.
     *
     * @return PHP_Depend_Code_Value
     * @since 0.9.5
     */
    private function _parseStaticValue()
    {
        $defaultValue = new PHP_Depend_Code_Value();

        $this->_consumeComments();

        // By default all parameters positive signed
        $signed = 1;

        $tokenType = $this->_tokenizer->peek();
        while ($tokenType !== self::T_EOF) {

            switch ($tokenType) {

            case self::T_COMMA:            
            case self::T_SEMICOLON:
            case self::T_PARENTHESIS_CLOSE:
                if ($defaultValue->isValueAvailable() === true) {
                    return $defaultValue;
                }
                throw new PHP_Depend_Parser_MissingValueException($this->_tokenizer);

            case self::T_NULL:
                $token = $this->_consumeToken(self::T_NULL);
                $defaultValue->setValue(null);
                break;

            case self::T_TRUE:
                $token = $this->_consumeToken(self::T_TRUE);
                $defaultValue->setValue(true);
                break;

            case self::T_FALSE:
                $token = $this->_consumeToken(self::T_FALSE);
                $defaultValue->setValue(false);
                break;

            case self::T_LNUMBER:
                $token = $this->_consumeToken(self::T_LNUMBER);
                $defaultValue->setValue($signed * (int) $token->image);
                break;

            case self::T_DNUMBER:
                $token = $this->_consumeToken(self::T_DNUMBER);
                $defaultValue->setValue($signed * (double) $token->image);
                break;

            case self::T_CONSTANT_ENCAPSED_STRING:
                $token = $this->_consumeToken(self::T_CONSTANT_ENCAPSED_STRING);
                $defaultValue->setValue(substr($token->image, 1, -1));
                break;

            case self::T_DOUBLE_COLON:
                $this->_consumeToken(self::T_DOUBLE_COLON);
                break;

            case self::T_PLUS:
                $this->_consumeToken(self::T_PLUS);
                break;

            case self::T_MINUS:
                $this->_consumeToken(self::T_MINUS);
                $signed *= -1;
                break;

            case self::T_DIR:
            case self::T_FILE:
            case self::T_LINE:
            case self::T_SELF:
            case self::T_NS_C:
            case self::T_FUNC_C:
            case self::T_PARENT:
            case self::T_STRING:
            case self::T_STATIC:
            case self::T_CLASS_C:
            case self::T_METHOD_C:
            case self::T_BACKSLASH:
            
                // There is a default value but we don't handle it at the moment.
                $defaultValue->setValue(null);
                $this->_consumeToken($tokenType);
                break;

            default:
                throw new PHP_Depend_Parser_UnexpectedTokenException(
                    $this->_tokenizer
                );
            }
            
            $this->_consumeComments();

            $tokenType = $this->_tokenizer->peek();
        }

        // We should never reach this, so throw an exception
        throw new PHP_Depend_Parser_TokenStreamEndException($this->_tokenizer);
    }

    /**
     * This method parses an array as it is used for for parameter or property
     * default values.
     *
     * Note: At the moment the implementation of this method only returns an
     *       empty array, but consumes all tokens that belong to the array
     *       declaration.
     *
     * TODO: Implement array content/value handling, but how should we handle
     *       constant values like array(self::FOO, FOOBAR)?
     *
     * @return array
     * @since 0.9.5
     */
    private function _parseStaticArray()
    {
        $staticValue = array();

        // Fetch all tokens that belong to this array
        $this->_consumeToken(self::T_ARRAY);
        $this->_consumeComments();
        $this->_consumeToken(self::T_PARENTHESIS_OPEN);

        $parenthesis = 1;

        $tokenType = $this->_tokenizer->peek();
        while ($tokenType !== self::T_EOF) {

            switch ($tokenType) {

            case self::T_PARENTHESIS_CLOSE:
                if (--$parenthesis === 0) {
                    break 2;
                }
                $this->_consumeToken(self::T_PARENTHESIS_CLOSE);
                break;

            case self::T_PARENTHESIS_OPEN:
                $this->_consumeToken(self::T_PARENTHESIS_OPEN);
                ++$parenthesis;
                break;

            case self::T_DIR:
            case self::T_NULL:
            case self::T_TRUE:
            case self::T_FILE:
            case self::T_LINE:
            case self::T_NS_C:
            case self::T_PLUS:
            case self::T_SELF:
            case self::T_ARRAY:
            case self::T_FALSE:
            case self::T_EQUAL:
            case self::T_COMMA:
            case self::T_MINUS:
            case self::T_COMMENT:
            case self::T_DOC_COMMENT:
            case self::T_DOUBLE_COLON:
            case self::T_STRING:
            case self::T_BACKSLASH:
            case self::T_DNUMBER:
            case self::T_LNUMBER:
            case self::T_FUNC_C:
            case self::T_METHOD_C:
            case self::T_STATIC:
            case self::T_PARENT:
            case self::T_NUM_STRING:
            case self::T_DOUBLE_ARROW:
            case self::T_CONSTANT_ENCAPSED_STRING:
                $this->_consumeToken($tokenType);
                break;

            default:
                break 2;
            }

            $tokenType = $this->_tokenizer->peek();
        }

        // Read closing parenthesis
        $this->_consumeToken(self::T_PARENTHESIS_CLOSE);

        $defaultValue = new PHP_Depend_Code_Value();
        $defaultValue->setValue($staticValue);

        return $defaultValue;
    }

    /**
     * This method creates a qualified class or interface name based on the
     * current parser state. By default method uses the current namespace scope
     * as prefix for the given local name. And it will fallback to a previously
     * parsed package annotation, when no namespace declaration was parsed.
     *
     * @param string $localName The local class or interface name.
     *
     * @return string
     */
    private function _createQualifiedTypeName($localName)
    {
        $separator = '\\';
        $namespace = $this->_namespaceName;

        if ($namespace === null) {
            $separator = self::PACKAGE_SEPARATOR;
            $namespace = $this->_packageName;
        }

        return $namespace . $separator . $localName;
    }

    /**
     * Extracts the @package information from the given comment.
     *
     * @param string $comment A doc comment block.
     *
     * @return string
     */
    private function _parsePackageAnnotation($comment)
    {
        $package = self::DEFAULT_PACKAGE;
        if (preg_match('#\*\s*@package\s+(.*)#', $comment, $match)) {
            $package = trim($match[1]);
            if (preg_match('#\*\s*@subpackage\s+(.*)#', $comment, $match)) {
                $package .= self::PACKAGE_SEPARATOR . trim($match[1]);
            }
        }

        // Check for doc level comment
        if ($this->_globalPackageName === self::DEFAULT_PACKAGE
            && $this->isFileComment() === true
        ) {
            $this->_globalPackageName = $package;

            $this->_sourceFile->setDocComment($comment);
        }
        return $package;
    }

    /**
     * Checks that the current token could be used as file comment.
     *
     * This method checks that the previous token is an open tag and the following
     * token is not a class, a interface, final, abstract or a function.
     *
     * @return boolean
     */
    protected function isFileComment()
    {
        if ($this->_tokenizer->prev() !== self::T_OPEN_TAG) {
            return false;
        }

        $notExpectedTags = array(
            self::T_CLASS,
            self::T_FINAL,
            self::T_ABSTRACT,
            self::T_FUNCTION,
            self::T_INTERFACE
        );

        return !in_array($this->_tokenizer->peek(), $notExpectedTags, true);
    }

    /**
     * Skips an encapsulted block like strings or backtick strings.
     *
     * @param integer $endToken The end token.
     *
     * @return void
     */
    private function _skipEncapsultedBlock($endToken)
    {
        while (($tokenType = $this->_tokenizer->peek()) !== $endToken) {
            $this->_consumeToken($tokenType);
        }
        $this->_consumeToken($endToken);
    }

    /**
     * Returns the class names of all <b>throws</b> annotations with in the
     * given comment block.
     *
     * @param string $comment The context doc comment block.
     *
     * @return array
     */
    private function _parseThrowsAnnotations($comment)
    {
        $throws = array();
        if (preg_match_all(self::REGEXP_THROWS_TYPE, $comment, $matches) > 0) {
            foreach ($matches[1] as $match) {
                $throws[] = $match;
            }
        }
        return $throws;
    }

    /**
     * This method parses the given doc comment text for a return annotation and
     * it returns the found return type.
     *
     * @param string $comment A doc comment text.
     *
     * @return string
     */
    private function _parseReturnAnnotation($comment)
    {
        if (preg_match(self::REGEXP_RETURN_TYPE, $comment, $match) > 0) {
            foreach (explode('|', end($match)) as $type) {
                if (PHP_Depend_Util_Type::isScalarType($type) === false) {
                    return $type;
                }
            }
        }
        return null;
    }

    /**
     * This method parses the given doc comment text for a var annotation and
     * it returns the found property type.
     *
     * @param string $comment A doc comment text.
     *
     * @return string
     */
    private function _parseVarAnnotation($comment)
    {
        if (preg_match(self::REGEXP_VAR_TYPE, $comment, $match) > 0) {
            foreach (explode('|', end($match)) as $type) {
                if (PHP_Depend_Util_Type::isScalarType($type) === false) {
                    return $type;
                }
            }
        }
        return null;
    }

    /**
     * Extracts non scalar types from a field doc comment and creates a
     * matching type instance.
     *
     * @return PHP_Depend_Code_ClassOrInterfaceReference
     * @since 0.9.6
     */
    private function _parseFieldDeclarationClassOrInterfaceReference()
    {
        // Skip, if ignore annotations is set
        if ($this->_ignoreAnnotations === true) {
            return null;
        }

        // Get type annotation
        $qualifiedName = $this->_parseVarAnnotation($this->_docComment);
        if ($qualifiedName === null) {
            return null;
        }
        return $this->_builder->buildClassOrInterfaceReference($qualifiedName);
    }

    /**
     * Extracts documented <b>throws</b> and <b>return</b> types and sets them
     * to the given <b>$callable</b> instance.
     *
     * @param PHP_Depend_Code_AbstractCallable $callable The context callable.
     *
     * @return void
     */
    private function _prepareCallable(PHP_Depend_Code_AbstractCallable $callable)
    {
        // Skip, if ignore annotations is set
        if ($this->_ignoreAnnotations === true) {
            return;
        }

        // Get all @throws Types
        $throws = $this->_parseThrowsAnnotations($callable->getDocComment());
        foreach ($throws as $qualifiedName) {
            $callable->addExceptionClassReference(
                $this->_builder->buildClassOrInterfaceReference($qualifiedName)
            );
        }

        // Get return annotation
        $qualifiedName = $this->_parseReturnAnnotation($callable->getDocComment());
        if ($qualifiedName !== null) {
            $callable->setReturnClassReference(
                $this->_builder->buildClassOrInterfaceReference($qualifiedName)
            );
        }
    }

    /**
     * This method will consume the next token in the token stream. It will
     * throw an exception if the type of this token is not identical with
     * <b>$tokenType</b>.
     *
     * @param integer $tokenType The next expected token type.
     *
     * @return PHP_Depend_Token
     */
    private function _consumeToken($tokenType)
    {
        if ($this->_tokenizer->peek() === self::T_EOF) {
            throw new PHP_Depend_Parser_TokenStreamEndException($this->_tokenizer);
        }

        if ($this->_tokenizer->peek() !== $tokenType) {
            throw new PHP_Depend_Parser_UnexpectedTokenException($this->_tokenizer);
        }
        return $this->_tokenStack->add($this->_tokenizer->next());
    }

    /**
     * This method will consume all comment tokens from the token stream.
     *
     * @return void
     */
    private function _consumeComments()
    {
        $comments = array(self::T_COMMENT, self::T_DOC_COMMENT);

        while (($type = $this->_tokenizer->peek()) !== self::T_EOF) {
            if (in_array($type, $comments, true) === false) {
                break;
            }
            $this->_tokenStack->add($this->_tokenizer->next());
        }
    }
}
?>