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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTClosure} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_ASTNode
 * @covers PHP_Depend_Code_ASTClosure
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTClosureTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testReturnsByReferenceReturnsFalseByDefault
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsFalseByDefault()
    {
        $closure = $this->_getFirstClosureInFunction();
        self::assertFalse($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsTrueForClosure
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsTrueForClosure()
    {
        $closure = $this->_getFirstClosureInFunction();
        self::assertTrue($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsTrueForAssignedClosure
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsTrueForAssignedClosure()
    {
        $closure = $this->_getFirstClosureInFunction();
        self::assertTrue($closure->returnsByReference());
    }

    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitClosure'));

        $node = new PHP_Depend_Code_ASTClosure();
        $node->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitClosure'))
            ->will($this->returnValue(42));

        $node = new PHP_Depend_Code_ASTClosure();
        self::assertEquals(42, $node->accept($visitor));
    }

    /**
     * Tests the start line value.
     *
     * @return void
     */
    public function testClosureHasExpectedStartLine()
    {
        $label = $this->_getFirstClosureInFunction();
        $this->assertEquals(4, $label->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     */
    public function testClosureHasExpectedStartColumn()
    {
        $label = $this->_getFirstClosureInFunction();
        $this->assertEquals(12, $label->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     */
    public function testClosureHasExpectedEndLine()
    {
        $label = $this->_getFirstClosureInFunction();
        $this->assertEquals(6, $label->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     */
    public function testClosureHasExpectedEndColumn()
    {
        $label = $this->_getFirstClosureInFunction();
        $this->assertEquals(5, $label->getEndColumn());
    }

    /**
     * testClosureContainsExpectedNumberChildNodes
     *
     * @return void
     */
    public function testClosureContainsExpectedNumberChildNodes()
    {
        $closure = $this->_getFirstClosureInFunction();
        $this->assertEquals(2, count($closure->getChildren()));
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTClosure
     */
    private function _getFirstClosureInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            self::getCallingTestMethod(),
            PHP_Depend_Code_ASTClosure::CLAZZ
        );
    }
}