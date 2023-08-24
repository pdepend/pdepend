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

use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTType;
use PDepend\Source\AST\State;
use PDepend\Source\Parser\InvalidStateException;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 7.1.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 2.4
 */
abstract class PHPParserVersion71 extends PHPParserVersion70
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

        return parent::parseReturnTypeHint();
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

        return parent::parseTypeHint();
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
            case 'void':
                return $this->builder->buildAstScalarType($image);
            case 'iterable':
                return $this->builder->buildAstTypeIterable();
        }

        return parent::parseScalarOrCallableTypeHint($image);
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
}
