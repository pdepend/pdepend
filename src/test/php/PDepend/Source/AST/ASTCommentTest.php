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
 * Test case for the {@link \PDepend\Source\AST\ASTComment} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTComment
 * @group unittest
 */
class ASTCommentTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testSingleLineCommentHasExpectedStartLine
     *
     * @return void
     */
    public function testSingleLineCommentHasExpectedStartLine()
    {
        $comment = $this->_getFirstCommentInClass(__METHOD__);
        $this->assertEquals(4, $comment->getStartLine());
    }

    /**
     * testSingleLineCommentHasExpectedStartColumn
     *
     * @return void
     */
    public function testSingleLineCommentHasExpectedStartColumn()
    {
        $comment = $this->_getFirstCommentInClass(__METHOD__);
        $this->assertEquals(5, $comment->getStartColumn());
    }

    /**
     * testSingleLineCommentHasExpectedEndLine
     *
     * @return void
     */
    public function testSingleLineCommentHasExpectedEndLine()
    {
        $comment = $this->_getFirstCommentInClass(__METHOD__);
        $this->assertEquals(4, $comment->getEndLine());
    }

    /**
     * testSingleLineCommentHasExpectedEndColumn
     *
     * @return void
     */
    public function testSingleLineCommentHasExpectedEndColumn()
    {
        $comment = $this->_getFirstCommentInClass(__METHOD__);
        $this->assertEquals(48, $comment->getEndColumn());
    }

    /**
     * testMultiLineCommentHasExpectedStartLine
     *
     * @return void
     */
    public function testMultiLineCommentHasExpectedStartLine()
    {
        $comment = $this->_getFirstCommentInClass(__METHOD__);
        $this->assertEquals(4, $comment->getStartLine());
    }

    /**
     * testMultiLineCommentHasExpectedStartColumn
     *
     * @return void
     */
    public function testMultiLineCommentHasExpectedStartColumn()
    {
        $comment = $this->_getFirstCommentInClass(__METHOD__);
        $this->assertEquals(5, $comment->getStartColumn());
    }

    /**
     * testMultiLineCommentHasExpectedEndLine
     *
     * @return void
     */
    public function testMultiLineCommentHasExpectedEndLine()
    {
        $comment = $this->_getFirstCommentInClass(__METHOD__);
        $this->assertEquals(8, $comment->getEndLine());
    }

    /**
     * testMultiLineCommentHasExpectedEndColumn
     *
     * @return void
     */
    public function testMultiLineCommentHasExpectedEndColumn()
    {
        $comment = $this->_getFirstCommentInClass(__METHOD__);
        $this->assertEquals(7, $comment->getEndColumn());
    }

    /**
     * testDocCommentHasExpectedStartLine
     *
     * @return void
     */
    public function testDocCommentHasExpectedStartLine()
    {
        $comment = $this->_getFirstCommentInClass(__METHOD__);
        $this->assertEquals(4, $comment->getStartLine());
    }

    /**
     * testDocCommentHasExpectedStartColumn
     *
     * @return void
     */
    public function testDocCommentHasExpectedStartColumn()
    {
        $comment = $this->_getFirstCommentInClass(__METHOD__);
        $this->assertEquals(5, $comment->getStartColumn());
    }

    /**
     * testDocCommentHasExpectedEndLine
     *
     * @return void
     */
    public function testDocCommentHasExpectedEndLine()
    {
        $comment = $this->_getFirstCommentInClass(__METHOD__);
        $this->assertEquals(8, $comment->getEndLine());
    }

    /**
     * testDocCommentHasExpectedEndColumn
     *
     * @return void
     */
    public function testDocCommentHasExpectedEndColumn()
    {
        $comment = $this->_getFirstCommentInClass(__METHOD__);
        $this->assertEquals(7, $comment->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PDepend\Source\AST\ASTComment
     */
    private function _getFirstCommentInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase,
            'PDepend\\Source\\AST\\ASTComment'
        );
    }
}
