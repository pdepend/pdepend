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

namespace PDepend\Source\Language\PHP\Features\PHP82;

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTParameter;

/**
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion82
 * @group unittest
 * @group php8.2
 */
class AllowNullAndFalseAsStandAloneTypesTest extends PHPParserVersion82Test
{
    /**
     * @return void
     */
    public function testTypedProperties()
    {
        /** @var ASTClass $class */
        $class = $this->getFirstClassForTestCase();
        $children = $class->getChildren();

        /** @var array[] $declarations */
        $declarations = array_map(function (ASTFieldDeclaration $child) {
            $childChildren = $child->getChildren();

            return array(
                $child->hasType() ? $child->getType() : null,
                $childChildren[1],
            );
        }, $children);

        foreach (array(
            array('null', '$nullish'),
            array('false', '$falsy'),
        ) as $index => $expected) {
            list($expectedType, $expectedVariable) = $expected;
            $expectedTypeClass = isset($expected[2]) ? $expected[2] : 'PDepend\\Source\\AST\\ASTScalarType';
            list($type, $variable) = $declarations[$index];

            $this->assertInstanceOf(
                $expectedTypeClass,
                $type,
                "Wrong type for $expectedType $expectedVariable"
            );
            $this->assertSame(ltrim($expectedType, '?'), $type->getImage());
            $this->assertInstanceOf(
                'PDepend\\Source\\AST\\ASTVariableDeclarator',
                $variable,
                "Wrong variable for $expectedType $expectedVariable"
            );
            $this->assertSame($expectedVariable, $variable->getImage());
        }
    }

    /**
     * @return void
     */
    public function testReturnTypes()
    {
        $class = $this->getFirstClassForTestCase();
        /** @var ASTMethod[] $methods */
        $methods = $class->getMethods();
        $nullish = $methods[0]->getReturnType();
        $falsy = $methods[1]->getReturnType();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $nullish);
        $this->assertSame('null', $nullish->getImage());

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $falsy);
        $this->assertSame('false', $falsy->getImage());
    }

    /**
     * @return void
     */
    public function testParameters()
    {
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTParameter[] $methods */
        $parameters = $method->getParameters();
        $nullish = $parameters[0];
        $falsy = $parameters[1];

        $this->assertTrue($nullish->allowsNull());
        $this->assertFalse($falsy->allowsNull());

        $nullish = $nullish->getFormalParameter()->getType();
        $falsy = $falsy->getFormalParameter()->getType();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $nullish);
        $this->assertSame('null', $nullish->getImage());
        $this->assertSame(3, $nullish->getStartLine());
        $this->assertSame(29, $nullish->getStartColumn());
        $this->assertSame(3, $nullish->getEndLine());
        $this->assertSame(32, $nullish->getEndColumn());

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $falsy);
        $this->assertSame('false', $falsy->getImage());
        $this->assertSame(3, $falsy->getStartLine());
        $this->assertSame(44, $falsy->getStartColumn());
        $this->assertSame(3, $falsy->getEndLine());
        $this->assertSame(48, $falsy->getEndColumn());
    }
}
