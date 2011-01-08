<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 * @since     0.10.0
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTNode} class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 * @since     0.10.0
 *
 * @covers PHP_Depend_Code_ASTNode
 */
class PHP_Depend_Code_CommonASTNodeTest extends PHP_Depend_AbstractTest
{
    /**
     * testGetCommentReturnsNullByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetCommentReturnsNullByDefault()
    {
        $node = $this->getNodeMock();
        self::assertNull($node->getComment());
    }

    /**
     * testGetCommentReturnsInjectedCommentValue
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetCommentReturnsInjectedCommentValue()
    {
        $node = $this->getNodeMock();
        $node->setComment('/** Manuel */');
        
        self::assertEquals('/** Manuel */', $node->getComment());
    }

    /**
     * testGetStartColumnReturnsZeroByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetStartColumnReturnsZeroByDefault()
    {
        $node = $this->getNodeMock();
        self::assertSame(0, $node->getStartColumn());
    }

    /**
     * testGetStartColumnReturnsInjectedEndLineValue
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetStartColumnReturnsInjectedEndLineValue()
    {
        $node = $this->getNodeMock();
        $node->setStartColumn(42);

        self::assertEquals(42, $node->getStartColumn());
    }

    /**
     * testGetStartLineReturnsZeroByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetStartLineReturnsZeroByDefault()
    {
        $node = $this->getNodeMock();
        self::assertSame(0, $node->getStartLine());
    }

    /**
     * testGetStartLineReturnsInjectedEndLineValue
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetStartLineReturnsInjectedEndLineValue()
    {
        $node = $this->getNodeMock();
        $node->setStartLine(42);

        self::assertEquals(42, $node->getStartLine());
    }

    /**
     * testGetEndColumnReturnsZeroByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetEndColumnReturnsZeroByDefault()
    {
        $node = $this->getNodeMock();
        self::assertSame(0, $node->getEndColumn());
    }

    /**
     * testGetEndColumnReturnsInjectedEndLineValue
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetEndColumnReturnsInjectedEndLineValue()
    {
        $node = $this->getNodeMock();
        $node->setEndColumn(42);

        self::assertEquals(42, $node->getEndColumn());
    }

    /**
     * testGetEndLineReturnsZeroByDefault
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetEndLineReturnsZeroByDefault()
    {
        $node = $this->getNodeMock();
        self::assertSame(0, $node->getEndLine());
    }

    /**
     * testGetEndLineReturnsInjectedEndLineValue
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testGetEndLineReturnsInjectedEndLineValue()
    {
        $node = $this->getNodeMock();
        $node->setEndLine(42);

        self::assertEquals(42, $node->getEndLine());
    }

    /**
     * Returns a mocked ast node instance.
     *
     * @return PHP_Depend_Code_ASTNode
     */
    protected function getNodeMock()
    {
        return $this->getMockForAbstractClass(
            PHP_Depend_Code_ASTNode::CLAZZ,
            array(__CLASS__)
        );
    }
}