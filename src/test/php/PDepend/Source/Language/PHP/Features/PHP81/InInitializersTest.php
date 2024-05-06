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

use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTScalarType;
use PDepend\Source\AST\ASTValue;
use PDepend\Source\AST\ASTVariableDeclarator;

/**
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion81
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 * @group php8.1
 */
class InInitializersTest extends PHPParserVersion81TestCase
{
    public function testInInitializers(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $children = $method->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFormalParameters', $children[0]);

        /** @var ASTFormalParameters $parametersBag */
        $parametersBag = $children[0];
        /** @var ASTFormalParameter[] $parameters */
        $parameters = $parametersBag->getChildren();

        $this->assertCount(1, $parameters);

        $classRef = $parameters[0];

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFormalParameter', $classRef);
        $this->assertTrue($classRef->isPromoted());
        $this->assertTrue($classRef->isPublic());

        /** @var ASTVariableDeclarator $variable */
        $variable = $classRef->getChild(1);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariableDeclarator', $variable);

        /** @var ASTValue $defaultValue */
        $defaultValue = $variable->getValue();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTValue', $defaultValue);
        $this->assertTrue($defaultValue->isValueAvailable());

        /** @var ASTAllocationExpression $expression */
        $expression = $defaultValue->getValue();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTAllocationExpression', $expression);
        $this->assertSame('Bar', $expression->getChild(0)->getImage());
    }

    public function testInInitializersMultipleProperties(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $children = $method->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFormalParameters', $children[0]);

        /** @var ASTFormalParameters $parametersBag */
        $parametersBag = $children[0];
        /** @var ASTFormalParameter[] $parameters */
        $parameters = $parametersBag->getChildren();

        $this->assertCount(4, $parameters);

        $classRef = $parameters[0];

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFormalParameter', $classRef);
        $this->assertTrue($classRef->isPromoted());
        $this->assertTrue($classRef->isPublic());

        /** @var ASTClassOrInterfaceReference $variable */
        $type = $classRef->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassOrInterfaceReference', $type);
        $this->assertSame('Bar', $type->getImage());

        /** @var ASTVariableDeclarator $variable */
        $variable = $classRef->getChild(1);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariableDeclarator', $variable);

        /** @var ASTValue $defaultValue */
        $defaultValue = $variable->getValue();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTValue', $defaultValue);
        $this->assertTrue($defaultValue->isValueAvailable());

        /** @var ASTAllocationExpression $expression */
        $expression = $defaultValue->getValue();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTAllocationExpression', $expression);
        $this->assertSame('Bar', $expression->getChild(0)->getImage());

        $str = $parameters[1];

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFormalParameter', $str);
        $this->assertTrue($str->isPromoted());
        $this->assertTrue($str->isProtected());

        /** @var ASTScalarType $variable */
        $type = $str->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $type);
        $this->assertSame('string', $type->getImage());

        /** @var ASTVariableDeclarator $variable */
        $variable = $str->getChild(1);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariableDeclarator', $variable);

        /** @var ASTValue $defaultValue */
        $defaultValue = $variable->getValue();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTValue', $defaultValue);
        $this->assertTrue($defaultValue->isValueAvailable());

        /** @var ASTAllocationExpression $expression */
        $expression = $defaultValue->getValue();

        $this->assertSame('abc', $expression);

        $classRef = $parameters[2];

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFormalParameter', $classRef);
        $this->assertFalse($classRef->isPromoted());
        $this->assertFalse($classRef->isPublic());

        /** @var ASTVariableDeclarator $variable */
        $variable = $classRef->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariableDeclarator', $variable);

        /** @var ASTValue $defaultValue */
        $defaultValue = $variable->getValue();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTValue', $defaultValue);
        $this->assertTrue($defaultValue->isValueAvailable());

        /** @var ASTAllocationExpression $expression */
        $expression = $defaultValue->getValue();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTAllocationExpression', $expression);
        $this->assertSame('Biz', $expression->getChild(0)->getImage());
    }
}
