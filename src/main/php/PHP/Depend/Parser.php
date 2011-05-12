<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

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
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 * @todo      Rename class from "Parser" to "AbstractParser"
 */
abstract class PHP_Depend_Parser implements PHP_Depend_ConstantsI
{
    /**
     * Regular expression for inline type definitions in regular comments. This
     * kind of type is supported by IDEs like Netbeans or eclipse.
     */
    const REGEXP_INLINE_TYPE = '(^\s*/\*\s*
                                 @var\s+
                                   \$[a-zA-Z_\x7f-\xff\\\\][a-zA-Z0-9_\x7f-\xff]*\s+
                                   (.*?)
                                \s*\*/\s*$)ix';

    /**
     * Regular expression for types defined in <b>throws</b> annotations of
     * method or function doc comments.
     */
    const REGEXP_THROWS_TYPE = '(\*\s*
                             @throws\s+
                               ([a-zA-Z_\x7f-\xff\\\\][a-zA-Z0-9_\x7f-\xff\\\\]*)
                            )ix';

    /**
     * Regular expression for types defined in annotations like <b>return</b> or
     * <b>var</b> in doc comments of functions and methods.
     */
    const REGEXP_RETURN_TYPE = '(\*\s*
                     @return\s+
                      (array\(\s*
                        (\w+\s*=>\s*)?
                        ([a-zA-Z_\x7f-\xff\\\\][a-zA-Z0-9_\x7f-\xff\|\\\\]*)\s*
                      \)
                      |
                      ([a-zA-Z_\x7f-\xff\\\\][a-zA-Z0-9_\x7f-\xff\|\\\\]*))\s+
                    )ix';

    /**
     * Regular expression for types defined in annotations like <b>return</b> or
     * <b>var</b> in doc comments of functions and methods.
     */
    const REGEXP_VAR_TYPE = '(\*\s*
                      @var\s+
                       (array\(\s*
                         (\w+\s*=>\s*)?
                         ([a-zA-Z_\x7f-\xff\\\\][a-zA-Z0-9_\x7f-\xff\|\\\\]*)\s*
                       \)
                       |
                       ([a-zA-Z_\x7f-\xff\\\\][a-zA-Z0-9_\x7f-\xff\|\\\\]*))\s+
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
     * Used identifier builder instance.
     *
     * @var PHP_Depend_Util_UuidBuilder
     * @since 0.9.12
     */
    private $_uuidBuilder = null;

    /**
     * The maximum valid nesting level allowed.
     *
     * @var integer
     * @since 0.9.12
     */
    private $_maxNestingLevel = 1024;

    /**
     *
     * @var PHP_Depend_Util_Cache_Driver
     * @since 0.10.0
     */
    protected $cache = null;

    /*
     * The used code tokenizer.
     *
     * @var PHP_Depend_TokenizerI $_tokenizer
     */
    protected $tokenizer = null;

    /**
     * Constructs a new source parser.
     *
     * @param PHP_Depend_TokenizerI        $tokenizer The used code tokenizer.
     * @param PHP_Depend_BuilderI          $builder   The used node builder.
     * @param PHP_Depend_Util_Cache_Driver $cache     The used parser cache.
     */
    public function __construct(
        PHP_Depend_TokenizerI $tokenizer,
        PHP_Depend_BuilderI $builder,
        PHP_Depend_Util_Cache_Driver $cache
    ) {
        $this->tokenizer = $tokenizer;
        $this->_builder  = $builder;
        $this->cache     = $cache;

        $this->_uuidBuilder    = new PHP_Depend_Util_UuidBuilder();
        $this->_tokenStack     = new PHP_Depend_Parser_TokenStack();

        $this->_useSymbolTable = new PHP_Depend_Parser_SymbolTable();

        $this->_builder->setCache($this->cache);
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
     * Configures the maximum allowed nesting level.
     *
     * @param integer $maxNestingLevel The maximum allowed nesting level.
     *
     * @return void
     * @since 0.9.12
     */
    public function setMaxNestingLevel($maxNestingLevel)
    {
        $this->_maxNestingLevel = $maxNestingLevel;
    }

    /**
     * Returns the maximum allowed nesting/recursion level.
     *
     * @return integer
     * @since 0.9.12
     */
    protected function getMaxNestingLevel()
    {
        return $this->_maxNestingLevel;
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
        $this->_sourceFile = $this->tokenizer->getSourceFile();
        $this->_sourceFile
            ->setCache($this->cache)
            ->setUUID($this->_uuidBuilder->forFile($this->_sourceFile));

        $hash = md5_file($this->_sourceFile->getFileName());

        if ($this->cache->restore($this->_sourceFile->getUUID(), $hash)) {
            return;
        }
        $this->cache->remove($this->_sourceFile->getUUID());

        $this->setUpEnvironment();

        $this->_tokenStack->push();

        // Debug currently parsed source file.
        PHP_Depend_Util_Log::debug('Processing file ' . $this->_sourceFile);

        $tokenType = $this->tokenizer->peek();
        while ($tokenType !== self::T_EOF) {

            switch ($tokenType) {

            case self::T_COMMENT:
                $this->consumeToken(self::T_COMMENT);
                break;

            case self::T_DOC_COMMENT:
                $comment = $this->consumeToken(self::T_DOC_COMMENT)->image;

                $this->_packageName = $this->_parsePackageAnnotation($comment);
                $this->_docComment  = $comment;
                break;

            case self::T_INTERFACE:
                $package = $this->_builder->buildPackage(
                    $this->_getNamespaceOrPackageName()
                );
                $package->addType($interface = $this->_parseInterfaceDeclaration());

                $this->_builder->restoreInterface($interface);
                $this->_sourceFile->addChild($interface);
                break;

            case self::T_CLASS:
            case self::T_FINAL:
            case self::T_ABSTRACT:
                $package = $this->_builder->buildPackage(
                    $this->_getNamespaceOrPackageName()
                );
                $package->addType($class = $this->_parseClassDeclaration());

                $this->_builder->restoreClass($class);
                $this->_sourceFile->addChild($class);
                break;

            case self::T_FUNCTION:
                $callable = $this->_parseFunctionOrClosureDeclaration();
                $this->_sourceFile->addChild($callable);
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
                $this->consumeToken($tokenType);
                $this->reset();
                break;
            }

            $tokenType = $this->tokenizer->peek();
        }

        $this->_sourceFile->setTokens($this->_tokenStack->pop());
        $this->cache->store(
            $this->_sourceFile->getUUID(),
            $this->_sourceFile,
            $hash
        );

        $this->tearDownEnvironment();
    }

    /**
     * Initializes the parser environment.
     *
     * @return void
     * @since 0.9.12
     */
    protected function setUpEnvironment()
    {
        ini_set('xdebug.max_nesting_level', $this->getMaxNestingLevel());

        $this->_useSymbolTable->createScope();

        $this->reset();
    }

    /**
     * Restores the parser environment back.
     *
     * @return void
     * @since 0.9.12
     */
    protected function tearDownEnvironment()
    {
        ini_restore('xdebug.max_nesting_level');

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
     * Parses a valid class or interface name for the currently configured php
     * version.
     *
     * @return string
     * @since 0.9.20
     */
    protected abstract function parseClassName();

    /**
     * Parses a valid method or function name for the currently configured php
     * version.
     *
     * @return string
     * @since 0.10.0
     */
    protected abstract function parseFunctionName();

    /**
     * Parses the dependencies in a interface signature.
     *
     * @return PHP_Depend_Code_Interface
     */
    private function _parseInterfaceDeclaration()
    {
        $this->_tokenStack->push();

        $interface = $this->_parseInterfaceSignature();
        $interface = $this->_parseClassOrInterfaceBody($interface);
        $interface->setTokens($this->_tokenStack->pop());

        $this->reset();

        return $interface;
    }

    /**
     * Parses the signature of an interface and finally returns a configured
     * interface instance.
     *
     * @return PHP_Depend_Code_Interface
     * @since 0.10.2
     */
    private function _parseInterfaceSignature()
    {
        $this->consumeToken(self::T_INTERFACE);
        $this->consumeComments();

        $qualifiedName = $this->_createQualifiedTypeName($this->parseClassName());

        $interface = $this->_builder->buildInterface($qualifiedName);
        $interface->setSourceFile($this->_sourceFile);
        $interface->setDocComment($this->_docComment);
        $interface->setUUID($this->_uuidBuilder->forClassOrInterface($interface));
        $interface->setUserDefined();

        return $this->_parseOptionalExtendsList($interface);
    }

    /**
     * Parses an optional interface list of an interface declaration.
     *
     * @param PHP_Depend_Code_Interface $interface The declaring interface.
     *
     * @return PHP_Depend_Code_Interface
     * @since 0.10.2
     */
    private function _parseOptionalExtendsList(PHP_Depend_Code_Interface $interface)
    {
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === self::T_EXTENDS) {
            $this->consumeToken(self::T_EXTENDS);
            $this->_parseInterfaceList($interface);
        }
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
        $this->_parseClassModifiers();

        // Consume class keyword and read class start line
        $this->consumeToken(self::T_CLASS);

        // Remove leading comments and get class name
        $this->consumeComments();
        $localName = $this->parseClassName();

        $qualifiedName = $this->_createQualifiedTypeName($localName);

        $class = $this->_builder->buildClass($qualifiedName);
        $class->setSourceFile($this->_sourceFile);
        $class->setModifiers($this->_modifiers);
        $class->setDocComment($this->_docComment);
        $class->setUUID($this->_uuidBuilder->forClassOrInterface($class));
        $class->setUserDefined();

        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === self::T_EXTENDS) {
            $this->consumeToken(self::T_EXTENDS);

            $this->_tokenStack->push();

            $class->setParentClassReference(
                $this->_setNodePositionsAndReturn(
                    $this->_builder->buildASTClassReference(
                        $this->_parseQualifiedName()
                    )
                )
            );

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();
        }

        if ($tokenType === self::T_IMPLEMENTS) {
            $this->consumeToken(self::T_IMPLEMENTS);
            $this->_parseInterfaceList($class);
        }

        $this->_parseClassOrInterfaceBody($class);

        $class->setTokens($this->_tokenStack->pop());

        $this->reset();

        return $class;
    }

    /**
     * This method parses an optional class modifier. Valid class modifiers are
     * <b>final</b> or <b>abstract</b>.
     *
     * @return void
     */
    private function _parseClassModifiers()
    {
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === self::T_ABSTRACT) {
            $this->consumeToken(self::T_ABSTRACT);
            $this->_modifiers |= self::IS_EXPLICIT_ABSTRACT;
        } else if ($tokenType === self::T_FINAL) {
            $this->consumeToken(self::T_FINAL);
            $this->_modifiers |= self::IS_FINAL;
        }

        $this->consumeComments();
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

            $this->_tokenStack->push();

            $abstractType->addInterfaceReference(
                $this->_setNodePositionsAndReturn(
                    $this->_builder->buildASTClassOrInterfaceReference(
                        $this->_parseQualifiedName()
                    )
                )
            );

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            if ($tokenType === self::T_CURLY_BRACE_OPEN) {
                break;
            }
            $this->consumeToken(self::T_COMMA);
        }
    }

    /**
     * Parses a class/interface body.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $type The context class
     *        or interface instance.
     *
     * @return PHP_Depend_Code_AbstractClassOrInterface
     */
    private function _parseClassOrInterfaceBody(
        PHP_Depend_Code_AbstractClassOrInterface $type
    ) {
        $this->_classOrInterface = $type;

        // Consume comments and read opening curly brace
        $this->consumeComments();
        $this->consumeToken(self::T_CURLY_BRACE_OPEN);

        $defaultModifier = self::IS_PUBLIC;
        if ($type instanceof PHP_Depend_Code_Interface) {
            $defaultModifier |= self::IS_ABSTRACT;
        }
        $this->reset();

        $tokenType = $this->tokenizer->peek();

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

                if ($methodOrProperty instanceof PHP_Depend_Code_ASTNode) {
                    $type->addChild($methodOrProperty);
                }

                $this->reset();
                break;

            case self::T_CONST:
                $type->addChild($this->_parseConstantDefinition());
                $this->reset();
                break;

            case self::T_CURLY_BRACE_CLOSE:
                $this->consumeToken(self::T_CURLY_BRACE_CLOSE);

                $this->reset();

                // Reset context class or interface instance
                $this->_classOrInterface = null;

                // Stop processing
                return $type;

            case self::T_COMMENT:
                $token = $this->consumeToken(self::T_COMMENT);

                $comment = $this->_builder->buildASTComment($token->image);
                $comment->configureLinesAndColumns(
                    $token->startLine,
                    $token->endLine,
                    $token->startColumn,
                    $token->endColumn
                );

                $type->addChild($comment);

                break;

            case self::T_DOC_COMMENT:
                $token = $this->consumeToken(self::T_DOC_COMMENT);

                $comment = $this->_builder->buildASTComment($token->image);
                $comment->configureLinesAndColumns(
                    $token->startLine,
                    $token->endLine,
                    $token->startColumn,
                    $token->endColumn
                );

                $type->addChild($comment);

                $this->_docComment = $token->image;
                break;

            default:
                throw new PHP_Depend_Parser_UnexpectedTokenException(
                    $this->tokenizer->next(),
                    $this->tokenizer->getSourceFile()
                );
            }

            $tokenType = $this->tokenizer->peek();
        }

        throw new PHP_Depend_Parser_TokenStreamEndException($this->tokenizer);
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

        $tokenType = $this->tokenizer->peek();
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
                $method->setSourceFile($this->_sourceFile);
                $method->setUUID($this->_uuidBuilder->forMethod($method));
                $method->setTokens($this->_tokenStack->pop());
                return $method;

            case self::T_VARIABLE:
                $declaration = $this->_parseFieldDeclaration();
                $declaration->setModifiers($modifiers);

                return $declaration;

            default:
                break 2;
            }

            $this->consumeToken($tokenType);
            $this->consumeComments();

            $tokenType = $this->tokenizer->peek();
        }
        throw new PHP_Depend_Parser_UnexpectedTokenException(
            $this->tokenizer->next(),
            $this->tokenizer->getSourceFile()
        );
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

        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        while ($tokenType !== self::T_EOF) {
            $declaration->addChild($this->_parseVariableDeclarator());

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            if ($tokenType !== self::T_COMMA) {
                break;
            }
            $this->consumeToken(self::T_COMMA);

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();
        }

        $this->_setNodePositionsAndReturn($declaration);

        $this->consumeToken(self::T_SEMICOLON);

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

        $this->consumeToken(self::T_FUNCTION);
        $this->consumeComments();

        $returnReference = $this->_parseOptionalReturnbyReference();

        if ($this->_isNextTokenFormalParameterList()) {
            $callable = $this->_parseClosureDeclaration();
            $callable->setSourceFile($this->_sourceFile);
        } else {
            $callable = $this->_parseFunctionDeclaration();
        }

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
        return ($this->tokenizer->peek() === self::T_BITWISE_AND);
    }

    /**
     * This method parses a returns by reference token and returns <b>true</b>.
     *
     * @return boolean
     */
    private function _parseReturnByReference()
    {
        $this->consumeToken(self::T_BITWISE_AND);
        $this->consumeComments();

        return true;
    }

    /**
     * Tests that the next available token is an opening parenthesis.
     *
     * @return boolean
     * @since 0.9.10
     */
    private function _isNextTokenFormalParameterList()
    {
        $this->consumeComments();
        return ($this->tokenizer->peek() === self::T_PARENTHESIS_OPEN);
    }

    /**
     * This method parses a function declaration.
     *
     * @return PHP_Depend_Code_Function
     * @since 0.9.5
     */
    private function _parseFunctionDeclaration()
    {
        $this->consumeComments();

        // Next token must be the function identifier
        $functionName = $this->parseFunctionName();

        $function = $this->_builder->buildFunction($functionName);
        $function->setSourceFile($this->_sourceFile);
        $function->setUUID($this->_uuidBuilder->forFunction($function));

        $this->_parseCallableDeclaration($function);

        // First check for an existing namespace
        if ($this->_namespaceName !== null) {
            $packageName = $this->_namespaceName;
        } else if ($this->_packageName !== self::DEFAULT_PACKAGE) {
            $packageName = $this->_packageName;
        } else {
            $packageName = $this->_globalPackageName;
        }

        $this->_builder
            ->buildPackage($packageName)
            ->addFunction($function);

        // Store function in source file, because we need them during the file's
        // __wakeup() phase for function declarations within another function or
        // method declaration.
        $this->_sourceFile->addChild($function);

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
        $this->consumeToken(self::T_FUNCTION);
        $this->consumeComments();

        $returnsReference = $this->_parseOptionalReturnbyReference();

        $methodName = $this->parseFunctionName();

        $method = $this->_builder->buildMethod($methodName);
        $method->setDocComment($this->_docComment);
        $method->setSourceFile($this->_sourceFile);

        $this->_classOrInterface->addMethod($method);

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
        $closure->addChild($this->_parseFormalParameters());

        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_USE) {
            $this->_parseBoundVariables($closure);
        }
        $closure->addChild($this->_parseScope());

        return $closure;
    }

    /**
     * Parses an ast closure node.
     *
     * @return PHP_Depend_Code_ASTClosure
     * @since 0.9.12
     */
    private function _parseClosure()
    {
        $this->_tokenStack->push();
        // TODO: Refactor this temporary closure solution.
        $temp = $this->_parseFunctionOrClosureDeclaration();
        $expr = $this->_builder->buildASTClosure();
        foreach ($temp->getChildren() as $child) {
            $expr->addChild($child);
        }
        return $this->_setNodePositionsAndReturn($expr);
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
        $callable->addChild($this->_parseFormalParameters());

        $this->consumeComments();
        if ($this->tokenizer->peek() == self::T_CURLY_BRACE_OPEN) {
            $callable->addChild($this->_parseScope());
        } else {
            $this->consumeToken(self::T_SEMICOLON);
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

        $token = $this->consumeToken(self::T_NEW);

        $allocation = $this->_builder->buildASTAllocationExpression($token->image);
        $allocation = $this->_parseExpressionTypeReference($allocation, true);

        if ($this->_isNextTokenArguments()) {
            $allocation->addChild($this->_parseArguments());
        }
        return $this->_setNodePositionsAndReturn($allocation);
    }

    /**
     * Parses a eval-expression node.
     *
     * @return PHP_Depend_Code_ASTEvalExpression
     * @since 0.9.12
     */
    private function _parseEvalExpression()
    {
        $this->_tokenStack->push();
        $token = $this->consumeToken(self::T_EVAL);

        $expr = $this->_builder->buildASTEvalExpression($token->image);
        $expr->addChild($this->_parseParenthesisExpression());

        return $this->_setNodePositionsAndReturn($expr);
    }

    /**
     * This method parses an exit-expression.
     *
     * @return PHP_Depend_Code_ASTExitExpression
     * @since 0.9.12
     */
    private function _parseExitExpression()
    {
        $this->_tokenStack->push();
        $token = $this->consumeToken(self::T_EXIT);

        $expr = $this->_builder->buildASTExitExpression($token->image);

        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_PARENTHESIS_OPEN) {
            $expr->addChild($this->_parseParenthesisExpression());
        }
        return $this->_setNodePositionsAndReturn($expr);
    }

    /**
     * Parses a clone-expression node.
     *
     * @return PHP_Depend_Code_ASTCloneExpression
     * @since 0.9.12
     */
    private function _parseCloneExpression()
    {
        $this->_tokenStack->push();
        $token = $this->consumeToken(self::T_CLONE);

        $expr = $this->_builder->buildASTCloneExpression($token->image);
        // TODO: $expr->addChild($this->_parseExpression());
        if (($child = $this->_parseOptionalExpression()) != null) {
            $expr->addChild($child);
        }
        return $this->_setNodePositionsAndReturn($expr);
    }

    /**
     * This method parses a single list-statement node.
     *
     * @return PHP_Depend_Code_ASTListExpression
     * @author Joey Mazzarelli
     * @since 0.9.12
     */
    private function _parseListExpression()
    {
        $this->_tokenStack->push();

        $token = $this->consumeToken(self::T_LIST);
        $this->consumeComments();

        $list = $this->_builder->buildASTListExpression($token->image);

        $this->consumeToken(self::T_PARENTHESIS_OPEN);
        $this->consumeComments();

        while (($tokenType = $this->tokenizer->peek()) !== self::T_EOF) {

            // The variable is optional:
            //   list(, , , , $something) = ...;
            // is valid.
            switch ($tokenType) {

            case self::T_COMMA:
                $this->consumeToken(self::T_COMMA);
                $this->consumeComments();
                break;

            case self::T_PARENTHESIS_CLOSE:
                break 2;

            default:
                $list->addChild($this->_parseVariableOrConstantOrPrimaryPrefix());
                $this->consumeComments();
                break;
            }
        }

        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        return $this->_setNodePositionsAndReturn($list);
    }

    /**
     * Parses a include-expression node.
     *
     * @return PHP_Depend_Code_ASTIncludeExpression
     * @since 0.9.12
     */
    private function _parseIncludeExpression()
    {
        $expr = $this->_builder->buildASTIncludeExpression();

        return $this->_parseRequireOrIncludeExpression($expr, self::T_INCLUDE);
    }

    /**
     * Parses a include_once-expression node.
     *
     * @return PHP_Depend_Code_ASTIncludeExpression
     * @since 0.9.12
     */
    private function _parseIncludeOnceExpression()
    {
        $expr = $this->_builder->buildASTIncludeExpression();
        $expr->setOnce();

        return $this->_parseRequireOrIncludeExpression($expr, self::T_INCLUDE_ONCE);
    }

    /**
     * Parses a require-expression node.
     *
     * @return PHP_Depend_Code_ASTRequireExpression
     * @since 0.9.12
     */
    private function _parseRequireExpression()
    {
        $expr = $this->_builder->buildASTRequireExpression();

        return $this->_parseRequireOrIncludeExpression($expr, self::T_REQUIRE);
    }

    /**
     * Parses a require_once-expression node.
     *
     * @return PHP_Depend_Code_ASTRequireExpression
     * @since 0.9.12
     */
    private function _parseRequireOnceExpression()
    {
        $expr = $this->_builder->buildASTRequireExpression();
        $expr->setOnce();

        return $this->_parseRequireOrIncludeExpression($expr, self::T_REQUIRE_ONCE);
    }

    /**
     * Parses a <b>require_once</b>-, <b>require</b>-, <b>include_once</b>- or
     * <b>include</b>-expression node.
     *
     * @param PHP_Depend_Code_ASTExpression $expr The concrete expression type.
     * @param integer                       $type The token type to read.
     *
     * @return PHP_Depend_Code_ASTExpression
     * @since 0.9.12
     */
    private function _parseRequireOrIncludeExpression(
        PHP_Depend_Code_ASTExpression $expr, $type
    ) {
        $this->_tokenStack->push();

        $this->consumeToken($type);
        $this->consumeComments();

        if ($this->tokenizer->peek() === self::T_PARENTHESIS_OPEN) {
            $this->consumeToken(self::T_PARENTHESIS_OPEN);
            $expr->addChild($this->_parseOptionalExpression());
            $this->consumeToken(self::T_PARENTHESIS_CLOSE);
        } else {
            $expr->addChild($this->_parseOptionalExpression());
        }

        return $this->_setNodePositionsAndReturn($expr);
    }

    /**
     * Parses a cast-expression node.
     *
     * @return PHP_Depend_Code_ASTCaseExpression
     * @since 0.10.0
     */
    private function _parseCastExpression()
    {
        $token = $this->consumeToken($this->tokenizer->peek());

        $expr = $this->_builder->buildASTCastExpression($token->image);
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $expr;
    }

    /**
     * This method will parse an increment-expression. Depending on the previous
     * node this can be a {@link PHP_Depend_Code_ASTPostIncrementExpression} or
     * {@link PHP_Depend_Code_ASTPostfixExpression}.
     *
     * @param array(PHP_Depend_Code_ASTExpression) $expressions List of previous
     *        parsed expression nodes.
     *
     * @return PHP_Depend_Code_ASTExpression
     * @since 0.10.0
     */
    private function _parseIncrementExpression(array $expressions)
    {
        if ($this->_isReadWriteVariable(end($expressions))) {
            return $this->_parsePostIncrementExpression(array_pop($expressions));
        }
        return $this->_parsePreIncrementExpression();
    }

    /**
     * Parses a post increment-expression and adds the given child to that node.
     *
     * @param PHP_Depend_Code_ASTNode $child The child expression node.
     *
     * @return PHP_Depend_Code_ASTPostfixExpression
     * @since 0.10.0
     */
    private function _parsePostIncrementExpression(PHP_Depend_Code_ASTNode $child)
    {
        $token = $this->consumeToken(self::T_INC);

        $expr = $this->_builder->buildASTPostfixExpression($token->image);
        $expr->addChild($child);
        $expr->configureLinesAndColumns(
            $child->getStartLine(),
            $token->endLine,
            $child->getStartColumn(),
            $token->endColumn
        );

        return $expr;
    }

    /**
     * Parses a pre increment-expression and adds the given child to that node.
     *
     * @return PHP_Depend_Code_ASTPreIncrementExpression
     * @since 0.10.0
     */
    private function _parsePreIncrementExpression()
    {
        $token = $this->consumeToken(self::T_INC);

        $expr = $this->_builder->buildASTPreIncrementExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $expr;
    }

    /**
     * This method will parse an decrement-expression. Depending on the previous
     * node this can be a {@link PHP_Depend_Code_ASTPostDecrementExpression} or
     * {@link PHP_Depend_Code_ASTPostfixExpression}.
     *
     * @param array(PHP_Depend_Code_ASTExpression) $expressions List of previous
     *        parsed expression nodes.
     *
     * @return PHP_Depend_Code_ASTExpression
     * @since 0.10.0
     */
    private function _parseDecrementExpression(array $expressions)
    {
        if ($this->_isReadWriteVariable(end($expressions))) {
            return $this->_parsePostDecrementExpression(array_pop($expressions));
        }
        return $this->_parsePreDecrementExpression();
    }

    /**
     * Parses a post decrement-expression and adds the given child to that node.
     *
     * @param PHP_Depend_Code_ASTNode $child The child expression node.
     *
     * @return PHP_Depend_Code_ASTPostfixExpression
     * @since 0.10.0
     */
    private function _parsePostDecrementExpression(PHP_Depend_Code_ASTNode $child)
    {
        $token = $this->consumeToken(self::T_DEC);

        $expr = $this->_builder->buildASTPostfixExpression($token->image);
        $expr->addChild($child);
        $expr->configureLinesAndColumns(
            $child->getStartLine(),
            $token->endLine,
            $child->getStartColumn(),
            $token->endColumn
        );

        return $expr;
    }

    /**
     * Parses a pre decrement-expression and adds the given child to that node.
     *
     * @return PHP_Depend_Code_ASTPreDecrementExpression
     * @since 0.10.0
     */
    private function _parsePreDecrementExpression()
    {
        $token = $this->consumeToken(self::T_DEC);

        $expr = $this->_builder->buildASTPreDecrementExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $expr;
    }

    /**
     * Parses one or more optional php <b>array</b> or <b>string</b> expressions.
     *
     * <code>
     * ---------
     * $array[0];
     * ---------
     *
     * ----------------
     * $array[1]['foo'];
     * ----------------
     *
     * ----------------
     * $string{1}[0]{0};
     * ----------------
     * </code>
     *
     * @param PHP_Depend_Code_ASTNode $node The parent/context node instance.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 0.9.12
     */
    private function _parseOptionalIndexExpression(PHP_Depend_Code_ASTNode $node)
    {
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {

        case self::T_CURLY_BRACE_OPEN:
            return $this->_parseStringIndexExpression($node);

        case self::T_SQUARED_BRACKET_OPEN:
            return $this->_parseArrayIndexExpression($node);
        }

        return $node;
    }

    /**
     * Parses an index expression as it is valid to access elements in a php
     * string or array.
     *
     * @param PHP_Depend_Code_ASTNode       $node  The context source node.
     * @param PHP_Depend_Code_ASTExpression $expr  The concrete index expression.
     * @param integer                       $open  The open token type.
     * @param integer                       $close The close token type.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 0.9.12
     */
    private function _parseIndexExpression(
        PHP_Depend_Code_ASTNode $node,
        PHP_Depend_Code_ASTExpression $expr,
        $open,
        $close
    ) {
        $this->consumeToken($open);

        if (($child = $this->_parseOptionalExpression()) != null) {
            $expr->addChild($child);
        }

        $token = $this->consumeToken($close);

        $expr->configureLinesAndColumns(
            $node->getStartLine(),
            $token->endLine,
            $node->getStartColumn(),
            $token->endColumn
        );

        return $this->_parseOptionalIndexExpression($expr);
    }

    /**
     * Parses a mandatory array index expression.
     *
     * <code>
     * //    ---
     * $array[0];
     * //    ---
     * </code>
     *
     * @param PHP_Depend_Code_ASTNode $node The context source node.
     *
     * @return PHP_Depend_Code_ASTArrayIndexExpression
     * @since 0.9.12
     */
    private function _parseArrayIndexExpression(PHP_Depend_Code_ASTNode $node)
    {
        $expr = $this->_builder->buildASTArrayIndexExpression();
        $expr->addChild($node);

        return $this->_parseIndexExpression(
            $node,
            $expr,
            self::T_SQUARED_BRACKET_OPEN,
            self::T_SQUARED_BRACKET_CLOSE
        );
    }

    /**
     * Parses a mandatory array index expression.
     *
     * <code>
     * //     ---
     * $string{0};
     * //     ---
     * </code>
     *
     * @param PHP_Depend_Code_ASTNode $node The context source node.
     *
     * @return PHP_Depend_Code_ASTStringIndexExpression
     * @since 0.9.12
     */
    private function _parseStringIndexExpression(PHP_Depend_Code_ASTNode $node)
    {
        $expr = $this->_builder->buildASTStringIndexExpression();
        $expr->addChild($node);

        return $this->_parseIndexExpression(
            $node,
            $expr,
            self::T_CURLY_BRACE_OPEN,
            self::T_CURLY_BRACE_CLOSE
        );
    }

    /**
     * This method checks if the next available token starts an arguments node.
     *
     * @return boolean
     * @since 0.9.8
     */
    private function _isNextTokenArguments()
    {
        $this->consumeComments();
        return $this->tokenizer->peek() === self::T_PARENTHESIS_OPEN;
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

        $end   = $tokens[count($tokens) - 1];
        $start = $tokens[0];

        $node->configureLinesAndColumns(
            $start->startLine,
            $end->endLine,
            $start->startColumn,
            $end->endColumn
        );

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
        $token = $this->consumeToken(self::T_INSTANCEOF);

        return $this->_parseExpressionTypeReference(
            $this->_builder->buildASTInstanceOfExpression($token->image), false
        );
    }

    /**
     * Parses an isset-expression node.
     *
     * <code>
     * //  -----------
     * if (isset($foo)) {
     * //  -----------
     * }
     *
     * //  -----------------------
     * if (isset($foo, $bar, $baz)) {
     * //  -----------------------
     * }
     * </code>
     *
     * @return PHP_Depend_Code_ASTIssetExpression
     * @since 0.9.12
     */
    private function _parseIssetExpression()
    {
        $startToken = $this->consumeToken(self::T_ISSET);
        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        $expr = $this->_builder->buildASTIssetExpression();
        $expr = $this->_parseVariableList($expr);

        $stopToken = $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        $expr->configureLinesAndColumns(
            $startToken->startLine,
            $stopToken->endLine,
            $startToken->startColumn,
            $stopToken->endColumn
        );

        return $expr;
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
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

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
                    $this->consumeToken(self::T_SELF)
                )
            );
            break;

        case self::T_PARENT:
            $expression->addChild(
                $this->_parseParentReference(
                    $this->consumeToken(self::T_PARENT)
                )
            );
            break;

        case self::T_STATIC:
            $expression->addChild(
                $this->_parseStaticReference(
                    $this->consumeToken(self::T_STATIC)
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
        $this->consumeToken(self::T_QUESTION_MARK);

        $expr = $this->_builder->buildASTConditionalExpression();
        if (($child = $this->_parseOptionalExpression()) != null) {
            $expr->addChild($child);
        }

        $this->consumeToken(self::T_COLON);

        // TODO: $expr->addChild($this->_parseExpression());
        if (($child = $this->_parseOptionalExpression()) != null) {
            $expr->addChild($child);
        }

        return $this->_setNodePositionsAndReturn($expr);
    }

    /**
     * This method parses a boolean and-expression.
     *
     * @return PHP_Depend_Code_ASTBooleanAndExpression
     * @since 0.9.8
     */
    private function _parseBooleanAndExpression()
    {
        $token = $this->consumeToken(self::T_BOOLEAN_AND);

        $expr = $this->_builder->buildASTBooleanAndExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );
        return $expr;
    }

    /**
     * This method parses a boolean or-expression.
     *
     * @return PHP_Depend_Code_ASTBooleanOrExpression
     * @since 0.9.8
     */
    private function _parseBooleanOrExpression()
    {
        $token = $this->consumeToken(self::T_BOOLEAN_OR);

        $expr = $this->_builder->buildASTBooleanOrExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );
        return $expr;
    }

    /**
     * This method parses a logical <b>and</b>-expression.
     *
     * @return PHP_Depend_Code_ASTLogicalAndExpression
     * @since 0.9.8
     */
    private function _parseLogicalAndExpression()
    {
        $token = $this->consumeToken(self::T_LOGICAL_AND);

        $expr = $this->_builder->buildASTLogicalAndExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );
        return $expr;
    }

    /**
     * This method parses a logical <b>or</b>-expression.
     *
     * @return PHP_Depend_Code_ASTLogicalOrExpression
     * @since 0.9.8
     */
    private function _parseLogicalOrExpression()
    {
        $token = $this->consumeToken(self::T_LOGICAL_OR);

        $expr = $this->_builder->buildASTLogicalOrExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );
        return $expr;
    }

    /**
     * This method parses a logical <b>xor</b>-expression.
     *
     * @return PHP_Depend_Code_ASTLogicalXorExpression
     * @since 0.9.8
     */
    private function _parseLogicalXorExpression()
    {
        $token = $this->consumeToken(self::T_LOGICAL_XOR);

        $expr = $this->_builder->buildASTLogicalXorExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );
        return $expr;
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
     * @param PHP_Depend_Token        $start      The opening token.
     * @param integer                 $closeToken The brace close token type.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_TokenStreamEndException When this method reaches
     *         the token stream end without terminating the brache expression.
     * @since 0.9.6
     */
    private function _parseBraceExpression(
        PHP_Depend_Code_ASTNode $node,
        PHP_Depend_Token $start,
        $closeToken
    ) {
        // TODO: $node->addChild($this->_parseExpression());
        if (is_object($expr = $this->_parseOptionalExpression())) {
            $node->addChild($expr);
        }
        $end = $this->consumeToken($closeToken);

        $node->configureLinesAndColumns(
            $start->startLine,
            $end->endLine,
            $start->startColumn,
            $end->endColumn
        );
        return $node;
    }

    /**
     * Parses the body of the given statement instance and adds all parsed nodes
     * to that statement.
     *
     * @param PHP_Depend_Code_ASTStatement $stmt The owning statement.
     *
     * @return PHP_Depend_Code_ASTStatement
     * @since 0.9.12
     */
    private function _parseStatementBody(PHP_Depend_Code_ASTStatement $stmt)
    {
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === self::T_CURLY_BRACE_OPEN) {
            $stmt->addChild($this->_parseRegularScope());
        } else if ($tokenType === self::T_COLON) {
            $stmt->addChild($this->_parseAlternativeScope());
        } else {
            $stmt->addChild($this->_parseOptionalStatement());
        }
        return $stmt;
    }

    /**
     * Parse a scope enclosed by curly braces.
     *
     * @return PHP_Depend_Code_ASTScope
     * @since 0.9.12
     */
    private function _parseRegularScope()
    {
        $this->_tokenStack->push();

        $this->consumeComments();
        $this->consumeToken(self::T_CURLY_BRACE_OPEN);

        $scope = $this->_parseScopeStatements();

        $this->consumeToken(self::T_CURLY_BRACE_CLOSE);
        return $this->_setNodePositionsAndReturn($scope);
    }

    /**
     * Parses the scope of a statement that is surrounded with PHP's alternative
     * syntax for statements.
     *
     * @return PHP_Depend_Code_ASTScopeStatement
     * @since 0.10.0
     */
    private function _parseAlternativeScope()
    {
        $this->_tokenStack->push();
        $this->consumeToken(self::T_COLON);

        $scope = $this->_parseScopeStatements();

        $this->_parseOptionalAlternativeScopeTermination();
        return $this->_setNodePositionsAndReturn($scope);
    }

    /**
     * Parses all statements that exist in a scope an adds them to a scope
     * instance.
     *
     * @return PHP_Depend_Code_ASTScopeStatement
     * @since 0.10.0
     */
    private function _parseScopeStatements()
    {
        $scope = $this->_builder->buildASTScopeStatement();
        while (($child = $this->_parseOptionalStatement()) != null) {
            if ($child instanceof PHP_Depend_Code_ASTNode) {
                $scope->addChild($child);
            }
        }
        return $scope;
    }

    /**
     * Parses the termination of a scope statement that uses PHP's laternative
     * syntax format.
     *
     * @return void
     * @since 0.10.0
     */
    private function _parseOptionalAlternativeScopeTermination()
    {
        $tokenType = $this->tokenizer->peek();
        if ($this->_isAlternativeScopeTermination($tokenType)) {
            $this->_parseAlternativeScopeTermination($tokenType);
        }
    }


    /**
     * This method returns <b>true</b> when the given token identifier represents
     * the end token of a alternative scope termination symbol. Otherwise this
     * method will return <b>false</b>.
     *
     * @param integer $tokenType The token type identifier.
     *
     * @return boolean
     * @since 0.10.0
     */
    private function _isAlternativeScopeTermination($tokenType)
    {
        return in_array(
            $tokenType,
            array(
                self::T_ENDDECLARE,
                self::T_ENDFOR,
                self::T_ENDFOREACH,
                self::T_ENDIF,
                self::T_ENDSWITCH,
                self::T_ENDWHILE
            )
        );
    }

    /**
     * Parses a series of tokens that represent an alternative scope termination.
     *
     * @param integer $tokenType The token type identifier.
     *
     * @return void
     * @since 0.10.0
     */
    private function _parseAlternativeScopeTermination($tokenType)
    {
        $this->consumeToken($tokenType);
        $this->consumeComments();

        if ($this->tokenizer->peek() === self::T_SEMICOLON) {
            $this->consumeToken(self::T_SEMICOLON);
        } else {
            $this->_parseNonePhpCode();
        }
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

        while (($tokenType = $this->tokenizer->peek()) != self::T_EOF) {

            $expr = null;

            switch ($tokenType) {

            // TODO: Add these tokens
            //case self::T_COMMA:
            case self::T_AS:
            case self::T_BREAK:
            case self::T_CLOSE_TAG:
            case self::T_COLON:
            case self::T_CONTINUE:
            case self::T_CURLY_BRACE_CLOSE:
            case self::T_DECLARE:
            case self::T_DO:
            case self::T_ECHO:
            case self::T_END_HEREDOC:
            case self::T_ENDFOREACH:
            case self::T_FOR:
            case self::T_FOREACH:
            case self::T_GLOBAL:
            case self::T_GOTO:
            case self::T_IF:
            case self::T_PARENTHESIS_CLOSE:
            case self::T_RETURN:
            case self::T_SEMICOLON:
            case self::T_SQUARED_BRACKET_CLOSE:
            case self::T_SWITCH:
            case self::T_THROW:
            case self::T_TRY:
            case self::T_UNSET:
            case self::T_WHILE:
                break 2;

            case self::T_SELF:
            case self::T_STRING:
            case self::T_PARENT:
            case self::T_STATIC:
            case self::T_DOLLAR:
            case self::T_VARIABLE:
            case self::T_BACKSLASH:
            case self::T_NAMESPACE:
                $expressions[] = $this->_parseVariableOrConstantOrPrimaryPrefix();
                break;

            case self::T_NULL:
            case self::T_TRUE:
            case self::T_FALSE:
            case self::T_LNUMBER:
            case self::T_DNUMBER:
            case self::T_BACKTICK:
            case self::T_DOUBLE_QUOTE:
            case self::T_CONSTANT_ENCAPSED_STRING:
                $expressions[] = $this->_parseLiteralOrString();
                break;

            case self::T_NEW:
                $expressions[] = $this->_parseAllocationExpression();
                break;

            case self::T_EVAL:
                $expressions[] = $this->_parseEvalExpression();
                break;

            case self::T_CLONE:
                $expressions[] = $this->_parseCloneExpression();
                break;

            case self::T_INSTANCEOF:
                $expressions[] = $this->_parseInstanceOfExpression();
                break;

            case self::T_ISSET:
                $expressions[] = $this->_parseIssetExpression();
                break;

            case self::T_LIST:
                $expressions[] = $this->_parseListExpression();
                break;

            case self::T_QUESTION_MARK:
                $expressions[] = $this->_parseConditionalExpression();
                break;

            case self::T_BOOLEAN_AND:
                $expressions[] = $this->_parseBooleanAndExpression();
                break;

            case self::T_BOOLEAN_OR:
                $expressions[] = $this->_parseBooleanOrExpression();
                break;

            case self::T_LOGICAL_AND:
                $expressions[] = $this->_parseLogicalAndExpression();
                break;

            case self::T_LOGICAL_OR:
                $expressions[] = $this->_parseLogicalOrExpression();
                break;

            case self::T_LOGICAL_XOR:
                $expressions[] = $this->_parseLogicalXorExpression();
                break;

            case self::T_FUNCTION:
                $expressions[] = $this->_parseClosure();
                break;

            case self::T_PARENTHESIS_OPEN:
                $expressions[] = $this->_parseParenthesisExpression();
                break;


            case self::T_EXIT:
                $expressions[] = $this->_parseExitExpression();
                break;

            case self::T_START_HEREDOC:
                $expressions[] = $this->_parseHeredoc();
                break;

            case self::T_CURLY_BRACE_OPEN:
                $expressions[] = $this->_parseBraceExpression(
                    $this->_builder->buildASTExpression(),
                    $this->consumeToken(self::T_CURLY_BRACE_OPEN),
                    self::T_CURLY_BRACE_CLOSE
                );
                break;

            case self::T_SQUARED_BRACKET_OPEN:
                $expressions[] = $this->_parseBraceExpression(
                    $this->_builder->buildASTExpression(),
                    $this->consumeToken(self::T_SQUARED_BRACKET_OPEN),
                    self::T_SQUARED_BRACKET_CLOSE
                );
                break;

            case self::T_INCLUDE:
                $expressions[] = $this->_parseIncludeExpression();
                break;

            case self::T_INCLUDE_ONCE:
                $expressions[] = $this->_parseIncludeOnceExpression();
                break;

            case self::T_REQUIRE:
                $expressions[] = $this->_parseRequireExpression();
                break;

            case self::T_REQUIRE_ONCE:
                $expressions[] = $this->_parseRequireOnceExpression();
                break;

            case self::T_DEC:
                $expressions[] = $this->_parseDecrementExpression($expressions);
                break;

            case self::T_INC:
                $expressions[] = $this->_parseIncrementExpression($expressions);
                break;

            case self::T_INT_CAST:
            case self::T_BOOL_CAST:
            case self::T_ARRAY_CAST:
            case self::T_UNSET_CAST:
            case self::T_OBJECT_CAST:
            case self::T_DOUBLE_CAST:
            case self::T_STRING_CAST:
                $expressions[] = $this->_parseCastExpression();
                break;

            case self::T_EQUAL:
            case self::T_OR_EQUAL:
            case self::T_AND_EQUAL:
            case self::T_DIV_EQUAL:
            case self::T_MOD_EQUAL:
            case self::T_XOR_EQUAL:
            case self::T_PLUS_EQUAL:
            case self::T_MINUS_EQUAL:
            case self::T_CONCAT_EQUAL:
                $expressions[] = $this->_parseAssignmentExpression(
                    array_pop($expressions)
                );
                break;

            default:
                $this->consumeToken($tokenType);
                break;
            }
        }

        $expressions = $this->_reduce($expressions);

        $count = count($expressions);
        if ($count == 0) {
            return null;
        } else if ($count == 1) {
            return $expressions[0];
        }

        $expr = $this->_builder->buildASTExpression();
        foreach ($expressions as $node) {
            $expr->addChild($node);
        }
        $expr->configureLinesAndColumns(
            $expressions[0]->getStartLine(),
            $expressions[$count - 1]->getEndLine(),
            $expressions[0]->getStartColumn(),
            $expressions[$count - 1]->getEndColumn()
        );

        return $expr;
    }

    /**
     * Applies all reduce rules against the given expression list.
     *
     * @param array(PHP_Depend_Code_ASTExpression) $expressions Unprepared input
     *        array with parsed expression nodes found in the source tree.
     *
     * @return array(PHP_Depend_Code_ASTExpression)
     * @since 0.10.0
     */
    private function _reduce(array $expressions)
    {
        return $this->_reduceUnaryExpression($expressions);
    }

    /**
     * Reduces all unary-expressions in the given expression list.
     *
     * @param array(PHP_Depend_Code_ASTExpression) $expressions Unprepared input
     *        array with parsed expression nodes found in the source tree.
     *
     * @return array(PHP_Depend_Code_ASTExpression)
     * @since 0.10.0
     */
    private function _reduceUnaryExpression(array $expressions)
    {
        for ($i = count($expressions) - 2; $i >= 0; --$i) {
            $expression = $expressions[$i];
            if ($expression instanceof PHP_Depend_Code_ASTUnaryExpression) {
                $child = $expressions[$i + 1];

                $expression->addChild($child);
                $expression->setEndColumn($child->getEndColumn());
                $expression->setEndLine($child->getEndLine());

                unset($expressions[$i + 1]);
            }
        }
        return array_values($expressions);
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
        $this->consumeToken(self::T_SWITCH);

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
        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_CURLY_BRACE_OPEN) {
            $this->consumeToken(self::T_CURLY_BRACE_OPEN);
        } else {
            $this->consumeToken(self::T_COLON);
        }

        while (($tokenType = $this->tokenizer->peek()) !== self::T_EOF) {

            switch ($tokenType) {

            case self::T_ENDSWITCH:
                $this->_parseAlternativeScopeTermination(self::T_ENDSWITCH);
                return $switch;

            case self::T_CURLY_BRACE_CLOSE:
                $this->consumeToken(self::T_CURLY_BRACE_CLOSE);
                return $switch;

            case self::T_CASE:
                $switch->addChild($this->_parseSwitchLabel());
                break;

            case self::T_DEFAULT:
                $switch->addChild($this->_parseSwitchLabelDefault());
                break;

            case self::T_COMMENT:
            case self::T_DOC_COMMENT:
                $this->consumeToken($tokenType);
                break;

            default:
                break 2;
            }
        }
        throw new PHP_Depend_Parser_UnexpectedTokenException(
            $this->tokenizer->next(),
            $this->tokenizer->getSourceFile()
        );
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
        $token = $this->consumeToken(self::T_CASE);

        $label = $this->_builder->buildASTSwitchLabel($token->image);
        // TODO: $label->addChild($this->_parseExpression());
        if (($expr = $this->_parseOptionalExpression()) != null) {
            $label->addChild($expr);
        }

        if ($this->tokenizer->peek() === self::T_COLON) {
            $this->consumeToken(self::T_COLON);
        } else {
            $this->consumeToken(self::T_SEMICOLON);
        }

        $this->_parseSwitchLabelBody($label);

        return $this->_setNodePositionsAndReturn($label);
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
        $token = $this->consumeToken(self::T_DEFAULT);

        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_COLON) {
            $this->consumeToken(self::T_COLON);
        } else {
            $this->consumeToken(self::T_SEMICOLON);
        }

        $label = $this->_builder->buildASTSwitchLabel($token->image);
        $label->setDefault();

        $this->_parseSwitchLabelBody($label);

        return $this->_setNodePositionsAndReturn($label);
    }

    /**
     * Parses the body of an switch label node.
     *
     * @param PHP_Depend_Code_ASTSwitchLabel $label The context switch label.
     *
     * @return PHP_Depend_Code_ASTSwitchLabel
     */
    private function _parseSwitchLabelBody(PHP_Depend_Code_ASTSwitchLabel $label)
    {
        $curlyBraceCount = 0;

        $tokenType = $this->tokenizer->peek();
        while ($tokenType !== self::T_EOF) {

            switch ($tokenType) {

            case self::T_CURLY_BRACE_OPEN:
                $this->consumeToken(self::T_CURLY_BRACE_OPEN);
                ++$curlyBraceCount;
                break;

            case self::T_CURLY_BRACE_CLOSE:
                if ($curlyBraceCount === 0) {
                    return $label;
                }
                $this->consumeToken(self::T_CURLY_BRACE_CLOSE);
                --$curlyBraceCount;
                break;

            case self::T_CASE:
            case self::T_DEFAULT:
            case self::T_ENDSWITCH:
                return $label;

            default:
                $statement = $this->_parseOptionalStatement();
                if ($statement === null) {
                    $this->consumeToken($tokenType);
                } else if ($statement instanceof PHP_Depend_Code_ASTNodeI) {
                    $label->addChild($statement);
                }
                // TODO: Change the <else if> into and <else> when the ast
                //       implementation is finished.
                break;
            }
            $tokenType = $this->tokenizer->peek();
        }
        throw new PHP_Depend_Parser_TokenStreamEndException($this->tokenizer);
    }

    /**
     * Parses the termination token for a statement. This termination token can
     * be a semicolon or a closing php tag.
     *
     * @return void
     * @since 0.9.12
     */
    private function _parseStatementTermination()
    {
        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_SEMICOLON) {
            $this->consumeToken(self::T_SEMICOLON);
        } else {
            $this->_parseNonePhpCode();
        }
    }

    /**
     * This method parses a try-statement + associated catch-statements.
     *
     * @return PHP_Depend_Code_ASTTryStatement
     * @since 0.9.12
     */
    private function _parseTryStatement()
    {
        $this->_tokenStack->push();
        $token = $this->consumeToken(self::T_TRY);

        $stmt = $this->_builder->buildASTTryStatement($token->image);
        $stmt->addChild($this->_parseRegularScope());

        do {
            $stmt->addChild($this->_parseCatchStatement());
            $this->consumeComments();
        } while ($this->tokenizer->peek() === self::T_CATCH);

        return $this->_setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a throw-statement.
     *
     * @return PHP_Depend_Code_ASTThrowStatement
     * @since 0.9.12
     */
    private function _parseThrowStatement()
    {
        $this->_tokenStack->push();
        $token = $this->consumeToken(self::T_THROW);

        $stmt = $this->_builder->buildASTThrowStatement($token->image);
        // TODO: $stmt->addChild($this->_parseExpression());
        if (($expr = $this->_parseOptionalExpression()) != null) {
            $stmt->addChild($expr);
        }
        $this->_parseStatementTermination();

        return $this->_setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a goto-statement.
     *
     * @return PHP_Depend_Code_ASTGotoStatement
     * @since 0.9.12
     */
    private function _parseGotoStatement()
    {
        $this->_tokenStack->push();

        $this->consumeToken(self::T_GOTO);
        $this->consumeComments();

        $token = $this->consumeToken(self::T_STRING);

        $this->_parseStatementTermination();

        $stmt = $this->_builder->buildASTGotoStatement($token->image);
        return $this->_setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a label-statement.
     *
     * @return PHP_Depend_Code_ASTLabelStatement
     * @since 0.9.12
     */
    private function _parseLabelStatement()
    {
        $this->_tokenStack->push();

        $token = $this->consumeToken(self::T_STRING);
        $this->consumeComments();
        $this->consumeToken(self::T_COLON);

        return $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTLabelStatement($token->image)
        );
    }

    /**
     * This method parses a global-statement.
     *
     * @return PHP_Depend_Code_ASTGlobalStatement
     * @since 0.9.12
     */
    private function _parseGlobalStatement()
    {
        $this->_tokenStack->push();
        $this->consumeToken(self::T_GLOBAL);

        $stmt = $this->_builder->buildASTGlobalStatement();
        $stmt = $this->_parseVariableList($stmt);

        $this->_parseStatementTermination();

        return $this->_setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a unset-statement.
     *
     * @return PHP_Depend_Code_ASTUnsetStatement
     * @since 0.9.12
     */
    private function _parseUnsetStatement()
    {
        $this->_tokenStack->push();

        $this->consumeToken(self::T_UNSET);
        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        $stmt = $this->_builder->buildASTUnsetStatement();
        $stmt = $this->_parseVariableList($stmt);

        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        $this->_parseStatementTermination();

        return $this->_setNodePositionsAndReturn($stmt);
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
        $this->consumeComments();

        $token = $this->consumeToken(self::T_CATCH);

        $catch = $this->_builder->buildASTCatchStatement($token->image);

        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        $catch->addChild(
            $this->_builder->buildASTClassOrInterfaceReference(
                $this->_parseQualifiedName()
            )
        );

        $this->consumeComments();
        $catch->addChild($this->_parseVariable());

        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        $catch->addChild($this->_parseRegularScope());

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
        $token = $this->consumeToken(self::T_IF);

        $stmt = $this->_builder->buildASTIfStatement($token->image);
        $stmt->addChild($this->_parseParenthesisExpression());

        $this->_parseStatementBody($stmt);
        $this->_parseOptionalElseOrElseIfStatement($stmt);

        return $this->_setNodePositionsAndReturn($stmt);
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
        $token = $this->consumeToken(self::T_ELSEIF);

        $stmt = $this->_builder->buildASTElseIfStatement($token->image);
        $stmt->addChild($this->_parseParenthesisExpression());

        $this->_parseStatementBody($stmt);
        $this->_parseOptionalElseOrElseIfStatement($stmt);

        return $this->_setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses an optional else-, else+if- or elseif-statement.
     *
     * @param PHP_Depend_Code_ASTStatement $stmt The owning if/elseif statement.
     *
     * @return PHP_Depend_Code_ASTStatement
     * @since 0.9.12
     */
    private function _parseOptionalElseOrElseIfStatement(
        PHP_Depend_Code_ASTStatement $stmt
    ) {
        $this->consumeComments();
        switch ($this->tokenizer->peek()) {

        case self::T_ELSE:
            $this->consumeToken(self::T_ELSE);
            $this->consumeComments();
            if ($this->tokenizer->peek() === self::T_IF) {
                $stmt->addChild($this->_parseIfStatement());
            } else {
                $this->_parseStatementBody($stmt);
            }
            break;

        case self::T_ELSEIF:
            $stmt->addChild($this->_parseElseIfStatement());
            break;
        }

        return $stmt;
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
        $token = $this->consumeToken(self::T_FOR);

        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        $stmt = $this->_builder->buildASTForStatement($token->image);

        if (($init = $this->_parseForInit()) !== null) {
            $stmt->addChild($init);
        }
        $this->consumeToken(self::T_SEMICOLON);

        if (($expr = $this->_parseForExpression()) !== null) {
            $stmt->addChild($expr);
        }
        $this->consumeToken(self::T_SEMICOLON);

        if (($update = $this->_parseForUpdate()) !== null) {
            $stmt->addChild($update);
        }
        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        return $this->_setNodePositionsAndReturn($this->_parseStatementBody($stmt));
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
        if (($expr = $this->_parseOptionalExpression())) {
            $init = $this->_builder->buildASTForInit();
            $init->addChild($expr);
            $init->configureLinesAndColumns(
                $expr->getStartLine(),
                $expr->getEndLine(),
                $expr->getStartColumn(),
                $expr->getEndColumn()
            );

            return $init;
        }
        return null;
    }

    /**
     * Parses the expression part of a for-statement.
     *
     * @return PHP_Depend_Code_ASTExpression
     * @since 0.9.12
     */
    private function _parseForExpression()
    {
        return $this->_parseOptionalExpression();
    }

    /**
     * Parses the update part of a for-statement.
     *
     * <code>
     *                                        -------------------------------
     * for ($x = 0, $y = 23, $z = 42; $x < $y; ++$x, $y = $x + 1, $z = $x + 2) {}
     *                                        -------------------------------
     * </code>
     *
     * @return PHP_Depend_Code_ASTForUpdate
     * @since 0.9.12
     */
    private function _parseForUpdate()
    {
        $this->_tokenStack->push();
        $this->consumeComments();

        $update = null;
        if (($expr = $this->_parseOptionalExpression()) != null) {
            $update = $this->_builder->buildASTForUpdate();
            $update->addChild($expr);

            return $this->_setNodePositionsAndReturn($update);
        }
        $this->_tokenStack->pop();
        return null;
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
        $token = $this->consumeToken(self::T_FOREACH);

        $foreach = $this->_builder->buildASTForeachStatement($token->image);

        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        // TODO: $foreach->addChild($this->_parseExpression());
        if (($expr = $this->_parseOptionalExpression()) != null) {
            $foreach->addChild($expr);
        }

        $this->consumeToken(self::T_AS);
        $this->consumeComments();

        if ($this->tokenizer->peek() === self::T_BITWISE_AND) {
            $foreach->addChild($this->_parseVariableOrMemberByReference());
        } else {
            $foreach->addChild($this->_parseVariableOrConstantOrPrimaryPrefix());

            if ($this->tokenizer->peek() === self::T_DOUBLE_ARROW) {
                $this->consumeToken(self::T_DOUBLE_ARROW);
                $foreach->addChild(
                    $this->_parseVariableOrMemberOptionalByReference()
                );
            }
        }

        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        return $this->_setNodePositionsAndReturn(
            $this->_parseStatementBody($foreach)
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
        $token = $this->consumeToken(self::T_WHILE);

        $stmt = $this->_builder->buildASTWhileStatement($token->image);
        $stmt->addChild($this->_parseParenthesisExpression());

        return $this->_setNodePositionsAndReturn(
            $this->_parseStatementBody($stmt)
        );
    }

    /**
     * This method parses a do/while-statement.
     *
     * @return PHP_Depend_Code_ASTDoWhileStatement
     * @sibce 0.9.12
     */
    private function _parseDoWhileStatement()
    {
        $this->_tokenStack->push();
        $token = $this->consumeToken(self::T_DO);

        $stmt = $this->_builder->buildASTDoWhileStatement($token->image);
        $stmt = $this->_parseStatementBody($stmt);

        $this->consumeComments();
        $this->consumeToken(self::T_WHILE);

        $stmt->addChild($this->_parseParenthesisExpression());

        $this->_parseStatementTermination();

        return $this->_setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a declare-statement.
     *
     * <code>
     * -------------------------------
     * declare(encoding='ISO-8859-1');
     * -------------------------------
     *
     * -------------------
     * declare(ticks=42) {
     *     // ...
     * }
     * -
     *
     * ------------------
     * declare(ticks=42):
     *     // ...
     * enddeclare;
     * -----------
     * </code>
     *
     * @return PHP_Depend_Code_ASTDeclareStatement
     * @since 0.10.0
     */
    private function _parseDeclareStatement()
    {
        $this->_tokenStack->push();
        $this->consumeToken(self::T_DECLARE);

        $stmt = $this->_builder->buildASTDeclareStatement();
        $stmt = $this->_parseDeclareList($stmt);
        $stmt = $this->_parseStatementBody($stmt);

        return $this->_setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a list of declare values. A declare list value always
     * consists of a string token and a static scalar.
     *
     * @param PHP_Depend_Code_ASTDeclareStatement $stmt The declare statement that
     *        is the owner of this list.
     *
     * @return PHP_Depend_Code_ASTDeclareStatement
     * @since 0.10.0
     */
    private function _parseDeclareList(PHP_Depend_Code_ASTDeclareStatement $stmt)
    {
        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        while (true) {
            $this->consumeComments();
            $name = $this->consumeToken(self::T_STRING)->image;

            $this->consumeComments();
            $this->consumeToken(self::T_EQUAL);

            $this->consumeComments();
            $value = $this->_parseStaticValue();

            $stmt->addValue($name, $value);

            $this->consumeComments();
            if ($this->tokenizer->peek() === self::T_COMMA) {
                $this->consumeToken(self::T_COMMA);
                continue;
            }
            break;
        }

        $this->consumeToken(self::T_PARENTHESIS_CLOSE);
        return $stmt;
    }

    /**
     * This method parses a single return-statement node.
     *
     * @return PHP_Depend_Code_ASTReturnStatement
     * @since 0.9.12
     */
    private function _parseReturnStatement()
    {
        $this->_tokenStack->push();
        $token = $this->consumeToken(self::T_RETURN);

        $stmt = $this->_builder->buildASTReturnStatement($token->image);
        if (($expr = $this->_parseOptionalExpression()) != null) {
            $stmt->addChild($expr);
        }
        $this->_parseStatementTermination();

        return $this->_setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a break-statement node.
     *
     * @return PHP_Depend_Code_ASTBreakStatement
     * @since 0.9.12
     */
    private function _parseBreakStatement()
    {
        $this->_tokenStack->push();
        $token = $this->consumeToken(self::T_BREAK);

        $stmt = $this->_builder->buildASTBreakStatement($token->image);
        if (($expr = $this->_parseOptionalExpression()) != null) {
            $stmt->addChild($expr);
        }
        $this->_parseStatementTermination();

        return $this->_setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a continue-statement node.
     *
     * @return PHP_Depend_Code_ASTContinueStatement
     * @since 0.9.12
     */
    private function _parseContinueStatement()
    {
        $this->_tokenStack->push();
        $token = $this->consumeToken(self::T_CONTINUE);

        $stmt = $this->_builder->buildASTContinueStatement($token->image);
        if (($expr = $this->_parseOptionalExpression()) != null) {
            $stmt->addChild($expr);
        }
        $this->_parseStatementTermination();

        return $this->_setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a echo-statement node.
     *
     * @return PHP_Depend_Code_ASTEchoStatement
     * @since 0.9.12
     */
    private function _parseEchoStatement()
    {
        $this->_tokenStack->push();
        $token = $this->consumeToken(self::T_ECHO);

        $stmt = $this->_builder->buildASTEchoStatement($token->image);
        // TODO: $stmt->addChild($this->_parseExpression())
        if (($expr = $this->_parseOptionalExpression()) != null) {
            $stmt->addChild($expr);
        }
        $this->_parseStatementTermination();

        return $this->_setNodePositionsAndReturn($stmt);
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
        $this->consumeComments();

        $expression = $this->_builder->buildASTExpression();
        $expression = $this->_parseBraceExpression(
            $expression,
            $this->consumeToken(self::T_PARENTHESIS_OPEN),
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
        $this->_tokenStack->push();

        $qName = $this->_parseQualifiedName();

        // Remove comments
        $this->consumeComments();

        // Get next token type
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {

        case self::T_DOUBLE_COLON:
            $node = $this->_builder->buildASTClassOrInterfaceReference($qName);
            $node = $this->_setNodePositionsAndReturn($node);
            $node = $this->_parseStaticMemberPrimaryPrefix($node);
            break;

        case self::T_PARENTHESIS_OPEN:
            $node = $this->_builder->buildASTIdentifier($qName);
            $node = $this->_setNodePositionsAndReturn($node);
            $node = $this->_parseFunctionPostfix($node);
            break;

        default:
            $node = $this->_builder->buildASTConstant($qName);
            $node = $this->_setNodePositionsAndReturn($node);
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
        $function = $this->_builder->buildASTFunctionPostfix($node->getImage());
        $function->addChild($node);
        $function->addChild($this->_parseArguments());

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
        $this->consumeComments();

        if ($this->tokenizer->peek() === self::T_OBJECT_OPERATOR) {
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
        $token = $this->consumeToken(self::T_OBJECT_OPERATOR);

        $prefix = $this->_builder->buildASTMemberPrimaryPrefix($token->image);
        $prefix->addChild($node);

        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {

        case self::T_STRING:
            $child = $this->_parseIdentifier();
            break;

        case self::T_CURLY_BRACE_OPEN:
            $child = $this->_parseCompoundExpression();
            break;

        default:
            $child = $this->_parseCompoundVariableOrVariableVariableOrVariable();
            break;
        }

        $prefix->addChild(
            $this->_parseMethodOrPropertyPostfix(
                $this->_parseOptionalIndexExpression($child)
            )
        );
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
        $token = $this->consumeToken(self::T_DOUBLE_COLON);

        $prefix = $this->_builder->buildASTMemberPrimaryPrefix($token->image);
        $prefix->addChild($node);

        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {

        case self::T_STRING:
            $postfix = $this->_parseMethodOrConstantPostfix();
            break;

        default:
            $postfix = $this->_parseMethodOrPropertyPostfix(
                $this->_parseOptionalIndexExpression(
                    $this->_parseCompoundVariableOrVariableVariableOrVariable()
                )
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

        $node = $this->_parseIdentifier();

        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_PARENTHESIS_OPEN) {
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
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {

        case self::T_PARENTHESIS_OPEN:
            $postfix = $this->_parseMethodPostfix($node);
            break;

        default:
            $postfix = $this->_parsePropertyPostfix($node);
            break;
        }
        return $this->_parseOptionalMemberPrimaryPrefix($postfix);
    }

    /**
     * Parses/Creates a property postfix node instance.
     *
     * @param PHP_Depend_Code_ASTNode $node Node that represents the image of
     *        the property postfix node.
     *
     * @return PHP_Depend_Code_ASTPropertyPostfix
     * @since 0.10.2
     */
    private function _parsePropertyPostfix(PHP_Depend_Code_ASTNode $node)
    {
        $postfix = $this->_builder->buildASTPropertyPostfix($node->getImage());
        $postfix->addChild($node);

        $postfix->setEndLine($node->getEndLine());
        $postfix->setEndColumn($node->getEndColumn());
        $postfix->setStartLine($node->getStartLine());
        $postfix->setStartColumn($node->getStartColumn());

        return $postfix;
    }

    /**
     * Parses a method postfix node instance.
     *
     * @param PHP_Depend_Code_ASTNode $node Node that represents the image of
     *        the method postfix node.
     *
     * @return PHP_Depend_Code_ASTMethodPostfix
     * @since 0.11.0
     */
    private function _parseMethodPostfix(PHP_Depend_Code_ASTNode $node)
    {
        $args = $this->_parseArguments();

        $postfix = $this->_builder->buildASTMethodPostfix($node->getImage());
        $postfix->addChild($node);
        $postfix->addChild($args);

        $postfix->setEndLine($args->getEndLine());
        $postfix->setEndColumn($args->getEndColumn());
        $postfix->setStartLine($node->getStartLine());
        $postfix->setStartColumn($node->getStartColumn());

        return $postfix;
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
        $this->consumeComments();

        return $this->_parseBraceExpression(
            $this->_builder->buildASTArguments(),
            $this->consumeToken(self::T_PARENTHESIS_OPEN),
            self::T_PARENTHESIS_CLOSE
        );
    }

    /**
     * This method implements the parsing for various expression types like
     * variables, object/static method. All these expressions are valid in
     * several php language constructs like, isset, empty, unset etc.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 0.9.12
     */
    private function _parseVariableOrConstantOrPrimaryPrefix()
    {
        $this->consumeComments();
        switch ($this->tokenizer->peek()) {

        case self::T_DOLLAR:
        case self::T_VARIABLE:
            return $this->_parseVariableOrFunctionPostfixOrMemberPrimaryPrefix();

        case self::T_SELF:
            return $this->_parseConstantOrSelfMemberPrimaryPrefix();

        case self::T_PARENT:
            return $this->_parseConstantOrParentMemberPrimaryPrefix();
            break;

        case self::T_STATIC:
            return $this->_parseStaticVariableDeclarationOrMemberPrimaryPrefix();
        }

        // T_NAMESPACE or T_BACKSLASH or T_STRING
        return $this->_parseMemberPrefixOrFunctionPostfix();
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
        $variable = $this->_parseOptionalIndexExpression($variable);

        $this->consumeComments();
        switch ($this->tokenizer->peek()) {

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

    /**
     * Parses an assingment expression node.
     *
     * @param PHP_Depend_Code_ASTNode $left The left part of the assignment
     *        expression that will be parsed by this method.
     *
     * @return PHP_Depend_Code_ASTAssignmentExpression
     * @since 0.9.12
     */
    private function _parseAssignmentExpression(PHP_Depend_Code_ASTNode $left)
    {
        $token = $this->consumeToken($this->tokenizer->peek());

        $node = $this->_builder->buildASTAssignmentExpression($token->image);
        $node->addChild($left);
        $node->setStartLine($left->getStartLine());
        $node->setStartColumn($left->getStartColumn());

        // TODO: Change this into a mandatory expression in later versions
        if (($expression = $this->_parseOptionalExpression()) != null) {
            $node->addChild($expression);
            $node->setEndLine($expression->getEndLine());
            $node->setEndColumn($expression->getEndColumn());
        } else {
            $node->setEndLine($left->getEndLine());
            $node->setEndColumn($left->getEndColumn());
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
        $this->consumeComments();

        if ($this->_classOrInterface === null) {
            throw new PHP_Depend_Parser_InvalidStateException(
                $token->startLine,
                (string) $this->_sourceFile,
                'The keyword "static" was used outside of a class/method scope.'
            );
        }

        $ref = $this->_builder->buildASTStaticReference($this->_classOrInterface);
        $ref->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $ref;
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

        $ref = $this->_builder->buildASTSelfReference($this->_classOrInterface);
        $ref->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $ref;
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
        $token = $this->consumeToken(self::T_SELF);
        $this->consumeComments();

        if ($this->tokenizer->peek() == self::T_DOUBLE_COLON) {
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
     * @return PHP_Depend_Code_ASTNode
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
                    'The keyword "parent" was used as type hint but the ' .
                    'class "%s" does not declare a parent.',
                    $this->_classOrInterface->getName()
                )
            );
        }

        $ref = $this->_builder->buildASTParentReference($classReference);
        $ref->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $ref;
    }

    /**
     * This method parses a {@link PHP_Depend_Code_ASTConstant} node or an
     * instance of {@link PHP_Depend_Code_ASTParentReference} as part of
     * a {@link PHP_Depend_Code_MemberPrimaryPrefix} that contains the parent
     * reference as its first child when the self token is followed by a
     * double colon token.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @throws PHP_Depend_Parser_InvalidStateException When the keyword parent
     *         was used outside of a class or interface scope.
     * @since 0.9.6
     */
    private function _parseConstantOrParentMemberPrimaryPrefix()
    {
        // Consume parent token and strip optional comments
        $token = $this->consumeToken(self::T_PARENT);
        $this->consumeComments();

        if ($this->tokenizer->peek() == self::T_DOUBLE_COLON) {
            return $this->_parseStaticMemberPrimaryPrefix(
                $this->_parseParentReference($token)
            );
        }
        return $this->_builder->buildASTConstant($token->image);
    }

    /**
     * Parses a variable or any other valid member expression that is optionally
     * prefixed with PHP's reference operator.
     *
     * <code>
     * //                  -----------
     * foreach ( $array as &$this->foo ) {}
     * //                  -----------
     *
     * //     ----------
     * $foo = &$bar->baz;
     * //     ----------
     * </code>
     *
     * @return PHP_Depend_Code_ASTUnaryExpression
     * @since 0.9.18
     */
    private function _parseVariableOrMemberOptionalByReference()
    {
        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_BITWISE_AND) {
            return $this->_parseVariableOrMemberByReference();
        }
        return $this->_parseVariableOrConstantOrPrimaryPrefix();
    }

    /**
     * Parses a variable or any other valid member expression that is prefixed
     * with PHP's reference operator.
     *
     * <code>
     * //                  -----------
     * foreach ( $array as &$this->foo ) {}
     * //                  -----------
     *
     * //     ----------
     * $foo = &$bar->baz;
     * //     ----------
     * </code>
     *
     * @return PHP_Depend_Code_ASTUnaryExpression
     * @since 0.9.18
     */
    private function _parseVariableOrMemberByReference()
    {
        $this->_tokenStack->push();

        $token = $this->consumeToken(self::T_BITWISE_AND);
        $this->consumeComments();

        $expression = $this->_builder->buildASTUnaryExpression($token->image);
        $expression->addChild($this->_parseVariableOrConstantOrPrimaryPrefix());

        return $this->_setNodePositionsAndReturn($expression);
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
        $token = $this->consumeToken(self::T_VARIABLE);

        $variable = $this->_builder->buildASTVariable($token->image);
        $variable->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $variable;
    }

    /**
     * This method parses a comma separated list of valid php variables and/or
     * properties and adds them to the given node instance.
     *
     * @param PHP_Depend_Code_ASTNode $node The context parent node.
     *
     * @return PHP_Depend_Code_ASTNode The prepared entire node.
     * @since 0.9.12
     */
    private function _parseVariableList(PHP_Depend_Code_ASTNode $node)
    {
        $this->consumeComments();
        while ($this->tokenizer->peek() !== self::T_EOF) {
            $node->addChild($this->_parseVariableOrConstantOrPrimaryPrefix());

            $this->consumeComments();
            if ($this->tokenizer->peek() === self::T_COMMA) {

                $this->consumeToken(self::T_COMMA);
                $this->consumeComments();
            } else {
                break;
            }
        }

        return $node;
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
        if ($this->tokenizer->peek() == self::T_DOLLAR) {
            return $this->_parseCompoundVariableOrVariableVariable();
        }
        return $this->_parseVariable();
    }

    /**
     * Parses a PHP compound variable or a simple literal node.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 0.9.19
     */
    private function _parseCompoundVariableOrLiteral()
    {
        $this->_tokenStack->push();

        // Read the dollar token
        $token = $this->consumeToken(self::T_DOLLAR);
        $this->consumeComments();

        // Get next token type
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {

        case self::T_CURLY_BRACE_OPEN:
            $variable = $this->_builder->buildASTCompoundVariable($token->image);
            $variable->addChild($this->_parseCompoundExpression());
            break;

        default:
            $variable = $this->_builder->buildASTLiteral($token->image);
            break;
        }

        return $this->_setNodePositionsAndReturn($variable);
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
        $token = $this->consumeToken(self::T_DOLLAR);
        $this->consumeComments();

        // Get next token type
        $tokenType = $this->tokenizer->peek();

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
            $variable = $this->_parseCompoundVariable($token);
            break;
        }

        return $this->_setNodePositionsAndReturn($variable);
    }

    /**
     * This method parses a compound variable like:
     *
     * <code>
     * //     ----------------
     * return ${'Foo' . 'Bar'};
     * //     ----------------
     * </code>
     *
     * @param PHP_Depend_Token $token The dollar token.
     *
     * @return PHP_Depend_Code_ASTCompoundVariable
     * @since 0.10.0
     */
    private function _parseCompoundVariable(PHP_Depend_Token $token)
    {
        return $this->_parseBraceExpression(
            $this->_builder->buildASTCompoundVariable($token->image),
            $this->consumeToken(self::T_CURLY_BRACE_OPEN),
            self::T_CURLY_BRACE_CLOSE
        );
    }

    /**
     * This method parses a compound expression like:
     *
     * <code>
     * //      ------  ------
     * $foo = "{$bar}, {$baz}\n";
     * //      ------  ------
     * </code>
     *
     * or a simple literal token:
     *
     * <code>
     * //      -
     * $foo = "{{$bar}, {$baz}\n";
     * //      -
     * </code>
     *
     * @return PHP_Depend_Code_NodeI
     * @since 0.9.10
     */
    private function _parseCompoundExpressionOrLiteral()
    {
        $token = $this->consumeToken(self::T_CURLY_BRACE_OPEN);
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {

        case self::T_DOLLAR:
        case self::T_VARIABLE:
            return $this->_parseBraceExpression(
                $this->_builder->buildASTCompoundExpression(),
                $token,
                self::T_CURLY_BRACE_CLOSE
            );
        }

        $literal = $this->_builder->buildASTLiteral($token->image);
        $literal->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $literal;
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
        $this->consumeComments();

        return $this->_parseBraceExpression(
            $this->_builder->buildASTCompoundExpression(),
            $this->consumeToken(self::T_CURLY_BRACE_OPEN),
            self::T_CURLY_BRACE_CLOSE
        );
    }

    /**
     * Parses a static identifier expression, as it is used for method and
     * function names.
     *
     * @return PHP_Depend_Code_ASTIdentifier
     * @since 0.9.12
     */
    private function _parseIdentifier()
    {
        $token = $this->consumeToken(self::T_STRING);

        $node = $this->_builder->buildASTIdentifier($token->image);
        $node->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $node;
    }

    /**
     * This method parses a {@link PHP_Depend_Code_ASTLiteral} node or an
     * instance of {@link PHP_Depend_Code_ASTString} that represents a string
     * in double quotes or surrounded by backticks.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_UnexpectedTokenException When this method
     *         reaches the end of the token stream without terminating the
     *         literal string.
     */
    private function _parseLiteralOrString()
    {
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {

        case self::T_NULL:
        case self::T_TRUE:
        case self::T_FALSE:
        case self::T_LNUMBER:
        case self::T_DNUMBER:
        case self::T_CONSTANT_ENCAPSED_STRING:
            $token = $this->consumeToken($tokenType);

            $literal = $this->_builder->buildASTLiteral($token->image);
            $literal->configureLinesAndColumns(
                $token->startLine,
                $token->endLine,
                $token->startColumn,
                $token->endColumn
            );
            return $literal;

        default:
            return $this->_parseString($tokenType);
        }
    }

    /**
     * Parses a here- or nowdoc string instance.
     *
     * @return PHP_Depend_Code_ASTHeredoc
     * @since 0.9.12
     */
    private function _parseHeredoc()
    {
        $this->_tokenStack->push();
        $this->consumeToken(self::T_START_HEREDOC);

        $heredoc = $this->_builder->buildASTHeredoc();
        $this->_parseStringExpressions($heredoc, self::T_END_HEREDOC);

        $token = $this->consumeToken(self::T_END_HEREDOC);
        $heredoc->setDelimiter($token->image);

        return $this->_setNodePositionsAndReturn($heredoc);
    }

    /**
     * Parses a simple string sequence between two tokens of the same type.
     *
     * @param integer $tokenType The start/stop token type.
     *
     * @return string
     * @since 0.9.10
     */
    private function _parseStringSequence($tokenType)
    {
        $type   = $tokenType;
        $string = '';

        do {
            $string .= $this->consumeToken($type)->image;
            $type    = $this->tokenizer->peek();
        } while ($type != $tokenType && $type != self::T_EOF);

        return $string . $this->consumeToken($tokenType)->image;
    }

    /**
     * This method parses a php string with all possible embedded expressions.
     *
     * <code>
     * $string = "Manuel $Pichler <{$email}>";
     *
     * // PHP_Depend_Code_ASTSTring
     * // |-- ASTLiteral             -  "Manuel ")
     * // |-- ASTVariable            -  $Pichler
     * // |-- ASTLiteral             -  " <"
     * // |-- ASTCompoundExpression  -  {...}
     * // |   |-- ASTVariable        -  $email
     * // |-- ASTLiteral             -  ">"
     * </code>
     *
     * @param integer $delimiterType The start/stop token type.
     *
     * @return PHP_Depend_Code_ASTString
     * @throws PHP_Depend_Parser_UnexpectedTokenException When this method
     *         reaches the end of the token stream without terminating the
     *         literal string.
     * @since 0.9.10
     */
    private function _parseString($delimiterType)
    {
        $token = $this->consumeToken($delimiterType);

        $string = $this->_builder->buildASTString();
        $string->setStartLine($token->startLine);
        $string->setStartColumn($token->startColumn);

        $this->_parseStringExpressions($string, $delimiterType);

        $token = $this->consumeToken($delimiterType);
        $string->setEndLine($token->endLine);
        $string->setEndColumn($token->endColumn);

        return $string;
    }

    /**
     * This method parses the contents of a string or here-/now-doc node. It
     * will not consume the given stop token, so it is up to the calling method
     * to consume the stop token. The return value of this method is the prepared
     * input string node.
     *
     * @param PHP_Depend_Code_ASTNode $node      The parent string or nowdoc node.
     * @param integer                 $stopToken The stop token type.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 0.9.12
     */
    private function _parseStringExpressions(
        PHP_Depend_Code_ASTNode $node,
        $stopToken
    ) {
        while (($tokenType = $this->tokenizer->peek()) != self::T_EOF) {
            switch ($tokenType) {

            case $stopToken:
                break 2;

            case self::T_BACKSLASH:
                $node->addChild($this->_parseEscapedASTLiteralString());
                break;

            case self::T_DOLLAR:
                $expr = $this->_parseCompoundVariableOrLiteral();
                $node->addChild($expr);
                break;

            case self::T_VARIABLE:
                $expr = $this->_parseVariable();
                $node->addChild($expr);
                break;

            case self::T_CURLY_BRACE_OPEN:
                $node->addChild($this->_parseCompoundExpressionOrLiteral());
                break;

            default:
                $node->addChild($this->_parseLiteral());
                break;
            }
        }
        return $node;
    }

    /**
     * This method parses an escaped sequence of literal tokens.
     *
     * @return PHP_Depend_Code_ASTLiteral
     * @since 0.9.10
     */
    private function _parseEscapedASTLiteralString()
    {
        $this->_tokenStack->push();

        $image  = $this->consumeToken(self::T_BACKSLASH)->image;
        $escape = true;

        $tokenType = $this->tokenizer->peek();
        while ($tokenType != self::T_EOF) {
            if ($tokenType === self::T_BACKSLASH) {
                $escape != $escape;
                $image  .= $this->consumeToken(self::T_BACKSLASH)->image;

                $tokenType = $this->tokenizer->peek();
                continue;
            }

            if ($escape) {
                $image .= $this->consumeToken($tokenType)->image;
                break;
            }
        }
        return $this->_setNodePositionsAndReturn(
            $this->_builder->buildASTLiteral($image)
        );
    }

    /**
     * This method parses a simple literal and configures the position
     * properties.
     *
     * @return PHP_Depend_Code_ASTLiteral
     * @since 0.9.10
     */
    private function _parseLiteral()
    {
        $token = $this->consumeToken($this->tokenizer->peek());

        $node = $this->_builder->buildASTLiteral($token->image);
        $node->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $node;
    }

    /**
     * Extracts all dependencies from a callable signature.
     *
     * @return PHP_Depend_Code_ASTFormalParameters
     * @since 0.9.5
     */
    private function _parseFormalParameters()
    {
        $this->consumeComments();

        $this->_tokenStack->push();

        $formalParameters = $this->_builder->buildASTFormalParameters();

        $this->consumeToken(self::T_PARENTHESIS_OPEN);
        $this->consumeComments();

        $tokenType = $this->tokenizer->peek();

        // Check for function without parameters
        if ($tokenType === self::T_PARENTHESIS_CLOSE) {
            $this->consumeToken(self::T_PARENTHESIS_CLOSE);
            return $this->_setNodePositionsAndReturn($formalParameters);
        }

        while ($tokenType !== self::T_EOF) {

            $formalParameters->addChild(
                $this->_parseFormalParameterOrTypeHintOrByReference()
            );

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            // Check for following parameter
            if ($tokenType !== self::T_COMMA) {
                break;
            }
            $this->consumeToken(self::T_COMMA);
        }
        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        return $this->_setNodePositionsAndReturn($formalParameters);
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
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        $this->_tokenStack->push();

        switch ($tokenType) {

        case self::T_ARRAY:
            $parameter = $this->_parseFormalParameterAndArrayTypeHint();
            break;

        case self::T_STRING:
        case self::T_BACKSLASH:
        case self::T_NAMESPACE:
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
        return $this->_setNodePositionsAndReturn($parameter);
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
        $token = $this->consumeToken(self::T_ARRAY);

        $node = $this->_builder->buildASTArrayType();
        $node->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        $parameter = $this->_parseFormalParameterOrByReference();
        $parameter->addChild($node);

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
        $token = $this->consumeToken(self::T_PARENT);

        $reference = $this->_parseParentReference($token);
        $parameter = $this->_parseFormalParameterOrByReference();
        $parameter->prependChild($reference);

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
        $token = $this->consumeToken(self::T_SELF);

        $self = $this->_builder->buildASTSelfReference($this->_classOrInterface);
        $self->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        $parameter = $this->_parseFormalParameterOrByReference();
        $parameter->addChild($self);

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
        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_BITWISE_AND) {
            return $this->_parseFormalParameterAndByReference();
        }
        return $this->_parseFormalParameter();
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
        $this->consumeToken(self::T_BITWISE_AND);
        $this->consumeComments();

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
        $parameter->addChild($this->_parseVariableDeclarator());

        return $parameter;
    }

    /**
     * Extracts all dependencies from a callable body.
     *
     * @return PHP_Depend_Code_ASTScope
     * @since 0.9.12
     */
    private function _parseScope()
    {
        $scope = $this->_builder->buildASTScope();

        $this->_tokenStack->push();

        $this->consumeComments();
        $this->consumeToken(self::T_CURLY_BRACE_OPEN);

        while (($stmt = $this->_parseOptionalStatement()) !== null) {
            // TODO: Remove if-statement once, we have translated functions and
            //       closures into ast-nodes
            if ($stmt instanceof PHP_Depend_Code_ASTNodeI) {
                $scope->addChild($stmt);
            }
        }

        $this->consumeComments();
        $this->consumeToken(self::T_CURLY_BRACE_CLOSE);

        return $this->_setNodePositionsAndReturn($scope);
    }

    /**
     * Parses an optional statement or returns <b>null</b>.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 0.9.8
     */
    private function _parseOptionalStatement()
    {
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {

        case self::T_ECHO:
            return $this->_parseEchoStatement();

        case self::T_SWITCH:
            return $this->_parseSwitchStatement();

        case self::T_TRY:
            return $this->_parseTryStatement();

        case self::T_THROW:
            return $this->_parseThrowStatement();

        case self::T_IF:
            return $this->_parseIfStatement();

        case self::T_FOR:
            return $this->_parseForStatement();

        case self::T_FOREACH:
            return $this->_parseForeachStatement();

        case self::T_DO:
            return $this->_parseDoWhileStatement();

        case self::T_WHILE:
            return $this->_parseWhileStatement();

        case self::T_RETURN:
            return $this->_parseReturnStatement();

        case self::T_BREAK:
            return $this->_parseBreakStatement();

        case self::T_CONTINUE:
            return $this->_parseContinueStatement();

        case self::T_GOTO:
            return $this->_parseGotoStatement();

        case self::T_GLOBAL:
            return $this->_parseGlobalStatement();

        case self::T_UNSET:
            return $this->_parseUnsetStatement();

        case self::T_STRING:
            if ($this->tokenizer->peekNext() === self::T_COLON) {
                return $this->_parseLabelStatement();
            }
            break;

        case self::T_FUNCTION:
            return $this->_parseFunctionOrClosureDeclaration();

        case self::T_COMMENT:
            return $this->_parseCommentWithOptionalInlineClassOrInterfaceReference();

        case self::T_DOC_COMMENT:
            return $this->_builder->buildASTComment(
                $this->consumeToken(self::T_DOC_COMMENT)->image
            );

        case self::T_CURLY_BRACE_OPEN:
            return $this->_parseRegularScope();

        case self::T_DECLARE:
            return $this->_parseDeclareStatement();

        case self::T_ELSE:
        case self::T_ENDIF:
        case self::T_ELSEIF:
        case self::T_ENDFOR:
        case self::T_ENDWHILE:
        case self::T_ENDSWITCH:
        case self::T_ENDDECLARE:
        case self::T_ENDFOREACH:
        case self::T_CURLY_BRACE_CLOSE:
            return null;

        case self::T_CLOSE_TAG:
            if (($tokenType = $this->_parseNonePhpCode()) === self::T_EOF) {
                return null;
            }
            return $this->_parseOptionalStatement();
        }

        $this->_tokenStack->push();
        $stmt = $this->_builder->buildASTStatement();
        if (($expr = $this->_parseOptionalExpression()) != null) {
            $stmt->addChild($expr);
        }
        $this->_parseStatementTermination();
        return $this->_setNodePositionsAndReturn($stmt);
    }

    /**
     * Parses a sequence of none php code tokens and returns the token type of
     * the next token.
     *
     * @return integer
     * @since 0.9.12
     */
    private function _parseNonePhpCode()
    {
        $this->consumeToken(self::T_CLOSE_TAG);

        $this->_tokenStack->push();
        while (($tokenType = $this->tokenizer->peek()) !== self::T_EOF) {
            switch ($tokenType) {

            case self::T_OPEN_TAG:
            case self::T_OPEN_TAG_WITH_ECHO:
                $this->consumeToken($tokenType);
                $tokenType = $this->tokenizer->peek();
                break 2;

            default:
                $this->consumeToken($tokenType);
                break;
            }
        }
        $this->_tokenStack->pop();

        return $tokenType;
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
        $token = $this->consumeToken(self::T_COMMENT);

        $comment = $this->_builder->buildASTComment($token->image);
        if (preg_match(self::REGEXP_INLINE_TYPE, $token->image, $match)) {
            $comment->addChild(
                $this->_builder->buildASTClassOrInterfaceReference($match[1])
            );
        }

        $comment->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );
        return $comment;
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
        $this->consumeComments();
        $this->consumeToken(self::T_USE);

        // Consume opening parenthesis
        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        while ($this->tokenizer->peek() !== self::T_EOF) {
            // Consume leading comments
            $this->consumeComments();

            // Check for by-ref operator
            if ($this->tokenizer->peek() === self::T_BITWISE_AND) {
                $this->consumeToken(self::T_BITWISE_AND);
                $this->consumeComments();
            }

            // Read bound variable
            $this->consumeToken(self::T_VARIABLE);
            $this->consumeComments();

            // Check for further bound variables
            if ($this->tokenizer->peek() === self::T_COMMA) {
                $this->consumeToken(self::T_COMMA);
                continue;
            }
            break;
        }

        // Consume closing parenthesis
        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_CLOSE);
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
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        $qualifiedName = array();

        // Check for local name
        if ($tokenType === self::T_STRING) {
            $qualifiedName[] = $this->consumeToken(self::T_STRING)->image;

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            // Stop here for simple identifier
            if ($tokenType !== self::T_BACKSLASH) {
                return $qualifiedName;
            }
        } else if ($tokenType === self::T_NAMESPACE) {
            // Consume namespace keyword
            $this->consumeToken(self::T_NAMESPACE);
            $this->consumeComments();

            // Add current namespace as first token
            $qualifiedName = array((string) $this->_namespaceName);

            // Set prefixed flag to true
            $this->_namespacePrefixReplaced = true;
        }

        do {
            // Next token must be a namespace separator
            $this->consumeToken(self::T_BACKSLASH);
            $this->consumeComments();

            // Next token must be a namespace identifier
            $token = $this->consumeToken(self::T_STRING);
            $this->consumeComments();

            // Append to qualified name
            $qualifiedName[] = '\\';
            $qualifiedName[] = $token->image;

            // Get next token type
            $tokenType = $this->tokenizer->peek();
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
        $this->consumeToken(self::T_NAMESPACE);
        $this->consumeComments();

        $tokenType = $this->tokenizer->peek();

        // Search for a namespace identifier
        if ($tokenType === self::T_STRING) {
            // Reset namespace property
            $this->_namespaceName = null;

            $qualifiedName = $this->_parseQualifiedName();

            $this->consumeComments();
            if ($this->tokenizer->peek() === self::T_CURLY_BRACE_OPEN) {
                $this->consumeToken(self::T_CURLY_BRACE_OPEN);
            } else {
                $this->consumeToken(self::T_SEMICOLON);
            }

            // Create a package for this namespace
            $this->_namespaceName = $qualifiedName;

            $this->_useSymbolTable->resetScope();

        } else if ($tokenType === self::T_BACKSLASH) {
            // Same namespace reference, something like:
            //   new namespace\Foo();
            // or:
            //   $x = namespace\foo::bar();

            // Now parse a qualified name
            $this->_parseQualifiedNameRaw();
        } else {
            // Consume opening curly brace
            $this->consumeToken(self::T_CURLY_BRACE_OPEN);

            // Create a package for this namespace
            $this->_namespaceName = '';

            $this->_useSymbolTable->resetScope();
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
        $this->consumeToken(self::T_USE);
        $this->consumeComments();

        // Parse all use declarations
        $this->_parseUseDeclaration();
        $this->consumeComments();

        // Consume closing semicolon
        $this->consumeToken(self::T_SEMICOLON);

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
        $this->consumeComments();

        // Add leading backslash, because aliases must be full qualified
        // http://php.net/manual/en/language.namespaces.importing.php
        if ($fragments[0] !== '\\') {
            array_unshift($fragments, '\\');
        }

        if ($this->tokenizer->peek() === self::T_AS) {
            $this->consumeToken(self::T_AS);
            $this->consumeComments();

            $image = $this->consumeToken(self::T_STRING)->image;
            $this->consumeComments();
        } else {
            $image = end($fragments);
        }

        // Add mapping between image and qualified name to symbol table
        $this->_useSymbolTable->add($image, join('', $fragments));

        // Check for a following use declaration
        if ($this->tokenizer->peek() === self::T_COMMA) {
            // Consume comma token and comments
            $this->consumeToken(self::T_COMMA);
            $this->consumeComments();

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
        $this->_tokenStack->push();

        $token = $this->consumeToken(self::T_CONST);

        $definition = $this->_builder->buildASTConstantDefinition($token->image);
        $definition->setComment($this->_docComment);

        do {
            $definition->addChild($this->_parseConstantDeclarator());

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            if ($tokenType === self::T_SEMICOLON) {
                break;
            }
            $this->consumeToken(self::T_COMMA);
        } while ($tokenType !== self::T_EOF);


        $definition = $this->_setNodePositionsAndReturn($definition);

        $this->consumeToken(self::T_SEMICOLON);

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
        $this->consumeComments();
        $this->_tokenStack->push();

        $token = $this->consumeToken(self::T_STRING);

        $this->consumeComments();
        $this->consumeToken(self::T_EQUAL);

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
        $token = $this->consumeToken(self::T_STATIC);
        $this->consumeComments();

        // Fetch next token type
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === self::T_PARENTHESIS_OPEN
            || $tokenType === self::T_DOUBLE_COLON
        ) {
            $static = $this->_parseStaticReference($token);

            $prefix = $this->_parseStaticMemberPrimaryPrefix($static);
            return $this->_setNodePositionsAndReturn($prefix);
        }

        $declaration = $this->_parseStaticVariableDeclaration($token);
        return $this->_setNodePositionsAndReturn($declaration);

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
        $this->consumeComments();

        // Fetch next token type
        $tokenType = $this->tokenizer->peek();

        while ($tokenType !== self::T_EOF) {
            $staticDeclaration->addChild($this->_parseVariableDeclarator());

            $this->consumeComments();

            // Semicolon terminates static declaration
            $tokenType = $this->tokenizer->peek();
            if ($tokenType === self::T_SEMICOLON) {
                break;
            }
            // We are here, so there must be a next declarator
            $this->consumeToken(self::T_COMMA);
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
        $this->_tokenStack->push();

        $name = $this->consumeToken(self::T_VARIABLE)->image;
        $this->consumeComments();

        $declarator = $this->_builder->buildASTVariableDeclarator($name);

        if ($this->tokenizer->peek() === self::T_EQUAL) {
            $this->consumeToken(self::T_EQUAL);
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
        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_ARRAY) {
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

        $this->consumeComments();

        // By default all parameters positive signed
        $signed = 1;

        $tokenType = $this->tokenizer->peek();
        while ($tokenType !== self::T_EOF) {

            switch ($tokenType) {

            case self::T_COMMA:
            case self::T_SEMICOLON:
            case self::T_PARENTHESIS_CLOSE:
                if ($defaultValue->isValueAvailable() === true) {
                    return $defaultValue;
                }
                throw new PHP_Depend_Parser_MissingValueException($this->tokenizer);

            case self::T_NULL:
                $token = $this->consumeToken(self::T_NULL);
                $defaultValue->setValue(null);
                break;

            case self::T_TRUE:
                $token = $this->consumeToken(self::T_TRUE);
                $defaultValue->setValue(true);
                break;

            case self::T_FALSE:
                $token = $this->consumeToken(self::T_FALSE);
                $defaultValue->setValue(false);
                break;

            case self::T_LNUMBER:
                $token = $this->consumeToken(self::T_LNUMBER);
                $defaultValue->setValue($signed * (int) $token->image);
                break;

            case self::T_DNUMBER:
                $token = $this->consumeToken(self::T_DNUMBER);
                $defaultValue->setValue($signed * (double) $token->image);
                break;

            case self::T_CONSTANT_ENCAPSED_STRING:
                $token = $this->consumeToken(self::T_CONSTANT_ENCAPSED_STRING);
                $defaultValue->setValue(substr($token->image, 1, -1));
                break;

            case self::T_DOUBLE_COLON:
                $this->consumeToken(self::T_DOUBLE_COLON);
                break;

            case self::T_PLUS:
                $this->consumeToken(self::T_PLUS);
                break;

            case self::T_MINUS:
                $this->consumeToken(self::T_MINUS);
                $signed *= -1;
                break;

            case self::T_DOUBLE_QUOTE:
                $defaultValue->setValue($this->_parseStringSequence($tokenType));
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
                $this->consumeToken($tokenType);
                break;

            default:
                throw new PHP_Depend_Parser_UnexpectedTokenException(
                    $this->tokenizer->next(),
                    $this->tokenizer->getSourceFile()
                );
            }

            $this->consumeComments();

            $tokenType = $this->tokenizer->peek();
        }

        // We should never reach this, so throw an exception
        throw new PHP_Depend_Parser_TokenStreamEndException($this->tokenizer);
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
        $this->consumeToken(self::T_ARRAY);
        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        $parenthesis = 1;

        $tokenType = $this->tokenizer->peek();
        while ($tokenType !== self::T_EOF) {

            switch ($tokenType) {

            case self::T_PARENTHESIS_CLOSE:
                if (--$parenthesis === 0) {
                    break 2;
                }
                $this->consumeToken(self::T_PARENTHESIS_CLOSE);
                break;

            case self::T_PARENTHESIS_OPEN:
                $this->consumeToken(self::T_PARENTHESIS_OPEN);
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
                $this->consumeToken($tokenType);
                break;

            default:
                break 2;
            }

            $tokenType = $this->tokenizer->peek();
        }

        // Read closing parenthesis
        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        $defaultValue = new PHP_Depend_Code_Value();
        $defaultValue->setValue($staticValue);

        return $defaultValue;
    }

    /**
     * Checks if the given expression is a read/write variable as defined in
     * the PHP zend_language_parser.y definition.
     *
     * @param PHP_Depend_Code_ASTNode $expression The context node instance.
     *
     * @return boolean
     * @since 0.10.0
     */
    private function _isReadWriteVariable($expression)
    {
        return ($expression instanceof PHP_Depend_Code_ASTVariable
            || $expression instanceof PHP_Depend_Code_ASTFunctionPostfix
            || $expression instanceof PHP_Depend_Code_ASTVariableVariable
            || $expression instanceof PHP_Depend_Code_ASTCompoundVariable
            || $expression instanceof PHP_Depend_Code_ASTMemberPrimaryPrefix);
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
        return ltrim($this->_getNamespaceOrPackageName() . '\\' . $localName, '\\');
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
        if ($this->tokenizer->prev() !== self::T_OPEN_TAG) {
            return false;
        }

        $notExpectedTags = array(
            self::T_CLASS,
            self::T_FINAL,
            self::T_ABSTRACT,
            self::T_FUNCTION,
            self::T_INTERFACE
        );

        return !in_array($this->tokenizer->peek(), $notExpectedTags, true);
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
    protected function consumeToken($tokenType)
    {
        $token = $this->tokenizer->next();
        if ($token === self::T_EOF) {
            throw new PHP_Depend_Parser_TokenStreamEndException($this->tokenizer);
        } else if ($token->type == $tokenType) {
            return $this->_tokenStack->add($token);
        }
        throw new PHP_Depend_Parser_UnexpectedTokenException(
            $token,
            $this->tokenizer->getSourceFile()
        );
    }

    /**
     * This method will consume all comment tokens from the token stream.
     *
     * @return void
     */
    protected function consumeComments()
    {
        $type = $this->tokenizer->peek();
        while ($type == self::T_COMMENT || $type == self::T_DOC_COMMENT) {
            $this->_tokenStack->add($this->tokenizer->next());
            $type = $this->tokenizer->peek();
        }
    }
}
