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

use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTConstantPostfix;
use PDepend\Source\AST\ASTFunctionPostfix;
use PDepend\Source\AST\ASTIdentifier;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTMethodPostfix;
use PDepend\Source\AST\ASTParentReference;
use PDepend\Source\AST\ASTPropertyPostfix;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTVariable;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\AbstractPHPParser} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.10.2
 *
 * @group unittest
 */
class ASTArgumentsParsingTest extends AbstractParserTestCase
{
    /**
     * Tests that the parser adds the expected children to an argument instance.
     */
    public function testArgumentsContainsStaticMethodPostfixExpression(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArgumentsOfFunction(),
            [
                ASTMemberPrimaryPrefix::class,
                ASTClassOrInterfaceReference::class,
                ASTMethodPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
            ]
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     */
    public function testArgumentsContainsMethodPostfixExpression(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArgumentsOfFunction(),
            [
                ASTMemberPrimaryPrefix::class,
                ASTVariable::class,
                ASTMethodPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
            ]
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     */
    public function testArgumentsContainsConstantsPostfixExpression(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArgumentsOfFunction(),
            [
                ASTMemberPrimaryPrefix::class,
                ASTClassOrInterfaceReference::class,
                ASTConstantPostfix::class,
                ASTIdentifier::class,
            ]
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     */
    public function testArgumentsContainsPropertyPostfixExpression(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArgumentsOfFunction(),
            [
                ASTMemberPrimaryPrefix::class,
                ASTClassOrInterfaceReference::class,
                ASTPropertyPostfix::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     */
    public function testArgumentsContainsSelfPropertyPostfixExpression(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArgumentsOfMethod(),
            [
                ASTMemberPrimaryPrefix::class,
                ASTSelfReference::class,
                ASTPropertyPostfix::class,
                ASTVariable::class,
            ]
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     */
    public function testArgumentsContainsParentMethodPostfixExpression(): void
    {
        $this->assertGraphEquals(
            $this->getFirstArgumentsOfMethod(),
            [
                ASTMemberPrimaryPrefix::class,
                ASTParentReference::class,
                ASTMethodPostfix::class,
                ASTIdentifier::class,
                ASTArguments::class,
            ]
        );
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     */
    public function testArgumentsContainsAllocationExpression(): void
    {
        $arguments = $this->getFirstArgumentsOfFunction();

        $allocation = $arguments->getChild(0);
        static::assertInstanceOf(ASTAllocationExpression::class, $allocation);
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     */
    public function testArgumentsWithSeveralParameters(): void
    {
        $arguments = $this->getFirstArgumentsOfFunction();

        $postfix = $arguments->getFirstChildOfType(
            ASTFunctionPostfix::class
        );
        static::assertInstanceOf(ASTFunctionPostfix::class, $postfix);
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     */
    public function testArgumentsWithInlineComments(): void
    {
        $arguments = $this->getFirstArgumentsOfFunction();

        $child = $arguments->getChild(0);
        static::assertInstanceOf(ASTVariable::class, $child);
    }

    /**
     * Tests that the parser adds the expected children to an argument instance.
     */
    public function testArgumentsWithInlineConcatExpression(): void
    {
        $arguments = $this->getFirstArgumentsOfFunction();

        $postfixes = $arguments->findChildrenOfType(
            ASTMethodPostfix::class
        );
        static::assertCount(1, $postfixes);
    }

    /**
     * Tests that an invalid arguments expression results in the expected
     * exception.
     */
    public function testUnclosedArgumentsExpressionThrowsExpectedException(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * Returns an arguments instance for the currently executed test case.
     */
    private function getFirstArgumentsOfFunction(): ASTArguments
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTArguments::class
        );
    }

    /**
     * Returns an arguments instance for the currently executed test case.
     */
    private function getFirstArgumentsOfMethod(): ASTArguments
    {
        return $this->getFirstNodeOfTypeInClass(
            ASTArguments::class
        );
    }
}
