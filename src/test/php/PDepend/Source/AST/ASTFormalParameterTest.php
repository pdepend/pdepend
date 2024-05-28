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
 */

namespace PDepend\Source\AST;

use OutOfBoundsException;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTFormalParameter} class.
 *
 * @covers \PDepend\Source\AST\ASTFormalParameter
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTFormalParameterTest extends ASTNodeTestCase
{
    /**
     * testHasTypeReturnsFalseByDefault
     */
    public function testHasTypeReturnsFalseByDefault(): void
    {
        $parameter = new ASTFormalParameter();
        $parameter->addChild(new ASTVariableDeclarator());

        static::assertFalse($parameter->hasType());
    }

    /**
     * testHasTypeReturnsTrueWhenSecondChildNodeExists
     */
    public function testHasTypeReturnsTrueWhenSecondChildNodeExists(): void
    {
        $parameter = new ASTFormalParameter();
        $parameter->addChild(new ASTScalarType('int'));
        $parameter->addChild(new ASTVariableDeclarator());

        static::assertTrue($parameter->hasType());
    }

    /**
     * testGetTypeThrowsAnExceptionByDefault
     */
    public function testGetTypeThrowsAnExceptionByDefault(): void
    {
        $parameter = new ASTFormalParameter();
        $parameter->addChild(new ASTVariableDeclarator());

        $this->expectException(OutOfBoundsException::class);

        $parameter->getType();
    }

    /**
     * testGetTypeReturnsAssociatedTypeInstance
     */
    public function testGetTypeReturnsAssociatedTypeInstance(): void
    {
        $parameter = new ASTFormalParameter();
        $parameter->addChild($scalar = new ASTScalarType('float'));
        $parameter->addChild(new ASTVariableDeclarator());

        static::assertSame($scalar, $parameter->getType());
    }

    /**
     * testIsVariableArgListReturnsFalseByDefault
     */
    public function testIsVariableArgListReturnsFalseByDefault(): void
    {
        $parameter = $this->getFirstFormalParameterInFunction();
        static::assertFalse($parameter->isVariableArgList());
    }

    /**
     * testIsVariableArgListReturnsTrue
     */
    public function testIsVariableArgListReturnsTrue(): void
    {
        $parameter = $this->getFirstFormalParameterInFunction();
        static::assertTrue($parameter->isVariableArgList());
    }

    /**
     * testIsVariableArgListWithArrayTypeHint
     */
    public function testIsVariableArgListWithArrayTypeHint(): void
    {
        $parameter = $this->getFirstFormalParameterInFunction();
        static::assertTrue($parameter->isVariableArgList());
    }

    /**
     * testIsVariableArgListWithClassTypeHint
     */
    public function testIsVariableArgListWithClassTypeHint(): void
    {
        $parameter = $this->getFirstFormalParameterInFunction();
        static::assertTrue($parameter->isVariableArgList());
    }

    /**
     * testIsVariableArgListPassedByReference
     */
    public function testIsVariableArgListPassedByReference(): void
    {
        $parameter = $this->getFirstFormalParameterInFunction();
        static::assertTrue($parameter->isVariableArgList());
    }

    /**
     * testIsPassedByReferenceReturnsFalseByDefault
     */
    public function testIsPassedByReferenceReturnsFalseByDefault(): void
    {
        $param = new ASTFormalParameter();
        static::assertFalse($param->isPassedByReference());
    }

    /**
     * testIsPassedByReferenceCanBeSetToTrue
     */
    public function testIsPassedByReferenceCanBeSetToTrue(): void
    {
        $param = new ASTFormalParameter();
        $param->setPassedByReference();

        static::assertTrue($param->isPassedByReference());
    }

    /**
     * testSimpleParameterIsFlaggedAsPassedByReference
     */
    public function testSimpleParameterIsFlaggedAsPassedByReference(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        static::assertTrue($param->isPassedByReference());
    }

    /**
     * testParameterWithTypeHintIsFlaggedAsPassedByReference
     */
    public function testParameterWithTypeHintIsFlaggedAsPassedByReference(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        static::assertTrue($param->isPassedByReference());
    }

    /**
     * testParameterWithDefaultValueIsFlaggedAsPassedByReference
     */
    public function testParameterWithDefaultValueIsFlaggedAsPassedByReference(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        static::assertTrue($param->isPassedByReference());
    }

    /**
     * testFormalParameterWithArrayTypeHint
     *
     * @since 1.0.0
     */
    public function testFormalParameterWithArrayTypeHint(): void
    {
        static::assertInstanceOf(
            ASTTypeArray::class,
            $this->getFirstFormalParameterInFunction()->getChild(0)
        );
    }

    // public function

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames(): void
    {
        $param = $this->createNodeInstance();
        static::assertEquals(
            [
                'modifiers',
                'comment',
                'metadata',
                'nodes',
            ],
            $param->__sleep()
        );
    }

    /**
     * testFormalParameterHasExpectedStartLine
     */
    public function testFormalParameterHasExpectedStartLine(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        static::assertEquals(3, $param->getStartLine());
    }

    /**
     * testFormalParameterHasExpectedStartColumn
     */
    public function testFormalParameterHasExpectedStartColumn(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        static::assertEquals(5, $param->getStartColumn());
    }

    /**
     * testFormalParameterHasExpectedEndLine
     */
    public function testFormalParameterHasExpectedEndLine(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        static::assertEquals(6, $param->getEndLine());
    }

    /**
     * testFormalParameterHasExpectedEndColumn
     */
    public function testFormalParameterHasExpectedEndColumn(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        static::assertEquals(20, $param->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstFormalParameterInFunction(): ASTFormalParameter
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTFormalParameter::class
        );
    }
}
