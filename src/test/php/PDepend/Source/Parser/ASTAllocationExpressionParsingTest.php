<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 0.10.2
 */

namespace PDepend\Source\Parser;

use PDepend\Source\AST\ASTAllocationExpression;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\AbstractPHPParser} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 0.10.2
 *
 * @group unittest
 */
class ASTAllocationExpressionParsingTest extends AbstractParserTestCase
{
    /**
     * testAllocationExpressionForSelfProperty
     *
     * @since 1.0.1
     */
    public function testAllocationExpressionForSelfProperty(): void
    {
        $allocation = $this->getFirstAllocationInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $allocation->getChild(0));
    }

    /**
     * testAllocationExpressionForParentProperty
     *
     * @since 1.0.1
     */
    public function testAllocationExpressionForParentProperty(): void
    {
        $allocation = $this->getFirstAllocationInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $allocation->getChild(0));
    }

    /**
     * testAllocationExpressionForStaticProperty
     *
     * @since 1.0.1
     */
    public function testAllocationExpressionForStaticProperty(): void
    {
        $allocation = $this->getFirstAllocationInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $allocation->getChild(0));
    }

    /**
     * testAllocationExpressionForThisProperty
     *
     * @since 1.0.1
     */
    public function testAllocationExpressionForThisProperty(): void
    {
        $allocation = $this->getFirstAllocationInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFunctionPostfix', $allocation->getChild(0));
    }

    /**
     * testAllocationExpressionForObjectProperty
     *
     * @since 1.0.1
     */
    public function testAllocationExpressionForObjectProperty(): void
    {
        $allocation = $this->getFirstAllocationInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $allocation->getChild(0));
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     */
    public function testAllocationExpressionGraphForSimpleIdentifier(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $allocation = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $reference = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassReference', $reference);
        $this->assertEquals('Foo', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     */
    public function testAllocationExpressionGraphForSelfKeyword(): void
    {
        $method = $this->getFirstClassMethodForTestCase();
        $allocation = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $self = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSelfReference', $self);
        $this->assertEquals(__FUNCTION__, $self->getType()->getName());
    }


    /**
     * Tests that the allocation object graph contains the expected objects
     */
    public function testAllocationExpressionGraphForParentKeyword(): void
    {
        $method = $this->getFirstClassMethodForTestCase();
        $allocation = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $parent = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTParentReference', $parent);
        $this->assertEquals(__FUNCTION__ . 'Parent', $parent->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     */
    public function testAllocationExpressionGraphForLocalNamespaceIdentifier(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $allocation = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $reference = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassReference', $reference);
        $this->assertEquals('Bar', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     */
    public function testAllocationExpressionGraphForAbsoluteNamespaceIdentifier(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $allocation = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $reference = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassReference', $reference);
        $this->assertEquals('Bar', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     */
    public function testAllocationExpressionGraphForAbsoluteNamespacedNamespaceIdentifier(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $allocation = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $reference = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassReference', $reference);
        $this->assertEquals('Foo', $reference->getType()->getName());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     */
    public function testAllocationExpressionGraphForVariableIdentifier(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $allocation = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTAllocationExpression');
        $variable = $allocation->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $variable);
        $this->assertEquals('$foo', $variable->getImage());
    }

    /**
     * Tests that the allocation object graph contains the expected objects
     */
    public function testAllocationExpressionGraphForVariableVariableIdentifier(): void
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
     */
    public function testAllocationExpressionGraphForStaticReference(): void
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
     */
    public function testInvalidAllocationExpressionResultsInExpectedException(): void
    {
        $this->expectException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException'
        );
        $this->expectExceptionMessage(
            'Unexpected token: ;, line: 4, col: 9, file: '
        );
        $this->parseCodeResourceForTest();
    }

    /**
     * Returns the first allocation expression found in the test file associated
     * with the calling test method.
     *
     * @return ASTAllocationExpression
     *
     * @since 1.0.1
     */
    private function getFirstAllocationInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTAllocationExpression'
        );
    }
}
