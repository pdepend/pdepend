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

require_once 'PHP/Depend/Code/ASTAllocationExpression.php';
require_once 'PHP/Depend/Code/ASTArguments.php';
require_once 'PHP/Depend/Code/ASTClassReference.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTAllocationExpression} class.
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
class PHP_Depend_Code_ASTAllocationExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitAllocationExpression'));

        $node = new PHP_Depend_Code_ASTAllocationExpression();
        $node->accept($visitor);
    }

    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitAllocationExpression'))
            ->will($this->returnValue(42));

        $node = new PHP_Depend_Code_ASTAllocationExpression();
        self::assertEquals(42, $node->accept($visitor));
    }
    
    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionGraphForSimpleIdentifier()
    {
        $packages = self::parseCodeResourceForTest();
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $allocation = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTAllocationExpression::CLAZZ
        );

        $reference = $allocation->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTClassReference::CLAZZ, $reference);
        $this->assertEquals('Foo', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionGraphForSelfKeyword()
    {
        $packages = self::parseCodeResourceForTest();
        $method = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $allocation = $method->getFirstChildOfType(
            PHP_Depend_Code_ASTAllocationExpression::CLAZZ
        );

        $self = $allocation->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTSelfReference::CLAZZ, $self);
        $this->assertEquals(__FUNCTION__, $self->getType()->getName());
    }


    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionGraphForParentKeyword()
    {
        $packages = self::parseCodeResourceForTest();
        $method = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $allocation = $method->getFirstChildOfType(
            PHP_Depend_Code_ASTAllocationExpression::CLAZZ
        );

        $parent = $allocation->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTParentReference::CLAZZ, $parent);
        $this->assertEquals(__FUNCTION__ . 'Parent', $parent->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionGraphForLocalNamespaceIdentifier()
    {
        $packages = self::parseCodeResourceForTest();
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $allocation = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTAllocationExpression::CLAZZ
        );

        $reference = $allocation->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTClassReference::CLAZZ, $reference);
        $this->assertEquals('Bar', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionGraphForAbsoluteNamespaceIdentifier()
    {
        $packages = self::parseCodeResourceForTest();
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $allocation = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTAllocationExpression::CLAZZ
        );

        $reference = $allocation->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTClassReference::CLAZZ, $reference);
        $this->assertEquals('Bar', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionGraphForAbsoluteNamespacedNamespaceIdentifier()
    {
        $packages = self::parseCodeResourceForTest();
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $allocation = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTAllocationExpression::CLAZZ
        );

        $reference = $allocation->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTClassReference::CLAZZ, $reference);
        $this->assertEquals('Foo', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionGraphForVariableIdentifier()
    {
        $packages = self::parseCodeResourceForTest();
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $allocation = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTAllocationExpression::CLAZZ
        );

        $variable = $allocation->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertEquals('$foo', $variable->getImage());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionGraphForVariableVariableIdentifier()
    {
        $packages = self::parseCodeResourceForTest();
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $allocation = $function->getFirstChildOfType(
            PHP_Depend_Code_ASTAllocationExpression::CLAZZ
        );

        $vvariable = $allocation->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariableVariable::CLAZZ, $vvariable);
        $this->assertEquals('$', $vvariable->getImage());

        $variable = $vvariable->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertEquals('$foo', $variable->getImage());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionGraphForStaticReference()
    {
        $packages = self::parseCodeResourceForTest();
        $method   = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $allocation = $method->getFirstChildOfType(
            PHP_Depend_Code_ASTAllocationExpression::CLAZZ
        );

        $reference = $allocation->getChild(0);
        $this->assertType(PHP_Depend_Code_ASTStaticReference::CLAZZ, $reference);
        $this->assertEquals(__FUNCTION__, $reference->getType()->getName());
    }

    /**
     * Tests that invalid allocation expression results in the expected 
     * exception.
     * 
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testInvalidAllocationExpressionResultsInExpectedException()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: ;, line: 4, col: 9, file: '
        );
        self::parseCodeResourceForTest();
    }

    /**
     * Tests the implementation with an allocation expression without arguments.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionWithoutArguments()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        $args = $expr->findChildrenOfType(PHP_Depend_Code_ASTArguments::CLAZZ);

        $this->assertEquals(0, count($args));
    }

    /**
     * Tests the implementation with an allocation expression with arguments.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionWithArguments()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        $args = $expr->findChildrenOfType(PHP_Depend_Code_ASTArguments::CLAZZ);

        $this->assertEquals(1, count($args));
    }

    /**
     * Tests the implementation with an allocation expression with nested
     * expressions that have arguments.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionWithNestedArguments()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        $arg  = $expr->getFirstChildOfType(PHP_Depend_Code_ASTArguments::CLAZZ);

        $this->assertEquals($expr, $arg->getParent());
    }

    /**
     * Tests the start line of an allocation expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * Tests the start column of an allocation expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        $this->assertEquals(5, $expr->getStartColumn());
    }

    /**
     * Tests the end line of an allocation expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        $this->assertEquals(8, $expr->getEndLine());
    }

    /**
     * Tests the end column of an allocation expression.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTAllocationExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAllocationExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstAllocationExpressionInFunction(__METHOD__);
        $this->assertEquals(13, $expr->getEndColumn());
    }

    /**
     * Returns a test allocation expression.
     *
     * @param string $testCase The calling test case.
     *
     * @return PHP_Depend_Code_ASTAllocationExpression
     */
    private function _getFirstAllocationExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTAllocationExpression::CLAZZ
        );
    }
}
