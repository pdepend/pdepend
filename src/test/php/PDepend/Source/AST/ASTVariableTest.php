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
 * Test case for the {@link \PDepend\Source\AST\ASTVariable} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTVariable
 * @group unittest
 */
class ASTVariableTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testIsThisReturnsTrueForThisImageName
     *
     * @return void
     */
    public function testIsThisReturnsTrueForThisImageName()
    {
        $variable = new \PDepend\Source\AST\ASTVariable('$this');
        $this->assertTrue($variable->isThis());
    }

    /**
     * testIsThisReturnsFalseForThisImageButDifferentCase
     *
     * @return void
     */
    public function testIsThisReturnsFalseForThisImageButDifferentCase()
    {
        $variable = new \PDepend\Source\AST\ASTVariable('$This');
        $this->assertFalse($variable->isThis());
    }

    /**
     * testIsThisReturnsFalseForDifferentImage
     *
     * @return void
     */
    public function testIsThisReturnsFalseForDifferentImage()
    {
        $variable = new \PDepend\Source\AST\ASTVariable('$foo');
        $this->assertFalse($variable->isThis());
    }

    /**
     * testAcceptInvokesAcceptOnChildNode
     *
     * @return void
     */
    public function testAcceptInvokesAcceptOnChildNode()
    {
        // Not valid for leaf nodes.
    }

    /**
     * testVariable
     *
     * @return \PDepend\Source\AST\ASTVariable
     * @since 1.0.2
     */
    public function testVariable()
    {
        $variable = $this->_getFirstVariableInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $variable);

        return $variable;
    }

    /**
     * testVariableHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTVariable $variable
     *
     * @return void
     * @depends testVariable
     */
    public function testVariableHasExpectedStartLine($variable)
    {
        $this->assertEquals(6, $variable->getStartLine());
    }

    /**
     * testVariableHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTVariable $variable
     *
     * @return void
     * @depends testVariable
     */
    public function testVariableHasExpectedStartColumn($variable)
    {
        $this->assertEquals(9, $variable->getStartColumn());
    }

    /**
     * testVariableHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTVariable $variable
     *
     * @return void
     * @depends testVariable
     */
    public function testVariableHasExpectedEndLine($variable)
    {
        $this->assertEquals(6, $variable->getEndLine());
    }

    /**
     * testVariableHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTVariable $variable
     *
     * @return void
     * @depends testVariable
     */
    public function testVariableHasExpectedEndColumn($variable)
    {
        $this->assertEquals(10, $variable->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTVariable
     */
    private function _getFirstVariableInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(), 
            'PDepend\\Source\\AST\\ASTVariable'
        );
    }
}
