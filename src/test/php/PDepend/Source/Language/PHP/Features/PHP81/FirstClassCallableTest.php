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

/**
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion81
 * @group unittest
 * @group php8.1
 */
class FirstClassCallableTest extends AbstractTest
{
    /**
     * @return void
     */
    public function testFirstClassCallable()
    {
        $method   = $this->getFirstMethodForTestCase();
        $children = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTFunctionPostfix')->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTIdentifier', $children[0]);
        $this->assertSame('trim', $children[0]->getImage());

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariadicPlaceholder', $children[1]);
        $this->assertSame('...', $children[1]->getImage());
    }

    /**
     * @return void
     */
    public function testFirstClassCallableWithComments()
    {
        $method   = $this->getFirstMethodForTestCase();
        $children = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTFunctionPostfix')->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTIdentifier', $children[0]);
        $this->assertSame('trim', $children[0]->getImage());

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariadicPlaceholder', $children[1]);
        $this->assertSame('...', $children[1]->getImage());
    }

    /**
     * @return void
     */
    public function testFirstClassCallableObjectMethod()
    {
        $method   = $this->getFirstMethodForTestCase();
        $children = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTMethodPostfix')->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariadicPlaceholder', $children[1]);
        $this->assertSame('...', $children[1]->getImage());
    }

    /**
     * @return void
     */
    public function testFirstClassCallableDynamicMethod()
    {
        $method   = $this->getFirstMethodForTestCase();
        $children = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTMethodPostfix')->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariadicPlaceholder', $children[1]);
        $this->assertSame('...', $children[1]->getImage());
    }

    /**
     * @return void
     */
    public function testFirstClassCallableStaticMethod()
    {
        $method   = $this->getFirstMethodForTestCase();
        $children = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTMethodPostfix')->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariadicPlaceholder', $children[1]);
        $this->assertSame('...', $children[1]->getImage());
    }

    /**
     * @return void
     */
    public function testFirstClassCallableDynamicStaticMethod()
    {
        $method   = $this->getFirstMethodForTestCase();
        $children = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTMethodPostfix')->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariadicPlaceholder', $children[1]);
        $this->assertSame('...', $children[1]->getImage());
    }

    /**
     * @return void
     */
    public function testFirstClassCallableTraditionalCallableFunction()
    {
        $method   = $this->getFirstMethodForTestCase();
        $children = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $children[0]);
        $this->assertSame("'strlen'", $children[0]->getImage());

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariadicPlaceholder', $children[1]);
        $this->assertSame('...', $children[1]->getImage());
    }

    /**
     * @return void
     */
    public function testFirstClassCallableObjectCallable()
    {
        $method   = $this->getFirstMethodForTestCase();
        $children = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTArray', $children[0]);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariadicPlaceholder', $children[1]);
        $this->assertSame('...', $children[1]->getImage());
    }


    /**
     * @return void
     */
    public function testFirstClassCallableStaticClassMethodCallable()
    {
        $method   = $this->getFirstMethodForTestCase();
        $children = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTExpression')->getChildren();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTArray', $children[0]);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariadicPlaceholder', $children[1]);
        $this->assertSame('...', $children[1]->getImage());
    }
}
