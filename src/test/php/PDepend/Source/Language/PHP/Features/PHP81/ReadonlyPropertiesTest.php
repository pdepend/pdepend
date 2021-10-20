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

use PDepend\AbstractTest;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\State;

/**
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion80
 * @group unittest
 * @group php8
 */
class ReadonlyPropertiesTest extends AbstractTest
{
    /**
     * @return void
     */
    public function testReadonlyProperty()
    {
        $class = $this->getFirstClassForTestCase();
        $property = $class->getChild(0);

        $this->assertSame('string', $property->getChild(0)->getImage());
        $this->assertSame('$bar', $property->getChild(1)->getImage());

        $expectedModifiers = ~State::IS_PUBLIC & ~State::IS_READONLY;
        $this->assertSame(0, ($expectedModifiers & $property->getModifiers()));
    }

    /**
     * @return void
     */
    public function testReadonlyPropertyInConstructor()
    {
        $class = $this->getFirstClassForTestCase();
        $constructor = $class->getMethods()->offsetGet(0);
        $this->assertSame('__construct', $constructor->getName());

        $parameters = $constructor->getParameters();
        $parameter = $parameters[0];

        $this->assertSame('string', $parameter->getFormalParameter()->getChild(0)->getImage());
        $this->assertSame('$bar', $parameter->getFormalParameter()->getChild(1)->getImage());

        $expectedModifiers = ~State::IS_PUBLIC & ~State::IS_READONLY;
        $this->assertSame(0, ($expectedModifiers & $parameter->getFormalParameter()->getModifiers()));
    }

    /**
     * @return void
     */
    public function testReadonlyNameUsedElsewhere()
    {
        $class = $this->getFirstClassForTestCase();

        $constant = $class->getChild(0);
        $this->assertSame('readonly', $constant->getChild(0)->getImage());

        $propertyPostfix = $class->getChild(1);
        $this->assertSame('$readonly', $propertyPostfix->getChild(1)->getImage());

        $expectedModifiers = ~State::IS_PUBLIC & ~State::IS_READONLY;
        $this->assertSame(0, ($expectedModifiers & $propertyPostfix->getModifiers()));

        /** @var ASTMethod $constructor */
        $constructor = $class->getMethods()->offsetGet(0);
        $this->assertSame('__construct', $constructor->getName());
        $constructorNodes = $constructor->getChildren();
        $assignment = $constructorNodes[1]->getChild(0)->getChild(0);

        $propertyPostfix = $assignment->getChild(0)->getChild(1);
        self::assertInstanceOf('PDepend\\Source\\AST\\ASTPropertyPostfix', $propertyPostfix);
        $this->assertSame('readonly', $propertyPostfix->getImage());

        $methodPostfix = $assignment->getChild(1)->getChild(1);
        self::assertInstanceOf('PDepend\\Source\\AST\\ASTMethodPostfix', $methodPostfix);
        $this->assertSame('readonly', $methodPostfix->getImage());

        $method = $class->getMethods()->offsetGet(1);
        $this->assertSame('readonly', $method->getName());

        $methodNodes = $method->getChildren();
        $constantCall = $methodNodes[1]->getChild(0)->getChild(0);
        $this->assertSame('readonly', $constantCall->getChild(1)->getImage());
    }
}
