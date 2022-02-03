<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\AST\AbstractASTCallable;
use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\AbstractASTNode;
use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTArrayElement;
use PDepend\Source\AST\ASTAssignmentExpression;
use PDepend\Source\AST\ASTBooleanAndExpression;
use PDepend\Source\AST\ASTBooleanOrExpression;
use PDepend\Source\AST\ASTBreakStatement;
use PDepend\Source\AST\ASTCallable;
use PDepend\Source\AST\ASTCastExpression;
use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTClassFqnPostfix;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTCloneExpression;
use PDepend\Source\AST\ASTClosure;
use PDepend\Source\AST\ASTComment;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTCompoundExpression;
use PDepend\Source\AST\ASTCompoundVariable;
use PDepend\Source\AST\ASTConditionalExpression;
use PDepend\Source\AST\ASTConstant;
use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\AST\ASTConstantDefinition;
use PDepend\Source\AST\ASTContinueStatement;
use PDepend\Source\AST\ASTDeclareStatement;
use PDepend\Source\AST\ASTDoWhileStatement;
use PDepend\Source\AST\ASTEchoStatement;
use PDepend\Source\AST\ASTElseIfStatement;
use PDepend\Source\AST\ASTEvalExpression;
use PDepend\Source\AST\ASTExitExpression;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTFinallyStatement;
use PDepend\Source\AST\ASTForeachStatement;
use PDepend\Source\AST\ASTForInit;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTForStatement;
use PDepend\Source\AST\ASTForUpdate;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTFunctionPostfix;
use PDepend\Source\AST\ASTGlobalStatement;
use PDepend\Source\AST\ASTGotoStatement;
use PDepend\Source\AST\ASTHeredoc;
use PDepend\Source\AST\ASTIdentifier;
use PDepend\Source\AST\ASTIfStatement;
use PDepend\Source\AST\ASTIncludeExpression;
use PDepend\Source\AST\ASTIndexExpression;
use PDepend\Source\AST\ASTInstanceOfExpression;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTIssetExpression;
use PDepend\Source\AST\ASTLabelStatement;
use PDepend\Source\AST\ASTListExpression;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTLogicalAndExpression;
use PDepend\Source\AST\ASTLogicalOrExpression;
use PDepend\Source\AST\ASTLogicalXorExpression;
use PDepend\Source\AST\ASTMatchEntry;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTMethodPostfix;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTParentReference;
use PDepend\Source\AST\ASTPostDecrementExpression;
use PDepend\Source\AST\ASTPostfixExpression;
use PDepend\Source\AST\ASTPostIncrementExpression;
use PDepend\Source\AST\ASTPreDecrementExpression;
use PDepend\Source\AST\ASTPreIncrementExpression;
use PDepend\Source\AST\ASTPropertyPostfix;
use PDepend\Source\AST\ASTRequireExpression;
use PDepend\Source\AST\ASTReturnStatement;
use PDepend\Source\AST\ASTScope;
use PDepend\Source\AST\ASTScopeStatement;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTShiftLeftExpression;
use PDepend\Source\AST\ASTShiftRightExpression;
use PDepend\Source\AST\ASTStatement;
use PDepend\Source\AST\ASTStaticReference;
use PDepend\Source\AST\ASTStaticVariableDeclaration;
use PDepend\Source\AST\ASTString;
use PDepend\Source\AST\ASTSwitchLabel;
use PDepend\Source\AST\ASTSwitchStatement;
use PDepend\Source\AST\ASTThrowStatement;
use PDepend\Source\AST\ASTTrait;
use PDepend\Source\AST\ASTTraitAdaptation;
use PDepend\Source\AST\ASTTraitAdaptationAlias;
use PDepend\Source\AST\ASTTraitAdaptationPrecedence;
use PDepend\Source\AST\ASTTraitReference;
use PDepend\Source\AST\ASTTraitUseStatement;
use PDepend\Source\AST\ASTTryStatement;
use PDepend\Source\AST\ASTType;
use PDepend\Source\AST\ASTTypeArray;
use PDepend\Source\AST\ASTUnaryExpression;
use PDepend\Source\AST\ASTUnsetStatement;
use PDepend\Source\AST\ASTValue;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableDeclarator;
use PDepend\Source\AST\ASTVariableVariable;
use PDepend\Source\AST\ASTWhileStatement;
use PDepend\Source\AST\ASTYieldStatement;
use PDepend\Source\AST\State;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Parser\InvalidStateException;
use PDepend\Source\Parser\MissingValueException;
use PDepend\Source\Parser\NoActiveScopeException;
use PDepend\Source\Parser\ParserException;
use PDepend\Source\Parser\SymbolTable;
use PDepend\Source\Parser\TokenStack;
use PDepend\Source\Parser\TokenStreamEndException;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Token;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Source\Tokenizer\Tokens;
use PDepend\Util\Cache\CacheDriver;
use PDepend\Util\IdBuilder;
use PDepend\Util\Log;
use PDepend\Util\Type;

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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
abstract class AbstractPHPParser
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
                      |
                       ([a-zA-Z_\x7f-\xff\\\\][a-zA-Z0-9_\x7f-\xff\|\\\\]*)\[\]
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
                       ([a-zA-Z_\x7f-\xff\\\\][a-zA-Z0-9_\x7f-\xff\|\\\\]*)\[\]\s+
                       |
                       (array)\(\s*\)\s+
                     )ix';

    /**
     * Internal state flag, that will be set to <b>true</b> when the parser has
     * prefixed a qualified name with the actual namespace.
     *
     * @var bool
     */
    protected $namespacePrefixReplaced = false;

    /**
     * The name of the last detected namespace.
     *
     * @var string|null
     */
    private $namespaceName;

    /**
     * Last parsed package tag.
     *
     * @var string|null
     */
    private $packageName = Builder::DEFAULT_NAMESPACE;

    /**
     * The package defined in the file level comment.
     *
     * @var string|null
     */
    private $globalPackageName = Builder::DEFAULT_NAMESPACE;

    /**
     * The used data structure builder.
     *
     * @var PHPBuilder<mixed>
     */
    protected $builder;

    /**
     * The currently parsed file instance.
     *
     * @var ASTCompilationUnit
     */
    protected $compilationUnit;

    /**
     * The symbol table used to handle PHP 5.3 use statements.
     *
     * @var SymbolTable
     */
    protected $useSymbolTable;

    /**
     * The last parsed doc comment or <b>null</b>.
     *
     * @var string|null
     */
    private $docComment;

    /**
     * Bitfield of last parsed modifiers.
     *
     * @var int
     */
    private $modifiers = 0;

    /**
     * The actually parsed class or interface instance.
     *
     * @var AbstractASTClassOrInterface|null
     */
    protected $classOrInterface;

    /**
     * If this property is set to <b>true</b> the parser will ignore all doc
     * comment annotations.
     *
     * @var bool
     */
    private $ignoreAnnotations = false;

    /**
     * Stack with all active token scopes.
     *
     * @var TokenStack
     */
    protected $tokenStack;

    /**
     * Used identifier builder instance.
     *
     * @var IdBuilder
     *
     * @since 0.9.12
     */
    private $idBuilder = null;

    /**
     * The maximum valid nesting level allowed.
     *
     * @var int
     *
     * @since 0.9.12
     */
    private $maxNestingLevel = 1024;

    /**
     * @var CacheDriver
     *
     * @since 0.10.0
     */
    protected $cache;

    /**
     * The used code tokenizer.
     *
     * @var Tokenizer
     */
    protected $tokenizer;

    /**
     * Constructs a new source parser.
     *
     * @param PHPBuilder<mixed> $builder
     */
    public function __construct(Tokenizer $tokenizer, Builder $builder, CacheDriver $cache)
    {
        $this->tokenizer = $tokenizer;
        $this->builder   = $builder;
        $this->cache     = $cache;

        $this->idBuilder    = new IdBuilder();
        $this->tokenStack     = new TokenStack();
        $this->useSymbolTable = new SymbolTable();

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
     * @param int $maxNestingLevel The maximum allowed nesting level.
     *
     * @return void
     *
     * @since 0.9.12
     */
    public function setMaxNestingLevel($maxNestingLevel)
    {
        $this->maxNestingLevel = $maxNestingLevel;
    }

    /**
     * Returns the maximum allowed nesting/recursion level.
     *
     * @return int
     *
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
        $this->compilationUnit = $this->tokenizer->getSourceFile();
        $this->compilationUnit
            ->setCache($this->cache)
            ->setId($this->idBuilder->forFile($this->compilationUnit));

        if ($this->compilationUnit->getFileName() === 'php://stdin') {
            $hash = md5('php://stdin');
        } else {
            $hash = md5_file($this->compilationUnit->getFileName());
        }

        if ($this->cache->restore($this->compilationUnit->getId(), $hash)) {
            return;
        }

        $this->cache->remove($this->compilationUnit->getId());

        $this->setUpEnvironment();

        $this->tokenStack->push();

        Log::debug('Processing file ' . $this->compilationUnit);

        $tokenType = $this->tokenizer->peek();

        while ($tokenType !== Tokenizer::T_EOF) {
            switch ($tokenType) {
                case Tokens::T_COMMENT:
                    $this->consumeToken(Tokens::T_COMMENT);
                    break;
                case Tokens::T_DOC_COMMENT:
                    $comment = $this->consumeToken(Tokens::T_DOC_COMMENT)->image;

                    $this->packageName = $this->parsePackageAnnotation($comment);
                    $this->docComment  = $comment;
                    break;
                case Tokens::T_USE:
                    // Parse a use statement. This method has no return value but it
                    // creates a new entry in the symbol map.
                    $this->parseUseDeclarations();
                    break;
                case Tokens::T_NAMESPACE:
                    $this->parseNamespaceDeclaration();
                    break;
                case Tokens::T_NO_PHP:
                case Tokens::T_OPEN_TAG:
                case Tokens::T_OPEN_TAG_WITH_ECHO:
                    $this->consumeToken($tokenType);
                    $this->reset();
                    break;
                case Tokens::T_CLOSE_TAG:
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

        $this->compilationUnit->setTokens($this->tokenStack->pop());
        $this->cache->store(
            $this->compilationUnit->getId(),
            $this->compilationUnit,
            $hash
        );

        $this->tearDownEnvironment();
    }

    /**
     * Initializes the parser environment.
     *
     * @return void
     *
     * @since 0.9.12
     */
    protected function setUpEnvironment()
    {
        ini_set('xdebug.max_nesting_level', (string)$this->getMaxNestingLevel());

        $this->useSymbolTable->createScope();

        $this->reset();
    }

    /**
     * Restores the parser environment back.
     *
     * @throws NoActiveScopeException
     *
     * @return void
     *
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
     * @param int $modifiers Optional default modifiers.
     *
     * @return void
     */
    protected function reset($modifiers = 0)
    {
        $this->packageName = Builder::DEFAULT_NAMESPACE;
        $this->docComment  = null;
        $this->modifiers   = $modifiers;
    }

    /**
     * Tests if the given token type is a reserved keyword in the supported PHP
     * version.
     *
     * @param int $tokenType
     *
     * @return bool
     *
     * @since 1.1.1
     */
    abstract protected function isKeyword($tokenType);

    /**
     * Parses a valid class or interface name and returns the image of the parsed
     * token.
     *
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     *
     * @return string
     */
    protected function parseClassName()
    {
        $type = $this->tokenizer->peek();

        if ($this->isClassName($type)) {
            return $this->consumeToken($type)->image;
        } elseif ($type === Tokenizer::T_EOF) {
            throw new TokenStreamEndException($this->tokenizer);
        }

        throw $this->getUnexpectedTokenException();
    }

    /**
     * Will return <b>true</b> if the given <b>$tokenType</b> is a valid class
     * name part.
     *
     * @param int $tokenType
     *
     * @return bool
     *
     * @since 0.10.6
     */
    protected function isClassName($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_NULL:
            case Tokens::T_TRUE:
            case Tokens::T_FALSE:
            case Tokens::T_TRAIT:
            case Tokens::T_YIELD:
            case Tokens::T_STRING:
            case Tokens::T_TRAIT_C:
            case Tokens::T_CALLABLE:
            case Tokens::T_INSTEADOF:
            case Tokens::T_READONLY:
                return true;
        }

        return false;
    }

    /**
     * Parses a function name from the given tokenizer and returns the string
     * literal representing the function name. If no valid token exists in the
     * token stream, this method will throw an exception.
     *
     * @throws UnexpectedTokenException
     * @throws TokenStreamEndException
     *
     * @return string
     *
     * @since 0.10.0
     */
    protected function parseFunctionName()
    {
        $tokenType = $this->tokenizer->peek();

        if ($this->isFunctionName($tokenType)) {
            return $this->consumeToken($tokenType)->image;
        } elseif ($tokenType === Tokenizer::T_EOF) {
            throw new TokenStreamEndException($this->tokenizer);
        }

        throw $this->getUnexpectedTokenException();
    }

    /**
     * @param int $tokenType
     *
     * @return bool
     */
    private function isAllowedName($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_NULL:
            case Tokens::T_SELF:
            case Tokens::T_TRUE:
            case Tokens::T_FALSE:
            case Tokens::T_TRAIT:
            case Tokens::T_YIELD:
            case Tokens::T_PARENT:
            case Tokens::T_STRING:
            case Tokens::T_TRAIT_C:
            case Tokens::T_CALLABLE:
            case Tokens::T_INSTEADOF:
                return true;
        }
        return false;
    }

    /**
     * @param int $tokenType
     *
     * @return bool
     */
    protected function isConstantName($tokenType)
    {
        return $this->isAllowedName($tokenType);
    }

    /**
     * Tests if the give token is a valid function name in the supported PHP
     * version.
     *
     * @param int $tokenType
     *
     * @return bool
     *
     * @since 2.3
     */
    protected function isFunctionName($tokenType)
    {
        return $this->isAllowedName($tokenType);
    }

    /**
     * @throws UnexpectedTokenException
     * @throws TokenStreamEndException
     *
     * @return string
     */
    protected function parseMethodName()
    {
        $tokenType = $this->tokenizer->peek();

        if ($this->isMethodName($tokenType)) {
            return $this->consumeToken($tokenType)->image;
        } elseif ($tokenType === Tokenizer::T_EOF) {
            throw new TokenStreamEndException($this->tokenizer);
        }

        throw $this->getUnexpectedTokenException();
    }

    /**
     * @param int $tokenType
     *
     * @return bool
     */
    protected function isMethodName($tokenType)
    {
        return $this->isAllowedName($tokenType);
    }

    /**
     * Parses a trait declaration.
     *
     * @return ASTTrait
     *
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
     * @return ASTTrait
     */
    private function parseTraitSignature()
    {
        $this->consumeToken(Tokens::T_TRAIT);
        $this->consumeComments();

        $qualifiedName = $this->createQualifiedTypeName($this->parseClassName());

        $trait = $this->builder->buildTrait($qualifiedName);
        $trait->setCompilationUnit($this->compilationUnit);
        $trait->setComment($this->docComment);
        $trait->setId($this->idBuilder->forClassOrInterface($trait));
        $trait->setUserDefined();

        return $trait;
    }

    /**
     * Parses the dependencies in a interface signature.
     *
     * @return ASTInterface
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
     * @return ASTInterface
     *
     * @since 0.10.2
     */
    private function parseInterfaceSignature()
    {
        $this->consumeToken(Tokens::T_INTERFACE);
        $this->consumeComments();

        $qualifiedName = $this->createQualifiedTypeName($this->parseClassName());

        $interface = $this->builder->buildInterface($qualifiedName);
        $interface->setCompilationUnit($this->compilationUnit);
        $interface->setComment($this->docComment);
        $interface->setId($this->idBuilder->forClassOrInterface($interface));
        $interface->setUserDefined();

        return $this->parseOptionalExtendsList($interface);
    }

    /**
     * Parses an optional interface list of an interface declaration.
     *
     * @return ASTInterface
     *
     * @since 0.10.2
     */
    private function parseOptionalExtendsList(ASTInterface $interface)
    {
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === Tokens::T_EXTENDS) {
            $this->consumeToken(Tokens::T_EXTENDS);
            $this->parseInterfaceList($interface);
        }
        return $interface;
    }

    /**
     * Parses the dependencies in a class signature.
     *
     * @return ASTClass
     */
    protected function parseClassDeclaration()
    {
        $startToken = $this->tokenizer->currentToken();
        $this->tokenStack->push();

        $class = $this->parseClassSignature();
        $class = $this->parseTypeBody($class);
        $class->setTokens($this->tokenStack->pop(), $startToken);

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
     * @return ASTClass
     *
     * @since 1.0.0
     */
    protected function parseClassSignature()
    {
        $this->parseClassModifiers();
        $this->consumeToken(Tokens::T_CLASS);
        $this->consumeComments();

        $qualifiedName = $this->createQualifiedTypeName($this->parseClassName());

        $class = $this->builder->buildClass($qualifiedName);
        $class->setCompilationUnit($this->compilationUnit);
        $class->setModifiers($this->modifiers);
        $class->setComment($this->docComment);
        $class->setId($this->idBuilder->forClassOrInterface($class));
        $class->setUserDefined();

        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === Tokens::T_EXTENDS) {
            $class = $this->parseClassExtends($class);

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();
        }

        if ($tokenType === Tokens::T_IMPLEMENTS) {
            $this->consumeToken(Tokens::T_IMPLEMENTS);
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

        if ($tokenType === Tokens::T_ABSTRACT) {
            $this->consumeToken(Tokens::T_ABSTRACT);
            $this->modifiers |= State::IS_EXPLICIT_ABSTRACT;
        } elseif ($tokenType === Tokens::T_FINAL) {
            $this->consumeToken(Tokens::T_FINAL);
            $this->modifiers |= State::IS_FINAL;
        }

        $this->consumeComments();
    }

    /**
     * Parses a parent class declaration for the given <b>$class</b>.
     *
     * @return ASTClass
     *
     * @since 1.0.0
     */
    protected function parseClassExtends(ASTClass $class)
    {
        $this->consumeToken(Tokens::T_EXTENDS);
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
     * @return void
     */
    protected function parseInterfaceList(AbstractASTClassOrInterface $abstractType)
    {
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

            if ($tokenType === Tokens::T_CURLY_BRACE_OPEN) {
                break;
            }
            $this->consumeToken(Tokens::T_COMMA);
        }
    }

    /**
     * Parses a class/interface/trait body.
     *
     * @template T of AbstractASTClassOrInterface
     *
     * @param T $classOrInterface
     *
     * @throws UnexpectedTokenException
     * @throws TokenStreamEndException
     *
     * @return T
     */
    protected function parseTypeBody(AbstractASTClassOrInterface $classOrInterface)
    {
        $this->classOrInterface = $classOrInterface;

        // Consume comments and read opening curly brace
        $this->consumeComments();
        $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);

        $defaultModifier = State::IS_PUBLIC;
        if ($classOrInterface instanceof ASTInterface) {
            $defaultModifier |= State::IS_ABSTRACT;
        }
        $this->reset();

        $tokenType = $this->tokenizer->peek();

        while ($tokenType !== Tokenizer::T_EOF) {
            switch ($tokenType) {
                case Tokens::T_ABSTRACT:
                case Tokens::T_PUBLIC:
                case Tokens::T_PRIVATE:
                case Tokens::T_PROTECTED:
                case Tokens::T_STATIC:
                case Tokens::T_FINAL:
                case Tokens::T_FUNCTION:
                case Tokens::T_VARIABLE:
                case Tokens::T_VAR:
                    $methodOrProperty = $this->parseMethodOrFieldDeclaration(
                        $defaultModifier
                    );

                    if ($methodOrProperty instanceof ASTNode) {
                        $classOrInterface->addChild($methodOrProperty);
                    }

                    $this->reset();
                    break;
                case Tokens::T_CONST:
                    $classOrInterface->addChild($this->parseConstantDefinition());
                    $this->reset();
                    break;
                case Tokens::T_CURLY_BRACE_CLOSE:
                    $this->consumeToken(Tokens::T_CURLY_BRACE_CLOSE);

                    $this->reset();

                    // Reset context class or interface instance
                    $this->classOrInterface = null;

                    // Stop processing
                    return $classOrInterface;
                case Tokens::T_COMMENT:
                    $token = $this->consumeToken(Tokens::T_COMMENT);

                    $comment = $this->builder->buildAstComment($token->image);
                    $comment->configureLinesAndColumns(
                        $token->startLine,
                        $token->endLine,
                        $token->startColumn,
                        $token->endColumn
                    );

                    $classOrInterface->addChild($comment);
                    break;
                case Tokens::T_DOC_COMMENT:
                    $token = $this->consumeToken(Tokens::T_DOC_COMMENT);

                    $comment = $this->builder->buildAstComment($token->image);
                    $comment->configureLinesAndColumns(
                        $token->startLine,
                        $token->endLine,
                        $token->startColumn,
                        $token->endColumn
                    );

                    $classOrInterface->addChild($comment);

                    $this->docComment = $token->image;
                    break;
                case Tokens::T_USE:
                    $classOrInterface->addChild($this->parseTraitUseStatement());
                    break;
                default:
                    throw $this->getUnexpectedTokenException();
            }

            $tokenType = $this->tokenizer->peek();
        }

        throw new TokenStreamEndException($this->tokenizer);
    }

    /**
     * This method will parse a list of modifiers and a following property or
     * method.
     *
     * @param int $modifiers
     *
     * @return ASTConstantDefinition|ASTFieldDeclaration|ASTMethod
     *
     * @since 0.9.6
     */
    protected function parseMethodOrFieldDeclaration($modifiers = 0)
    {
        $this->tokenStack->push();
        $tokenType = $this->tokenizer->peek();

        while ($tokenType !== Tokenizer::T_EOF) {
            switch ($tokenType) {
                case Tokens::T_PRIVATE:
                    $modifiers |= State::IS_PRIVATE;
                    $modifiers = $modifiers & ~State::IS_PUBLIC;
                    break;
                case Tokens::T_PROTECTED:
                    $modifiers |= State::IS_PROTECTED;
                    $modifiers = $modifiers & ~State::IS_PUBLIC;
                    break;
                case Tokens::T_VAR:
                case Tokens::T_PUBLIC:
                    $modifiers |= State::IS_PUBLIC;
                    break;
                case Tokens::T_STATIC:
                    $modifiers |= State::IS_STATIC;
                    break;
                case Tokens::T_ABSTRACT:
                    $modifiers |= State::IS_ABSTRACT;
                    break;
                case Tokens::T_FINAL:
                    $modifiers |= State::IS_FINAL;
                    break;
                case Tokens::T_READONLY:
                    $modifiers |= State::IS_READONLY;
                    break;
                case Tokens::T_FUNCTION:
                    $method = $this->parseMethodDeclaration();
                    $method->setModifiers($modifiers);
                    $method->setCompilationUnit($this->compilationUnit);
                    $method->setId($this->idBuilder->forMethod($method));
                    $method->setTokens($this->tokenStack->pop());

                    return $method;
                case Tokens::T_VARIABLE:
                    $declaration = $this->parseFieldDeclaration();
                    $declaration->setModifiers($modifiers);

                    return $declaration;
                default:
                    return $this->parseUnknownDeclaration($tokenType, $modifiers);
            }

            $this->consumeToken($tokenType);
            $this->consumeComments();

            $tokenType = $this->tokenizer->peek();
        }

        throw $this->getUnexpectedTokenException();
    }

    /**
     * Override this in later PHPParserVersions as necessary
     *
     * @param int $tokenType
     * @param int $modifiers
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTConstantDefinition|ASTFieldDeclaration
     */
    protected function parseUnknownDeclaration($tokenType, $modifiers)
    {
        throw $this->getUnexpectedTokenException();
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
     * @return ASTFieldDeclaration
     *
     * @since 0.9.6
     */
    protected function parseFieldDeclaration()
    {
        $declaration = $this->builder->buildAstFieldDeclaration();
        $declaration->setComment($this->docComment);

        $type = $this->parseFieldDeclarationType();

        if ($type !== null) {
            $declaration->addChild($type);
        }

        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        while ($tokenType !== Tokenizer::T_EOF) {
            $declaration->addChild($this->parseVariableDeclarator());

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            if ($tokenType !== Tokens::T_COMMA) {
                break;
            }

            $this->consumeToken(Tokens::T_COMMA);

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();
        }

        $this->setNodePositionsAndReturn($declaration);

        $this->consumeToken(Tokens::T_SEMICOLON);

        return $declaration;
    }

    /**
     * This method parses a simple function or a PHP 5.3 lambda function or
     * closure.
     *
     * @return ASTCallable
     *
     * @since 0.9.5
     */
    private function parseFunctionOrClosureDeclaration()
    {
        $this->tokenStack->push();

        $this->consumeToken(Tokens::T_FUNCTION);
        $this->consumeComments();

        $returnReference = $this->parseOptionalByReference();

        if ($this->isNextTokenFormalParameterList()) {
            return $this->setNodePositionsAndReturn(
                $this->parseClosureDeclaration()
            );
        }

        $callable = $this->parseFunctionDeclaration();
        $this->compilationUnit->addChild($callable);

        $callable->setComment($this->docComment);
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
     * @return bool
     *
     * @since 0.9.8
     */
    protected function parseOptionalByReference()
    {
        return $this->isNextTokenByReference() && $this->parseByReference();
    }

    /**
     * Tests that the next available token is the returns by reference token.
     *
     * @return bool
     *
     * @since 0.9.8
     */
    private function isNextTokenByReference()
    {
        return ($this->tokenizer->peek() === Tokens::T_BITWISE_AND);
    }

    /**
     * This method parses a returns by reference token and returns <b>true</b>.
     *
     * @return bool
     */
    private function parseByReference()
    {
        $this->consumeToken(Tokens::T_BITWISE_AND);
        $this->consumeComments();

        return true;
    }

    /**
     * Tests that the next available token is an opening parenthesis.
     *
     * @return bool
     *
     * @since 0.9.10
     */
    private function isNextTokenFormalParameterList()
    {
        $this->consumeComments();
        return ($this->tokenizer->peek() === Tokens::T_PARENTHESIS_OPEN);
    }

    /**
     * This method parses a function declaration.
     *
     * @return ASTFunction
     *
     * @since 0.9.5
     */
    private function parseFunctionDeclaration()
    {
        $this->consumeComments();

        // Next token must be the function identifier
        $functionName = $this->parseFunctionName();

        $function = $this->builder->buildFunction($functionName);
        $function->setCompilationUnit($this->compilationUnit);
        $function->setId($this->idBuilder->forFunction($function));

        $this->parseCallableDeclaration($function);

        // First check for an existing namespace
        if ($this->namespaceName !== null) {
            $namespaceName = $this->namespaceName;
        } elseif ($this->packageName !== Builder::DEFAULT_NAMESPACE) {
            $namespaceName = $this->packageName;
        } else {
            $namespaceName = $this->globalPackageName;
        }

        $namespace = $this->builder->buildNamespace($namespaceName);
        $namespace->setPackageAnnotation(null === $this->namespaceName);
        $namespace->addFunction($function);

        // Store function in source file, because we need them during the file's
        // __wakeup() phase for function declarations within another function or
        // method declaration.
        $this->compilationUnit->addChild($function);

        return $function;
    }

    /**
     * This method parses a method declaration.
     *
     * @return ASTMethod
     *
     * @since 0.9.5
     */
    private function parseMethodDeclaration()
    {
        // Read function keyword
        $this->consumeToken(Tokens::T_FUNCTION);
        $this->consumeComments();

        $returnsReference = $this->parseOptionalByReference();

        $methodName = $this->parseMethodName();

        $method = $this->builder->buildMethod($methodName);
        $method->setComment($this->docComment);
        $method->setCompilationUnit($this->compilationUnit);

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
     * @return ASTClosure
     *
     * @since 0.9.5
     */
    private function parseClosureDeclaration()
    {
        $this->tokenStack->push();

        if (Tokens::T_FUNCTION === $this->tokenizer->peek()) {
            $this->consumeToken(Tokens::T_FUNCTION);
        }

        $closure = $this->builder->buildAstClosure();
        $closure->setReturnsByReference($this->parseOptionalByReference());
        $closure->addChild($this->parseFormalParameters($closure));
        $closure = $this->parseOptionalBoundVariables($closure);
        $this->parseCallableDeclarationAddition($closure);
        $closure->addChild($this->parseScope());

        return $this->setNodePositionsAndReturn($closure);
    }

    /**
     * Parses a function or a method and adds it to the parent context node.
     *
     * @return void
     */
    private function parseCallableDeclaration(AbstractASTCallable $callable)
    {
        $callable->addChild($this->parseFormalParameters($callable));
        $this->parseCallableDeclarationAddition($callable);

        $this->consumeComments();

        if ($this->tokenizer->peek() == Tokens::T_CURLY_BRACE_OPEN) {
            $callable->addChild($this->parseScope());

            return;
        }

        $this->consumeToken(Tokens::T_SEMICOLON);
    }

    /**
     * Extension for version specific additions.
     *
     * @template T of AbstractASTCallable
     *
     * @param T $callable
     *
     * @return T
     */
    protected function parseCallableDeclarationAddition($callable)
    {
        return $callable;
    }

    /**
     * Parses a trait use statement.
     *
     * @return ASTTraitUseStatement
     *
     * @since 1.0.0
     */
    private function parseTraitUseStatement()
    {
        $this->tokenStack->push();
        $this->consumeToken(Tokens::T_USE);

        $useStatement = $this->builder->buildAstTraitUseStatement();
        $useStatement->addChild($this->parseTraitReference());

        $this->consumeComments();

        while (Tokens::T_COMMA === $this->tokenizer->peek()) {
            $this->consumeToken(Tokens::T_COMMA);
            $useStatement->addChild($this->parseTraitReference());
        }

        return $this->setNodePositionsAndReturn(
            $this->parseOptionalTraitAdaptation($useStatement)
        );
    }

    /**
     * Parses a trait reference instance.
     *
     * @return ASTTraitReference
     *
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
     * @return ASTTraitUseStatement
     *
     * @since 1.0.0
     */
    private function parseOptionalTraitAdaptation(ASTTraitUseStatement $useStatement)
    {
        $this->consumeComments();

        if (Tokens::T_CURLY_BRACE_OPEN === $this->tokenizer->peek()) {
            $useStatement->addChild($this->parseTraitAdaptation());
        } else {
            $this->consumeToken(Tokens::T_SEMICOLON);
        }

        return $useStatement;
    }

    /**
     * Parses the adaptation expression of a trait use statement.
     *
     * @return ASTTraitAdaptation
     *
     * @since 1.0.0
     */
    private function parseTraitAdaptation()
    {
        $this->tokenStack->push();

        $adaptation = $this->builder->buildAstTraitAdaptation();

        $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);

        do {
            $this->tokenStack->push();

            $reference = $this->parseTraitMethodReference();
            $this->consumeComments();

            $stmt = Tokens::T_AS === $this->tokenizer->peek()
                ? $this->parseTraitAdaptationAliasStatement($reference)
                : $this->parseTraitAdaptationPrecedenceStatement($reference);

            $this->consumeComments();
            $this->consumeToken(Tokens::T_SEMICOLON);

            $adaptation->addChild($this->setNodePositionsAndReturn($stmt));

            $this->consumeComments();
        } while (Tokens::T_CURLY_BRACE_CLOSE !== $this->tokenizer->peek());

        $this->consumeToken(Tokens::T_CURLY_BRACE_CLOSE);

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
     * {@link ASTTraitReference} class that represents the
     * declaring trait.
     *
     * @return array<int, mixed>
     *
     * @since 1.0.0
     */
    private function parseTraitMethodReference()
    {
        $this->tokenStack->push();

        $qualifiedName = $this->parseQualifiedName();

        $this->consumeComments();

        if (Tokens::T_DOUBLE_COLON === $this->tokenizer->peek()) {
            $traitReference = $this->setNodePositionsAndReturn(
                $this->builder->buildAstTraitReference($qualifiedName)
            );

            $this->consumeToken(Tokens::T_DOUBLE_COLON);
            $this->consumeComments();

            return array($this->parseMethodName(), $traitReference);
        }

        $this->tokenStack->pop();

        return array($qualifiedName);
    }

    /**
     * Parses a trait adaptation alias statement.
     *
     * @param array<int, mixed> $reference Parsed method reference array.
     *
     * @return ASTTraitAdaptationAlias
     *
     * @since 1.0.0
     */
    private function parseTraitAdaptationAliasStatement(array $reference)
    {
        $stmt = $this->builder->buildAstTraitAdaptationAlias($reference[0]);

        if (2 === count($reference)) {
            $stmt->addChild($reference[1]);
        }

        $this->consumeToken(Tokens::T_AS);
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {
            case Tokens::T_PUBLIC:
                $stmt->setNewModifier(State::IS_PUBLIC);
                $this->consumeToken(Tokens::T_PUBLIC);
                $this->consumeComments();
                break;
            case Tokens::T_PROTECTED:
                $stmt->setNewModifier(State::IS_PROTECTED);
                $this->consumeToken(Tokens::T_PROTECTED);
                $this->consumeComments();
                break;
            case Tokens::T_PRIVATE:
                $stmt->setNewModifier(State::IS_PRIVATE);
                $this->consumeToken(Tokens::T_PRIVATE);
                $this->consumeComments();
                break;
        }

        if (Tokens::T_SEMICOLON !== $this->tokenizer->peek()) {
            $stmt->setNewName($this->parseMethodName());
        }
        return $stmt;
    }

    /**
     * Parses a trait adaptation precedence statement.
     *
     * @param array<int, mixed> $reference Parsed method reference array.
     *
     * @throws InvalidStateException
     *
     * @return ASTTraitAdaptationPrecedence
     *
     * @since 1.0.0
     */
    private function parseTraitAdaptationPrecedenceStatement(array $reference)
    {
        if (count($reference) < 2) {
            throw new InvalidStateException(
                $this->tokenizer->next()->startLine,
                $this->compilationUnit->getFileName(),
                'Expecting full qualified trait method name.'
            );
        }

        $stmt = $this->builder->buildAstTraitAdaptationPrecedence($reference[0]);
        $stmt->addChild($reference[1]);

        $this->consumeToken(Tokens::T_INSTEADOF);
        $this->consumeComments();

        $stmt->addChild($this->parseTraitReference());

        $this->consumeComments();
        while (Tokens::T_COMMA === $this->tokenizer->peek()) {
            $this->consumeToken(Tokens::T_COMMA);
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
     * @return ASTAllocationExpression
     *
     * @since 0.9.6
     */
    private function parseAllocationExpression()
    {
        $this->tokenStack->push();

        $token = $this->consumeToken(Tokens::T_NEW);

        $allocation = $this->builder->buildAstAllocationExpression($token->image);
        $allocation = $this->parseAllocationExpressionTypeReference($allocation);

        if ($this->isNextTokenArguments()) {
            $allocation->addChild($this->parseArguments());
        }

        return $this->setNodePositionsAndReturn($allocation);
    }

    /**
     * Parse the type reference used in an allocation expression.
     *
     * @template T of ASTAllocationExpression
     *
     * @param T $allocation
     *
     * @return T
     *
     * @since 2.3
     */
    protected function parseAllocationExpressionTypeReference(ASTAllocationExpression $allocation)
    {
        return $this->parseExpressionTypeReference($allocation, true);
    }

    /**
     * Parses a eval-expression node.
     *
     * @return ASTEvalExpression
     *
     * @since 0.9.12
     */
    private function parseEvalExpression()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_EVAL);

        $expr = $this->builder->buildAstEvalExpression($token->image);
        $expr->addChild($this->parseParenthesisExpression());

        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * This method parses an exit-expression.
     *
     * @return ASTExitExpression
     *
     * @since 0.9.12
     */
    private function parseExitExpression()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_EXIT);

        $expr = $this->builder->buildAstExitExpression($token->image);

        $this->consumeComments();
        if ($this->tokenizer->peek() === Tokens::T_PARENTHESIS_OPEN) {
            $expr->addChild($this->parseParenthesisExpression());
        }
        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * Parses a clone-expression node.
     *
     * @return ASTCloneExpression
     *
     * @since 0.9.12
     */
    private function parseCloneExpression()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_CLONE);

        $expr = $this->builder->buildAstCloneExpression($token->image);
        $expr->addChild($this->parseExpression());

        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * Throws an exception if the given token is not a valid list unpacking opening token for current PHP level.
     *
     * @param int   $tokenType
     * @param Token $unexpectedToken
     *
     * @return void
     */
    private function ensureTokenIsListUnpackingOpening($tokenType, $unexpectedToken = null)
    {
        if (!$this->isListUnpacking($tokenType)) {
            throw $this->getUnexpectedTokenException($unexpectedToken ?: $this->tokenizer->prevToken());
        }
    }

    /**
     * Return true if current PHP level supports keys in lists.
     *
     * @return bool
     */
    protected function supportsKeysInList()
    {
        return false;
    }

    /**
     * This method parses a single list-statement node.
     *
     * @return ASTListExpression
     *
     * @since 0.9.12
     */
    private function parseListExpression()
    {
        $this->tokenStack->push();

        $tokenType = $this->tokenizer->peek();
        $this->ensureTokenIsListUnpackingOpening($tokenType);
        $shortSyntax = ($tokenType !== Tokens::T_LIST);

        if ($shortSyntax) {
            $token = $this->consumeToken(Tokens::T_SQUARED_BRACKET_OPEN);
            $list = $this->builder->buildAstListExpression($token->image);
        } else {
            $token = $this->consumeToken(Tokens::T_LIST);
            $this->consumeComments();

            $list = $this->builder->buildAstListExpression($token->image);

            $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);
        }

        $this->consumeComments();

        while (($tokenType = $this->tokenizer->peek()) !== Tokenizer::T_EOF) {
            // The variable is optional:
            //   list(, , , , $something) = ...;
            // is valid.
            switch ($tokenType) {
                case Tokens::T_COMMA:
                    $this->consumeToken(Tokens::T_COMMA);
                    $this->consumeComments();
                    break;
                case Tokens::T_SQUARED_BRACKET_CLOSE:
                case Tokens::T_PARENTHESIS_CLOSE:
                    break 2;
                case Tokens::T_LIST:
                case Tokens::T_SQUARED_BRACKET_OPEN:
                    $list->addChild($this->parseListExpression());
                    $this->consumeComments();
                    break;
                default:
                    $list->addChild($this->parseListSlotExpression());
                    $this->consumeComments();
                    break;
            }
        }

        $closeToken = $shortSyntax ? Tokens::T_SQUARED_BRACKET_CLOSE : Tokens::T_PARENTHESIS_CLOSE;
        $this->consumeToken($closeToken);

        return $this->setNodePositionsAndReturn($list);
    }

    /**
     * Parse individual slot of a list() expression.
     *
     * @return ASTListExpression|ASTNode
     */
    private function parseListSlotExpression()
    {
        $startToken = $this->tokenizer->currentToken();
        $node = $this->parseOptionalExpression();

        if ($node && !$this->isReadWriteVariable($node) && $this->tokenizer->peek() === Tokens::T_DOUBLE_ARROW) {
            if (!$this->supportsKeysInList()) {
                throw $this->getUnexpectedTokenException($startToken);
            }

            $this->consumeComments();
            $this->consumeToken(Tokens::T_DOUBLE_ARROW);
            $this->consumeComments();

            return in_array($this->tokenizer->peek(), array(Tokens::T_LIST, Tokens::T_SQUARED_BRACKET_OPEN))
                ? $this->parseListExpression()
                : $this->parseVariableOrConstantOrPrimaryPrefix();
        }

        return $node ?: $this->parseVariableOrConstantOrPrimaryPrefix();
    }

    /**
     * Parses a include-expression node.
     *
     * @return ASTIncludeExpression
     *
     * @since 0.9.12
     */
    private function parseIncludeExpression()
    {
        $expr = $this->builder->buildAstIncludeExpression();

        return $this->parseRequireOrIncludeExpression($expr, Tokens::T_INCLUDE);
    }

    /**
     * Parses a include_once-expression node.
     *
     * @return ASTIncludeExpression
     *
     * @since 0.9.12
     */
    private function parseIncludeOnceExpression()
    {
        $expr = $this->builder->buildAstIncludeExpression();
        $expr->setOnce();

        return $this->parseRequireOrIncludeExpression($expr, Tokens::T_INCLUDE_ONCE);
    }

    /**
     * Parses a require-expression node.
     *
     * @return ASTRequireExpression
     *
     * @since 0.9.12
     */
    private function parseRequireExpression()
    {
        $expr = $this->builder->buildAstRequireExpression();

        return $this->parseRequireOrIncludeExpression($expr, Tokens::T_REQUIRE);
    }

    /**
     * Parses a require_once-expression node.
     *
     * @return ASTRequireExpression
     *
     * @since 0.9.12
     */
    private function parseRequireOnceExpression()
    {
        $expr = $this->builder->buildAstRequireExpression();
        $expr->setOnce();

        return $this->parseRequireOrIncludeExpression($expr, Tokens::T_REQUIRE_ONCE);
    }

    /**
     * Parses a <b>require_once</b>-, <b>require</b>-, <b>include_once</b>- or
     * <b>include</b>-expression node.
     *
     * @template T of ASTExpression
     *
     * @param T   $expr
     * @param int $type
     *
     * @return T
     *
     * @since 0.9.12
     */
    private function parseRequireOrIncludeExpression(ASTExpression $expr, $type)
    {
        $this->tokenStack->push();

        $this->consumeToken($type);
        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_PARENTHESIS_OPEN) {
            $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);
            $expr->addChild($this->parseOptionalExpression());
            $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);
        } else {
            $expr->addChild($this->parseOptionalExpression());
        }

        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * Parses a cast-expression node.
     *
     * @return ASTCastExpression
     *
     * @since 0.10.0
     */
    protected function parseCastExpression()
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
     * This method will parse an increment-expression. Depending on the previous node
     * this can be a {@link ASTPostIncrementExpression} or {@link ASTPostfixExpression}.
     *
     * @param array<ASTNode> $expressions List of previous parsed expression nodes.
     *
     * @return ASTExpression
     *
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
     * @param ASTNode $child The child expression node.
     *
     * @return ASTPostfixExpression
     *
     * @since 0.10.0
     */
    private function parsePostIncrementExpression(ASTNode $child)
    {
        $token = $this->consumeToken(Tokens::T_INC);

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
     * @return ASTPreIncrementExpression
     *
     * @since 0.10.0
     */
    private function parsePreIncrementExpression()
    {
        $token = $this->consumeToken(Tokens::T_INC);

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
     * This method will parse an decrement-expression. Depending on the previous node
     * this can be a {@link ASTPostDecrementExpression} or {@link ASTPostfixExpression}.
     *
     * @param array<ASTNode> $expressions List of previous parsed expression nodes.
     *
     * @return ASTExpression
     *
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
     * @param ASTNode $child The child expression node.
     *
     * @return ASTPostfixExpression
     *
     * @since 0.10.0
     */
    private function parsePostDecrementExpression(ASTNode $child)
    {
        $token = $this->consumeToken(Tokens::T_DEC);

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
     * @return ASTPreDecrementExpression
     *
     * @since 0.10.0
     */
    private function parsePreDecrementExpression()
    {
        $token = $this->consumeToken(Tokens::T_DEC);

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
     * @template T of ASTNode
     *
     * @param T $node The parent/context node instance.
     *
     * @return ASTIndexExpression|T
     *
     * @since 0.9.12
     */
    protected function parseOptionalIndexExpression(ASTNode $node)
    {
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {
            case Tokens::T_CURLY_BRACE_OPEN:
                return $this->parseStringIndexExpression($node);
            case Tokens::T_SQUARED_BRACKET_OPEN:
                return $this->parseArrayIndexExpression($node);
        }

        return $node;
    }

    /**
     * Parses an index expression as it is valid to access elements in a php
     * string or array.
     *
     * @template T of ASTExpression
     *
     * @param ASTNode $node  The context source node.
     * @param T       $expr  The concrete index expression.
     * @param int     $open  The open token type.
     * @param int     $close The close token type.
     *
     * @return ASTIndexExpression|T
     *
     * @since 0.9.12
     */
    private function parseIndexExpression(
        ASTNode $node,
        ASTExpression $expr,
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
     * @param ASTNode $node The context source node.
     *
     * @return ASTIndexExpression
     *
     * @since 0.9.12
     */
    private function parseArrayIndexExpression(ASTNode $node)
    {
        $expr = $this->builder->buildAstArrayIndexExpression();
        $expr->addChild($node);

        return $this->parseIndexExpression(
            $node,
            $expr,
            Tokens::T_SQUARED_BRACKET_OPEN,
            Tokens::T_SQUARED_BRACKET_CLOSE
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
     * @param ASTNode $node The context source node.
     *
     * @return ASTIndexExpression
     *
     * @since 0.9.12
     */
    private function parseStringIndexExpression(ASTNode $node)
    {
        $expr = $this->builder->buildAstStringIndexExpression();
        $expr->addChild($node);

        return $this->parseIndexExpression(
            $node,
            $expr,
            Tokens::T_CURLY_BRACE_OPEN,
            Tokens::T_CURLY_BRACE_CLOSE
        );
    }

    /**
     * This method checks if the next available token starts an arguments node.
     *
     * @return bool
     *
     * @since 0.9.8
     */
    protected function isNextTokenArguments()
    {
        $this->consumeComments();
        return $this->tokenizer->peek() === Tokens::T_PARENTHESIS_OPEN;
    }

    /**
     * This method configures the given node with its start and end positions.
     *
     * @template T of ASTNode
     *
     * @param T                      $node
     * @param array<int, Token>|null $tokens
     *
     * @return T
     *
     * @since 0.9.8
     */
    protected function setNodePositionsAndReturn(ASTNode $node, array &$tokens = null)
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
     * @param Token[] $tokens Original token stream.
     *
     * @return Token[]
     *
     * @since 1.0.0
     */
    private function stripTrailingComments(array $tokens)
    {
        $comments = array(Tokens::T_COMMENT, Tokens::T_DOC_COMMENT);

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
     * @return ASTInstanceOfExpression
     *
     * @since 0.9.6
     */
    private function parseInstanceOfExpression()
    {
        // Consume the "instanceof" keyword and strip comments
        $token = $this->consumeToken(Tokens::T_INSTANCEOF);

        return $this->parseExpressionTypeReference(
            $this->builder->buildAstInstanceOfExpression($token->image),
            false
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
     * @return ASTIssetExpression
     *
     * @since 0.9.12
     */
    private function parseIssetExpression()
    {
        $startToken = $this->consumeToken(Tokens::T_ISSET);
        $this->consumeComments();
        $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);
        $this->consumeComments();

        $expr = $this->builder->buildAstIssetExpression();
        $expr = $this->parseVariableList($expr, true);
        $this->consumeComments();

        $stopToken = $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);

        $expr->configureLinesAndColumns(
            $startToken->startLine,
            $stopToken->endLine,
            $startToken->startColumn,
            $stopToken->endColumn
        );

        return $expr;
    }

    protected function allowTrailingCommaInSpecialFunctions()
    {
        return false;
    }

    /**
     * @param bool $classRef
     *
     * @return ASTClassOrInterfaceReference
     */
    private function parseStandAloneExpressionTypeReference($classRef)
    {
        // Peek next token and look for a static type identifier
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {
            case Tokens::T_DOLLAR:
            case Tokens::T_VARIABLE:
                // TODO: Parse variable or Member Primary Prefix + Property Postfix
                return $this->parseVariableOrFunctionPostfixOrMemberPrimaryPrefix();
            case Tokens::T_SELF:
                return $this->parseSelfReference($this->consumeToken(Tokens::T_SELF));
            case Tokens::T_PARENT:
                return $this->parseParentReference($this->consumeToken(Tokens::T_PARENT));
            case Tokens::T_STATIC:
                return $this->parseStaticReference($this->consumeToken(Tokens::T_STATIC));
            default:
                return $this->parseClassOrInterfaceReference($classRef);
        }
    }

    /**
     * This method parses a type identifier as it is used in expression nodes
     * like {@link ASTInstanceOfExpression} or an object
     * allocation node like {@link ASTAllocationExpression}.
     *
     * @template T of AbstractASTNode
     *
     * @param T    $expr
     * @param bool $classRef
     *
     * @return T
     */
    protected function parseExpressionTypeReference(ASTNode $expr, $classRef)
    {
        $expr->addChild(
            $this->parseOptionalMemberPrimaryPrefix(
                $this->parseOptionalStaticMemberPrimaryPrefix(
                    $this->parseStandAloneExpressionTypeReference($classRef)
                )
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
     * @return ASTConditionalExpression
     *
     * @since 0.9.8
     */
    protected function parseConditionalExpression()
    {
        $this->tokenStack->push();
        $this->consumeToken(Tokens::T_QUESTION_MARK);

        $expr = $this->builder->buildAstConditionalExpression();
        if (($child = $this->parseOptionalExpression()) != null) {
            $expr->addChild($child);
        }

        $this->consumeToken(Tokens::T_COLON);

        $expr->addChild($this->parseExpression());

        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * This method parses a shift left expression node.
     *
     * @return ASTShiftLeftExpression
     *
     * @since 1.0.1
     */
    protected function parseShiftLeftExpression()
    {
        $token = $this->consumeToken(Tokens::T_SL);

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
     * @return ASTShiftRightExpression
     *
     * @since 1.0.1
     */
    protected function parseShiftRightExpression()
    {
        $token = $this->consumeToken(Tokens::T_SR);

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
     * @return ASTBooleanAndExpression
     *
     * @since 0.9.8
     */
    protected function parseBooleanAndExpression()
    {
        $token = $this->consumeToken(Tokens::T_BOOLEAN_AND);

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
     * @return ASTBooleanOrExpression
     *
     * @since 0.9.8
     */
    protected function parseBooleanOrExpression()
    {
        $token = $this->consumeToken(Tokens::T_BOOLEAN_OR);

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
     * @return ASTLogicalAndExpression
     *
     * @since 0.9.8
     */
    protected function parseLogicalAndExpression()
    {
        $token = $this->consumeToken(Tokens::T_LOGICAL_AND);

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
     * @return ASTLogicalOrExpression
     *
     * @since 0.9.8
     */
    protected function parseLogicalOrExpression()
    {
        $token = $this->consumeToken(Tokens::T_LOGICAL_OR);

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
     * @return ASTLogicalXorExpression
     *
     * @since 0.9.8
     */
    protected function parseLogicalXorExpression()
    {
        $token = $this->consumeToken(Tokens::T_LOGICAL_XOR);

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
     * @param bool $classReference Force a class reference.
     *
     * @return ASTClassOrInterfaceReference
     *
     * @since 0.9.8
     */
    private function parseClassOrInterfaceReference($classReference)
    {
        $this->tokenStack->push();

        return $this->setNodePositionsAndReturn(
            $this->builder->buildAstNeededReference(
                $this->parseQualifiedName(),
                $classReference
            )
        );
    }

    /**
     * This method parses a brace expression and adds all parsed node instances
     * to the given {@link ASTNode} object. Finally it returns
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
     * @template T of AbstractASTNode
     *
     * @param T   $node
     * @param int $closeToken
     *
     * @throws TokenStreamEndException
     *
     * @return T
     *
     * @since 0.9.6
     */
    protected function parseBraceExpression(
        ASTNode $node,
        Token $start,
        $closeToken,
        $separatorToken = null
    ) {
        if (is_object($expr = $this->parseOptionalExpression())) {
            $node->addChild($expr);
        }

        $this->consumeComments();

        while ($separatorToken && $this->tokenizer->peek() === $separatorToken) {
            $this->consumeToken($separatorToken);

            if (is_object($expr = $this->parseOptionalExpression())) {
                $node->addChild($expr);
            }

            $this->consumeComments();
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
     * @template T of ASTStatement
     *
     * @param T $stmt The owning statement.
     *
     * @return T
     *
     * @since 0.9.12
     */
    private function parseStatementBody(ASTStatement $stmt)
    {
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === Tokens::T_CURLY_BRACE_OPEN) {
            $stmt->addChild($this->parseRegularScope());
        } elseif ($tokenType === Tokens::T_COLON) {
            $stmt->addChild($this->parseAlternativeScope());
        } else {
            $stmt->addChild($this->parseStatement());
        }
        return $stmt;
    }

    /**
     * Parse a scope enclosed by curly braces.
     *
     * @return ASTScopeStatement
     *
     * @since 0.9.12
     */
    private function parseRegularScope()
    {
        $this->tokenStack->push();

        $this->consumeComments();
        $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);

        $scope = $this->parseScopeStatements();

        $this->consumeToken(Tokens::T_CURLY_BRACE_CLOSE);
        return $this->setNodePositionsAndReturn($scope);
    }

    /**
     * Parses the scope of a statement that is surrounded with PHP's alternative
     * syntax for statements.
     *
     * @return ASTScopeStatement
     *
     * @since 0.10.0
     */
    private function parseAlternativeScope()
    {
        $this->tokenStack->push();
        $this->consumeToken(Tokens::T_COLON);

        $scope = $this->parseScopeStatements();

        $this->parseOptionalAlternativeScopeTermination();
        return $this->setNodePositionsAndReturn($scope);
    }

    /**
     * Parses all statements that exist in a scope an adds them to a scope
     * instance.
     *
     * @return ASTScopeStatement
     *
     * @since 0.10.0
     */
    private function parseScopeStatements()
    {
        $scope = $this->builder->buildAstScopeStatement();
        while (($child = $this->parseOptionalStatement()) != null) {
            if ($child instanceof ASTNode) {
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
     *
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
     * @param int $tokenType The token type identifier.
     *
     * @return bool
     *
     * @since 0.10.0
     */
    private function isAlternativeScopeTermination($tokenType)
    {
        return in_array(
            $tokenType,
            array(
                Tokens::T_ENDDECLARE,
                Tokens::T_ENDFOR,
                Tokens::T_ENDFOREACH,
                Tokens::T_ENDIF,
                Tokens::T_ENDSWITCH,
                Tokens::T_ENDWHILE
            )
        );
    }

    /**
     * Parses a series of tokens that represent an alternative scope termination.
     *
     * @param int $tokenType The token type identifier.
     *
     * @return void
     *
     * @since 0.10.0
     */
    private function parseAlternativeScopeTermination($tokenType)
    {
        $this->consumeToken($tokenType);
        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_SEMICOLON) {
            $this->consumeToken(Tokens::T_SEMICOLON);
        } else {
            $this->parseNonePhpCode();
        }
    }

    /**
     * This method parses multiple expressions and adds them as children to the
     * given <b>$exprList</b> node.
     *
     * @template T of ASTNode
     *
     * @param T $exprList
     *
     * @return T
     *
     * @since 1.0.0
     */
    private function parseExpressionList(ASTNode $exprList)
    {
        $this->consumeComments();

        do {
            $expr = $this->parseOptionalExpression();
        } while ($expr && $this->addChildToList($exprList, $expr));

        return $exprList;
    }

    /**
     * Return true if children remain to be added, false else.
     *
     * @param AbstractASTNode $exprList
     *
     * @return bool
     */
    protected function addChildToList(ASTNode $exprList, ASTNode $expr)
    {
        $exprList->addChild($expr);

        $this->consumeComments();

        if ($this->tokenizer->peek() !== Tokens::T_COMMA) {
            return false;
        }

        if ($exprList instanceof ASTArguments && !$exprList->acceptsMoreArguments()) {
            throw $this->getUnexpectedTokenException();
        }

        $this->consumeToken(Tokens::T_COMMA);
        $this->consumeComments();

        return true;
    }

    /**
     * This method parses an expression node and returns it. When no expression
     * was found this method will throw an InvalidStateException.
     *
     * @throws ParserException
     *
     * @return ASTNode
     *
     * @since 1.0.1
     */
    private function parseExpression()
    {
        if (null === ($expr = $this->parseOptionalExpression())) {
            $token = $this->consumeToken($this->tokenizer->peek());

            throw new InvalidStateException(
                $token->startLine,
                $this->compilationUnit->getFileName(),
                'Mandatory expression expected.'
            );
        }
        return $expr;
    }

    /**
     * This method optionally parses an expression node and returns it. When no
     * expression was found this method will return <b>null</b>.
     *
     * @throws ParserException
     *
     * @return ASTExpression|null
     *
     * @since 0.9.6
     */
    protected function parseOptionalExpression()
    {
        $expressions = array();

        while (($tokenType = $this->tokenizer->peek()) != Tokenizer::T_EOF) {
            $expr = null;

            switch ($tokenType) {
                case Tokens::T_COMMA:
                case Tokens::T_AS:
                case Tokens::T_BREAK:
                case Tokens::T_CLOSE_TAG:
                case Tokens::T_COLON:
                case Tokens::T_CONTINUE:
                case Tokens::T_CURLY_BRACE_CLOSE:
                case Tokens::T_DECLARE:
                case Tokens::T_DO:
                case Tokens::T_DOUBLE_ARROW:
                case Tokens::T_ECHO:
                case Tokens::T_END_HEREDOC:
                case Tokens::T_ENDFOREACH:
                case Tokens::T_FOR:
                case Tokens::T_FOREACH:
                case Tokens::T_GLOBAL:
                case Tokens::T_GOTO:
                case Tokens::T_IF:
                case Tokens::T_PARENTHESIS_CLOSE:
                case Tokens::T_RETURN:
                case Tokens::T_SEMICOLON:
                case Tokens::T_SQUARED_BRACKET_CLOSE:
                case Tokens::T_SWITCH:
                case Tokens::T_TRY:
                case Tokens::T_UNSET:
                case Tokens::T_WHILE:
                    break 2;
                case Tokens::T_THROW:
                    $expressions[] = $this->parseThrowExpression();
                    break;
                case Tokens::T_SELF:
                case Tokens::T_STRING:
                case Tokens::T_PARENT:
                case Tokens::T_STATIC:
                case Tokens::T_DOLLAR:
                case Tokens::T_VARIABLE:
                case Tokens::T_BACKSLASH:
                case Tokens::T_NAMESPACE:
                    $expressions[] = $this->parseVariableOrConstantOrPrimaryPrefix();
                    break;
                case ($this->isArrayStartDelimiter()):
                    $expressions[] = $this->doParseArray();
                    break;
                case Tokens::T_NULL:
                case Tokens::T_TRUE:
                case Tokens::T_FALSE:
                case Tokens::T_LNUMBER:
                case Tokens::T_DNUMBER:
                case Tokens::T_BACKTICK:
                case Tokens::T_DOUBLE_QUOTE:
                case Tokens::T_CONSTANT_ENCAPSED_STRING:
                    $expressions[] = $this->parseLiteralOrString();
                    break;
                case Tokens::T_NEW:
                    $expressions[] = $this->parseAllocationExpression();
                    break;
                case Tokens::T_EVAL:
                    $expressions[] = $this->parseEvalExpression();
                    break;
                case Tokens::T_CLONE:
                    $expressions[] = $this->parseCloneExpression();
                    break;
                case Tokens::T_INSTANCEOF:
                    $expressions[] = $this->parseInstanceOfExpression();
                    break;
                case Tokens::T_ISSET:
                    $expressions[] = $this->parseIssetExpression();
                    break;
                case Tokens::T_LIST:
                case Tokens::T_SQUARED_BRACKET_OPEN:
                    $expressions[] = $this->parseListExpression();
                    break;
                case Tokens::T_QUESTION_MARK:
                    $expressions[] = $this->parseConditionalExpression();
                    break;
                case Tokens::T_BOOLEAN_AND:
                    $expressions[] = $this->parseBooleanAndExpression();
                    break;
                case Tokens::T_BOOLEAN_OR:
                    $expressions[] = $this->parseBooleanOrExpression();
                    break;
                case Tokens::T_LOGICAL_AND:
                    $expressions[] = $this->parseLogicalAndExpression();
                    break;
                case Tokens::T_LOGICAL_OR:
                    $expressions[] = $this->parseLogicalOrExpression();
                    break;
                case Tokens::T_LOGICAL_XOR:
                    $expressions[] = $this->parseLogicalXorExpression();
                    break;
                case Tokens::T_FUNCTION:
                    $expressions[] = $this->parseClosureDeclaration();
                    break;
                case Tokens::T_FN:
                    $expressions[] = $this->parseLambdaFunctionDeclaration();
                    break;
                case Tokens::T_PARENTHESIS_OPEN:
                    $expressions[] = $this->parseParenthesisExpressionOrPrimaryPrefix();
                    break;
                case Tokens::T_EXIT:
                    $expressions[] = $this->parseExitExpression();
                    break;
                case Tokens::T_START_HEREDOC:
                    $expressions[] = $this->parseHeredoc();
                    break;
                case Tokens::T_CURLY_BRACE_OPEN:
                    $expressions[] = $this->parseBraceExpression(
                        $this->builder->buildAstExpression(),
                        $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN),
                        Tokens::T_CURLY_BRACE_CLOSE
                    );
                    break;
                case Tokens::T_INCLUDE:
                    $expressions[] = $this->parseIncludeExpression();
                    break;
                case Tokens::T_INCLUDE_ONCE:
                    $expressions[] = $this->parseIncludeOnceExpression();
                    break;
                case Tokens::T_REQUIRE:
                    $expressions[] = $this->parseRequireExpression();
                    break;
                case Tokens::T_REQUIRE_ONCE:
                    $expressions[] = $this->parseRequireOnceExpression();
                    break;
                case Tokens::T_DEC:
                    $expressions[] = $this->parseDecrementExpression($expressions);
                    break;
                case Tokens::T_INC:
                    $expressions[] = $this->parseIncrementExpression($expressions);
                    break;
                case Tokens::T_SL:
                    $expressions[] = $this->parseShiftLeftExpression();
                    break;
                case Tokens::T_SR:
                    $expressions[] = $this->parseShiftRightExpression();
                    break;
                case Tokens::T_DIR:
                case Tokens::T_FILE:
                case Tokens::T_LINE:
                case Tokens::T_NS_C:
                case Tokens::T_FUNC_C:
                case Tokens::T_CLASS_C:
                case Tokens::T_METHOD_C:
                    $expressions[] = $this->parseConstant();
                    break;
                case Tokens::T_INT_CAST:
                case Tokens::T_BOOL_CAST:
                case Tokens::T_ARRAY_CAST:
                case Tokens::T_UNSET_CAST:
                case Tokens::T_OBJECT_CAST:
                case Tokens::T_DOUBLE_CAST:
                case Tokens::T_STRING_CAST:
                    $expressions[] = $this->parseCastExpression();
                    break;
                case Tokens::T_EQUAL:
                case Tokens::T_OR_EQUAL:
                case Tokens::T_SL_EQUAL:
                case Tokens::T_SR_EQUAL:
                case Tokens::T_AND_EQUAL:
                case Tokens::T_DIV_EQUAL:
                case Tokens::T_MOD_EQUAL:
                case Tokens::T_MUL_EQUAL:
                case Tokens::T_XOR_EQUAL:
                case Tokens::T_PLUS_EQUAL:
                case Tokens::T_MINUS_EQUAL:
                case Tokens::T_CONCAT_EQUAL:
                case Tokens::T_COALESCE_EQUAL:
                    $expressions[] = $this->parseAssignmentExpression(
                        array_pop($expressions)
                    );
                    break;
                // TODO: Handle comments here
                case Tokens::T_COMMENT:
                case Tokens::T_DOC_COMMENT:
                    $this->consumeToken($tokenType);
                    break;
                case Tokens::T_PRINT: // TODO: Implement print expression
                    $token = $this->consumeToken($tokenType);

                    $expr = $this->builder->buildAstPrintExpression();
                    $expr->configureLinesAndColumns(
                        $token->startLine,
                        $token->endLine,
                        $token->startColumn,
                        $token->endColumn
                    );

                    $expressions[] = $expr;
                    break;
                case Tokens::T_ELLIPSIS:
                    $this->checkEllipsisInExpressionSupport();
                    // no break
                case Tokens::T_STRING_VARNAME: // TODO: Implement this
                case Tokens::T_PLUS: // TODO: Make this a arithmetic expression
                case Tokens::T_MINUS:
                case Tokens::T_MUL:
                case Tokens::T_DIV:
                case Tokens::T_MOD:
                case Tokens::T_IS_EQUAL: // TODO: Implement compare expressions
                case Tokens::T_IS_NOT_EQUAL:
                case Tokens::T_IS_IDENTICAL:
                case Tokens::T_IS_NOT_IDENTICAL:
                case Tokens::T_IS_GREATER_OR_EQUAL:
                case Tokens::T_IS_SMALLER_OR_EQUAL:
                case Tokens::T_ANGLE_BRACKET_OPEN:
                case Tokens::T_ANGLE_BRACKET_CLOSE:
                case Tokens::T_EMPTY:
                case Tokens::T_CONCAT:
                case Tokens::T_BITWISE_OR:
                case Tokens::T_BITWISE_AND:
                case Tokens::T_BITWISE_NOT:
                case Tokens::T_BITWISE_XOR:
                    $token = $this->consumeToken($tokenType);

                    $expr = $this->builder->buildAstExpression($token->image);
                    $expr->configureLinesAndColumns(
                        $token->startLine,
                        $token->endLine,
                        $token->startColumn,
                        $token->endColumn
                    );

                    $expressions[] = $expr;
                    break;
                case Tokens::T_AT:
                case Tokens::T_EXCLAMATION_MARK:
                    $token = $this->consumeToken($tokenType);

                    $expr = $this->builder->buildAstUnaryExpression($token->image);
                    $expr->configureLinesAndColumns(
                        $token->startLine,
                        $token->endLine,
                        $token->startColumn,
                        $token->endColumn
                    );

                    $expressions[] = $expr;
                    break;
                case Tokens::T_YIELD:
                    $expressions[] = $this->parseYield();
                    break;
                default:
                    $expressions[] = $this->parseOptionalExpressionForVersion();
                    break;
            }
        }

        $expressions = $this->reduce($expressions);

        $count = count($expressions);
        if ($count == 0) {
            return null;
        } elseif ($count == 1) {
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
     * This method will be called when the base parser cannot handle an expression
     * in the base version. In this method you can implement version specific
     * expressions.
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTNode
     *
     * @since 2.2
     */
    protected function parseOptionalExpressionForVersion()
    {
        throw $this->getUnexpectedTokenException();
    }

    /**
     * Applies all reduce rules against the given expression list.
     *
     * @param ASTExpression[] $expressions Unprepared input array with parsed expression nodes found in the source tree.
     *
     * @return ASTExpression[]
     *
     * @since 0.10.0
     */
    protected function reduce(array $expressions)
    {
        return $this->reduceUnaryExpression($expressions);
    }

    /**
     * Reduces all unary-expressions in the given expression list.
     *
     * @param ASTExpression[] $expressions Unprepared input array with parsed expression nodes found in the source tree.
     *
     * @return ASTExpression[]
     *
     * @since 0.10.0
     */
    private function reduceUnaryExpression(array $expressions)
    {
        for ($i = count($expressions) - 2; $i >= 0; --$i) {
            $expr = $expressions[$i];
            if ($expr instanceof ASTUnaryExpression) {
                $child = $expressions[$i + 1];

                $expr->addChild($child);

                $expr->configureLinesAndColumns(
                    $expr->getStartLine(),
                    $child->getEndLine(),
                    $expr->getStartColumn(),
                    $child->getEndColumn()
                );

                unset($expressions[$i + 1]);
            }
        }
        return array_values($expressions);
    }

    /**
     * This method parses a switch statement.
     *
     * @return ASTSwitchStatement
     *
     * @since 0.9.8
     */
    private function parseSwitchStatement()
    {
        $this->tokenStack->push();
        $this->consumeToken(Tokens::T_SWITCH);

        $switch = $this->builder->buildAstSwitchStatement();
        $switch->addChild($this->parseParenthesisExpression());
        $this->parseSwitchStatementBody($switch);

        return $this->setNodePositionsAndReturn($switch);
    }

    /**
     * Parses the body of a switch statement.
     *
     * @param ASTSwitchStatement $switch The parent switch stmt.
     *
     * @return ASTSwitchStatement
     *
     * @since 0.9.8
     */
    private function parseSwitchStatementBody(ASTSwitchStatement $switch)
    {
        $this->consumeComments();
        if ($this->tokenizer->peek() === Tokens::T_CURLY_BRACE_OPEN) {
            $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);
        } else {
            $this->consumeToken(Tokens::T_COLON);
        }

        while (($tokenType = $this->tokenizer->peek()) !== Tokenizer::T_EOF) {
            switch ($tokenType) {
                case Tokens::T_CLOSE_TAG:
                    $this->parseNonePhpCode();
                    break;
                case Tokens::T_ENDSWITCH:
                    $this->parseAlternativeScopeTermination(Tokens::T_ENDSWITCH);
                    return $switch;
                case Tokens::T_CURLY_BRACE_CLOSE:
                    $this->consumeToken(Tokens::T_CURLY_BRACE_CLOSE);
                    return $switch;
                case Tokens::T_CASE:
                    $switch->addChild($this->parseSwitchLabel());
                    break;
                case Tokens::T_DEFAULT:
                    $switch->addChild($this->parseSwitchLabelDefault());
                    break;
                case Tokens::T_COMMENT:
                case Tokens::T_DOC_COMMENT:
                    $this->consumeToken($tokenType);
                    break;
                default:
                    break 2;
            }
        }

        throw $this->getUnexpectedTokenException();
    }

    /**
     * This method parses a case label of a switch statement.
     *
     * @return ASTSwitchLabel
     *
     * @since 0.9.8
     */
    private function parseSwitchLabel()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_CASE);

        $label = $this->builder->buildAstSwitchLabel($token->image);
        $label->addChild($this->parseExpression());

        if ($this->tokenizer->peek() === Tokens::T_COLON) {
            $this->consumeToken(Tokens::T_COLON);
        } else {
            $this->consumeToken(Tokens::T_SEMICOLON);
        }

        $this->parseSwitchLabelBody($label);

        return $this->setNodePositionsAndReturn($label);
    }

    /**
     * This method parses the default label of a switch statement.
     *
     * @return ASTSwitchLabel
     *
     * @since 0.9.8
     */
    private function parseSwitchLabelDefault()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_DEFAULT);

        $this->consumeComments();
        if ($this->tokenizer->peek() === Tokens::T_COLON) {
            $this->consumeToken(Tokens::T_COLON);
        } else {
            $this->consumeToken(Tokens::T_SEMICOLON);
        }

        $label = $this->builder->buildAstSwitchLabel($token->image);
        $label->setDefault();

        $this->parseSwitchLabelBody($label);

        return $this->setNodePositionsAndReturn($label);
    }

    /**
     * Parses the body of an switch label node.
     *
     * @param ASTSwitchLabel $label The context switch label.
     *
     * @return ASTSwitchLabel
     */
    private function parseSwitchLabelBody(ASTSwitchLabel $label)
    {
        $curlyBraceCount = 0;

        $tokenType = $this->tokenizer->peek();
        while ($tokenType !== Tokenizer::T_EOF) {
            switch ($tokenType) {
                case Tokens::T_CURLY_BRACE_OPEN:
                    $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);
                    ++$curlyBraceCount;
                    break;
                case Tokens::T_CURLY_BRACE_CLOSE:
                    if ($curlyBraceCount === 0) {
                        return $label;
                    }
                    $this->consumeToken(Tokens::T_CURLY_BRACE_CLOSE);
                    --$curlyBraceCount;
                    break;
                case Tokens::T_CLOSE_TAG:
                    $this->parseNonePhpCode();
                    break;
                case Tokens::T_CASE:
                case Tokens::T_DEFAULT:
                case Tokens::T_ENDSWITCH:
                    return $label;
                default:
                    $statement = $this->parseOptionalStatement();
                    if ($statement === null) {
                        $this->consumeToken($tokenType);
                    } elseif ($statement instanceof ASTNode) {
                        $label->addChild($statement);
                    }
                    // TODO: Change the <else if> into and <else> when the ast
                    //       implementation is finished.
                    break;
            }
            $tokenType = $this->tokenizer->peek();
        }
        throw new TokenStreamEndException($this->tokenizer);
    }

    /**
     * Parses the termination token for a statement. This termination token can
     * be a semicolon or a closing php tag.
     *
     * @param int[] $allowedTerminationTokens list of extra token types that can terminate the statement
     *
     * @return void
     *
     * @since 0.9.12
     */
    private function parseStatementTermination(array $allowedTerminationTokens = array())
    {
        $this->consumeComments();

        if (in_array($this->tokenizer->peek(), $allowedTerminationTokens, true)) {
            return;
        }

        if ($this->tokenizer->peek() === Tokens::T_SEMICOLON) {
            $this->consumeToken(Tokens::T_SEMICOLON);
        } else {
            $this->parseNonePhpCode();
        }
    }

    /**
     * This method parses a try-statement + associated catch-statements.
     *
     * @return ASTTryStatement
     *
     * @since 0.9.12
     */
    private function parseTryStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_TRY);

        $stmt = $this->builder->buildAstTryStatement($token->image);
        $stmt->addChild($this->parseRegularScope());

        $this->consumeComments();

        if (false === in_array($this->tokenizer->peek(), array(Tokens::T_CATCH, Tokens::T_FINALLY))) {
            throw $this->getUnexpectedTokenException();
        }

        while ($this->tokenizer->peek() === Tokens::T_CATCH) {
            $stmt->addChild($this->parseCatchStatement());
            $this->consumeComments();
        }

        while ($this->tokenizer->peek() === Tokens::T_FINALLY) {
            $stmt->addChild($this->parseFinallyStatement());
            $this->consumeComments();
        }

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a throw-statement.
     *
     * @param int[] $allowedTerminationTokens list of extra token types that can terminate the statement
     *
     * @return ASTThrowStatement
     *
     * @since 0.9.12
     */
    protected function parseThrowStatement(array $allowedTerminationTokens = array())
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_THROW);

        $stmt = $this->builder->buildAstThrowStatement($token->image);
        $stmt->addChild($this->parseExpression());

        $this->parseStatementTermination($allowedTerminationTokens);

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a goto-statement.
     *
     * @return ASTGotoStatement
     *
     * @since 0.9.12
     */
    private function parseGotoStatement()
    {
        $this->tokenStack->push();

        $this->consumeToken(Tokens::T_GOTO);
        $this->consumeComments();

        $token = $this->consumeToken(Tokens::T_STRING);

        $this->parseStatementTermination();

        $stmt = $this->builder->buildAstGotoStatement($token->image);
        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a label-statement.
     *
     * @return ASTLabelStatement
     *
     * @since 0.9.12
     */
    private function parseLabelStatement()
    {
        $this->tokenStack->push();

        $token = $this->consumeToken(Tokens::T_STRING);
        $this->consumeComments();
        $this->consumeToken(Tokens::T_COLON);

        return $this->setNodePositionsAndReturn(
            $this->builder->buildAstLabelStatement($token->image)
        );
    }

    /**
     * This method parses a global-statement.
     *
     * @return ASTGlobalStatement
     *
     * @since 0.9.12
     */
    private function parseGlobalStatement()
    {
        $this->tokenStack->push();
        $this->consumeToken(Tokens::T_GLOBAL);

        $stmt = $this->builder->buildAstGlobalStatement();
        $stmt = $this->parseVariableList($stmt);

        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a unset-statement.
     *
     * @return ASTUnsetStatement
     *
     * @since 0.9.12
     */
    private function parseUnsetStatement()
    {
        $this->tokenStack->push();

        $this->consumeToken(Tokens::T_UNSET);
        $this->consumeComments();
        $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);
        $this->consumeComments();

        $stmt = $this->builder->buildAstUnsetStatement();
        $stmt = $this->parseVariableList($stmt, true);
        $this->consumeComments();

        $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);

        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a catch-statement.
     *
     * @return ASTCatchStatement
     *
     * @since 0.9.8
     */
    private function parseCatchStatement()
    {
        $this->tokenStack->push();
        $this->consumeComments();

        $token = $this->consumeToken(Tokens::T_CATCH);

        $catch = $this->builder->buildAstCatchStatement($token->image);

        $this->consumeComments();
        $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);

        $this->parseCatchExceptionClass($catch);

        $this->consumeComments();
        $this->parseCatchVariable($catch);

        $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);

        $catch->addChild($this->parseRegularScope());

        return $this->setNodePositionsAndReturn($catch);
    }

    /**
     * This method parses assigned variable in catch statement.
     *
     * @param ASTCatchStatement $stmt The owning catch statement.
     *
     * @return void
     */
    protected function parseCatchVariable(ASTCatchStatement $stmt)
    {
        $stmt->addChild($this->parseVariable());

        $this->consumeComments();
    }

    /**
     * This method parses class references in catch statement.
     *
     * @param ASTCatchStatement $stmt The owning catch statement.
     *
     * @return void
     */
    protected function parseCatchExceptionClass(ASTCatchStatement $stmt)
    {
        $stmt->addChild(
            $this->builder->buildAstClassOrInterfaceReference(
                $this->parseQualifiedName()
            )
        );
    }

    /**
     * This method parses a finally-statement.
     *
     * @return ASTFinallyStatement
     *
     * @since 2.0.0
     */
    private function parseFinallyStatement()
    {
        $this->tokenStack->push();
        $this->consumeComments();

        $token = $this->consumeToken(Tokens::T_FINALLY);

        $finally = $this->builder->buildAstFinallyStatement();
        $finally->addChild($this->parseRegularScope());

        return $this->setNodePositionsAndReturn($finally);
    }

    /**
     * This method parses a single if-statement node.
     *
     * @return ASTIfStatement
     *
     * @since 0.9.8
     */
    private function parseIfStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_IF);

        $stmt = $this->builder->buildAstIfStatement($token->image);
        $stmt->addChild($this->parseParenthesisExpression());

        $this->parseStatementBody($stmt);
        $this->parseOptionalElseOrElseIfStatement($stmt);

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a single elseif-statement node.
     *
     * @return ASTElseIfStatement
     *
     * @since 0.9.8
     */
    private function parseElseIfStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_ELSEIF);

        $stmt = $this->builder->buildAstElseIfStatement($token->image);
        $stmt->addChild($this->parseParenthesisExpression());

        $this->parseStatementBody($stmt);
        $this->parseOptionalElseOrElseIfStatement($stmt);

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses an optional else-, else+if- or elseif-statement.
     *
     * @param ASTStatement $stmt The owning if/elseif statement.
     *
     * @return ASTStatement
     *
     * @since 0.9.12
     */
    private function parseOptionalElseOrElseIfStatement(ASTStatement $stmt)
    {
        $this->consumeComments();
        switch ($this->tokenizer->peek()) {
            case Tokens::T_ELSE:
                $this->consumeToken(Tokens::T_ELSE);
                $this->consumeComments();
                if ($this->tokenizer->peek() === Tokens::T_IF) {
                    $stmt->addChild($this->parseIfStatement());
                } else {
                    $this->parseStatementBody($stmt);
                }
                break;
            case Tokens::T_ELSEIF:
                $stmt->addChild($this->parseElseIfStatement());
                break;
        }

        return $stmt;
    }

    /**
     * This method parses a single for-statement node.
     *
     * @return ASTForStatement
     *
     * @since 0.9.8
     */
    private function parseForStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_FOR);

        $this->consumeComments();
        $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);

        $stmt = $this->builder->buildAstForStatement($token->image);

        if (($init = $this->parseForInit()) !== null) {
            $stmt->addChild($init);
        }
        $this->consumeToken(Tokens::T_SEMICOLON);

        if (($expr = $this->parseForExpression()) !== null) {
            $stmt->addChild($expr);
        }
        $this->consumeToken(Tokens::T_SEMICOLON);

        if (($update = $this->parseForUpdate()) !== null) {
            $stmt->addChild($update);
        }
        $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);

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
     * @return ASTForInit|null
     *
     * @since 0.9.8
     */
    private function parseForInit()
    {
        $this->consumeComments();

        if (Tokens::T_SEMICOLON === $this->tokenizer->peek()) {
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
     * @return ASTExpression|null
     *
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
     * @return ASTForUpdate|null
     *
     * @since 0.9.12
     */
    private function parseForUpdate()
    {
        $this->consumeComments();
        if (Tokens::T_PARENTHESIS_CLOSE === $this->tokenizer->peek()) {
            return null;
        }

        $this->tokenStack->push();

        $update = $this->builder->buildAstForUpdate();
        $this->parseExpressionList($update);

        return $this->setNodePositionsAndReturn($update);
    }

    /**
     * This methods return true if the token matches a list opening in the current PHP version level.
     *
     * @param int $tokenType
     *
     * @return bool
     *
     * @since 2.6.0
     */
    protected function isListUnpacking($tokenType = null)
    {
        return ($tokenType ?: $this->tokenizer->peek()) === Tokens::T_LIST;
    }

    /**
     * Get the parsed list of a foreach statement children.
     *
     * @return ASTNode[]
     */
    private function parseForeachChildren()
    {
        if ($this->tokenizer->peek() === Tokens::T_BITWISE_AND) {
            return array($this->parseVariableOrMemberByReference());
        }

        if ($this->isListUnpacking()) {
            return array($this->parseListExpression());
        }

        $children = array(
            $this->parseVariableOrConstantOrPrimaryPrefix()
        );

        if ($this->tokenizer->peek() === Tokens::T_DOUBLE_ARROW) {
            $this->consumeToken(Tokens::T_DOUBLE_ARROW);

            $children[] = $this->isListUnpacking()
                ? $this->parseListExpression()
                : $this->parseVariableOrMemberOptionalByReference();
        }

        return $children;
    }

    /**
     * This method parses a single foreach-statement node.
     *
     * @return ASTForeachStatement
     *
     * @since 0.9.8
     */
    private function parseForeachStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_FOREACH);

        $foreach = $this->builder->buildAstForeachStatement($token->image);

        $this->consumeComments();
        $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);

        $foreach->addChild($this->parseExpression());

        $this->consumeToken(Tokens::T_AS);
        $this->consumeComments();

        foreach ($this->parseForeachChildren() as $child) {
            $foreach->addChild($child);
        }

        $this->consumeComments();
        $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);

        return $this->setNodePositionsAndReturn(
            $this->parseStatementBody($foreach)
        );
    }

    /**
     * This method parses a single while-statement node.
     *
     * @return ASTWhileStatement
     *
     * @since 0.9.8
     */
    private function parseWhileStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_WHILE);

        $stmt = $this->builder->buildAstWhileStatement($token->image);
        $stmt->addChild($this->parseParenthesisExpression());

        return $this->setNodePositionsAndReturn(
            $this->parseStatementBody($stmt)
        );
    }

    /**
     * This method parses a do/while-statement.
     *
     * @return ASTDoWhileStatement
     *
     * @since 0.9.12
     */
    private function parseDoWhileStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_DO);

        $stmt = $this->builder->buildAstDoWhileStatement($token->image);
        $stmt = $this->parseStatementBody($stmt);

        $this->consumeComments();
        $this->consumeToken(Tokens::T_WHILE);

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
     * @return ASTDeclareStatement
     *
     * @since 0.10.0
     */
    private function parseDeclareStatement()
    {
        $this->tokenStack->push();
        $this->consumeToken(Tokens::T_DECLARE);

        $stmt = $this->builder->buildAstDeclareStatement();
        $stmt = $this->parseDeclareList($stmt);
        $stmt = $this->parseStatementBody($stmt);

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a list of declare values. A declare list value always
     * consists of a string token and a static scalar.
     *
     * @param ASTDeclareStatement $stmt The declare statement that is the owner of this list.
     *
     * @return ASTDeclareStatement
     *
     * @since 0.10.0
     */
    private function parseDeclareList(ASTDeclareStatement $stmt)
    {
        $this->consumeComments();
        $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);

        while (true) {
            $this->consumeComments();
            $name = $this->consumeToken(Tokens::T_STRING)->image;

            $this->consumeComments();
            $this->consumeToken(Tokens::T_EQUAL);

            $this->consumeComments();
            $value = $this->parseStaticValue();

            $stmt->addValue($name, $value);

            $this->consumeComments();
            if ($this->tokenizer->peek() === Tokens::T_COMMA) {
                $this->consumeToken(Tokens::T_COMMA);
                continue;
            }
            break;
        }

        $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);
        return $stmt;
    }

    /**
     * This method builds a return statement from a given token.
     *
     * @return ASTReturnStatement
     *
     * @since 2.7.0
     */
    protected function buildReturnStatement(Token $token)
    {
        $stmt = $this->builder->buildAstReturnStatement($token->image);

        if (($expr = $this->parseOptionalExpression()) != null) {
            $stmt->addChild($expr);
        }

        return $stmt;
    }

    /**
     * This method parses a single return-statement node.
     *
     * @return ASTReturnStatement
     *
     * @since 0.9.12
     */
    private function parseReturnStatement()
    {
        $this->tokenStack->push();

        $stmt = $this->buildReturnStatement(
            $this->consumeToken(Tokens::T_RETURN)
        );

        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a break-statement node.
     *
     * @return ASTBreakStatement
     *
     * @since 0.9.12
     */
    private function parseBreakStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_BREAK);

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
     * @return ASTContinueStatement
     *
     * @since 0.9.12
     */
    private function parseContinueStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_CONTINUE);

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
     * @return ASTEchoStatement
     *
     * @since 0.9.12
     */
    private function parseEchoStatement()
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_ECHO);

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
     * @return ASTNode
     *
     * @since 1.0.0
     */
    protected function parseParenthesisExpressionOrPrimaryPrefix()
    {
        return $this->parseParenthesisExpressionOrPrimaryPrefixForVersion(
            $this->parseParenthesisExpression()
        );
    }

    /**
     * @template T of ASTExpression
     *
     * @param T $expr
     *
     * @return ASTMemberPrimaryPrefix|T
     */
    protected function parseParenthesisExpressionOrPrimaryPrefixForVersion(ASTExpression $expr)
    {
        $this->consumeComments();

        if ($this->isNextTokenObjectOperator()) {
            return $this->parseMemberPrimaryPrefix($expr->getChild(0));
        }

        return $expr;
    }

    /**
     * @eturn bool
     */
    protected function isNextTokenObjectOperator()
    {
        return $this->tokenizer->peek() === Tokens::T_OBJECT_OPERATOR;
    }

    /**
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     *
     * @return Token
     */
    protected function consumeObjectOperatorToken()
    {
        return $this->consumeToken(Tokens::T_OBJECT_OPERATOR);
    }

    /**
     * Parses any expression that is surrounded by an opening and a closing
     * parenthesis
     *
     * @return ASTExpression
     *
     * @since 0.9.8
     */
    protected function parseParenthesisExpression()
    {
        $this->tokenStack->push();
        $this->consumeComments();

        $expr = $this->builder->buildAstExpression();
        $expr = $this->parseBraceExpression(
            $expr,
            $this->consumeToken(Tokens::T_PARENTHESIS_OPEN),
            Tokens::T_PARENTHESIS_CLOSE
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
     * @throws ParserException
     *
     * @return ASTNode
     *
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
            case Tokens::T_DOUBLE_COLON:
                $node = $this->builder->buildAstClassOrInterfaceReference($qName);
                $node = $this->setNodePositionsAndReturn($node);
                $node = $this->parseStaticMemberPrimaryPrefix($node);
                break;
            case Tokens::T_PARENTHESIS_OPEN:
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
     * wrap the given <b>$node</b> with a {@link ASTFunctionPostfix}
     * node.
     *
     * @template T of ASTNode
     *
     * @param T $node The previously parsed node.
     *
     * @return ASTFunctionPostfix|T The original input node or this node wrapped with a function postfix instance.
     *
     * @since 1.0.0
     */
    private function parseOptionalFunctionPostfix(ASTNode $node)
    {
        $this->consumeComments();
        if (Tokens::T_PARENTHESIS_OPEN === $this->tokenizer->peek()) {
            return $this->parseFunctionPostfix($node);
        }
        return $node;
    }

    /**
     * This method parses a function postfix expression. An object of type
     * {@link ASTFunctionPostfix} represents any valid php
     * function call.
     *
     * This method will delegate the call to another method that returns a
     * member primary prefix object when the function postfix expression is
     * followed by an object operator.
     *
     * @param ASTNode $node This node represents the function identifier. An identifier can be a static string,
     *                      a variable, a compound variable or any other valid php function identifier.
     *
     * @throws ParserException
     *
     * @return ASTFunctionPostfix
     *
     * @since 0.9.6
     */
    protected function parseFunctionPostfix(ASTNode $node)
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
     * @return ASTNode
     *
     * @since 1.0.0
     */
    protected function parsePostfixIdentifier()
    {
        switch ($this->tokenizer->peek()) {
            case Tokens::T_STRING:
                $node = $this->parseLiteral();
                break;
            default:
                $node = $this->parseCompoundVariableOrVariableVariableOrVariable();
                break;
        }
        return $this->parseOptionalIndexExpression($node);
    }

    /**
     * This method parses an optional member primary expression. It will parse
     * the primary expression when an object operator can be found at the actual
     * token stream position. Otherwise this method simply returns the input
     * {@link ASTNode} instance.
     *
     * @template T of ASTNode
     *
     * @param T $node This node represents primary prefix
     *                left expression. It will be the first child of the parsed member
     *                primary expression.
     *
     * @throws ParserException
     *
     * @return ASTMemberPrimaryPrefix|T
     *
     * @since 0.9.6
     */
    protected function parseOptionalMemberPrimaryPrefix(ASTNode $node)
    {
        $this->consumeComments();

        if ($this->isNextTokenObjectOperator()) {
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
     * @param ASTNode $node The left node in the parsed member primary expression.
     *
     * @throws ParserException
     *
     * @return ASTMemberPrimaryPrefix
     *
     * @since 0.9.6
     */
    protected function parseMemberPrimaryPrefix(ASTNode $node)
    {
        // Consume double colon and optional comments
        $token = $this->consumeObjectOperatorToken();

        $prefix = $this->builder->buildAstMemberPrimaryPrefix($token->image);
        $prefix->addChild($node);

        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {
            case ($this->isMethodName($tokenType)):
                $child = $this->parseIdentifier($tokenType);
                $child = $this->parseOptionalIndexExpression($child);

                // TODO: Move this in a separate method
                if ($child instanceof ASTIndexExpression) {
                    $this->consumeComments();
                    if (Tokens::T_PARENTHESIS_OPEN === $this->tokenizer->peek()) {
                        $prefix->addChild($this->parsePropertyPostfix($child));
                        return $this->parseOptionalFunctionPostfix($prefix);
                    }
                }
                break;
            case Tokens::T_CURLY_BRACE_OPEN:
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
     * input {@link ASTNode} instance.
     *
     * @param ASTNode $node This node represents primary prefix left expression. It will
     *                      be the first child of the parsed member primary expression.
     *
     * @throws ParserException
     *
     * @return ASTNode
     *
     * @since 1.0.1
     */
    private function parseOptionalStaticMemberPrimaryPrefix(ASTNode $node)
    {
        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_DOUBLE_COLON) {
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
     * @param ASTNode $node The left node in the parsed member primary expression.
     *
     * @throws ParserException
     *
     * @return ASTMemberPrimaryPrefix
     *
     * @since 0.9.6
     */
    protected function parseStaticMemberPrimaryPrefix(ASTNode $node)
    {
        $token = $this->consumeToken(Tokens::T_DOUBLE_COLON);

        $prefix = $this->builder->buildAstMemberPrimaryPrefix($token->image);
        $prefix->addChild($node);

        $this->consumeComments();

        switch ($this->tokenizer->peek()) {
            case Tokens::T_STRING:
                $postfix = $this->parseMethodOrConstantPostfix();
                break;
            case Tokens::T_CLASS_FQN:
                $postfix = $this->parseFullQualifiedClassNamePostfix();
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
     * @throws ParserException
     *
     * @return ASTNode
     *
     * @since 0.9.6
     */
    private function parseMethodOrConstantPostfix()
    {
        $this->tokenStack->push();

        $node = $this->parseIdentifier();

        $this->consumeComments();
        if ($this->tokenizer->peek() === Tokens::T_PARENTHESIS_OPEN) {
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
     * @param ASTNode $node The identifier for the parsed postfix expression node. This node
     *                      will be the first child of the returned postfix node instance.
     *
     * @throws ParserException
     *
     * @return ASTNode
     *
     * @since 0.9.6
     */
    private function parseMethodOrPropertyPostfix(ASTNode $node)
    {
        // Strip optional comments
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {
            case Tokens::T_PARENTHESIS_OPEN:
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
     * @param ASTNode $node Node that represents the image of the property postfix node.
     *
     * @return ASTPropertyPostfix
     *
     * @since 0.10.2
     */
    private function parsePropertyPostfix(ASTNode $node)
    {
        $image = $this->extractPostfixImage($node);

        $postfix = $this->builder->buildAstPropertyPostfix($image);
        $postfix->addChild($node);

        $postfix->configureLinesAndColumns(
            $node->getStartLine(),
            $node->getEndLine(),
            $node->getStartColumn(),
            $node->getEndColumn()
        );

        return $postfix;
    }

    /**
     * Parses a full qualified class name postfix.
     *
     * @return ASTClassFqnPostfix
     *
     * @since 2.0.0
     */
    protected function parseFullQualifiedClassNamePostfix()
    {
        throw $this->getUnexpectedTokenException();
    }

    /**
     * This method will extract the image/name of the real property/variable
     * that is wrapped by {@link ASTIndexExpression} nodes. If
     * the given node is now wrapped by index expressions, this method will
     * return the image of the entire <b>$node</b>.
     *
     * @param ASTNode $node The context node that may be wrapped by multiple array or string index expressions.
     *
     * @return string
     *
     * @since 1.0.0
     */
    protected function extractPostfixImage(ASTNode $node)
    {
        while ($node instanceof ASTIndexExpression) {
            $node = $node->getChild(0);
        }
        return $node->getImage();
    }

    /**
     * Parses a method postfix node instance.
     *
     * @param ASTNode $node Node that represents the image of the method postfix node.
     *
     * @return ASTMethodPostfix
     *
     * @since 1.0.0
     */
    private function parseMethodPostfix(ASTNode $node)
    {
        $args  = $this->parseArguments();
        $image = $this->extractPostfixImage($node);

        $postfix = $this->builder->buildAstMethodPostfix($image);
        $postfix->addChild($node);
        $postfix->addChild($args);

        $postfix->configureLinesAndColumns(
            $node->getStartLine(),
            $args->getEndLine(),
            $node->getStartColumn(),
            $args->getEndColumn()
        );

        return $this->parseOptionalMemberPrimaryPrefix($postfix);
    }

    /**
     * This method parses the arguments passed to a function- or method-call.
     *
     * @throws ParserException
     *
     * @return ASTArguments
     *
     * @since 0.9.6
     */
    protected function parseArguments()
    {
        $this->consumeComments();

        $this->tokenStack->push();

        return $this->parseArgumentsParenthesesContent(
            $this->builder->buildAstArguments()
        );
    }

    /**
     * This method parses the tokens after arguments passed to a function- or method-call.
     *
     * @throws ParserException
     *
     * @return ASTArguments
     *
     * @since 0.9.6
     */
    protected function parseArgumentsParenthesesContent(ASTArguments $arguments)
    {
        $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);
        $this->consumeComments();

        if (Tokens::T_PARENTHESIS_CLOSE !== $this->tokenizer->peek()) {
            $arguments = $this->parseArgumentList($arguments);
        }

        $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);

        return $this->setNodePositionsAndReturn($arguments);
    }

    /**
     * @template T of ASTArguments
     *
     * @param T $arguments
     *
     * @return T
     */
    protected function parseArgumentList(ASTArguments $arguments)
    {
        return $this->parseExpressionList($arguments);
    }

    /**
     * This method implements the parsing for various expression types like
     * variables, object/static method. All these expressions are valid in
     * several php language constructs like, isset, empty, unset etc.
     *
     * @return ASTNode
     *
     * @since 0.9.12
     */
    protected function parseVariableOrConstantOrPrimaryPrefix()
    {
        $this->consumeComments();
        switch ($this->tokenizer->peek()) {
            case Tokens::T_DOLLAR:
            case Tokens::T_VARIABLE:
                return $this->parseVariableOrFunctionPostfixOrMemberPrimaryPrefix();
            case Tokens::T_SELF:
                return $this->parseConstantOrSelfMemberPrimaryPrefix();
            case Tokens::T_PARENT:
                return $this->parseConstantOrParentMemberPrimaryPrefix();
            case Tokens::T_STATIC:
                return $this->parseStaticVariableDeclarationOrMemberPrimaryPrefix();
            case Tokens::T_STRING:
            case Tokens::T_BACKSLASH:
            case Tokens::T_NAMESPACE:
                return $this->parseMemberPrefixOrFunctionPostfix();
        }

        throw $this->getUnexpectedTokenException();
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
     * @throws ParserException
     *
     * @return ASTClassOrInterfaceReference|ASTExpression
     *
     * @since 0.9.6
     */
    private function parseVariableOrFunctionPostfixOrMemberPrimaryPrefix()
    {
        $this->tokenStack->push();

        $variable = $this->parseCompoundVariableOrVariableVariableOrVariable();
        $variable = $this->parseOptionalIndexExpression($variable);

        $this->consumeComments();
        switch ($this->tokenizer->peek()) {
            case Tokens::T_DOUBLE_COLON:
                $result = $this->parseStaticMemberPrimaryPrefix($variable);
                break;
            case Tokens::T_NULLSAFE_OBJECT_OPERATOR:
            case Tokens::T_OBJECT_OPERATOR:
                $result = $this->parseMemberPrimaryPrefix($variable);
                break;
            case Tokens::T_PARENTHESIS_OPEN:
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
     * @param ASTNode $left The left part of the assignment expression that will be parsed by this method.
     *
     * @return ASTAssignmentExpression
     *
     * @since 0.9.12
     */
    protected function parseAssignmentExpression(ASTNode $left)
    {
        $token = $this->consumeToken($this->tokenizer->peek());

        $node = $this->builder->buildAstAssignmentExpression($token->image);
        $node->addChild($left);

        // TODO: Change this into a mandatory expression in later versions
        if (($expr = $this->parseOptionalExpression()) != null) {
            $node->addChild($expr);
        } else {
            $expr = $left;
        }

        $node->configureLinesAndColumns(
            $left->getStartLine(),
            $expr->getEndLine(),
            $left->getStartColumn(),
            $expr->getEndColumn()
        );

        return $node;
    }

    /**
     * This method parses a {@link ASTStaticReference} node.
     *
     * @param Token $token The "static" keyword token.
     *
     * @throws ParserException
     * @throws InvalidStateException
     *
     * @return ASTStaticReference
     *
     * @since 0.9.6
     */
    private function parseStaticReference(Token $token)
    {
        // Strip optional comments
        $this->consumeComments();

        if ($this->classOrInterface === null) {
            throw new InvalidStateException(
                $token->startLine,
                (string) $this->compilationUnit,
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
     * This method parses a {@link ASTSelfReference} node.
     *
     * @param Token $token The "self" keyword token.
     *
     * @throws ParserException
     * @throws InvalidStateException
     *
     * @return ASTSelfReference
     *
     * @since 0.9.6
     */
    protected function parseSelfReference(Token $token)
    {
        if ($this->classOrInterface === null) {
            throw new InvalidStateException(
                $token->startLine,
                (string) $this->compilationUnit,
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
     * @return ASTNode|null
     *
     * @since 1.0.0
     */
    protected function parseConstant()
    {
        $this->tokenStack->push();
        switch ($type = $this->tokenizer->peek()) {
            case Tokens::T_STRING:
                // TODO: Separate node classes for magic constants
            case Tokens::T_DIR:
            case Tokens::T_FILE:
            case Tokens::T_LINE:
            case Tokens::T_NS_C:
            case Tokens::T_FUNC_C:
            case Tokens::T_CLASS_C:
            case Tokens::T_METHOD_C:
            case Tokens::T_TRAIT_C:
                $token = $this->consumeToken($type);

                return $this->setNodePositionsAndReturn(
                    $this->builder->buildAstConstant($token->image)
                );
        }

        return null;
    }

    /**
     * This method parses a {@link ASTConstant} node or an instance of
     * {@link ASTSelfReference} as part of a {@link ASTMemberPrimaryPrefix} that
     * contains the self reference as its first child when the self token is
     * followed by a double colon token.
     *
     * @throws ParserException
     * @throws InvalidStateException
     *
     * @return ASTNode
     *
     * @since 0.9.6
     */
    private function parseConstantOrSelfMemberPrimaryPrefix()
    {
        // Read self token and strip optional comments
        $token = $this->consumeToken(Tokens::T_SELF);
        $this->consumeComments();

        if ($this->tokenizer->peek() == Tokens::T_DOUBLE_COLON) {
            return $this->parseStaticMemberPrimaryPrefix(
                $this->parseSelfReference($token)
            );
        }

        return $this->builder->buildAstConstant($token->image);
    }

    /**
     * This method parses a {@link ASTParentReference} node.
     *
     * @param Token $token The "self" keyword token.
     *
     * @throws ParserException
     * @throws InvalidStateException
     *
     * @return ASTParentReference
     *
     * @since 0.9.6
     */
    private function parseParentReference(Token $token)
    {
        if ($this->classOrInterface === null) {
            throw new InvalidStateException(
                $token->startLine,
                (string) $this->compilationUnit,
                'The keyword "parent" was used as type hint but the parameter ' .
                'declaration is not in a class scope.'
            );
        }

        $classReference = $this->classOrInterface instanceof ASTTrait
            ? $this->builder->buildAstClassReference('__PDepend_TraitRuntimeReference')
            : $this->classOrInterface->getParentClassReference();

        if ($classReference === null) {
            throw new InvalidStateException(
                $token->startLine,
                (string) $this->compilationUnit,
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
     * This method parses a {@link ASTConstant} node or an instance of
     * {@link ASTParentReference} as part of a {@link ASTMemberPrimaryPrefix}
     * that contains the parent reference as its first child when the self token
     * is followed by a double colon token.
     *
     * @throws ParserException
     * @throws InvalidStateException
     *
     * @return ASTNode
     *
     * @since 0.9.6
     */
    private function parseConstantOrParentMemberPrimaryPrefix()
    {
        // Consume parent token and strip optional comments
        $token = $this->consumeToken(Tokens::T_PARENT);
        $this->consumeComments();

        if ($this->tokenizer->peek() == Tokens::T_DOUBLE_COLON) {
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
     * @return ASTNode
     *
     * @since 0.9.18
     */
    private function parseVariableOrMemberOptionalByReference()
    {
        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_BITWISE_AND) {
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
     * @return ASTUnaryExpression
     *
     * @since 0.9.18
     */
    private function parseVariableOrMemberByReference()
    {
        $this->tokenStack->push();

        $token = $this->consumeToken(Tokens::T_BITWISE_AND);
        $this->consumeComments();

        $expr = $this->builder->buildAstUnaryExpression($token->image);
        $expr->addChild($this->parseVariableOrConstantOrPrimaryPrefix());

        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * This method parses a simple PHP variable.
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTVariable
     *
     * @since 0.9.6
     */
    private function parseVariable()
    {
        $token = $this->consumeToken(Tokens::T_VARIABLE);

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
     * @template T of AbstractASTNode
     *
     * @param T $node The context parent node.
     *
     * @return T The prepared entire node.
     *
     * @since 0.9.12
     */
    private function parseVariableList(ASTNode $node, $inCall = false)
    {
        $this->consumeComments();
        while ($this->tokenizer->peek() !== Tokenizer::T_EOF) {
            $node->addChild($this->parseVariableOrConstantOrPrimaryPrefix());

            $this->consumeComments();

            if ($this->tokenizer->peek() === Tokens::T_COMMA) {
                $this->consumeToken(Tokens::T_COMMA);
                $this->consumeComments();

                if ($inCall &&
                    $this->allowTrailingCommaInSpecialFunctions() &&
                    $this->tokenizer->peek() === Tokens::T_PARENTHESIS_CLOSE
                ) {
                    break;
                }
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
     * @throws ParserException
     * @throws UnexpectedTokenException
     *
     * @return ASTExpression
     *
     * @since 0.9.6
     */
    protected function parseCompoundVariableOrVariableVariableOrVariable()
    {
        if ($this->tokenizer->peek() == Tokens::T_DOLLAR) {
            return $this->parseCompoundVariableOrVariableVariable();
        }
        return $this->parseVariable();
    }

    /**
     * Parses a PHP compound variable or a simple literal node.
     *
     * @return ASTNode
     *
     * @since 0.9.19
     */
    private function parseCompoundVariableOrLiteral()
    {
        $this->tokenStack->push();

        // Read the dollar token
        $token = $this->consumeToken(Tokens::T_DOLLAR);
        $this->consumeComments();

        // Get next token type
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {
            case Tokens::T_CURLY_BRACE_OPEN:
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
     * @throws ParserException
     * @throws UnexpectedTokenException
     *
     * @return ASTExpression
     *
     * @since 0.9.6
     */
    private function parseCompoundVariableOrVariableVariable()
    {
        $this->tokenStack->push();

        // Read the dollar token
        $token = $this->consumeToken(Tokens::T_DOLLAR);
        $this->consumeComments();

        // Get next token type
        $tokenType = $this->tokenizer->peek();

        // T_DOLLAR|T_VARIABLE === Variable variable,
        // T_CURLY_BRACE_OPEN === Compound variable
        switch ($tokenType) {
            case Tokens::T_DOLLAR:
            case Tokens::T_VARIABLE:
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
     * @param Token $token The dollar token.
     *
     * @return ASTCompoundVariable
     *
     * @since 0.10.0
     */
    private function parseCompoundVariable(Token $token)
    {
        return $this->parseBraceExpression(
            $this->builder->buildAstCompoundVariable($token->image),
            $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN),
            Tokens::T_CURLY_BRACE_CLOSE
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
     * @return ASTNode
     *
     * @since 0.9.10
     */
    private function parseCompoundExpressionOrLiteral()
    {
        $token = $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {
            case Tokens::T_DOLLAR:
            case Tokens::T_VARIABLE:
                return $this->parseBraceExpression(
                    $this->builder->buildAstCompoundExpression(),
                    $token,
                    Tokens::T_CURLY_BRACE_CLOSE
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
     * @throws ParserException
     * @throws ParserException
     *
     * @return ASTCompoundExpression
     *
     * @since 0.9.6
     */
    protected function parseCompoundExpression()
    {
        $this->consumeComments();

        return $this->parseBraceExpression(
            $this->builder->buildAstCompoundExpression(),
            $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN),
            Tokens::T_CURLY_BRACE_CLOSE
        );
    }

    /**
     * Parses a static identifier expression, as it is used for method and
     * function names.
     *
     * @param int $tokenType
     *
     * @return ASTIdentifier
     *
     * @since 0.9.12
     */
    protected function parseIdentifier($tokenType = Tokens::T_STRING)
    {
        $token = $this->consumeToken($tokenType);

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
     * This method parses a {@link ASTLiteral} node or an
     * instance of {@link ASTString} that represents a string
     * in double quotes or surrounded by backticks.
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTNode
     */
    protected function parseLiteralOrString()
    {
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {
            case Tokens::T_NULL:
            case Tokens::T_TRUE:
            case Tokens::T_FALSE:
            case Tokens::T_DNUMBER:
            case Tokens::T_CONSTANT_ENCAPSED_STRING:
                $token = $this->consumeToken($tokenType);

                $literal = $this->builder->buildAstLiteral($token->image);
                $literal->configureLinesAndColumns(
                    $token->startLine,
                    $token->endLine,
                    $token->startColumn,
                    $token->endColumn
                );
                return $literal;
            case Tokens::T_LNUMBER:
                return $this->parseIntegerNumber();
            default:
                return $this->parseString($tokenType);
        }
    }

    /**
     * Parses an integer value.
     *
     * @return ASTLiteral
     *
     * @since 1.0.0
     */
    protected function parseIntegerNumber()
    {
        $token = $this->consumeToken(Tokens::T_LNUMBER);

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
     * Parses an array structure.
     *
     * @param bool $static
     *
     * @return ASTArray
     *
     * @since 1.0.0
     */
    protected function doParseArray($static = false)
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
     * @return bool
     *
     * @since 1.0.0
     */
    abstract protected function isArrayStartDelimiter();

    /**
     * Parses a php array declaration.
     *
     * @param bool $static
     *
     * @return ASTArray
     *
     * @since 1.0.0
     */
    abstract protected function parseArray(ASTArray $array, $static = false);

    /**
     * Return true if [, $foo] or [$foo, , $bar] is allowed.
     *
     * @return bool
     */
    protected function canHaveCommaBetweenArrayElements()
    {
        return false;
    }

    /**
     * Parses all elements in an array.
     *
     * @param int  $endDelimiter
     * @param bool $static
     *
     * @return ASTArray
     *
     * @since 1.0.0
     */
    protected function parseArrayElements(ASTArray $array, $endDelimiter, $static = false)
    {
        $consecutiveComma = null;
        $openingToken = $this->tokenizer->prevToken();
        $useSquaredBrackets = ($endDelimiter === Tokens::T_SQUARED_BRACKET_CLOSE);
        $this->consumeComments();

        while ($endDelimiter !== $this->tokenizer->peek()) {
            while ($this->canHaveCommaBetweenArrayElements() && Tokens::T_COMMA === $this->tokenizer->peek()) {
                $this->consumeToken(Tokens::T_COMMA);
                $this->consumeComments();
            }

            $array->addChild($this->parseArrayElement($static));

            $this->consumeComments();

            if (Tokens::T_COMMA === $this->tokenizer->peek()) {
                $this->consumeToken(Tokens::T_COMMA);
                $this->consumeComments();
            }

            if ($useSquaredBrackets && $this->isListUnpacking(Tokens::T_SQUARED_BRACKET_OPEN)) {
                while (Tokens::T_COMMA === $this->tokenizer->peek()) {
                    $consecutiveComma = $this->tokenizer->prevToken();
                    $this->consumeToken(Tokens::T_COMMA);
                    $this->consumeComments();
                }
            }
        }

        // Once we parsed the whole array, detect if it's a destructuring list or a value,
        // then check the content is consistent
        $this->ensureArrayIsValid($useSquaredBrackets, $openingToken, $consecutiveComma);

        return $array;
    }

    /**
     * Check if the given array/list is a value and so does not have consecutive commas in it,
     * or if it's a destructuring list and so check the syntax is valid in the current PHP level.
     *
     * @param bool       $useSquaredBrackets
     * @param Token|null $openingToken
     * @param Token|null $consecutiveComma
     *
     * @throws UnexpectedTokenException
     *
     * @return void
     */
    protected function ensureArrayIsValid($useSquaredBrackets, $openingToken, $consecutiveComma)
    {
        // If this array is followed by =, it's in fact a destructuring list
        if ($this->tokenizer->peekNext() === Tokens::T_EQUAL) {
            // If it uses [], check the PHP level allow it
            if ($useSquaredBrackets) {
                $this->ensureTokenIsListUnpackingOpening(Tokens::T_SQUARED_BRACKET_OPEN, $openingToken);
            }
        } elseif ($consecutiveComma) {
            // If it's not a destructuring list, it must not contain 2 consecutive commas
            throw $this->getUnexpectedTokenException($consecutiveComma);
        }
    }

    /**
     * Parses a single match key.
     *
     * @return ASTNode
     *
     * @since 2.9.0
     */
    protected function parseMatchEntryKey()
    {
        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_DEFAULT) {
            $this->consumeToken(Tokens::T_DEFAULT);
            $label = $this->builder->buildAstSwitchLabel('default');
            $label->setDefault();

            return $label;
        }

        if ($this->isKeyword($this->tokenizer->peek())) {
            throw $this->getUnexpectedTokenException();
        }

        return $this->parseExpression();
    }

    /**
     * Parses a single match value expression.
     *
     * @return ASTNode
     *
     * @since 2.9.0
     */
    protected function parseMatchEntryValue()
    {
        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_THROW) {
            return $this->parseThrowStatement(array(Tokens::T_COMMA, Tokens::T_CURLY_BRACE_CLOSE));
        }

        return $this->parseExpression();
    }

    /**
     * Parses a single match entry key-expression pair.
     *
     * @return ASTMatchEntry
     *
     * @since 2.9.0
     */
    protected function parseMatchEntry()
    {
        $this->consumeComments();

        $this->tokenStack->push();

        $matchEntry = $this->builder->buildAstMatchEntry();

        do {
            $matchEntry->addChild($this->parseMatchEntryKey());
            $this->consumeComments();

            if ($this->tokenizer->peek() === Tokens::T_COMMA) {
                $this->consumeToken(Tokens::T_COMMA);
                $this->consumeComments();
            }

            $type = $this->tokenizer->peek();
        } while ($type != Tokens::T_DOUBLE_ARROW);

        $this->consumeComments();
        $this->consumeToken(Tokens::T_DOUBLE_ARROW);
        $this->consumeComments();

        $matchEntry->addChild($this->parseMatchEntryValue());

        return $this->setNodePositionsAndReturn($matchEntry);
    }

    /**
     * Parses a single array element.
     *
     * An array element can have a simple value, a key/value pair, a value by
     * reference or a key/value pair with a referenced value.
     *
     * @param bool $static
     *
     * @return ASTArrayElement
     *
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

                throw $this->getUnexpectedTokenException(end($tokens));
            }

            $element->setByReference();
        }

        $this->consumeComments();
        if ($this->isKeyword($this->tokenizer->peek())) {
            throw $this->getUnexpectedTokenException();
        }

        $element->addChild($this->parseExpression());

        $this->consumeComments();
        if (Tokens::T_DOUBLE_ARROW === $this->tokenizer->peek()) {
            $this->consumeToken(Tokens::T_DOUBLE_ARROW);
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
     * @return ASTHeredoc
     *
     * @since 0.9.12
     */
    protected function parseHeredoc()
    {
        $this->tokenStack->push();
        $this->consumeToken(Tokens::T_START_HEREDOC);

        $heredoc = $this->builder->buildAstHeredoc();
        $this->parseStringExpressions($heredoc, Tokens::T_END_HEREDOC);

        $token = $this->consumeToken(Tokens::T_END_HEREDOC);
        $heredoc->setDelimiter($token->image);

        return $this->setNodePositionsAndReturn($heredoc);
    }

    /**
     * Parses a simple string sequence between two tokens of the same type.
     *
     * @param int $tokenType The start/stop token type.
     *
     * @return string
     *
     * @since 0.9.10
     */
    private function parseStringSequence($tokenType)
    {
        $type   = $tokenType;
        $string = '';

        do {
            $string .= $this->consumeToken($type)->image;
            $type    = $this->tokenizer->peek();
        } while ($type != $tokenType && $type != Tokenizer::T_EOF);

        return $string . $this->consumeToken($tokenType)->image;
    }

    /**
     * This method parses a php string with all possible embedded expressions.
     *
     * <code>
     * $string = "Manuel $Pichler <{$email}>";
     *
     * // ASTSTring
     * // |-- ASTLiteral             -  "Manuel ")
     * // |-- ASTVariable            -  $Pichler
     * // |-- ASTLiteral             -  " <"
     * // |-- ASTCompoundExpression  -  {...}
     * // |   |-- ASTVariable        -  $email
     * // |-- ASTLiteral             -  ">"
     * </code>
     *
     * @param int $delimiterType The start/stop token type.
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTString
     *
     * @since 0.9.10
     */
    private function parseString($delimiterType)
    {
        $token = $this->consumeToken($delimiterType);

        $string = $this->builder->buildAstString();
        $startLine = $token->startLine;
        $startColumn = $token->startColumn;

        $this->parseStringExpressions($string, $delimiterType);

        $token = $this->consumeToken($delimiterType);
        $endLine = $token->endLine;
        $endColumn = $token->endColumn;

        $string->configureLinesAndColumns(
            $startLine,
            $endLine,
            $startColumn,
            $endColumn
        );

        return $string;
    }

    /**
     * This method parses the contents of a string or here-/now-doc node. It
     * will not consume the given stop token, so it is up to the calling method
     * to consume the stop token. The return value of this method is the prepared
     * input string node.
     * 
     * @param AbstractASTNode $node
     * @param int             $stopToken
     *
     * @return ASTNode
     *
     * @since 0.9.12
     */
    private function parseStringExpressions(ASTNode $node, $stopToken)
    {
        while (($tokenType = $this->tokenizer->peek()) != Tokenizer::T_EOF) {
            switch ($tokenType) {
                case $stopToken:
                    break 2;
                case Tokens::T_BACKSLASH:
                    $node->addChild($this->parseEscapedAstLiteralString());
                    break;
                case Tokens::T_DOLLAR:
                    $node->addChild($this->parseCompoundVariableOrLiteral());
                    break;
                case Tokens::T_VARIABLE:
                    $node->addChild($this->parseVariable());
                    break;
                case Tokens::T_CURLY_BRACE_OPEN:
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
     * @return ASTLiteral
     *
     * @since 0.9.10
     */
    private function parseEscapedAstLiteralString()
    {
        $this->tokenStack->push();

        $image  = $this->consumeToken(Tokens::T_BACKSLASH)->image;
        $escape = true;

        $tokenType = $this->tokenizer->peek();
        while ($tokenType != Tokenizer::T_EOF) {
            if ($tokenType === Tokens::T_BACKSLASH) {
                $escape = !$escape;
                $image  .= $this->consumeToken(Tokens::T_BACKSLASH)->image;

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
     * @return ASTLiteral
     *
     * @since 0.9.10
     */
    protected function parseLiteral()
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
     * This method parse a formal parameter and all the stuff that may be allowed
     * before it according to the PHP level (type hint, passing by reference, property promotion).
     *
     * @param ASTCallable $callable the callable object (closure, function or method)
     *                              requiring the given parameters list.
     *
     * @return ASTFormalParameter|ASTNode
     */
    protected function parseFormalParameterOrPrefix(ASTCallable $callable)
    {
        return $this->parseFormalParameterOrTypeHintOrByReference();
    }

    /**
     * Extracts all dependencies from a callable signature.
     *
     * @param ASTCallable $callable the callable object (closure, function or method)
     *                              requiring the given parameters list.
     *
     * @return ASTFormalParameters
     *
     * @since 0.9.5
     */
    protected function parseFormalParameters(ASTCallable $callable)
    {
        $this->consumeComments();

        $this->tokenStack->push();

        $formalParameters = $this->builder->buildAstFormalParameters();

        $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);
        $this->consumeComments();

        $tokenType = $this->tokenizer->peek();

        // Check for function without parameters
        if ($tokenType === Tokens::T_PARENTHESIS_CLOSE) {
            $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);

            return $this->setNodePositionsAndReturn($formalParameters);
        }

        while ($tokenType !== Tokenizer::T_EOF) {
            // check for trailing comma in parameter list
            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            if ($this->allowTrailingCommaInFormalParametersList() && $tokenType === Tokens::T_PARENTHESIS_CLOSE) {
                break;
            }

            $formalParameters->addChild(
                $this->parseFormalParameterOrPrefix($callable)
            );

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            // Check for following parameter
            if ($tokenType !== Tokens::T_COMMA) {
                break;
            }

            $this->consumeToken(Tokens::T_COMMA);
        }

        $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);

        return $this->setNodePositionsAndReturn($formalParameters);
    }

    /**
     * use of trailing comma in formal parameters list is allowed since PHP 8.0
     * example function foo(string $bar, int $baz,)
     */
    protected function allowTrailingCommaInFormalParametersList()
    {
        return false;
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
     * @return ASTFormalParameter
     *
     * @since 0.9.6
     */
    protected function parseFormalParameterOrTypeHintOrByReference()
    {
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        $this->tokenStack->push();

        return $this->setNodePositionsAndReturn(
            $this->parseFormalParameterFromType($tokenType)
        );
    }

    /**
     * @param int $tokenType
     *
     * @return ASTFormalParameter
     */
    private function parseFormalParameterFromType($tokenType)
    {
        if ($this->isTypeHint($tokenType)) {
            $typeHint = $this->parseOptionalTypeHint();
            if ($typeHint) {
                return $this->parseFormalParameterAndTypeHint($typeHint);
            }
        }

        switch ($tokenType) {
            case Tokens::T_ARRAY:
                return $this->parseFormalParameterAndArrayTypeHint();
            case Tokens::T_SELF:
                return $this->parseFormalParameterAndSelfTypeHint();
            case Tokens::T_PARENT:
                return $this->parseFormalParameterAndParentTypeHint();
            case Tokens::T_STATIC:
                return $this->parseFormalParameterAndStaticTypeHint();
            case Tokens::T_BITWISE_AND:
                return $this->parseFormalParameterAndByReference();
            default:
                return $this->parseFormalParameter();
        }
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
     * @return ASTFormalParameter
     *
     * @since 0.9.6
     */
    private function parseFormalParameterAndArrayTypeHint()
    {
        $node = $this->parseArrayType();

        $parameter = $this->parseFormalParameterOrByReference();
        $parameter->prependChild($node);

        return $parameter;
    }

    /**
     * @return ASTTypeArray
     */
    protected function parseArrayType()
    {
        $token = $this->consumeToken(Tokens::T_ARRAY);

        $type = $this->builder->buildAstTypeArray();
        $type->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn
        );

        return $type;
    }

    /**
     * Parses a type hint that is valid in the supported PHP version after the next token.
     *
     * @return ASTType|null
     *
     * @since 2.9.2
     */
    private function parseOptionalTypeHint()
    {
        $this->tokenStack->push();

        return $this->parseTypeHint();
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
     * @return ASTFormalParameter
     *
     * @since 0.9.6
     */
    private function parseFormalParameterAndTypeHint(ASTNode $typeHint)
    {
        $classReference = $this->setNodePositionsAndReturn($typeHint);
        $parameter = $this->parseFormalParameterOrByReference();
        $parameter->prependChild($classReference);

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
     * @throws InvalidStateException
     *
     * @return ASTFormalParameter
     *
     * @since 0.9.6
     */
    private function parseFormalParameterAndParentTypeHint()
    {
        $reference = $this->parseParentType();
        $parameter = $this->parseFormalParameterOrByReference();
        $parameter->prependChild($reference);

        return $parameter;
    }

    /**
     * @return ASTParentReference
     */
    protected function parseParentType()
    {
        return $this->parseParentReference($this->consumeToken(Tokens::T_PARENT));
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
     * @return ASTFormalParameter
     *
     * @since 0.9.6
     */
    private function parseFormalParameterAndSelfTypeHint()
    {
        $self = $this->parseSelfType();

        $parameter = $this->parseFormalParameterOrByReference();
        $parameter->addChild($self);

        return $parameter;
    }

    /**
     * This method will parse a formal parameter that has the keyword static as
     * parameter type hint.
     *
     * <code>
     * class Foo
     * {
     *     //                   -------
     *     public function test(static $o) {}
     *     //                   -------
     * }
     * </code>
     *
     * @return ASTFormalParameter
     *
     * @since 2.9.2
     */
    private function parseFormalParameterAndStaticTypeHint()
    {
        $self = $this->parseStaticType();

        $parameter = $this->parseFormalParameterOrByReference();
        $parameter->addChild($self);

        return $parameter;
    }

    /**
     * @return ASTSelfReference
     */
    protected function parseSelfType()
    {
        return $this->parseSelfReference($this->consumeToken(Tokens::T_SELF));
    }

    /**
     * @return ASTStaticReference
     */
    protected function parseStaticType()
    {
        return $this->parseStaticReference($this->consumeToken(Tokens::T_STATIC));
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
     * @return ASTFormalParameter
     *
     * @since 0.9.6
     */
    protected function parseFormalParameterOrByReference()
    {
        $this->consumeComments();
        if ($this->tokenizer->peek() === Tokens::T_BITWISE_AND) {
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
     * @return ASTFormalParameter
     *
     * @since 0.9.6
     */
    private function parseFormalParameterAndByReference()
    {
        $this->consumeToken(Tokens::T_BITWISE_AND);
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
     * @return ASTFormalParameter
     *
     * @since 0.9.6
     */
    protected function parseFormalParameter()
    {
        $parameter = $this->builder->buildAstFormalParameter();
        $parameter->addChild($this->parseVariableDeclarator());

        return $parameter;
    }

    /**
     * Tests if the given token type is a valid formal parameter in the supported
     * PHP version.
     *
     * @param int $tokenType
     *
     * @return bool
     *
     * @since 1.0.0
     */
    protected function isTypeHint($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_STRING:
            case Tokens::T_BACKSLASH:
            case Tokens::T_NAMESPACE:
            case Tokens::T_ARRAY:
                return true;
        }

        return false;
    }

    /**
     * Parses a type hint that is valid in the supported PHP version.
     *
     * @return ASTType|null
     *
     * @since 1.0.0
     */
    protected function parseTypeHint()
    {
        switch ($this->tokenizer->peek()) {
            case Tokens::T_STRING:
            case Tokens::T_BACKSLASH:
            case Tokens::T_NAMESPACE:
            case Tokens::T_CLASS:
                return $this->builder->buildAstClassOrInterfaceReference(
                    $this->parseQualifiedName()
                );
        }

        return null;
    }

    /**
     * Extracts all dependencies from a callable body.
     *
     * @return ASTScope
     *
     * @since 0.9.12
     */
    private function parseScope()
    {
        $scope = $this->builder->buildAstScope();

        $this->tokenStack->push();

        $this->consumeComments();
        $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);

        while (($stmt = $this->parseOptionalStatement()) !== null) {
            // TODO: Remove if-statement once, we have translated functions and
            //       closures into ast-nodes
            if ($stmt instanceof ASTNode) {
                $scope->addChild($stmt);
            }
        }

        $this->consumeComments();
        $this->consumeToken(Tokens::T_CURLY_BRACE_CLOSE);

        return $this->setNodePositionsAndReturn($scope);
    }

    /**
     * Parse a statement.
     *
     * @throws UnexpectedTokenException
     * @throws TokenStreamEndException
     *
     * @return ASTNode
     *
     * @since 1.0.0
     */
    private function parseStatement()
    {
        if ($stmt = $this->parseOptionalStatement()) {
            return $stmt;
        }
        if ($token = $this->tokenizer->next()) {
            throw $this->getUnexpectedTokenException($token);
        }
        throw new TokenStreamEndException($this->tokenizer);
    }

    /**
     * Parses an optional statement or returns <b>null</b>.
     *
     * @return AbstractASTClassOrInterface|ASTCallable|ASTNode|null
     *
     * @since 0.9.8
     */
    private function parseOptionalStatement()
    {
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {
            case Tokens::T_ECHO:
                return $this->parseEchoStatement();
            case Tokens::T_SWITCH:
                return $this->parseSwitchStatement();
            case Tokens::T_TRY:
                return $this->parseTryStatement();
            case Tokens::T_THROW:
                return $this->parseThrowStatement();
            case Tokens::T_IF:
                return $this->parseIfStatement();
            case Tokens::T_FOR:
                return $this->parseForStatement();
            case Tokens::T_FOREACH:
                return $this->parseForeachStatement();
            case Tokens::T_DO:
                return $this->parseDoWhileStatement();
            case Tokens::T_WHILE:
                return $this->parseWhileStatement();
            case Tokens::T_RETURN:
                return $this->parseReturnStatement();
            case Tokens::T_BREAK:
                return $this->parseBreakStatement();
            case Tokens::T_CONTINUE:
                return $this->parseContinueStatement();
            case Tokens::T_GOTO:
                return $this->parseGotoStatement();
            case Tokens::T_GLOBAL:
                return $this->parseGlobalStatement();
            case Tokens::T_UNSET:
                return $this->parseUnsetStatement();
            case Tokens::T_STRING:
                if ($this->tokenizer->peekNext() === Tokens::T_COLON) {
                    return $this->parseLabelStatement();
                }
                break;
            case Tokens::T_CONST:
                return $this->parseConstantDefinition();
            case Tokens::T_FN:
                return $this->parseLambdaFunctionDeclaration();
            case Tokens::T_FUNCTION:
                return $this->parseFunctionOrClosureDeclaration();
            case Tokens::T_COMMENT:
                return $this->parseCommentWithOptionalInlineClassOrInterfaceReference();
            case Tokens::T_DOC_COMMENT:
                return $this->builder->buildAstComment(
                    $this->consumeToken(Tokens::T_DOC_COMMENT)->image
                );
            case Tokens::T_CURLY_BRACE_OPEN:
                return $this->parseRegularScope();
            case Tokens::T_DECLARE:
                return $this->parseDeclareStatement();
            case Tokens::T_ELSE:
            case Tokens::T_ENDIF:
            case Tokens::T_ELSEIF:
            case Tokens::T_ENDFOR:
            case Tokens::T_ENDWHILE:
            case Tokens::T_ENDSWITCH:
            case Tokens::T_ENDDECLARE:
            case Tokens::T_ENDFOREACH:
            case Tokens::T_CURLY_BRACE_CLOSE:
                return null;
            case Tokens::T_CLOSE_TAG:
                if (($tokenType = $this->parseNonePhpCode()) === Tokenizer::T_EOF) {
                    return null;
                }

                return $this->parseOptionalStatement();
            case Tokens::T_TRAIT:
                $package = $this->getNamespaceOrPackage();
                $package->addType($trait = $this->parseTraitDeclaration());

                $this->builder->restoreTrait($trait);
                $this->compilationUnit->addChild($trait);

                return $trait;
            case Tokens::T_INTERFACE:
                $package = $this->getNamespaceOrPackage();
                $package->addType($interface = $this->parseInterfaceDeclaration());

                $this->builder->restoreInterface($interface);
                $this->compilationUnit->addChild($interface);

                return $interface;
            case Tokens::T_CLASS:
            case Tokens::T_FINAL:
            case Tokens::T_ABSTRACT:
                $package = $this->getNamespaceOrPackage();
                $package->addType($class = $this->parseClassDeclaration());

                $this->builder->restoreClass($class);
                $this->compilationUnit->addChild($class);

                return $class;
            case Tokens::T_YIELD:
                return $this->parseYield();
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
     * @return int
     *
     * @since 0.9.12
     */
    private function parseNonePhpCode()
    {
        $this->consumeToken(Tokens::T_CLOSE_TAG);

        $this->tokenStack->push();
        while (($tokenType = $this->tokenizer->peek()) !== Tokenizer::T_EOF) {
            switch ($tokenType) {
                case Tokens::T_OPEN_TAG:
                case Tokens::T_OPEN_TAG_WITH_ECHO:
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
     * @return ASTComment
     *
     * @since 0.9.8
     */
    private function parseCommentWithOptionalInlineClassOrInterfaceReference()
    {
        $token = $this->consumeToken(Tokens::T_COMMENT);

        $comment = $this->builder->buildAstComment($token->image);
        if (preg_match(self::REGEXP_INLINE_TYPE, $token->image, $match)) {
            $image = $this->useSymbolTable->lookup($match[1]) ?: $match[1];

            $comment->addChild(
                $this->builder->buildAstClassOrInterfaceReference($image)
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
     * @template T of ASTClosure
     *
     * @param T $closure The context closure instance.
     *
     * @return T
     *
     * @since 1.0.0
     */
    protected function parseOptionalBoundVariables(
        ASTClosure $closure
    ) {
        $this->consumeComments();

        if (Tokens::T_USE === $this->tokenizer->peek()) {
            return $this->parseBoundVariables($closure);
        }

        return $closure;
    }

    /**
     * Parses a list of bound closure variables.
     *
     * @template T of ASTClosure
     *
     * @param T $closure The parent closure instance.
     *
     * @return T
     *
     * @since 0.9.5
     */
    private function parseBoundVariables(ASTClosure $closure)
    {
        $this->consumeToken(Tokens::T_USE);

        $this->consumeComments();
        $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);

        while ($this->tokenizer->peek() !== Tokenizer::T_EOF) {
            $this->consumeComments();

            if ($this->allowTrailingCommaInClosureUseList() &&
                $this->tokenizer->peek() === Tokens::T_PARENTHESIS_CLOSE) {
                break;
            }

            if ($this->tokenizer->peek() === Tokens::T_BITWISE_AND) {
                $this->consumeToken(Tokens::T_BITWISE_AND);
                $this->consumeComments();
            }

            $this->consumeToken(Tokens::T_VARIABLE);
            $this->consumeComments();

            if ($this->tokenizer->peek() === Tokens::T_COMMA) {
                $this->consumeToken(Tokens::T_COMMA);

                continue;
            }

            break;
        }

        $this->consumeComments();
        $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);

        return $closure;
    }

    /**
     * Trailing commas is allowed in closure use list from PHP 8.0
     *
     * @return bool
     */
    protected function allowTrailingCommaInClosureUseList()
    {
        return false;
    }

    /**
     * Parses a php class/method name chain.
     *
     * <code>
     * PDepend\Source\Parser::parse();
     * </code>
     *
     * @throws NoActiveScopeException
     *
     * @return string
     *
     * @link   http://php.net/manual/en/language.namespaces.importing.php
     */
    protected function parseQualifiedName()
    {
        $fragments = $this->parseQualifiedNameRaw();

        // Check for fully qualified name
        if ($fragments[0] === '\\') {
            return join('', $fragments);
        }

        if ($this->isScalarOrCallableTypeHint($fragments[0])) {
            return $fragments[0];
        }

        // Search for an use alias
        $mapsTo = $this->useSymbolTable->lookup($fragments[0]);

        if ($mapsTo !== null) {
            // Remove alias and add real namespace
            array_shift($fragments);
            array_unshift($fragments, $mapsTo);
        } elseif ($this->namespaceName !== null
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
     * @return array<string>
     *
     * @since 0.9.5
     */
    protected function parseQualifiedNameRaw()
    {
        // Reset namespace prefix flag
        $this->namespacePrefixReplaced = false;

        // Consume comments and fetch first token type
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        $qualifiedName = array();

        if ($tokenType === Tokens::T_NAMESPACE) {
            // Consume namespace keyword
            $this->consumeToken(Tokens::T_NAMESPACE);
            $this->consumeComments();

            // Add current namespace as first token
            $qualifiedName = array((string) $this->namespaceName);

            // Set prefixed flag to true
            $this->namespacePrefixReplaced = true;
        } elseif ($this->isClassName($tokenType)) {
            $qualifiedName[] = $this->parseClassName();

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            // Stop here for simple identifier
            if ($tokenType !== Tokens::T_BACKSLASH) {
                return $qualifiedName;
            }
        }

        do {
            // Next token must be a namespace separator
            $this->consumeToken(Tokens::T_BACKSLASH);
            $this->consumeComments();

            // Append to qualified name
            $qualifiedName[] = '\\';

            if ($nextElement = $this->parseQualifiedNameElement($qualifiedName)) {
                $qualifiedName[] = $nextElement;
            }

            $this->consumeComments();

            // Get next token type
            $tokenType = $this->tokenizer->peek();
        } while ($tokenType === Tokens::T_BACKSLASH);

        return $qualifiedName;
    }

    /**
     * Determines if the given image is a PHP 7 type hint.
     *
     * @param string $image
     *
     * @return bool
     */
    protected function isScalarOrCallableTypeHint($image)
    {
        // Scalar & callable type hints were not present in PHP 5
        return false;
    }

    /**
     * @param array<string> $previousElements
     *
     * @return string
     */
    protected function parseQualifiedNameElement(array $previousElements)
    {
        return $this->parseClassName();
    }

    /**
     * This method parses a PHP 5.3 namespace declaration.
     *
     * @throws NoActiveScopeException
     *
     * @return void
     *
     * @since 0.9.5
     */
    private function parseNamespaceDeclaration()
    {
        // Consume namespace keyword and strip optional comments
        $this->consumeToken(Tokens::T_NAMESPACE);
        $this->consumeComments();

        $tokenType = $this->tokenizer->peek();

        // Search for a namespace identifier
        if ($this->isClassName($tokenType)) {
            // Reset namespace property
            $this->namespaceName = null;

            $qualifiedName = $this->parseQualifiedName();

            $this->consumeComments();
            if ($this->tokenizer->peek() === Tokens::T_CURLY_BRACE_OPEN) {
                $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);
            } else {
                $this->consumeToken(Tokens::T_SEMICOLON);
            }

            // Create a package for this namespace
            $this->namespaceName = $qualifiedName;

            $this->useSymbolTable->resetScope();
        } elseif ($tokenType === Tokens::T_BACKSLASH) {
            // Same namespace reference, something like:
            //   new namespace\Foo();
            // or:
            //   $x = namespace\foo::bar();

            // Now parse a qualified name
            $this->parseQualifiedNameRaw();
        } else {
            // Consume opening curly brace
            $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);

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
     *
     * @since 0.9.5
     */
    protected function parseUseDeclarations()
    {
        // Consume use keyword
        $this->consumeToken(Tokens::T_USE);
        $this->consumeComments();

        // Parse all use declarations
        $this->parseUseDeclaration();
        $this->consumeComments();

        // Consume closing semicolon
        $this->consumeToken(Tokens::T_SEMICOLON);

        // Reset any previous state
        $this->reset();
    }

    /**
     * This method parses a single use declaration and adds a mapping between
     * short name and full qualified name to the use symbol table.
     *
     * @throws NoActiveScopeException
     *
     * @return void
     *
     * @since 0.9.5
     */
    protected function parseUseDeclaration()
    {
        $fragments = $this->parseQualifiedNameRaw();
        $this->consumeComments();

        // Add leading backslash, because aliases must be full qualified
        // http://php.net/manual/en/language.namespaces.importing.php
        if ($fragments[0] !== '\\') {
            array_unshift($fragments, '\\');
        }

        $this->parseUseDeclarationForVersion($fragments);

        // Check for a following use declaration
        if ($this->tokenizer->peek() === Tokens::T_COMMA) {
            // Consume comma token and comments
            $this->consumeToken(Tokens::T_COMMA);
            $this->consumeComments();

            $this->parseUseDeclaration();
        }
    }

    /**
     * @param array<string> $fragments
     *
     * @return void
     */
    protected function parseUseDeclarationForVersion(array $fragments)
    {
        $image = $this->parseNamespaceImage($fragments);

        // Add mapping between image and qualified name to symbol table
        $this->useSymbolTable->add($image, join('', $fragments));
    }

    /**
     * @param array<string> $fragments
     *
     * @return string
     */
    protected function parseNamespaceImage(array $fragments)
    {
        if ($this->tokenizer->peek() === Tokens::T_AS) {
            $this->consumeToken(Tokens::T_AS);
            $this->consumeComments();

            $image = $this->consumeToken(Tokens::T_STRING)->image;
            $this->consumeComments();
        } else {
            $image = end($fragments);
        }

        return $image;
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
     * @return ASTConstantDefinition
     *
     * @since 0.9.6
     */
    protected function parseConstantDefinition()
    {
        $this->tokenStack->push();

        $token = $this->consumeToken(Tokens::T_CONST);

        $definition = $this->builder->buildAstConstantDefinition($token->image);
        $definition->setComment($this->docComment);

        do {
            $definition->addChild($this->parseConstantDeclarator());

            $this->consumeComments();
            $tokenType = $this->tokenizer->peek();

            if ($tokenType === Tokens::T_SEMICOLON) {
                break;
            }

            $this->consumeToken(Tokens::T_COMMA);
        } while ($tokenType !== Tokenizer::T_EOF);


        $definition = $this->setNodePositionsAndReturn($definition);

        $this->consumeToken(Tokens::T_SEMICOLON);

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
     * @return ASTConstantDeclarator
     *
     * @since 0.9.6
     */
    protected function parseConstantDeclarator()
    {
        // Remove leading comments and create a new token stack
        $this->consumeComments();
        $this->tokenStack->push();

        $tokenType = $this->tokenizer->peek();

        if (false === $this->isConstantName($tokenType)) {
            throw $this->getUnexpectedTokenException();
        }

        $token = $this->consumeToken($tokenType);

        $this->consumeComments();
        $this->consumeToken(Tokens::T_EQUAL);

        $declarator = $this->builder->buildAstConstantDeclarator($token->image);
        $declarator->setValue($this->parseConstantDeclaratorValue());

        return $this->setNodePositionsAndReturn($declarator);
    }

    /**
     * Parses the value of a php constant. By default this can be only static
     * values that were allowed in the oldest supported PHP version.
     *
     * @return ASTValue
     *
     * @since 2.2.x
     */
    protected function parseConstantDeclaratorValue()
    {
        return $this->parseStaticValue();
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
     * @throws ParserException
     * @throws UnexpectedTokenException
     *
     * @return ASTNode
     *
     * @since 0.9.6
     */
    private function parseStaticVariableDeclarationOrMemberPrimaryPrefix()
    {
        $this->tokenStack->push();

        // Consume static token and strip optional comments
        $token = $this->consumeToken(Tokens::T_STATIC);
        $this->consumeComments();

        // Fetch next token type
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === Tokens::T_PARENTHESIS_OPEN
            || $tokenType === Tokens::T_DOUBLE_COLON
        ) {
            return $this->setNodePositionsAndReturn(
                $this->parseStaticMemberPrimaryPrefix(
                    $this->parseStaticReference($token)
                )
            );
        } elseif ($tokenType === Tokens::T_FUNCTION) {
            $closure = $this->parseClosureDeclaration();
            $closure->setStatic(true);

            return $this->setNodePositionsAndReturn($closure);
        } elseif ($tokenType === Tokens::T_FN) {
            $closure = $this->parseLambdaFunctionDeclaration();
            $closure->setStatic(true);

            return $this->setNodePositionsAndReturn($closure);
        }

        return $this->setNodePositionsAndReturn(
            $this->parseStaticVariableDeclaration($token)
        );
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
     * @param Token $token Token with the "static" keyword.
     *
     * @return ASTStaticVariableDeclaration
     *
     * @since 0.9.6
     */
    private function parseStaticVariableDeclaration(Token $token)
    {
        $staticDeclaration = $this->builder->buildAstStaticVariableDeclaration(
            $token->image
        );

        // Strip optional comments
        $this->consumeComments();

        // Fetch next token type
        $tokenType = $this->tokenizer->peek();

        while ($tokenType !== Tokenizer::T_EOF) {
            $staticDeclaration->addChild($this->parseVariableDeclarator());

            $this->consumeComments();

            // Semicolon terminates static declaration
            $tokenType = $this->tokenizer->peek();

            if ($tokenType === Tokens::T_SEMICOLON) {
                break;
            }

            // We are here, so there must be a next declarator
            $this->consumeToken(Tokens::T_COMMA);
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
     * @return ASTVariableDeclarator
     *
     * @since 0.9.6
     */
    protected function parseVariableDeclarator()
    {
        $this->tokenStack->push();

        $name = $this->consumeToken(Tokens::T_VARIABLE)->image;
        $this->consumeComments();

        $declarator = $this->builder->buildAstVariableDeclarator($name);

        if ($this->tokenizer->peek() === Tokens::T_EQUAL) {
            $this->consumeToken(Tokens::T_EQUAL);
            $declarator->setValue($this->parseStaticValueOrStaticArray());
        }

        return $this->setNodePositionsAndReturn($declarator);
    }

    /**
     * This method will parse a static value or a static array as it is
     * used as default value for a parameter or property declaration.
     *
     * @return ASTValue
     *
     * @since 0.9.6
     */
    protected function parseStaticValueOrStaticArray()
    {
        $this->consumeComments();

        if ($this->isArrayStartDelimiter()) {
            // TODO: Use default value as value!
            $defaultValue = $this->doParseArray(true);

            $value = new ASTValue();
            $value->setValue(array());

            return $value;
        }

        return $this->parseStaticValue();
    }

    /**
     * This method will parse a static default value as it is used for a
     * parameter, property or constant declaration.
     *
     * @return ASTValue
     *
     * @since 0.9.5
     */
    protected function parseStaticValue()
    {
        $defaultValue = new ASTValue();

        $this->consumeComments();

        // By default all parameters positive signed
        $signed = 1;

        $tokenType = $this->tokenizer->peek();

        while ($tokenType !== Tokenizer::T_EOF) {
            switch ($tokenType) {
                case Tokens::T_COMMA:
                case Tokens::T_SEMICOLON:
                case Tokens::T_PARENTHESIS_CLOSE:
                    if ($defaultValue->isValueAvailable() === true) {
                        return $defaultValue;
                    }

                    throw new MissingValueException($this->tokenizer);
                case Tokens::T_NULL:
                    $this->consumeToken(Tokens::T_NULL);
                    $defaultValue->setValue(null);
                    break;
                case Tokens::T_TRUE:
                    $this->consumeToken(Tokens::T_TRUE);
                    $defaultValue->setValue(true);
                    break;
                case Tokens::T_FALSE:
                    $this->consumeToken(Tokens::T_FALSE);
                    $defaultValue->setValue(false);
                    break;
                case Tokens::T_LNUMBER:
                    $token = $this->consumeToken(Tokens::T_LNUMBER);
                    $defaultValue->setValue($signed * (int) $token->image);
                    break;
                case Tokens::T_DNUMBER:
                    $token = $this->consumeToken(Tokens::T_DNUMBER);
                    $defaultValue->setValue($signed * (double) $token->image);
                    break;
                case Tokens::T_CONSTANT_ENCAPSED_STRING:
                    $token = $this->consumeToken(Tokens::T_CONSTANT_ENCAPSED_STRING);
                    $defaultValue->setValue(substr($token->image, 1, -1));
                    break;
                case Tokens::T_DOUBLE_COLON:
                    $this->consumeToken(Tokens::T_DOUBLE_COLON);
                    break;
                case Tokens::T_CLASS_FQN:
                    $this->consumeToken(Tokens::T_CLASS_FQN);
                    break;
                case Tokens::T_PLUS:
                    $this->consumeToken(Tokens::T_PLUS);
                    break;
                case Tokens::T_ELLIPSIS:
                    $this->checkEllipsisInExpressionSupport();
                    $this->consumeToken(Tokens::T_ELLIPSIS);
                    break;
                case Tokens::T_MINUS:
                    $this->consumeToken(Tokens::T_MINUS);
                    $signed *= -1;
                    break;
                case Tokens::T_DOUBLE_QUOTE:
                    $defaultValue->setValue($this->parseStringSequence($tokenType));
                    break;
                case Tokens::T_STATIC:
                case Tokens::T_SELF:
                case Tokens::T_PARENT:
                    $node = $this->parseStandAloneExpressionTypeReference(true);

                    if ($this->tokenizer->peek() === Tokens::T_DOUBLE_COLON) {
                        $node->addChild($this->parseStaticMemberPrimaryPrefix($node));
                    }

                    $defaultValue->setValue($node);
                    break;
                case Tokens::T_STRING:
                case Tokens::T_BACKSLASH:
                    $node = $this->builder->buildAstClassOrInterfaceReference(
                        $this->parseQualifiedName()
                    );

                    if ($this->tokenizer->peek() === Tokens::T_DOUBLE_COLON) {
                        $node->addChild($this->parseStaticMemberPrimaryPrefix($node));
                    }

                    $defaultValue->setValue($node);
                    break;
                case Tokens::T_DIR:
                case Tokens::T_FILE:
                case Tokens::T_LINE:
                case Tokens::T_NS_C:
                case Tokens::T_FUNC_C:
                case Tokens::T_CLASS_C:
                case Tokens::T_METHOD_C:
                case Tokens::T_SQUARED_BRACKET_OPEN:
                case Tokens::T_SQUARED_BRACKET_CLOSE:
                    // There is a default value but we don't handle it at the moment.
                    $defaultValue->setValue(null);
                    $this->consumeToken($tokenType);
                    break;
                case Tokens::T_START_HEREDOC:
                    $defaultValue->setValue(
                        $this->parseHeredoc()->getChild(0)->getImage()
                    );
                    break;
                default:
                    return $this->parseStaticValueVersionSpecific($defaultValue);
            }

            $this->consumeComments();

            $tokenType = $this->tokenizer->peek();
        }

        // We should never reach this, so throw an exception
        throw new TokenStreamEndException($this->tokenizer);
    }

    /**
     * Parses additional static values that are valid in the supported php version.
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTValue
     */
    protected function parseStaticValueVersionSpecific(ASTValue $value)
    {
        throw $this->getUnexpectedTokenException();
    }

    /**
     * Parses fn operator of lambda function for syntax fn() => available since PHP 7.4.
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTClosure
     */
    protected function parseLambdaFunctionDeclaration()
    {
        throw $this->getUnexpectedTokenException();
    }

    /**
     * Checks if the given expression is a read/write variable as defined in
     * the PHP zend_language_parser.y definition.
     *
     * @param ASTNode $expr The context node instance.
     *
     * @return bool
     *
     * @since 0.10.0
     */
    private function isReadWriteVariable($expr)
    {
        return $expr instanceof ASTVariable
            || $expr instanceof ASTFunctionPostfix
            || $expr instanceof ASTVariableVariable
            || $expr instanceof ASTCompoundVariable
            || $expr instanceof ASTMemberPrimaryPrefix;
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
     *
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
     * @return ASTNamespace
     *
     * @since 1.0.0
     */
    private function getNamespaceOrPackage()
    {
        $namespace = $this->builder->buildNamespace($this->getNamespaceOrPackageName());
        $namespace->setPackageAnnotation(null === $this->namespaceName);

        return $namespace;
    }

    /**
     * Extracts the @package information from the given comment.
     *
     * @param string $comment
     *
     * @return string|null
     */
    private function parsePackageAnnotation($comment)
    {
        if (getenv('DISMISS_PACKAGES')) {
            $this->packageName = null;
            $this->globalPackageName = null;

            return null;
        }

        $package = Builder::DEFAULT_NAMESPACE;
        if (preg_match('#\*\s*@package\s+(\S+)#', $comment, $match)) {
            $package = trim($match[1]);
            if (preg_match('#\*\s*@subpackage\s+(\S+)#', $comment, $match)) {
                $package .= '\\' . trim($match[1]);
            }
        }

        // Check for doc level comment
        if ($this->globalPackageName === Builder::DEFAULT_NAMESPACE
            && $this->isFileComment() === true
        ) {
            $this->globalPackageName = $package;

            $this->compilationUnit->setComment($comment);
        }

        return $package;
    }

    /**
     * Checks that the current token could be used as file comment.
     *
     * This method checks that the previous token is an open tag and the following
     * token is not a class, a interface, final, abstract or a function.
     *
     * @return bool
     */
    protected function isFileComment()
    {
        if ($this->tokenizer->prev() !== Tokens::T_OPEN_TAG) {
            return false;
        }

        $notExpectedTags = array(
            Tokens::T_CLASS,
            Tokens::T_FINAL,
            Tokens::T_TRAIT,
            Tokens::T_ABSTRACT,
            Tokens::T_FUNCTION,
            Tokens::T_INTERFACE
        );

        return !in_array($this->tokenizer->peek(), $notExpectedTags, true);
    }

    /**
     * Returns the class names of all <b>throws</b> annotations with in the
     * given comment block.
     *
     * @param string $comment The context doc comment block.
     *
     * @return array<int, string>
     */
    private function parseThrowsAnnotations($comment)
    {
        $throws = array();

        if (preg_match_all(self::REGEXP_THROWS_TYPE, $comment, $matches) > 0) {
            foreach ($matches[1] as $match) {
                $throws[] = $this->useSymbolTable->lookup($match) ?: $match;
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
     * @return string|null
     */
    private function parseReturnAnnotation($comment)
    {
        if (0 === preg_match(self::REGEXP_RETURN_TYPE, $comment, $match)) {
            return null;
        }

        foreach (explode('|', end($match)) as $image) {
            $image = $this->useSymbolTable->lookup($image) ?: $image;

            if (Type::isScalarType($image)) {
                continue;
            }

            return $image;
        }

        return null;
    }

    /**
     * This method parses the given doc comment text for a var annotation and
     * it returns the found property types.
     *
     * @param string $comment A doc comment text.
     *
     * @return array<string>
     */
    private function parseVarAnnotation($comment)
    {
        if (preg_match(self::REGEXP_VAR_TYPE, (string) $comment, $match) > 0) {
            $useSymbolTable = $this->useSymbolTable;

            return array_map(
                function ($image) use ($useSymbolTable) {
                    return $useSymbolTable->lookup($image) ?: $image;
                },
                array_map('trim', explode('|', end($match)))
            );
        }

        return array();
    }

    /**
     * This method will extract the type information of a property from it's
     * doc comment information. The returned value will be <b>null</b> when no
     * type information exists.
     *
     * @return ASTType|null
     *
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
            if (Type::isPrimitiveType($annotation) === true) {
                return $this->builder->buildAstScalarType(
                    Type::getPrimitiveType($annotation)
                );
            }

            if (Type::isArrayType($annotation) === true) {
                return $this->builder->buildAstTypeArray();
            }
        }

        return null;
    }

    /**
     * Extracts non scalar types from a field doc comment and creates a
     * matching type instance.
     *
     * @return ASTClassOrInterfaceReference|null
     *
     * @since 0.9.6
     */
    private function parseFieldDeclarationClassOrInterfaceReference()
    {
        $annotations = $this->parseVarAnnotation($this->docComment);

        foreach ($annotations as $annotation) {
            if (Type::isScalarType($annotation) === false) {
                return $this->builder->buildAstClassOrInterfaceReference(
                    $annotation
                );
            }
        }

        return null;
    }

    /**
     * This method parses a yield-statement node.
     *
     * @return ASTYieldStatement
     */
    private function parseYield()
    {
        $this->tokenStack->push();

        $token = $this->consumeToken(Tokens::T_YIELD);
        $this->consumeComments();

        $yield = $this->builder->buildAstYieldStatement($token->image);

        $node = $this->parseOptionalExpression();
        if ($node) {
            $yield->addChild($node);

            if ($this->tokenizer->peek() === Tokens::T_DOUBLE_ARROW) {
                $this->consumeToken(Tokens::T_DOUBLE_ARROW);

                $yield->addChild($this->parseOptionalExpression());
            }
        }

        $this->consumeComments();

        if (Tokens::T_PARENTHESIS_CLOSE === $this->tokenizer->peek()) {
            return $this->setNodePositionsAndReturn($yield);
        }

        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($yield);
    }

    /**
     * Extracts documented <b>throws</b> and <b>return</b> types and sets them
     * to the given <b>$callable</b> instance.
     *
     * @return void
     */
    private function prepareCallable(AbstractASTCallable $callable)
    {
        // Skip, if ignore annotations is set
        if ($this->ignoreAnnotations === true) {
            return;
        }

        // Get all @throws Types
        $comment = $callable->getComment();
        $throws = $comment === null ? array() : $this->parseThrowsAnnotations($comment);

        foreach ($throws as $qualifiedName) {
            $callable->addExceptionClassReference(
                $this->builder->buildAstClassOrInterfaceReference($qualifiedName)
            );
        }

        // Stop here if return class already exists.
        if ($callable->hasReturnClass()) {
            return;
        }

        // Get return annotation
        $qualifiedName = $comment === null ? null : $this->parseReturnAnnotation($comment);

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
     * @param int $tokenType The next expected token type.
     *
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     *
     * @return Token
     */
    protected function consumeToken($tokenType)
    {
        switch ($this->tokenizer->peek()) {
            case $tokenType:
                return $this->tokenStack->add($this->tokenizer->next());
            case Tokenizer::T_EOF:
                throw new TokenStreamEndException($this->tokenizer);
        }

        throw $this->getUnexpectedTokenException();
    }

    /**
     * This method will consume all comment tokens from the token stream.
     *
     * @return void
     */
    protected function consumeComments()
    {
        $type = $this->tokenizer->peek();
        while ($type == Tokens::T_COMMENT || $type == Tokens::T_DOC_COMMENT) {
            $token = $this->consumeToken($type);
            $type  = $this->tokenizer->peek();

            if (Tokens::T_COMMENT === $token->type) {
                continue;
            }

            $this->docComment = $token->image;
            if (preg_match('(\s+@package\s+[^\s]+\s+)', $token->image)) {
                $this->packageName = $this->parsePackageAnnotation($token->image);
            }
        }
    }

    /**
     * @return UnexpectedTokenException
     */
    protected function getUnexpectedTokenException(Token $token = null)
    {
        return new UnexpectedTokenException(
            (null === $token) ? $this->tokenizer->next() : $token,
            $this->tokenizer->getSourceFile()
        );
    }

    /**
     * Throws an UnexpectedTokenException
     *
     * @throws UnexpectedTokenException
     *
     * @return never
     *
     * @since 2.2.5
     * @deprecated 3.0.0 Use throw $this->getUnexpectedTokenException($token) instead
     */
    protected function throwUnexpectedTokenException(Token $token = null)
    {
        throw $this->getUnexpectedTokenException($token);
    }

    /**
     * @return void
     */
    protected function checkEllipsisInExpressionSupport()
    {
        throw $this->getUnexpectedTokenException();
    }

    /**
     * Parses throw expression syntax. available since PHP 8.0. Ex.:
     *  $callable = fn() => throw new Exception();
     *  $value = $nullableValue ?? throw new InvalidArgumentException();
     *  $value = $falsableValue ?: throw new InvalidArgumentException();
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTThrowStatement
     */
    protected function parseThrowExpression()
    {
        throw $this->getUnexpectedTokenException();
    }
}
