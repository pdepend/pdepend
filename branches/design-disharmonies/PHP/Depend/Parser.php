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
 * @version   SVN: $Id$
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
require_once 'PHP/Depend/Parser/InvalidStateException.php';
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
                               |
                               (array)\(\s*\)\s+
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
     * @var PHP_Depend_Parser_TokenStack $_tokenStack
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
                $package = $this->_builder->buildPackage(
                    $this->_getNamespaceOrPackageName()
                );
                $package->addType($this->_parseInterfaceDeclaration());
                break;

            case self::T_CLASS:
            case self::T_FINAL:
            case self::T_ABSTRACT:
                $package = $this->_builder->buildPackage(
                    $this->_getNamespaceOrPackageName()
                );
                $package->addType($this->_parseClassDeclaration());
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
                $this->_builder->buildASTClassReference(
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
        
        $this->_parseClassOrInterfaceBody($class);

        $class->setTokens($this->_tokenStack->pop());

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

                $methodOrProperty = $this->_parseMethodOrFieldDeclaration(
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
                $type->addChild(
                    $this->_parseConstantDefinition()
                );
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
     * @return PHP_Depend_Code_Method|PHP_Depend_Code_ASTFieldDeclaration
     * @since 0.9.6
     */
    private function _parseMethodOrFieldDeclaration($modifiers = 0)
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

                return $declaration;
                
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
     * @return PHP_Depend_Code_ASTFieldDeclaration
     * @since 0.9.6
     */
    private function _parseFieldDeclaration()
    {
        $declaration = $this->_builder->buildASTFieldDeclaration();
        $declaration->setComment($this->_docComment);

        $type = $this->_parseFieldDeclarationType();
        if ($type !== null) {
            $declaration->addChild($type);
        }
        
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

        $this->_setNodePositionsAndReturn($declaration);

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

        $this->_consumeToken(self::T_FUNCTION);
        $this->_consumeComments();
        
        $returnReference = $this->_parseOptionalReturnbyReference();

        if ($this->_isNextTokenFunctionOrMethodIdentifier()) {
            $callable = $this->_parseFunctionDeclaration();
        } else {
            $callable = $this->_parseClosureDeclaration();
        }

        $callable->setSourceFile($this->_sourceFile);
        $callable->setDocComment($this->_docComment);
        $callable->setTokens($this->_tokenStack->pop());
        $this->_prepareCallable($callable);

        if ($returnReference) {
            $callable->setReturnsReference();
        }

        $this->reset();

        return $callable;
    }

    /**
     * Parses an optional returns by reference token. The return value will be
     * <b>true</b> when a reference token was found, otherwise this method will
     * return <b>false</b>.
     *
     * @return boolean
     * @since 0.9.8
     */
    private function _parseOptionalReturnbyReference()
    {
        if ($this->_isNextTokenReturnByReference()) {
            return $this->_parseReturnByReference();
        }
        return false;
    }

    /**
     * Tests that the next available token is the returns by reference token.
     *
     * @return boolean
     * @since 0.9.8
     */
    private function _isNextTokenReturnByReference()
    {
        return ($this->_tokenizer->peek() === self::T_BITWISE_AND);
    }


    /**
     * This method parses a returns by reference token and returns <b>true</b>.
     *
     * @return boolean
     */
    private function _parseReturnByReference()
    {
        $this->_consumeToken(self::T_BITWISE_AND);
        $this->_consumeComments();

        return true;
    }

    /**
     * Tests that the next available token is a function or method identifier.
     *
     * @return boolean
     * @since 0.9.8
     */
    private function _isNextTokenFunctionOrMethodIdentifier()
    {
        return ($this->_tokenizer->peek() === self::T_STRING);
    }

    /**
     * This method parses a function declaration.
     *
     * @return PHP_Depend_Code_Function
     * @since 0.9.5
     */
    private function _parseFunctionDeclaration()
    {
        $this->_consumeComments();

        // Next token must be the function identifier
        $functionName = $this->_consumeToken(self::T_STRING)->image;

        $function = $this->_builder->buildFunction($functionName);
        $this->_parseCallableDeclaration($function);

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

        $returnsReference = $this->_parseOptionalReturnbyReference();

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
        $closure->addChild(
            $this->_parseFormalParameters()
        );

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
        $callable->addChild(
            $this->_parseFormalParameters()
        );
        $this->_consumeComments();

        if ($this->_tokenizer->peek() === self::T_CURLY_BRACE_OPEN) {
            // Get function body dependencies
            $this->_parseCallableBody($callable);
        } else {
            $this->_consumeToken(self::T_SEMICOLON);
        }
    }

    /**
     * Parses an allocation expression.
     *
     * <code>
     * function foo()
     * {
     * //  -------------
     *     new bar\Baz();
     * //  -------------
     *
     * //  ---------
     *     new Foo();
     * //  ---------
     * }
     * </code>
     *
     * @return PHP_Depend_Code_ASTAllocationExpression
     * @since 0.9.6
     */
    private function _parseAllocationExpression()
    {
        $this->_tokenStack->push();

        $token = $this->_consumeToken(self::T_NEW);

        $allocation = $this->_parseExpressionTypeReference(
            $this->_builder->buildASTAllocationExpression($token->image), true
        );

        if ($this->_isNextTokenArguments()) {
            $allocation->addChild(
                $this->_parseArguments()
            );
        }
        return $this->_setNodePositionsAndReturn($allocation);

    }

    /**
     * This method checks if the next available token starts an arguments node.
     *
     * @return boolean
     * @since 0.9.8
     */
    private function _isNextTokenArguments()
    {
        $this->_consumeComments();
        return $this->_tokenizer->peek() === self::T_PARENTHESIS_OPEN;
    }

    /**
     * This method configures the given node with its start and end positions.
     *
     * @param PHP_Depend_Code_ASTNode $node The node to prepare.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 0.9.8
     */
    private function _setNodePositionsAndReturn(PHP_Depend_Code_ASTNode $node)
    {
        $tokens = $this->_tokenStack->pop();

        $startToken = reset($tokens);
        $node->setStartLine($startToken->startLine);
        $node->setStartColumn($startToken->startColumn);

        $endToken = end($tokens);
        $node->setEndLine($endToken->endLine);
        $node->setEndColumn($endToken->endColumn);

        return $node;
    }

    /**
     * This method parse an instance of expression with its associated class or
     * interface reference.
     *
     * <code>
     *          ----------------
     * ($object instanceof Clazz);
     *          ----------------
     *
     *          ------------------------
     * ($object instanceof Clazz::$clazz);
     *          ------------------------
     *
     *          -----------------
     * ($object instanceof $clazz);
     *          -----------------
     *
     *          -----------------------
     * ($object instanceof $clazz->type);
     *          -----------------------
     *
     *          -----------------------------
     * ($object instanceof static|self|parent);
     *          -----------------------------
     * </code>
     *
     * @return PHP_Depend_Code_ASTInstanceOfExpression
     * @since 0.9.6
     */
    private function _parseInstanceOfExpression()
    {
        // Consume the "instanceof" keyword and strip comments
        $token = $this->_consumeToken(self::T_INSTANCEOF);
        $this->_consumeComments();

        // Create a new instanceof expression and parse identifier
        return $this->_parseExpressionTypeReference(
            $this->_builder->buildASTInstanceOfExpression($token->image), false
        );
    }

    /**
     * This method parses a type identifier as it is used in expression nodes
     * like {@link PHP_Depend_Code_ASTInstanceOfExpression} or an object
     * allocation node like {@link PHP_Depend_Code_ASTAllocationExpression}.
     *
     * @param PHP_Depend_Code_ASTNode $expression     The parent expression node.
     * @param boolean                 $classReference Create a class reference.
     *
     * @return PHP_Depend_Code_ASTNode
     */
    private function _parseExpressionTypeReference(
        PHP_Depend_Code_ASTNode $expression, $classReference
    ) {
        // Peek next token and look for a static type identifier
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_DOLLAR:
        case self::T_VARIABLE:
            // TODO: Parse variable or Member Primary Prefix + Property Postfix
            $expression->addChild(
                $this->_parseVariableOrFunctionPostfixOrMemberPrimaryPrefix()
            );
            break;

        case self::T_SELF:
            $expression->addChild(
                $this->_parseSelfReference(
                    $this->_consumeToken(self::T_SELF)
                )
            );
            break;

        case self::T_PARENT:
            $expression->addChild(
                $this->_parseParentReference(
                    $this->_consumeToken(self::T_PARENT)
                )
            );
            break;

        case self::T_STATIC:
            $expression->addChild(
                $this->_parseStaticReference(
                    $this->_consumeToken(self::T_STATIC)
                )
            );
            break;

        default:
            $expression->addChild(
                $this->_parseClassOrInterfaceReference($classReference)
            );
            break;
        }

        return $expression;
    }

    /**
     * This method parses a conditional-expression.
     *
     * <code>
     *         --------------
     * $foo = ($bar ? 42 : 23);
     *         --------------
     * </code>
     *
     * @return PHP_Depend_Code_ASTConditionalExpression
     * @since 0.9.8
     */
    private function _parseConditionalExpression()
    {
        $this->_tokenStack->push();
        $this->_consumeToken(self::T_QUESTION_MARK);

        return $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTConditionalExpression()
        );
    }

    /**
     * This method parses a boolean and-expression.
     *
     * @return PHP_Depend_Code_ASTBooleanAndExpression
     * @since 0.9.8
     */
    private function _parseBooleanAndExpression()
    {
        $this->_tokenStack->push();
        $this->_consumeToken(self::T_BOOLEAN_AND);

        return $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTBooleanAndExpression()
        );
    }

    /**
     * This method parses a boolean or-expression.
     *
     * @return PHP_Depend_Code_ASTBooleanOrExpression
     * @since 0.9.8
     */
    private function _parseBooleanOrExpression()
    {
        $this->_tokenStack->push();
        $this->_consumeToken(self::T_BOOLEAN_OR);

        return $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTBooleanOrExpression()
        );
    }

    /**
     * This method parses a logical <b>and</b>-expression.
     *
     * @return PHP_Depend_Code_ASTLogicalAndExpression
     * @since 0.9.8
     */
    private function _parseLogicalAndExpression()
    {
        $this->_tokenStack->push();
        $this->_consumeToken(self::T_LOGICAL_AND);

        return $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTLogicalAndExpression()
        );
    }

    /**
     * This method parses a logical <b>or</b>-expression.
     *
     * @return PHP_Depend_Code_ASTLogicalOrExpression
     * @since 0.9.8
     */
    private function _parseLogicalOrExpression()
    {
        $this->_tokenStack->push();
        $this->_consumeToken(self::T_LOGICAL_OR);

        return $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTLogicalOrExpression()
        );
    }

    /**
     * This method parses a logical <b>xor</b>-expression.
     *
     * @return PHP_Depend_Code_ASTLogicalXorExpression
     * @since 0.9.8
     */
    private function _parseLogicalXorExpression()
    {
        $this->_tokenStack->push();
        $this->_consumeToken(self::T_LOGICAL_XOR);

        return $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTLogicalXorExpression()
        );
    }

    /**
     * Parses a class or interface reference node.
     *
     * @param boolean $classReference Force a class reference.
     *
     * @return PHP_Depend_Code_ASTClassOrInterfaceReference
     * @since 0.9.8
     */
    private function _parseClassOrInterfaceReference($classReference)
    {
        $this->_tokenStack->push();

        if ($classReference === true) {
            return $this->_setNodePositionsAndReturn(
                $this->_builder->buildASTClassReference(
                    $this->_parseQualifiedName()
                )
            );
        }
        return $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTClassOrInterfaceReference(
                $this->_parseQualifiedName()
            )
        );
    }

    /**
     * This method parses a brace expression and adds all parsed node instances
     * to the given {@link PHP_Depend_Code_ASTNode} object. Finally it returns
     * the prepared input node.
     *
     * A brace expression can be a compound:
     *
     * <code>
     * $this->{$foo ? 'foo' : 'bar'}();
     * </code>
     *
     * or a parameter list:
     *
     * <code>
     * $this->foo($bar, $baz);
     * </code>
     *
     * or an array index:
     *
     * <code>
     * $foo[$bar];
     * </code>
     *
     * @param PHP_Depend_Code_ASTNode $node       The context node instance.
     * @param integer                 $openToken  The brace open token type.
     * @param integer                 $closeToken The brace close token type.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_TokenStreamEndException When this method reaches
     *         the token stream end without terminating the brache expression.
     * @since 0.9.6
     */
    private function _parseBraceExpression(
        PHP_Depend_Code_ASTNode $node,
        $openToken,
        $closeToken
    ) {

        $this->_tokenStack->push();

        // Strip comments and read open token
        $this->_consumeComments();
        $token = $this->_consumeToken($openToken);

        $braceCount = 1;

        // Remove all comments
        $this->_consumeComments();

        // Get next token type
        $tokenType = $this->_tokenizer->peek();

        while ($tokenType !== self::T_EOF) {

            if (($expr = $this->_parseOptionalExpression()) !== null) {
                $node->addChild($expr);
            } else {
                if ($tokenType === $openToken) {
                    ++$braceCount;
                } else if ($tokenType === $closeToken) {
                    --$braceCount;
                }
                $this->_consumeToken($tokenType);
            }

            if ($braceCount === 0) {
                return $this->_setNodePositionsAndReturn($node);
            }

            // Remove all comments
            $this->_consumeComments();

            // Get next token type
            $tokenType = $this->_tokenizer->peek();
        }

        throw new PHP_Depend_Parser_TokenStreamEndException($this->_tokenizer);
    }

    /**
     * This method optionally parses an expression node and returns it. When no
     * expression was found this method will return <b>null</b>.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 0.9.6
     */
    private function _parseOptionalExpression()
    {
        $expressions = array();

        $this->_tokenStack->push();

        $this->_consumeComments();
        
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_EQUAL:
        case self::T_OR_EQUAL:
        case self::T_AND_EQUAL:
        case self::T_DIV_EQUAL:
        case self::T_MOD_EQUAL:
        case self::T_XOR_EQUAL:
        case self::T_PLUS_EQUAL:
        case self::T_MINUS_EQUAL:
        case self::T_CONCAT_EQUAL:
            return null;

        case self::T_LNUMBER:
        case self::T_DNUMBER:
        case self::T_BACKTICK:
        case self::T_DOUBLE_QUOTE:
        case self::T_CONSTANT_ENCAPSED_STRING:
            $expr = $this->_parseLiteral();
            break;

        case self::T_NEW:
            $expr = $this->_parseAllocationExpression();
            break;

        case self::T_INSTANCEOF:
            $expr = $this->_parseInstanceOfExpression();
            break;

        case self::T_STRING:
        case self::T_BACKSLASH:
        case self::T_NAMESPACE:
            $this->_tokenStack->push();
            $expr = $this->_parseMemberPrefixOrFunctionPostfix();
            $expr = $this->_setNodePositionsAndReturn($expr);
            $expr = $this->_parseOptionalAssignmentExpression($expr);
            break;

        case self::T_SELF:
            $this->_tokenStack->push();
            $expr = $this->_parseConstantOrSelfMemberPrimaryPrefix();
            $expr = $this->_setNodePositionsAndReturn($expr);
            $expr = $this->_parseOptionalAssignmentExpression($expr);
            break;

        case self::T_PARENT:
            $this->_tokenStack->push();
            $expr = $this->_parseConstantOrParentMemberPrimaryPrefix();
            $expr = $this->_setNodePositionsAndReturn($expr);
            $expr = $this->_parseOptionalAssignmentExpression($expr);
            break;

        case self::T_DOLLAR:
        case self::T_VARIABLE:
            $this->_tokenStack->push();
            $expr = $this->_parseVariableOrFunctionPostfixOrMemberPrimaryPrefix();
            $expr = $this->_setNodePositionsAndReturn($expr);
            $expr = $this->_parseOptionalAssignmentExpression($expr);
            break;

        case self::T_STATIC:
            $this->_tokenStack->push();
            $expr = $this->_parseStaticVariableDeclarationOrMemberPrimaryPrefix();
            $expr = $this->_setNodePositionsAndReturn($expr);
            $expr = $this->_parseOptionalAssignmentExpression($expr);
            break;

        case self::T_QUESTION_MARK:
            $expr = $this->_parseConditionalExpression();
            break;

        case self::T_BOOLEAN_AND:
            $expr = $this->_parseBooleanAndExpression();
            break;

        case self::T_BOOLEAN_OR:
            $expr = $this->_parseBooleanOrExpression();
            break;

        case self::T_LOGICAL_AND:
            $expr = $this->_parseLogicalAndExpression();
            break;

        case self::T_LOGICAL_OR:
            $expr = $this->_parseLogicalOrExpression();
            break;

        case self::T_LOGICAL_XOR:
            $expr = $this->_parseLogicalXorExpression();
            break;

        default:
            $this->_tokenStack->pop();
            return null;
        }
        return $this->_setNodePositionsAndReturn($expr);
    }

    /**
     * This method parses a single expression node. It will throw an exception
     * when it cannot detect an expression node at the actual token stream
     * possition.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_UnexpectedTokenException When there is no
     *         expression that can be parsed.
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 0.9.6
     */
    private function _parseExpression()
    {
        if (($expr = $this->_parseOptionalExpression()) === null) {
            throw new PHP_Depend_Parser_UnexpectedTokenException($this->_tokenizer);
        }
        return $expr;
    }

    /**
     * This method parses a switch statement.
     *
     * @return PHP_Depend_Code_ASTSwitchStatement
     * @since 0.9.8
     */
    private function _parseSwitchStatement()
    {
        $this->_tokenStack->push();
        $this->_consumeToken(self::T_SWITCH);

        $switch = $this->_builder->buildASTSwitchStatement();
        $switch->addChild($this->_parseParenthesisExpression());
        $this->_parseSwitchStatementBody($switch);

        return $this->_setNodePositionsAndReturn($switch);
    }

    /**
     * Parses the body of a switch statement.
     *
     * @param PHP_Depend_Code_ASTSwitchStatement $switch The parent switch stmt.
     *
     * @return PHP_Depend_Code_ASTSwitchStatement
     * @since 0.9.8
     */
    private function _parseSwitchStatementBody(
        PHP_Depend_Code_ASTSwitchStatement $switch
    ) {
        $this->_consumeComments();
        $this->_consumeToken(self::T_CURLY_BRACE_OPEN);

        $curlyBraceCount = 1;

        $tokenType = $this->_tokenizer->peek();
        while ($tokenType !== self::T_EOF) {

            switch ($tokenType) {

            case self::T_CURLY_BRACE_OPEN:
                $this->_consumeToken(self::T_CURLY_BRACE_OPEN);
                ++$curlyBraceCount;
                break;

            case self::T_CURLY_BRACE_CLOSE:
                $this->_consumeToken(self::T_CURLY_BRACE_CLOSE);
                --$curlyBraceCount;
                break;

            case self::T_CASE:
                $switch->addChild($this->_parseSwitchLabel());
                break;

            case self::T_DEFAULT:
                $switch->addChild($this->_parseSwitchLabelDefault());
                break;

            default:
                $statement = $this->_parseOptionalStatement();
                if ($statement === null) {
                    $this->_consumeToken($tokenType);
                } else if ($statement instanceof PHP_Depend_Code_ASTNodeI) {
                    $switch->addChild($statement);
                }
                // TODO: Change the <else if> into and <else> when the ast
                //       implementation is finished.
                break;
            }

            if ($curlyBraceCount === 0) {
                return $switch;
            }
            $tokenType = $this->_tokenizer->peek();
        }
        throw new PHP_Depend_Parser_TokenStreamEndException($this->_tokenizer);
    }

    /**
     * This method parses a case label of a switch statement.
     *
     * @return PHP_Depend_Code_ASTSwitchLabel
     * @since 0.9.8
     */
    private function _parseSwitchLabel()
    {
        $this->_tokenStack->push();
        $token = $this->_consumeToken(self::T_CASE);

        return $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTSwitchLabel($token->image)
        );
    }

    /**
     * This method parses the default label of a switch statement.
     *
     * @return PHP_Depend_Code_ASTSwitchLabel
     * @since 0.9.8
     */
    private function _parseSwitchLabelDefault()
    {
        $this->_tokenStack->push();
        $token = $this->_consumeToken(self::T_DEFAULT);

        $this->_consumeComments();
        $this->_consumeToken(self::T_COLON);

        $label = $this->_builder->buildASTSwitchLabel($token->image);
        $label->setDefault();

        return $this->_setNodePositionsAndReturn($label);
    }

    /**
     * This method parses a catch-statement.
     *
     * @return PHP_Depend_Code_ASTCatchStatement
     * @since 0.9.8
     */
    private function _parseCatchStatement()
    {
        $this->_tokenStack->push();
        $token = $this->_consumeToken(self::T_CATCH);

        $catch = $this->_builder->buildASTCatchStatement($token->image);

        $this->_consumeComments();
        $this->_consumeToken(self::T_PARENTHESIS_OPEN);

        $catch->addChild(
            $this->_builder->buildASTClassOrInterfaceReference(
                $this->_parseQualifiedName()
            )
        );

        $this->_consumeComments();
        $token = $this->_consumeToken(self::T_VARIABLE);

        $catch->addChild($this->_builder->buildASTVariable($token->image));
        
        $this->_consumeComments();
        $this->_consumeToken(self::T_PARENTHESIS_CLOSE);

        return $this->_setNodePositionsAndReturn($catch);
    }

    /**
     * This method parses a single if-statement node.
     *
     * @return PHP_Depend_Code_ASTIfStatement
     * @since 0.9.8
     */
    private function _parseIfStatement()
    {
        $this->_tokenStack->push();
        $token = $this->_consumeToken(self::T_IF);

        $if = $this->_builder->buildASTIfStatement($token->image);
        $if->addChild($this->_parseParenthesisExpression());

        return $this->_setNodePositionsAndReturn($if);
    }

    /**
     * This method parses a single elseif-statement node.
     *
     * @return PHP_Depend_Code_ASTElseIfStatement
     * @since 0.9.8
     */
    private function _parseElseIfStatement()
    {
        $this->_tokenStack->push();
        $token = $this->_consumeToken(self::T_ELSEIF);

        $elseIf = $this->_builder->buildASTElseIfStatement($token->image);
        $elseIf->addChild($this->_parseParenthesisExpression());

        return $this->_setNodePositionsAndReturn($elseIf);
    }

    /**
     * This method parses a single for-statement node.
     *
     * @return PHP_Depend_Code_ASTForStatement
     * @since 0.9.8
     */
    private function _parseForStatement()
    {
        $this->_tokenStack->push();
        $token = $this->_consumeToken(self::T_FOR);

        $forStatement = $this->_builder->buildASTForStatement($token->image);
        $forStatement->addChild($this->_parseForInit());

        $this->_consumeComments();
        $this->_consumeToken(self::T_SEMICOLON);

        return $this->_setNodePositionsAndReturn($forStatement);
    }

    /**
     * Parses the init part of a for-statement.
     *
     * <code>
     *      ------------------------
     * for ($x = 0, $y = 23, $z = 42; $x < $y; ++$x) {}
     *      ------------------------
     * </code>
     *
     * @return PHP_Depend_Code_ASTForInit
     * @since 0.9.8
     */
    private function _parseForInit()
    {
        $this->_tokenStack->push();
        $this->_consumeComments();

        $forInit = $this->_builder->buildASTForInit();

        while (($tokenType = $this->_tokenizer->peek()) !== self::T_EOF) {
            if ($tokenType === self::T_SEMICOLON) {
                break;
            }
            if (($expr = $this->_parseOptionalExpression()) === null) {
                $this->_consumeToken($tokenType);
            } else {
                $forInit->addChild($expr);
            }
        }
        return $this->_setNodePositionsAndReturn($forInit);
    }

    /**
     * This method parses a single foreach-statement node.
     *
     * @return PHP_Depend_Code_ASTForeachStatement
     * @since 0.9.8
     */
    private function _parseForeachStatement()
    {
        $this->_tokenStack->push();
        $token = $this->_consumeToken(self::T_FOREACH);

        return $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTForeachStatement($token->image)
        );
    }

    /**
     * This method parses a single while-statement node.
     *
     * @return PHP_Depend_Code_ASTWhileStatement
     * @since 0.9.8
     */
    private function _parseWhileStatement()
    {
        $this->_tokenStack->push();
        $token = $this->_consumeToken(self::T_WHILE);

        $while = $this->_builder->buildASTWhileStatement($token->image);
        $while->addChild($this->_parseParenthesisExpression());

        return $this->_setNodePositionsAndReturn($while);
    }

    /**
     * Parses any expression that is surrounded by an opening and a closing
     * parenthesis
     *
     * @return PHP_Depend_Code_ASTExpression
     * @since 0.9.8
     */
    private function _parseParenthesisExpression()
    {
        $this->_tokenStack->push();
        $this->_consumeComments();

        $expression = $this->_builder->buildASTExpression();
        $expression = $this->_parseBraceExpression(
            $expression,
            self::T_PARENTHESIS_OPEN,
            self::T_PARENTHESIS_CLOSE
        );
        
        return $this->_setNodePositionsAndReturn($expression);
    }

    /**
     * This method parses a member primary prefix expression or a function
     * postfix expression node.
     *
     * A member primary prefix can be a method call:
     *
     * <code>
     * $object->foo();
     *
     * clazz::foo();
     * </code>
     *
     * a property access:
     *
     * <code>
     * $object->foo;
     *
     * clazz::$foo;
     * </code>
     *
     * or a class constant access:
     *
     * <code>
     * clazz::FOO;
     * </code>
     *
     * A function postfix represents any kind of function call:
     *
     * <code>
     * $function();
     *
     * func();
     * </code>
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 0.9.6
     */
    private function _parseMemberPrefixOrFunctionPostfix()
    {
        $this->_tokenStack->push();

        $qualifiedName = $this->_parseQualifiedName();

        // Remove comments
        $this->_consumeComments();

        // Get next token type
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_DOUBLE_COLON:
            $node = $this->_parseStaticMemberPrimaryPrefix(
                $this->_builder->buildASTClassOrInterfaceReference($qualifiedName)
            );
            break;

        case self::T_PARENTHESIS_OPEN:
            $node = $this->_parseFunctionPostfix(
                $this->_builder->buildASTIdentifier($qualifiedName)
            );
            break;

        default:
            $node = $this->_builder->buildASTConstant($qualifiedName);
            break;
        }
        
        return $this->_setNodePositionsAndReturn($node);
    }

    /**
     * This method parses a function postfix expression. An object of type
     * {@link PHP_Depend_Code_ASTFunctionPostfix} represents any valid php
     * function call.
     *
     * This method will delegate the call to another method that returns a
     * member primary prefix object when the function postfix expression is
     * followed by an object operator.
     *
     * @param PHP_Depend_Code_ASTNode $node This node represents the function
     *        identifier. An identifier can be a static string, a variable, a
     *        compound variable or any other valid php function identifier.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 0.9.6
     */
    private function _parseFunctionPostfix(PHP_Depend_Code_ASTNode $node)
    {
        // Remove comments
        $this->_consumeComments();

        $function = $this->_builder->buildASTFunctionPostfix($node->getImage());
        $function->addChild($node);
        $function->addChild($this->_parseArguments());

        // Remove comments
        $this->_consumeComments();

        // Test for method or property access
        return $this->_parseOptionalMemberPrimaryPrefix($function);
    }

    /**
     * This method parses an optional member primary expression. It will parse
     * the primary expression when an object operator can be found at the actual
     * token stream position. Otherwise this method simply returns the input
     * {@link PHP_Depend_Code_ASTNode} instance.
     *
     * @param PHP_Depend_Code_ASTNode $node This node represents primary prefix
     *        left expression. It will be the first child of the parsed member
     *        primary expression.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 0.9.6
     */
    private function _parseOptionalMemberPrimaryPrefix(PHP_Depend_Code_ASTNode $node)
    {
        // Strip optional comments.
        $this->_consumeComments();

        if ($this->_tokenizer->peek() === self::T_OBJECT_OPERATOR) {
            return $this->_parseMemberPrimaryPrefix($node);
        }
        return $node;
    }

    /**
     * This method parses a dynamic or object bound member primary expression.
     * A member primary prefix can be a method call:
     *
     * <code>
     * $object->foo();
     * </code>
     *
     * or a property access:
     *
     * <code>
     * $object->foo;
     * </code>
     *
     * @param PHP_Depend_Code_ASTNode $node The left node in the parsed member
     *        primary expression.
     *
     * @return PHP_Depend_Code_ASTMemberPrimaryPrefix
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 0.9.6
     */
    private function _parseMemberPrimaryPrefix(PHP_Depend_Code_ASTNode $node)
    {
        // Consume double colon and optional comments
        $token = $this->_consumeToken(self::T_OBJECT_OPERATOR);
        $this->_consumeComments();

        $prefix = $this->_builder->buildASTMemberPrimaryPrefix($token->image);
        $prefix->addChild($node);

        // Get next token type
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_STRING:
            $prefix->addChild(
                $this->_parseMethodOrPropertyPostfix(
                    $this->_builder->buildASTIdentifier(
                        $this->_consumeToken(self::T_STRING)->image
                    )
                )
            );
            break;

        case self::T_CURLY_BRACE_OPEN:
            $prefix->addChild(
                $this->_parseMethodOrPropertyPostfix(
                    $this->_parseCompoundExpression()
                )
            );
            break;

        default:
            $prefix->addChild(
                $this->_parseMethodOrPropertyPostfix(
                    $this->_parseCompoundVariableOrVariableVariableOrVariable()
                )
            );
            break;
        }
        return $prefix;
    }

    /**
     * This method parses a static member primary expression. The given node
     * contains the used static class or interface identifier. A static member
     * primary prefix can represent the following code expressions:
     *
     * A static method class:
     *
     * <code>
     * Foo::bar();
     * </code>
     *
     * a static property access:
     *
     * <code>
     * Foo::$bar;
     * </code>
     *
     * or a static constant access:
     *
     * <code>
     * Foo::BAR;
     * </code>
     *
     * @param PHP_Depend_Code_ASTNode $node The left node in the parsed member
     *        primary expression.
     *
     * @return PHP_Depend_Code_ASTMemberPrimaryPrefix
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 0.9.6
     */
    private function _parseStaticMemberPrimaryPrefix(PHP_Depend_Code_ASTNode $node)
    {
        $token = $this->_consumeToken(self::T_DOUBLE_COLON);
        $this->_consumeComments();

        $prefix = $this->_builder->buildASTMemberPrimaryPrefix($token->image);
        $prefix->addChild($node);

        // Get next token type
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_STRING:
            $postfix = $this->_parseMethodOrConstantPostfix();
            break;

        default:
            $postfix = $this->_parseMethodOrPropertyPostfix(
                $this->_parseCompoundVariableOrVariableVariableOrVariable()
            );
            break;
        }
        $prefix->addChild($postfix);

        return $prefix;
    }

    /**
     * This method parses a method- or constant-postfix expression. This expression
     * will contain an identifier node as nested child.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 0.9.6
     */
    private function _parseMethodOrConstantPostfix()
    {
        $this->_tokenStack->push();

        $node = $this->_builder->buildASTIdentifier(
            $this->_consumeToken(self::T_STRING)->image
        );

        // Strip optional comments
        $this->_consumeComments();

        // Get next token type
        if ($this->_tokenizer->peek() === self::T_PARENTHESIS_OPEN) {
            $postfix = $this->_builder->buildASTMethodPostfix($node->getImage());
            $postfix->addChild($node);
            $postfix->addChild($this->_parseArguments());

            return $this->_setNodePositionsAndReturn(
                $this->_parseOptionalMemberPrimaryPrefix($postfix)
            );
        }

        $postfix = $this->_builder->buildASTConstantPostfix($node->getImage());
        $postfix->addChild($node);
        
        return $this->_setNodePositionsAndReturn($postfix);
    }

    /**
     * This method parses a method- or property-postfix expression. This expression
     * will contain the given node as method or property identifier.
     *
     * @param PHP_Depend_Code_ASTNode $node The identifier for the parsed postfix
     *        expression node. This node will be the first child of the returned
     *        postfix node instance.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 0.9.6
     */
    private function _parseMethodOrPropertyPostfix(PHP_Depend_Code_ASTNode $node)
    {
        // Strip optional comments
        $this->_consumeComments();

        // Get next token type
        $tokenType = $this->_tokenizer->peek();

        // T_PARENTHESIS_OPEN === method, everything else property
        switch ($tokenType) {

        case self::T_PARENTHESIS_OPEN:
            $postfix = $this->_builder->buildASTMethodPostfix($node->getImage());
            $postfix->addChild($node);
            $postfix->addChild($this->_parseArguments());

            return $this->_parseOptionalMemberPrimaryPrefix($postfix);

        default:
            $postfix = $this->_builder->buildASTPropertyPostfix($node->getImage());
            $postfix->addChild($node);

            return $this->_parseOptionalMemberPrimaryPrefix($postfix);
        }
    }

    /**
     * This method parses the arguments passed to a function- or method-call.
     *
     * @return PHP_Depend_Code_ASTArguments
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 0.9.6
     */
    private function _parseArguments()
    {
        return $this->_parseBraceExpression(
            $this->_builder->buildASTArguments(),
            self::T_PARENTHESIS_OPEN,
            self::T_PARENTHESIS_CLOSE
        );
    }

    /**
     * This method parses any type of variable, function postfix expressions or
     * any kind of member primary prefix.
     *
     * This method expects that the actual token represents any kind of valid
     * php variable: simple variable, compound variable or variable variable.
     *
     * It will parse a function postfix or member primary expression when this
     * variable is followed by an object operator, double colon or opening
     * parenthesis.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 0.9.6
     */
    private function _parseVariableOrFunctionPostfixOrMemberPrimaryPrefix()
    {
        $this->_tokenStack->push();

        $variable = $this->_parseCompoundVariableOrVariableVariableOrVariable();
        $this->_consumeComments();

        // Get next token type
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_DOUBLE_COLON:
            $result = $this->_parseStaticMemberPrimaryPrefix($variable);
            break;

        case self::T_OBJECT_OPERATOR:
            $result = $this->_parseMemberPrimaryPrefix($variable);
            break;

        case self::T_PARENTHESIS_OPEN:
            $result = $this->_parseFunctionPostfix($variable);
            break;

        default:
            $result = $variable;
            break;
        }
        return $this->_setNodePositionsAndReturn($result);
    }

    private function _parseOptionalAssignmentExpression(
        PHP_Depend_Code_ASTNode $left
    ) {
        $this->_consumeComments();
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_EQUAL:
        case self::T_OR_EQUAL:
        case self::T_AND_EQUAL:
        case self::T_DIV_EQUAL:
        case self::T_MOD_EQUAL:
        case self::T_XOR_EQUAL:
        case self::T_PLUS_EQUAL:
        case self::T_MINUS_EQUAL:
        case self::T_CONCAT_EQUAL:

            $token = $this->_consumeToken($tokenType);

            $node = $this->_builder->buildASTAssignmentExpression($token->image);
            $node->addChild($left);

            // TODO: Change this into a mandatory expression in later versions
            if (is_object($expression = $this->_parseOptionalExpression())) {
                $node->addChild($expression);
            }
            break;

        default:
            $node = $left;
            break;
        }
        return $node;
    }

    /**
     * This method parses a {@link PHP_Depend_Code_ASTStaticReference} node.
     *
     * @param PHP_Depend_Token $token The "static" keyword token.
     *
     * @return PHP_Depend_Code_ASTStaticReference
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @throws PHP_Depend_Parser_InvalidStateException When the keyword static
     *         was used outside of a class or interface scope.
     * @since 0.9.6
     */
    private function _parseStaticReference(PHP_Depend_Token $token)
    {
        // Strip optional comments
        $this->_consumeComments();

        if ($this->_classOrInterface === null) {
            throw new PHP_Depend_Parser_InvalidStateException(
                $token->startLine,
                (string) $this->_sourceFile,
                'The keyword "static" was used outside of a class/method scope.'
            );
        }

        return $this->_builder->buildASTStaticReference($this->_classOrInterface);
    }

    /**
     * This method parses a {@link PHP_Depend_Code_ASTSelfReference} node.
     *
     * @param PHP_Depend_Token $token The "self" keyword token.
     *
     * @return PHP_Depend_Code_ASTSelfReference
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @throws PHP_Depend_Parser_InvalidStateException When the keyword self
     *         was used outside of a class or interface scope.
     * @since 0.9.6
     */
    private function _parseSelfReference(PHP_Depend_Token $token)
    {
        if ($this->_classOrInterface === null) {
            throw new PHP_Depend_Parser_InvalidStateException(
                $token->startLine,
                (string) $this->_sourceFile,
                'The keyword "self" was used outside of a class/method scope.'
            );
        }
        return $this->_builder->buildASTSelfReference($this->_classOrInterface);
    }

    /**
     * This method parses a {@link PHP_Depend_Code_ASTConstant} node or an
     * instance of {@link PHP_Depend_Code_ASTSelfReference} as part of
     * a {@link PHP_Depend_Code_MemberPrimaryPrefix} that contains the self
     * reference as its first child when the self token is followed by a
     * double colon token.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @throws PHP_Depend_Parser_InvalidStateException When the keyword self
     *         was used outside of a class or interface scope.
     * @since 0.9.6
     */
    private function _parseConstantOrSelfMemberPrimaryPrefix()
    {
        // Read self token and strip optional comments
        $token = $this->_consumeToken(self::T_SELF);
        $this->_consumeComments();

        // Get next token type
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_DOUBLE_COLON:
            return $this->_parseStaticMemberPrimaryPrefix(
                $this->_parseSelfReference($token)
            );
        }
        return $this->_builder->buildASTConstant($token->image);
    }

    /**
     * This method parses a {@link PHP_Depend_Code_ASTParentReference} node.
     *
     * @param PHP_Depend_Token $token The "self" keyword token.
     *
     * @return PHP_Depend_Code_AST_Node
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @throws PHP_Depend_Parser_InvalidStateException When the keyword parent
     *         was used outside of a class or interface scope.
     * @since 0.9.6
     */
    private function _parseParentReference(PHP_Depend_Token $token)
    {
        if ($this->_classOrInterface === null) {
            throw new PHP_Depend_Parser_InvalidStateException(
                $token->startLine,
                (string) $this->_sourceFile,
                'The keyword "parent" was used outside of a class/method scope.'
            );
        }

        $classReference = $this->_classOrInterface->getParentClassReference();
        if ($classReference === null) {
            throw new PHP_Depend_Parser_InvalidStateException(
                $token->startLine,
                (string) $this->_sourceFile,
                sprintf(
                    'The keyword "parent" was used but the ' .
                    'class "%s" does not declare a parent.',
                    $this->_classOrInterface->getName()
                )
            );
        }

        return $this->_builder->buildASTParentReference($classReference);
    }

    /**
     * This method parses a {@link PHP_Depend_Code_ASTConstant} node or an
     * instance of {@link PHP_Depend_Code_ASTParentReference} as part of
     * a {@link PHP_Depend_Code_MemberPrimaryPrefix} that contains the parent
     * reference as its first child when the self token is followed by a
     * double colon token.
     *
     * @return PHP_Depend_Code_AST_Node
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @throws PHP_Depend_Parser_InvalidStateException When the keyword parent
     *         was used outside of a class or interface scope.
     * @since 0.9.6
     */
    private function _parseConstantOrParentMemberPrimaryPrefix()
    {
        // Consume parent token and strip optional comments
        $token = $this->_consumeToken(self::T_PARENT);
        $this->_consumeComments();

        // Get next token type
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_DOUBLE_COLON:
            return $this->_parseStaticMemberPrimaryPrefix(
                $this->_parseParentReference($token)
            );
        }
        return $this->_builder->buildASTConstant($token->image);
    }

    /**
     * This method parses a simple PHP variable.
     *
     * @return PHP_Depend_Code_ASTVariable
     * @throws PHP_Depend_Parser_UnexpectedTokenException When the actual token
     *         is not a valid variable token.
     * @since 0.9.6
     */
    private function _parseVariable()
    {
        // Read variable token
        $token = $this->_consumeToken(self::T_VARIABLE);
        $this->_consumeComments();

        if('$this' === $token->image) {
            if ($this->_classOrInterface === null) {
                throw new PHP_Depend_Parser_InvalidStateException(
                    $token->startLine,
                    (string) $this->_sourceFile,
                    'The keyword "$this" was used outside of a class/method scope.'
                );
            }

            $variable = $this->_builder->buildASTThisVariable($this->_classOrInterface);
        }
        else {
            $variable = $this->_builder->buildASTVariable($token->image);
        }

        return $variable;
    }

    /**
     * This method is a decision point between the different variable types
     * availanle in PHP. It peeks the next token and then decides whether it is
     * a regular variable or when the next token is of type <b>T_DOLLAR</b> a
     * compound- or variable-variable.
     *
     * <code>
     * ----
     * $foo;
     * ----
     *
     * -----
     * $$foo;
     * -----
     *
     * ------
     * ${FOO};
     * ------
     * </code>
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @throws PHP_Depend_Parser_UnexpectedTokenException When the actual token
     *         is not a valid variable token.
     * @since 0.9.6
     */
    private function _parseCompoundVariableOrVariableVariableOrVariable()
    {
        $this->_consumeComments();

        // Get next token type
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_DOLLAR:
            return $this->_parseCompoundVariableOrVariableVariable();

        default:
            return $this->_parseVariable();
        }
    }

    /**
     * This method implements a decision point between compound-variables and
     * variable-variable. It expects that the next token in the token-stream is
     * of type <b>T_DOLLAR</b> and removes it from the stream. Then this method
     * peeks the next available token when it is of type <b>T_CURLY_BRACE_OPEN</b>
     * this is compound variable, otherwise it can be a variable-variable or a
     * compound-variable.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @throws PHP_Depend_Parser_UnexpectedTokenException When the actual token
     *         is not a valid variable token.
     * @since 0.9.6
     */
    private function _parseCompoundVariableOrVariableVariable()
    {
        $this->_tokenStack->push();

        // Read the dollar token
        $token = $this->_consumeToken(self::T_DOLLAR);
        $this->_consumeComments();

        // Get next token type
        $tokenType = $this->_tokenizer->peek();

        // T_DOLLAR|T_VARIABLE === Variable variable,
        // T_CURLY_BRACE_OPEN === Compound variable
        switch ($tokenType) {

        case self::T_DOLLAR:
        case self::T_VARIABLE:
            $variable = $this->_builder->buildASTVariableVariable($token->image);
            $variable->addChild(
                $this->_parseCompoundVariableOrVariableVariableOrVariable()
            );
            break;

        default:
            $variable = $this->_builder->buildASTCompoundVariable($token->image);
            $variable->addChild($this->_parseCompoundExpression());
            break;
        }

        return $this->_setNodePositionsAndReturn($variable);
    }

    /**
     * This method parses a compound expression node.
     *
     * <code>
     * ------------------
     * {'_' . foo . $bar}
     * ------------------
     * </code>
     *
     * @return PHP_Depend_Code_ASTCompoundExpression
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @throws PHP_Depend_Parser_UnexpectedTokenException When the actual token
     *         is not a valid variable token.
     * @since 0.9.6
     */
    private function _parseCompoundExpression()
    {
        return $this->_parseBraceExpression(
            $this->_builder->buildASTCompoundExpression(),
            self::T_CURLY_BRACE_OPEN,
            self::T_CURLY_BRACE_CLOSE
        );
    }

    /**
     * This method parses a {@link PHP_Depend_Code_ASTLiteral} node. A literal
     * can be a single/double quote string or a backtick literal string.
     *
     * @return PHP_Depend_Code_ASTLiteral
     * @throws PHP_Depend_Parser_UnexpectedTokenException When this method
     *         reaches the end of the token stream without terminating the
     *         literal string.
     */
    private function _parseLiteral()
    {
        $this->_consumeComments();
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_LNUMBER:
        case self::T_DNUMBER:
        case self::T_CONSTANT_ENCAPSED_STRING:
            $token = $this->_consumeToken($tokenType);
            return $this->_builder->buildASTLiteral($token->image);

        case self::T_BACKTICK:
        case self::T_DOUBLE_QUOTE:
            $endToken = $tokenType;

            $image = '';
            do {
                $image .= $this->_consumeToken($tokenType)->image;
            } while (($tokenType = $this->_tokenizer->peek()) !== $endToken);
            $image .= $this->_consumeToken($endToken)->image;
            
            return $this->_builder->buildASTLiteral($image);
        }
    }

    /**
     * Extracts all dependencies from a callable signature.
     *
     * @return PHP_Depend_Code_ASTFormalParameters
     * @since 0.9.5
     */
    private function _parseFormalParameters()
    {
        $this->_consumeComments();

        $this->_tokenStack->push();

        $formalParameters = $this->_builder->buildASTFormalParameters();
        
        $this->_consumeToken(self::T_PARENTHESIS_OPEN);
        $this->_consumeComments();

        $tokenType = $this->_tokenizer->peek();

        // Check for function without parameters
        if ($tokenType === self::T_PARENTHESIS_CLOSE) {
            $this->_consumeToken(self::T_PARENTHESIS_CLOSE);
            $formalParameters->setTokens($this->_tokenStack->pop());

            return $formalParameters;
        }

        while ($tokenType !== self::T_EOF) {

            $formalParameters->addChild(
                $this->_parseFormalParameterOrTypeHintOrByReference()
            );

            $this->_consumeComments();
            $tokenType = $this->_tokenizer->peek();

            // Check for following parameter
            if ($tokenType !== self::T_COMMA) {
                break;
            }

            // It must be a comma
            $this->_consumeToken(self::T_COMMA);
        }

        $this->_consumeToken(self::T_PARENTHESIS_CLOSE);
        $formalParameters->setTokens($this->_tokenStack->pop());
        
        return $formalParameters;
    }

    /**
     * This method parses a formal parameter in all it's variations.
     *
     * <code>
     * //                ------------
     * function traverse(Iterator $it) {}
     * //                ------------
     *
     * //                ---------
     * function traverse(array $ar) {}
     * //                ---------
     * 
     * //                ---
     * function traverse(&$x) {}
     * //                ---
     * </code>
     *
     * @return PHP_Depend_Code_ASTFormalParameter
     * @since 0.9.6
     */
    private function _parseFormalParameterOrTypeHintOrByReference()
    {
        $this->_consumeComments();
        $tokenType = $this->_tokenizer->peek();

        $this->_tokenStack->push();

        switch ($tokenType) {

        case self::T_ARRAY:
            $parameter = $this->_parseFormalParameterAndArrayTypeHint();
            break;

        case self::T_STRING:
        case self::T_BACKSLASH:
            $parameter = $this->_parseFormalParameterAndTypeHint();
            break;

        case self::T_SELF:
            $parameter = $this->_parseFormalParameterAndSelfTypeHint();
            break;

        case self::T_PARENT:
            $parameter = $this->_parseFormalParameterAndParentTypeHint();
            break;

        case self::T_BITWISE_AND:
            $parameter = $this->_parseFormalParameterAndByReference();
            break;

        default:
            $parameter = $this->_parseFormalParameter();
            break;
        }

        $parameter->setTokens($this->_tokenStack->pop());
        return $parameter;
    }

    /**
     * This method parses a formal parameter that has an array type hint.
     *
     * <code>
     * //                ---------
     * function traverse(array $ar) {}
     * //                ---------
     * </code>
     *
     * @return PHP_Depend_Code_ASTFormalParameter
     * @since 0.9.6
     */
    private function _parseFormalParameterAndArrayTypeHint()
    {
        $this->_tokenStack->push();
        $this->_consumeToken(self::T_ARRAY);

        $array = $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTArrayType()
        );

        $parameter = $this->_parseFormalParameterOrByReference();
        $parameter->addChild($array);

        return $parameter;
    }

    /**
     * This method parses a formal parameter that has a regular class type hint.
     * 
     * <code>
     * //                ------------
     * function traverse(Iterator $it) {}
     * //                ------------
     * </code>
     *
     * @return PHP_Depend_Code_ASTFormalParameter
     * @since 0.9.6
     */
    private function _parseFormalParameterAndTypeHint()
    {
        $this->_tokenStack->push();

        $classReference = $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTClassOrInterfaceReference(
                $this->_parseQualifiedName()
            )
        );

        $parameter = $this->_parseFormalParameterOrByReference();
        $parameter->addChild($classReference);

        return $parameter;
    }

    /**
     * This method will parse a formal parameter that has the keyword parent as
     * parameter type hint.
     *
     * <code>
     * class Foo extends Bar
     * {
     *     //                   ---------
     *     public function test(parent $o) {}
     *     //                   ---------
     * }
     * </code>
     *
     * @return PHP_Depend_Code_ASTFormalParameter
     * @throws PHP_Depend_Parser_InvalidStateException When this type hint is
     *         used outside the scope of a class. When this type hint is used
     *         for a class that has no parent.
     * @since 0.9.6
     */
    private function _parseFormalParameterAndParentTypeHint()
    {
        $token = $this->_consumeToken(self::T_PARENT);

        if ($this->_classOrInterface === null) {
            throw new PHP_Depend_Parser_InvalidStateException(
                $token->startLine,
                (string) $this->_sourceFile,
                'The keyword "parent" was used as type hint but the parameter ' .
                'declaration is not in a class scope.'
            );
        }

        $classReference = $this->_classOrInterface->getParentClassReference();
        if ($classReference === null) {
            throw new PHP_Depend_Parser_InvalidStateException(
                $token->startLine,
                (string) $this->_sourceFile,
                sprintf(
                    'The keyword "parent" was used as type hint but the parent ' .
                    'class "%s" does not declare a parent.',
                    $this->_classOrInterface->getName()
                )
            );
        }

        $classReference = clone $classReference;
        $classReference->setTokens(array($token));

        $parameter = $this->_parseFormalParameterOrByReference();
        $parameter->addChild($classReference);

        return $parameter;
    }

    /**
     * This method will parse a formal parameter that has the keyword self as
     * parameter type hint.
     *
     * <code>
     * class Foo
     * {
     *     //                   -------
     *     public function test(self $o) {}
     *     //                   -------
     * }
     * </code>
     *
     * @return PHP_Depend_Code_ASTFormalParameter
     * @since 0.9.6
     */
    private function _parseFormalParameterAndSelfTypeHint()
    {
        $selfReference = $this->_builder->buildASTSelfReference(
            $this->_classOrInterface
        );
        $selfReference->setTokens(array($this->_consumeToken(self::T_SELF)));

        $parameter = $this->_parseFormalParameterOrByReference();
        $parameter->addChild($selfReference);

        return $parameter;
    }

    /**
     * This method will parse a formal parameter that can optionally be passed
     * by reference.
     *
     * <code>
     * //                 ---  -------
     * function foo(array &$x, $y = 42) {}
     * //                 ---  -------
     * </code>
     *
     * @return PHP_Depend_Code_ASTFormalParameter
     * @since 0.9.6
     */
    private function _parseFormalParameterOrByReference()
    {
        $this->_consumeComments();
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_BITWISE_AND:
            $parameter = $this->_parseFormalParameterAndByReference();
            break;

        default:
            $parameter = $this->_parseFormalParameter();
            break;
        }
        return $parameter;
    }

    /**
     * This method will parse a formal parameter that is passed by reference.
     *
     * <code>
     * //                 ---  --------
     * function foo(array &$x, &$y = 42) {}
     * //                 ---  --------
     * </code>
     *
     * @return PHP_Depend_Code_ASTFormalParameter
     * @since 0.9.6
     */
    private function _parseFormalParameterAndByReference()
    {
        $this->_consumeToken(self::T_BITWISE_AND);

        $parameter = $this->_parseFormalParameter();
        $parameter->setPassedByReference();

        return $parameter;
    }

    /**
     * This method will parse a formal parameter. A formal parameter is at least
     * a variable name, but can also contain a default parameter value.
     *
     * <code>
     * //               --  -------
     * function foo(Bar $x, $y = 42) {}
     * //               --  -------
     * </code>
     *
     * @return PHP_Depend_Code_ASTFormalParameter
     * @since 0.9.6
     */
    private function _parseFormalParameter()
    {
        $parameter = $this->_builder->buildASTFormalParameter();
        $parameter->addChild(
            $this->_parseVariableDeclarator()
        );

        return $parameter;
    }

    /**
     * Extracts all dependencies from a callable body.
     *
     * @param PHP_Depend_Code_AbstractCallable $callable The context callable.
     *
     * @return void
     */
    private function _parseCallableBody(PHP_Depend_Code_AbstractCallable $callable)
    {
        $this->_useSymbolTable->createScope();
        
        $curly = 0;

        $tokenType = $this->_tokenizer->peek();

        while ($tokenType !== self::T_EOF) {

            switch ($tokenType) {

            case self::T_CURLY_BRACE_OPEN:
                $this->_consumeToken(self::T_CURLY_BRACE_OPEN);
                ++$curly;
                break;

            case self::T_CURLY_BRACE_CLOSE:
                $this->_consumeToken(self::T_CURLY_BRACE_CLOSE);
                --$curly;
                break;

            default:
                $statement = $this->_parseOptionalStatement();
                if ($statement === null) {
                    $this->_consumeToken($tokenType);
                } else if ($statement instanceof PHP_Depend_Code_ASTNodeI) {
                    $callable->addChild($statement);
                }
                // TODO: Change the <else if> into and <else> when the ast
                //       implementation is finished.
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
     * Parses an optional statement or returns <b>null</b>.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 0.9.8
     */
    private function _parseOptionalStatement()
    {
        $tokenType = $this->_tokenizer->peek();

        switch ($tokenType) {

        case self::T_SWITCH:
            return $this->_parseSwitchStatement();

        case self::T_CATCH:
            return $this->_parseCatchStatement();

        case self::T_IF:
            return $this->_parseIfStatement();

        case self::T_ELSEIF:
            return $this->_parseElseIfStatement();

        case self::T_FOR:
            return $this->_parseForStatement();

        case self::T_FOREACH:
            return $this->_parseForeachStatement();

        case self::T_WHILE:
            return $this->_parseWhileStatement();

        case self::T_FUNCTION:
            return $this->_parseFunctionOrClosureDeclaration();

        case self::T_COMMENT:
            return $this->_parseCommentWithOptionalInlineClassOrInterfaceReference();

        case self::T_DOC_COMMENT:
            // TODO: Move this
            return $this->_builder->buildASTComment(
                $this->_consumeToken(self::T_DOC_COMMENT)->image
            );
        }
        return $this->_parseOptionalExpression();
    }

    /**
     * Parses a comment and optionally an embedded class or interface type
     * annotation.
     *
     * <code>
     * / * @var $foo FooBar * /
     *
     * - ASTComment
     *   - ASTClassOrInterfaceReference
     * </code>
     *
     * @return PHP_Depend_Code_ASTComment
     * @since 0.9.8
     */
    private function _parseCommentWithOptionalInlineClassOrInterfaceReference()
    {
        $this->_tokenStack->push();
        $token = $this->_consumeToken(self::T_COMMENT);

        $comment = $this->_builder->buildASTComment($token->image);

        if (preg_match(self::REGEXP_INLINE_TYPE, $token->image, $match)) {
            $comment->addChild(
                $this->_builder->buildASTClassOrInterfaceReference($match[1])
            );
        }
        return $this->_setNodePositionsAndReturn($comment);
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
     * @link http://php.net/manual/en/language.namespaces.importing.php
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

        // Add leading backslash, because aliases must be full qualified
        // http://php.net/manual/en/language.namespaces.importing.php
        if ($fragments[0] !== '\\') {
            array_unshift($fragments, '\\');
        }

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
     * Parses a single constant definition with one or more constant declarators.
     *
     * <code>
     * class Foo
     * {
     * //  ------------------------
     *     const FOO = 42, BAR = 23;
     * //  ------------------------
     * }
     * </code>
     *
     * @return PHP_Depend_Code_ASTConstantDefinition
     * @since 0.9.6
     */
    private function _parseConstantDefinition()
    {
        $this->_consumeComments();
        $this->_tokenStack->push();

        $token = $this->_consumeToken(self::T_CONST);

        $definition = $this->_builder->buildASTConstantDefinition($token->image);
        $definition->setComment($this->_docComment);

        do {
            $definition->addChild(
                $this->_parseConstantDeclarator()
            );

            $this->_consumeComments();
            $tokenType = $this->_tokenizer->peek();

            if ($tokenType === self::T_SEMICOLON) {
                break;
            }
            $this->_consumeToken(self::T_COMMA);
        } while ($tokenType !== self::T_EOF);


        $definition = $this->_setNodePositionsAndReturn($definition);

        $this->_consumeToken(self::T_SEMICOLON);

        return $definition;
    }

    /**
     * Parses a single constant declarator.
     *
     * <code>
     * class Foo
     * {
     *     //    --------
     *     const BAR = 42;
     *     //    --------
     * }
     * </code>
     *
     * Or in a comma separated constant defintion:
     *
     * <code>
     * class Foo
     * {
     *     //    --------
     *     const BAR = 42,
     *     //    --------
     *
     *     //    --------------
     *     const BAZ = 'Foobar',
     *     //    --------------
     *
     *     //    ----------
     *     const FOO = 3.14;
     *     //    ----------
     * }
     * </code>
     *
     * @return PHP_Depend_Code_ASTConstantDeclarator
     * @since 0.9.6
     */
    private function _parseConstantDeclarator()
    {
        // Remove leading comments and create a new token stack
        $this->_consumeComments();
        $this->_tokenStack->push();

        $token = $this->_consumeToken(self::T_STRING);

        $this->_consumeComments();
        $this->_consumeToken(self::T_EQUAL);

        $declarator = $this->_builder->buildASTConstantDeclarator($token->image);
        $declarator->setValue($this->_parseStaticValue());
        
        return $this->_setNodePositionsAndReturn($declarator);
    }

    /**
     * This method parses a static variable declaration list or a member primary
     * prefix invoked in the static context of a class.
     *
     * <code>
     * function foo() {
     * //  ------------------------------
     *     static $foo, $bar, $baz = null;
     * //  ------------------------------
     * }
     *
     *
     * class Foo {
     *     public function baz() {
     * //      ----------------
     *         static::foobar();
     * //      ----------------
     *     }
     *     public function foobar() {}
     * }
     *
     * class Bar extends Foo {
     *     public function foobar() {}
     * }
     * </code>
     *
     * @return PHP_Depend_Code_ASTConstant
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @throws PHP_Depend_Parser_UnexpectedTokenException When the actual token
     *         is not a valid variable token.
     * @since 0.9.6
     */
    private function _parseStaticVariableDeclarationOrMemberPrimaryPrefix()
    {
        $this->_tokenStack->push();

        // Consume static token and strip optional comments
        $token = $this->_consumeToken(self::T_STATIC);
        $this->_consumeComments();

        // Fetch next token type
        $tokenType = $this->_tokenizer->peek();

        if ($tokenType === self::T_PARENTHESIS_OPEN
            || $tokenType === self::T_DOUBLE_COLON
        ) {
            $static = $this->_parseStaticReference($token);
            $prefix = $this->_parseStaticMemberPrimaryPrefix($static);
            $prefix->setTokens($this->_tokenStack->pop());

            return $prefix;
        }
        $declaration = $this->_parseStaticVariableDeclaration($token);
        $declaration->setTokens($this->_tokenStack->pop());
        
        return $declaration;

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
     * @param PHP_Depend_Token $token Token with the "static" keyword.
     *
     * @return PHP_Depend_Code_ASTStaticVariableDeclaration
     * @since 0.9.6
     */
    private function _parseStaticVariableDeclaration(PHP_Depend_Token $token)
    {
        $staticDeclaration = $this->_builder->buildASTStaticVariableDeclaration(
            $token->image
        );

        // Strip optional comments
        $this->_consumeComments();

        // Fetch next token type
        $tokenType = $this->_tokenizer->peek();

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
     * @return PHP_Depend_Code_ASTVariableDeclarator
     * @since 0.9.6
     */
    private function _parseVariableDeclarator()
    {
        $this->_consumeComments();
        $this->_tokenStack->push();

        $name = $this->_consumeToken(self::T_VARIABLE)->image;
        $this->_consumeComments();

        $declarator = $this->_builder->buildASTVariableDeclarator($name);

        if ($this->_tokenizer->peek() === self::T_EQUAL) {
            $this->_consumeToken(self::T_EQUAL);
            $this->_consumeComments();

            $declarator->setValue($this->_parseStaticValueOrStaticArray());
        }
        return $this->_setNodePositionsAndReturn($declarator);
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
            case self::T_CLASS_C:
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
        return $this->_getNamespaceOrPackageName() . '\\' . $localName;
    }

    /**
     * Returns the name of a declared names. When the parsed code is not namespaced
     * this method will return the name from the @package annotation.
     *
     * @return string
     * @since 0.9.8
     */
    private function _getNamespaceOrPackageName()
    {
        if ($this->_namespaceName === null) {
            return $this->_packageName;
        }
        return $this->_namespaceName;
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
        if (preg_match('#\*\s*@package\s+(\S+)#', $comment, $match)) {
            $package = trim($match[1]);
            if (preg_match('#\*\s*@subpackage\s+(\S+)#', $comment, $match)) {
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
     * it returns the found property types.
     *
     * @param string $comment A doc comment text.
     *
     * @return array(string)
     */
    private function _parseVarAnnotation($comment)
    {
        if (preg_match(self::REGEXP_VAR_TYPE, $comment, $match) > 0) {
            return array_map('trim', explode('|', end($match)));
        }
        return array();
    }

    /**
     * This method will extract the type information of a property from it's
     * doc comment information. The returned value will be <b>null</b> when no
     * type information exists.
     *
     * @return PHP_Depend_Code_ASTTypeNode
     * @since 0.9.6
     */
    private function _parseFieldDeclarationType()
    {
        // Skip, if ignore annotations is set
        if ($this->_ignoreAnnotations === true) {
            return null;
        }

        $reference = $this->_parseFieldDeclarationClassOrInterfaceReference();
        if ($reference !== null) {
            return $reference;
        }

        $annotations = $this->_parseVarAnnotation($this->_docComment);
        foreach ($annotations as $annotation) {
            if (PHP_Depend_Util_Type::isPrimitiveType($annotation) === true) {
                return $this->_builder->buildASTPrimitiveType(
                    PHP_Depend_Util_Type::getPrimitiveType($annotation)
                );
            } else if (PHP_Depend_Util_Type::isArrayType($annotation) === true) {
                return $this->_builder->buildASTArrayType();
            }
        }
        return null;
    }

    /**
     * Extracts non scalar types from a field doc comment and creates a
     * matching type instance.
     *
     * @return PHP_Depend_Code_ASTClassOrInterfaceReference
     * @since 0.9.6
     */
    private function _parseFieldDeclarationClassOrInterfaceReference()
    {
        $annotations = $this->_parseVarAnnotation($this->_docComment);
        foreach ($annotations as $annotation) {
            if (PHP_Depend_Util_Type::isScalarType($annotation) === false) {
                return $this->_builder->buildASTClassOrInterfaceReference(
                    $annotation
                );
            }
        }
        return null;
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
                $this->_builder->buildASTClassOrInterfaceReference($qualifiedName)
            );
        }

        // Get return annotation
        $qualifiedName = $this->_parseReturnAnnotation($callable->getDocComment());
        if ($qualifiedName !== null) {
            $callable->setReturnClassReference(
                $this->_builder->buildASTClassOrInterfaceReference($qualifiedName)
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
