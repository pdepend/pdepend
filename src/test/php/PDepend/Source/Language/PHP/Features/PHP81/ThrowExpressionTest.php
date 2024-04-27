<?php

/**
 * This file is part of PDepend.
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

namespace PDepend\Source\Language\PHP\Features\PHP81;

use PDepend\Source\AST\ASTArrayIndexExpression;
use PDepend\Source\AST\ASTClassReference;
use PDepend\Source\AST\ASTClosure;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTReturnStatement;
use PDepend\Source\AST\ASTThrowStatement;
use PDepend\Source\AST\ASTVariable;

/**
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @group unittest
 * @group php8
 */
class ThrowExpressionTest extends PHPParserVersion81TestCase
{
    public function testNullcoalescingThrow(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();

        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType(ASTReturnStatement::class);

        $expression = $returns[0]->getChild(0);
        static::assertInstanceOf(ASTExpression::class, $expression);

        $value = $expression->getChild(0);
        static::assertInstanceOf(ASTVariable::class, $value);
        static::assertSame('$value', $value->getImage());

        $throw = $expression->getChild(2);
        static::assertInstanceOf(ASTThrowStatement::class, $throw);

        $exceptionClass = $throw->findChildrenOfType(ASTClassReference::class);
        static::assertSame('\\InvalidArgumentException', $exceptionClass[0]->getImage());

        $exceptionMessage = $throw->findChildrenOfType(ASTLiteral::class);
        static::assertSame('\'should not be null\'', $exceptionMessage[0]->getImage());
    }

    public function testShorthandTernaryOperator(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();

        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType(ASTReturnStatement::class);

        $expression = $returns[0]->getChild(0);
        static::assertInstanceOf(ASTExpression::class, $expression);

        $value = $expression->getChild(0);
        static::assertInstanceOf(ASTVariable::class, $value);
        static::assertSame('$value', $value->getImage());

        $ternary = $expression->getChild(1);
        $throw = $ternary->getChild(0);
        static::assertInstanceOf(ASTThrowStatement::class, $throw);

        $exceptionClass = $throw->findChildrenOfType(ASTClassReference::class);
        static::assertSame('\\InvalidArgumentException', $exceptionClass[0]->getImage());

        $exceptionMessage = $throw->findChildrenOfType(ASTLiteral::class);
        static::assertSame('\'should not be empty\'', $exceptionMessage[0]->getImage());
    }

    public function testTernaryOperator(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();

        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType(ASTReturnStatement::class);

        $expression = $returns[0]->getChild(0);
        static::assertInstanceOf(ASTExpression::class, $expression);

        $value = $expression->getChild(0);
        static::assertInstanceOf(ASTVariable::class, $value);
        static::assertSame('$value', $value->getImage());

        $ternary = $expression->getChild(1);

        $throw = $ternary->getChild(0);
        static::assertInstanceOf(ASTThrowStatement::class, $throw);

        $exceptionClass = $throw->findChildrenOfType(ASTClassReference::class);
        static::assertSame('\\InvalidArgumentException', $exceptionClass[0]->getImage());

        $exceptionMessage = $throw->findChildrenOfType(ASTLiteral::class);
        static::assertSame('\'should be empty\'', $exceptionMessage[0]->getImage());

        $elseValue = $ternary->getChild(1);
        static::assertInstanceOf(ASTVariable::class, $elseValue);
        static::assertSame('$value', $elseValue->getImage());
    }

    public function testThrowFromArrowFunction(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();

        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType(ASTReturnStatement::class);

        $closure = $returns[0]->getChild(0);
        static::assertInstanceOf(ASTClosure::class, $closure);

        $throw = $closure->getChild(1)->getChild(0);
        static::assertInstanceOf(ASTThrowStatement::class, $throw);

        $exceptionClass = $throw->findChildrenOfType(ASTClassReference::class);
        static::assertSame('\\BadMethodCallException', $exceptionClass[0]->getImage());

        $exceptionMessage = $throw->findChildrenOfType(ASTLiteral::class);
        static::assertSame('\'not implemented\'', $exceptionMessage[0]->getImage());
    }

    public function testThrowFromArrowFunctionAsParameter(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();

        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType(ASTReturnStatement::class);

        $expression = $returns[0]->getChild(0);
        static::assertInstanceOf(ASTExpression::class, $expression);

        $value = $expression->getChild(0);
        static::assertInstanceOf(ASTVariable::class, $value);
        static::assertSame('$value', $value->getImage());

        $throw = $expression->getChild(2);
        static::assertInstanceOf(ASTThrowStatement::class, $throw);

        $exceptionClass = $throw->findChildrenOfType(ASTClassReference::class);
        static::assertSame('\\InvalidArgumentException', $exceptionClass[0]->getImage());

        $exceptionMessage = $throw->findChildrenOfType(ASTLiteral::class);
        static::assertSame('\'should not be null\'', $exceptionMessage[0]->getImage());

        $expression = $returns[1]->getChild(0);
        static::assertInstanceOf(ASTExpression::class, $expression);

        $value = $expression->getChild(0);
        static::assertInstanceOf(ASTVariable::class, $value);
        static::assertSame('$value', $value->getImage());

        $throw = $expression->getChild(1)->getChild(0);
        static::assertInstanceOf(ASTThrowStatement::class, $throw);

        $exceptionClass = $throw->findChildrenOfType(ASTClassReference::class);
        static::assertSame('\\InvalidArgumentException', $exceptionClass[0]->getImage());

        $exceptionMessage = $throw->findChildrenOfType(ASTLiteral::class);
        static::assertSame('\'should not be empty\'', $exceptionMessage[0]->getImage());

        $expression = $returns[2]->getChild(0);
        static::assertInstanceOf(ASTExpression::class, $expression);

        $value = $expression->getChild(0);
        static::assertInstanceOf(ASTVariable::class, $value);
        static::assertSame('$value', $value->getImage());

        $throw = $expression->getChild(2);
        static::assertInstanceOf(ASTThrowStatement::class, $throw);

        $exceptionClass = $throw->findChildrenOfType(ASTClassReference::class);
        static::assertSame('\\InvalidArgumentException', $exceptionClass[0]->getImage());

        static::assertEmpty($throw->findChildrenOfType(ASTLiteral::class));

        $methods = $this->getFirstTypeForTestCase()
            ->getMethods();

        /** @var ASTMethod $arrayAccessMethod */
        $arrayAccessMethod = $methods[2];

        /** @var ASTReturnStatement $return */
        $return = $arrayAccessMethod->getFirstChildOfType(ASTReturnStatement::class);
        $value = $return->getChild(0);
        static::assertInstanceOf(ASTArrayIndexExpression::class, $value);

        $children = $value->getChildren();

        static::assertCount(2, $children);
        static::assertInstanceOf(ASTVariable::class, $children[0]);
        static::assertSame('$a', $children[0]->getImage());
        static::assertInstanceOf(ASTExpression::class, $children[1]);

        $children = $children[1]->getChildren();
        static::assertCount(3, $children);
        static::assertInstanceOf(ASTVariable::class, $children[0]);
        static::assertSame('$value', $children[0]->getImage());
        static::assertInstanceOf(ASTExpression::class, $children[1]);
        static::assertSame('??', $children[1]->getImage());
        static::assertInstanceOf(ASTThrowStatement::class, $children[2]);
    }
}
