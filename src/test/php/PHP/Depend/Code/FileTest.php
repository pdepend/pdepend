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
 * Test case for the code file class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Code_File
 * @group pdepend
 * @group pdepend::code
 * @group unittest
 */
class PHP_Depend_Code_FileTest extends PHP_Depend_AbstractTest
{
    /**
     * testGetNameReturnsTheFileName
     * 
     * @return void
     */
    public function testGetNameReturnsTheFileName()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        self::assertEquals(__FILE__, $file->getName());
    }

    /**
     * testGetFileNameReturnsTheFileName
     *
     * @return void
     */
    public function testGetFileNameReturnsTheFileName()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        self::assertEquals(__FILE__, $file->getFileName());
    }

    /**
     * testGetUuidReturnsNullByDefault
     *
     * @return void
     */
    public function testGetUuidReturnsNullByDefault()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        self::assertNull($file->getUuid());
    }

    /**
     * testGetUuidReturnsInjectedUuidValue
     *
     * @return void
     */
    public function testGetUuidReturnsInjectedUuidValue()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUuid(__FUNCTION__);

        self::assertEquals(__FUNCTION__, $file->getUuid());
    }

    /**
     * testGetDocCommentReturnsNullByDefault
     *
     * @return void
     */
    public function testGetDocCommentReturnsNullByDefault()
    {
        $file = new PHP_Depend_Code_File(null);
        self::assertNull($file->getDocComment());
    }

    /**
     * testGetDocCommentReturnsInjectedDocCommentValue
     *
     * @return void
     */
    public function testGetDocCommentReturnsInjectedDocCommentValue()
    {
        $file = new PHP_Depend_Code_File(null);
        $file->setDocComment('/** Manuel */');

        self::assertEquals('/** Manuel */', $file->getDocComment());
    }

    /**
     * testGetTokensDelegatesCallToCacheRestoreWithFileUuid
     *
     * @return void
     */
    public function testGetTokensDelegatesCallToCacheRestoreWithFileUuid()
    {
        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->with(self::equalTo('tokens'))
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('restore')
            ->with(self::equalTo(__FUNCTION__));

        $file = new PHP_Depend_Code_File(null);
        $file->setCache($cache);
        $file->setUuid(__FUNCTION__);

        $file->getTokens();
    }

    /**
     * testSetTokensDelegatesCallToCacheStoreWithFileUuid
     *
     * @return void
     */
    public function testSetTokensDelegatesCallToCacheStoreWithFileUuid()
    {
        $cache = $this->getMock('PHP_Depend_Util_Cache_Driver');
        $cache->expects($this->once())
            ->method('type')
            ->with(self::equalTo('tokens'))
            ->will($this->returnValue($cache));
        $cache->expects($this->once())
            ->method('store')
            ->with(self::equalTo(__FUNCTION__), self::equalTo(array(1, 2, 3)));

        $file = new PHP_Depend_Code_File(null);
        $file->setCache($cache);
        $file->setUuid(__FUNCTION__);

        $file->setTokens(array(1, 2, 3));
    }

    /**
     * testAcceptInvokesVisitFileOnGivenVisitor
     *
     * @return void
     */
    public function testAcceptInvokesVisitFileOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_VisitorI');
        $visitor->expects($this->once())
            ->method('visitFile')
            ->with(self::isInstanceOf(PHP_Depend_Code_File::CLAZZ));

        $file = new PHP_Depend_Code_File(null);
        $file->accept($visitor);
    }

    /**
     * testMagicStringMethodReturnsEmptyStringWhenFileNameIsNull
     *
     * @return void
     */
    public function testMagicStringMethodReturnsEmptyStringWhenFileNameIsNull()
    {
        $file = new PHP_Depend_Code_File(null);
        self::assertSame('', $file->__toString());
    }

    /**
     * testMagicStringMethodReturnInjectedFileNameValue
     *
     * @return void
     */
    public function testMagicStringMethodReturnInjectedFileNameValue()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        self::assertEquals(__FILE__, $file->__toString());
    }

    /**
     * testMagicSleepMethodReturnsExpectedSetOfPropertyNames
     * 
     * @return void
     */
    public function testMagicSleepMethodReturnsExpectedSetOfPropertyNames()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        self::assertEquals(
            array(
                'cache',
                'childNodes',
                'docComment',
                'endLine',
                'fileName',
                'startLine',
                'uuid'
            ),
            $file->__sleep()
        );
    }

    /**
     * testMagicWakeupMethodInvokesSetSourceFileOnChildNodes
     *
     * @return void
     */
    public function testMagicWakeupMethodInvokesSetSourceFileOnChildNodes()
    {
        $node = $this->getMock(
            PHP_Depend_Code_Class::CLAZZ,
            array('setSourceFile'),
            array(__CLASS__)
        );
        $node->expects($this->once())
            ->method('setSourceFile')
            ->with(self::isInstanceOf(PHP_Depend_Code_File::CLAZZ));

        $file = new PHP_Depend_Code_File(__FILE__);
        $file->addChild($node);
        $file->__wakeup();
    }

    /**
     * testIsCachedReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsCachedReturnsFalseByDefault()
    {
        $file = new PHP_Depend_Code_File(null);
        self::assertFalse($file->isCached());
    }

    /**
     * testIsCachedReturnsFalseWhenObjectGetsSerialized
     *
     * @return void
     */
    public function testIsCachedReturnsFalseWhenObjectGetsSerialized()
    {
        $file = new PHP_Depend_Code_File(null);
        serialize($file);

        self::assertFalse($file->isCached());
    }

    /**
     * testIsCachedReturnsTrueAfterCallToWakeup
     *
     * @return void
     */
    public function testIsCachedReturnsTrueAfterCallToWakeup()
    {
        $file = new PHP_Depend_Code_File(null);
        $file = unserialize(serialize($file));

        self::assertTrue($file->isCached());
    }

    /**
     * testGetStartLineReturnsZeroWhenSourceFileNotExists
     *
     * @return void
     */
    public function testGetStartLineReturnsZeroWhenSourceFileNotExists()
    {
        $file = new PHP_Depend_Code_File(null);
        self::assertSame(0, $file->getStartLine());
    }

    /**
     * testGetStartLineReturnsOneWhenSourceFileExists
     *
     * @return void
     */
    public function testGetStartLineReturnsOneWhenSourceFileExists()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        self::assertEquals(1, $file->getStartLine());
    }

    /**
     * testGetEndLineReturnsZeroWhenSourceFileNotExists
     *
     * @return void
     */
    public function testGetEndLineReturnsZeroWhenSourceFileNotExists()
    {
        $file = new PHP_Depend_Code_File(null);
        self::assertSame(0, $file->getEndLine());
    }

    /**
     * testGetEndLineReturnsOneWhenSourceFileExists
     *
     * @return void
     */
    public function testGetEndLineReturnsOneWhenSourceFileExists()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        self::assertEquals(372, $file->getEndLine());
    }

    /**
     * testGetSourceReturnsNullWhenSourceFileNotExists
     *
     * @return void
     */
    public function testGetSourceReturnsNullWhenSourceFileNotExists()
    {
        $file = new PHP_Depend_Code_File(null);
        self::assertNull($file->getSource());
    }

    /**
     * Tests the {@link PHP_Depend_Code_File#getSource()} method.
     *
     * @return void
     */
    public function testGetSourceReturnsOriginalFileContents()
    {
        $file = new PHP_Depend_Code_File(self::createCodeResourceUriForTest());

        $actual   = $file->getSource();
        $expected = file_get_contents(self::createCodeResourceUriForTest());

        self::assertEquals($expected, $actual);
    }
}
