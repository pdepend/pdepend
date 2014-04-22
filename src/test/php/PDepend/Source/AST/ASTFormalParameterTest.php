<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTFormalParameter} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTNode
 * @covers \PDepend\Source\AST\ASTFormalParameter
 * @group unittest
 */
class ASTFormalParameterTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testIsPassedByReferenceReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsPassedByReferenceReturnsFalseByDefault()
    {
        $param = new \PDepend\Source\AST\ASTFormalParameter();
        $this->assertFalse($param->isPassedByReference());
    }

    /**
     * testIsPassedByReferenceCanBeSetToTrue
     *
     * @return void
     */
    public function testIsPassedByReferenceCanBeSetToTrue()
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
    public function testSimpleParameterIsFlaggedAsPassedByReference()
    {
        $param = $this->_getFirstFormalParameterInFunction();
        $this->assertTrue($param->isPassedByReference());
    }

    /**
     * testParameterWithTypeHintIsFlaggedAsPassedByReference
     *
     * @return void
     */
    public function testParameterWithTypeHintIsFlaggedAsPassedByReference()
    {
        $param = $this->_getFirstFormalParameterInFunction();
        $this->assertTrue($param->isPassedByReference());
    }

    /**
     * testParameterWithDefaultValueIsFlaggedAsPassedByReference
     *
     * @return void
     */
    public function testParameterWithDefaultValueIsFlaggedAsPassedByReference()
    {
        $param = $this->_getFirstFormalParameterInFunction();
        $this->assertTrue($param->isPassedByReference());
    }
    
    /**
     * testFormalParameterWithArrayTypeHint
     * 
     * @return void
     * @since 1.0.0
     */
    public function testFormalParameterWithArrayTypeHint()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTypeArray',
            $this->_getFirstFormalParameterInFunction()->getChild(0)
        );
    }

    //public function

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames()
    {
        $param = $this->createNodeInstance();
        $this->assertEquals(
            array(
                'comment',
                'metadata',
                'nodes'
            ),
            $param->__sleep()
        );
    }

    /**
     * testFormalParameterHasExpectedStartLine
     *
     * @return void
     */
    public function testFormalParameterHasExpectedStartLine()
    {
        $param = $this->_getFirstFormalParameterInFunction();
        $this->assertEquals(3, $param->getStartLine());
    }

    /**
     * testFormalParameterHasExpectedStartColumn
     *
     * @return void
     */
    public function testFormalParameterHasExpectedStartColumn()
    {
        $param = $this->_getFirstFormalParameterInFunction();
        $this->assertEquals(5, $param->getStartColumn());
    }

    /**
     * testFormalParameterHasExpectedEndLine
     *
     * @return void
     */
    public function testFormalParameterHasExpectedEndLine()
    {
        $param = $this->_getFirstFormalParameterInFunction();
        $this->assertEquals(6, $param->getEndLine());
    }

    /**
     * testFormalParameterHasExpectedEndColumn
     *
     * @return void
     */
    public function testFormalParameterHasExpectedEndColumn()
    {
        $param = $this->_getFirstFormalParameterInFunction();
        $this->assertEquals(20, $param->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTFormalParameter
     */
    private function _getFirstFormalParameterInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(), 
            'PDepend\\Source\\AST\\ASTFormalParameter'
        );
    }
}
