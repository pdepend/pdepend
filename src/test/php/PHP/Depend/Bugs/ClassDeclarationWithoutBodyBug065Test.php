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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case related to bug 65.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers stdClass
 * @group pdepend
 * @group pdepend::bugs
 * @group regressiontest
 */
class PHP_Depend_Bugs_ClassDeclarationWithoutBodyBug065Test extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the parser does not end in an endless loop when it detects an
     * interface without a body.
     *
     * @return void
     */
    public function testInterfaceDeclarationWithoutBody()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Unexpected end of token stream in file: '
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects an
     * interface declaration with extend but without a body.
     *
     * @return void
     */
    public function testInterfaceDeclarationWithExtendWithoutBody()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Unexpected end of token stream in file: '
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects an
     * interface declaration with extend with invalid end of interface list.
     *
     * @return void
     */
    public function testInterfaceDeclarationWithInvalidInterfaceList()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Unexpected token: {, line: 2, col: 28, file: '
        );

        self::parseCodeResourceForTest();
    }
    
    /**
     * Tests that the parser does not end in an endless loop when it detects a
     * class declaration without a body.
     *
     * @return void
     */
    public function testClassDeclarationWithoutBody()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Unexpected end of token stream in file: '
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects a
     * class declaration with extend but without a parent class name and a body.
     *
     * @return void
     */
    public function testClassDeclarationWithExtendsWithoutClassName()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Unexpected end of token stream in file: '
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects a
     * class declaration with implements but without a interface name and a body.
     *
     * @return void
     */
    public function testClassDeclarationWithExtendsWithoutInterfaceName()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Unexpected end of token stream in file: '
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects a
     * class declaration with parent interface but without a body.
     *
     * @return void
     */
    public function testClassDeclarationWithParentInterfaceWithoutBody()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Unexpected end of token stream in file: '
        );

        self::parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects a
     * class declaration with an incomplete parent interface list and without a body.
     *
     * @return void
     */
    public function testClassDeclarationWithIncompleteParentInterfaceWithoutBody()
    {
        $this->setExpectedException(
            'RuntimeException',
            'Unexpected end of token stream in file: '
        );

        self::parseCodeResourceForTest();
    }
}
