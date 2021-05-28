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

namespace PDepend\Source\Language\PHP\Features\PHP80;

use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTVariableDeclarator;

/**
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion80
 * @group unittest
 * @group php8
 */
class ThrowExpressionTest extends PHPParserVersion80Test
{
    /**
     * @return void
     */
    public function testNullcoalescingThrow()
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType('PDepend\\Source\\AST\\ASTReturnStatement');

        $expression = $returns[0]->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $expression);

        $value = $expression->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $value);
        $this->assertSame('$value', $value->getImage());

        $throw = $expression->getChild(2);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTThrowStatement', $throw);

        $exceptionClass = $throw->findChildrenOfType('PDepend\\Source\\AST\\ASTClassReference');
        $this->assertSame('\\InvalidArgumentException', $exceptionClass[0]->getImage());

        $exceptionMessage = $throw->findChildrenOfType('PDepend\\Source\\AST\\ASTLiteral');
        $this->assertSame('\'should not be null\'', $exceptionMessage[0]->getImage());
    }

    /**
     * @return void
     */
    public function testShorthandTernaryOperator()
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType('PDepend\\Source\\AST\\ASTReturnStatement');

        $expression = $returns[0]->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $expression);

        $value = $expression->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $value);
        $this->assertSame('$value', $value->getImage());

        $ternary = $expression->getChild(1);
        $throw = $ternary->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTThrowStatement', $throw);

        $exceptionClass = $throw->findChildrenOfType('PDepend\\Source\\AST\\ASTClassReference');
        $this->assertSame('\\InvalidArgumentException', $exceptionClass[0]->getImage());

        $exceptionMessage = $throw->findChildrenOfType('PDepend\\Source\\AST\\ASTLiteral');
        $this->assertSame('\'should not be empty\'', $exceptionMessage[0]->getImage());
    }

    /**
     * @return void
     */
    public function testTernaryOperator()
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType('PDepend\\Source\\AST\\ASTReturnStatement');

        $expression = $returns[0]->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $expression);

        $value = $expression->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $value);
        $this->assertSame('$value', $value->getImage());

        $ternary = $expression->getChild(1);

        $throw = $ternary->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTThrowStatement', $throw);

        $exceptionClass = $throw->findChildrenOfType('PDepend\\Source\\AST\\ASTClassReference');
        $this->assertSame('\\InvalidArgumentException', $exceptionClass[0]->getImage());

        $exceptionMessage = $throw->findChildrenOfType('PDepend\\Source\\AST\\ASTLiteral');
        $this->assertSame('\'should be empty\'', $exceptionMessage[0]->getImage());

        $elseValue = $ternary->getChild(1);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $elseValue);
        $this->assertSame('$value', $elseValue->getImage());
    }

    /**
     * @return void
     */
    public function testThrowFromArrowFunction()
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType('PDepend\\Source\\AST\\ASTReturnStatement');

        $closure = $returns[0]->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClosure', $closure);

        $throw = $closure->getChild(1)->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTThrowStatement', $throw);

        $exceptionClass = $throw->findChildrenOfType('PDepend\\Source\\AST\\ASTClassReference');
        $this->assertSame('\\BadMethodCallException', $exceptionClass[0]->getImage());

        $exceptionMessage = $throw->findChildrenOfType('PDepend\\Source\\AST\\ASTLiteral');
        $this->assertSame('\'not implemented\'', $exceptionMessage[0]->getImage());
    }

    /**
     * @return void
     */
    public function testThrowFromArrowFunctionAsParameter()
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType('PDepend\\Source\\AST\\ASTReturnStatement');

        $expression = $returns[0]->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $expression);

        $value = $expression->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $value);
        $this->assertSame('$value', $value->getImage());

        $throw = $expression->getChild(2);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTThrowStatement', $throw);

        $exceptionClass = $throw->findChildrenOfType('PDepend\\Source\\AST\\ASTClassReference');
        $this->assertSame('\\InvalidArgumentException', $exceptionClass[0]->getImage());

        $exceptionMessage = $throw->findChildrenOfType('PDepend\\Source\\AST\\ASTLiteral');
        $this->assertSame('\'should not be null\'', $exceptionMessage[0]->getImage());

        $expression = $returns[1]->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $expression);

        $value = $expression->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $value);
        $this->assertSame('$value', $value->getImage());

        $throw = $expression->getChild(1)->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTThrowStatement', $throw);

        $exceptionClass = $throw->findChildrenOfType('PDepend\\Source\\AST\\ASTClassReference');
        $this->assertSame('\\InvalidArgumentException', $exceptionClass[0]->getImage());

        $exceptionMessage = $throw->findChildrenOfType('PDepend\\Source\\AST\\ASTLiteral');
        $this->assertSame('\'should not be empty\'', $exceptionMessage[0]->getImage());
    }
}
