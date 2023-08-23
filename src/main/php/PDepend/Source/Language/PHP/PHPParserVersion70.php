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
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTType;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 7.0.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 2.3
 */
abstract class PHPParserVersion70 extends PHPParserVersion56
{
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

        return parent::isConstantName($tokenType);
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
        switch (true) {
            case ($this->isConstantName($tokenType)):
                $node = $this->parseLiteral();
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
     * @return ASTType
     */
    protected function parseReturnTypeHint()
    {
        $this->consumeComments();

        return $this->parseEndReturnTypeHint();
    }

    protected function parseTypeHint()
    {
        switch ($this->tokenizer->peek()) {
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
     * Parses a scalar type hint or a callable type hint.
     *
     * @param string $image
     *
     * @return ASTType|false
     */
    protected function parseScalarOrCallableTypeHint($image)
    {
        switch (strtolower($image)) {
            case 'int':
            case 'bool':
            case 'float':
            case 'string':
                return $this->builder->buildAstScalarType($image);
            case 'callable':
                return $this->builder->buildAstTypeCallable();
            case 'void':
            case 'iterable':
                throw $this->getUnexpectedTokenException($this->tokenizer->prevToken());
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
     * @since 2.3
     */
    protected function parseOptionalExpressionForVersion()
    {
        return $this->parseExpressionVersion70()
            ?: parent::parseOptionalExpressionForVersion();
    }

    /**
     * In this method we implement parsing of PHP 7.0 specific expressions.
     *
     * @return ASTNode|null
     *
     * @since 2.3
     */
    protected function parseExpressionVersion70()
    {
        $this->consumeComments();
        $nextTokenType = $this->tokenizer->peek();

        switch ($nextTokenType) {
            case Tokens::T_SPACESHIP:
            case Tokens::T_COALESCE:
                $token = $this->consumeToken($nextTokenType);

                $expr = $this->builder->buildAstExpression($token->image);
                $expr->configureLinesAndColumns(
                    $token->startLine,
                    $token->endLine,
                    $token->startColumn,
                    $token->endColumn
                );

                return $expr;
        }

        return null;
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
}
