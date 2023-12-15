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
 *
 * @since 2.3
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTClassFqnPostfix;
use PDepend\Source\AST\ASTConstant;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTNamedArgument;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTType;
use PDepend\Source\AST\ASTValue;
use PDepend\Source\AST\State;
use PDepend\Source\Parser\InvalidStateException;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\FullTokenizer;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 7.1.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 2.4
 */
abstract class PHPParserVersion71 extends AbstractPHPParser
{
    /**
     * Return true if current PHP level supports keys in lists.
     *
     * @return bool
     */
    protected function supportsKeysInList()
    {
        return true;
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
        return in_array($tokenType ?: $this->tokenizer->peek(), array(Tokens::T_LIST, Tokens::T_SQUARED_BRACKET_OPEN));
    }

    /**
     * @return ASTType
     */
    protected function parseReturnTypeHint()
    {
        $this->consumeComments();
        $this->consumeQuestionMark();
        $this->consumeComments();

        return $this->parseEndReturnTypeHint();
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
     */
    protected function parseFormalParameterOrTypeHintOrByReference()
    {
        $this->consumeComments();
        $this->consumeQuestionMark();

        return parent::parseFormalParameterOrTypeHintOrByReference();
    }

    protected function parseTypeHint()
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
                    ? ($this->parseScalarOrCallableTypeHint($name) ?: null)
                    : $this->builder->buildAstClassOrInterfaceReference($name);

            default:
                return parent::parseTypeHint();
        }
    }

    protected function parseUnknownDeclaration($tokenType, $modifiers)
    {
        if ($tokenType == Tokens::T_CONST) {
            $definition = $this->parseConstantDefinition();
            $constantModifiers = $this->getModifiersForConstantDefinition($tokenType, $modifiers);
            $definition->setModifiers($constantModifiers);

            return $definition;
        }

        return parent::parseUnknownDeclaration($tokenType, $modifiers);
    }

    /**
     * Parses a scalar type hint or a callable type hint.
     *
     * @param string $image
     */
    protected function parseScalarOrCallableTypeHint($image)
    {
        switch (strtolower($image)) {
            case 'int':
            case 'bool':
            case 'float':
            case 'string':
            case 'void':
                return $this->builder->buildAstScalarType($image);
            case 'callable':
                return $this->builder->buildAstTypeCallable();
            case 'iterable':
                return $this->builder->buildAstTypeIterable();
        }

        return false;
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
        do {
            $repeat = false;
            parent::parseCatchExceptionClass($stmt);

            if (Tokens::T_BITWISE_OR === $this->tokenizer->peek()) {
                $this->consumeToken(Tokens::T_BITWISE_OR);
                $repeat = true;
            }
        } while ($repeat === true);
    }

    /**
     * Return true if [, $foo] or [$foo, , $bar] is allowed.
     *
     * @return bool
     */
    protected function canHaveCommaBetweenArrayElements()
    {
        return true;
    }

    /**
     * @return void
     */
    private function consumeQuestionMark()
    {
        if ($this->tokenizer->peek() === Tokens::T_QUESTION_MARK) {
            $this->consumeToken(Tokens::T_QUESTION_MARK);
        }
    }

    /**
     * @param int $tokenType
     * @param int $modifiers
     *
     * @return int
     */
    private function getModifiersForConstantDefinition($tokenType, $modifiers)
    {
        $allowed = State::IS_PUBLIC | State::IS_PROTECTED | State::IS_PRIVATE;
        $modifiers &= $allowed;

        if ($this->classOrInterface instanceof ASTInterface && ($modifiers & (State::IS_PROTECTED | State::IS_PRIVATE)) !== 0) {
            throw new InvalidStateException(
                $this->requireNextToken()->startLine,
                (string) $this->compilationUnit,
                sprintf(
                    'Constant can\'t be declared private or protected in interface "%s".',
                    $this->classOrInterface->getName()
                )
            );
        }

        return $modifiers;
    }

    /**
     * Return true if the current node can be used as a list key.
     *
     * @param ASTExpression|null $node
     *
     * @return bool
     */
    protected function canBeListKey($node)
    {
        // Starting with PHP 7.1, any expression can be used as list key
        return true;
    }

    /**
     * @param int $tokenType
     *
     * @return bool
     */
    protected function isConstantName($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_CALLABLE:
            case Tokens::T_TRAIT:
            case Tokens::T_EXTENDS:
            case Tokens::T_IMPLEMENTS:
            case Tokens::T_STATIC:
            case Tokens::T_ABSTRACT:
            case Tokens::T_FINAL:
            case Tokens::T_PUBLIC:
            case Tokens::T_PROTECTED:
            case Tokens::T_PRIVATE:
            case Tokens::T_CONST:
            case Tokens::T_ENDDECLARE:
            case Tokens::T_ENDFOR:
            case Tokens::T_ENDFOREACH:
            case Tokens::T_ENDIF:
            case Tokens::T_ENDWHILE:
            case Tokens::T_EMPTY:
            case Tokens::T_EVAL:
            case Tokens::T_LOGICAL_AND:
            case Tokens::T_GLOBAL:
            case Tokens::T_GOTO:
            case Tokens::T_INSTANCEOF:
            case Tokens::T_INSTEADOF:
            case Tokens::T_INTERFACE:
            case Tokens::T_ISSET:
            case Tokens::T_NAMESPACE:
            case Tokens::T_NEW:
            case Tokens::T_LOGICAL_OR:
            case Tokens::T_LOGICAL_XOR:
            case Tokens::T_TRY:
            case Tokens::T_USE:
            case Tokens::T_VAR:
            case Tokens::T_EXIT:
            case Tokens::T_LIST:
            case Tokens::T_CLONE:
            case Tokens::T_INCLUDE:
            case Tokens::T_INCLUDE_ONCE:
            case Tokens::T_THROW:
            case Tokens::T_ARRAY:
            case Tokens::T_PRINT:
            case Tokens::T_ECHO:
            case Tokens::T_REQUIRE:
            case Tokens::T_REQUIRE_ONCE:
            case Tokens::T_RETURN:
            case Tokens::T_ELSE:
            case Tokens::T_ELSEIF:
            case Tokens::T_DEFAULT:
            case Tokens::T_BREAK:
            case Tokens::T_CONTINUE:
            case Tokens::T_SWITCH:
            case Tokens::T_YIELD:
            case Tokens::T_FUNCTION:
            case Tokens::T_IF:
            case Tokens::T_ENDSWITCH:
            case Tokens::T_FINALLY:
            case Tokens::T_FOR:
            case Tokens::T_FOREACH:
            case Tokens::T_DECLARE:
            case Tokens::T_CASE:
            case Tokens::T_DO:
            case Tokens::T_WHILE:
            case Tokens::T_AS:
            case Tokens::T_CATCH:
                //case Tokens::T_DIE:
            case Tokens::T_SELF:
            case Tokens::T_PARENT:
            case Tokens::T_UNSET:
                return true;
        }

        return $this->isFunctionName($tokenType);;
    }


    /**
     * @param int $tokenType
     *
     * @return bool
     */
    protected function isMethodName($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_CLASS:
                return true;
        }

        return $this->isConstantName($tokenType);
    }

    /**
     * @param int $tokenType
     *
     * @return bool
     */
    protected function isTypeHint($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_SELF:
            case Tokens::T_PARENT:
            case Tokens::T_CALLABLE:
                return true;
        }

        return parent::isTypeHint($tokenType);
    }

    /**
     * @return ASTNode
     */
    protected function parsePostfixIdentifier()
    {
        $tokenType = $this->tokenizer->peek();
        if ($this->isConstantName($tokenType)) {
            return $this->parseOptionalIndexExpression($this->parseLiteral());
        }
        switch ($tokenType) {
            case Tokens::T_CURLY_BRACE_OPEN:
                $node = $this->parseCompoundExpression();
                break;
            default:
                $node = parent::parsePostfixIdentifier();
                break;
        }
        return $this->parseOptionalIndexExpression($node);
    }

    protected function parseCallableDeclarationAddition($callable)
    {
        $this->consumeComments();
        if (Tokens::T_COLON != $this->tokenizer->peek()) {
            return $callable;
        }

        $this->consumeToken(Tokens::T_COLON);

        $type = $this->parseReturnTypeHint();
        $callable->addChild($type);

        return $callable;
    }

    /**
     * @return ASTType
     */
    protected function parseEndReturnTypeHint()
    {
        switch ($this->tokenizer->peek()) {
            case Tokens::T_ARRAY:
                return $this->parseArrayType();
            case Tokens::T_SELF:
                return $this->parseSelfType();
            case Tokens::T_PARENT:
                return $this->parseParentType();
            default:
                return $this->parseTypeHint();
        }
    }

    /**
     * Parses any expression that is surrounded by an opening and a closing
     * parenthesis
     *
     * @return ASTExpression
     */
    protected function parseParenthesisExpression()
    {
        $this->tokenStack->push();
        $this->consumeComments();

        $expr = $this->builder->buildAstExpression();
        $expr = $this->parseBraceExpression(
            $expr,
            $this->consumeToken(Tokens::T_PARENTHESIS_OPEN),
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_COMMA
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
     * Tests if the given image is a PHP 7.0 type hint.
     *
     * @param string $image
     *
     * @return bool
     */
    protected function isScalarOrCallableTypeHint($image)
    {
        switch (strtolower($image)) {
            case 'int':
            case 'bool':
            case 'float':
            case 'string':
            case 'callable':
            case 'iterable':
            case 'void':
                return true;
        }

        return false;
    }

    /**
     * Parse the type reference used in an allocation expression.
     *
     * @return ASTNode
     *
     * @since 2.3
     */
    protected function parseAllocationExpressionTypeReference(ASTAllocationExpression $allocation)
    {
        return $this->parseAnonymousClassDeclaration($allocation)
            ?: parent::parseAllocationExpressionTypeReference($allocation);
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
    protected function parseAnonymousClassDeclaration(ASTAllocationExpression $allocation)
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

    protected function parseOptionalMemberPrimaryPrefix(ASTNode $node)
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

    protected function parseParenthesisExpressionOrPrimaryPrefixForVersion(ASTExpression $expr)
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
        $this->consumeComments();
        $nextTokenType = $this->tokenizer->peek();

        switch ($nextTokenType) {
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
                return parent::parseOptionalExpressionForVersion();
        }
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
     * @since 2.0.7
     */
    protected function parseFormalParameter()
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
     * @param array<string> $fragments
     *
     * @return void
     */
    protected function parseUseDeclarationForVersion(array $fragments)
    {
        if (Tokens::T_CURLY_BRACE_OPEN === $this->tokenizer->peek()) {
            $this->parseUseDeclarationVersion70($fragments);

            return;
        }

        parent::parseUseDeclarationForVersion($fragments);
    }

    /**
     * @param array<string> $fragments
     *
     * @return void
     */
    protected function parseUseDeclarationVersion70(array $fragments)
    {
        $namespacePrefixReplaced = $this->namespacePrefixReplaced;

        $this->consumeToken(Tokens::T_CURLY_BRACE_OPEN);
        $this->consumeComments();

        do {
            $nextToken = $this->tokenizer->peek();
            switch ($nextToken) {
                case Tokens::T_CONST:
                case Tokens::T_FUNCTION:
                    $this->consumeToken($nextToken);
            }

            if ($this->allowUseGroupDeclarationTrailingComma() &&
                Tokens::T_CURLY_BRACE_CLOSE === $this->tokenizer->peek()
            ) {
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
            $this->useSymbolTable->add($image, join('', array_merge($fragments, $subFragments)));
        } while (true);

        if (isset($image, $subFragments)) {
            $this->useSymbolTable->add($image, join('', array_merge($fragments, $subFragments)));
        }

        $this->consumeToken(Tokens::T_CURLY_BRACE_CLOSE);
        $this->consumeComments();

        $this->namespacePrefixReplaced = $namespacePrefixReplaced;
    }

    /**
     * @param array<string> $previousElements
     *
     * @return string|null
     */
    protected function parseQualifiedNameElement(array $previousElements)
    {
        if (Tokens::T_CURLY_BRACE_OPEN !== $this->tokenizer->peek()) {
            return parent::parseQualifiedNameElement($previousElements);
        }

        if (count($previousElements) >= 2 && '\\' === end($previousElements)) {
            return null;
        }

        throw $this->getUnexpectedNextTokenException();
    }

    /**
     * use Foo\Bar\{TestA, TestB} is allowed since PHP 7.0
     * use Foo\Bar\{TestA, TestB,} but trailing comma isn't
     *
     * @return bool
     */
    protected function allowUseGroupDeclarationTrailingComma()
    {
        return false;
    }

    /**
     * Parses additional static values that are valid in the supported php version.
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTValue|null
     */
    protected function parseStaticValueVersionSpecific(ASTValue $value)
    {
        $expressions = array();

        while (($tokenType = $this->tokenizer->peek()) != Tokenizer::T_EOF) {
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
                case ($this->isArrayStartDelimiter()):
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
                    $this->checkEllipsisInExpressionSupport();
                // no break
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
                    $expressions[] = $this->parseAssignmentExpression(
                        array_pop($expressions)
                    );
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

        $expressions = $this->reduce($expressions);

        $count = count($expressions);
        if ($count == 0) {
            return null;
        } elseif ($count == 1) {
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
     * Parses use declarations that are valid in the supported php version.
     *
     * @return void
     */
    protected function parseUseDeclarations()
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
     * @return ASTConstant|ASTNamedArgument
     */
    protected function parseConstantArgument(ASTConstant $constant, ASTArguments $arguments)
    {
        return $constant;
    }

    /**
     * @return ASTArguments
     */
    protected function parseArgumentList(ASTArguments $arguments)
    {
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

        return $arguments;
    }

    /**
     * @return ASTNode|null
     */
    protected function parseArgumentExpression()
    {
        return $this->parseOptionalExpression();
    }

    /**
     * Parses the value of a php constant. By default this can be only static
     * values that were allowed in the oldest supported PHP version.
     *
     * @return ASTValue
     */
    protected function parseConstantDeclaratorValue()
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
     * Determines if the following expression can be stored as a static value.
     *
     * @return bool
     */
    protected function isFollowedByStaticValueOrStaticArray()
    {
        // If we can't anticipate, we should assume it can be a dynamic value
        if (!($this->tokenizer instanceof FullTokenizer)) {
            return false;
        }

        for ($i = 0; $type = $this->tokenizer->peekAt($i); $i++) {
            switch ($type) {
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

        return false;
    }

    /**
     * Parses a full qualified class name postfix.
     *
     * parseFullQualifiedClassNamePostfix() exists since 2.0.0 and have been customized for PHP 5.5 since 2.6.0.
     *
     * @return ASTClassFqnPostfix
     *
     * @since 2.0.0
     */
    protected function parseFullQualifiedClassNamePostfix()
    {
        $this->tokenStack->push();

        $this->consumeToken(Tokens::T_CLASS_FQN);

        return $this->setNodePositionsAndReturn(
            $this->builder->buildAstClassFqnPostfix()
        );
    }

    /**
     * Will return <b>true</b> if the given <b>$tokenType</b> is a valid class
     * name part.
     *
     * @param int $tokenType The type of a parsed token.
     *
     * @return bool
     *
     * @since  0.10.6
     */
    protected function isClassName($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_NULL:
            case Tokens::T_TRUE:
            case Tokens::T_FALSE:
            case Tokens::T_STRING:
            case Tokens::T_READONLY:
                return true;
        }

        return false;
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
        switch ($tokenType) {
            case Tokens::T_STRING:
            case Tokens::T_NULL:
            case Tokens::T_SELF:
            case Tokens::T_TRUE:
            case Tokens::T_FALSE:
            case Tokens::T_PARENT:
                return true;
        }
        return false;
    }

    /**
     * Tests if the given token type is a reserved keyword in the supported PHP
     * version.
     *
     * @param int $tokenType
     *
     * @return bool
     */
    protected function isKeyword($tokenType)
    {
        switch ($tokenType) {
            case Tokens::T_CLASS:
            case Tokens::T_TRAIT:
            case Tokens::T_CALLABLE:
            case Tokens::T_INSTEADOF:
            case Tokens::T_INTERFACE:
                return true;
        }
        return false;
    }

    /**
     * Tests if the next token is a valid array start delimiter in the supported
     * PHP version.
     *
     * @return bool
     *
     * @since 1.0.0
     */
    protected function isArrayStartDelimiter()
    {
        switch ($this->tokenizer->peek()) {
            case Tokens::T_ARRAY:
            case Tokens::T_SQUARED_BRACKET_OPEN:
                return true;
        }

        return false;
    }

    /**
     * Parses a php array declaration.
     *
     * @param bool $static
     *
     * @return ASTArray
     *
     * @since 1.0.0
     */
    protected function parseArray(ASTArray $array, $static = false)
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
     * Parses an integer value.
     *
     * @throws UnexpectedTokenException
     *
     * @return ASTLiteral
     */
    protected function parseIntegerNumber()
    {
        $token = $this->consumeToken(Tokens::T_LNUMBER);
        $number = $token->image;

        while ($next = $this->addTokenToStackIfType(Tokens::T_STRING)) {
            $number .= $next->image;
        }

        if ('0' !== substr($number, 0, 1)) {
            goto BUILD_LITERAL;
        }

        if (Tokens::T_STRING !== $this->tokenizer->peek()) {
            goto BUILD_LITERAL;
        }

        $token1 = $this->consumeToken(Tokens::T_STRING);
        if (0 === preg_match('(^b[01]+$)i', $token1->image)) {
            throw new UnexpectedTokenException(
                $token1,
                $this->tokenizer->getSourceFile()
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
            $token->endColumn
        );

        return $literal;
    }
}
