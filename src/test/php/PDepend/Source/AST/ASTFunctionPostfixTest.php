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
 * Test case for the {@link \PDepend\Source\AST\ASTFunctionPostfix} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTInvocation
 * @covers \PDepend\Source\AST\ASTFunctionPostfix
 * @group unittest
 */
class ASTFunctionPostfixTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testGetImageForVariableFunction
     *
     * <code>
     * $function(23);
     * </code>
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetImageForVariableFunction()
    {
        $postfix = $this->_getFirstFunctionPostfixInFunction();
        $this->assertEquals('$function', $postfix->getImage());
    }

    /**
     * testGetImageForArrayIndexedVariableFunction
     *
     * <code>
     * $function[42](23);
     * </code>
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetImageForArrayIndexedVariableFunction()
    {
        $postfix = $this->_getFirstFunctionPostfixInFunction();
        $this->assertEquals('$function', $postfix->getImage());
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     *
     * @return void
     */
    public function testFunctionPostfixGraphForSimpleInvocation()
    {
        $postfix  = $this->_getFirstFunctionPostfixInFunction();
        $expected = array(
            'PDepend\\Source\\AST\\ASTIdentifier',
            'PDepend\\Source\\AST\\ASTArguments',
            'PDepend\\Source\\AST\\ASTLiteral'
        );

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     *
     * @return void
     */
    public function testFunctionPostfixGraphForVariableInvocation()
    {
        $postfix  = $this->_getFirstFunctionPostfixInFunction();
        $expected = array(
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     *
     * @return void
     */
    public function testFunctionPostfixGraphForCompoundVariableInvocation()
    {
        $postfix  = $this->_getFirstFunctionPostfixInFunction();
        $expected = array(
            'PDepend\\Source\\AST\\ASTCompoundVariable',
            'PDepend\\Source\\AST\\ASTConstant',
            'PDepend\\Source\\AST\\ASTArguments',
            'PDepend\\Source\\AST\\ASTConstant'
        );

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * testFunctionPostfixGraphForArrayIndexedVariableInvocation
     * 
     * @return void
     */
    public function testFunctionPostfixGraphForArrayIndexedVariableInvocation()
    {
        $postfix  = $this->_getFirstFunctionPostfixInFunction();
        $expected = array(
            'PDepend\\Source\\AST\\ASTArrayIndexExpression',
            'PDepend\\Source\\AST\\ASTArrayIndexExpression',
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTLiteral',
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     *
     * @return void
     */
    public function testFunctionPostfixGraphForInvocationWithMemberPrimaryPrefixMethod()
    {
        $postfix  = $this->_getFirstFunctionPostfixInFunction();
        $expected = array(
            'PDepend\\Source\\AST\\ASTIdentifier',
            'PDepend\\Source\\AST\\ASTArguments',
            'PDepend\\Source\\AST\\ASTLiteral'
        );

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * Tests that a parsed function postfix has the expected object structure.
     *
     * @return void
     */
    public function testFunctionPostfixGraphForInvocationWithMemberPrimaryPrefixProperty()
    {
        $postfix  = $this->_getFirstFunctionPostfixInFunction();
        $expected = array(
            'PDepend\\Source\\AST\\ASTIdentifier',
            'PDepend\\Source\\AST\\ASTArguments',
            'PDepend\\Source\\AST\\ASTLiteral'
        );

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * testFunctionPostfixGraphForObjectProperty
     *
     * @return void
     */
    public function testFunctionPostfixGraphForObjectProperty()
    {
        $postfix  = $this->_getFirstFunctionPostfixInFunction();
        $expected = array(
            'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTPropertyPostfix',
            'PDepend\\Source\\AST\\ASTArrayIndexExpression',
            'PDepend\\Source\\AST\\ASTIdentifier',
            'PDepend\\Source\\AST\\ASTLiteral',
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $this->assertGraphEquals($postfix, $expected);
    }

    /**
     * testFunctionPostfixHasExpectedStartLine
     *
     * @return void
     */
    public function testFunctionPostfixHasExpectedStartLine()
    {
        $init = $this->_getFirstFunctionPostfixInFunction();
        $this->assertEquals(4, $init->getStartLine());
    }

    /**
     * testFunctionPostfixHasExpectedStartColumn
     *
     * @return void
     */
    public function testFunctionPostfixHasExpectedStartColumn()
    {
        $init = $this->_getFirstFunctionPostfixInFunction();
        $this->assertEquals(5, $init->getStartColumn());
    }

    /**
     * testFunctionPostfixHasExpectedEndLine
     *
     * @return void
     */
    public function testFunctionPostfixHasExpectedEndLine()
    {
        $init = $this->_getFirstFunctionPostfixInFunction();
        $this->assertEquals(8, $init->getEndLine());
    }

    /**
     * testFunctionPostfixHasExpectedEndColumn
     *
     * @return void
     */
    public function testFunctionPostfixHasExpectedEndColumn()
    {
        $init = $this->_getFirstFunctionPostfixInFunction();
        $this->assertEquals(13, $init->getEndColumn());
    }

    /**
     * Creates a field declaration node.
     *
     * @return \PDepend\Source\AST\ASTFunctionPostfix
     */
    protected function createNodeInstance()
    {
        return new \PDepend\Source\AST\ASTFunctionPostfix(__FUNCTION__);
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTFunctionPostfix
     */
    private function _getFirstFunctionPostfixInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(), 
            'PDepend\\Source\\AST\\ASTFunctionPostfix'
        );
    }
}
