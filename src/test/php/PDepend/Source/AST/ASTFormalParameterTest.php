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

/**
 * Test case for the {@link \PDepend\Source\AST\ASTFormalParameter} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTFormalParameter
 * @group unittest
 */
class ASTFormalParameterTest extends ASTNodeTestCase
{
    /**
     * testHasTypeReturnsFalseByDefault
     *
     * @return void
     */
    public function testHasTypeReturnsFalseByDefault(): void
    {
        $parameter = new ASTFormalParameter();
        $parameter->addChild(new ASTVariableDeclarator());

        $this->assertFalse($parameter->hasType());
    }

    /**
     * testHasTypeReturnsTrueWhenSecondChildNodeExists
     *
     * @return void
     */
    public function testHasTypeReturnsTrueWhenSecondChildNodeExists(): void
    {
        $parameter = new ASTFormalParameter();
        $parameter->addChild(new ASTScalarType('int'));
        $parameter->addChild(new ASTVariableDeclarator());

        $this->assertTrue($parameter->hasType());
    }

    /**
     * testGetTypeThrowsAnExceptionByDefault
     *
     * @return void
     */
    public function testGetTypeThrowsAnExceptionByDefault(): void
    {
        $parameter = new ASTFormalParameter();
        $parameter->addChild(new ASTVariableDeclarator());

        $this->expectException('\\OutOfBoundsException');

        $parameter->getType();
    }

    /**
     * testGetTypeReturnsAssociatedTypeInstance
     *
     * @return void
     */
    public function testGetTypeReturnsAssociatedTypeInstance(): void
    {
        $parameter = new ASTFormalParameter();
        $parameter->addChild($scalar = new ASTScalarType('float'));
        $parameter->addChild(new ASTVariableDeclarator());

        $this->assertSame($scalar, $parameter->getType());
    }

    /**
     * testIsVariableArgListReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsVariableArgListReturnsFalseByDefault(): void
    {
        $parameter = $this->getFirstFormalParameterInFunction();
        $this->assertFalse($parameter->isVariableArgList());
    }

    /**
     * testIsVariableArgListReturnsTrue
     *
     * @return void
     */
    public function testIsVariableArgListReturnsTrue(): void
    {
        $parameter = $this->getFirstFormalParameterInFunction();
        $this->assertTrue($parameter->isVariableArgList());
    }

    /**
     * testIsVariableArgListWithArrayTypeHint
     *
     * @return void
     */
    public function testIsVariableArgListWithArrayTypeHint(): void
    {
        $parameter = $this->getFirstFormalParameterInFunction();
        $this->assertTrue($parameter->isVariableArgList());
    }

    /**
     * testIsVariableArgListWithClassTypeHint
     *
     * @return void
     */
    public function testIsVariableArgListWithClassTypeHint(): void
    {
        $parameter = $this->getFirstFormalParameterInFunction();
        $this->assertTrue($parameter->isVariableArgList());
    }

    /**
     * testIsVariableArgListPassedByReference
     *
     * @return void
     */
    public function testIsVariableArgListPassedByReference(): void
    {
        $parameter = $this->getFirstFormalParameterInFunction();
        $this->assertTrue($parameter->isVariableArgList());
    }

    /**
     * testIsPassedByReferenceReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsPassedByReferenceReturnsFalseByDefault(): void
    {
        $param = new ASTFormalParameter();
        $this->assertFalse($param->isPassedByReference());
    }

    /**
     * testIsPassedByReferenceCanBeSetToTrue
     *
     * @return void
     */
    public function testIsPassedByReferenceCanBeSetToTrue(): void
    {
        $param = new \PDepend\Source\AST\ASTFormalParameter();
        $param->setPassedByReference();

        $this->assertTrue($param->isPassedByReference());
    }

    /**
     * testSimpleParameterIsFlaggedAsPassedByReference
     *
     * @return void
     */
    public function testSimpleParameterIsFlaggedAsPassedByReference(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        $this->assertTrue($param->isPassedByReference());
    }

    /**
     * testParameterWithTypeHintIsFlaggedAsPassedByReference
     *
     * @return void
     */
    public function testParameterWithTypeHintIsFlaggedAsPassedByReference(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        $this->assertTrue($param->isPassedByReference());
    }

    /**
     * testParameterWithDefaultValueIsFlaggedAsPassedByReference
     *
     * @return void
     */
    public function testParameterWithDefaultValueIsFlaggedAsPassedByReference(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        $this->assertTrue($param->isPassedByReference());
    }
    
    /**
     * testFormalParameterWithArrayTypeHint
     *
     * @return void
     * @since 1.0.0
     */
    public function testFormalParameterWithArrayTypeHint(): void
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTypeArray',
            $this->getFirstFormalParameterInFunction()->getChild(0)
        );
    }

    //public function

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames(): void
    {
        $param = $this->createNodeInstance();
        $this->assertEquals(
            [
                'modifiers',
                'comment',
                'metadata',
                'nodes'
            ],
            $param->__sleep()
        );
    }

    /**
     * testFormalParameterHasExpectedStartLine
     *
     * @return void
     */
    public function testFormalParameterHasExpectedStartLine(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        $this->assertEquals(3, $param->getStartLine());
    }

    /**
     * testFormalParameterHasExpectedStartColumn
     *
     * @return void
     */
    public function testFormalParameterHasExpectedStartColumn(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        $this->assertEquals(5, $param->getStartColumn());
    }

    /**
     * testFormalParameterHasExpectedEndLine
     *
     * @return void
     */
    public function testFormalParameterHasExpectedEndLine(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        $this->assertEquals(6, $param->getEndLine());
    }

    /**
     * testFormalParameterHasExpectedEndColumn
     *
     * @return void
     */
    public function testFormalParameterHasExpectedEndColumn(): void
    {
        $param = $this->getFirstFormalParameterInFunction();
        $this->assertEquals(20, $param->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTFormalParameter
     */
    private function getFirstFormalParameterInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTFormalParameter'
        );
    }
}
