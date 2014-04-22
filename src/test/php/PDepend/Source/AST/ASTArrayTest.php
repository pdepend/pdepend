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
 * @since     1.0.0
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTArray} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since     1.0.0
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTArray
 * @group unittest
 */
class ASTArrayTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testArrayGraphForEmptyArrayDefinition
     *
     * Source:
     * <code>
     * array()
     * </code>
     *
     * AST:
     * <code>
     * - ASTArray
     * </code>
     *
     * @return void
     */
    public function testArrayGraphForEmptyArrayDefinition()
    {
        $this->assertGraph(
            $this->_getFirstArrayInFunction(),
            array()
        );
    }

    /**
     * testArrayGraphForEmptyShortArrayDefinition
     *
     * Source:
     * <code>
     * []
     * </code>
     *
     * AST:
     * <code>
     * - ASTArray
     * </code>
     *
     * @return void
     */
    public function testArrayGraphForEmptyShortArrayDefinition()
    {
        $this->assertGraph(
            $this->_getFirstArrayInFunction(),
            array()
        );
    }

    /**
     * Tests the start line value of an array instance.
     *
     * @return void
     */
    public function testArrayHasExpectedStartLine()
    {
        $array = $this->_getFirstArrayInFunction();
        $this->assertEquals(4, $array->getStartLine());
    }

    /**
     * Tests the start column value of an array instance.
     *
     * @return void
     */
    public function testArrayHasExpectedStartColumn()
    {
        $array = $this->_getFirstArrayInFunction();
        $this->assertEquals(12, $array->getStartColumn());
    }

    /**
     * Tests the end line value of an array instance.
     *
     * @return void
     */
    public function testArrayHasExpectedEndLine()
    {
        $array = $this->_getFirstArrayInFunction();
        $this->assertEquals(13, $array->getEndLine());
    }

    /**
     * Tests the end column value of an array instance.
     *
     * @return void
     */
    public function testArrayHasExpectedEndColumn()
    {
        $array = $this->_getFirstArrayInFunction();
        $this->assertEquals(5, $array->getEndColumn());
    }

    /**
     * Returns an array instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTArray
     */
    private function _getFirstArrayInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTArray'
        );
    }
}
