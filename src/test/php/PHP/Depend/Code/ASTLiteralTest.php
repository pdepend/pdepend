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

require_once 'PHP/Depend/Code/ASTLiteral.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTLiteral} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Code_ASTLiteralTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testLiteralWithBooleanTrueExpression
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTLiteral
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testLiteralWithBooleanTrueExpression()
    {
        $literal = $this->_getFirstLiteralInFunction();
        self::assertEquals('True', $literal->getImage());
    }

    /**
     * testLiteralWithBooleanFalseExpression
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTLiteral
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testLiteralWithBooleanFalseExpression()
    {
        $literal = $this->_getFirstLiteralInFunction();
        self::assertEquals('False', $literal->getImage());
    }

    /**
     * testLiteralWithIntegerExpression
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTLiteral
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testLiteralWithIntegerExpression()
    {
        $literal = $this->_getFirstLiteralInFunction();
        self::assertEquals('42', $literal->getImage());
    }

    /**
     * testLiteralWithSignedIntegerExpression
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTLiteral
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testLiteralWithSignedIntegerExpression()
    {
        $literal = $this->_getFirstLiteralInFunction();
        self::assertEquals('42', $literal->getImage());
    }

    /**
     * testLiteralWithFloatExpression
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTLiteral
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testLiteralWithFloatExpression()
    {
        $literal = $this->_getFirstLiteralInFunction();
        self::assertEquals('42.23', $literal->getImage());
    }

    /**
     * testLiteralWithSignedFloatExpression
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTLiteral
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testLiteralWithSignedFloatExpression()
    {
        $literal = $this->_getFirstLiteralInFunction();
        self::assertEquals('42.23', $literal->getImage());
    }

    /**
     * testLiteralWithNullExpression
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTLiteral
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testLiteralWithNullExpression()
    {
        $literal = $this->_getFirstLiteralInFunction();
        self::assertEquals('NULL', $literal->getImage());
    }

    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTLiteral
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitLiteral'));

        $literal = new PHP_Depend_Code_ASTLiteral();
        $literal->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTLiteral
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitLiteral'))
            ->will($this->returnValue(42));

        $literal = new PHP_Depend_Code_ASTLiteral();
        self::assertEquals(42, $literal->accept($visitor));
    }

    /**
     * Tests that an invalid literal results in the expected exception.
     * 
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTLiteral
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     * @expectedException PHP_Depend_Parser_TokenStreamEndException
     */
    public function testUnclosedDoubleQuoteStringResultsInExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Creates a literal node.
     *
     * @return PHP_Depend_Code_ASTLiteral
     */
    protected function createNodeInstance()
    {
        return new PHP_Depend_Code_ASTLiteral("'" . __METHOD__ . "'");
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTLiteral
     */
    private function _getFirstLiteralInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            self::getCallingTestMethod(),
            PHP_Depend_Code_ASTLiteral::CLAZZ
        );
    }
}