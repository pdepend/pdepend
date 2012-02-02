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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_AbstractCallable} class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Code_AbstractCallable
 * @group pdepend
 * @group pdepend::code
 * @group unittest
 */
class PHP_Depend_Code_CommonCallableTest extends PHP_Depend_AbstractTest
{
    /**
     * testGetParametersReturnsEmptyArrayByDefault
     *
     * @return void
     */
    public function testGetParametersReturnsEmptyArrayByDefault()
    {
        $callable = $this->getFirstCallableForTest();
        self::assertEquals(array(), $callable->getParameters());
    }

    /**
     * testGetParametersReturnsArrayWithOneElement
     *
     * @return void
     */
    public function testGetParametersReturnsArrayWithOneElement()
    {
        $callable = $this->getFirstCallableForTest();
        self::assertEquals(1, count($callable->getParameters()));
    }

    /**
     * testGetParametersReturnsArrayWithThreeElements
     *
     * @return void
     */
    public function testGetParametersReturnsArrayWithThreeElements()
    {
        $callable = $this->getFirstCallableForTest();
        self::assertEquals(3, count($callable->getParameters()));
    }

    /**
     * testGetParametersReturnsArrayWithObjectsOfTypeParameter
     *
     * @return void
     */
    public function testGetParametersReturnsArrayWithObjectsOfTypeParameter()
    {
        $parameters = $this->getFirstCallableForTest()->getParameters();
        self::assertInstanceOf(PHP_Depend_Code_Parameter::CLAZZ, $parameters[0]);
    }

    /**
     * testGetParametersNotSetsOptionalOnParameterWithoutDefaultValue
     *
     * @return void
     */
    public function testGetParametersNotSetsOptionalOnParameterWithoutDefaultValue()
    {
        $parameters = $this->getFirstCallableForTest()->getParameters();
        self::assertFalse($parameters[0]->isOptional());
    }

    /**
     * testGetParametersNotSetsOptionalOnParameterWithFollowingParameterWithoutDefaultValue
     *
     * @return void
     */
    public function testGetParametersNotSetsOptionalOnParameterWithFollowingParameterWithoutDefaultValue()
    {
        $parameters = $this->getFirstCallableForTest()->getParameters();
        self::assertFalse($parameters[0]->isOptional());
    }

    /**
     * testGetParametersSetsOptionalOnParameterWithDefaultValue
     *
     * @return void
     */
    public function testGetParametersSetsOptionalOnParameterWithDefaultValue()
    {
        $parameters = $this->getFirstCallableForTest()->getParameters();
        self::assertTrue($parameters[0]->isOptional());
    }

    /**
     * testGetParametersSetsOptionalOnLastParameterWithDefaultValue
     *
     * @return void
     */
    public function testGetParametersSetsOptionalOnLastParameterWithDefaultValue()
    {
        $parameters = $this->getFirstCallableForTest()->getParameters();
        self::assertTrue($parameters[2]->isOptional());
    }

    /**
     * testGetChildrenReturnsExpectedNumberOfNodes
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetChildrenReturnsExpectedNumberOfNodes()
    {
        $children = $this->getFirstCallableForTest()
            ->getChildren();

        $this->assertEquals(2, count($children));
    }

    /**
     * testGetTokensDelegatesCallToCacheRestore
     *
     * @return void
     */
    public function testGetTokensDelegatesCallToCacheRestore()
    {
        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->with(self::equalTo('tokens'))
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('restore');

        $callable = $this->getCallableMock();
        $callable->setCache($cache)
            ->getTokens();
    }

    /**
     * testSetTokensDelegatesCallToCacheStore
     *
     * @return void
     */
    public function testSetTokensDelegatesCallToCacheStore()
    {
        $tokens = array(new PHP_Depend_Token(1, 'a', 23, 42, 13, 17));

        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->with(self::equalTo('tokens'))
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('store');

        $callable = $this->getCallableMock();
        $callable->setCache($cache)
            ->setTokens($tokens);
    }

    /**
     * testGetStartLineReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetStartLineReturnsZeroByDefault()
    {
        $callable = $this->getCallableMock();
        self::assertSame(0, $callable->getStartLine());
    }

    /**
     * testGetStartLineReturnsStartLineOfFirstToken
     *
     * @return void
     */
    public function testGetStartLineReturnsStartLineOfFirstToken()
    {
        $tokens = array(
            new PHP_Depend_Token(1, 'a', 13, 17, 0, 0),
            new PHP_Depend_Token(2, 'b', 23, 42, 0, 0)
        );

        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));

        $callable = $this->getCallableMock();
        $callable->setCache($cache)
            ->setTokens($tokens);

        self::assertSame(13, $callable->getStartLine());
    }

    /**
     * testGetEndLineReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetEndLineReturnsZeroByDefault()
    {
        $callable = $this->getCallableMock();
        self::assertSame(0, $callable->getEndLine());
    }

    /**
     * testGetEndLineReturnsEndLineOfLastToken
     *
     * @return void
     */
    public function testGetEndLineReturnsEndLineOfLastToken()
    {
        $tokens = array(
            new PHP_Depend_Token(1, 'a', 13, 17, 0, 0),
            new PHP_Depend_Token(2, 'b', 23, 42, 0, 0)
        );

        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->will($this->returnValue($cache));

        $callable = $this->getCallableMock();
        $callable->setCache($cache)
            ->setTokens($tokens);

        self::assertSame(42, $callable->getEndLine());
    }

    /**
     * testGetReturnClassReturnsNullByDefault
     *
     * @return void
     */
    public function testGetReturnClassReturnsNullByDefault()
    {
        $callable = $this->getCallableMock();
        self::assertNull($callable->getReturnClass());
    }

    /**
     * Returns the first callable found in the test file for the calling test
     * method.
     *
     * @return PHP_Depend_Code_AbstractCallable
     * @since 0.10.0
     */
    protected function getFirstCallableForTest()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();
    }

    /**
     * Returns a mocked instance of the callable class.
     *
     * @return PHP_Depend_Code_AbstractCallable
     */
    protected function getCallableMock()
    {
        return $this->getMockForAbstractClass(
            PHP_Depend_Code_AbstractCallable::CLAZZ,
            array(__CLASS__)
        );
    }
}
