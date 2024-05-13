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

use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamedArgument;
use PDepend\Source\AST\ASTVariable;

/**
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion80
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 * @group php8
 */
class NamedArgumentsTest extends PHPParserVersion80TestCase
{
    public function testNamedArguments(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTArguments $arguments */
        $arguments = $method->getFirstChildOfType(
            ASTArguments::class
        );
        $children = $arguments->getChildren();

        $this->assertCount(2, $children);
        $this->assertInstanceOf(ASTLiteral::class, $children[0]);
        $this->assertSame('5623', $children[0]->getImage());
        $this->assertInstanceOf(ASTNamedArgument::class, $children[1]);
        /** @var ASTNamedArgument $argument */
        $argument = $children[1];
        $this->assertSame('thousands_separator', $argument->getName());
        $this->assertInstanceOf(ASTLiteral::class, $argument->getValue());
        $this->assertSame("' '", $argument->getValue()->getImage());
        $this->assertSame("thousands_separator: ' '", $argument->getImage());
    }

    public function testNamedArgumentsWithArrays(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTArguments $arguments */
        $arguments = $method->getFirstChildOfType(
            ASTArguments::class
        );
        $children = $arguments->getChildren();

        $this->assertCount(4, $children);
        $this->assertInstanceOf(ASTLiteral::class, $children[0]);
        $this->assertSame("'/thing/{id}'", $children[0]->getImage());
        $this->assertInstanceOf(ASTNamedArgument::class, $children[3]);
        /** @var ASTNamedArgument $argument */
        $argument = $children[3];

        $this->assertSame('methods', $argument->getName());
        $this->assertInstanceOf(ASTArray::class, $argument->getValue());
    }

    public function testNamedArgumentsWithDefaultName(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTArguments $arguments */
        $arguments = $method->getFirstChildOfType(
            ASTArguments::class
        );
        $children = $arguments->getChildren();

        $this->assertCount(1, $children);
        $this->assertInstanceOf(ASTNamedArgument::class, $children[0]);
        $this->assertSame("default: 'bar'", $children[0]->getImage());
    }

    public function testNamedArgumentsInInstances(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTArguments $arguments */
        $arguments = $method->getFirstChildOfType(
            ASTArguments::class
        );
        $children = $arguments->getChildren();

        $this->assertCount(4, $children);
        $this->assertInstanceOf(ASTLiteral::class, $children[0]);
        $this->assertSame("'/thing/{id}'", $children[0]->getImage());
        $this->assertInstanceOf(ASTNamedArgument::class, $children[3]);
        /** @var ASTNamedArgument $argument */
        $argument = $children[3];

        $this->assertSame('methods', $argument->getName());
        $this->assertInstanceOf(ASTArray::class, $argument->getValue());
    }

    public function testNamedArgumentsFindVariable(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTNamedArgument $namedArgument */
        $namedArgument = $method->getFirstChildOfType(
            ASTNamedArgument::class
        );
        /** @var ASTVariable[] $variables */
        $variables = $method->findChildrenOfType(
            ASTVariable::class
        );
        $this->assertCount(2, $variables);

        $foundVariable = $namedArgument->getFirstChildOfType(ASTVariable::class);
        $this->assertSame($variables[1], $foundVariable);
        $this->assertSame('$foo', $foundVariable->getImage());
        /** @var ASTExpression $expression */
        $expression = $variables[1]->getParent();
        /** @var ASTNamedArgument $expression */
        $expressionParent = $expression->getParent();
        $this->assertSame($namedArgument, $expressionParent);
        $this->assertSame(
            [$variables[1]],
            $namedArgument->findChildrenOfType(ASTVariable::class)
        );
    }

    public function testNamedArgumentsFindDirectVariable(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTNamedArgument $namedArgument */
        $namedArgument = $method->getFirstChildOfType(
            ASTNamedArgument::class
        );
        /** @var ASTVariable[] $variables */
        $variables = $method->findChildrenOfType(
            ASTVariable::class
        );
        $this->assertCount(2, $variables);

        $foundVariable = $namedArgument->getFirstChildOfType(ASTVariable::class);
        $this->assertSame($variables[1], $foundVariable);
        $this->assertSame('$foo', $foundVariable->getImage());
        /** @var ASTNamedArgument $variableParent */
        $variableParent = $variables[1]->getParent();
        $this->assertSame($namedArgument, $variableParent);
        $this->assertSame(
            [$variables[1]],
            $namedArgument->findChildrenOfType(ASTVariable::class)
        );
    }
}
