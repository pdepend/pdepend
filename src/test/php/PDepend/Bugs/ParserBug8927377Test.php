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
 * @link       https://www.pivotaltracker.com/story/show/8927377
 */

namespace PDepend\Bugs;

use PDepend\Source\AST\ASTPropertyPostfix;

/**
 * Test case for bug #8927377.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       https://www.pivotaltracker.com/story/show/8927377
 *
 * @ticket 8927377
 * @covers \stdClass
 * @group regressiontest
 */
class ParserBug8927377Test extends AbstractRegressionTest
{
    /**
     * testPropertyPostfixHasExpectedStartLine
     * 
     * @return void
     */
    public function testPropertyPostfixHasExpectedStartLine()
    {
        $postfix = $this->getFirstPropertyPostfixInClass();
        $this->assertEquals(6, $postfix->getStartLine());
    }

    /**
     * testPropertyPostfixHasExpectedEndLine
     *
     * @return void
     */
    public function testPropertyPostfixHasExpectedEndLine()
    {
        $postfix = $this->getFirstPropertyPostfixInClass();
        $this->assertEquals(6, $postfix->getEndLine());
    }

    /**
     * testPropertyPostfixHasExpectedStartColumn
     *
     * @return void
     */
    public function testPropertyPostfixHasExpectedStartColumn()
    {
        $postfix = $this->getFirstPropertyPostfixInClass();
        $this->assertEquals(16, $postfix->getStartColumn());
    }

    /**
     * testPropertyPostfixHasExpectedEndColumn
     *
     * @return void
     */
    public function testPropertyPostfixHasExpectedEndColumn()
    {
        $postfix = $this->getFirstPropertyPostfixInClass();
        $this->assertEquals(18, $postfix->getEndColumn());
    }

    /**
     * Returns the property postfix found in a class.
     * 
     * @return \PDepend\Source\AST\ASTPropertyPostfix
     */
    protected function getFirstPropertyPostfixInClass()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTPropertyPostfix');
    }
}
