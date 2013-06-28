<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
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
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
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
     * @var boolean
     */
    private $namespacePrefixReplaced = false;

    /**
     * The name of the last detected namespace.
     *
     * @var string
     */
    private $namespaceName;

    /**
     * Last parsed package tag.
     *
     * @var string
     */
    private $packageName = self::DEFAULT_PACKAGE;

    /**
     * The package defined in the file level comment.
     *
     * @var string
     */
    private $globalPackageName = self::DEFAULT_PACKAGE;

    /**
     * The used data structure builder.
     *
     * @var PHP_Depend_BuilderI
     */
    protected $builder;

    /**
     * The currently parsed file instance.
     *
     * @var PHP_Depend_Code_File
     */
    private $sourceFile;

    /**
     * The symbol table used to handle PHP 5.3 use statements.
     *
     * @var PHP_Depend_Parser_SymbolTable
     */
    private $useSymbolTable;

    /**
     * The last parsed doc comment or <b>null</b>.
     *
     * @var string
     */
    private $docComment;

    /**
     * Bitfield of last parsed modifiers.
     *
     * @var integer
     */
    private $modifiers = 0;

    /**
     * The actually parsed class or interface instance.
     *
     * @var PHP_Depend_Code_AbstractClassOrInterface
     */
    private $classOrInterface;

    /**
     * If this property is set to <b>true</b> the parser will ignore all doc
     * comment annotations.
     *
     * @var boolean
     */
    private $ignoreAnnotations = false;

    /**
     * Stack with all active token scopes.
     *
     * @var PHP_Depend_Parser_TokenStack
     */
    private $tokenStack;

    /**
     * Used identifier builder instance.
     *
     * @var PHP_Depend_Util_UuidBuilder
     * @since 0.9.12
     */
    private $uuidBuilder = null;

    /**
     * The maximum valid nesting level allowed.
     *
     * @var integer
     * @since 0.9.12
     */
    private $maxNestingLevel = 1024;

    /**
     *
     * @var PHP_Depend_Util_Cache_Driver
     * @since 0.10.0
     */
    protected $cache;

    /*
     * The used code tokenizer.
     *
     * @var PHP_Depend_TokenizerI
     */
    protected $tokenizer;

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
        $this->builder   = $builder;
        $this->cache     = $cache;

        $this->uuidBuilder    = new PHP_Depend_Util_UuidBuilder();
        $this->tokenStack     = new PHP_Depend_Parser_TokenStack();

        $this->useSymbolTable = new PHP_Depend_Parser_SymbolTable();

        $this->builder->setCache($this->cache);
    }

    /**
     * Sets the ignore annotations flag. This means that the parser will ignore
     * doc comment annotations.
     *
     * @return void
     */
    public function setIgnoreAnnotations()
    {
        $this->ignoreAnnotations = true;
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
        $this->maxNestingLevel = $maxNestingLevel;
    }

    /**
     * Returns the maximum allowed nesting/recursion level.
     *
     * @return integer
     * @since 0.9.12
     */
    protected function getMaxNestingLevel()
    {
        return $this->maxNestingLevel;
    }

    /**
     * Parses the contents of the tokenizer and generates a node tree based on
     * the found tokens.
     *
     * @return void
     */
    public function parse()
    {
        $this->sourceFile = $this->tokenizer->getSourceFile();
        $this->sourceFile
            ->setCache($this->cache)
            ->setUuid($this->uuidBuilder->forFile($this->sourceFile));

        $hash = md5_file($this->sourceFile->getFileName());

        if ($this->cache->restore($this->sourceFile->getUuid(), $hash)) {
            return;
        }

        $this->cache->remove($this->sourceFile->getUuid());

        $this->setUpEnvironment();

        $this->tokenStack->push();

        PHP_Depend_Util_Log::debug('Processing file ' . $this->sourceFile);

        $tokenType = $this->tokenizer->peek();
        while ($tokenType !== self::T_EOF) {

            switch ($tokenType) {

            case self::T_COMMENT:
                $this->consumeToken(self::T_COMMENT);
                break;

            case self::T_DOC_COMMENT:
                $comment = $this->consumeToken(self::T_DOC_COMMENT)->image;

                $this->packageName = $this->parsePackageAnnotation($comment);
                $this->docComment  = $comment;
                break;

            case self::T_USE:
                // Parse a use statement. This method has no return value but it
                // creates a new entry in the symbol map.
                $this->parseUseDeclarations();
                break;

            case self::T_NAMESPACE:
                $this->parseNamespaceDeclaration();
                break;

            case self::T_NO_PHP:
            case self::T_OPEN_TAG:
            case self::T_OPEN_TAG_WITH_ECHO:
                $this->consumeToken($tokenType);
                $this->reset();
                break;

            case self::T_CLOSE_TAG:
                $this->parseNonePhpCode();
                $this->reset();
                break;

            default:
                if (null === $this->parseOptionalStatement()) {
                    // Consume whatever token
                    $this->consumeToken($tokenType);
                }
                break;
            }

            $tokenType = $this->tokenizer->peek();
        }

        $this->sourceFile->setTokens($this->tokenStack->pop());
        $this->cache->store(
            $this->sourceFile->getUuid(),
            $this->sourceFile,
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

        $this->useSymbolTable->createScope();

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

        $this->useSymbolTable->destroyScope();
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
        $this->packageName = self::DEFAULT_PACKAGE;
        $this->docComment  = null;
        $this->modifiers   = $modifiers;
    }

    /**
     * Tests if the given token type is a reserved keyword in the supported PHP
     * version.
     *
     * @param $tokenType
     * @return boolean
     * @since 1.1.1
     */
    abstract protected function isKeyword($tokenType);

    /**
     * Will return <b>true</b> if the given <b>$tokenType</b> is a valid class
     * name part.
     *
     * @param integer $tokenType The type of a parsed token.
     * @return boolean
     * @since 0.10.6
     */
    protected abstract function isClassName($tokenType);

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
     * Parses a trait declaration.
     *
     * @return PHP_Depend_Code_Trait
     * @since 1.0.0
     */
    private function parseTraitDeclaration()
    {
        $this->tokenStack->push();

        $trait = $this->parseTraitSignature();
        $trait = $this->parseTypeBody($trait);
        $trait->setTokens($this->tokenStack->pop());

        $this->reset();

        return $trait;
    }

    /**
     * Parses the signature of a trait.
     *
     * @return PHP_Depend_Code_Trait
     */
    private function parseTraitSignature()
    {
        $this->consumeToken(self::T_TRAIT);
        $this->consumeComments();

        $qualifiedName = $this->createQualifiedTypeName($this->parseClassName());

        $trait = $this->builder->buildTrait($qualifiedName);
        $trait->setSourceFile($this->sourceFile);
        $trait->setDocComment($this->docComment);
        $trait->setUuid($this->uuidBuilder->forClassOrInterface($trait));
        $trait->setUserDefined();

        return $trait;
    }

    /**
     * Parses the dependencies in a interface signature.
     *
     * @return PHP_Depend_Code_Interface
     */
    private function parseInterfaceDeclaration()
    {
        $this->tokenStack->push();

        $interface = $this->parseInterfaceSignature();
        $interface = $this->parseTypeBody($interface);
        $interface->setTokens($this->tokenStack->pop());

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
    private function parseInterfaceSignature()
    {
        $this->consumeToken(self::T_INTERFACE);
        $this->consumeComments();

        $qualifiedName = $this->createQualifiedTypeName($this->parseClassName());

        $interface = $this->builder->buildInterface($qualifiedName);
        $interface->setSourceFile($this->sourceFile);
        $interface->setDocComment($this->docComment);
        $interface->setUuid($this->uuidBuilder->forClassOrInterface($interface));
        $interface->setUserDefined();

        return $this->parseOptionalExtendsList($interface);
    }

    /**
     * Parses an optional interface list of an interface declaration.
     *
     * @param PHP_Depend_Code_Interface $interface The declaring interface.
     *
     * @return PHP_Depend_Code_Interface
     * @since 0.10.2
     */
    private function parseOptionalExtendsList(PHP_Depend_Code_Interface $interface)
    {
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === self::T_EXTENDS) {
            $this->consumeToken(self::T_EXTENDS);
            $this->parseInterfaceList($interface);
        }
        return $interface;
    }

    /**
     * Parses the dependencies in a class signature.
     *
     * @return PHP_Depend_Code_Class
     */
    private function parseClassDeclaration()
    {
        $this->tokenStack->push();

        $class = $this->parseClassSignature();
        $class = $this->parseTypeBody($class);
        $class->setTokens($this->tokenStack->pop());

        $this->reset();

        return $class;
    }

    /**
     * Parses the signature of a class.
     *
     * The signature of a class consists of optional class modifiers like, final
     * or abstract, the T_CLASS token, the class name, an optional parent class
     * and an optional list of implemented interfaces.
     *
     * @return PHP_Depend_Code_Class
     * @since 1.0.0
     */
    private function parseClassSignature()
    {
        $this->parseClassModifiers();
        $this->consumeToken(self::T_CLASS);
        $this->consumeComments();

        $qualifiedName = $this->createQualifiedTypeName($this->parseClassName());

        $class = $this->builder->buildClass($qualifiedName);
        $class->setSourceFile($this->sourceFile);
        $class->setModifiers($this->modifiers);
        $class->setDocComment($this->docComment);
        $class->setUuid($this->uuidBuilder->forClassOrInterface($class));
        $class->setUserDefined();

        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === self::T_EXTENDS) {
            $class = $this->parseClassExtends($class);

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();
        }

        if ($tokenType === self::T_IMPLEMENTS) {
            $this->consumeToken(self::T_IMPLEMENTS);
            $this->parseInterfaceList($class);
        }

        return $class;
    }

    /**
     * This method parses an optional class modifier. Valid class modifiers are
     * <b>final</b> or <b>abstract</b>.
     *
     * @return void
     */
    private function parseClassModifiers()
    {
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === self::T_ABSTRACT) {
            $this->consumeToken(self::T_ABSTRACT);
            $this->modifiers |= self::IS_EXPLICIT_ABSTRACT;
        } else if ($tokenType === self::T_FINAL) {
            $this->consumeToken(self::T_FINAL);
            $this->modifiers |= self::IS_FINAL;
        }

        $this->consumeComments();
    }

    /**
     * Parses a parent class declaration for the given <b>$class</b>.
     *
     * @param PHP_Depend_Code_Class $class The context class instance.
     *
     * @return PHP_Depend_Code_Class
     * @since 1.0.0
     */
    private function parseClassExtends(PHP_Depend_Code_Class $class)
    {
        $this->consumeToken(self::T_EXTENDS);
        $this->tokenStack->push();

        $class->setParentClassReference(
            $this->setNodePositionsAndReturn(
                $this->builder->buildAstClassReference(
                    $this->parseQualifiedName()
                )
            )
        );

        return $class;
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
    private function parseInterfaceList(
        PHP_Depend_Code_AbstractClassOrInterface $abstractType
    ) {
        while (true) {

            $this->tokenStack->push();

            $abstractType->addInterfaceReference(
                $this->setNodePositionsAndReturn(
                    $this->builder->buildAstClassOrInterfaceReference(
                        $this->parseQualifiedName()
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
     * Parses a class/interface/trait body.
     *
     * @param PHP_Depend_Code_AbstractType $type Context class, trait or interface
     *
     * @return PHP_Depend_Code_AbstractType
     */
    private function parseTypeBody(PHP_Depend_Code_AbstractType $type)
    {
        $this->classOrInterface = $type;

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

                $methodOrProperty = $this->parseMethodOrFieldDeclaration(
                    $defaultModifier
                );

                if ($methodOrProperty instanceof PHP_Depend_Code_ASTNode) {
                    $type->addChild($methodOrProperty);
                }

                $this->reset();
                break;

            case self::T_CONST:
                $type->addChild($this->parseConstantDefinition());
                $this->reset();
                break;

            case self::T_CURLY_BRACE_CLOSE:
                $this->consumeToken(self::T_CURLY_BRACE_CLOSE);

                $this->reset();

                // Reset context class or interface instance
                $this->classOrInterface = null;

                // Stop processing
                return $type;

            case self::T_COMMENT:
                $token = $this->consumeToken(self::T_COMMENT);

                $comment = $this->builder->buildAstComment($token->image);
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

                $comment = $this->builder->buildAstComment($token->image);
                $comment->configureLinesAndColumns(
                    $token->startLine,
                    $token->endLine,
                    $token->startColumn,
                    $token->endColumn
                );

                $type->addChild($comment);

                $this->docComment = $token->image;
                break;

            case self::T_USE:
                $type->addChild($this->parseTraitUseStatement());
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
    private function parseMethodOrFieldDeclaration($modifiers = 0)
    {
        $this->tokenStack->push();

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
                $method = $this->parseMethodDeclaration();
                $method->setModifiers($modifiers);
                $method->setSourceFile($this->sourceFile);
                $method->setUuid($this->uuidBuilder->forMethod($method));
                $method->setTokens($this->tokenStack->pop());
                return $method;

            case self::T_VARIABLE:
                $declaration = $this->parseFieldDeclaration();
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
    private function parseFieldDeclaration()
    {
        $declaration = $this->builder->buildAstFieldDeclaration();
        $declaration->setComment($this->docComment);

        $type = $this->parseFieldDeclarationType();
        if ($type !== null) {
            $declaration->addChild($type);
        }

        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        while ($tokenType !== self::T_EOF) {
            $declaration->addChild($this->parseVariableDeclarator());

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            if ($tokenType !== self::T_COMMA) {
                break;
            }
            $this->consumeToken(self::T_COMMA);

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();
        }

        $this->setNodePositionsAndReturn($declaration);

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
    private function parseFunctionOrClosureDeclaration()
    {
        $this->tokenStack->push();

        $this->consumeToken(self::T_FUNCTION);
        $this->consumeComments();

        $returnReference = $this->parseOptionalByReference();

        if ($this->isNextTokenFormalParameterList()) {
            $callable = $this->parseClosureDeclaration();
            return $this->setNodePositionsAndReturn($callable);
        } else {
            $callable = $this->parseFunctionDeclaration();
            $this->sourceFile->addChild($callable);
        }

        $callable->setDocComment($this->docComment);
        $callable->setTokens($this->tokenStack->pop());
        $this->prepareCallable($callable);

        if ($returnReference) {
            $callable->setReturnsReference();
        }

        $this->reset();

        return $callable;
    }

    /**
     * Parses an optional by reference token. The return value will be
     * <b>true</b> when a reference token was found, otherwise this method will
     * return <b>false</b>.
     *
     * @return boolean
     * @since 0.9.8
     */
    private function parseOptionalByReference()
    {
        if ($this->isNextTokenByReference()) {
            return $this->parseByReference();
        }
        return false;
    }

    /**
     * Tests that the next available token is the returns by reference token.
     *
     * @return boolean
     * @since 0.9.8
     */
    private function isNextTokenByReference()
    {
        return ($this->tokenizer->peek() === self::T_BITWISE_AND);
    }

    /**
     * This method parses a returns by reference token and returns <b>true</b>.
     *
     * @return boolean
     */
    private function parseByReference()
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
    private function isNextTokenFormalParameterList()
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
    private function parseFunctionDeclaration()
    {
        $this->consumeComments();

        // Next token must be the function identifier
        $functionName = $this->parseFunctionName();

        $function = $this->builder->buildFunction($functionName);
        $function->setSourceFile($this->sourceFile);
        $function->setUuid($this->uuidBuilder->forFunction($function));

        $this->parseCallableDeclaration($function);

        // First check for an existing namespace
        if ($this->namespaceName !== null) {
            $packageName = $this->namespaceName;
        } else if ($this->packageName !== self::DEFAULT_PACKAGE) {
            $packageName = $this->packageName;
        } else {
            $packageName = $this->globalPackageName;
        }

        $this->builder
            ->buildPackage($packageName)
            ->addFunction($function);

        // Store function in source file, because we need them during the file's
        // __wakeup() phase for function declarations within another function or
        // method declaration.
        $this->sourceFile->addChild($function);

        return $function;
    }

    /**
     * This method parses a method declaration.
     *
     * @return PHP_Depend_Code_Method
     * @since 0.9.5
     */
    private function parseMethodDeclaration()
    {
        // Read function keyword
        $this->consumeToken(self::T_FUNCTION);
        $this->consumeComments();

        $returnsReference = $this->parseOptionalByReference();

        $methodName = $this->parseFunctionName();

        $method = $this->builder->buildMethod($methodName);
        $method->setDocComment($this->docComment);
        $method->setSourceFile($this->sourceFile);

        $this->classOrInterface->addMethod($method);

        $this->parseCallableDeclaration($method);
        $this->prepareCallable($method);

        if ($returnsReference === true) {
            $method->setReturnsReference();
        }

        return $method;
    }

    /**
     * This method parses a PHP 5.3 closure or lambda function.
     *
     * @return PHP_Depend_Code_ASTClosure
     * @since 0.9.5
     */
    private function parseClosureDeclaration()
    {
        $this->tokenStack->push();

        if (self::T_FUNCTION === $this->tokenizer->peek()) {
            $this->consumeToken(self::T_FUNCTION);
        }

        $closure = $this->builder->buildAstClosure();
        $closure->setReturnsByReference($this->parseOptionalByReference());
        $closure->addChild($this->parseFormalParameters());
        $closure = $this->parseOptionalBoundVariables($closure);
        $closure->addChild($this->parseScope());

        return $this->setNodePositionsAndReturn($closure);
    }

    /**
     * Parses a function or a method and adds it to the parent context node.
     *
     * @param PHP_Depend_Code_AbstractCallable $callable The context callable.
     *
     * @return void
     */
    private function parseCallableDeclaration(
        PHP_Depend_Code_AbstractCallable $callable
    ) {
        $callable->addChild($this->parseFormalParameters());

        $this->consumeComments();
        if ($this->tokenizer->peek() == self::T_CURLY_BRACE_OPEN) {
            $callable->addChild($this->parseScope());
        } else {
            $this->consumeToken(self::T_SEMICOLON);
        }
    }

    /**
     * Parses a trait use statement.
     *
     * @return PHP_Depend_Code_ASTTraitUseStatement
     * @since 1.0.0
     */
    private function parseTraitUseStatement()
    {
        $this->tokenStack->push();
        $this->consumeToken(self::T_USE);

        $useStatement = $this->builder->buildAstTraitUseStatement();
        $useStatement->addChild($this->parseTraitReference());

        $this->consumeComments();
        while (self::T_COMMA === $this->tokenizer->peek()) {
            $this->consumeToken(self::T_COMMA);
            $useStatement->addChild($this->parseTraitReference());
        }

        return $this->setNodePositionsAndReturn(
            $this->parseOptionalTraitAdaptation($useStatement)
        );
    }

    /**
     * Parses a trait reference instance.
     *
     * @return PHP_Depend_Code_ASTTraitReference
     * @since 1.0.0
     */
    private function parseTraitReference()
    {
        $this->consumeComments();
        $this->tokenStack->push();

        return $this->setNodePositionsAndReturn(
            $this->builder->buildAstTraitReference(
                $this->parseQualifiedName()
            )
        );
    }

    /**
     * Parses the adaptation list of the given use statement or simply reads
     * the terminating semicolon, when no adaptation list exists.
     *
     * @param PHP_Depend_Code_ASTTraitUseStatement $useStatement The parent use
     *
     * @return PHP_Depend_Code_ASTTraitUseStatement
     * @since 1.0.0
     */
    private function parseOptionalTraitAdaptation(
        PHP_Depend_Code_ASTTraitUseStatement $useStatement
    ) {
        $this->consumeComments();
        if (self::T_CURLY_BRACE_OPEN === $this->tokenizer->peek()) {
            $useStatement->addChild($this->parseTraitAdaptation());
        } else {
            $this->consumeToken(self::T_SEMICOLON);
        }
        return $useStatement;
    }

    /**
     * Parses the adaptation expression of a trait use statement.
     *
     * @return PHP_Depend_Code_ASTTraitAdaptation
     * @since 1.0.0
     */
    private function parseTraitAdaptation()
    {
        $this->tokenStack->push();

        $adaptation = $this->builder->buildAstTraitAdaptation();

        $this->consumeToken(self::T_CURLY_BRACE_OPEN);

        do {
            $this->tokenStack->push();

            $reference = $this->parseTraitMethodReference();
            $this->consumeComments();

            if (self::T_AS === $this->tokenizer->peek()) {
                $stmt = $this->parseTraitAdaptationAliasStatement($reference);
            } else {
                $stmt = $this->parseTraitAdaptationPrecedenceStatement($reference);
            }

            $this->consumeComments();
            $this->consumeToken(self::T_SEMICOLON);

            $adaptation->addChild($this->setNodePositionsAndReturn($stmt));

            $this->consumeComments();
        } while (self::T_CURLY_BRACE_CLOSE !== $this->tokenizer->peek());

        $this->consumeToken(self::T_CURLY_BRACE_CLOSE);

        return $this->setNodePositionsAndReturn($adaptation);
    }

    /**
     * Parses a trait method reference and returns the found reference as an
     * <b>array</b>.
     *
     * The returned array with contain only one element, when the referenced
     * method is specified by the method's name, without the declaring trait.
     * When the method reference contains the declaring trait the returned
     * <b>array</b> will contain two elements. The first element is the plain
     * method name and the second element is an instance of the
     * {@link PHP_Depend_Code_ASTTraitReference} class that represents the
     * declaring trait.
     *
     * @return array
     * @since 1.0.0
     */
    private function parseTraitMethodReference()
    {
        $this->tokenStack->push();

        $qualifiedName = $this->parseQualifiedName();

        $this->consumeComments();
        if (self::T_DOUBLE_COLON === $this->tokenizer->peek()) {

            $traitReference = $this->setNodePositionsAndReturn(
                $this->builder->buildAstTraitReference($qualifiedName)
            );

            $this->consumeToken(self::T_DOUBLE_COLON);
            $this->consumeComments();

            return array($this->parseFunctionName(), $traitReference);
        }
        $this->tokenStack->pop();

        return array($qualifiedName);
    }

    /**
     * Parses a trait adaptation alias statement.
     *
     * @param array $reference Parsed method reference array.
     *
     * @return PHP_Depend_Code_ASTTraitAdaptationAlias
     * @since 1.0.0
     */
    private function parseTraitAdaptationAliasStatement(array $reference)
    {
        $stmt = $this->builder->buildAstTraitAdaptationAlias($reference[0]);

        if (2 === count($reference)) {
            $stmt->addChild($reference[1]);
        }

        $this->consumeToken(self::T_AS);
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {

        case self::T_PUBLIC:
            $stmt->setNewModifier(PHP_Depend_ConstantsI::IS_PUBLIC);
            $this->consumeToken(self::T_PUBLIC);
            $this->consumeComments();
            break;

        case self::T_PROTECTED:
            $stmt->setNewModifier(PHP_Depend_ConstantsI::IS_PROTECTED);
            $this->consumeToken(self::T_PROTECTED);
            $this->consumeComments();
            break;

        case self::T_PRIVATE:
            $stmt->setNewModifier(PHP_Depend_ConstantsI::IS_PRIVATE);
            $this->consumeToken(self::T_PRIVATE);
            $this->consumeComments();
            break;
        }

        if (self::T_SEMICOLON !== $this->tokenizer->peek()) {
            $stmt->setNewName($this->parseFunctionName());
        }
        return $stmt;
    }

    /**
     * Parses a trait adaptation precedence statement.
     *
     * @param array $reference Parsed method reference array.
     *
     * @return PHP_Depend_Code_ASTTraitAdaptationPrecedence
     * @throws PHP_Depend_Parser_InvalidStateException
     * @since 1.0.0
     */
    private function parseTraitAdaptationPrecedenceStatement(array $reference)
    {
        if (count($reference) < 2) {
            throw new PHP_Depend_Parser_InvalidStateException(
                $this->tokenizer->next()->startLine,
                $this->sourceFile->getFileName(),
                'Expecting full qualified trait method name.'
            );
        }

        $stmt = $this->builder->buildAstTraitAdaptationPrecedence($reference[0]);
        $stmt->addChild($reference[1]);

        $this->consumeToken(self::T_INSTEADOF);
        $this->consumeComments();

        $stmt->addChild($this->parseTraitReference());

        $this->consumeComments();
        while (self::T_COMMA === $this->tokenizer->peek()) {
            $this->consumeToken(self::T_COMMA);
            $stmt->addChild($this->parseTraitReference());
            $this->consumeComments();
        }

        return $stmt;
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
    private function parseAllocationExpression()
    {
        $this->tokenStack->push();

        $token = $this->consumeToken(self::T_NEW);

        $allocation = $this->builder->buildAstAllocationExpression($token->image);
        $allocation = $this->parseExpressionTypeReference($allocation, true);

        if ($this->isNextTokenArguments()) {
            $allocation->addChild($this->parseArguments());
        }
        return $this->setNodePositionsAndReturn($allocation);
    }

    /**
     * Parses a eval-expression node.
     *
     * @return PHP_Depend_Code_ASTEvalExpression
     * @since 0.9.12
     */
    private function parseEvalExpression()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_EVAL);

        $expr = $this->builder->buildAstEvalExpression($token->image);
        $expr->addChild($this->parseParenthesisExpression());

        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * This method parses an exit-expression.
     *
     * @return PHP_Depend_Code_ASTExitExpression
     * @since 0.9.12
     */
    private function parseExitExpression()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_EXIT);

        $expr = $this->builder->buildAstExitExpression($token->image);

        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_PARENTHESIS_OPEN) {
            $expr->addChild($this->parseParenthesisExpression());
        }
        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * Parses a clone-expression node.
     *
     * @return PHP_Depend_Code_ASTCloneExpression
     * @since 0.9.12
     */
    private function parseCloneExpression()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_CLONE);

        $expr = $this->builder->buildAstCloneExpression($token->image);
        $expr->addChild($this->parseExpression());

        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * This method parses a single list-statement node.
     *
     * @return PHP_Depend_Code_ASTListExpression
     * @author Joey Mazzarelli
     * @since 0.9.12
     */
    private function parseListExpression()
    {
        $this->tokenStack->push();

        $token = $this->consumeToken(self::T_LIST);
        $this->consumeComments();

        $list = $this->builder->buildAstListExpression($token->image);

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

            case self::T_LIST:
                $list->addChild($this->parseListExpression());
                $this->consumeComments();
                break;

            default:
                $list->addChild($this->parseVariableOrConstantOrPrimaryPrefix());
                $this->consumeComments();
                break;
            }
        }

        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        return $this->setNodePositionsAndReturn($list);
    }

    /**
     * Parses a include-expression node.
     *
     * @return PHP_Depend_Code_ASTIncludeExpression
     * @since 0.9.12
     */
    private function parseIncludeExpression()
    {
        $expr = $this->builder->buildAstIncludeExpression();

        return $this->parseRequireOrIncludeExpression($expr, self::T_INCLUDE);
    }

    /**
     * Parses a include_once-expression node.
     *
     * @return PHP_Depend_Code_ASTIncludeExpression
     * @since 0.9.12
     */
    private function parseIncludeOnceExpression()
    {
        $expr = $this->builder->buildAstIncludeExpression();
        $expr->setOnce();

        return $this->parseRequireOrIncludeExpression($expr, self::T_INCLUDE_ONCE);
    }

    /**
     * Parses a require-expression node.
     *
     * @return PHP_Depend_Code_ASTRequireExpression
     * @since 0.9.12
     */
    private function parseRequireExpression()
    {
        $expr = $this->builder->buildAstRequireExpression();

        return $this->parseRequireOrIncludeExpression($expr, self::T_REQUIRE);
    }

    /**
     * Parses a require_once-expression node.
     *
     * @return PHP_Depend_Code_ASTRequireExpression
     * @since 0.9.12
     */
    private function parseRequireOnceExpression()
    {
        $expr = $this->builder->buildAstRequireExpression();
        $expr->setOnce();

        return $this->parseRequireOrIncludeExpression($expr, self::T_REQUIRE_ONCE);
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
    private function parseRequireOrIncludeExpression(
        PHP_Depend_Code_ASTExpression $expr, $type
    ) {
        $this->tokenStack->push();

        $this->consumeToken($type);
        $this->consumeComments();

        if ($this->tokenizer->peek() === self::T_PARENTHESIS_OPEN) {
            $this->consumeToken(self::T_PARENTHESIS_OPEN);
            $expr->addChild($this->parseOptionalExpression());
            $this->consumeToken(self::T_PARENTHESIS_CLOSE);
        } else {
            $expr->addChild($this->parseOptionalExpression());
        }

        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * Parses a cast-expression node.
     *
     * @return PHP_Depend_Code_ASTCaseExpression
     * @since 0.10.0
     */
    private function parseCastExpression()
    {
        $token = $this->consumeToken($this->tokenizer->peek());

        $expr = $this->builder->buildAstCastExpression($token->image);
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
     * @param array &$expressions List of previous parsed expression nodes.
     *
     * @return PHP_Depend_Code_ASTExpression
     * @since 0.10.0
     */
    private function parseIncrementExpression(array &$expressions)
    {
        if ($this->isReadWriteVariable(end($expressions))) {
            return $this->parsePostIncrementExpression(array_pop($expressions));
        }
        return $this->parsePreIncrementExpression();
    }

    /**
     * Parses a post increment-expression and adds the given child to that node.
     *
     * @param PHP_Depend_Code_ASTNode $child The child expression node.
     *
     * @return PHP_Depend_Code_ASTPostfixExpression
     * @since 0.10.0
     */
    private function parsePostIncrementExpression(PHP_Depend_Code_ASTNode $child)
    {
        $token = $this->consumeToken(self::T_INC);

        $expr = $this->builder->buildAstPostfixExpression($token->image);
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
    private function parsePreIncrementExpression()
    {
        $token = $this->consumeToken(self::T_INC);

        $expr = $this->builder->buildAstPreIncrementExpression();
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
     * @param array &$expressions List of previous parsed expression nodes.
     *
     * @return PHP_Depend_Code_ASTExpression
     * @since 0.10.0
     */
    private function parseDecrementExpression(array &$expressions)
    {
        if ($this->isReadWriteVariable(end($expressions))) {
            return $this->parsePostDecrementExpression(array_pop($expressions));
        }
        return $this->parsePreDecrementExpression();
    }

    /**
     * Parses a post decrement-expression and adds the given child to that node.
     *
     * @param PHP_Depend_Code_ASTNode $child The child expression node.
     *
     * @return PHP_Depend_Code_ASTPostfixExpression
     * @since 0.10.0
     */
    private function parsePostDecrementExpression(PHP_Depend_Code_ASTNode $child)
    {
        $token = $this->consumeToken(self::T_DEC);

        $expr = $this->builder->buildAstPostfixExpression($token->image);
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
    private function parsePreDecrementExpression()
    {
        $token = $this->consumeToken(self::T_DEC);

        $expr = $this->builder->buildAstPreDecrementExpression();
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
    protected function parseOptionalIndexExpression(PHP_Depend_Code_ASTNode $node)
    {
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {

        case self::T_CURLY_BRACE_OPEN:
            return $this->parseStringIndexExpression($node);

        case self::T_SQUARED_BRACKET_OPEN:
            return $this->parseArrayIndexExpression($node);
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
    private function parseIndexExpression(
        PHP_Depend_Code_ASTNode $node,
        PHP_Depend_Code_ASTExpression $expr,
        $open,
        $close
    ) {
        $this->consumeToken($open);

        if (($child = $this->parseOptionalExpression()) != null) {
            $expr->addChild($child);
        }

        $token = $this->consumeToken($close);

        $expr->configureLinesAndColumns(
            $node->getStartLine(),
            $token->endLine,
            $node->getStartColumn(),
            $token->endColumn
        );

        return $this->parseOptionalIndexExpression($expr);
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
    private function parseArrayIndexExpression(PHP_Depend_Code_ASTNode $node)
    {
        $expr = $this->builder->buildAstArrayIndexExpression();
        $expr->addChild($node);

        return $this->parseIndexExpression(
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
    private function parseStringIndexExpression(PHP_Depend_Code_ASTNode $node)
    {
        $expr = $this->builder->buildAstStringIndexExpression();
        $expr->addChild($node);

        return $this->parseIndexExpression(
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
    private function isNextTokenArguments()
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
    private function setNodePositionsAndReturn(PHP_Depend_Code_ASTNode $node)
    {
        $tokens = $this->stripTrailingComments($this->tokenStack->pop());

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
     * Strips all trailing comments from the given token stream.
     *
     * @param PHP_Depend_Token[] $tokens Original token stream.
     *
     * @return PHP_Depend_Token[]
     * @since 1.0.0
     */
    private function stripTrailingComments(array $tokens)
    {
        $comments = array(self::T_COMMENT, self::T_DOC_COMMENT);

        while (count($tokens) > 1 && in_array(end($tokens)->type, $comments)) {
            array_pop($tokens);
        }
        return $tokens;
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
    private function parseInstanceOfExpression()
    {
        // Consume the "instanceof" keyword and strip comments
        $token = $this->consumeToken(self::T_INSTANCEOF);

        return $this->parseExpressionTypeReference(
            $this->builder->buildAstInstanceOfExpression($token->image), false
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
    private function parseIssetExpression()
    {
        $startToken = $this->consumeToken(self::T_ISSET);
        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        $expr = $this->builder->buildAstIssetExpression();
        $expr = $this->parseVariableList($expr);

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
     * @param PHP_Depend_Code_ASTNode $expr     The parent expression node.
     * @param boolean                 $classRef Create a class reference.
     *
     * @return PHP_Depend_Code_ASTNode
     */
    private function parseExpressionTypeReference(
        PHP_Depend_Code_ASTNode $expr, $classRef
    ) {
        // Peek next token and look for a static type identifier
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {

        case self::T_DOLLAR:
        case self::T_VARIABLE:
            // TODO: Parse variable or Member Primary Prefix + Property Postfix
            $ref = $this->parseVariableOrFunctionPostfixOrMemberPrimaryPrefix();
            break;

        case self::T_SELF:
            $ref = $this->parseSelfReference($this->consumeToken(self::T_SELF));
            break;

        case self::T_PARENT:
            $ref = $this->parseParentReference($this->consumeToken(self::T_PARENT));
            break;

        case self::T_STATIC:
            $ref = $this->parseStaticReference($this->consumeToken(self::T_STATIC));
            break;

        default:
            $ref = $this->parseClassOrInterfaceReference($classRef);
            break;
        }

        $expr->addChild(
            $this->parseOptionalMemberPrimaryPrefix(
                $this->parseOptionalStaticMemberPrimaryPrefix($ref)
            )
        );

        return $expr;
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
    private function parseConditionalExpression()
    {
        $this->tokenStack->push();
        $this->consumeToken(self::T_QUESTION_MARK);

        $expr = $this->builder->buildAstConditionalExpression();
        if (($child = $this->parseOptionalExpression()) != null) {
            $expr->addChild($child);
        }

        $this->consumeToken(self::T_COLON);

        $expr->addChild($this->parseExpression());

        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * This method parses a shift left expression node.
     *
     * @return PHP_Depend_Code_ASTShiftLeftExpression
     * @since 1.0.1
     */
    private function parseShiftLeftExpression()
    {
        $token = $this->consumeToken(self::T_SL);

        $expr = $this->builder->buildAstShiftLeftExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );
        return $expr;
    }

    /**
     * This method parses a shift right expression node.
     *
     * @return PHP_Depend_Code_ASTShiftRightExpression
     * @since 1.0.1
     */
    private function parseShiftRightExpression()
    {
        $token = $this->consumeToken(self::T_SR);

        $expr = $this->builder->buildAstShiftRightExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );
        return $expr;
    }

    /**
     * This method parses a boolean and-expression.
     *
     * @return PHP_Depend_Code_ASTBooleanAndExpression
     * @since 0.9.8
     */
    private function parseBooleanAndExpression()
    {
        $token = $this->consumeToken(self::T_BOOLEAN_AND);

        $expr = $this->builder->buildAstBooleanAndExpression();
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
    private function parseBooleanOrExpression()
    {
        $token = $this->consumeToken(self::T_BOOLEAN_OR);

        $expr = $this->builder->buildAstBooleanOrExpression();
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
    private function parseLogicalAndExpression()
    {
        $token = $this->consumeToken(self::T_LOGICAL_AND);

        $expr = $this->builder->buildAstLogicalAndExpression();
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
    private function parseLogicalOrExpression()
    {
        $token = $this->consumeToken(self::T_LOGICAL_OR);

        $expr = $this->builder->buildAstLogicalOrExpression();
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
    private function parseLogicalXorExpression()
    {
        $token = $this->consumeToken(self::T_LOGICAL_XOR);

        $expr = $this->builder->buildAstLogicalXorExpression();
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
    private function parseClassOrInterfaceReference($classReference)
    {
        $this->tokenStack->push();

        if ($classReference === true) {
            return $this->setNodePositionsAndReturn(
                $this->builder->buildAstClassReference(
                    $this->parseQualifiedName()
                )
            );
        }
        return $this->setNodePositionsAndReturn(
            $this->builder->buildAstClassOrInterfaceReference(
                $this->parseQualifiedName()
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
    private function parseBraceExpression(
        PHP_Depend_Code_ASTNode $node,
        PHP_Depend_Token $start,
        $closeToken
    ) {
        if (is_object($expr = $this->parseOptionalExpression())) {
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
    private function parseStatementBody(PHP_Depend_Code_ASTStatement $stmt)
    {
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === self::T_CURLY_BRACE_OPEN) {
            $stmt->addChild($this->parseRegularScope());
        } else if ($tokenType === self::T_COLON) {
            $stmt->addChild($this->parseAlternativeScope());
        } else {
            $stmt->addChild($this->parseStatement());
        }
        return $stmt;
    }

    /**
     * Parse a scope enclosed by curly braces.
     *
     * @return PHP_Depend_Code_ASTScope
     * @since 0.9.12
     */
    private function parseRegularScope()
    {
        $this->tokenStack->push();

        $this->consumeComments();
        $this->consumeToken(self::T_CURLY_BRACE_OPEN);

        $scope = $this->parseScopeStatements();

        $this->consumeToken(self::T_CURLY_BRACE_CLOSE);
        return $this->setNodePositionsAndReturn($scope);
    }

    /**
     * Parses the scope of a statement that is surrounded with PHP's alternative
     * syntax for statements.
     *
     * @return PHP_Depend_Code_ASTScopeStatement
     * @since 0.10.0
     */
    private function parseAlternativeScope()
    {
        $this->tokenStack->push();
        $this->consumeToken(self::T_COLON);

        $scope = $this->parseScopeStatements();

        $this->parseOptionalAlternativeScopeTermination();
        return $this->setNodePositionsAndReturn($scope);
    }

    /**
     * Parses all statements that exist in a scope an adds them to a scope
     * instance.
     *
     * @return PHP_Depend_Code_ASTScopeStatement
     * @since 0.10.0
     */
    private function parseScopeStatements()
    {
        $scope = $this->builder->buildAstScopeStatement();
        while (($child = $this->parseOptionalStatement()) != null) {
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
    private function parseOptionalAlternativeScopeTermination()
    {
        $tokenType = $this->tokenizer->peek();
        if ($this->isAlternativeScopeTermination($tokenType)) {
            $this->parseAlternativeScopeTermination($tokenType);
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
    private function isAlternativeScopeTermination($tokenType)
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
    private function parseAlternativeScopeTermination($tokenType)
    {
        $this->consumeToken($tokenType);
        $this->consumeComments();

        if ($this->tokenizer->peek() === self::T_SEMICOLON) {
            $this->consumeToken(self::T_SEMICOLON);
        } else {
            $this->parseNonePhpCode();
        }
    }

    /**
     * This method parses multiple expressions and adds them as children to the
     * given <b>$exprList</b> node.
     *
     * @param PHP_Depend_Code_ASTNode $exprList Parent that accepts multiple expr.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 1.0.0
     */
    private function parseExpressionList(PHP_Depend_Code_ASTNode $exprList)
    {
        $this->consumeComments();
        while ($expr = $this->parseOptionalExpression()) {
            $exprList->addChild($expr);

            $this->consumeComments();
            if (self::T_COMMA === $this->tokenizer->peek()) {
                $this->consumeToken(self::T_COMMA);
                $this->consumeComments();
            } else {
                break;
            }
        }

        return $exprList;
    }

    /**
     * This method parses an expression node and returns it. When no expression
     * was found this method will throw an InvalidStateException.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 1.0.1
     */
    private function parseExpression()
    {
        if (null === ($expr = $this->parseOptionalExpression())) {
            $token = $this->consumeToken($this->tokenizer->peek());

            throw new PHP_Depend_Parser_InvalidStateException(
                $token->startLine,
                $this->sourceFile->getFileName(),
                'Mandatory expression expected.'
            );
        }
        return $expr;
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
    private function parseOptionalExpression()
    {
        $expressions = array();

        while (($tokenType = $this->tokenizer->peek()) != self::T_EOF) {

            $expr = null;

            switch ($tokenType) {

            case self::T_COMMA:
            case self::T_AS:
            case self::T_BREAK:
            case self::T_CLOSE_TAG:
            case self::T_COLON:
            case self::T_CONTINUE:
            case self::T_CURLY_BRACE_CLOSE:
            case self::T_DECLARE:
            case self::T_DO:
            case self::T_DOUBLE_ARROW:
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
                $expressions[] = $this->parseVariableOrConstantOrPrimaryPrefix();
                break;

            case ($this->isArrayStartDelimiter()):
                $expressions[] = $this->doParseArray();
                break;

            case self::T_NULL:
            case self::T_TRUE:
            case self::T_FALSE:
            case self::T_LNUMBER:
            case self::T_DNUMBER:
            case self::T_BACKTICK:
            case self::T_DOUBLE_QUOTE:
            case self::T_CONSTANT_ENCAPSED_STRING:
                $expressions[] = $this->parseLiteralOrString();
                break;

            case self::T_NEW:
                $expressions[] = $this->parseAllocationExpression();
                break;

            case self::T_EVAL:
                $expressions[] = $this->parseEvalExpression();
                break;

            case self::T_CLONE:
                $expressions[] = $this->parseCloneExpression();
                break;

            case self::T_INSTANCEOF:
                $expressions[] = $this->parseInstanceOfExpression();
                break;

            case self::T_ISSET:
                $expressions[] = $this->parseIssetExpression();
                break;

            case self::T_LIST:
                $expressions[] = $this->parseListExpression();
                break;

            case self::T_QUESTION_MARK:
                $expressions[] = $this->parseConditionalExpression();
                break;

            case self::T_BOOLEAN_AND:
                $expressions[] = $this->parseBooleanAndExpression();
                break;

            case self::T_BOOLEAN_OR:
                $expressions[] = $this->parseBooleanOrExpression();
                break;

            case self::T_LOGICAL_AND:
                $expressions[] = $this->parseLogicalAndExpression();
                break;

            case self::T_LOGICAL_OR:
                $expressions[] = $this->parseLogicalOrExpression();
                break;

            case self::T_LOGICAL_XOR:
                $expressions[] = $this->parseLogicalXorExpression();
                break;

            case self::T_FUNCTION:
                $expressions[] = $this->parseClosureDeclaration();
                break;

            case self::T_PARENTHESIS_OPEN:
                $expressions[] = $this->parseParenthesisExpressionOrPrimaryPrefix();
                break;

            case self::T_EXIT:
                $expressions[] = $this->parseExitExpression();
                break;

            case self::T_START_HEREDOC:
                $expressions[] = $this->parseHeredoc();
                break;

            case self::T_CURLY_BRACE_OPEN:
                $expressions[] = $this->parseBraceExpression(
                    $this->builder->buildAstExpression(),
                    $this->consumeToken(self::T_CURLY_BRACE_OPEN),
                    self::T_CURLY_BRACE_CLOSE
                );
                break;

            case self::T_INCLUDE:
                $expressions[] = $this->parseIncludeExpression();
                break;

            case self::T_INCLUDE_ONCE:
                $expressions[] = $this->parseIncludeOnceExpression();
                break;

            case self::T_REQUIRE:
                $expressions[] = $this->parseRequireExpression();
                break;

            case self::T_REQUIRE_ONCE:
                $expressions[] = $this->parseRequireOnceExpression();
                break;

            case self::T_DEC:
                $expressions[] = $this->parseDecrementExpression($expressions);
                break;

            case self::T_INC:
                $expressions[] = $this->parseIncrementExpression($expressions);
                break;

            case self::T_SL:
                $expressions[] = $this->parseShiftLeftExpression();
                break;

            case self::T_SR:
                $expressions[] = $this->parseShiftRightExpression();
                break;

            case self::T_DIR:
            case self::T_FILE:
            case self::T_LINE:
            case self::T_NS_C:
            case self::T_FUNC_C:
            case self::T_CLASS_C:
            case self::T_METHOD_C:
                $expressions[] = $this->parseConstant();
                break;

            case self::T_INT_CAST:
            case self::T_BOOL_CAST:
            case self::T_ARRAY_CAST:
            case self::T_UNSET_CAST:
            case self::T_OBJECT_CAST:
            case self::T_DOUBLE_CAST:
            case self::T_STRING_CAST:
                $expressions[] = $this->parseCastExpression();
                break;

            case self::T_EQUAL:
            case self::T_OR_EQUAL:
            case self::T_SL_EQUAL:
            case self::T_SR_EQUAL:
            case self::T_AND_EQUAL:
            case self::T_DIV_EQUAL:
            case self::T_MOD_EQUAL:
            case self::T_MUL_EQUAL:
            case self::T_XOR_EQUAL:
            case self::T_PLUS_EQUAL:
            case self::T_MINUS_EQUAL:
            case self::T_CONCAT_EQUAL:
                $expressions[] = $this->parseAssignmentExpression(
                    array_pop($expressions)
                );
                break;

            // TODO: Handle comments here
            case self::T_COMMENT:
            case self::T_DOC_COMMENT:
                $this->consumeToken($tokenType);
                break;

            case self::T_PRINT: // TODO: Implement print expression

            case self::T_STRING_VARNAME: // TODO: Implement this

            case self::T_PLUS: // TODO: Make this a arithmetic expression
            case self::T_MINUS:
            case self::T_MUL:
            case self::T_DIV:
            case self::T_MOD:

            case self::T_IS_EQUAL: // TODO: Implement compare expressions
            case self::T_IS_NOT_EQUAL:
            case self::T_IS_IDENTICAL:
            case self::T_IS_NOT_IDENTICAL:
            case self::T_IS_GREATER_OR_EQUAL:
            case self::T_IS_SMALLER_OR_EQUAL:
            case self::T_ANGLE_BRACKET_OPEN:
            case self::T_ANGLE_BRACKET_CLOSE:

            case self::T_EMPTY:
            case self::T_CONCAT:
            case self::T_BITWISE_OR:
            case self::T_BITWISE_AND:
            case self::T_BITWISE_NOT:
            case self::T_BITWISE_XOR:
                $token = $this->consumeToken($tokenType);

                $expr = $this->builder->buildAstExpression();
                $expr->setImage($token->image);
                $expr->setStartLine($token->startLine);
                $expr->setStartColumn($token->startColumn);
                $expr->setEndLine($token->endLine);
                $expr->setEndColumn($token->endColumn);

                $expressions[] = $expr;
                break;

            case self::T_AT:
            case self::T_EXCLAMATION_MARK:
                $token = $this->consumeToken($tokenType);

                $expr = $this->builder->buildAstUnaryExpression($token->image);
                $expr->setStartLine($token->startLine);
                $expr->setStartColumn($token->startColumn);
                $expr->setEndLine($token->endLine);
                $expr->setEndColumn($token->endColumn);

                $expressions[] = $expr;
                break;

            default:
                throw new PHP_Depend_Parser_UnexpectedTokenException(
                    $this->consumeToken($tokenType),
                    $this->sourceFile->getFileName()
                );
            }
        }

        $expressions = $this->reduce($expressions);

        $count = count($expressions);
        if ($count == 0) {
            return null;
        } else if ($count == 1) {
            return $expressions[0];
        }

        $expr = $this->builder->buildAstExpression();
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
     * @param PHP_Depend_Code_ASTExpression[] $expressions Unprepared input
     *        array with parsed expression nodes found in the source tree.
     *
     * @return PHP_Depend_Code_ASTExpression[]
     * @since 0.10.0
     */
    private function reduce(array $expressions)
    {
        return $this->reduceUnaryExpression($expressions);
    }

    /**
     * Reduces all unary-expressions in the given expression list.
     *
     * @param PHP_Depend_Code_ASTExpression[] $expressions Unprepared input
     *        array with parsed expression nodes found in the source tree.
     *
     * @return PHP_Depend_Code_ASTExpression[]
     * @since 0.10.0
     */
    private function reduceUnaryExpression(array $expressions)
    {
        for ($i = count($expressions) - 2; $i >= 0; --$i) {
            $expr = $expressions[$i];
            if ($expr instanceof PHP_Depend_Code_ASTUnaryExpression) {
                $child = $expressions[$i + 1];

                $expr->addChild($child);
                $expr->setEndColumn($child->getEndColumn());
                $expr->setEndLine($child->getEndLine());

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
    private function parseSwitchStatement()
    {
        $this->tokenStack->push();
        $this->consumeToken(self::T_SWITCH);

        $switch = $this->builder->buildAstSwitchStatement();
        $switch->addChild($this->parseParenthesisExpression());
        $this->parseSwitchStatementBody($switch);

        return $this->setNodePositionsAndReturn($switch);
    }

    /**
     * Parses the body of a switch statement.
     *
     * @param PHP_Depend_Code_ASTSwitchStatement $switch The parent switch stmt.
     *
     * @return PHP_Depend_Code_ASTSwitchStatement
     * @since 0.9.8
     */
    private function parseSwitchStatementBody(
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
                $this->parseAlternativeScopeTermination(self::T_ENDSWITCH);
                return $switch;

            case self::T_CURLY_BRACE_CLOSE:
                $this->consumeToken(self::T_CURLY_BRACE_CLOSE);
                return $switch;

            case self::T_CASE:
                $switch->addChild($this->parseSwitchLabel());
                break;

            case self::T_DEFAULT:
                $switch->addChild($this->parseSwitchLabelDefault());
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
    private function parseSwitchLabel()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_CASE);

        $label = $this->builder->buildAstSwitchLabel($token->image);
        $label->addChild($this->parseExpression());

        if ($this->tokenizer->peek() === self::T_COLON) {
            $this->consumeToken(self::T_COLON);
        } else {
            $this->consumeToken(self::T_SEMICOLON);
        }

        $this->parseSwitchLabelBody($label);

        return $this->setNodePositionsAndReturn($label);
    }

    /**
     * This method parses the default label of a switch statement.
     *
     * @return PHP_Depend_Code_ASTSwitchLabel
     * @since 0.9.8
     */
    private function parseSwitchLabelDefault()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_DEFAULT);

        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_COLON) {
            $this->consumeToken(self::T_COLON);
        } else {
            $this->consumeToken(self::T_SEMICOLON);
        }

        $label = $this->builder->buildAstSwitchLabel($token->image);
        $label->setDefault();

        $this->parseSwitchLabelBody($label);

        return $this->setNodePositionsAndReturn($label);
    }

    /**
     * Parses the body of an switch label node.
     *
     * @param PHP_Depend_Code_ASTSwitchLabel $label The context switch label.
     *
     * @return PHP_Depend_Code_ASTSwitchLabel
     */
    private function parseSwitchLabelBody(PHP_Depend_Code_ASTSwitchLabel $label)
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
                $statement = $this->parseOptionalStatement();
                if ($statement === null) {
                    $this->consumeToken($tokenType);
                } else if ($statement instanceof PHP_Depend_Code_ASTNode) {
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
    private function parseStatementTermination()
    {
        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_SEMICOLON) {
            $this->consumeToken(self::T_SEMICOLON);
        } else {
            $this->parseNonePhpCode();
        }
    }

    /**
     * This method parses a try-statement + associated catch-statements.
     *
     * @return PHP_Depend_Code_ASTTryStatement
     * @since 0.9.12
     */
    private function parseTryStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_TRY);

        $stmt = $this->builder->buildAstTryStatement($token->image);
        $stmt->addChild($this->parseRegularScope());

        do {
            $stmt->addChild($this->parseCatchStatement());
            $this->consumeComments();
        } while ($this->tokenizer->peek() === self::T_CATCH);

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a throw-statement.
     *
     * @return PHP_Depend_Code_ASTThrowStatement
     * @since 0.9.12
     */
    private function parseThrowStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_THROW);

        $stmt = $this->builder->buildAstThrowStatement($token->image);
        $stmt->addChild($this->parseExpression());

        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a goto-statement.
     *
     * @return PHP_Depend_Code_ASTGotoStatement
     * @since 0.9.12
     */
    private function parseGotoStatement()
    {
        $this->tokenStack->push();

        $this->consumeToken(self::T_GOTO);
        $this->consumeComments();

        $token = $this->consumeToken(self::T_STRING);

        $this->parseStatementTermination();

        $stmt = $this->builder->buildAstGotoStatement($token->image);
        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a label-statement.
     *
     * @return PHP_Depend_Code_ASTLabelStatement
     * @since 0.9.12
     */
    private function parseLabelStatement()
    {
        $this->tokenStack->push();

        $token = $this->consumeToken(self::T_STRING);
        $this->consumeComments();
        $this->consumeToken(self::T_COLON);

        return $this->setNodePositionsAndReturn(
            $this->builder->buildAstLabelStatement($token->image)
        );
    }

    /**
     * This method parses a global-statement.
     *
     * @return PHP_Depend_Code_ASTGlobalStatement
     * @since 0.9.12
     */
    private function parseGlobalStatement()
    {
        $this->tokenStack->push();
        $this->consumeToken(self::T_GLOBAL);

        $stmt = $this->builder->buildAstGlobalStatement();
        $stmt = $this->parseVariableList($stmt);

        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a unset-statement.
     *
     * @return PHP_Depend_Code_ASTUnsetStatement
     * @since 0.9.12
     */
    private function parseUnsetStatement()
    {
        $this->tokenStack->push();

        $this->consumeToken(self::T_UNSET);
        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        $stmt = $this->builder->buildAstUnsetStatement();
        $stmt = $this->parseVariableList($stmt);

        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a catch-statement.
     *
     * @return PHP_Depend_Code_ASTCatchStatement
     * @since 0.9.8
     */
    private function parseCatchStatement()
    {
        $this->tokenStack->push();
        $this->consumeComments();

        $token = $this->consumeToken(self::T_CATCH);

        $catch = $this->builder->buildAstCatchStatement($token->image);

        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        $catch->addChild(
            $this->builder->buildAstClassOrInterfaceReference(
                $this->parseQualifiedName()
            )
        );

        $this->consumeComments();
        $catch->addChild($this->parseVariable());

        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        $catch->addChild($this->parseRegularScope());

        return $this->setNodePositionsAndReturn($catch);
    }

    /**
     * This method parses a single if-statement node.
     *
     * @return PHP_Depend_Code_ASTIfStatement
     * @since 0.9.8
     */
    private function parseIfStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_IF);

        $stmt = $this->builder->buildAstIfStatement($token->image);
        $stmt->addChild($this->parseParenthesisExpression());

        $this->parseStatementBody($stmt);
        $this->parseOptionalElseOrElseIfStatement($stmt);

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a single elseif-statement node.
     *
     * @return PHP_Depend_Code_ASTElseIfStatement
     * @since 0.9.8
     */
    private function parseElseIfStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_ELSEIF);

        $stmt = $this->builder->buildAstElseIfStatement($token->image);
        $stmt->addChild($this->parseParenthesisExpression());

        $this->parseStatementBody($stmt);
        $this->parseOptionalElseOrElseIfStatement($stmt);

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses an optional else-, else+if- or elseif-statement.
     *
     * @param PHP_Depend_Code_ASTStatement $stmt The owning if/elseif statement.
     *
     * @return PHP_Depend_Code_ASTStatement
     * @since 0.9.12
     */
    private function parseOptionalElseOrElseIfStatement(
        PHP_Depend_Code_ASTStatement $stmt
    ) {
        $this->consumeComments();
        switch ($this->tokenizer->peek()) {

        case self::T_ELSE:
            $this->consumeToken(self::T_ELSE);
            $this->consumeComments();
            if ($this->tokenizer->peek() === self::T_IF) {
                $stmt->addChild($this->parseIfStatement());
            } else {
                $this->parseStatementBody($stmt);
            }
            break;

        case self::T_ELSEIF:
            $stmt->addChild($this->parseElseIfStatement());
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
    private function parseForStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_FOR);

        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        $stmt = $this->builder->buildAstForStatement($token->image);

        if (($init = $this->parseForInit()) !== null) {
            $stmt->addChild($init);
        }
        $this->consumeToken(self::T_SEMICOLON);

        if (($expr = $this->parseForExpression()) !== null) {
            $stmt->addChild($expr);
        }
        $this->consumeToken(self::T_SEMICOLON);

        if (($update = $this->parseForUpdate()) !== null) {
            $stmt->addChild($update);
        }
        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        return $this->setNodePositionsAndReturn($this->parseStatementBody($stmt));
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
    private function parseForInit()
    {
        $this->consumeComments();
        if (self::T_SEMICOLON === $this->tokenizer->peek()) {
            return null;
        }

        $this->tokenStack->push();

        $init = $this->builder->buildAstForInit();
        $this->parseExpressionList($init);

        return $this->setNodePositionsAndReturn($init);
    }

    /**
     * Parses the expression part of a for-statement.
     *
     * @return PHP_Depend_Code_ASTExpression
     * @since 0.9.12
     */
    private function parseForExpression()
    {
        return $this->parseOptionalExpression();
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
    private function parseForUpdate()
    {
        $this->consumeComments();
        if (self::T_PARENTHESIS_CLOSE === $this->tokenizer->peek()) {
            return null;
        }

        $this->tokenStack->push();

        $update = $this->builder->buildAstForUpdate();
        $this->parseExpressionList($update);

        return $this->setNodePositionsAndReturn($update);
    }

    /**
     * This method parses a single foreach-statement node.
     *
     * @return PHP_Depend_Code_ASTForeachStatement
     * @since 0.9.8
     */
    private function parseForeachStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_FOREACH);

        $foreach = $this->builder->buildAstForeachStatement($token->image);

        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        $foreach->addChild($this->parseExpression());

        $this->consumeToken(self::T_AS);
        $this->consumeComments();

        if ($this->tokenizer->peek() === self::T_BITWISE_AND) {
            $foreach->addChild($this->parseVariableOrMemberByReference());
        } else {
            $foreach->addChild($this->parseVariableOrConstantOrPrimaryPrefix());

            if ($this->tokenizer->peek() === self::T_DOUBLE_ARROW) {
                $this->consumeToken(self::T_DOUBLE_ARROW);
                $foreach->addChild(
                    $this->parseVariableOrMemberOptionalByReference()
                );
            }
        }

        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        return $this->setNodePositionsAndReturn(
            $this->parseStatementBody($foreach)
        );
    }

    /**
     * This method parses a single while-statement node.
     *
     * @return PHP_Depend_Code_ASTWhileStatement
     * @since 0.9.8
     */
    private function parseWhileStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_WHILE);

        $stmt = $this->builder->buildAstWhileStatement($token->image);
        $stmt->addChild($this->parseParenthesisExpression());

        return $this->setNodePositionsAndReturn(
            $this->parseStatementBody($stmt)
        );
    }

    /**
     * This method parses a do/while-statement.
     *
     * @return PHP_Depend_Code_ASTDoWhileStatement
     * @sibce 0.9.12
     */
    private function parseDoWhileStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_DO);

        $stmt = $this->builder->buildAstDoWhileStatement($token->image);
        $stmt = $this->parseStatementBody($stmt);

        $this->consumeComments();
        $this->consumeToken(self::T_WHILE);

        $stmt->addChild($this->parseParenthesisExpression());

        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
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
    private function parseDeclareStatement()
    {
        $this->tokenStack->push();
        $this->consumeToken(self::T_DECLARE);

        $stmt = $this->builder->buildAstDeclareStatement();
        $stmt = $this->parseDeclareList($stmt);
        $stmt = $this->parseStatementBody($stmt);

        return $this->setNodePositionsAndReturn($stmt);
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
    private function parseDeclareList(PHP_Depend_Code_ASTDeclareStatement $stmt)
    {
        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        while (true) {
            $this->consumeComments();
            $name = $this->consumeToken(self::T_STRING)->image;

            $this->consumeComments();
            $this->consumeToken(self::T_EQUAL);

            $this->consumeComments();
            $value = $this->parseStaticValue();

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
    private function parseReturnStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_RETURN);

        $stmt = $this->builder->buildAstReturnStatement($token->image);
        if (($expr = $this->parseOptionalExpression()) != null) {
            $stmt->addChild($expr);
        }
        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a break-statement node.
     *
     * @return PHP_Depend_Code_ASTBreakStatement
     * @since 0.9.12
     */
    private function parseBreakStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_BREAK);

        $stmt = $this->builder->buildAstBreakStatement($token->image);
        if (($expr = $this->parseOptionalExpression()) != null) {
            $stmt->addChild($expr);
        }
        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a continue-statement node.
     *
     * @return PHP_Depend_Code_ASTContinueStatement
     * @since 0.9.12
     */
    private function parseContinueStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_CONTINUE);

        $stmt = $this->builder->buildAstContinueStatement($token->image);
        if (($expr = $this->parseOptionalExpression()) != null) {
            $stmt->addChild($expr);
        }
        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a echo-statement node.
     *
     * @return PHP_Depend_Code_ASTEchoStatement
     * @since 0.9.12
     */
    private function parseEchoStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(self::T_ECHO);

        $stmt = $this->parseExpressionList(
            $this->builder->buildAstEchoStatement($token->image)
        );

        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * Parses a simple parenthesis expression or a direct object access, which
     * was introduced with PHP 5.4.0:
     *
     * <code>
     * (new MyClass())->bar();
     * </code>
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 1.0.0
     */
    private function parseParenthesisExpressionOrPrimaryPrefix()
    {
        $expr = $this->parseParenthesisExpression();

        $this->consumeComments();
        if (self::T_OBJECT_OPERATOR === $this->tokenizer->peek()) {
            return $this->parseMemberPrimaryPrefix($expr->getChild(0));
        }
        return $expr;
    }

    /**
     * Parses any expression that is surrounded by an opening and a closing
     * parenthesis
     *
     * @return PHP_Depend_Code_ASTExpression
     * @since 0.9.8
     */
    private function parseParenthesisExpression()
    {
        $this->tokenStack->push();
        $this->consumeComments();

        $expr = $this->builder->buildAstExpression();
        $expr = $this->parseBraceExpression(
            $expr,
            $this->consumeToken(self::T_PARENTHESIS_OPEN),
            self::T_PARENTHESIS_CLOSE
        );

        return $this->setNodePositionsAndReturn($expr);
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
    private function parseMemberPrefixOrFunctionPostfix()
    {
        $this->tokenStack->push();
        $this->tokenStack->push();

        $qName = $this->parseQualifiedName();

        // Remove comments
        $this->consumeComments();

        // Get next token type
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {

        case self::T_DOUBLE_COLON:
            $node = $this->builder->buildAstClassOrInterfaceReference($qName);
            $node = $this->setNodePositionsAndReturn($node);
            $node = $this->parseStaticMemberPrimaryPrefix($node);
            break;

        case self::T_PARENTHESIS_OPEN:
            $node = $this->builder->buildAstIdentifier($qName);
            $node = $this->setNodePositionsAndReturn($node);
            $node = $this->parseFunctionPostfix($node);
            break;

        default:
            $node = $this->builder->buildAstConstant($qName);
            $node = $this->setNodePositionsAndReturn($node);
            break;
        }

        return $this->setNodePositionsAndReturn($node);
    }

    /**
     * This method will parse an optional function postfix.
     *
     * If the next available token is an opening parenthesis, this method will
     * wrap the given <b>$node</b> with a {@link PHP_Depend_Code_ASTFunctionPostfix}
     * node.
     *
     * @param PHP_Depend_Code_ASTNode $node The previously parsed node.
     *
     * @return PHP_Depend_Code_ASTNode The original input node or this node
     *         wrapped with a function postfix instance.
     * @since 1.0.0
     */
    private function parseOptionalFunctionPostfix(PHP_Depend_Code_ASTNode $node)
    {
        $this->consumeComments();
        if (self::T_PARENTHESIS_OPEN === $this->tokenizer->peek()) {
            return $this->parseFunctionPostfix($node);
        }
        return $node;
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
    private function parseFunctionPostfix(PHP_Depend_Code_ASTNode $node)
    {
        $image = $this->extractPostfixImage($node);

        $function = $this->builder->buildAstFunctionPostfix($image);
        $function->addChild($node);
        $function->addChild($this->parseArguments());

        return $this->parseOptionalMemberPrimaryPrefix(
            $this->parseOptionalIndexExpression($function)
        );
    }

    /**
     * This method parses a PHP version specific identifier for method and
     * property postfix expressions.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 1.0.0
     */
    protected abstract function parsePostfixIdentifier();

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
    private function parseOptionalMemberPrimaryPrefix(PHP_Depend_Code_ASTNode $node)
    {
        $this->consumeComments();

        if ($this->tokenizer->peek() === self::T_OBJECT_OPERATOR) {
            return $this->parseMemberPrimaryPrefix($node);
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
    private function parseMemberPrimaryPrefix(PHP_Depend_Code_ASTNode $node)
    {
        // Consume double colon and optional comments
        $token = $this->consumeToken(self::T_OBJECT_OPERATOR);

        $prefix = $this->builder->buildAstMemberPrimaryPrefix($token->image);
        $prefix->addChild($node);

        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {

        case self::T_STRING:
            $child = $this->parseIdentifier();
            $child = $this->parseOptionalIndexExpression($child);

            // TODO: Move this in a separate method
            if ($child instanceof PHP_Depend_Code_ASTIndexExpression) {
                $this->consumeComments();
                if (self::T_PARENTHESIS_OPEN === $this->tokenizer->peek()) {
                    $prefix->addChild($this->parsePropertyPostfix($child));
                    return $this->parseOptionalFunctionPostfix($prefix);
                }
            }
            break;

        case self::T_CURLY_BRACE_OPEN:
            $child = $this->parseCompoundExpression();
            break;

        default:
            $child = $this->parseCompoundVariableOrVariableVariableOrVariable();
            break;
        }

        $prefix->addChild(
            $this->parseMethodOrPropertyPostfix(
                $this->parseOptionalIndexExpression($child)
            )
        );

        return $this->parseOptionalMemberPrimaryPrefix(
            $this->parseOptionalIndexExpression($prefix)
        );
    }

    /**
     * This method parses an optional member primary expression. It will parse
     * the primary expression when a double colon operator can be found at the
     * actual token stream position. Otherwise this method simply returns the
     * input {@link PHP_Depend_Code_ASTNode} instance.
     *
     * @param PHP_Depend_Code_ASTNode $node This node represents primary prefix
     *        left expression. It will be the first child of the parsed member
     *        primary expression.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 1.0.1
     */
    private function parseOptionalStaticMemberPrimaryPrefix(
        PHP_Depend_Code_ASTNode $node
    ) {
        $this->consumeComments();

        if ($this->tokenizer->peek() === self::T_DOUBLE_COLON) {
            return $this->parseStaticMemberPrimaryPrefix($node);
        }
        return $node;
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
    private function parseStaticMemberPrimaryPrefix(PHP_Depend_Code_ASTNode $node)
    {
        $token = $this->consumeToken(self::T_DOUBLE_COLON);

        $prefix = $this->builder->buildAstMemberPrimaryPrefix($token->image);
        $prefix->addChild($node);

        $this->consumeComments();

        switch ($this->tokenizer->peek()) {

        case self::T_STRING:
            $postfix = $this->parseMethodOrConstantPostfix();
            break;

        default:
            $postfix = $this->parseMethodOrPropertyPostfix(
                $this->parsePostfixIdentifier()
            );
            break;
        }

        $prefix->addChild($postfix);

        return $this->parseOptionalMemberPrimaryPrefix(
            $this->parseOptionalIndexExpression($prefix)
        );
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
    private function parseMethodOrConstantPostfix()
    {
        $this->tokenStack->push();

        $node = $this->parseIdentifier();

        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_PARENTHESIS_OPEN) {
            $postfix = $this->parseMethodPostfix($node);
        } else {
            $postfix = $this->builder->buildAstConstantPostfix($node->getImage());
            $postfix->addChild($node);
        }

        return $this->setNodePositionsAndReturn($postfix);
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
    private function parseMethodOrPropertyPostfix(PHP_Depend_Code_ASTNode $node)
    {
        // Strip optional comments
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {

        case self::T_PARENTHESIS_OPEN:
            $postfix = $this->parseMethodPostfix($node);
            break;

        default:
            $postfix = $this->parsePropertyPostfix($node);
            break;
        }
        return $this->parseOptionalMemberPrimaryPrefix($postfix);
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
    private function parsePropertyPostfix(PHP_Depend_Code_ASTNode $node)
    {
        $image = $this->extractPostfixImage($node);

        $postfix = $this->builder->buildAstPropertyPostfix($image);
        $postfix->addChild($node);

        $postfix->setEndLine($node->getEndLine());
        $postfix->setEndColumn($node->getEndColumn());
        $postfix->setStartLine($node->getStartLine());
        $postfix->setStartColumn($node->getStartColumn());

        return $postfix;
    }

    /**
     * This method will extract the image/name of the real property/variable
     * that is wrapped by {@link PHP_Depend_Code_ASTIndexExpression} nodes. If
     * the given node is now wrapped by index expressions, this method will
     * return the image of the entire <b>$node</b>.
     *
     * @param PHP_Depend_Code_ASTNode $node The context node that may be wrapped
     *        by multiple array or string index expressions.
     *
     * @return string
     * @since 1.0.0
     */
    private function extractPostfixImage(PHP_Depend_Code_ASTNode $node)
    {
        while ($node instanceof PHP_Depend_Code_ASTIndexExpression) {
            $node = $node->getChild(0);
        }
        return $node->getImage();
    }

    /**
     * Parses a method postfix node instance.
     *
     * @param PHP_Depend_Code_ASTNode $node Node that represents the image of
     *        the method postfix node.
     *
     * @return PHP_Depend_Code_ASTMethodPostfix
     * @since 1.0.0
     */
    private function parseMethodPostfix(PHP_Depend_Code_ASTNode $node)
    {
        $args  = $this->parseArguments();
        $image = $this->extractPostfixImage($node);

        $postfix = $this->builder->buildAstMethodPostfix($image);
        $postfix->addChild($node);
        $postfix->addChild($args);

        $postfix->setEndLine($args->getEndLine());
        $postfix->setEndColumn($args->getEndColumn());
        $postfix->setStartLine($node->getStartLine());
        $postfix->setStartColumn($node->getStartColumn());

        return $this->parseOptionalMemberPrimaryPrefix($postfix);
    }

    /**
     * This method parses the arguments passed to a function- or method-call.
     *
     * @return PHP_Depend_Code_ASTArguments
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @since 0.9.6
     */
    private function parseArguments()
    {
        $this->consumeComments();

        $this->tokenStack->push();

        $arguments = $this->builder->buildAstArguments();

        $this->consumeToken(self::T_PARENTHESIS_OPEN);
        $this->consumeComments();

        if (self::T_PARENTHESIS_CLOSE !== $this->tokenizer->peek()) {
            $arguments = $this->parseExpressionList($arguments);
        }
        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        return $this->setNodePositionsAndReturn($arguments);
    }

    /**
     * This method implements the parsing for various expression types like
     * variables, object/static method. All these expressions are valid in
     * several php language constructs like, isset, empty, unset etc.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 0.9.12
     */
    private function parseVariableOrConstantOrPrimaryPrefix()
    {
        $this->consumeComments();
        switch ($this->tokenizer->peek()) {

        case self::T_DOLLAR:
        case self::T_VARIABLE:
            $node = $this->parseVariableOrFunctionPostfixOrMemberPrimaryPrefix();
            break;

        case self::T_SELF:
            $node = $this->parseConstantOrSelfMemberPrimaryPrefix();
            break;

        case self::T_PARENT:
            $node = $this->parseConstantOrParentMemberPrimaryPrefix();
            break;

        case self::T_STATIC:
            $node = $this->parseStaticVariableDeclarationOrMemberPrimaryPrefix();
            break;

        case self::T_STRING:
        case self::T_BACKSLASH:
        case self::T_NAMESPACE:
            $node = $this->parseMemberPrefixOrFunctionPostfix();
            break;

        default:
            throw new PHP_Depend_Parser_UnexpectedTokenException(
                $this->tokenizer->next(),
                $this->sourceFile->getFileName()
            );
        }

        return $node;
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
     * @throws PHP_Depend_Parser_Exception When an error occur during the
     *         parsing process.
     * @since 0.9.6
     */
    private function parseVariableOrFunctionPostfixOrMemberPrimaryPrefix()
    {
        $this->tokenStack->push();

        $variable = $this->parseCompoundVariableOrVariableVariableOrVariable();
        $variable = $this->parseOptionalIndexExpression($variable);

        $this->consumeComments();
        switch ($this->tokenizer->peek()) {

        case self::T_DOUBLE_COLON:
            $result = $this->parseStaticMemberPrimaryPrefix($variable);
            break;

        case self::T_OBJECT_OPERATOR:
            $result = $this->parseMemberPrimaryPrefix($variable);
            break;

        case self::T_PARENTHESIS_OPEN:
            $result = $this->parseFunctionPostfix($variable);
            break;

        default:
            $result = $variable;
            break;
        }
        return $this->setNodePositionsAndReturn($result);
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
    private function parseAssignmentExpression(PHP_Depend_Code_ASTNode $left)
    {
        $token = $this->consumeToken($this->tokenizer->peek());

        $node = $this->builder->buildAstAssignmentExpression($token->image);
        $node->addChild($left);
        $node->setStartLine($left->getStartLine());
        $node->setStartColumn($left->getStartColumn());

        // TODO: Change this into a mandatory expression in later versions
        if (($expr = $this->parseOptionalExpression()) != null) {
            $node->addChild($expr);
            $node->setEndLine($expr->getEndLine());
            $node->setEndColumn($expr->getEndColumn());
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
    private function parseStaticReference(PHP_Depend_Token $token)
    {
        // Strip optional comments
        $this->consumeComments();

        if ($this->classOrInterface === null) {
            throw new PHP_Depend_Parser_InvalidStateException(
                $token->startLine,
                (string) $this->sourceFile,
                'The keyword "static" was used outside of a class/method scope.'
            );
        }

        $ref = $this->builder->buildAstStaticReference($this->classOrInterface);
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
    private function parseSelfReference(PHP_Depend_Token $token)
    {
        if ($this->classOrInterface === null) {
            throw new PHP_Depend_Parser_InvalidStateException(
                $token->startLine,
                (string) $this->sourceFile,
                'The keyword "self" was used outside of a class/method scope.'
            );
        }

        $ref = $this->builder->buildAstSelfReference($this->classOrInterface);
        $ref->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $ref;
    }

    /**
     * Parses a simple PHP constant use and returns a corresponding node.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 1.0.0
     */
    private function parseConstant()
    {
        $this->tokenStack->push();

        switch ($type = $this->tokenizer->peek()) {

        case self::T_STRING:
            // TODO: Separate node classes for magic constants
        case self::T_DIR:
        case self::T_FILE:
        case self::T_LINE:
        case self::T_NS_C:
        case self::T_FUNC_C:
        case self::T_CLASS_C:
        case self::T_METHOD_C:
            $token = $this->consumeToken($type);
            $const = $this->builder->buildAstConstant($token->image);
            break;
        }
        return $this->setNodePositionsAndReturn($const);
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
    private function parseConstantOrSelfMemberPrimaryPrefix()
    {
        // Read self token and strip optional comments
        $token = $this->consumeToken(self::T_SELF);
        $this->consumeComments();

        if ($this->tokenizer->peek() == self::T_DOUBLE_COLON) {
            return $this->parseStaticMemberPrimaryPrefix(
                $this->parseSelfReference($token)
            );
        }
        return $this->builder->buildAstConstant($token->image);
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
    private function parseParentReference(PHP_Depend_Token $token)
    {
        if ($this->classOrInterface === null) {
            throw new PHP_Depend_Parser_InvalidStateException(
                $token->startLine,
                (string) $this->sourceFile,
                'The keyword "parent" was used as type hint but the parameter ' .
                'declaration is not in a class scope.'
            );
        }

        $classReference = $this->classOrInterface->getParentClassReference();
        if ($classReference === null) {
            throw new PHP_Depend_Parser_InvalidStateException(
                $token->startLine,
                (string) $this->sourceFile,
                sprintf(
                    'The keyword "parent" was used as type hint but the ' .
                    'class "%s" does not declare a parent.',
                    $this->classOrInterface->getName()
                )
            );
        }

        $ref = $this->builder->buildAstParentReference($classReference);
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
    private function parseConstantOrParentMemberPrimaryPrefix()
    {
        // Consume parent token and strip optional comments
        $token = $this->consumeToken(self::T_PARENT);
        $this->consumeComments();

        if ($this->tokenizer->peek() == self::T_DOUBLE_COLON) {
            return $this->parseStaticMemberPrimaryPrefix(
                $this->parseParentReference($token)
            );
        }
        return $this->builder->buildAstConstant($token->image);
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
    private function parseVariableOrMemberOptionalByReference()
    {
        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_BITWISE_AND) {
            return $this->parseVariableOrMemberByReference();
        }
        return $this->parseVariableOrConstantOrPrimaryPrefix();
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
    private function parseVariableOrMemberByReference()
    {
        $this->tokenStack->push();

        $token = $this->consumeToken(self::T_BITWISE_AND);
        $this->consumeComments();

        $expr = $this->builder->buildAstUnaryExpression($token->image);
        $expr->addChild($this->parseVariableOrConstantOrPrimaryPrefix());

        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * This method parses a simple PHP variable.
     *
     * @return PHP_Depend_Code_ASTVariable
     * @throws PHP_Depend_Parser_UnexpectedTokenException When the actual token
     *         is not a valid variable token.
     * @since 0.9.6
     */
    private function parseVariable()
    {
        $token = $this->consumeToken(self::T_VARIABLE);

        $variable = $this->builder->buildAstVariable($token->image);
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
    private function parseVariableList(PHP_Depend_Code_ASTNode $node)
    {
        $this->consumeComments();
        while ($this->tokenizer->peek() !== self::T_EOF) {
            $node->addChild($this->parseVariableOrConstantOrPrimaryPrefix());

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
    protected function parseCompoundVariableOrVariableVariableOrVariable()
    {
        if ($this->tokenizer->peek() == self::T_DOLLAR) {
            return $this->parseCompoundVariableOrVariableVariable();
        }
        return $this->parseVariable();
    }

    /**
     * Parses a PHP compound variable or a simple literal node.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 0.9.19
     */
    private function parseCompoundVariableOrLiteral()
    {
        $this->tokenStack->push();

        // Read the dollar token
        $token = $this->consumeToken(self::T_DOLLAR);
        $this->consumeComments();

        // Get next token type
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {

        case self::T_CURLY_BRACE_OPEN:
            $variable = $this->builder->buildAstCompoundVariable($token->image);
            $variable->addChild($this->parseCompoundExpression());
            break;

        default:
            $variable = $this->builder->buildAstLiteral($token->image);
            break;
        }

        return $this->setNodePositionsAndReturn($variable);
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
    private function parseCompoundVariableOrVariableVariable()
    {
        $this->tokenStack->push();

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
            $variable = $this->builder->buildAstVariableVariable($token->image);
            $variable->addChild(
                $this->parseCompoundVariableOrVariableVariableOrVariable()
            );
            break;

        default:
            $variable = $this->parseCompoundVariable($token);
            break;
        }

        return $this->setNodePositionsAndReturn($variable);
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
    private function parseCompoundVariable(PHP_Depend_Token $token)
    {
        return $this->parseBraceExpression(
            $this->builder->buildAstCompoundVariable($token->image),
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
    private function parseCompoundExpressionOrLiteral()
    {
        $token = $this->consumeToken(self::T_CURLY_BRACE_OPEN);
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {

        case self::T_DOLLAR:
        case self::T_VARIABLE:
            return $this->parseBraceExpression(
                $this->builder->buildAstCompoundExpression(),
                $token,
                self::T_CURLY_BRACE_CLOSE
            );
        }

        $literal = $this->builder->buildAstLiteral($token->image);
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
    protected function parseCompoundExpression()
    {
        $this->consumeComments();

        return $this->parseBraceExpression(
            $this->builder->buildAstCompoundExpression(),
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
    protected function parseIdentifier()
    {
        $token = $this->consumeToken(self::T_STRING);

        $node = $this->builder->buildAstIdentifier($token->image);
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
    private function parseLiteralOrString()
    {
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {

        case self::T_NULL:
        case self::T_TRUE:
        case self::T_FALSE:
        case self::T_DNUMBER:
        case self::T_CONSTANT_ENCAPSED_STRING:
            $token = $this->consumeToken($tokenType);

            $literal = $this->builder->buildAstLiteral($token->image);
            $literal->configureLinesAndColumns(
                $token->startLine,
                $token->endLine,
                $token->startColumn,
                $token->endColumn
            );
            return $literal;

        case self::T_LNUMBER:
            return $this->parseIntegerNumber();

        default:
            return $this->parseString($tokenType);
        }
    }

    /**
     * Parses an integer value.
     *
     * @return PHP_Depend_Code_ASTLiteral
     * @since 1.0.0
     */
    protected abstract function parseIntegerNumber();

    /**
     * Parses an array structure.
     *
     * @return PHP_Depend_Code_ASTArray
     * @since 1.0.0
     */
    private function doParseArray($static = false)
    {
        $this->tokenStack->push();

        return $this->setNodePositionsAndReturn(
            $this->parseArray(
                $this->builder->buildAstArray(),
                $static
            )
        );
    }

    /**
     * Tests if the next token is a valid array start delimiter in the supported
     * PHP version.
     *
     * @return boolean
     * @since 1.0.0
     */
    protected abstract function isArrayStartDelimiter();

    /**
     * Parses a php array declaration.
     *
     * @param PHP_Depend_Code_ASTArray $array
     * @param boolean $static
     *
     * @return PHP_Depend_Code_ASTArray
     * @since 1.0.0
     */
    protected abstract function parseArray(PHP_Depend_Code_ASTArray $array, $static = false);

    /**
     * Parses all elements in an array.
     *
     * @param PHP_Depend_Code_ASTArray $array
     * @param integer $endDelimiter
     * @param boolean $static
     *
     * @return PHP_Depend_Code_ASTArray
     * @since 1.0.0
     */
    protected function parseArrayElements(
        PHP_Depend_Code_ASTArray $array,
        $endDelimiter,
        $static = false
    ) {
        $this->consumeComments();
        while ($endDelimiter !== $this->tokenizer->peek()) {
            $array->addChild($this->parseArrayElement($static));

            $this->consumeComments();
            if (self::T_COMMA === $this->tokenizer->peek()) {
                $this->consumeToken(self::T_COMMA);
                $this->consumeComments();
            }
        }
        return $array;
    }

    /**
     * Parses a single array element.
     *
     * An array element can have a simple value, a key/value pair, a value by
     * reference or a key/value pair with a referenced value.
     *
     * @param boolean $static
     * @return PHP_Depend_Code_ASTArrayElement
     * @since 1.0.0
     */
    protected function parseArrayElement($static = false)
    {
        $this->consumeComments();

        $this->tokenStack->push();

        $element = $this->builder->buildAstArrayElement();
        if ($this->parseOptionalByReference()) {

            if ($static) {
                $tokens = $this->tokenStack->pop();

                throw new PHP_Depend_Parser_UnexpectedTokenException(
                    end($tokens),
                    $this->sourceFile->getFileName()
                );
            }

            $element->setByReference();
        }

        $this->consumeComments();
        if ($this->isKeyword($this->tokenizer->peek())) {
            throw new PHP_Depend_Parser_UnexpectedTokenException(
                $this->tokenizer->next(),
                $this->sourceFile->getFileName()
            );
        }

        $element->addChild($this->parseExpression());

        $this->consumeComments();
        if (self::T_DOUBLE_ARROW === $this->tokenizer->peek()) {
            $this->consumeToken(self::T_DOUBLE_ARROW);
            $this->consumeComments();

            if ($this->parseOptionalByReference()) {
                $element->setByReference();
            }
            $element->addChild($this->parseExpression());
        }

        return $this->setNodePositionsAndReturn($element);
    }

    /**
     * Parses a here- or nowdoc string instance.
     *
     * @return PHP_Depend_Code_ASTHeredoc
     * @since 0.9.12
     */
    protected function parseHeredoc()
    {
        $this->tokenStack->push();
        $this->consumeToken(self::T_START_HEREDOC);

        $heredoc = $this->builder->buildAstHeredoc();
        $this->parseStringExpressions($heredoc, self::T_END_HEREDOC);

        $token = $this->consumeToken(self::T_END_HEREDOC);
        $heredoc->setDelimiter($token->image);

        return $this->setNodePositionsAndReturn($heredoc);
    }

    /**
     * Parses a simple string sequence between two tokens of the same type.
     *
     * @param integer $tokenType The start/stop token type.
     *
     * @return string
     * @since 0.9.10
     */
    private function parseStringSequence($tokenType)
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
    private function parseString($delimiterType)
    {
        $token = $this->consumeToken($delimiterType);

        $string = $this->builder->buildAstString();
        $string->setStartLine($token->startLine);
        $string->setStartColumn($token->startColumn);

        $this->parseStringExpressions($string, $delimiterType);

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
    private function parseStringExpressions(
        PHP_Depend_Code_ASTNode $node,
        $stopToken
    ) {
        while (($tokenType = $this->tokenizer->peek()) != self::T_EOF) {

            switch ($tokenType) {

            case $stopToken:
                break 2;

            case self::T_BACKSLASH:
                $node->addChild($this->parseEscapedAstLiteralString());
                break;

            case self::T_DOLLAR:
                $node->addChild($this->parseCompoundVariableOrLiteral());
                break;

            case self::T_VARIABLE:
                $node->addChild($this->parseVariable());
                break;

            case self::T_CURLY_BRACE_OPEN:
                $node->addChild($this->parseCompoundExpressionOrLiteral());
                break;

            default:
                $node->addChild($this->parseLiteral());
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
    private function parseEscapedAstLiteralString()
    {
        $this->tokenStack->push();

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
        return $this->setNodePositionsAndReturn(
            $this->builder->buildAstLiteral($image)
        );
    }

    /**
     * This method parses a simple literal and configures the position
     * properties.
     *
     * @return PHP_Depend_Code_ASTLiteral
     * @since 0.9.10
     */
    private function parseLiteral()
    {
        $token = $this->consumeToken($this->tokenizer->peek());

        $node = $this->builder->buildAstLiteral($token->image);
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
    private function parseFormalParameters()
    {
        $this->consumeComments();

        $this->tokenStack->push();

        $formalParameters = $this->builder->buildAstFormalParameters();

        $this->consumeToken(self::T_PARENTHESIS_OPEN);
        $this->consumeComments();

        $tokenType = $this->tokenizer->peek();

        // Check for function without parameters
        if ($tokenType === self::T_PARENTHESIS_CLOSE) {
            $this->consumeToken(self::T_PARENTHESIS_CLOSE);
            return $this->setNodePositionsAndReturn($formalParameters);
        }

        while ($tokenType !== self::T_EOF) {

            $formalParameters->addChild(
                $this->parseFormalParameterOrTypeHintOrByReference()
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

        return $this->setNodePositionsAndReturn($formalParameters);
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
    private function parseFormalParameterOrTypeHintOrByReference()
    {
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        $this->tokenStack->push();

        switch ($tokenType) {

        case self::T_ARRAY:
            $parameter = $this->parseFormalParameterAndArrayTypeHint();
            break;

        case ($this->isFormalParameterTypeHint($tokenType)):
            $parameter = $this->parseFormalParameterAndTypeHint();
            break;

        case self::T_SELF:
            $parameter = $this->parseFormalParameterAndSelfTypeHint();
            break;

        case self::T_PARENT:
            $parameter = $this->parseFormalParameterAndParentTypeHint();
            break;

        case self::T_BITWISE_AND:
            $parameter = $this->parseFormalParameterAndByReference();
            break;

        default:
            $parameter = $this->parseFormalParameter();
            break;
        }
        return $this->setNodePositionsAndReturn($parameter);
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
    private function parseFormalParameterAndArrayTypeHint()
    {
        $token = $this->consumeToken(self::T_ARRAY);

        $node = $this->builder->buildAstTypeArray();
        $node->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        $parameter = $this->parseFormalParameterOrByReference();
        $parameter->prependChild($node);

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
    private function parseFormalParameterAndTypeHint()
    {
        $this->tokenStack->push();

        $classReference = $this->setNodePositionsAndReturn(
            $this->parseFormalParameterTypeHint()
        );

        $parameter = $this->parseFormalParameterOrByReference();
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
    private function parseFormalParameterAndParentTypeHint()
    {
        $token = $this->consumeToken(self::T_PARENT);

        $reference = $this->parseParentReference($token);
        $parameter = $this->parseFormalParameterOrByReference();
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
    private function parseFormalParameterAndSelfTypeHint()
    {
        $token = $this->consumeToken(self::T_SELF);

        $self = $this->builder->buildAstSelfReference($this->classOrInterface);
        $self->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        $parameter = $this->parseFormalParameterOrByReference();
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
    protected function parseFormalParameterOrByReference()
    {
        $this->consumeComments();
        if ($this->tokenizer->peek() === self::T_BITWISE_AND) {
            return $this->parseFormalParameterAndByReference();
        }
        return $this->parseFormalParameter();
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
    private function parseFormalParameterAndByReference()
    {
        $this->consumeToken(self::T_BITWISE_AND);
        $this->consumeComments();

        $parameter = $this->parseFormalParameter();
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
    private function parseFormalParameter()
    {
        $parameter = $this->builder->buildAstFormalParameter();
        $parameter->addChild($this->parseVariableDeclarator());

        return $parameter;
    }

    /**
     * Tests if the given token type is a valid formal parameter in the supported
     * PHP version.
     *
     * @param integer $tokenType Numerical token identifier.
     *
     * @return boolean
     * @since 1.0.0
     */
    protected abstract function isFormalParameterTypeHint($tokenType);

    /**
     * Parses a formal parameter type hint that is valid in the supported PHP
     * version.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 1.0.0
     */
    protected abstract function parseFormalParameterTypeHint();

    /**
     * Extracts all dependencies from a callable body.
     *
     * @return PHP_Depend_Code_ASTScope
     * @since 0.9.12
     */
    private function parseScope()
    {
        $scope = $this->builder->buildAstScope();

        $this->tokenStack->push();

        $this->consumeComments();
        $this->consumeToken(self::T_CURLY_BRACE_OPEN);

        while (($stmt = $this->parseOptionalStatement()) !== null) {
            // TODO: Remove if-statement once, we have translated functions and
            //       closures into ast-nodes
            if ($stmt instanceof PHP_Depend_Code_ASTNode) {
                $scope->addChild($stmt);
            }
        }

        $this->consumeComments();
        $this->consumeToken(self::T_CURLY_BRACE_CLOSE);

        return $this->setNodePositionsAndReturn($scope);
    }

    /**
     * Parse a statement.
     *
     * @return PHP_Depend_Code_ASTNode
     * @throws PHP_Depend_Parser_UnexpectedTokenException
     * @since 1.0.0
     */
    private function parseStatement()
    {
        if (null === ($stmt = $this->parseOptionalStatement())) {
            throw new PHP_Depend_Parser_UnexpectedTokenException(
                $this->tokenizer->next(),
                $this->sourceFile->getFileName()
            );
        }
        return $stmt;
    }

    /**
     * Parses an optional statement or returns <b>null</b>.
     *
     * @return PHP_Depend_Code_ASTNode
     * @since 0.9.8
     */
    private function parseOptionalStatement()
    {
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {

        case self::T_ECHO:
            return $this->parseEchoStatement();

        case self::T_SWITCH:
            return $this->parseSwitchStatement();

        case self::T_TRY:
            return $this->parseTryStatement();

        case self::T_THROW:
            return $this->parseThrowStatement();

        case self::T_IF:
            return $this->parseIfStatement();

        case self::T_FOR:
            return $this->parseForStatement();

        case self::T_FOREACH:
            return $this->parseForeachStatement();

        case self::T_DO:
            return $this->parseDoWhileStatement();

        case self::T_WHILE:
            return $this->parseWhileStatement();

        case self::T_RETURN:
            return $this->parseReturnStatement();

        case self::T_BREAK:
            return $this->parseBreakStatement();

        case self::T_CONTINUE:
            return $this->parseContinueStatement();

        case self::T_GOTO:
            return $this->parseGotoStatement();

        case self::T_GLOBAL:
            return $this->parseGlobalStatement();

        case self::T_UNSET:
            return $this->parseUnsetStatement();

        case self::T_STRING:
            if ($this->tokenizer->peekNext() === self::T_COLON) {
                return $this->parseLabelStatement();
            }
            break;

        case self::T_CONST:
            return $this->parseConstantDefinition();

        case self::T_FUNCTION:
            return $this->parseFunctionOrClosureDeclaration();

        case self::T_COMMENT:
            return $this->parseCommentWithOptionalInlineClassOrInterfaceReference();

        case self::T_DOC_COMMENT:
            return $this->builder->buildAstComment(
                $this->consumeToken(self::T_DOC_COMMENT)->image
            );

        case self::T_CURLY_BRACE_OPEN:
            return $this->parseRegularScope();

        case self::T_DECLARE:
            return $this->parseDeclareStatement();

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

        case self::T_DECLARE:
            return $this->parseDeclareStatement();

        case self::T_CLOSE_TAG:
            if (($tokenType = $this->parseNonePhpCode()) === self::T_EOF) {
                return null;
            }
            return $this->parseOptionalStatement();

        case self::T_TRAIT:
            $package = $this->getNamespaceOrPackage();
            $package->addType($trait = $this->parseTraitDeclaration());

            $this->builder->restoreTrait($trait);
            $this->sourceFile->addChild($trait);
            return $trait;

        case self::T_INTERFACE:
            $package = $this->getNamespaceOrPackage();
            $package->addType($interface = $this->parseInterfaceDeclaration());

            $this->builder->restoreInterface($interface);
            $this->sourceFile->addChild($interface);
            return $interface;

        case self::T_CLASS:
        case self::T_FINAL:
        case self::T_ABSTRACT:
            $package = $this->getNamespaceOrPackage();
            $package->addType($class = $this->parseClassDeclaration());

            $this->builder->restoreClass($class);
            $this->sourceFile->addChild($class);
            return $class;
        }

        $this->tokenStack->push();
        $stmt = $this->builder->buildAstStatement();
        if (($expr = $this->parseOptionalExpression()) != null) {
            $stmt->addChild($expr);
        }
        $this->parseStatementTermination();
        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * Parses a sequence of none php code tokens and returns the token type of
     * the next token.
     *
     * @return integer
     * @since 0.9.12
     */
    private function parseNonePhpCode()
    {
        $this->consumeToken(self::T_CLOSE_TAG);

        $this->tokenStack->push();
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
        $this->tokenStack->pop();

        return $tokenType;
    }

    /**
     * Parses a comment and optionally an embedded class or interface type
     * annotation.
     *
     * @return PHP_Depend_Code_ASTComment
     * @since 0.9.8
     */
    private function parseCommentWithOptionalInlineClassOrInterfaceReference()
    {
        $token = $this->consumeToken(self::T_COMMENT);

        $comment = $this->builder->buildAstComment($token->image);
        if (preg_match(self::REGEXP_INLINE_TYPE, $token->image, $match)) {
            $comment->addChild(
                $this->builder->buildAstClassOrInterfaceReference($match[1])
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
     * Parses an optional set of bound closure variables.
     *
     * @param PHP_Depend_Code_ASTClosure $closure The context closure instance.
     *
     * @return PHP_Depend_Code_ASTClosure
     * @since 1.0.0
     */
    private function parseOptionalBoundVariables(
        PHP_Depend_Code_ASTClosure $closure
    ) {
        $this->consumeComments();
        if (self::T_USE === $this->tokenizer->peek()) {
            return $this->parseBoundVariables($closure);
        }
        return $closure;
    }

    /**
     * Parses a list of bound closure variables.
     *
     * @param PHP_Depend_Code_ASTClosure $closure The parent closure instance.
     *
     * @return PHP_Depend_Code_ASTClosure
     * @since 0.9.5
     */
    private function parseBoundVariables(PHP_Depend_Code_ASTClosure $closure)
    {
        $this->consumeToken(self::T_USE);

        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_OPEN);

        while ($this->tokenizer->peek() !== self::T_EOF) {
            $this->consumeComments();

            if ($this->tokenizer->peek() === self::T_BITWISE_AND) {
                $this->consumeToken(self::T_BITWISE_AND);
                $this->consumeComments();
            }

            $this->consumeToken(self::T_VARIABLE);
            $this->consumeComments();

            if ($this->tokenizer->peek() === self::T_COMMA) {
                $this->consumeToken(self::T_COMMA);
                continue;
            }
            break;
        }

        $this->consumeComments();
        $this->consumeToken(self::T_PARENTHESIS_CLOSE);

        return $closure;
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
    protected function parseQualifiedName()
    {
        $fragments = $this->parseQualifiedNameRaw();

        // Check for fully qualified name
        if ($fragments[0] === '\\') {
            return join('', $fragments);
        }

        // Search for an use alias
        $mapsTo = $this->useSymbolTable->lookup($fragments[0]);
        if ($mapsTo !== null) {
            // Remove alias and add real namespace
            array_shift($fragments);
            array_unshift($fragments, $mapsTo);
        } else if ($this->namespaceName !== null
            && $this->namespacePrefixReplaced === false
        ) {
            // Prepend current namespace
            array_unshift($fragments, $this->namespaceName, '\\');
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
    private function parseQualifiedNameRaw()
    {
        // Reset namespace prefix flag
        $this->namespacePrefixReplaced = false;

        // Consume comments and fetch first token type
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        $qualifiedName = array();

        if ($tokenType === self::T_NAMESPACE) {
            // Consume namespace keyword
            $this->consumeToken(self::T_NAMESPACE);
            $this->consumeComments();

            // Add current namespace as first token
            $qualifiedName = array((string) $this->namespaceName);

            // Set prefixed flag to true
            $this->namespacePrefixReplaced = true;
        } else if ($this->isClassName($tokenType)) {
            $qualifiedName[] = $this->parseClassName();

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            // Stop here for simple identifier
            if ($tokenType !== self::T_BACKSLASH) {
                return $qualifiedName;
            }
        }

        do {
            // Next token must be a namespace separator
            $this->consumeToken(self::T_BACKSLASH);
            $this->consumeComments();

            // Append to qualified name
            $qualifiedName[] = '\\';
            $qualifiedName[] = $this->parseClassName();

            $this->consumeComments();

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
    private function parseNamespaceDeclaration()
    {
        // Consume namespace keyword and strip optional comments
        $this->consumeToken(self::T_NAMESPACE);
        $this->consumeComments();

        $tokenType = $this->tokenizer->peek();

        // Search for a namespace identifier
        if ($tokenType === self::T_STRING) {
            // Reset namespace property
            $this->namespaceName = null;

            $qualifiedName = $this->parseQualifiedName();

            $this->consumeComments();
            if ($this->tokenizer->peek() === self::T_CURLY_BRACE_OPEN) {
                $this->consumeToken(self::T_CURLY_BRACE_OPEN);
            } else {
                $this->consumeToken(self::T_SEMICOLON);
            }

            // Create a package for this namespace
            $this->namespaceName = $qualifiedName;

            $this->useSymbolTable->resetScope();

        } else if ($tokenType === self::T_BACKSLASH) {
            // Same namespace reference, something like:
            //   new namespace\Foo();
            // or:
            //   $x = namespace\foo::bar();

            // Now parse a qualified name
            $this->parseQualifiedNameRaw();
        } else {
            // Consume opening curly brace
            $this->consumeToken(self::T_CURLY_BRACE_OPEN);

            // Create a package for this namespace
            $this->namespaceName = '';

            $this->useSymbolTable->resetScope();
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
    private function parseUseDeclarations()
    {
        // Consume use keyword
        $this->consumeToken(self::T_USE);
        $this->consumeComments();

        // Parse all use declarations
        $this->parseUseDeclaration();
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
    private function parseUseDeclaration()
    {
        $fragments = $this->parseQualifiedNameRaw();
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
        $this->useSymbolTable->add($image, join('', $fragments));

        // Check for a following use declaration
        if ($this->tokenizer->peek() === self::T_COMMA) {
            // Consume comma token and comments
            $this->consumeToken(self::T_COMMA);
            $this->consumeComments();

            $this->parseUseDeclaration();
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
    private function parseConstantDefinition()
    {
        $this->tokenStack->push();

        $token = $this->consumeToken(self::T_CONST);

        $definition = $this->builder->buildAstConstantDefinition($token->image);
        $definition->setComment($this->docComment);

        do {
            $definition->addChild($this->parseConstantDeclarator());

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            if ($tokenType === self::T_SEMICOLON) {
                break;
            }
            $this->consumeToken(self::T_COMMA);
        } while ($tokenType !== self::T_EOF);


        $definition = $this->setNodePositionsAndReturn($definition);

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
    private function parseConstantDeclarator()
    {
        // Remove leading comments and create a new token stack
        $this->consumeComments();
        $this->tokenStack->push();

        $token = $this->consumeToken(self::T_STRING);

        $this->consumeComments();
        $this->consumeToken(self::T_EQUAL);

        $declarator = $this->builder->buildAstConstantDeclarator($token->image);
        $declarator->setValue($this->parseStaticValue());

        return $this->setNodePositionsAndReturn($declarator);
    }

    /**
     * This method parses a static variable declaration list, a member primary
     * prefix invoked in the static context of a class or it parses a static
     * closure declaration.
     *
     * Static variable:
     * <code>
     * function foo() {
     * //  ------------------------------
     *     static $foo, $bar, $baz = null;
     * //  ------------------------------
     * }
     * </code>
     *
     * Static method invocation:
     * <code>
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
     * Static closure declaration:
     * <code>
     * $closure = static function($x, $y) {
     *     return ($x * $y);
     * };
     * </code>
     *
     * @return PHP_Depend_Code_ASTConstant
     * @throws PHP_Depend_Parser_Exception When an error occured during the
     *         parsing process.
     * @throws PHP_Depend_Parser_UnexpectedTokenException When the actual token
     *         is not a valid variable token.
     * @since 0.9.6
     */
    private function parseStaticVariableDeclarationOrMemberPrimaryPrefix()
    {
        $this->tokenStack->push();

        // Consume static token and strip optional comments
        $token = $this->consumeToken(self::T_STATIC);
        $this->consumeComments();

        // Fetch next token type
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === self::T_PARENTHESIS_OPEN
            || $tokenType === self::T_DOUBLE_COLON
        ) {
            $static = $this->parseStaticReference($token);

            $prefix = $this->parseStaticMemberPrimaryPrefix($static);
            return $this->setNodePositionsAndReturn($prefix);
        } else if ($tokenType === self::T_FUNCTION) {
            $closure = $this->parseClosureDeclaration();
            $closure->setStatic(true);

            return $this->setNodePositionsAndReturn($closure);
        }

        $declaration = $this->parseStaticVariableDeclaration($token);
        return $this->setNodePositionsAndReturn($declaration);

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
    private function parseStaticVariableDeclaration(PHP_Depend_Token $token)
    {
        $staticDeclaration = $this->builder->buildAstStaticVariableDeclaration(
            $token->image
        );

        // Strip optional comments
        $this->consumeComments();

        // Fetch next token type
        $tokenType = $this->tokenizer->peek();

        while ($tokenType !== self::T_EOF) {
            $staticDeclaration->addChild($this->parseVariableDeclarator());

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
    private function parseVariableDeclarator()
    {
        $this->tokenStack->push();

        $name = $this->consumeToken(self::T_VARIABLE)->image;
        $this->consumeComments();

        $declarator = $this->builder->buildAstVariableDeclarator($name);

        if ($this->tokenizer->peek() === self::T_EQUAL) {
            $this->consumeToken(self::T_EQUAL);
            $declarator->setValue($this->parseStaticValueOrStaticArray());
        }
        return $this->setNodePositionsAndReturn($declarator);
    }

    /**
     * This method will parse a static value or a static array as it is
     * used as default value for a parameter or property declaration.
     *
     * @return PHP_Depend_Code_Value
     * @since 0.9.6
     */
    private function parseStaticValueOrStaticArray()
    {
        $this->consumeComments();
        if ($this->isArrayStartDelimiter()) {
            // TODO: Use default value as value!
            $defaultValue = $this->doParseArray(true);

            $value = new PHP_Depend_Code_Value();
            $value->setValue(array());

            return $value;
        }
        return $this->parseStaticValue();
    }

    /**
     * This method will parse a static default value as it is used for a
     * parameter, property or constant declaration.
     *
     * @return PHP_Depend_Code_Value
     * @since 0.9.5
     */
    private function parseStaticValue()
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
                $defaultValue->setValue($this->parseStringSequence($tokenType));
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
            case self::T_SQUARED_BRACKET_OPEN:
            case self::T_SQUARED_BRACKET_CLOSE:
                // There is a default value but we don't handle it at the moment.
                $defaultValue->setValue(null);
                $this->consumeToken($tokenType);
                break;

            case self::T_START_HEREDOC:
                $defaultValue->setValue(
                    $this->parseHeredoc()->getChild(0)->getImage()
                );
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
     * Checks if the given expression is a read/write variable as defined in
     * the PHP zend_language_parser.y definition.
     *
     * @param PHP_Depend_Code_ASTNode $expr The context node instance.
     *
     * @return boolean
     * @since 0.10.0
     */
    private function isReadWriteVariable($expr)
    {
        return ($expr instanceof PHP_Depend_Code_ASTVariable
            || $expr instanceof PHP_Depend_Code_ASTFunctionPostfix
            || $expr instanceof PHP_Depend_Code_ASTVariableVariable
            || $expr instanceof PHP_Depend_Code_ASTCompoundVariable
            || $expr instanceof PHP_Depend_Code_ASTMemberPrimaryPrefix);
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
    private function createQualifiedTypeName($localName)
    {
        return ltrim($this->getNamespaceOrPackageName() . '\\' . $localName, '\\');
    }

    /**
     * Returns the name of a declared names. When the parsed code is not namespaced
     * this method will return the name from the @package annotation.
     *
     * @return string
     * @since 0.9.8
     */
    private function getNamespaceOrPackageName()
    {
        if ($this->namespaceName === null) {
            return $this->packageName;
        }
        return $this->namespaceName;
    }

    /**
     * Returns the currently active package or namespace.
     *
     * @return PHP_Depend_Code_Package
     * @since 1.0.0
     */
    private function getNamespaceOrPackage()
    {
        return $this->builder->buildPackage($this->getNamespaceOrPackageName());
    }

    /**
     * Extracts the @package information from the given comment.
     *
     * @param string $comment A doc comment block.
     *
     * @return string
     */
    private function parsePackageAnnotation($comment)
    {
        $package = self::DEFAULT_PACKAGE;
        if (preg_match('#\*\s*@package\s+(\S+)#', $comment, $match)) {
            $package = trim($match[1]);
            if (preg_match('#\*\s*@subpackage\s+(\S+)#', $comment, $match)) {
                $package .= self::PACKAGE_SEPARATOR . trim($match[1]);
            }
        }

        // Check for doc level comment
        if ($this->globalPackageName === self::DEFAULT_PACKAGE
            && $this->isFileComment() === true
        ) {
            $this->globalPackageName = $package;

            $this->sourceFile->setDocComment($comment);
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
            self::T_TRAIT,
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
    private function parseThrowsAnnotations($comment)
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
    private function parseReturnAnnotation($comment)
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
    private function parseVarAnnotation($comment)
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
     * @return PHP_Depend_Code_ASTType
     * @since 0.9.6
     */
    private function parseFieldDeclarationType()
    {
        // Skip, if ignore annotations is set
        if ($this->ignoreAnnotations === true) {
            return null;
        }

        $reference = $this->parseFieldDeclarationClassOrInterfaceReference();
        if ($reference !== null) {
            return $reference;
        }

        $annotations = $this->parseVarAnnotation($this->docComment);
        foreach ($annotations as $annotation) {
            if (PHP_Depend_Util_Type::isPrimitiveType($annotation) === true) {
                return $this->builder->buildAstPrimitiveType(
                    PHP_Depend_Util_Type::getPrimitiveType($annotation)
                );
            } else if (PHP_Depend_Util_Type::isArrayType($annotation) === true) {
                return $this->builder->buildAstTypeArray();
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
    private function parseFieldDeclarationClassOrInterfaceReference()
    {
        $annotations = $this->parseVarAnnotation($this->docComment);
        foreach ($annotations as $annotation) {
            if (PHP_Depend_Util_Type::isScalarType($annotation) === false) {
                return $this->builder->buildAstClassOrInterfaceReference(
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
    private function prepareCallable(PHP_Depend_Code_AbstractCallable $callable)
    {
        // Skip, if ignore annotations is set
        if ($this->ignoreAnnotations === true) {
            return;
        }

        // Get all @throws Types
        $throws = $this->parseThrowsAnnotations($callable->getDocComment());
        foreach ($throws as $qualifiedName) {
            $callable->addExceptionClassReference(
                $this->builder->buildAstClassOrInterfaceReference($qualifiedName)
            );
        }

        // Get return annotation
        $qualifiedName = $this->parseReturnAnnotation($callable->getDocComment());
        if ($qualifiedName !== null) {
            $callable->setReturnClassReference(
                $this->builder->buildAstClassOrInterfaceReference($qualifiedName)
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
            return $this->tokenStack->add($token);
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

            $token = $this->consumeToken($type);
            $type  = $this->tokenizer->peek();

            if (self::T_COMMENT === $token->type) {
                continue;
            }

            $this->docComment = $token->image;
            if (preg_match('(\s+@package\s+[^\s]+\s+)', $token->image)) {
                $this->packageName = $this->parsePackageAnnotation($token->image);
            }
        }
    }
}
