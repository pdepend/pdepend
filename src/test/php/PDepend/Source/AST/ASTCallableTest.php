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
 */

namespace PDepend\Source\AST;

use PDepend\AbstractTestCase;
use PDepend\Source\Tokenizer\Token;

/**
 * Test case for the {@link \PDepend\Source\AST\AbstractASTCallable} class.
 *
 * @covers \PDepend\Source\AST\AbstractASTCallable
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTCallableTest extends AbstractTestCase
{
    /**
     * testGetParametersReturnsEmptyArrayByDefault
     */
    public function testGetParametersReturnsEmptyArrayByDefault(): void
    {
        $callable = $this->getFirstCallableForTest();
        static::assertEquals([], $callable->getParameters());
    }

    /**
     * testGetParametersReturnsArrayWithOneElement
     */
    public function testGetParametersReturnsArrayWithOneElement(): void
    {
        $callable = $this->getFirstCallableForTest();
        static::assertCount(1, $callable->getParameters());
    }

    /**
     * testGetParametersReturnsArrayWithThreeElements
     */
    public function testGetParametersReturnsArrayWithThreeElements(): void
    {
        $callable = $this->getFirstCallableForTest();
        static::assertCount(3, $callable->getParameters());
    }

    /**
     * testGetParametersReturnsArrayWithObjectsOfTypeParameter
     */
    public function testGetParametersReturnsArrayWithObjectsOfTypeParameter(): void
    {
        $parameters = $this->getFirstCallableForTest()->getParameters();
        static::assertInstanceOf(ASTParameter::class, $parameters[0]);
    }

    /**
     * testGetParametersNotSetsOptionalOnParameterWithoutDefaultValue
     */
    public function testGetParametersNotSetsOptionalOnParameterWithoutDefaultValue(): void
    {
        $parameters = $this->getFirstCallableForTest()->getParameters();
        static::assertFalse($parameters[0]->isOptional());
    }

    /**
     * testGetParametersNotSetsOptionalOnParameterWithFollowingParameterWithoutDefaultValue
     */
    public function testGetParametersNotSetsOptionalOnParameterWithFollowingParameterWithoutDefaultValue(): void
    {
        $parameters = $this->getFirstCallableForTest()->getParameters();
        static::assertFalse($parameters[0]->isOptional());
    }

    /**
     * testGetParametersSetsOptionalOnParameterWithDefaultValue
     */
    public function testGetParametersSetsOptionalOnParameterWithDefaultValue(): void
    {
        $parameters = $this->getFirstCallableForTest()->getParameters();
        static::assertTrue($parameters[0]->isOptional());
    }

    /**
     * testGetParametersSetsOptionalOnLastParameterWithDefaultValue
     */
    public function testGetParametersSetsOptionalOnLastParameterWithDefaultValue(): void
    {
        $parameters = $this->getFirstCallableForTest()->getParameters();
        static::assertTrue($parameters[2]->isOptional());
    }

    /**
     * testGetChildrenReturnsExpectedNumberOfNodes
     *
     * @since 1.0.0
     */
    public function testGetChildrenReturnsExpectedNumberOfNodes(): void
    {
        $children = $this->getFirstCallableForTest()
            ->getChildren();

        static::assertCount(2, $children);
    }

    /**
     * testGetTokensDelegatesCallToCacheRestore
     */
    public function testGetTokensDelegatesCallToCacheRestore(): void
    {
        $cache = $this->createCacheFixture();
        $cache->expects(static::once())
            ->method('type')
            ->with(static::equalTo('tokens'))
            ->will(static::returnValue($cache));
        $cache->expects(static::once())
            ->method('restore');

        $callable = $this->getCallableMock();
        $callable->setCache($cache)
            ->getTokens();
    }

    /**
     * testSetTokensDelegatesCallToCacheStore
     */
    public function testSetTokensDelegatesCallToCacheStore(): void
    {
        $tokens = [new Token(1, 'a', 23, 42, 13, 17)];

        $cache = $this->createCacheFixture();
        $cache->expects(static::once())
            ->method('type')
            ->with(static::equalTo('tokens'))
            ->will(static::returnValue($cache));
        $cache->expects(static::once())
            ->method('store');

        $callable = $this->getCallableMock();
        $callable->setCache($cache)
            ->setTokens($tokens);
    }

    /**
     * testGetStartLineReturnsZeroByDefault
     */
    public function testGetStartLineReturnsZeroByDefault(): void
    {
        $callable = $this->getCallableMock();
        static::assertSame(0, $callable->getStartLine());
    }

    /**
     * testGetStartLineReturnsStartLineOfFirstToken
     */
    public function testGetStartLineReturnsStartLineOfFirstToken(): void
    {
        $tokens = [
            new Token(1, 'a', 13, 17, 0, 0),
            new Token(2, 'b', 23, 42, 0, 0),
        ];

        $cache = $this->createCacheFixture();
        $cache->expects(static::once())
            ->method('type')
            ->will(static::returnValue($cache));

        $callable = $this->getCallableMock();
        $callable->setCache($cache)
            ->setTokens($tokens);

        static::assertSame(13, $callable->getStartLine());
    }

    /**
     * testGetEndLineReturnsZeroByDefault
     */
    public function testGetEndLineReturnsZeroByDefault(): void
    {
        $callable = $this->getCallableMock();
        static::assertSame(0, $callable->getEndLine());
    }

    /**
     * testGetEndLineReturnsEndLineOfLastToken
     */
    public function testGetEndLineReturnsEndLineOfLastToken(): void
    {
        $tokens = [
            new Token(1, 'a', 13, 17, 0, 0),
            new Token(2, 'b', 23, 42, 0, 0),
        ];

        $cache = $this->createCacheFixture();
        $cache->expects(static::once())
            ->method('type')
            ->will(static::returnValue($cache));

        $callable = $this->getCallableMock();
        $callable->setCache($cache)
            ->setTokens($tokens);

        static::assertSame(42, $callable->getEndLine());
    }

    /**
     * testGetReturnClassReturnsNullByDefault
     */
    public function testGetReturnClassReturnsNullByDefault(): void
    {
        $callable = $this->getCallableMock();
        static::assertNull($callable->getReturnClass());
    }

    /**
     * Returns the first callable found in the test file for the calling test
     * method.
     *
     * @since 0.10.0
     */
    protected function getFirstCallableForTest(): AbstractASTCallable
    {
        return $this->getFirstFunctionForTestCase();
    }

    /**
     * Returns a mocked instance of the callable class.
     */
    protected function getCallableMock(): AbstractASTCallable
    {
        return $this->getMockForAbstractClass(
            AbstractASTCallable::class,
            [__CLASS__]
        );
    }
}
