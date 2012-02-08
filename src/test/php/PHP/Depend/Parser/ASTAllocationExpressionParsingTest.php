<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 * @since      0.10.2
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Parser} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * @since      0.10.2
 *
 * @covers PHP_Depend_Parser
 * @group pdepend
 * @group pdepend::parser
 * @group unittest
 */
class PHP_Depend_Parser_ASTAllocationExpressionParsingTest
    extends PHP_Depend_Parser_AbstractTest
{
    /**
     * testAllocationExpressionForSelfProperty
     *
     * @return void
     * @since 1.0.1
     */
    public function testAllocationExpressionForSelfProperty()
    {
        $allocation = $this->_getFirstAllocationInClass();
        $this->assertInstanceOf(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
            $allocation->getChild(0)
        );
    }

    /**
     * testAllocationExpressionForParentProperty
     *
     * @return void
     * @since 1.0.1
     */
    public function testAllocationExpressionForParentProperty()
    {
        $allocation = $this->_getFirstAllocationInClass();
        $this->assertInstanceOf(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
            $allocation->getChild(0)
        );
    }

    /**
     * testAllocationExpressionForStaticProperty
     *
     * @return void
     * @since 1.0.1
     */
    public function testAllocationExpressionForStaticProperty()
    {
        $allocation = $this->_getFirstAllocationInClass();
        $this->assertInstanceOf(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
            $allocation->getChild(0)
        );
    }

    /**
     * testAllocationExpressionForThisProperty
     *
     * @return void
     * @since 1.0.1
     */
    public function testAllocationExpressionForThisProperty()
    {
        $allocation = $this->_getFirstAllocationInClass();
        $this->assertInstanceOf(
            PHP_Depend_Code_ASTFunctionPostfix::CLAZZ,
            $allocation->getChild(0)
        );
    }

    /**
     * testAllocationExpressionForObjectProperty
     *
     * @return void
     * @since 1.0.1
     */
    public function testAllocationExpressionForObjectProperty()
    {
        $allocation = $this->_getFirstAllocationInClass();
        $this->assertInstanceOf(
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ,
            $allocation->getChild(0)
        );
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
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
        $this->assertInstanceOf(PHP_Depend_Code_ASTClassReference::CLAZZ, $reference);
        $this->assertEquals('Foo', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
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
        $this->assertInstanceOf(PHP_Depend_Code_ASTSelfReference::CLAZZ, $self);
        $this->assertEquals(__FUNCTION__, $self->getType()->getName());
    }


    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
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
        $this->assertInstanceOf(PHP_Depend_Code_ASTParentReference::CLAZZ, $parent);
        $this->assertEquals(__FUNCTION__ . 'Parent', $parent->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
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
        $this->assertInstanceOf(PHP_Depend_Code_ASTClassReference::CLAZZ, $reference);
        $this->assertEquals('Bar', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
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
        $this->assertInstanceOf(PHP_Depend_Code_ASTClassReference::CLAZZ, $reference);
        $this->assertEquals('Bar', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
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
        $this->assertInstanceOf(PHP_Depend_Code_ASTClassReference::CLAZZ, $reference);
        $this->assertEquals('Foo', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
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
        $this->assertInstanceOf(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertEquals('$foo', $variable->getImage());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
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
        $this->assertInstanceOf(PHP_Depend_Code_ASTVariableVariable::CLAZZ, $vvariable);
        $this->assertEquals('$', $vvariable->getImage());

        $variable = $vvariable->getChild(0);
        $this->assertInstanceOf(PHP_Depend_Code_ASTVariable::CLAZZ, $variable);
        $this->assertEquals('$foo', $variable->getImage());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
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
        $this->assertInstanceOf(PHP_Depend_Code_ASTStaticReference::CLAZZ, $reference);
        $this->assertEquals(__FUNCTION__, $reference->getType()->getName());
    }

    /**
     * Tests that invalid allocation expression results in the expected
     * exception.
     *
     * @return void
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
     * Returns the first allocation expression found in the test file associated
     * with the calling test method.
     *
     * @return PHP_Depend_Code_ASTAllocationExpression
     * @since 1.0.1
     */
    private function _getFirstAllocationInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            PHP_Depend_Code_ASTAllocationExpression::CLAZZ
        );
    }
}
