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

use PDepend\AbstractTestCase;
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
class PHPParserVersion54Test extends AbstractTestCase
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
     */
    public function testParserThrowsExpectedExceptionForTraitAsClassName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsFunctionName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTraitAsFunctionName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsInterfaceName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTraitAsInterfaceName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsMethodName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTraitAsMethodName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsNamespaceName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTraitAsNamespaceName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsCalledFunction
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTraitAsCalledFunction()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that ::class is not allowed PHP < 5.5.
     *
     * @return void
     */
    public function testDoubleColonClass()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsCalledMethod
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTraitAsCalledMethod()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTraitAsConstant
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTraitAsConstant()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsClassName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsClassName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsFunctionName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsFunctionName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsInterfaceName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsInterfaceName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsMethodName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsMethodName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsInterfaceName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsNamespaceName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsCalledFunction
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsCalledFunction()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsCalledMethod
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsCalledMethod()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForInsteadOfAsConstant
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInsteadOfAsConstant()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsClassName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForCallableAsClassName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsFunctionName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForCallableAsFunctionName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsInterfaceName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForCallableAsInterfaceName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsMethodName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForCallableAsMethodName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsInterfaceName
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForCallableAsNamespaceName()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsCalledFunction
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForCallableAsCalledFunction()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForCallableAsConstant
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForCallableAsConstant()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExceptionForParameterWithExpressionValue
     *
     * @return void
     */
    public function testParserThrowsExceptionForParameterWithExpressionValue()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * @return void
     */
    public function testListKeywordAsMethodNameThrowsException()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * @return void
     */
    public function testListKeywordAsFunctionNameThrowsException()
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

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
        $this->expectException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException'
        );
        $this->expectExceptionMessage(
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
