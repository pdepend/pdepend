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

use InvalidArgumentException;
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
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTEnumCase;
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
use PDepend\Source\AST\ASTIntersectionType;
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
use PDepend\Source\AST\ASTScalarType;
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
use PDepend\Source\AST\ASTUnionType;
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
use PDepend\Source\Parser\TokenException;
use PDepend\Source\Parser\TokenStack;
use PDepend\Source\Parser\TokenStreamEndException;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\FullTokenizer;
use PDepend\Source\Tokenizer\Token;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Source\Tokenizer\Tokens;
use PDepend\Util\Cache\CacheDriver;
use PDepend\Util\IdBuilder;
use PDepend\Util\Log;
use PDepend\Util\Type;

/**
 * The php source parser implementation that supports features up to PHP version 8.1.
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
    /** Tell if readonly is allowed as class modifier in the current PHP level. */
    protected const READONLY_CLASS_ALLOWED = false;

    /**
     * Regular expression for inline type definitions in regular comments. This
     * kind of type is supported by IDEs like Netbeans or eclipse.
     */
    private const REGEXP_INLINE_TYPE = '(^\s*/\*\s*
                                 @var\s+
                                   \$[a-zA-Z_\x7f-\xff\\\\][a-zA-Z0-9_\x7f-\xff]*\s+
                                   (.*?)
                                \s*\*/\s*$)ix';

    /**
     * Regular expression for types defined in <b>throws</b> annotations of
     * method or function doc comments.
     */
    private const REGEXP_THROWS_TYPE = '(\*\s*
                             @throws\s+
                               ([a-zA-Z_\x7f-\xff\\\\][a-zA-Z0-9_\x7f-\xff\\\\]*)
                            )ix';

    /**
     * Regular expression for types defined in annotations like <b>return</b> or
     * <b>var</b> in doc comments of functions and methods.
     */
    private const REGEXP_RETURN_TYPE = '(\*\s*
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
    private const REGEXP_VAR_TYPE = '(\*\s*
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
     * Regular expression for integer numbers representation.
     *
     * @see https://php.net/manual/en/language.types.integer.php
     * @see https://github.com/php/doc-en/blob/d494ffa4d9f83b60fe66972ec2c0cf0301513b4a/language/types/integer.xml#L77-L89
     */
    private const REGEXP_INTEGER = '(
                       0
                       |
                       [1-9][0-9]*(?:_[0-9]+)*
                       |
                       0[xX][0-9a-fA-F]+(?:_[0-9a-fA-F]+)*
                       |
                       0[oO]?[0-7]+(?:_[0-7]+)*
                       |
                       0[bB][01]+(?:_[01]+)*
                     )x';

    /** Stack with all active token scopes. */
    protected TokenStack $tokenStack;

    /** @var array<int, int> */
    protected array $possiblePropertyTypes = [
        Tokens::T_STRING,
        Tokens::T_ARRAY,
        Tokens::T_QUESTION_MARK,
        Tokens::T_BACKSLASH,
        Tokens::T_CALLABLE,
        Tokens::T_SELF,
        Tokens::T_NULL,
        Tokens::T_FALSE,
    ];

    /**
     * Internal state flag, that will be set to <b>true</b> when the parser has
     * prefixed a qualified name with the actual namespace.
     */
    private bool $namespacePrefixReplaced = false;

    /** The currently parsed file instance. */
    private ASTCompilationUnit $compilationUnit;

    /** The symbol table used to handle PHP use statements. */
    private SymbolTable $useSymbolTable;

    /** The actually parsed class or interface instance. */
    private ?AbstractASTClassOrInterface $classOrInterface = null;

    /** The name of the last detected namespace. */
    private ?string $namespaceName = null;

    /** Last parsed package tag. */
    private ?string $packageName = Builder::DEFAULT_NAMESPACE;

    /** The package defined in the file level comment. */
    private ?string $globalPackageName = Builder::DEFAULT_NAMESPACE;

    /** The last parsed doc comment or <b>null</b>. */
    private ?string $docComment = null;

    /** Bitfield of last parsed modifiers. */
    private int $modifiers = 0;

    /**
     * If this property is set to <b>true</b> the parser will ignore all doc
     * comment annotations.
     */
    private bool $ignoreAnnotations = false;

    /**
     * Used identifier builder instance.
     *
     * @since 0.9.12
     */
    private readonly IdBuilder $idBuilder;

    /**
     * The maximum valid nesting level allowed.
     *
     * @since 0.9.12
     */
    private int $maxNestingLevel = 1024;

    /** True if current statement is echoing (such as after <?=) */
    private bool $echoing = false;

    /**
     * Constructs a new source parser.
     *
     * @param Tokenizer $tokenizer The used code tokenizer.
     * @param PHPBuilder<mixed> $builder The used data structure builder.
     */
    public function __construct(
        protected Tokenizer $tokenizer,
        protected PHPBuilder $builder,
        private readonly CacheDriver $cache
    ) {
        $this->idBuilder = new IdBuilder();
        $this->tokenStack = new TokenStack();
        $this->useSymbolTable = new SymbolTable();

        $this->builder->setCache($this->cache);
    }

    /**
     * Sets the ignore annotations flag. This means that the parser will ignore
     * doc comment annotations.
     */
    public function setIgnoreAnnotations(): void
    {
        $this->ignoreAnnotations = true;
    }

    /**
     * Configures the maximum allowed nesting level.
     *
     * @param int $maxNestingLevel The maximum allowed nesting level.
     * @since 0.9.12
     */
    public function setMaxNestingLevel(int $maxNestingLevel): void
    {
        $this->maxNestingLevel = $maxNestingLevel;
    }

    /**
     * Returns the maximum allowed nesting/recursion level.
     *
     * @since 0.9.12
     */
    private function getMaxNestingLevel(): int
    {
        return $this->maxNestingLevel;
    }

    private function parseReturnTypeHint(): ASTType
    {
        $this->consumeComments();
        $this->consumeQuestionMark();
        $this->consumeComments();

        return $this->parseEndReturnTypeHint();
    }

    /**
     * Parses a scalar type hint or a callable type hint.
     *
     * @throws ParserException
     */
    private function parseScalarOrCallableTypeHint(string $image): ASTType
    {
        return match (strtolower($image)) {
            'int',
            'bool',
            'float',
            'string',
            'void',
            'never' => $this->builder->buildAstScalarType($image),
            'callable' => $this->builder->buildAstTypeCallable(),
            'iterable' => $this->builder->buildAstTypeIterable(),
            default => throw new ParserException('Unsupported typehint'),
        };
    }

    private function consumeQuestionMark(): void
    {
        if ($this->tokenizer->peek() === Tokens::T_QUESTION_MARK) {
            $this->consumeToken(Tokens::T_QUESTION_MARK);
        }
    }

    private function getModifiersForConstantDefinition(int $tokenType, int $modifiers): int
    {
        $allowed = State::IS_PUBLIC | State::IS_PROTECTED | State::IS_PRIVATE;
        $modifiers &= $allowed;

        if (
            $this->classOrInterface instanceof ASTInterface
            && ($modifiers & (State::IS_PROTECTED | State::IS_PRIVATE)) !== 0
        ) {
            throw new InvalidStateException(
                $this->requireNextToken()->startLine,
                (string) $this->compilationUnit,
                sprintf(
                    'Constant can\'t be declared private or protected in interface "%s".',
                    $this->classOrInterface->getImage()
                )
            );
        }

        return $modifiers;
    }

    private function parseEndReturnTypeHint(): ASTType
    {
        return $this->parseTypeHint();
    }

    /**
     * Attempts to the next sequence of tokens as an anonymous class and adds it to the allocation expression
     *
     * @template T of ASTAllocationExpression
     *
     * @param T $allocation
     *
     * @return T|null
     */
    private function parseAnonymousClassDeclaration(ASTAllocationExpression $allocation): ?ASTAllocationExpression
    {
        $this->consumeComments();

        if (Tokens::T_CLASS !== $this->tokenizer->peek()) {
            return null;
        }

        $classOrInterface = $this->classOrInterface;

        $this->tokenStack->push();

        $this->consumeToken(Tokens::T_CLASS);
        $this->consumeComments();

        $class = $this->builder->buildAnonymousClass();
        $class->setName(
            sprintf(
                'class@anonymous%s0x%s',
                $this->compilationUnit->getFileName(),
                uniqid('')
            )
        );
        $class->setCompilationUnit($this->compilationUnit);
        $class->setUserDefined();

        if ($this->isNextTokenArguments()) {
            $class->addChild($this->parseArguments());
        }

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

        $tokens = [];
        $allocation->addChild(
            $this->setNodePositionsAndReturn(
                $this->parseTypeBody($class),
                $tokens
            )
        );
        $class->setTokens($tokens);

        $this->classOrInterface = $classOrInterface;

        return $allocation;
    }

    /**
     * @param array<string> $fragments
     */
    private function parseUseDeclarationVersion70(array $fragments): void
    {
        $namespacePrefixReplaced = $this->namespacePrefixReplaced;

        $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);
        $this->consumeComments();

        while (true) {
            $nextToken = $this->tokenizer->peek();

            switch ($nextToken) {
                case Tokens::T_CONST:
                case Tokens::T_FUNCTION:
                    $this->consumeToken($nextToken);
            }

            if (Tokens::T_CURLY_BRACE_CLOSE === $this->tokenizer->peek()) {
                break;
            }

            $subFragments = $this->parseQualifiedNameRaw();
            $this->consumeComments();

            $image = $this->parseNamespaceImage($subFragments);

            if (Tokens::T_COMMA !== $this->tokenizer->peek()) {
                break;
            }

            $this->consumeToken(Tokens::T_COMMA);
            $this->consumeComments();

            // Add mapping between image and qualified name to symbol table
            if ($image !== false) {
                $this->useSymbolTable->add($image, implode('', [...$fragments, ...$subFragments]));
            }
        }

        if (isset($image, $subFragments) && $image !== false) {
            $this->useSymbolTable->add($image, implode('', [...$fragments, ...$subFragments]));
        }

        $this->consumeToken(Tokens::T_CURLY_BRACE_CLOSE);
        $this->consumeComments();

        $this->namespacePrefixReplaced = $namespacePrefixReplaced;
    }

    private function parseConstantArgument(ASTConstant $constant, ASTArguments $arguments): AbstractASTNode
    {
        if ($this->tokenizer->peek() === Tokens::T_COLON) {
            $token = $this->tokenizer->next();
            assert($token instanceof Token);
            $this->tokenStack->add($token);

            $expression = $this->parseOptionalExpression();
            if ($expression) {
                return $this->builder->buildAstNamedArgument($constant->getImage(), $expression);
            }
        }

        return $constant;
    }

    private function parseArgumentExpression(): ?ASTNode
    {
        if ($this->tokenizer->peekNext() === Tokens::T_COLON) {
            $token = $this->tokenizer->currentToken();
            if ($token) {
                $image = $token->image;

                // Variable RegExp from https://www.php.net/manual/en/language.variables.basics.php
                if (preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $image)) {
                    $this->consumeToken($token->type);

                    return $this->builder->buildAstConstant($image);
                }
            }
        }

        return $this->parseOptionalExpression();
    }

    /**
     * Determines if the following expression can be stored as a static value.
     */
    private function isFollowedByStaticValueOrStaticArray(): bool
    {
        // If we can't anticipate, we should assume it can be a dynamic value
        if (!($this->tokenizer instanceof FullTokenizer)) {
            return false;
        }

        $i = 0;
        while (true) {
            switch ($this->tokenizer->peekAt($i++)) {
                case Tokens::T_COMMENT:
                case Tokens::T_DOC_COMMENT:
                case Tokens::T_ARRAY:
                case Tokens::T_SQUARED_BRACKET_OPEN:
                case Tokens::T_SQUARED_BRACKET_CLOSE:
                case Tokens::T_PARENTHESIS_OPEN:
                case Tokens::T_PARENTHESIS_CLOSE:
                case Tokens::T_COMMA:
                case Tokens::T_DOUBLE_ARROW:
                case Tokens::T_NULL:
                case Tokens::T_TRUE:
                case Tokens::T_FALSE:
                case Tokens::T_LNUMBER:
                case Tokens::T_DNUMBER:
                case Tokens::T_STRING:
                case Tokens::T_EQUAL:
                case Tokens::T_START_HEREDOC:
                case Tokens::T_END_HEREDOC:
                case Tokens::T_ENCAPSED_AND_WHITESPACE:
                    break;

                case Tokens::T_SEMICOLON:
                case Tokenizer::T_EOF:
                    return true;

                default:
                    return false;
            }
        }
    }

    /**
     * Parses the contents of the tokenizer and generates a node tree based on
     * the found tokens.
     *
     * @throws ParserException
     */
    public function parse(): void
    {
        $compilationUnit = $this->tokenizer->getSourceFile();
        if (!$compilationUnit) {
            return;
        }
        $this->compilationUnit = $compilationUnit;

        $compilationUnit
            ->setCache($this->cache)
            ->setId($this->idBuilder->forFile($compilationUnit));

        $filename = $compilationUnit->getFileName();
        $hash = ($filename === 'php://stdin' || !$filename)
            ? md5((string) $compilationUnit->getSource())
            : md5_file($filename);

        $id = $compilationUnit->getId();
        if ($hash && $id && $this->cache->restore($id, $hash)) {
            return;
        }

        if ($id) {
            $this->cache->remove($id);
        }

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
                    $this->docComment = $comment;

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
                    $this->reset(0, $tokenType === Tokens::T_OPEN_TAG_WITH_ECHO);

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
        $id = $this->compilationUnit->getId();
        if ($id) {
            $this->cache->store($id, $this->compilationUnit, $hash ?: null);
        }

        $this->tearDownEnvironment();
    }

    /**
     * Initializes the parser environment.
     *
     * @since 0.9.12
     */
    private function setUpEnvironment(): void
    {
        ini_set('xdebug.max_nesting_level', (string) $this->getMaxNestingLevel());

        $this->useSymbolTable->createScope();

        $this->reset();
    }

    /**
     * Restores the parser environment back.
     *
     * @throws NoActiveScopeException
     * @since 0.9.12
     */
    private function tearDownEnvironment(): void
    {
        ini_restore('xdebug.max_nesting_level');

        $this->useSymbolTable->destroyScope();
    }

    /**
     * Resets some object properties.
     *
     * @param int $modifiers Optional default modifiers.
     * @param bool $echoing True if current statement is echoing (such as after <?=).
     */
    private function reset(int $modifiers = 0, bool $echoing = false): void
    {
        $this->packageName = Builder::DEFAULT_NAMESPACE;
        $this->docComment = null;
        $this->modifiers = $modifiers;
        $this->echoing = $echoing;
    }

    /**
     * Tests if the given token type is a reserved keyword in the supported PHP
     * version.
     *
     * @since 1.1.1
     */
    private function isKeyword(int $tokenType): bool
    {
        return match ($tokenType) {
            Tokens::T_CLASS,
            Tokens::T_TRAIT,
            Tokens::T_CALLABLE,
            Tokens::T_INSTEADOF,
            Tokens::T_INTERFACE => true,
            default => false,
        };
    }

    /**
     * Parses a valid class or interface name and returns the image of the parsed
     * token.
     *
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function parseClassName(): string
    {
        $type = $this->tokenizer->peek();

        if ($this->isClassName($type)) {
            return $this->consumeToken($type)->image;
        }

        throw $this->getUnexpectedNextTokenException();
    }

    /**
     * Will return <b>true</b> if the given <b>$tokenType</b> is a valid class
     * name part.
     *
     * @param int $tokenType The type of a parsed token.
     * @since 0.10.6
     */
    private function isClassName(int $tokenType): bool
    {
        return match ($tokenType) {
            Tokens::T_DIR,
            Tokens::T_USE,
            Tokens::T_GOTO,
            Tokens::T_NULL,
            Tokens::T_NS_C,
            Tokens::T_TRUE,
            Tokens::T_CLONE,
            Tokens::T_FALSE,
            Tokens::T_TRAIT,
            Tokens::T_STRING,
            Tokens::T_TRAIT_C,
            Tokens::T_CALLABLE,
            Tokens::T_INSTEADOF,
            Tokens::T_NAMESPACE,
            Tokens::T_READONLY => true,
            default => false,
        };
    }

    /**
     * Parses a function name from the given tokenizer and returns the string
     * literal representing the function name. If no valid token exists in the
     * token stream, this method will throw an exception.
     *
     * @throws UnexpectedTokenException
     * @throws TokenStreamEndException
     * @since 0.10.0
     */
    private function parseFunctionName(): string
    {
        $tokenType = $this->tokenizer->peek();

        if ($this->isFunctionName($tokenType)) {
            return $this->consumeToken($tokenType)->image;
        }

        throw $this->getUnexpectedNextTokenException();
    }

    protected function isConstantName(int $tokenType): bool
    {
        return match ($tokenType) {
            Tokens::T_CALLABLE,
            Tokens::T_TRAIT,
            Tokens::T_EXTENDS,
            Tokens::T_IMPLEMENTS,
            Tokens::T_STATIC,
            Tokens::T_ABSTRACT,
            Tokens::T_FINAL,
            Tokens::T_PUBLIC,
            Tokens::T_PROTECTED,
            Tokens::T_PRIVATE,
            Tokens::T_CONST,
            Tokens::T_ENDDECLARE,
            Tokens::T_ENDFOR,
            Tokens::T_ENDFOREACH,
            Tokens::T_ENDIF,
            Tokens::T_ENDWHILE,
            Tokens::T_EMPTY,
            Tokens::T_EVAL,
            Tokens::T_LOGICAL_AND,
            Tokens::T_GLOBAL,
            Tokens::T_GOTO,
            Tokens::T_INSTANCEOF,
            Tokens::T_INSTEADOF,
            Tokens::T_INTERFACE,
            Tokens::T_ISSET,
            Tokens::T_NAMESPACE,
            Tokens::T_NEW,
            Tokens::T_LOGICAL_OR,
            Tokens::T_LOGICAL_XOR,
            Tokens::T_TRY,
            Tokens::T_USE,
            Tokens::T_VAR,
            Tokens::T_EXIT,
            Tokens::T_LIST,
            Tokens::T_CLONE,
            Tokens::T_INCLUDE,
            Tokens::T_INCLUDE_ONCE,
            Tokens::T_THROW,
            Tokens::T_ARRAY,
            Tokens::T_PRINT,
            Tokens::T_ECHO,
            Tokens::T_REQUIRE,
            Tokens::T_REQUIRE_ONCE,
            Tokens::T_RETURN,
            Tokens::T_ELSE,
            Tokens::T_ELSEIF,
            Tokens::T_DEFAULT,
            Tokens::T_BREAK,
            Tokens::T_CONTINUE,
            Tokens::T_SWITCH,
            Tokens::T_YIELD,
            Tokens::T_FUNCTION,
            Tokens::T_IF,
            Tokens::T_ENDSWITCH,
            Tokens::T_FINALLY,
            Tokens::T_FOR,
            Tokens::T_FOREACH,
            Tokens::T_DECLARE,
            Tokens::T_CASE,
            Tokens::T_DO,
            Tokens::T_WHILE,
            Tokens::T_AS,
            Tokens::T_CATCH,
            // Tokens::T_DIE,
            Tokens::T_SELF,
            Tokens::T_PARENT,
            Tokens::T_UNSET => true,
            default => $this->isFunctionName($tokenType),
        };
    }

    /**
     * Tests if the give token is a valid function name in the supported PHP
     * version.
     *
     * @since 2.3
     */
    protected function isFunctionName(int $tokenType): bool
    {
        return match ($tokenType) {
            Tokens::T_STRING,
            Tokens::T_NULL,
            Tokens::T_SELF,
            Tokens::T_TRUE,
            Tokens::T_FALSE,
            Tokens::T_PARENT => true,
            default => false,
        };
    }

    /**
     * @throws UnexpectedTokenException
     * @throws TokenStreamEndException
     */
    private function parseMethodName(): string
    {
        $tokenType = $this->tokenizer->peek();

        if ($this->isMethodName($tokenType)) {
            return $this->consumeToken($tokenType)->image;
        }

        throw $this->getUnexpectedNextTokenException();
    }

    private function isMethodName(int $tokenType): bool
    {
        return match ($tokenType) {
            Tokens::T_CLASS => true,
            default => $this->isConstantName($tokenType),
        };
    }

    /**
     * Parses a trait declaration.
     *
     * @since 1.0.0
     */
    private function parseTraitDeclaration(): ASTTrait
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
     */
    private function parseTraitSignature(): ASTTrait
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
     */
    private function parseInterfaceDeclaration(): ASTInterface
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
     * @since 0.10.2
     */
    private function parseInterfaceSignature(): ASTInterface
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
     * @template T of ASTInterface
     * @param T $interface
     * @return T
     * @since 0.10.2
     */
    private function parseOptionalExtendsList(ASTInterface $interface): ASTInterface
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
     */
    private function parseClassDeclaration(): ASTClass
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
     * @since 1.0.0
     */
    private function parseClassSignature(): ASTClass
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
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function parseClassModifiers(): void
    {
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        $validModifiers = [
            Tokens::T_ABSTRACT,
            Tokens::T_FINAL,
            Tokens::T_READONLY,
        ];

        $finalAllowed = true;
        $abstractAllowed = true;

        while (in_array($tokenType, $validModifiers, true)) {
            if ($tokenType === Tokens::T_ABSTRACT) {
                if (!$abstractAllowed) {
                    throw $this->getUnexpectedNextTokenException();
                }
                $finalAllowed = false;
                $this->consumeToken(Tokens::T_ABSTRACT);
                $this->modifiers |= State::IS_EXPLICIT_ABSTRACT;
            } elseif ($tokenType === Tokens::T_FINAL) {
                if (!$finalAllowed) {
                    throw $this->getUnexpectedNextTokenException();
                }
                $abstractAllowed = false;
                $this->consumeToken(Tokens::T_FINAL);
                $this->modifiers |= State::IS_FINAL;
            } elseif ($tokenType === Tokens::T_READONLY) {
                if (!static::READONLY_CLASS_ALLOWED) {
                    throw $this->getUnexpectedNextTokenException();
                }

                $this->consumeToken(Tokens::T_READONLY);
                $this->modifiers |= State::IS_READONLY;
            }

            $tokenType = $this->tokenizer->peek();
        }

        $this->consumeComments();
    }

    /**
     * Parses a parent class declaration for the given <b>$class</b>.
     *
     * @template T of ASTClass
     *
     * @param T $class
     * @return T
     * @since 1.0.0
     */
    private function parseClassExtends(ASTClass $class): ASTClass
    {
        $this->consumeToken(Tokens::T_EXTENDS);
        $this->tokenStack->push();

        $class->setParentClassReference(
            $this->setNodePositionsAndReturn(
                $this->builder->buildAstClassReference(
                    $this->parseQualifiedName(),
                ),
            ),
        );

        return $class;
    }

    /**
     * This method parses a list of interface names as used in the <b>extends</b>
     * part of a interface declaration or in the <b>implements</b> part of a
     * class declaration.
     */
    private function parseInterfaceList(AbstractASTClassOrInterface $abstractType): void
    {
        while (true) {
            $this->tokenStack->push();

            $abstractType->addInterfaceReference(
                $this->setNodePositionsAndReturn(
                    $this->builder->buildAstClassOrInterfaceReference(
                        $this->parseQualifiedName(),
                    ),
                ),
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
     * @return T
     * @throws UnexpectedTokenException
     * @throws TokenStreamEndException
     */
    private function parseTypeBody(AbstractASTClassOrInterface $classOrInterface): AbstractASTClassOrInterface
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
                case Tokens::T_READONLY:
                case Tokens::T_FUNCTION:
                case Tokens::T_VARIABLE:
                case Tokens::T_VAR:
                    $methodOrProperty = $this->parseMethodOrFieldDeclaration(
                        $defaultModifier,
                    );

                    $classOrInterface->addChild($methodOrProperty);

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
                        $token->endColumn,
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
                        $token->endColumn,
                    );

                    $classOrInterface->addChild($comment);

                    $this->docComment = $token->image;

                    break;

                case Tokens::T_USE:
                    $classOrInterface->addChild($this->parseTraitUseStatement());

                    break;

                case Tokens::T_CASE:
                    if (!($classOrInterface instanceof ASTEnum)) {
                        throw new TokenException(
                            'Enum case should be located only inside enum classes',
                        );
                    }

                    $case = $this->parseEnumCase();
                    $case->setEnum($classOrInterface);
                    $classOrInterface->addChild($case);

                    break;

                default:
                    throw $this->getUnexpectedNextTokenException();
            }

            $tokenType = $this->tokenizer->peek();
        }

        throw new TokenStreamEndException($this->tokenizer);
    }

    /**
     * This method will parse a list of modifiers and a following property or
     * method.
     *
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     * @since 0.9.6
     */
    private function parseMethodOrFieldDeclaration(int $modifiers = 0): ASTNode
    {
        $this->tokenStack->push();
        $tokenType = $this->tokenizer->peek();

        while ($tokenType !== Tokenizer::T_EOF) {
            switch ($tokenType) {
                case Tokens::T_PRIVATE:
                    $modifiers |= State::IS_PRIVATE;
                    $modifiers &= ~State::IS_PUBLIC;

                    break;

                case Tokens::T_PROTECTED:
                    $modifiers |= State::IS_PROTECTED;
                    $modifiers &= ~State::IS_PUBLIC;

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

        throw $this->getUnexpectedNextTokenException();
    }

    /**
     * Override this in later PHPParserVersions as necessary
     *
     * @throws UnexpectedTokenException
     */
    private function parseUnknownDeclaration(int $tokenType, int $modifiers): AbstractASTNode
    {
        /**
         * Typed properties
         * https://www.php.net/manual/en/migration74.new-features.php#migration74.new-features.core.typed-properties
         */
        if (in_array($tokenType, $this->possiblePropertyTypes, true)) {
            $type = $this->parseTypeHint();
            $declaration = $this->parseFieldDeclaration();
            $declaration->prependChild($type);
            $declaration->setModifiers($modifiers);

            return $declaration;
        }

        if ($tokenType !== Tokens::T_CONST) {
            throw $this->getUnexpectedNextTokenException();
        }

        $definition = $this->parseConstantDefinition();
        $constantModifiers = $this->getModifiersForConstantDefinition($tokenType, $modifiers);
        $definition->setModifiers($constantModifiers);

        return $definition;
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
     * @since 0.9.6
     */
    private function parseFieldDeclaration(): ASTFieldDeclaration
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
     * @since 0.9.5
     */
    private function parseFunctionOrClosureDeclaration(): ASTCallable
    {
        $this->tokenStack->push();

        $this->consumeToken(Tokens::T_FUNCTION);
        $this->consumeComments();

        $returnReference = $this->parseOptionalByReference();

        if ($this->isNextTokenFormalParameterList()) {
            return $this->setNodePositionsAndReturn(
                $this->parseClosureDeclaration(),
            );
        }

        $docComment = $this->docComment;
        $callable = $this->parseFunctionDeclaration();
        $this->compilationUnit->addChild($callable);

        $callable->setComment($docComment);
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
     * @since 0.9.8
     */
    private function parseOptionalByReference(): bool
    {
        return $this->isNextTokenByReference() && $this->parseByReference();
    }

    /**
     * Tests that the next available token is the returns by reference token.
     *
     * @since 0.9.8
     */
    private function isNextTokenByReference(): bool
    {
        return ($this->tokenizer->peek() === Tokens::T_BITWISE_AND);
    }

    /**
     * This method parses a returns by reference token and returns <b>true</b>.
     */
    private function parseByReference(): bool
    {
        $this->consumeToken(Tokens::T_BITWISE_AND);
        $this->consumeComments();

        return true;
    }

    /**
     * Tests that the next available token is an opening parenthesis.
     *
     * @since 0.9.10
     */
    private function isNextTokenFormalParameterList(): bool
    {
        $this->consumeComments();

        return ($this->tokenizer->peek() === Tokens::T_PARENTHESIS_OPEN);
    }

    /**
     * This method parses a function declaration.
     *
     * @since 0.9.5
     */
    private function parseFunctionDeclaration(): ASTFunction
    {
        $this->consumeComments();

        // Next token must be the function identifier
        $functionName = $this->parseFunctionName();

        $function = $this->builder->buildFunction($functionName);
        $function->setCompilationUnit($this->compilationUnit);
        $function->setId($this->idBuilder->forFunction($function));

        $this->parseCallableDeclaration($function);

        // First check for an existing namespace
        if (isset($this->namespaceName)) {
            $namespaceName = $this->namespaceName;
        } elseif ($this->packageName !== Builder::DEFAULT_NAMESPACE) {
            $namespaceName = $this->packageName;
        } else {
            $namespaceName = $this->globalPackageName;
        }

        $namespace = $this->builder->buildNamespace((string) $namespaceName);
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
     * @since 0.9.5
     */
    private function parseMethodDeclaration(): ASTMethod
    {
        // Read function keyword
        $this->consumeToken(Tokens::T_FUNCTION);
        $this->consumeComments();

        $returnsReference = $this->parseOptionalByReference();

        $methodName = $this->parseMethodName();

        $method = $this->builder->buildMethod($methodName);
        $method->setComment($this->docComment);
        $method->setCompilationUnit($this->compilationUnit);

        $this->classOrInterface?->addMethod($method);

        $this->parseCallableDeclaration($method);
        $this->prepareCallable($method);

        if ($returnsReference) {
            $method->setReturnsReference();
        }

        return $method;
    }

    /**
     * This method parses a PHP 5.3 closure or lambda function.
     *
     * @since 0.9.5
     */
    private function parseClosureDeclaration(): ASTClosure
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
     */
    private function parseCallableDeclaration(AbstractASTCallable $callable): void
    {
        $callable->addChild($this->parseFormalParameters($callable));
        $this->parseCallableDeclarationAddition($callable);

        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_CURLY_BRACE_OPEN) {
            $callable->addChild($this->parseScope());

            return;
        }

        $this->consumeToken(Tokens::T_SEMICOLON);
    }

    /**
     * Extension for version specific additions.
     *
     * @template T of AbstractASTCallable|ASTClosure
     *
     * @param T $callable
     * @return T
     */
    private function parseCallableDeclarationAddition(
        AbstractASTCallable|ASTClosure $callable
    ): AbstractASTCallable|ASTClosure {
        $this->consumeComments();
        if (Tokens::T_COLON !== $this->tokenizer->peek()) {
            return $callable;
        }

        $this->consumeToken(Tokens::T_COLON);

        $type = $this->parseReturnTypeHint();
        $callable->addChild($type);

        return $callable;
    }

    /**
     * Parses a trait use statement.
     *
     * @since 1.0.0
     */
    private function parseTraitUseStatement(): ASTTraitUseStatement
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
            $this->parseOptionalTraitAdaptation($useStatement),
        );
    }

    /**
     * Parses a trait reference instance.
     *
     * @since 1.0.0
     */
    private function parseTraitReference(): ASTTraitReference
    {
        $this->consumeComments();
        $this->tokenStack->push();

        return $this->setNodePositionsAndReturn(
            $this->builder->buildAstTraitReference(
                $this->parseQualifiedName(),
            ),
        );
    }

    /**
     * Parses the adaptation list of the given use statement or simply reads
     * the terminating semicolon, when no adaptation list exists.
     *
     * @template T of ASTTraitUseStatement
     * @param T $useStatement
     * @return T
     * @since 1.0.0
     */
    private function parseOptionalTraitAdaptation(ASTTraitUseStatement $useStatement): ASTTraitUseStatement
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
     * @since 1.0.0
     */
    private function parseTraitAdaptation(): ASTTraitAdaptation
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
     * @return array{string, ?ASTNode}
     * @since 1.0.0
     */
    private function parseTraitMethodReference(): array
    {
        $this->tokenStack->push();

        $qualifiedName = $this->parseQualifiedName();

        $this->consumeComments();

        if (Tokens::T_DOUBLE_COLON === $this->tokenizer->peek()) {
            $traitReference = $this->setNodePositionsAndReturn(
                $this->builder->buildAstTraitReference($qualifiedName),
            );

            $this->consumeToken(Tokens::T_DOUBLE_COLON);
            $this->consumeComments();

            return [$this->parseMethodName(), $traitReference];
        }

        $this->tokenStack->pop();

        return [$qualifiedName, null];
    }

    /**
     * Parses a trait adaptation alias statement.
     *
     * @param array{string, ?ASTNode} $reference Parsed method reference array.
     * @since 1.0.0
     */
    private function parseTraitAdaptationAliasStatement(array $reference): ASTTraitAdaptationAlias
    {
        $stmt = $this->builder->buildAstTraitAdaptationAlias($reference[0]);

        if (isset($reference[1])) {
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
     * @param array{string, ?ASTNode} $reference Parsed method reference array.
     * @throws InvalidStateException
     * @since 1.0.0
     */
    private function parseTraitAdaptationPrecedenceStatement(array $reference): ASTTraitAdaptationPrecedence
    {
        if (!isset($reference[1])) {
            throw new InvalidStateException(
                $this->requireNextToken()->startLine,
                $this->compilationUnit->getFileName() ?? 'unknown',
                'Expecting full qualified trait method name.',
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
     * @throws ParserException
     * @since 0.9.6
     */
    private function parseAllocationExpression(): ASTAllocationExpression
    {
        $this->tokenStack->push();

        $allocation = $this->parseAllocationExpressionValue();

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
     * @return T
     * @throws ParserException
     * @since 2.3
     */
    private function parseAllocationExpressionTypeReference(
        ASTAllocationExpression $allocation
    ): ASTAllocationExpression {
        return $this->parseAnonymousClassDeclaration($allocation)
            ?: $this->parseExpressionTypeReference($allocation, true);
    }

    /**
     * Parse the instanciation for new keyword without arguments.
     *
     * @throws ParserException
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function parseAllocationExpressionValue(): ASTAllocationExpression
    {
        $token = $this->consumeToken(Tokens::T_NEW);
        $this->consumeComments();
        $inParentheses = ($this->tokenizer->peek() === Tokens::T_PARENTHESIS_OPEN);

        if ($inParentheses) {
            $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);
        }

        $allocation = $this->builder->buildAstAllocationExpression($token->image);

        if (!$inParentheses) {
            return $this->parseAllocationExpressionTypeReference($allocation);
        }

        $expression = $this->parseOptionalExpression();

        if (!$expression) {
            throw $this->getUnexpectedNextTokenException();
        }

        $allocation->addChild($expression);
        $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);

        return $allocation;
    }

    /**
     * Parses a eval-expression node.
     *
     * @since 0.9.12
     */
    private function parseEvalExpression(): ASTEvalExpression
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
     * @since 0.9.12
     */
    private function parseExitExpression(): ASTExitExpression
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
     * @since 0.9.12
     */
    private function parseCloneExpression(): ASTCloneExpression
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
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function ensureTokenIsListUnpackingOpening(int $tokenType, ?Token $unexpectedToken = null): void
    {
        if (!$this->isListUnpacking($tokenType)) {
            throw $this->getUnexpectedTokenException($unexpectedToken ?: $this->tokenizer->prevToken());
        }
    }

    /**
     * This method parses a single list-statement node.
     *
     * @since 0.9.12
     */
    private function parseListExpression(): ASTListExpression
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
     */
    private function parseListSlotExpression(): ASTNode
    {
        $startToken = $this->tokenizer->currentToken();
        $node = $this->parseOptionalExpression();

        if ($node && $this->tokenizer->peek() === Tokens::T_DOUBLE_ARROW) {
            $this->consumeComments();
            $this->consumeToken(Tokens::T_DOUBLE_ARROW);
            $this->consumeComments();

            return in_array($this->tokenizer->peek(), [Tokens::T_LIST, Tokens::T_SQUARED_BRACKET_OPEN], true)
                ? $this->parseListExpression()
                : $this->parseVariableOrConstantOrPrimaryPrefix();
        }

        return $node ?: $this->parseVariableOrConstantOrPrimaryPrefix();
    }

    /**
     * Parses a include-expression node.
     *
     * @since 0.9.12
     */
    private function parseIncludeExpression(): ASTIncludeExpression
    {
        $expr = $this->builder->buildAstIncludeExpression();

        return $this->parseRequireOrIncludeExpression($expr, Tokens::T_INCLUDE);
    }

    /**
     * Parses a include_once-expression node.
     *
     * @since 0.9.12
     */
    private function parseIncludeOnceExpression(): ASTIncludeExpression
    {
        $expr = $this->builder->buildAstIncludeExpression();
        $expr->setOnce();

        return $this->parseRequireOrIncludeExpression($expr, Tokens::T_INCLUDE_ONCE);
    }

    /**
     * Parses a require-expression node.
     *
     * @since 0.9.12
     */
    private function parseRequireExpression(): ASTRequireExpression
    {
        $expr = $this->builder->buildAstRequireExpression();

        return $this->parseRequireOrIncludeExpression($expr, Tokens::T_REQUIRE);
    }

    /**
     * Parses a require_once-expression node.
     *
     * @since 0.9.12
     */
    private function parseRequireOnceExpression(): ASTRequireExpression
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
     * @param T $expr
     * @return T
     * @since 0.9.12
     */
    private function parseRequireOrIncludeExpression(ASTExpression $expr, int $type): ASTExpression
    {
        $this->tokenStack->push();

        $this->consumeToken($type);
        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_PARENTHESIS_OPEN) {
            $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);
            $child = $this->parseOptionalExpression();
            if ($child) {
                $expr->addChild($child);
            }
            $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);
        } elseif ($child = $this->parseOptionalExpression()) {
            $expr->addChild($child);
        }

        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * Parses a cast-expression node.
     *
     * @since 0.10.0
     */
    private function parseCastExpression(): ASTCastExpression
    {
        $token = $this->consumeToken($this->tokenizer->peek());

        $expr = $this->builder->buildAstCastExpression($token->image);
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $expr;
    }

    /**
     * This method will parse an increment-expression. Depending on the previous node
     * this can be a {@link ASTPostIncrementExpression} or {@link ASTPostfixExpression}.
     *
     * @param list<ASTNode> $expressions List of previous parsed expression nodes.
     * @return list<ASTNode>
     * @since 0.10.0
     */
    private function parseIncrementExpression(array $expressions): array
    {
        $expression = end($expressions);
        if ($expression && $this->isReadWriteVariable($expression)) {
            array_pop($expressions);

            $expressions[] = $this->parsePostIncrementExpression($expression);

            return $expressions;
        }

        $expressions[] = $this->parsePreIncrementExpression();

        return $expressions;
    }

    /**
     * Parses a post increment-expression and adds the given child to that node.
     *
     * @param ASTNode $child The child expression node.
     * @since 0.10.0
     */
    private function parsePostIncrementExpression(ASTNode $child): ASTPostfixExpression
    {
        $token = $this->consumeToken(Tokens::T_INC);

        $expr = $this->builder->buildAstPostfixExpression($token->image);
        $expr->addChild($child);
        $expr->configureLinesAndColumns(
            $child->getStartLine(),
            $token->endLine,
            $child->getStartColumn(),
            $token->endColumn,
        );

        return $expr;
    }

    /**
     * Parses a pre increment-expression and adds the given child to that node.
     *
     * @since 0.10.0
     */
    private function parsePreIncrementExpression(): ASTPreIncrementExpression
    {
        $token = $this->consumeToken(Tokens::T_INC);

        $expr = $this->builder->buildAstPreIncrementExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $expr;
    }

    /**
     * This method will parse an decrement-expression. Depending on the previous node
     * this can be a {@link ASTPostDecrementExpression} or {@link ASTPostfixExpression}.
     *
     * @param list<ASTNode> $expressions List of previous parsed expression nodes.
     * @return list<ASTNode>
     * @since 0.10.0
     */
    private function parseDecrementExpression(array $expressions): array
    {
        $expression = end($expressions);
        if ($expression && $this->isReadWriteVariable($expression)) {
            array_pop($expressions);

            $expressions[] = $this->parsePostDecrementExpression($expression);

            return $expressions;
        }

        $expressions[] = $this->parsePreDecrementExpression();

        return $expressions;
    }

    /**
     * Parses a post decrement-expression and adds the given child to that node.
     *
     * @param ASTNode $child The child expression node.
     * @since 0.10.0
     */
    private function parsePostDecrementExpression(ASTNode $child): ASTPostfixExpression
    {
        $token = $this->consumeToken(Tokens::T_DEC);

        $expr = $this->builder->buildAstPostfixExpression($token->image);
        $expr->addChild($child);
        $expr->configureLinesAndColumns(
            $child->getStartLine(),
            $token->endLine,
            $child->getStartColumn(),
            $token->endColumn,
        );

        return $expr;
    }

    /**
     * Parses a pre decrement-expression and adds the given child to that node.
     *
     * @since 0.10.0
     */
    private function parsePreDecrementExpression(): ASTPreDecrementExpression
    {
        $token = $this->consumeToken(Tokens::T_DEC);

        $expr = $this->builder->buildAstPreDecrementExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
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
     * @param AbstractASTNode $node The parent/context node instance.
     * @since 0.9.12
     */
    private function parseOptionalIndexExpression(AbstractASTNode $node): AbstractASTNode
    {
        $this->consumeComments();

        return match ($this->tokenizer->peek()) {
            Tokens::T_CURLY_BRACE_OPEN => $this->parseStringIndexExpression($node),
            Tokens::T_SQUARED_BRACKET_OPEN => $this->parseArrayIndexExpression($node),
            default => $node,
        };
    }

    /**
     * Parses an index expression as it is valid to access elements in a php
     * string or array.
     *
     * @param ASTNode $node The context source node.
     * @param ASTExpression $expr The concrete index expression.
     * @param int $open The open token type.
     * @param int $close The close token type.
     * @since 0.9.12
     */
    private function parseIndexExpression(
        ASTNode $node,
        ASTExpression $expr,
        int $open,
        int $close,
    ): AbstractASTNode {
        $this->consumeToken($open);

        if (($child = $this->parseOptionalExpression()) !== null) {
            $expr->addChild($child);
        }

        $token = $this->consumeToken($close);

        $expr->configureLinesAndColumns(
            $node->getStartLine(),
            $token->endLine,
            $node->getStartColumn(),
            $token->endColumn,
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
     * @param AbstractASTNode $node The context source node.
     * @since 0.9.12
     */
    private function parseArrayIndexExpression(AbstractASTNode $node): AbstractASTNode
    {
        $expr = $this->builder->buildAstArrayIndexExpression();
        $expr->addChild($node);

        return $this->parseIndexExpression(
            $node,
            $expr,
            Tokens::T_SQUARED_BRACKET_OPEN,
            Tokens::T_SQUARED_BRACKET_CLOSE,
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
     * @param AbstractASTNode $node The context source node.
     * @since 0.9.12
     */
    private function parseStringIndexExpression(AbstractASTNode $node): AbstractASTNode
    {
        $expr = $this->builder->buildAstStringIndexExpression();
        $expr->addChild($node);

        return $this->parseIndexExpression(
            $node,
            $expr,
            Tokens::T_CURLY_BRACE_OPEN,
            Tokens::T_CURLY_BRACE_CLOSE,
        );
    }

    /**
     * This method checks if the next available token starts an arguments node.
     *
     * @since 0.9.8
     */
    private function isNextTokenArguments(): bool
    {
        $this->consumeComments();

        return $this->tokenizer->peek() === Tokens::T_PARENTHESIS_OPEN;
    }

    /**
     * This method configures the given node with its start and end positions.
     *
     * @template T of ASTNode
     *
     * @param T $node
     * @param array<Token> $tokens
     * @return T
     * @since 0.9.8
     */
    protected function setNodePositionsAndReturn(ASTNode $node, array &$tokens = []): ASTNode
    {
        $tokens = $this->tokenStack->pop();
        if (count($tokens) > 1) {
            $tokens = $this->stripTrailingComments($tokens);
        }

        $end = $tokens[count($tokens) - 1];
        $start = $tokens[0];

        $node->configureLinesAndColumns(
            $start->startLine,
            $end->endLine,
            $start->startColumn,
            $end->endColumn,
        );

        return $node;
    }

    /**
     * Strips all trailing comments from the given token stream.
     *
     * @template T of Token[]
     * @param T $tokens Original token stream.
     * @return T
     * @since 1.0.0
     */
    private function stripTrailingComments(array $tokens): array
    {
        $comments = [Tokens::T_COMMENT, Tokens::T_DOC_COMMENT];

        while ($tokens && in_array(end($tokens)->type, $comments, true)) {
            array_pop($tokens);
            if (count($tokens) === 1) {
                break;
            }
        }

        /** @var T */
        $tokens = array_values($tokens);

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
     * @since 0.9.6
     */
    private function parseInstanceOfExpression(): ASTInstanceOfExpression
    {
        // Consume the "instanceof" keyword and strip comments
        $token = $this->consumeToken(Tokens::T_INSTANCEOF);

        return $this->parseExpressionTypeReference(
            $this->builder->buildAstInstanceOfExpression($token->image),
            false,
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
     *
     * //  -----------------------
     * if (isset($foo['bar'], BAR['baz']['foo'])) {
     * //  -----------------------
     * }
     * </code>
     *
     * @since 0.9.12
     */
    private function parseIssetExpression(): ASTIssetExpression
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
            $stopToken->endColumn,
        );

        return $expr;
    }

    /**
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function parseStandAloneExpressionTypeReference(int $tokenType): ASTClassOrInterfaceReference
    {
        return match ($tokenType) {
            Tokens::T_SELF => $this->parseSelfReference($this->consumeToken(Tokens::T_SELF)),
            Tokens::T_PARENT => $this->parseParentReference($this->consumeToken(Tokens::T_PARENT)),
            Tokens::T_STATIC => $this->parseStaticReference($this->consumeToken(Tokens::T_STATIC)),
            default => throw $this->getUnexpectedNextTokenException(),
        };
    }

    /**
     * @throws ParserException
     */
    private function parseStandAloneExpressionType(bool $classRef): AbstractASTNode
    {
        // Peek next token and look for a static type identifier
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        return match ($tokenType) {
            // TODO: Parse variable or Member Primary Prefix + Property Postfix
            Tokens::T_DOLLAR,
            Tokens::T_VARIABLE => $this->parseVariableOrFunctionPostfixOrMemberPrimaryPrefix(),
            Tokens::T_SELF,
            Tokens::T_PARENT,
            Tokens::T_STATIC => $this->parseStandAloneExpressionTypeReference($tokenType),
            default => $this->parseClassOrInterfaceReference($classRef),
        };
    }

    /**
     * This method parses a type identifier as it is used in expression nodes
     * like {@link ASTInstanceOfExpression} or an object
     * allocation node like {@link ASTAllocationExpression}.
     *
     * @template T of AbstractASTNode
     *
     * @param T $expr
     * @return T
     * @throws ParserException
     */
    private function parseExpressionTypeReference(AbstractASTNode $expr, bool $classRef): AbstractASTNode
    {
        $expr->addChild(
            $this->parseOptionalMemberPrimaryPrefix(
                $this->parseOptionalStaticMemberPrimaryPrefix(
                    $this->parseStandAloneExpressionType($classRef),
                ),
            ),
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
     * @since 0.9.8
     */
    private function parseConditionalExpression(): ASTConditionalExpression
    {
        $this->tokenStack->push();
        $this->consumeToken(Tokens::T_QUESTION_MARK);

        $expr = $this->builder->buildAstConditionalExpression();
        if (($child = $this->parseOptionalExpression()) !== null) {
            $expr->addChild($child);
        }

        $this->consumeToken(Tokens::T_COLON);

        $expr->addChild($this->parseExpression());

        return $this->setNodePositionsAndReturn($expr);
    }

    /**
     * This method parses a shift left expression node.
     *
     * @since 1.0.1
     */
    private function parseShiftLeftExpression(): ASTShiftLeftExpression
    {
        $token = $this->consumeToken(Tokens::T_SL);

        $expr = $this->builder->buildAstShiftLeftExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $expr;
    }

    /**
     * This method parses a shift right expression node.
     *
     * @since 1.0.1
     */
    private function parseShiftRightExpression(): ASTShiftRightExpression
    {
        $token = $this->consumeToken(Tokens::T_SR);

        $expr = $this->builder->buildAstShiftRightExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $expr;
    }

    /**
     * This method parses a boolean and-expression.
     *
     * @since 0.9.8
     */
    private function parseBooleanAndExpression(): ASTBooleanAndExpression
    {
        $token = $this->consumeToken(Tokens::T_BOOLEAN_AND);

        $expr = $this->builder->buildAstBooleanAndExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $expr;
    }

    /**
     * This method parses a boolean or-expression.
     *
     * @since 0.9.8
     */
    private function parseBooleanOrExpression(): ASTBooleanOrExpression
    {
        $token = $this->consumeToken(Tokens::T_BOOLEAN_OR);

        $expr = $this->builder->buildAstBooleanOrExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $expr;
    }

    /**
     * This method parses a logical <b>and</b>-expression.
     *
     * @since 0.9.8
     */
    private function parseLogicalAndExpression(): ASTLogicalAndExpression
    {
        $token = $this->consumeToken(Tokens::T_LOGICAL_AND);

        $expr = $this->builder->buildAstLogicalAndExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $expr;
    }

    /**
     * This method parses a logical <b>or</b>-expression.
     *
     * @since 0.9.8
     */
    private function parseLogicalOrExpression(): ASTLogicalOrExpression
    {
        $token = $this->consumeToken(Tokens::T_LOGICAL_OR);

        $expr = $this->builder->buildAstLogicalOrExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $expr;
    }

    /**
     * This method parses a logical <b>xor</b>-expression.
     *
     * @since 0.9.8
     */
    private function parseLogicalXorExpression(): ASTLogicalXorExpression
    {
        $token = $this->consumeToken(Tokens::T_LOGICAL_XOR);

        $expr = $this->builder->buildAstLogicalXorExpression();
        $expr->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $expr;
    }

    /**
     * Parses a class or interface reference node.
     *
     * @param bool $classReference Force a class reference.
     * @since 0.9.8
     */
    private function parseClassOrInterfaceReference(bool $classReference): ASTClassOrInterfaceReference
    {
        $this->tokenStack->push();

        return $this->setNodePositionsAndReturn(
            $this->builder->buildAstNeededReference(
                $this->parseQualifiedName(),
                $classReference,
            ),
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
     * @param T $node
     * @return T
     * @throws TokenStreamEndException
     * @since 0.9.6
     */
    private function parseBraceExpression(
        ASTNode $node,
        Token $start,
        int $closeToken,
        ?int $separatorToken = null,
    ): AbstractASTNode {
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
            $end->endColumn,
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
     * @return T
     * @since 0.9.12
     */
    private function parseStatementBody(ASTStatement $stmt): ASTStatement
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
     * @throws ParserException
     * @since 0.9.12
     */
    private function parseRegularScope(): ASTScopeStatement
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
     * @since 0.10.0
     */
    private function parseAlternativeScope(): ASTScopeStatement
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
     * @throws ParserException
     * @since 0.10.0
     */
    private function parseScopeStatements(): ASTScopeStatement
    {
        $scope = $this->builder->buildAstScopeStatement();
        while (($child = $this->parseOptionalStatement()) !== null) {
            $scope->addChild($child);
        }

        return $scope;
    }

    /**
     * Parses the termination of a scope statement that uses PHP's laternative
     * syntax format.
     *
     * @since 0.10.0
     */
    private function parseOptionalAlternativeScopeTermination(): void
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
     * @since 0.10.0
     */
    private function isAlternativeScopeTermination(int $tokenType): bool
    {
        return in_array(
            $tokenType,
            [
                Tokens::T_ENDDECLARE,
                Tokens::T_ENDFOR,
                Tokens::T_ENDFOREACH,
                Tokens::T_ENDIF,
                Tokens::T_ENDSWITCH,
                Tokens::T_ENDWHILE,
            ],
            true,
        );
    }

    /**
     * Parses a series of tokens that represent an alternative scope termination.
     *
     * @param int $tokenType The token type identifier.
     * @since 0.10.0
     */
    private function parseAlternativeScopeTermination(int $tokenType): void
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
     * @template T of AbstractASTNode
     *
     * @param T $exprList
     * @return T
     * @since 1.0.0
     */
    private function parseExpressionList(AbstractASTNode $exprList): AbstractASTNode
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
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function addChildToList(AbstractASTNode $exprList, ASTNode $expr): bool
    {
        $exprList->addChild($expr);

        $this->consumeComments();

        if ($this->tokenizer->peek() !== Tokens::T_COMMA) {
            return false;
        }

        if ($exprList instanceof ASTArguments && !$exprList->acceptsMoreArguments()) {
            throw $this->getUnexpectedNextTokenException();
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
     * @throws InvalidStateException
     * @since 1.0.1
     */
    private function parseExpression(): ASTNode
    {
        if (null === ($expr = $this->parseOptionalExpression())) {
            $token = $this->consumeToken($this->tokenizer->peek());

            throw new InvalidStateException(
                $token->startLine,
                $this->compilationUnit->getFileName() ?? 'unknown',
                'Mandatory expression expected.',
            );
        }

        return $expr;
    }

    /**
     * This method optionally parses an expression node and returns it. When no
     * expression was found this method will return <b>null</b>.
     *
     * @throws ParserException
     * @since 0.9.6
     */
    private function parseOptionalExpression(): ?ASTNode
    {
        $expressions = [];

        while (($tokenType = $this->tokenizer->peek()) !== Tokenizer::T_EOF) {
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

                case $this->isArrayStartDelimiter():
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
                        Tokens::T_CURLY_BRACE_CLOSE,
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
                    $expressions = $this->parseDecrementExpression($expressions);

                    break;

                case Tokens::T_INC:
                    $expressions = $this->parseIncrementExpression($expressions);

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
                    $expression = $this->parseConstant();
                    if ($expression) {
                        $expressions[] = $expression;
                    }

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
                    $expression = array_pop($expressions);
                    if ($expression) {
                        $expressions[] = $this->parseAssignmentExpression($expression);
                    }

                    break;

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
                        $token->endColumn,
                    );

                    $expressions[] = $expr;

                    break;

                case Tokens::T_ELLIPSIS:
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
                        $token->endColumn,
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
                        $token->endColumn,
                    );

                    $expressions[] = $expr;

                    break;

                case Tokens::T_YIELD:
                    $expressions[] = $this->parseYield(false);

                    break;

                default:
                    $expression = $this->parseOptionalExpressionForVersion();
                    if ($expression) {
                        $expressions[] = $expression;
                    }

                    break;
            }
        }

        $expressions = $this->reduceUnaryExpression($expressions);

        $count = count($expressions);
        if ($count === 0) {
            return null;
        }
        if ($count === 1) {
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
            $expressions[$count - 1]->getEndColumn(),
        );

        return $expr;
    }

    /**
     * This method will be called when the base parser cannot handle an expression
     * in the base version. In this method you can implement version specific
     * expressions.
     *
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     * @since 2.2
     */
    private function parseOptionalExpressionForVersion(): ?ASTNode
    {
        $this->consumeComments();
        $nextTokenType = $this->tokenizer->peek();

        switch ($nextTokenType) {
            case Tokens::T_NULLSAFE_OBJECT_OPERATOR:
            case Tokens::T_SPACESHIP:
            case Tokens::T_COALESCE:
            case Tokens::T_POW:
                $token = $this->consumeToken($nextTokenType);

                $expr = $this->builder->buildAstExpression($token->image);
                $expr->configureLinesAndColumns(
                    $token->startLine,
                    $token->endLine,
                    $token->startColumn,
                    $token->endColumn
                );

                return $expr;

            case Tokens::T_TRAIT_C:
                return $this->parseConstant();

            default:
                throw $this->getUnexpectedNextTokenException();
        }
    }

    /**
     * Reduces all unary-expressions in the given expression list.
     *
     * @template T of list<ASTNode>
     * @param T $expressions Unprepared input array with parsed expression nodes found in the source tree.
     * @return T
     * @since 0.10.0
     */
    private function reduceUnaryExpression(array $expressions): array
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
                    $child->getEndColumn(),
                );

                unset($expressions[$i + 1]);
            }
        }

        /** @var T */
        $values = array_values($expressions);

        return $values;
    }

    /**
     * This method parses a switch statement.
     *
     * @throws ParserException
     * @since 0.9.8
     */
    private function parseSwitchStatement(): ASTSwitchStatement
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
     * @template T of ASTSwitchStatement
     * @param T $switch The parent switch stmt.
     * @return T
     * @throws ParserException
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     * @since 0.9.8
     */
    private function parseSwitchStatementBody(ASTSwitchStatement $switch): ASTSwitchStatement
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

        throw $this->getUnexpectedNextTokenException();
    }

    /**
     * This method parses a case label of a switch statement.
     *
     * @throws ParserException
     * @since 0.9.8
     */
    private function parseSwitchLabel(): ASTSwitchLabel
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
     * @since 0.9.8
     */
    private function parseSwitchLabelDefault(): ASTSwitchLabel
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
     * @template T of ASTSwitchLabel
     * @param T $label The context switch label.
     * @return T
     * @throws ParserException
     * @throws TokenStreamEndException
     */
    private function parseSwitchLabelBody(ASTSwitchLabel $label): ASTSwitchLabel
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
     * @since 0.9.12
     */
    private function parseStatementTermination(array $allowedTerminationTokens = []): void
    {
        $this->consumeComments();
        $this->echoing = false;

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
     * @throws ParserException
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     * @since 0.9.12
     */
    private function parseTryStatement(): ASTTryStatement
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_TRY);

        $stmt = $this->builder->buildAstTryStatement($token->image);
        $stmt->addChild($this->parseRegularScope());

        $this->consumeComments();

        if (false === in_array($this->tokenizer->peek(), [Tokens::T_CATCH, Tokens::T_FINALLY], true)) {
            throw $this->getUnexpectedNextTokenException();
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
     * @since 0.9.12
     */
    private function parseThrowStatement(array $allowedTerminationTokens = []): ASTThrowStatement
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
     * @since 0.9.12
     */
    private function parseGotoStatement(): ASTGotoStatement
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
     * @since 0.9.12
     */
    private function parseLabelStatement(): ASTLabelStatement
    {
        $this->tokenStack->push();

        $token = $this->consumeToken(Tokens::T_STRING);
        $this->consumeComments();
        $this->consumeToken(Tokens::T_COLON);

        return $this->setNodePositionsAndReturn(
            $this->builder->buildAstLabelStatement($token->image),
        );
    }

    /**
     * This method parses a global-statement.
     *
     * @since 0.9.12
     */
    private function parseGlobalStatement(): ASTGlobalStatement
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
     * @since 0.9.12
     */
    private function parseUnsetStatement(): ASTUnsetStatement
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
     * @since 0.9.8
     */
    private function parseCatchStatement(): ASTCatchStatement
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
     */
    private function parseCatchVariable(ASTCatchStatement $stmt): void
    {
        if ($this->tokenizer->peek() !== Tokens::T_VARIABLE) {
            return;
        }

        $stmt->addChild($this->parseVariable());

        $this->consumeComments();
    }

    /**
     * This method parses class references in catch statement.
     *
     * @param ASTCatchStatement $stmt The owning catch statement.
     */
    private function parseCatchExceptionClass(ASTCatchStatement $stmt): void
    {
        do {
            $repeat = false;
            $stmt->addChild(
                $this->builder->buildAstClassOrInterfaceReference(
                    $this->parseQualifiedName()
                )
            );

            if (Tokens::T_BITWISE_OR === $this->tokenizer->peek()) {
                $this->consumeToken(Tokens::T_BITWISE_OR);
                $repeat = true;
            }
        } while ($repeat);
    }

    /**
     * This method parses a finally-statement.
     *
     * @since 2.0.0
     */
    private function parseFinallyStatement(): ASTFinallyStatement
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
     * @since 0.9.8
     */
    private function parseIfStatement(): ASTIfStatement
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
     * @since 0.9.8
     */
    private function parseElseIfStatement(): ASTElseIfStatement
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
     * @template T of ASTStatement
     * @param T $stmt The owning if/elseif statement.
     * @return T
     * @since 0.9.12
     */
    private function parseOptionalElseOrElseIfStatement(ASTStatement $stmt): ASTStatement
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
     * @throws ParserException
     * @since 0.9.8
     */
    private function parseForStatement(): ASTForStatement
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
     * @since 0.9.8
     */
    private function parseForInit(): ?ASTForInit
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
     * @throws ParserException
     * @since 0.9.12
     */
    private function parseForExpression(): ?ASTNode
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
     * @since 0.9.12
     */
    private function parseForUpdate(): ?ASTForUpdate
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
     * @since 2.6.0
     */
    private function isListUnpacking(?int $tokenType = null): bool
    {
        return in_array($tokenType ?: $this->tokenizer->peek(), [Tokens::T_LIST, Tokens::T_SQUARED_BRACKET_OPEN], true);
    }

    /**
     * Get the parsed list of a foreach statement children.
     *
     * @return ASTNode[]
     */
    private function parseForeachChildren(): array
    {
        if ($this->tokenizer->peek() === Tokens::T_BITWISE_AND) {
            return [$this->parseVariableOrMemberByReference()];
        }

        if ($this->isListUnpacking()) {
            return [$this->parseListExpression()];
        }

        $children = [
            $this->parseVariableOrConstantOrPrimaryPrefix(),
        ];

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
     * @since 0.9.8
     */
    private function parseForeachStatement(): ASTForeachStatement
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
            $this->parseStatementBody($foreach),
        );
    }

    /**
     * This method parses a single while-statement node.
     *
     * @since 0.9.8
     */
    private function parseWhileStatement(): ASTWhileStatement
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_WHILE);

        $stmt = $this->builder->buildAstWhileStatement($token->image);
        $stmt->addChild($this->parseParenthesisExpression());

        return $this->setNodePositionsAndReturn(
            $this->parseStatementBody($stmt),
        );
    }

    /**
     * This method parses a do/while-statement.
     *
     * @since 0.9.12
     */
    private function parseDoWhileStatement(): ASTDoWhileStatement
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
     * @since 0.10.0
     */
    private function parseDeclareStatement(): ASTDeclareStatement
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
     * @template T of ASTDeclareStatement
     * @param T $stmt The declare statement that is the owner of this list.
     * @return T
     * @since 0.10.0
     */
    private function parseDeclareList(ASTDeclareStatement $stmt): ASTDeclareStatement
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

            if ($value) {
                $stmt->addValue($name, $value);
            }

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
     * @since 2.7.0
     */
    private function buildReturnStatement(Token $token): ASTReturnStatement
    {
        $stmt = $this->builder->buildAstReturnStatement($token->image);

        if (($expr = $this->parseOptionalExpression()) !== null) {
            $stmt->addChild($expr);
        }

        return $stmt;
    }

    /**
     * This method parses a single return-statement node.
     *
     * @since 0.9.12
     */
    private function parseReturnStatement(): ASTReturnStatement
    {
        $this->tokenStack->push();

        $stmt = $this->buildReturnStatement(
            $this->consumeToken(Tokens::T_RETURN),
        );

        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a break-statement node.
     *
     * @since 0.9.12
     */
    private function parseBreakStatement(): ASTBreakStatement
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_BREAK);

        $stmt = $this->builder->buildAstBreakStatement($token->image);
        if (($expr = $this->parseOptionalExpression()) !== null) {
            $stmt->addChild($expr);
        }
        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a continue-statement node.
     *
     * @since 0.9.12
     */
    private function parseContinueStatement(): ASTContinueStatement
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_CONTINUE);

        $stmt = $this->builder->buildAstContinueStatement($token->image);
        if (($expr = $this->parseOptionalExpression()) !== null) {
            $stmt->addChild($expr);
        }
        $this->parseStatementTermination();

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * This method parses a echo-statement node.
     *
     * @since 0.9.12
     */
    private function parseEchoStatement(): ASTEchoStatement
    {
        $this->tokenStack->push();
        $token = $this->consumeToken(Tokens::T_ECHO);

        $stmt = $this->parseExpressionList(
            $this->builder->buildAstEchoStatement($token->image),
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
     * @since 1.0.0
     */
    private function parseParenthesisExpressionOrPrimaryPrefix(): ASTNode
    {
        return $this->parseParenthesisExpressionOrPrimaryPrefixForVersion(
            $this->parseParenthesisExpression(),
        );
    }

    private function parseParenthesisExpressionOrPrimaryPrefixForVersion(ASTExpression $expr): AbstractASTNode
    {
        $this->consumeComments();

        if (Tokens::T_DOUBLE_COLON === $this->tokenizer->peek()) {
            return $this->parseStaticMemberPrimaryPrefix($expr->getChild(0));
        }

        if ($this->isNextTokenObjectOperator()) {
            $node = count($expr->getChildren()) === 0 ? $expr : $expr->getChild(0);

            return $this->parseMemberPrimaryPrefix($node);
        }

        return $expr;
    }

    private function isNextTokenObjectOperator(): bool
    {
        return in_array($this->tokenizer->peek(), [
            Tokens::T_OBJECT_OPERATOR,
            Tokens::T_NULLSAFE_OBJECT_OPERATOR,
        ], true);
    }

    /**
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function consumeObjectOperatorToken(): Token
    {
        return $this->consumeToken(
            $this->tokenizer->peek() === Tokens::T_NULLSAFE_OBJECT_OPERATOR
                ? Tokens::T_NULLSAFE_OBJECT_OPERATOR
                : Tokens::T_OBJECT_OPERATOR
        );
    }

    /**
     * Parses any expression that is surrounded by an opening and a closing
     * parenthesis
     *
     * @since 0.9.8
     */
    private function parseParenthesisExpression(): ASTExpression
    {
        $this->tokenStack->push();
        $this->consumeComments();

        $expr = $this->builder->buildAstExpression();
        $expr = $this->parseBraceExpression(
            $expr,
            $this->consumeToken(Tokens::T_PARENTHESIS_OPEN),
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_COMMA,
        );

        while ($this->tokenizer->peek() === Tokens::T_PARENTHESIS_OPEN) {
            $function = $this->builder->buildAstFunctionPostfix($expr->getImage());
            $function->addChild($expr);
            $function->addChild($this->parseArguments());
            $expr = $function;
        }

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
     * @since 0.9.6
     */
    private function parseMemberPrefixOrFunctionPostfix(): ASTNode
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
     * @param AbstractASTNode $node The previously parsed node.
     * @return AbstractASTNode The original input node or this node wrapped with a function postfix instance.
     * @since 1.0.0
     */
    private function parseOptionalFunctionPostfix(AbstractASTNode $node): AbstractASTNode
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
     * @throws ParserException
     * @since 0.9.6
     */
    private function parseFunctionPostfix(ASTNode $node): AbstractASTNode
    {
        $image = $this->extractPostfixImage($node);

        $function = $this->builder->buildAstFunctionPostfix($image);
        $function->addChild($node);

        if (!($node instanceof ASTIdentifier) || $node->getImageWithoutNamespace() !== 'match') {
            $function->addChild($this->parseArguments());

            return $this->parseOptionalMemberPrimaryPrefix(
                $this->parseOptionalIndexExpression($function),
            );
        }

        $this->consumeComments();

        $this->tokenStack->push();

        $function->addChild(
            $this->parseArgumentsParenthesesContent(
                $this->builder->buildAstMatchArgument()
            )
        );

        $this->consumeComments();
        $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);

        $matchBlock = $this->builder->buildAstMatchBlock();

        while ($this->tokenizer->peek() !== Tokens::T_CURLY_BRACE_CLOSE) {
            $matchBlock->addChild($this->parseMatchEntry());

            $this->consumeComments();

            if ($this->tokenizer->peek() === Tokens::T_COMMA) {
                $this->consumeToken(Tokens::T_COMMA);
                $this->consumeComments();
            }
        }

        $this->consumeToken(Tokens::T_CURLY_BRACE_CLOSE);

        $function->addChild($matchBlock);

        return $function;
    }

    /**
     * This method parses a PHP version specific identifier for method and
     * property postfix expressions.
     *
     * @since 1.0.0
     */
    private function parsePostfixIdentifier(): ASTNode
    {
        $tokenType = $this->tokenizer->peek();
        if ($this->isConstantName($tokenType)) {
            return $this->parseOptionalIndexExpression($this->parseLiteral());
        }

        $node = match ($tokenType) {
            Tokens::T_CURLY_BRACE_OPEN => $this->parseCompoundExpression(),
            Tokens::T_STRING => $this->parseLiteral(),
            default => $this->parseCompoundVariableOrVariableVariableOrVariable(),
        };

        return $this->parseOptionalIndexExpression($node);
    }

    /**
     * This method parses an optional member primary expression. It will parse
     * the primary expression when an object operator can be found at the actual
     * token stream position. Otherwise this method simply returns the input
     * {@link ASTNode} instance.
     *
     * @param AbstractASTNode $node This node represents primary prefix
     *                              left expression. It will be the first child of the parsed member
     *                              primary expression.
     * @throws ParserException
     * @since 0.9.6
     */
    private function parseOptionalMemberPrimaryPrefix(AbstractASTNode $node): AbstractASTNode
    {
        $this->consumeComments();

        if (Tokens::T_DOUBLE_COLON === $this->tokenizer->peek()) {
            return $this->parseStaticMemberPrimaryPrefix($node);
        }

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
     * @throws ParserException
     * @since 0.9.6
     */
    private function parseMemberPrimaryPrefix(ASTNode $node): AbstractASTNode
    {
        // Consume double colon and optional comments
        $token = $this->consumeObjectOperatorToken();

        $prefix = $this->builder->buildAstMemberPrimaryPrefix($token->image);
        $prefix->addChild($node);

        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        switch ($tokenType) {
            case $this->isMethodName($tokenType):
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
                $this->parseOptionalIndexExpression($child),
            ),
        );

        return $this->parseOptionalMemberPrimaryPrefix(
            $this->parseOptionalIndexExpression($prefix),
        );
    }

    /**
     * This method parses an optional member primary expression. It will parse
     * the primary expression when a double colon operator can be found at the
     * actual token stream position. Otherwise this method simply returns the
     * input {@link ASTNode} instance.
     *
     * @param AbstractASTNode $node This node represents primary prefix left expression. It will
     *                              be the first child of the parsed member primary expression.
     * @throws ParserException
     * @since 1.0.1
     */
    private function parseOptionalStaticMemberPrimaryPrefix(AbstractASTNode $node): AbstractASTNode
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
     * @throws ParserException
     * @since 0.9.6
     */
    private function parseStaticMemberPrimaryPrefix(ASTNode $node): AbstractASTNode
    {
        $token = $this->consumeToken(Tokens::T_DOUBLE_COLON);

        $prefix = $this->builder->buildAstMemberPrimaryPrefix($token->image);
        $prefix->addChild($node);

        $this->consumeComments();

        $postfix = match ($this->tokenizer->peek()) {
            Tokens::T_STRING => $this->parseMethodOrConstantPostfix(),
            Tokens::T_CLASS_FQN => $this->parseFullQualifiedClassNamePostfix(),
            default => $this->parseMethodOrPropertyPostfix(
                $this->parsePostfixIdentifier(),
            ),
        };

        $prefix->addChild($postfix);

        return $this->parseOptionalMemberPrimaryPrefix(
            $this->parseOptionalIndexExpression($prefix),
        );
    }

    /**
     * This method parses a method- or constant-postfix expression. This expression
     * will contain an identifier node as nested child.
     *
     * @since 0.9.6
     */
    private function parseMethodOrConstantPostfix(): ASTNode
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
     * @throws ParserException
     * @since 0.9.6
     */
    private function parseMethodOrPropertyPostfix(ASTNode $node): ASTNode
    {
        // Strip optional comments
        $this->consumeComments();

        $postfix = match ($this->tokenizer->peek()) {
            Tokens::T_PARENTHESIS_OPEN => $this->parseMethodPostfix($node),
            default => $this->parsePropertyPostfix($node),
        };

        return $this->parseOptionalMemberPrimaryPrefix($postfix);
    }

    /**
     * Parses/Creates a property postfix node instance.
     *
     * @param ASTNode $node Node that represents the image of the property postfix node.
     * @since 0.10.2
     */
    private function parsePropertyPostfix(ASTNode $node): ASTPropertyPostfix
    {
        $image = $this->extractPostfixImage($node);

        $postfix = $this->builder->buildAstPropertyPostfix($image);
        $postfix->addChild($node);

        $postfix->configureLinesAndColumns(
            $node->getStartLine(),
            $node->getEndLine(),
            $node->getStartColumn(),
            $node->getEndColumn(),
        );

        return $postfix;
    }

    /**
     * Parses a full qualified class name postfix.
     *
     * @since 2.0.0
     */
    private function parseFullQualifiedClassNamePostfix(): ASTClassFqnPostfix
    {
        $this->tokenStack->push();

        $this->consumeToken(Tokens::T_CLASS_FQN);

        return $this->setNodePositionsAndReturn(
            $this->builder->buildAstClassFqnPostfix()
        );
    }

    /**
     * This method will extract the image/name of the real property/variable
     * that is wrapped by {@link ASTIndexExpression} nodes. If
     * the given node is now wrapped by index expressions, this method will
     * return the image of the entire <b>$node</b>.
     *
     * @param ASTNode $node The context node that may be wrapped by multiple array or string index expressions.
     * @since 1.0.0
     */
    private function extractPostfixImage(ASTNode $node): string
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
     * @since 1.0.0
     */
    private function parseMethodPostfix(ASTNode $node): AbstractASTNode
    {
        $args = $this->parseArguments();
        $image = $this->extractPostfixImage($node);

        $postfix = $this->builder->buildAstMethodPostfix($image);
        $postfix->addChild($node);
        $postfix->addChild($args);

        $postfix->configureLinesAndColumns(
            $node->getStartLine(),
            $args->getEndLine(),
            $node->getStartColumn(),
            $args->getEndColumn(),
        );

        return $this->parseOptionalMemberPrimaryPrefix($postfix);
    }

    /**
     * This method parses the arguments passed to a function- or method-call.
     *
     * @throws ParserException
     * @since 0.9.6
     */
    private function parseArguments(): ASTArguments
    {
        $this->consumeComments();

        $this->tokenStack->push();

        return $this->parseArgumentsParenthesesContent(
            $this->builder->buildAstArguments(),
        );
    }

    /**
     * This method parses the tokens after arguments passed to a function- or method-call.
     *
     * @template T of ASTArguments
     * @param T $arguments
     * @return T
     * @throws ParserException
     * @since 0.9.6
     */
    private function parseArgumentsParenthesesContent(ASTArguments $arguments): ASTArguments
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
     * @return T
     */
    private function parseArgumentList(ASTArguments $arguments): ASTArguments
    {
        $this->consumeComments();

        // peek if there's an ellipsis to determine variadic placeholder
        $ellipsis = Tokens::T_ELLIPSIS === $this->tokenizer->peek();

        while (true) {
            $this->consumeComments();

            if (Tokens::T_ELLIPSIS === $this->tokenizer->peek()) {
                $this->consumeToken(Tokens::T_ELLIPSIS);
            }

            $expr = $this->parseArgumentExpression();

            if ($expr instanceof ASTConstant) {
                $expr = $this->parseConstantArgument($expr, $arguments);
            }

            if (!$expr || !$this->addChildToList($arguments, $expr)) {
                break;
            }
        }

        // ellipsis and no further arguments => variadic placeholder foo(...)
        if ($ellipsis && count($arguments->getChildren()) === 0) {
            $arguments->setVariadicPlaceholder();
        }

        return $arguments;
    }

    /**
     * This method implements the parsing for various expression types like
     * variables, object/static method. All these expressions are valid in
     * several php language constructs like, isset, empty, unset etc.
     *
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     * @since 0.9.12
     */
    private function parseVariableOrConstantOrPrimaryPrefix(): ASTNode
    {
        $this->consumeComments();

        return match ($this->tokenizer->peek()) {
            Tokens::T_DOLLAR,
            Tokens::T_VARIABLE => $this->parseVariableOrFunctionPostfixOrMemberPrimaryPrefix(),
            Tokens::T_SELF => $this->parseConstantOrSelfMemberPrimaryPrefix(),
            Tokens::T_PARENT => $this->parseConstantOrParentMemberPrimaryPrefix(),
            Tokens::T_STATIC => $this->parseStaticVariableDeclarationOrMemberPrimaryPrefix(),
            Tokens::T_STRING,
            Tokens::T_BACKSLASH,
            Tokens::T_NAMESPACE => $this->parseMemberPrefixOrFunctionPostfix(),
            Tokens::T_PARENTHESIS_OPEN => $this->parseParenthesisExpression(),
            default => throw $this->getUnexpectedNextTokenException(),
        };
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
     * @since 0.9.6
     */
    private function parseVariableOrFunctionPostfixOrMemberPrimaryPrefix(): AbstractASTNode
    {
        $this->tokenStack->push();

        $variable = $this->parseCompoundVariableOrVariableVariableOrVariable();
        $variable = $this->parseOptionalIndexExpression($variable);

        $this->consumeComments();

        $result = match ($this->tokenizer->peek()) {
            Tokens::T_DOUBLE_COLON => $this->parseStaticMemberPrimaryPrefix($variable),
            Tokens::T_NULLSAFE_OBJECT_OPERATOR,
            Tokens::T_OBJECT_OPERATOR => $this->parseMemberPrimaryPrefix($variable),
            Tokens::T_PARENTHESIS_OPEN => $this->parseFunctionPostfix($variable),
            default => $variable,
        };

        return $this->setNodePositionsAndReturn($result);
    }

    /**
     * Parses an assingment expression node.
     *
     * @param ASTNode $left The left part of the assignment expression that will be parsed by this method.
     * @since 0.9.12
     */
    private function parseAssignmentExpression(ASTNode $left): ASTAssignmentExpression
    {
        $token = $this->consumeToken($this->tokenizer->peek());

        $node = $this->builder->buildAstAssignmentExpression($token->image);
        $node->addChild($left);

        // TODO: Change this into a mandatory expression in later versions
        if (($expr = $this->parseOptionalExpression()) !== null) {
            $node->addChild($expr);
        } else {
            $expr = $left;
        }

        $node->configureLinesAndColumns(
            $left->getStartLine(),
            $expr->getEndLine(),
            $left->getStartColumn(),
            $expr->getEndColumn(),
        );

        return $node;
    }

    /**
     * This method parses a {@link ASTStaticReference} node.
     *
     * @param Token $token The "static" keyword token.
     * @throws ParserException
     * @throws InvalidStateException
     * @since 0.9.6
     */
    private function parseStaticReference(Token $token): ASTStaticReference
    {
        // Strip optional comments
        $this->consumeComments();

        if (!isset($this->classOrInterface)) {
            throw new InvalidStateException(
                $token->startLine,
                (string) $this->compilationUnit,
                'The keyword "static" was used outside of a class/method scope.',
            );
        }

        $ref = $this->builder->buildAstStaticReference($this->classOrInterface);
        $ref->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $ref;
    }

    /**
     * This method parses a {@link ASTSelfReference} node.
     *
     * @param Token $token The "self" keyword token.
     * @throws ParserException
     * @throws InvalidStateException
     * @since 0.9.6
     */
    private function parseSelfReference(Token $token): ASTSelfReference
    {
        if (!isset($this->classOrInterface)) {
            throw new InvalidStateException(
                $token->startLine,
                (string) $this->compilationUnit,
                'The keyword "self" was used outside of a class/method scope.',
            );
        }

        $ref = $this->builder->buildAstSelfReference($this->classOrInterface);
        $ref->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $ref;
    }

    /**
     * Parses a simple PHP constant use and returns a corresponding node.
     *
     * @since 1.0.0
     */
    private function parseConstant(): ?ASTNode
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
                    $this->builder->buildAstConstant($token->image),
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
     * @since 0.9.6
     */
    private function parseConstantOrSelfMemberPrimaryPrefix(): ASTNode
    {
        // Read self token and strip optional comments
        $token = $this->consumeToken(Tokens::T_SELF);
        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_DOUBLE_COLON) {
            return $this->parseStaticMemberPrimaryPrefix(
                $this->parseSelfReference($token),
            );
        }

        return $this->builder->buildAstConstant($token->image);
    }

    /**
     * This method parses a {@link ASTParentReference} node.
     *
     * @param Token $token The "self" keyword token.
     * @throws ParserException
     * @throws InvalidStateException
     * @since 0.9.6
     */
    private function parseParentReference(Token $token): ASTParentReference
    {
        if (!isset($this->classOrInterface)) {
            throw new InvalidStateException(
                $token->startLine,
                (string) $this->compilationUnit,
                'The keyword "parent" was used as type hint but the parameter ' .
                'declaration is not in a class scope.',
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
                    $this->classOrInterface->getImage(),
                ),
            );
        }

        $ref = $this->builder->buildAstParentReference($classReference);
        $ref->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
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
     * @since 0.9.6
     */
    private function parseConstantOrParentMemberPrimaryPrefix(): ASTNode
    {
        // Consume parent token and strip optional comments
        $token = $this->consumeToken(Tokens::T_PARENT);
        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_DOUBLE_COLON) {
            return $this->parseStaticMemberPrimaryPrefix(
                $this->parseParentReference($token),
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
     * @since 0.9.18
     */
    private function parseVariableOrMemberOptionalByReference(): ASTNode
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
     * @since 0.9.18
     */
    private function parseVariableOrMemberByReference(): ASTUnaryExpression
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
     * @since 0.9.6
     */
    private function parseVariable(): ASTVariable
    {
        $token = $this->consumeToken(Tokens::T_VARIABLE);

        $variable = $this->builder->buildAstVariable($token->image);
        $variable->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
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
     * @return T The prepared entire node.
     * @since 0.9.12
     */
    private function parseVariableList(ASTNode $node, bool $inCall = false): AbstractASTNode
    {
        $this->consumeComments();
        while ($this->tokenizer->peek() !== Tokenizer::T_EOF) {
            $node->addChild($this->parseVariableOrConstantOrPrimaryPrefix());

            $this->consumeComments();

            while ($this->tokenizer->peek() === Tokens::T_SQUARED_BRACKET_OPEN) {
                $this->parseListExpression();
            }

            if ($this->tokenizer->peek() === Tokens::T_COMMA) {
                $this->consumeToken(Tokens::T_COMMA);
                $this->consumeComments();

                if (
                    $inCall &&
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
     * @since 0.9.6
     */
    private function parseCompoundVariableOrVariableVariableOrVariable(): AbstractASTNode
    {
        if ($this->tokenizer->peek() === Tokens::T_DOLLAR) {
            return $this->parseCompoundVariableOrVariableVariable();
        }

        return $this->parseVariable();
    }

    /**
     * Parses a PHP compound variable or a simple literal node.
     *
     * @since 0.9.19
     */
    private function parseCompoundVariableOrLiteral(): ASTNode
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
     * @since 0.9.6
     */
    private function parseCompoundVariableOrVariableVariable(): AbstractASTNode
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
                    $this->parseCompoundVariableOrVariableVariableOrVariable(),
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
     * @since 0.10.0
     */
    private function parseCompoundVariable(Token $token): ASTCompoundVariable
    {
        return $this->parseBraceExpression(
            $this->builder->buildAstCompoundVariable($token->image),
            $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN),
            Tokens::T_CURLY_BRACE_CLOSE,
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
     * @since 0.9.10
     */
    private function parseCompoundExpressionOrLiteral(): ASTNode
    {
        $token = $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {
            case Tokens::T_DOLLAR:
            case Tokens::T_VARIABLE:
                return $this->parseBraceExpression(
                    $this->builder->buildAstCompoundExpression(),
                    $token,
                    Tokens::T_CURLY_BRACE_CLOSE,
                );
        }

        $literal = $this->builder->buildAstLiteral($token->image);
        $literal->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
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
     * @since 0.9.6
     */
    private function parseCompoundExpression(): ASTCompoundExpression
    {
        $this->consumeComments();

        return $this->parseBraceExpression(
            $this->builder->buildAstCompoundExpression(),
            $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN),
            Tokens::T_CURLY_BRACE_CLOSE,
        );
    }

    /**
     * Parses a static identifier expression, as it is used for method and
     * function names.
     *
     * @since 0.9.12
     */
    private function parseIdentifier(int $tokenType = Tokens::T_STRING): ASTIdentifier
    {
        $token = $this->consumeToken($tokenType);

        $node = $this->builder->buildAstIdentifier($token->image);
        $node->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $node;
    }

    /**
     * This method parses a {@link ASTLiteral} node or an
     * instance of {@link ASTString} that represents a string
     * in double quotes or surrounded by backticks.
     *
     * @throws UnexpectedTokenException
     */
    private function parseLiteralOrString(): ASTNode
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
                    $token->endColumn,
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
     * @throws UnexpectedTokenException
     * @since 1.0.0
     */
    private function parseIntegerNumber(): ASTLiteral
    {
        $token = $this->consumeToken(Tokens::T_LNUMBER);
        $number = $token->image;

        while ($next = $this->addTokenToStackIfType(Tokens::T_STRING)) {
            $number .= $next->image;
        }

        if (!str_starts_with($number, '0')) {
            goto BUILD_LITERAL;
        }

        if (Tokens::T_STRING !== $this->tokenizer->peek()) {
            goto BUILD_LITERAL;
        }

        $token1 = $this->consumeToken(Tokens::T_STRING);
        if (!preg_match('(^b[01]+$)i', $token1->image)) {
            throw new UnexpectedTokenException(
                $token1,
                $this->tokenizer->getSourceFile() ?? 'unknown'
            );
        }

        $number .= $token1->image;
        $token->endLine = $token1->endLine;
        $token->endColumn = $token1->endColumn;

        BUILD_LITERAL:

        $literal = $this->builder->buildAstLiteral($number);
        $literal->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $literal;
    }

    /**
     * Parses an array structure.
     *
     * @since 1.0.0
     */
    private function doParseArray(bool $static = false): ASTArray
    {
        $this->tokenStack->push();

        return $this->setNodePositionsAndReturn(
            $this->parseArray(
                $this->builder->buildAstArray(),
                $static,
            ),
        );
    }

    /**
     * Tests if the next token is a valid array start delimiter in the supported
     * PHP version.
     *
     * @since 1.0.0
     */
    private function isArrayStartDelimiter(): bool
    {
        return match ($this->tokenizer->peek()) {
            Tokens::T_ARRAY,
            Tokens::T_SQUARED_BRACKET_OPEN => true,
            default => false,
        };
    }

    /**
     * Parses a php array declaration.
     *
     * @template T of ASTArray
     * @param T $array
     * @return T
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     * @since 1.0.0
     */
    private function parseArray(ASTArray $array, bool $static = false): ASTArray
    {
        switch ($this->tokenizer->peek()) {
            case Tokens::T_SQUARED_BRACKET_OPEN:
                $this->consumeToken(Tokens::T_SQUARED_BRACKET_OPEN);
                $this->parseArrayElements($array, Tokens::T_SQUARED_BRACKET_CLOSE, $static);
                $this->consumeToken(Tokens::T_SQUARED_BRACKET_CLOSE);

                break;

            case Tokens::T_ARRAY:
                $this->consumeToken(Tokens::T_ARRAY);
                $this->consumeComments();
                $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);
                $this->parseArrayElements($array, Tokens::T_PARENTHESIS_CLOSE, $static);
                $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);

                break;

            default:
                throw $this->getUnexpectedNextTokenException();
        }

        return $array;
    }

    /**
     * Parses all elements in an array.
     *
     * @template T of ASTArray
     * @param T $array
     * @return T
     * @since 1.0.0
     */
    private function parseArrayElements(ASTArray $array, int $endDelimiter, bool $static = false): ASTArray
    {
        $consecutiveComma = null;
        $openingToken = $this->tokenizer->prevToken();
        $useSquaredBrackets = ($endDelimiter === Tokens::T_SQUARED_BRACKET_CLOSE);
        $this->consumeComments();

        while ($endDelimiter !== $this->tokenizer->peek()) {
            while (Tokens::T_COMMA === $this->tokenizer->peek()) {
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
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function ensureArrayIsValid(bool $useSquaredBrackets, ?Token $openingToken, ?Token $consecutiveComma): void
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
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     * @since 2.9.0
     */
    private function parseMatchEntryKey(): ASTNode
    {
        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_DEFAULT) {
            $this->consumeToken(Tokens::T_DEFAULT);
            $label = $this->builder->buildAstSwitchLabel('default');
            $label->setDefault();

            return $label;
        }

        if ($this->isKeyword($this->tokenizer->peek())) {
            throw $this->getUnexpectedNextTokenException();
        }

        return $this->parseExpression();
    }

    /**
     * Parses a single match value expression.
     *
     * @since 2.9.0
     */
    private function parseMatchEntryValue(): ASTNode
    {
        $this->consumeComments();

        if ($this->tokenizer->peek() === Tokens::T_THROW) {
            return $this->parseThrowStatement([Tokens::T_COMMA, Tokens::T_CURLY_BRACE_CLOSE]);
        }

        return $this->parseExpression();
    }

    /**
     * Parses a single match entry key-expression pair.
     *
     * @since 2.9.0
     */
    private function parseMatchEntry(): ASTMatchEntry
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
        } while ($type !== Tokens::T_DOUBLE_ARROW);

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
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     * @since 1.0.0
     */
    private function parseArrayElement(bool $static = false): ASTArrayElement
    {
        $this->consumeComments();

        $this->tokenStack->push();

        $element = $this->builder->buildAstArrayElement();
        if ($this->parseOptionalByReference()) {
            if ($static) {
                $tokens = $this->tokenStack->pop();

                throw $this->getUnexpectedTokenException(end($tokens) ?: null);
            }

            $element->setByReference();
        }

        $this->consumeComments();
        if ($this->isKeyword($this->tokenizer->peek())) {
            throw $this->getUnexpectedNextTokenException();
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
     * @since 0.9.12
     */
    private function parseHeredoc(): ASTHeredoc
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
     * @since 0.9.10
     */
    private function parseStringSequence(int $tokenType): string
    {
        $type = $tokenType;
        $string = '';

        do {
            $string .= $this->consumeToken($type)->image;
            $type = $this->tokenizer->peek();
        } while ($type !== $tokenType && $type !== Tokenizer::T_EOF);

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
     * @throws UnexpectedTokenException
     * @since 0.9.10
     */
    private function parseString(int $delimiterType): ASTString
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
            $endColumn,
        );

        return $string;
    }

    /**
     * This method parses the contents of a string or here-/now-doc node. It
     * will not consume the given stop token, so it is up to the calling method
     * to consume the stop token. The return value of this method is the prepared
     * input string node.
     *
     * @template T of AbstractASTNode
     * @param T $node
     * @return T
     * @since 0.9.12
     */
    private function parseStringExpressions(AbstractASTNode $node, int $stopToken): AbstractASTNode
    {
        while (($tokenType = $this->tokenizer->peek()) !== Tokenizer::T_EOF) {
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
     * @since 0.9.10
     */
    private function parseEscapedAstLiteralString(): ASTLiteral
    {
        $this->tokenStack->push();

        $image = $this->consumeToken(Tokens::T_BACKSLASH)->image;
        $escape = true;

        $tokenType = $this->tokenizer->peek();
        while ($tokenType !== Tokenizer::T_EOF) {
            if ($tokenType === Tokens::T_BACKSLASH) {
                $escape = !$escape;
                $image .= $this->consumeToken(Tokens::T_BACKSLASH)->image;

                $tokenType = $this->tokenizer->peek();

                continue;
            }

            if ($escape) {
                $image .= $this->consumeToken($tokenType)->image;

                break;
            }
        }

        return $this->setNodePositionsAndReturn(
            $this->builder->buildAstLiteral($image),
        );
    }

    /**
     * This method parses a simple literal and configures the position
     * properties.
     *
     * @since 0.9.10
     */
    private function parseLiteral(): ASTLiteral
    {
        $token = $this->consumeToken($this->tokenizer->peek());

        $node = $this->builder->buildAstLiteral($token->image);
        $node->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $node;
    }

    private function checkReadonlyToken(): int
    {
        if ($this->addTokenToStackIfType(Tokens::T_READONLY)) {
            return State::IS_READONLY;
        }

        return 0;
    }

    /**
     * Parse the modifiers for construct parameter
     */
    private function parseConstructFormalParameterModifiers(): int
    {
        /** @var array<int, int> */
        static $states = [
            Tokens::T_PUBLIC => State::IS_PUBLIC,
            Tokens::T_PROTECTED => State::IS_PROTECTED,
            Tokens::T_PRIVATE => State::IS_PRIVATE,
        ];

        $modifier = $this->checkReadonlyToken();
        $token = $this->tokenizer->peek();

        if (isset($states[$token])) {
            $modifier |= $states[$token];
            $next = $this->tokenizer->next();
            assert($next instanceof Token);
            $this->tokenStack->add($next);
        }

        return $modifier | $this->checkReadonlyToken();
    }

    /**
     * This method parse a formal parameter and all the stuff that may be allowed
     * before it according to the PHP level (type hint, passing by reference, property promotion).
     *
     * @param ASTCallable $callable the callable object (closure, function or method)
     *                              requiring the given parameters list.
     */
    private function parseFormalParameterOrPrefix(ASTCallable $callable): ASTNode
    {
        $modifier = 0;

        if ($callable instanceof ASTMethod && $callable->getImage() === '__construct') {
            $modifier = $this->parseConstructFormalParameterModifiers();
        }

        $parameter = $this->parseFormalParameterOrTypeHintOrByReference();

        if ($modifier) {
            $parameter->setModifiers($modifier);
        }

        return $parameter;
    }

    /**
     * Extracts all dependencies from a callable signature.
     *
     * @param ASTCallable $callable the callable object (closure, function or method)
     *                              requiring the given parameters list.
     * @since 0.9.5
     */
    private function parseFormalParameters(ASTCallable $callable): ASTFormalParameters
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

            if ($tokenType === Tokens::T_PARENTHESIS_CLOSE) {
                break;
            }

            $formalParameters->addChild(
                $this->parseFormalParameterOrPrefix($callable),
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
     * @since 0.9.6
     */
    private function parseFormalParameterOrTypeHintOrByReference(): ASTFormalParameter
    {
        $this->consumeComments();
        $this->consumeQuestionMark();
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        $this->tokenStack->push();

        return $this->setNodePositionsAndReturn(
            $this->parseFormalParameterFromType($tokenType),
        );
    }

    private function parseFormalParameterFromType(int $tokenType): ASTFormalParameter
    {
        if ($this->isTypeHint($tokenType)) {
            $typeHint = $this->parseOptionalTypeHint();

            return $this->parseFormalParameterAndTypeHint($typeHint);
        }

        return match ($tokenType) {
            Tokens::T_ARRAY => $this->parseFormalParameterAndArrayTypeHint(),
            Tokens::T_SELF => $this->parseFormalParameterAndSelfTypeHint(),
            Tokens::T_PARENT => $this->parseFormalParameterAndParentTypeHint(),
            Tokens::T_STATIC => $this->parseFormalParameterAndStaticTypeHint(),
            Tokens::T_BITWISE_AND => $this->parseFormalParameterAndByReference(),
            default => $this->parseFormalParameter(),
        };
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
     * @since 0.9.6
     */
    private function parseFormalParameterAndArrayTypeHint(): ASTFormalParameter
    {
        $node = $this->parseArrayType();

        $parameter = $this->parseFormalParameterOrByReference();
        $parameter->prependChild($node);

        return $parameter;
    }

    private function parseArrayType(): ASTTypeArray
    {
        $token = $this->consumeToken(Tokens::T_ARRAY);

        $type = $this->builder->buildAstTypeArray();
        $type->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $type;
    }

    /**
     * Parses a type hint that is valid in the supported PHP version after the next token.
     *
     * @since 2.9.2
     */
    private function parseOptionalTypeHint(): ASTType
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
     * @since 0.9.6
     */
    private function parseFormalParameterAndTypeHint(ASTNode $typeHint): ASTFormalParameter
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
     * @since 0.9.6
     */
    private function parseFormalParameterAndParentTypeHint(): ASTFormalParameter
    {
        $reference = $this->parseParentType();
        $parameter = $this->parseFormalParameterOrByReference();
        $parameter->prependChild($reference);

        return $parameter;
    }

    private function parseParentType(): ASTParentReference
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
     * @since 0.9.6
     */
    private function parseFormalParameterAndSelfTypeHint(): ASTFormalParameter
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
     * @since 2.9.2
     */
    private function parseFormalParameterAndStaticTypeHint(): ASTFormalParameter
    {
        $self = $this->parseStaticType();

        $parameter = $this->parseFormalParameterOrByReference();
        $parameter->addChild($self);

        return $parameter;
    }

    private function parseSelfType(): ASTSelfReference
    {
        return $this->parseSelfReference($this->consumeToken(Tokens::T_SELF));
    }

    private function parseStaticType(): ASTStaticReference
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
     * @since 0.9.6
     */
    private function parseFormalParameterOrByReference(): ASTFormalParameter
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
     * @since 0.9.6
     */
    private function parseFormalParameterAndByReference(): ASTFormalParameter
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
     * @since 0.9.6
     */
    private function parseFormalParameter(): ASTFormalParameter
    {
        $parameter = $this->builder->buildAstFormalParameter();

        if (Tokens::T_ELLIPSIS === $this->tokenizer->peek()) {
            $this->consumeToken(Tokens::T_ELLIPSIS);
            $this->consumeComments();

            $parameter->setVariableArgList();
        }

        $parameter->addChild($this->parseVariableDeclarator());

        return $parameter;
    }

    /**
     * Tests if the given token type is a valid formal parameter in the supported
     * PHP version.
     *
     * @since 1.0.0
     */
    protected function isTypeHint(int $tokenType): bool
    {
        return match ($tokenType) {
            Tokens::T_SELF,
            Tokens::T_PARENT,
            Tokens::T_CALLABLE,
            Tokens::T_STRING,
            Tokens::T_BACKSLASH,
            Tokens::T_NAMESPACE,
            Tokens::T_ARRAY,
            Tokens::T_NULL,
            Tokens::T_FALSE,
            Tokens::T_STATIC => true,
            default => false,
        };
    }

    /**
     * Parses a type hint that is valid in the supported PHP version.
     *
     * @throws ParserException
     * @since 1.0.0
     */
    private function parseBasicTypeHint(): ASTType
    {
        $this->consumeQuestionMark();

        switch ($this->tokenizer->peek()) {
            case Tokens::T_CALLABLE:
                $this->consumeToken(Tokens::T_CALLABLE);

                return $this->builder->buildAstTypeCallable();

            case Tokens::T_ARRAY:
                return $this->parseArrayType();

            case Tokens::T_SELF:
                return $this->parseSelfType();

            case Tokens::T_STRING:
            case Tokens::T_BACKSLASH:
            case Tokens::T_NAMESPACE:
                $name = $this->parseQualifiedName();

                return $this->isScalarOrCallableTypeHint($name)
                    ? $this->parseScalarOrCallableTypeHint($name)
                    : $this->builder->buildAstClassOrInterfaceReference($name);

            case Tokens::T_CLASS:
                return $this->builder->buildAstClassOrInterfaceReference(
                    $this->parseQualifiedName(),
                );

            default:
                throw new ParserException('Unsupported typehint');
        }
    }

    protected function parseSingleTypeHint(): ASTType
    {
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {
            case Tokens::T_ARRAY:
                $type = $this->parseArrayType();

                break;

            case Tokens::T_SELF:
                $type = $this->parseSelfType();

                break;

            case Tokens::T_PARENT:
                $type = $this->parseParentType();

                break;

            case Tokens::T_STATIC:
                $type = $this->parseStaticType();

                break;

            case Tokens::T_NULL:
                $type = new ASTScalarType('null');
                $token = $this->tokenizer->next();
                assert($token instanceof Token);
                $this->tokenStack->add($token);

                break;

            case Tokens::T_FALSE:
                $type = new ASTScalarType('false');
                $token = $this->tokenizer->next();
                assert($token instanceof Token);
                $this->tokenStack->add($token);

                break;

            case Tokens::T_ARRAY:
                $type = $this->parseArrayType();

                break;

            case Tokens::T_SELF:
                $type = $this->parseSelfType();

                break;

            case Tokens::T_PARENT:
                $type = $this->parseParentType();

                break;

            default:
                $type = $this->parseBasicTypeHint();

                break;
        }

        $this->consumeComments();

        return $type;
    }

    private function parseUnionTypeHint(ASTType $firstType): ASTUnionType
    {
        $types = [$firstType];

        while ($this->tokenizer->peek() === Tokens::T_BITWISE_OR) {
            $token = $this->tokenizer->next();
            assert($token instanceof Token);
            $this->tokenStack->add($token);
            $types[] = $this->parseSingleTypeHint();
        }

        $unionType = $this->builder->buildAstUnionType();
        foreach ($types as $type) {
            $unionType->addChild($type);
        }

        return $unionType;
    }

    private function parseIntersectionTypeHint(ASTType $firstType): ASTIntersectionType
    {
        $token = $this->tokenizer->currentToken();
        $types = [$firstType];

        while (
            $this->tokenizer->peekNext() !== Tokens::T_VARIABLE
            && $this->addTokenToStackIfType(Tokens::T_BITWISE_AND)
        ) {
            $types[] = $this->parseSingleTypeHint();
        }

        $intersectionType = $this->builder->buildAstIntersectionType();
        foreach ($types as $type) {
            // no scalars are allowed as intersection types
            if ($type instanceof ASTScalarType) {
                throw new ParserException(
                    $type->getImage() . ' can not be used in an intersection type',
                    0,
                    $this->getUnexpectedTokenException($token)
                );
            }

            $intersectionType->addChild($type);
        }

        return $intersectionType;
    }

    private function parseTypeHintCombination(ASTType $type): ASTType
    {
        $peek = $this->tokenizer->peek();

        if ($peek === Tokens::T_BITWISE_OR) {
            return $this->parseUnionTypeHint($type);
        }

        $peekNext = $this->tokenizer->peekNext();
        // sniff for &, but avoid by_reference &$variable and &...$variables.
        if ($peek === Tokens::T_BITWISE_AND && $peekNext !== Tokens::T_VARIABLE && $peekNext !== Tokens::T_ELLIPSIS) {
            return $this->parseIntersectionTypeHint($type);
        }

        return $type;
    }

    protected function canNotBeStandAloneType(ASTNode $type): bool
    {
        return $type instanceof ASTScalarType && ($type->isFalse() || $type->isNull());
    }

    /**
     * Parses a type hint that is valid in the supported PHP version.
     *
     * @throws ParserException
     * @since 1.0.0
     */
    protected function parseTypeHint(): ASTType
    {
        $this->consumeComments();
        $token = $this->tokenizer->currentToken();
        $type = $this->parseSingleTypeHint();

        $type = $this->parseTypeHintCombination($type);

        if ($this->canNotBeStandAloneType($type)) {
            throw new ParserException(
                $type->getImage() . ' can not be used as a standalone type',
                0,
                $this->getUnexpectedTokenException($token)
            );
        }

        return $type;
    }

    /**
     * Extracts all dependencies from a callable body.
     *
     * @since 0.9.12
     */
    private function parseScope(): ASTScope
    {
        $scope = $this->builder->buildAstScope();

        $this->tokenStack->push();

        $this->consumeComments();
        $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);

        while (($stmt = $this->parseOptionalStatement()) !== null) {
            $scope->addChild($stmt);
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
     * @since 1.0.0
     */
    private function parseStatement(): ASTNode
    {
        if ($stmt = $this->parseOptionalStatement()) {
            return $stmt;
        }

        throw $this->getUnexpectedNextTokenException();
    }

    /**
     * Parses an optional statement or returns <b>null</b>.
     *
     * @throws ParserException
     * @since 0.9.8
     */
    private function parseOptionalStatement(): ?ASTNode
    {
        switch ($this->tokenizer->peek()) {
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

            case Tokens::T_ENUM:
            case Tokens::T_STRING:
                $token = $this->tokenizer->currentToken();
                $nextType = $this->tokenizer->peekNext();

                if ($token && $token->image === 'enum' && $nextType === Tokens::T_STRING) {
                    $package = $this->getNamespaceOrPackage();
                    $package->addType($enum = $this->parseEnumDeclaration());

                    $this->builder->restoreEnum($enum);
                    $this->compilationUnit->addChild($enum);

                    return $enum;
                }

                if ($nextType === Tokens::T_COLON) {
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
                    $this->consumeToken(Tokens::T_DOC_COMMENT)->image,
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
                if ($this->parseNonePhpCode() === Tokenizer::T_EOF) {
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
            case Tokens::T_READONLY:
                $package = $this->getNamespaceOrPackage();
                $package->addType($class = $this->parseClassDeclaration());

                $this->builder->restoreClass($class);
                $this->compilationUnit->addChild($class);

                return $class;

            case Tokens::T_YIELD:
                return $this->parseYield(true);
        }

        $this->tokenStack->push();
        $stmt = $this->builder->buildAstStatement();

        if (($expr = $this->parseOptionalExpression()) !== null) {
            $stmt->addChild($expr);
        }

        if ($this->echoing && $this->tokenizer->peek() === Tokens::T_COMMA) {
            $this->consumeToken(Tokens::T_COMMA);
            $this->parseOptionalStatement();
        } else {
            $this->parseStatementTermination();
        }

        return $this->setNodePositionsAndReturn($stmt);
    }

    /**
     * Parses a sequence of none php code tokens and returns the token type of
     * the next token.
     *
     * @since 0.9.12
     */
    private function parseNonePhpCode(): int
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
     * @since 0.9.8
     */
    private function parseCommentWithOptionalInlineClassOrInterfaceReference(): ASTComment
    {
        $token = $this->consumeToken(Tokens::T_COMMENT);

        $comment = $this->builder->buildAstComment($token->image);
        if (preg_match(self::REGEXP_INLINE_TYPE, $token->image, $match)) {
            $image = $this->useSymbolTable->lookup($match[1]) ?: $match[1];

            $comment->addChild(
                $this->builder->buildAstClassOrInterfaceReference($image),
            );
        }

        $comment->configureLinesAndColumns(
            $token->startLine,
            $token->endLine,
            $token->startColumn,
            $token->endColumn,
        );

        return $comment;
    }

    /**
     * Parses an optional set of bound closure variables.
     *
     * @template T of ASTClosure
     *
     * @param T $closure The context closure instance.
     * @return T
     * @since 1.0.0
     */
    private function parseOptionalBoundVariables(
        ASTClosure $closure,
    ): ASTClosure {
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
     * @return T
     * @since 0.9.5
     */
    private function parseBoundVariables(ASTClosure $closure): ASTClosure
    {
        $this->consumeToken(Tokens::T_USE);

        $this->consumeComments();
        $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);

        while ($this->tokenizer->peek() !== Tokenizer::T_EOF) {
            $this->consumeComments();

            if ($this->tokenizer->peek() === Tokens::T_PARENTHESIS_CLOSE) {
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
     * Parses a php class/method name chain.
     *
     * <code>
     * PDepend\Source\Parser::parse();
     * </code>
     *
     * @throws NoActiveScopeException
     * @link   http://php.net/manual/en/language.namespaces.importing.php
     */
    private function parseQualifiedName(): string
    {
        $fragments = $this->parseQualifiedNameRaw();

        // Check for fully qualified name
        if ($fragments[0] === '\\') {
            return implode('', $fragments);
        }

        if ($this->isScalarOrCallableTypeHint($fragments[0])) {
            return $fragments[0];
        }

        // Search for a use alias
        $mapsTo = $this->useSymbolTable->lookup($fragments[0]);

        if ($mapsTo !== null) {
            // Remove alias and add real namespace
            array_shift($fragments);
            array_unshift($fragments, $mapsTo);
        } elseif (
            isset($this->namespaceName)
            && !$this->namespacePrefixReplaced
        ) {
            // Prepend current namespace
            array_unshift($fragments, $this->namespaceName, '\\');
        }

        return implode('', $fragments);
    }

    /**
     * This method parses a qualified PHP 5.3 class, interface and namespace
     * identifier and returns the collected tokens as a string array.
     *
     * @return array<string>
     * @since 0.9.5
     */
    private function parseQualifiedNameRaw(): array
    {
        // Reset namespace prefix flag
        $this->namespacePrefixReplaced = false;

        // Consume comments and fetch first token type
        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        $qualifiedName = [];

        if ($tokenType === Tokens::T_NAMESPACE) {
            // Consume namespace keyword
            $this->consumeToken(Tokens::T_NAMESPACE);
            $this->consumeComments();

            // Add current namespace as first token
            $qualifiedName = [$this->namespaceName ?? ''];

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
     */
    protected function isScalarOrCallableTypeHint(string $image): bool
    {
        return match (strtolower($image)) {
            'int',
            'bool',
            'float',
            'string',
            'callable',
            'iterable',
            'void',
            'never' => true,
            default => false,
        };
    }

    /**
     * @param array<string> $previousElements
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function parseQualifiedNameElement(array $previousElements): ?string
    {
        if (Tokens::T_CURLY_BRACE_OPEN !== $this->tokenizer->peek()) {
            return $this->parseClassName();
        }

        if (count($previousElements) >= 2 && '\\' === end($previousElements)) {
            return null;
        }

        throw $this->getUnexpectedNextTokenException();
    }

    /**
     * This method parses a PHP 5.3 namespace declaration.
     *
     * @throws NoActiveScopeException
     * @since 0.9.5
     */
    private function parseNamespaceDeclaration(): void
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
     * Parses use declarations that are valid in the supported php version and
     * adds a mapping between short name and full qualified name to the use
     * symbol table.
     *
     * <code>
     * use \foo\bar as fb,
     *     \foobar\Bar;
     * </code>
     *
     * @since 0.9.5
     */
    private function parseUseDeclarations(): void
    {
        // Consume use keyword
        $this->consumeToken(Tokens::T_USE);
        $this->consumeComments();

        // Consume const and function tokens
        $nextToken = $this->tokenizer->peek();

        switch ($nextToken) {
            case Tokens::T_CONST:
            case Tokens::T_FUNCTION:
                $this->consumeToken($nextToken);
        }

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
     * @since 0.9.5
     */
    private function parseUseDeclaration(): void
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
     */
    private function parseUseDeclarationForVersion(array $fragments): void
    {
        if (Tokens::T_CURLY_BRACE_OPEN === $this->tokenizer->peek()) {
            $this->parseUseDeclarationVersion70($fragments);

            return;
        }

        $image = $this->parseNamespaceImage($fragments);
        if ($image === false) {
            return;
        }

        // Add mapping between image and qualified name to symbol table
        $this->useSymbolTable->add($image, implode('', $fragments));
    }

    /**
     * @param array<string> $fragments
     * @return false|string
     */
    private function parseNamespaceImage(array $fragments): bool|string
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
     * @since 0.9.6
     */
    private function parseConstantDefinition(): ASTConstantDefinition
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
     * Constant cannot be typed before PHP 8.3.
     *
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     * @since  1.16.0
     */
    protected function parseTypedConstantDeclarator(): ASTConstantDeclarator
    {
        throw $this->getUnexpectedNextTokenException();
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
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     * @since 0.9.6
     */
    private function parseConstantDeclarator(): ASTConstantDeclarator
    {
        // Remove leading comments and create a new token stack
        $this->consumeComments();
        $this->tokenStack->push();

        $nextToken = $this->tokenizer->peekNext();

        if (!$nextToken) {
            throw $this->getUnexpectedNextTokenException();
        }

        if ($this->isConstantName($nextToken)) {
            return $this->parseTypedConstantDeclarator();
        }

        $tokenType = $this->tokenizer->peek();

        if (!$this->isConstantName($tokenType)) {
            throw $this->getUnexpectedNextTokenException();
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
     * @since 2.2.x
     */
    protected function parseConstantDeclaratorValue(): ?ASTValue
    {
        if ($this->isFollowedByStaticValueOrStaticArray()) {
            return $this->parseVariableDefaultValue();
        }

        // Else it would be provided as ASTLiteral or expressions object.
        $value = new ASTValue();
        $value->setValue($this->parseOptionalExpression());

        return $value;
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
     * @since 0.9.6
     */
    private function parseStaticVariableDeclarationOrMemberPrimaryPrefix(): ASTNode
    {
        $this->tokenStack->push();

        // Consume static token and strip optional comments
        $token = $this->consumeToken(Tokens::T_STATIC);
        $this->consumeComments();

        // Fetch next token type
        $tokenType = $this->tokenizer->peek();

        if (
            $tokenType === Tokens::T_PARENTHESIS_OPEN
            || $tokenType === Tokens::T_DOUBLE_COLON
        ) {
            return $this->setNodePositionsAndReturn(
                $this->parseStaticMemberPrimaryPrefix(
                    $this->parseStaticReference($token),
                ),
            );
        }
        if ($tokenType === Tokens::T_FUNCTION) {
            $closure = $this->parseClosureDeclaration();
            $closure->setStatic(true);

            return $this->setNodePositionsAndReturn($closure);
        }
        if ($tokenType === Tokens::T_FN) {
            $closure = $this->parseLambdaFunctionDeclaration();
            $closure->setStatic(true);

            return $this->setNodePositionsAndReturn($closure);
        }

        return $this->setNodePositionsAndReturn(
            $this->parseStaticVariableDeclaration($token),
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
     * @since 0.9.6
     */
    private function parseStaticVariableDeclaration(Token $token): ASTStaticVariableDeclaration
    {
        $staticDeclaration = $this->builder->buildAstStaticVariableDeclaration(
            $token->image,
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
     * @throws MissingValueException
     * @since 0.9.6
     */
    private function parseVariableDeclarator(): ASTVariableDeclarator
    {
        $this->tokenStack->push();

        $name = $this->consumeToken(Tokens::T_VARIABLE)->image;
        $this->consumeComments();

        $declarator = $this->builder->buildAstVariableDeclarator($name);

        if ($this->tokenizer->peek() === Tokens::T_EQUAL) {
            $this->consumeToken(Tokens::T_EQUAL);
            $value = $this->parseVariableDefaultValue();
            if (!$value) {
                throw new MissingValueException($this->tokenizer);
            }
            $declarator->setValue($value);
        }

        return $this->setNodePositionsAndReturn($declarator);
    }

    /**
     * This method will parse a default value after a parameter/static variable/constant
     * declaration.
     *
     * @since 2.11.0
     */
    private function parseVariableDefaultValue(): ?ASTValue
    {
        if ($this->tokenizer->peek() === Tokens::T_NEW) {
            $defaultValue = new ASTValue();
            $defaultValue->setValue($this->parseAllocationExpression());

            return $defaultValue;
        }

        return $this->parseStaticValueOrStaticArray();
    }

    /**
     * This method will parse a static value or a static array as it is
     * used as default value for a parameter or property declaration.
     *
     * @since 0.9.6
     */
    private function parseStaticValueOrStaticArray(): ?ASTValue
    {
        $this->consumeComments();

        if ($this->isArrayStartDelimiter()) {
            // TODO: Use default value as value!
            $defaultValue = $this->doParseArray(true);

            $value = new ASTValue();
            $value->setValue([]);

            return $value;
        }

        return $this->parseStaticValue();
    }

    /**
     * This method will parse a static default value as it is used for a
     * parameter, property or constant declaration.
     *
     * @throws MissingValueException
     * @since 0.9.5
     */
    private function parseStaticValue(): ?ASTValue
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
                    if ($defaultValue->isValueAvailable()) {
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
                    $defaultValue->setValue($signed * $this->parseIntegerNumberImage(
                        $this->parseNumber(Tokens::T_LNUMBER),
                    ));

                    break;

                case Tokens::T_DNUMBER:
                    $defaultValue->setValue($signed * (float) $this->getNumberFromImage(
                        $this->parseNumber(Tokens::T_DNUMBER),
                    ));

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
                    $node = $this->parseStandAloneExpressionTypeReference($tokenType);

                    if ($this->tokenizer->peek() === Tokens::T_DOUBLE_COLON) {
                        $node->addChild($this->parseStaticMemberPrimaryPrefix($node));
                    }

                    $defaultValue->setValue($node);

                    break;

                case Tokens::T_STRING:
                case Tokens::T_BACKSLASH:
                    $node = $this->builder->buildAstClassOrInterfaceReference(
                        $this->parseQualifiedName(),
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
                        $this->parseHeredoc()->getChild(0)->getImage(),
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
     * @template T of ASTValue
     * @param T $value
     * @return T|null
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function parseStaticValueVersionSpecific(ASTValue $value): ?ASTValue
    {
        $expressions = [];

        while (($tokenType = $this->tokenizer->peek()) !== Tokenizer::T_EOF) {
            switch ($tokenType) {
                case Tokens::T_COMMA:
                case Tokens::T_CLOSE_TAG:
                case Tokens::T_COLON:
                case Tokens::T_DOUBLE_ARROW:
                case Tokens::T_END_HEREDOC:
                case Tokens::T_PARENTHESIS_CLOSE:
                case Tokens::T_SEMICOLON:
                case Tokens::T_SQUARED_BRACKET_CLOSE:
                    break 2;

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

                case $this->isArrayStartDelimiter():
                    $expressions[] = $this->doParseArray(true);

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

                case Tokens::T_PARENTHESIS_OPEN:
                    $expressions[] = $this->parseParenthesisExpressionOrPrimaryPrefix();

                    break;

                case Tokens::T_START_HEREDOC:
                    $expressions[] = $this->parseHeredoc();

                    break;

                case Tokens::T_SL:
                    $expressions[] = $this->parseShiftLeftExpression();

                    break;

                case Tokens::T_SR:
                    $expressions[] = $this->parseShiftRightExpression();

                    break;

                case Tokens::T_ELLIPSIS:
                case Tokens::T_STRING_VARNAME: // TODO: Implement this
                case Tokens::T_PLUS: // TODO: Make this a arithmetic expression
                case Tokens::T_MINUS:
                case Tokens::T_MUL:
                case Tokens::T_DIV:
                case Tokens::T_MOD:
                case Tokens::T_POW:
                case Tokens::T_IS_EQUAL: // TODO: Implement compare expressions
                case Tokens::T_IS_NOT_EQUAL:
                case Tokens::T_IS_IDENTICAL:
                case Tokens::T_IS_NOT_IDENTICAL:
                case Tokens::T_BITWISE_OR:
                case Tokens::T_BITWISE_AND:
                case Tokens::T_BITWISE_NOT:
                case Tokens::T_BITWISE_XOR:
                case Tokens::T_IS_GREATER_OR_EQUAL:
                case Tokens::T_IS_SMALLER_OR_EQUAL:
                case Tokens::T_ANGLE_BRACKET_OPEN:
                case Tokens::T_ANGLE_BRACKET_CLOSE:
                case Tokens::T_EMPTY:
                case Tokens::T_CONCAT:
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
                    $expression = array_pop($expressions);
                    if ($expression) {
                        $expressions[] = $this->parseAssignmentExpression($expression);
                    }

                    break;

                case Tokens::T_DIR:
                case Tokens::T_FILE:
                case Tokens::T_LINE:
                case Tokens::T_NS_C:
                case Tokens::T_FUNC_C:
                case Tokens::T_CLASS_C:
                case Tokens::T_METHOD_C:
                    $expression = $this->parseConstant();
                    if ($expression) {
                        $expressions[] = $expression;
                    }

                    break;

                    // TODO: Handle comments here
                case Tokens::T_COMMENT:
                case Tokens::T_DOC_COMMENT:
                    $this->consumeToken($tokenType);

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

                default:
                    throw $this->getUnexpectedNextTokenException();
            }
        }

        $expressions = $this->reduceUnaryExpression($expressions);

        $count = count($expressions);
        if ($count === 0) {
            return null;
        }
        if ($count === 1) {
            // @todo ASTValue must be a valid node.
            $value->setValue($expressions[0]);

            return $value;
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

        // @todo ASTValue must be a valid node.
        $value->setValue($expr);

        return $value;
    }

    /**
     * Parses fn operator of lambda function for syntax fn() => available since PHP 7.4.
     *
     * @throws UnexpectedTokenException
     */
    private function parseLambdaFunctionDeclaration(): ASTClosure
    {
        $this->tokenStack->push();

        if ($this->tokenizer->peek() === Tokens::T_FN) {
            $this->consumeToken(Tokens::T_FN);
        }

        $closure = $this->builder->buildAstClosure();
        $closure->setReturnsByReference($this->parseOptionalByReference());
        $closure->addChild($this->parseFormalParameters($closure));
        $this->parseCallableDeclarationAddition($closure);

        $closure->addChild(
            $this->buildReturnStatement(
                $this->consumeToken(Tokens::T_DOUBLE_ARROW)
            )
        );

        return $this->setNodePositionsAndReturn($closure);
    }

    /**
     * Checks if the given expression is a read/write variable as defined in
     * the PHP zend_language_parser.y definition.
     *
     * @param ASTNode $expr The context node instance.
     * @since 0.10.0
     */
    private function isReadWriteVariable(ASTNode $expr): bool
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
     */
    private function createQualifiedTypeName(string $localName): string
    {
        return ltrim($this->getNamespaceOrPackageName() . '\\' . $localName, '\\');
    }

    /**
     * Returns the name of a declared names. When the parsed code is not namespaced
     * this method will return the name from the @package annotation.
     *
     * @since 0.9.8
     */
    private function getNamespaceOrPackageName(): ?string
    {
        if (!isset($this->namespaceName)) {
            return $this->packageName;
        }

        return $this->namespaceName;
    }

    /**
     * Returns the currently active package or namespace.
     *
     * @since 1.0.0
     */
    private function getNamespaceOrPackage(): ASTNamespace
    {
        $namespace = $this->builder->buildNamespace((string) $this->getNamespaceOrPackageName());
        $namespace->setPackageAnnotation(null === $this->namespaceName);

        return $namespace;
    }

    /**
     * Extracts the @package information from the given comment.
     */
    private function parsePackageAnnotation(string $comment): ?string
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
        if (
            $this->globalPackageName === Builder::DEFAULT_NAMESPACE
            && $this->isFileComment()
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
     */
    private function isFileComment(): bool
    {
        if ($this->tokenizer->prev() !== Tokens::T_OPEN_TAG) {
            return false;
        }

        $notExpectedTags = [
            Tokens::T_CLASS,
            Tokens::T_FINAL,
            Tokens::T_TRAIT,
            Tokens::T_ABSTRACT,
            Tokens::T_FUNCTION,
            Tokens::T_INTERFACE,
        ];

        return !in_array($this->tokenizer->peek(), $notExpectedTags, true);
    }

    /**
     * Returns the class names of all <b>throws</b> annotations with in the
     * given comment block.
     *
     * @param string $comment The context doc comment block.
     * @return array<int, string>
     */
    private function parseThrowsAnnotations(string $comment): array
    {
        $throws = [];

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
     */
    private function parseReturnAnnotation(string $comment): ?string
    {
        if (!preg_match(self::REGEXP_RETURN_TYPE, $comment, $match)) {
            return null;
        }

        foreach (explode('|', end($match) ?: '') as $image) {
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
     * @return array<string>
     */
    private function parseVarAnnotation(string $comment): array
    {
        if (preg_match(self::REGEXP_VAR_TYPE, (string) $comment, $match) > 0) {
            $useSymbolTable = $this->useSymbolTable;

            return array_map(
                static fn($image) => $useSymbolTable->lookup($image) ?: $image,
                array_map('trim', explode('|', end($match) ?: '')),
            );
        }

        return [];
    }

    /**
     * This method will extract the type information of a property from it's
     * doc comment information. The returned value will be <b>null</b> when no
     * type information exists.
     *
     * @since 0.9.6
     */
    private function parseFieldDeclarationType(): ?ASTType
    {
        // Skip, if ignore annotations is set
        if ($this->ignoreAnnotations) {
            return null;
        }

        if (!$this->docComment) {
            return null;
        }

        $reference = $this->parseFieldDeclarationClassOrInterfaceReference();

        if ($reference !== null) {
            return $reference;
        }

        $annotations = $this->parseVarAnnotation($this->docComment);

        foreach ($annotations as $annotation) {
            if (Type::isPrimitiveType($annotation)) {
                $type = Type::getPrimitiveType($annotation);
                if ($type) {
                    return $this->builder->buildAstScalarType($type);
                }
            }

            if (Type::isArrayType($annotation)) {
                return $this->builder->buildAstTypeArray();
            }
        }

        return null;
    }

    /**
     * Extracts non scalar types from a field doc comment and creates a
     * matching type instance.
     *
     * @since 0.9.6
     */
    private function parseFieldDeclarationClassOrInterfaceReference(): ?ASTClassOrInterfaceReference
    {
        if (!$this->docComment) {
            return null;
        }

        $annotations = $this->parseVarAnnotation($this->docComment);

        foreach ($annotations as $annotation) {
            if (!Type::isScalarType($annotation)) {
                return $this->builder->buildAstClassOrInterfaceReference(
                    $annotation,
                );
            }
        }

        return null;
    }

    /**
     * This method parses a yield-statement node.
     *
     * @param bool $standalone Either yield is the statement (true), or nested in
     *                         an expression (false).
     */
    private function parseYield(bool $standalone): ASTYieldStatement
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

                $child = $this->parseOptionalExpression();
                if ($child) {
                    $yield->addChild($child);
                }
            }
        }

        $this->consumeComments();

        if ($standalone) {
            $this->parseStatementTermination();
        }

        return $this->setNodePositionsAndReturn($yield);
    }

    /**
     * Extracts documented <b>throws</b> and <b>return</b> types and sets them
     * to the given <b>$callable</b> instance.
     */
    private function prepareCallable(AbstractASTCallable $callable): void
    {
        // Skip, if ignore annotations is set
        if ($this->ignoreAnnotations) {
            return;
        }

        // Get all @throws Types
        $comment = $callable->getComment();
        $throws = $comment === null ? [] : $this->parseThrowsAnnotations($comment);

        foreach ($throws as $qualifiedName) {
            $callable->addExceptionClassReference(
                $this->builder->buildAstClassOrInterfaceReference($qualifiedName),
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
                $this->builder->buildAstClassOrInterfaceReference($qualifiedName),
            );
        }
    }

    /**
     * This method will consume the next token in the token stream. It will
     * throw an exception if the type of this token is not identical with
     * <b>$tokenType</b>.
     *
     * @param int $tokenType The next expected token type.
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    protected function consumeToken(int $tokenType): Token
    {
        assert($tokenType > 0);

        switch ($this->tokenizer->peek()) {
            case $tokenType:
                $next = $this->tokenizer->next();
                assert($next instanceof Token);

                return $this->tokenStack->add($next);
        }

        throw $this->getUnexpectedNextTokenException();
    }

    /**
     * This method will consume all comment tokens from the token stream.
     */
    protected function consumeComments(): void
    {
        $type = $this->tokenizer->peek();
        while ($type === Tokens::T_COMMENT || $type === Tokens::T_DOC_COMMENT) {
            $token = $this->consumeToken($type);
            $type = $this->tokenizer->peek();

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
     * Return the appropriate exception for either UnexpectedTokenException
     * configured with the next token, or TokenStreamEndException if there
     * is no more token.
     */
    private function getUnexpectedNextTokenException(): TokenStreamEndException|UnexpectedTokenException
    {
        return $this->getUnexpectedTokenException($this->tokenizer->next());
    }

    /**
     * Return the appropriate exception for either UnexpectedTokenException
     * configured with the given token, or TokenStreamEndException if given
     * null.
     *
     * @param Token|Tokenizer::T_*|null $token
     */
    private function getUnexpectedTokenException($token): TokenStreamEndException|UnexpectedTokenException
    {
        if ($token instanceof Token) {
            return new UnexpectedTokenException($token, $this->tokenizer->getSourceFile() ?? 'unknown');
        }

        return new TokenStreamEndException($this->tokenizer);
    }

    /**
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    protected function checkEllipsisInExpressionSupport(): void
    {
        throw $this->getUnexpectedNextTokenException();
    }

    /**
     * Parses throw expression syntax. available since PHP 8.0. Ex.:
     *  $callable = fn() => throw new Exception();
     *  $value = $nullableValue ?? throw new InvalidArgumentException();
     *  $value = $falsableValue ?: throw new InvalidArgumentException();
     *
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function parseThrowExpression(): ASTThrowStatement
    {
        if ($this->tokenizer->peek() === Tokens::T_THROW) {
            return $this->parseThrowStatement([
                Tokens::T_SEMICOLON,
                Tokens::T_COMMA,
                Tokens::T_COLON,
                Tokens::T_PARENTHESIS_CLOSE,
                Tokens::T_SQUARED_BRACKET_CLOSE,
            ]);
        }

        throw $this->getUnexpectedNextTokenException();
    }

    /**
     * Parses enum declaration. available since PHP 8.1. Ex.:
     *  enum Suit: string { case HEARTS = 'hearts'; }
     *
     * @throws UnexpectedTokenException
     */
    private function parseEnumDeclaration(): ASTEnum
    {
        $this->tokenStack->push();

        $enum = $this->parseEnumSignature();
        $enum = $this->parseTypeBody($enum);
        $enum->setTokens($this->tokenStack->pop());

        $this->reset();

        return $enum;
    }

    /**
     * Parses enum declaration signature. available since PHP 8.1. Ex.:
     *  enum Suit: string
     *
     * @throws TokenException
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function parseEnumSignature(): ASTEnum
    {
        $this->tokenStack->add($this->requireNextToken());
        $this->consumeComments();

        if ($this->tokenizer->peek() !== Tokens::T_STRING) {
            throw $this->getUnexpectedNextTokenException();
        }

        $name = $this->tokenizer->currentToken()?->image;
        if (!$name) {
            throw new TokenException('Invalid Enum name');
        }
        $this->consumeToken(Tokens::T_STRING);
        $this->consumeComments();
        $type = null;

        if ($this->tokenizer->peek() === Tokens::T_COLON) {
            $this->consumeToken(Tokens::T_COLON);
            $type = $this->parseTypeHint();

            if (
                !($type instanceof ASTScalarType) ||
                !in_array($type->getImage(), ['int', 'string'], true)
            ) {
                throw new TokenException(
                    "Enum backing type must be 'int' or 'string'",
                );
            }
        }

        $enum = $this->builder->buildEnum($name, $type);
        $enum->setCompilationUnit($this->compilationUnit);
        $enum->setModifiers($this->modifiers);
        $enum->setComment($this->docComment);
        $enum->setId($this->idBuilder->forClassOrInterface($enum));
        $enum->setUserDefined();

        $this->consumeComments();
        $tokenType = $this->tokenizer->peek();

        if ($tokenType === Tokens::T_IMPLEMENTS) {
            $this->consumeToken(Tokens::T_IMPLEMENTS);
            $this->parseInterfaceList($enum);
        }

        return $enum;
    }

    /**
     * Peek the next token if it's of the given type, add it to current tokenStack, and return it if so.
     *
     * @psalm-param Tokens::T_* $type
     */
    private function addTokenToStackIfType(int $type): ?Token
    {
        if ($this->tokenizer->peek() === $type) {
            $next = $this->tokenizer->next();
            assert($next instanceof Token);
            $this->tokenStack->add($next);

            return $next;
        }

        return null;
    }

    /**
     * Return the next token if it exists, else throw a TokenStreamEndException.
     *
     * @throws TokenStreamEndException
     */
    private function requireNextToken(): Token
    {
        $next = $this->tokenizer->next();

        if ($next instanceof Token) {
            return $next;
        }

        throw new TokenStreamEndException($this->tokenizer);
    }

    /**
     * @param string $numberRepresentation integer number as it appears in the code, `0xfe4`, `1_000_000`
     * @throws InvalidArgumentException
     */
    private function parseIntegerNumberImage(string $numberRepresentation): int
    {
        $numberRepresentation = trim($numberRepresentation);

        if (!preg_match(self::REGEXP_INTEGER, $numberRepresentation)) {
            throw new InvalidArgumentException("Invalid number $numberRepresentation");
        }

        return (int) $this->getNumberFromImage($numberRepresentation);
    }

    private function getNumberFromImage(string $numberRepresentation): float|int|string
    {
        $numberRepresentation = str_replace('_', '', $numberRepresentation);

        switch (substr($numberRepresentation, 0, 2)) {
            case '0x':
            case '0X':
                return hexdec(substr($numberRepresentation, 2));

            case '0b':
            case '0B':
                return bindec(substr($numberRepresentation, 2));

            default:
                if (preg_match('/^0+[oO]?(\d+)$/', $numberRepresentation, $match)) {
                    return octdec($match[1]);
                }

                return $numberRepresentation;
        }
    }

    /**
     * @throws TokenStreamEndException
     * @throws UnexpectedTokenException
     */
    private function parseEnumCase(): ASTEnumCase
    {
        $this->tokenStack->add($this->requireNextToken());
        $this->tokenStack->push();
        $this->consumeComments();
        $caseName = $this->tokenizer->currentToken()->image ?? '';

        if (!preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $caseName)) {
            throw $this->getUnexpectedNextTokenException();
        }

        $this->tokenStack->add($this->requireNextToken());
        $this->consumeComments();
        $case = $this->builder->buildEnumCase($caseName, $this->parseEnumCaseValue());
        $this->consumeComments();
        $this->consumeToken(Tokens::T_SEMICOLON);

        return $this->setNodePositionsAndReturn($case);
    }

    private function parseEnumCaseValue(): ?ASTNode
    {
        if ($this->tokenizer->peek() !== Tokens::T_EQUAL) {
            return null;
        }

        $this->consumeToken(Tokens::T_EQUAL);
        $this->consumeComments();

        $expression = $this->parseOptionalExpression();

        if (!$expression instanceof AbstractASTNode) {
            throw new MissingValueException($this->tokenizer);
        }

        return $expression;
    }

    /**
     * @psalm-param Tokens::T_* $type
     */
    private function parseNumber(int $type): string
    {
        $token = $this->consumeToken($type);
        $number = (string) $token->image;

        while ($next = $this->addTokenToStackIfType(Tokens::T_STRING)) {
            $number .= $next->image;
        }

        return $number;
    }
}
