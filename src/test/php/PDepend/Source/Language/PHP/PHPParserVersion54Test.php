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
 * @since 2.3
 */

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTest;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Util\Cache\CacheDriver;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserVersion54} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 2.3
 *
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion54
 * @group unittest
 */
class PHPParserVersion54Test extends AbstractTest
{
    /**
     * testParserHandlesBinaryIntegerLiteral
     *
     * @return void
     */
    public function testParserHandlesBinaryIntegerLiteral()
    {
        $method  = $this->getFirstMethodForTestCase();
        $literal = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTLiteral');

        $this->assertEquals('0b0100110100111', $literal->getImage());
    }

    /**
     * testParserThrowsExceptionForInvalidBinaryIntegerLiteral
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExceptionForInvalidBinaryIntegerLiteral()
    {
        if (version_compare(phpversion(), '5.4alpha') >= 0) {
            $this->markTestSkipped('This test only affects PHP < 5.4');
        }
        $this->getFirstMethodForTestCase();
    }

    /**
     * testParserHandlesStaticMemberExpressionSyntax
     *
     * @return void
     */
    public function testParserHandlesStaticMemberExpressionSyntax()
    {
        $function = $this->getFirstFunctionForTestCase();
        $expr = $function->getFirstChildOfType('PDepend\\Source\\AST\\ASTCompoundExpression');

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTCompoundExpression', $expr);
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsClassName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForTraitAsClassName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsFunctionName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForTraitAsFunctionName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsInterfaceName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForTraitAsInterfaceName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsMethodName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForTraitAsMethodName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsNamespaceName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForTraitAsNamespaceName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsCalledFunction
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForTraitAsCalledFunction()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that ::class is not allowed PHP < 5.5.
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testDoubleColonClass()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsCalledMethod
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForTraitAsCalledMethod()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsConstant
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForTraitAsConstant()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsClassName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsClassName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsFunctionName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsFunctionName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsInterfaceName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsInterfaceName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsMethodName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsMethodName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsInterfaceName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsNamespaceName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsCalledFunction
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsCalledFunction()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsCalledMethod
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsCalledMethod()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsConstant
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsConstant()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsClassName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForCallableAsClassName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsFunctionName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForCallableAsFunctionName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsInterfaceName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForCallableAsInterfaceName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsMethodName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForCallableAsMethodName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsInterfaceName
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForCallableAsNamespaceName()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsCalledFunction
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForCallableAsCalledFunction()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsConstant
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForCallableAsConstant()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExceptionForParameterWithExpressionValue
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExceptionForParameterWithExpressionValue()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testListKeywordAsMethodNameThrowsException()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testListKeywordAsFunctionNameThrowsException()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * @return void
     */
    public function testMagicTraitConstantInString()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * Tests that the parser throws an exception when it detects an invalid
     * token in a method or property declaration.
     *
     * @return void
     */
    public function testParserThrowsUnexpectedTokenExceptionForInvalidTokenInPropertyDeclaration()
    {
        $this->setExpectedException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException',
            'Unexpected token: const, line: 4, col: 13, file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * @param \PDepend\Source\Tokenizer\Tokenizer $tokenizer
     * @param \PDepend\Source\Builder\Builder<mixed> $builder
     * @param \PDepend\Util\Cache\CacheDriver $cache
     * @return \PDepend\Source\Language\PHP\AbstractPHPParser
     */
    protected function createPHPParser(Tokenizer $tokenizer, Builder $builder, CacheDriver $cache)
    {
        return $this->getAbstractClassMock(
            'PDepend\\Source\\Language\\PHP\\PHPParserVersion54',
            array($tokenizer, $builder, $cache)
        );
    }
}
