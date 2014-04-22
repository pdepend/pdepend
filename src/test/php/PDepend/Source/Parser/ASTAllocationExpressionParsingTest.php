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
 * @since     0.10.2
 */

namespace PDepend\Source\Parser;

use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTClassReference;
use PDepend\Source\AST\ASTFunctionPostfix;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTParentReference;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTStaticReference;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableVariable;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\AbstractPHPParser} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since     0.10.2
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @group unittest
 */
class ASTAllocationExpressionParsingTest extends AbstractParserTest
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
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $allocation->getChild(0));
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
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $allocation->getChild(0));
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
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $allocation->getChild(0));
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
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFunctionPostfix', $allocation->getChild(0));
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
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $allocation->getChild(0));
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     */
    public function testAllocationExpressionGraphForSimpleIdentifier()
    {
        $function = $this->getFirstFunctionForTestCase();
        $allocation = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $reference = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassReference', $reference);
        $this->assertEquals('Foo', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     */
    public function testAllocationExpressionGraphForSelfKeyword()
    {
        $method = $this->getFirstClassMethodForTestCase();
        $allocation = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $self = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSelfReference', $self);
        $this->assertEquals(__FUNCTION__, $self->getType()->getName());
    }


    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     */
    public function testAllocationExpressionGraphForParentKeyword()
    {
        $method = $this->getFirstClassMethodForTestCase();
        $allocation = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $parent = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTParentReference', $parent);
        $this->assertEquals(__FUNCTION__ . 'Parent', $parent->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     */
    public function testAllocationExpressionGraphForLocalNamespaceIdentifier()
    {
        $function = $this->getFirstFunctionForTestCase();
        $allocation = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $reference = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassReference', $reference);
        $this->assertEquals('Bar', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     */
    public function testAllocationExpressionGraphForAbsoluteNamespaceIdentifier()
    {
        $function = $this->getFirstFunctionForTestCase();
        $allocation = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $reference = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassReference', $reference);
        $this->assertEquals('Bar', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     */
    public function testAllocationExpressionGraphForAbsoluteNamespacedNamespaceIdentifier()
    {
        $function = $this->getFirstFunctionForTestCase();
        $allocation = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $reference = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassReference', $reference);
        $this->assertEquals('Foo', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     */
    public function testAllocationExpressionGraphForVariableIdentifier()
    {
        $function = $this->getFirstFunctionForTestCase();
        $allocation = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $variable = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $variable);
        $this->assertEquals('$foo', $variable->getImage());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     */
    public function testAllocationExpressionGraphForVariableVariableIdentifier()
    {
        $function = $this->getFirstFunctionForTestCase();
        $allocation = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $vvariable = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariableVariable', $vvariable);
        $this->assertEquals('$', $vvariable->getImage());

        $variable = $vvariable->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $variable);
        $this->assertEquals('$foo', $variable->getImage());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     *
     * @return void
     */
    public function testAllocationExpressionGraphForStaticReference()
    {
        $method = $this->getFirstClassMethodForTestCase();
        $allocation = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $reference = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTStaticReference', $reference);
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
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException',
            'Unexpected token: ;, line: 4, col: 9, file: '
        );
        self::parseCodeResourceForTest();
    }

    /**
     * Returns the first allocation expression found in the test file associated
     * with the calling test method.
     *
     * @return ASTAllocationExpression
     * @since 1.0.1
     */
    private function _getFirstAllocationInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTAllocationExpression'
        );
    }
}
