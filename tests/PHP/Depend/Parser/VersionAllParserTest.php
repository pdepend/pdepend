<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 * @since      0.9.20
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Parser_VersionAllParser} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * @since      0.9.20
 */
class PHP_Depend_Parser_VersionAllParserTest extends PHP_Depend_Parser_AbstractTest
{
    /**
     * testParserAcceptsStringAsClassName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsStringAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('SimpleClassName', $class->getName());
    }

    /**
     * testParserAcceptsStringAsInterfaceName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsStringAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('SimpleInterfaceName', $interface->getName());
    }

    /**
     * testParserAcceptsNullAsClassName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsNullAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('Null', $class->getName());
    }

    /**
     * testParserAcceptsNullAsInterfaceName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsNullAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('Null', $interface->getName());
    }

    /**
     * testParserAcceptsTrueAsClassName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsTrueAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('True', $class->getName());
    }

    /**
     * testParserAcceptsTrueAsInterfaceName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsTrueAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('True', $interface->getName());
    }

    /**
     * testParserAcceptsFalseAsClassName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsFalseAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('False', $class->getName());
    }

    /**
     * testParserAcceptsFalseAsInterfaceName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsFalseAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('False', $interface->getName());
    }

    /**
     * testParserAcceptsUseAsClassName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsUseAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('Use', $class->getName());
    }

    /**
     * testParserAcceptsUseAsInterfaceName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsUseAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('Use', $interface->getName());
    }

    /**
     * testParserAcceptsNamespaceAsClassName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsNamespaceAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('Namespace', $class->getName());
    }

    /**
     * testParserAcceptsNamespaceAsInterfaceName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsNamespaceAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('Namespace', $interface->getName());
    }

    /**
     * testParserThrowsExpectedExceptionOnTokenStreamEnd
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @covers PHP_Depend_Parser_TokenStreamEndException
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     * @expectedException PHP_Depend_Parser_TokenStreamEndException
     */
    public function testParserThrowsExpectedExceptionOnTokenStreamEnd()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForUnexpectedTokenType
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @covers PHP_Depend_Parser_UnexpectedTokenException
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForUnexpectedTokenType()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserAcceptsStringAsMethodName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsStringAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('myMethodName', $method->getName());
    }

    /**
     * testParserAcceptsUseKeywordAsMethodName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsUseKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('Use', $method->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsMethodName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsGotoKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('Goto', $method->getName());
    }

    /**
     * testParserAcceptsSelfKeywordAsMethodName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsSelfKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('self', $method->getName());
    }

    /**
     * testParserAcceptsNullAsMethodName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsNullAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('null', $method->getName());
    }

    /**
     * testParserAcceptsTrueAsMethodName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsTrueAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('true', $method->getName());
    }

    /**
     * testParserAcceptsFalseAsMethodName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsFalseAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('false', $method->getName());
    }

    /**
     * testParserAcceptsDirConstantAsMethodName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsDirConstantAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('__DIR__', $method->getName());
    }

    /**
     * testParserAcceptsNamespaceKeywordAsMethodName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsNamespaceKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('nameSpace', $method->getName());
    }

    /**
     * testParserAcceptsNamespaceConstantAsMethodName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsNamespaceConstantAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('__NAMESPACE__', $method->getName());
    }

    /**
     * testParserAcceptsParentKeywordAsMethodName
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAcceptsParentKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('Parent', $method->getName());
    }

    /**
     * testParserThrowsExpectedExceptionForInvalidToken
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForInvalidToken()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTokenStreamEnd
     *
     * @return void
     * @covers PHP_Depend_Parser_VersionAllParser
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     * @expectedException PHP_Depend_Parser_TokenStreamEndException
     */
    public function testParserThrowsExpectedExceptionForTokenStreamEnd()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Returns the first class or interface that could be found in the code under
     * test for the calling test case.
     *
     * @return PHP_Depend_Code_AbstractClassOrInterface
     */
    protected function getFirstTypeForTestCase()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current();
    }

    /**
     * Returns the first method that could be found in the code under test for
     * the calling test case.
     *
     * @return PHP_Depend_Code_Method
     */
    protected function getFirstMethodForTestCase()
    {
        return $this->getFirstTypeForTestCase()
            ->getMethods()
            ->current();
    }
}