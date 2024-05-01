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
 * @since 0.10.2
 */

namespace PDepend\Source\Parser;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\AbstractPHPParser} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.10.2
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @group unittest
 */
class ASTArgumentsParsingTest extends AbstractParserTestCase
{
    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsStaticMethodPostfixExpression(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArgumentsOfFunction(),
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments'
            )
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsMethodPostfixExpression(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArgumentsOfFunction(),
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTVariable',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments'
            )
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsConstantsPostfixExpression(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArgumentsOfFunction(),
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
                'PDepend\\Source\\AST\\ASTConstantPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier'
            )
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsPropertyPostfixExpression(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArgumentsOfFunction(),
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
                'PDepend\\Source\\AST\\ASTPropertyPostfix',
                'PDepend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsSelfPropertyPostfixExpression(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArgumentsOfMethod(),
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTSelfReference',
                'PDepend\\Source\\AST\\ASTPropertyPostfix',
                'PDepend\\Source\\AST\\ASTVariable'
            )
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsParentMethodPostfixExpression(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArgumentsOfMethod(),
            array(
                'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
                'PDepend\\Source\\AST\\ASTParentReference',
                'PDepend\\Source\\AST\\ASTMethodPostfix',
                'PDepend\\Source\\AST\\ASTIdentifier',
                'PDepend\\Source\\AST\\ASTArguments'
            )
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsContainsAllocationExpression(): void
    {
        $arguments = $this->getFirstArgumentsOfFunction();

        $allocation = $arguments->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTAllocationExpression', $allocation);
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsWithSeveralParameters(): void
    {
        $arguments = $this->getFirstArgumentsOfFunction();

        $postfix = $arguments->getFirstChildOfType(
            'PDepend\\Source\\AST\\ASTFunctionPostfix'
        );
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFunctionPostfix', $postfix);
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsWithInlineComments(): void
    {
        $arguments = $this->getFirstArgumentsOfFunction();

        $child = $arguments->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $child);
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     *
     * @return void
     */
    public function testArgumentsWithInlineConcatExpression(): void
    {
        $arguments = $this->getFirstArgumentsOfFunction();

        $postfixes = $arguments->findChildrenOfType(
            'PDepend\\Source\\AST\\ASTMethodPostfix'
        );
        $this->assertCount(1, $postfixes);
    }

    /**
     * Tests that an invalid arguments expression results in the expected
     * exception.
     *
     * @return void
     */
    public function testUnclosedArgumentsExpressionThrowsExpectedException(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * Returns an arguments instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTArguments
     */
    private function getFirstArgumentsOfFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTArguments'
        );
    }

    /**
     * Returns an arguments instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTArguments
     */
    private function getFirstArgumentsOfMethod()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTArguments'
        );
    }
}
