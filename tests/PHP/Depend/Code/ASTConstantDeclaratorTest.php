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

require_once 'PHP/Depend/Code/ASTConstantDeclarator.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTConstantDeclarator} class.
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
 * @covers PHP_Depend_Builder_Default
 * @covers PHP_Depend_Code_ASTConstantDeclarator
 */
class PHP_Depend_Code_ASTConstantDeclaratorTest extends PHP_Depend_Code_ASTNodeTest
{

    /**
     * testReturnValueOfMagicSleepContainsValueProperty
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testReturnValueOfMagicSleepContainsValueProperty()
    {
        $node = new PHP_Depend_Code_ASTConstantDeclarator();
        self::assertEquals(
            array(
                'value',
                'image',
                'comment',
                'startLine',
                'startColumn',
                'endLine',
                'endColumn',
                'nodes'
            ),
            $node->__sleep()
        );
    }

    /**
     * testParserInjectsValueObjectIntoConstantDeclarator
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParserInjectsValueObjectIntoConstantDeclarator()
    {
        $declarator = $this->_getFirstConstantDeclaratorInClass(__METHOD__);
        self::assertType('PHP_Depend_Code_Value', $declarator->getValue());
    }

    /**
     * testParserInjectsExpectedScalarValueIntoConstantDeclarator
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testParserInjectsExpectedScalarValueIntoConstantDeclarator()
    {
        $declarator = $this->_getFirstConstantDeclaratorInClass(__METHOD__);
        self::assertEquals(42, $declarator->getValue()->getValue());
    }

    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitConstantDeclarator'));

        $node = new PHP_Depend_Code_ASTConstantDeclarator();
        $node->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitConstantDeclarator'))
            ->will($this->returnValue(42));

        $node = new PHP_Depend_Code_ASTConstantDeclarator();
        self::assertEquals(42, $node->accept($visitor));
    }

    /**
     * testConstantDeclaratorHasExpectedStartLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testConstantDeclaratorHasExpectedStartLine()
    {
        $declarator = $this->_getFirstConstantDeclaratorInClass(__METHOD__);
        $this->assertEquals(5, $declarator->getStartLine());
    }

    /**
     * testConstantDeclaratorHasExpectedStartColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testConstantDeclaratorHasExpectedStartColumn()
    {
        $declarator = $this->_getFirstConstantDeclaratorInClass(__METHOD__);
        $this->assertEquals(7, $declarator->getStartColumn());
    }

    /**
     * testConstantDeclaratorHasExpectedEndLine
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testConstantDeclaratorHasExpectedEndLine()
    {
        $declarator = $this->_getFirstConstantDeclaratorInClass(__METHOD__);
        $this->assertEquals(7, $declarator->getEndLine());
    }

    /**
     * testConstantDeclaratorHasExpectedEndColumn
     *
     * @return void
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testConstantDeclaratorHasExpectedEndColumn()
    {
        $declarator = $this->_getFirstConstantDeclaratorInClass(__METHOD__);
        $this->assertEquals(14, $declarator->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTConstantDeclarator
     */
    private function _getFirstConstantDeclaratorInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase, PHP_Depend_Code_ASTConstantDeclarator::CLAZZ
        );
    }
}