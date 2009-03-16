<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for ticket 002, PHP 5.3 namespace support.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Issues_NamespaceSupportIssue002Test extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the parser handles a simple use statement as expected.
     *
     * @return void
     */
    public function testParserHandlesSimpleUseDeclaration()
    {
        $packages = self::parseSource('issues/002-001-use-declaration.php');

        $class = $packages->current()
                          ->getClasses()
                          ->current();

        $parentClass = $class->getParentClass();
        $this->assertSame('Bar', $parentClass->getName());
        $this->assertSame('foo', $parentClass->getPackage()->getName());
    }

    /**
     * Tests that the parser handles multiple, comma separated use declarations.
     *
     * @return void
     */
    public function testParserHandlesMultipleUseDeclarations()
    {
        $packages = self::parseSource('issues/002-002-use-declaration.php');

        $class = $packages->current()
                          ->getClasses()
                          ->current();

        $parentClass = $class->getParentClass();
        $this->assertSame('FooBar', $parentClass->getName());
        $this->assertSame('foo', $parentClass->getPackage()->getName());

        $interface = $class->getInterfaces()->current();
        $this->assertSame('Bar', $interface->getName());
        $this->assertSame('foo', $interface->getPackage()->getName());
    }

    /**
     * Tests that parser handles a use declaration case insensitive.
     *
     * @return void
     */
    public function testParserHandlesUseDeclarationCaseInsensitive()
    {
        $packages = self::parseSource('issues/002-003-use-declaration.php');

        $class = $packages->current()
                          ->getClasses()
                          ->current();

        $parentClass = $class->getParentClass();
        $this->assertSame('Bar', $parentClass->getName());
        $this->assertSame('\foo\bar', $parentClass->getPackage()->getName());
    }

    /**
     * Tests that parser throws an expected exception.
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionWhenUseDeclarationContextEndsOnBackslash()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: as, line: 2, col: 19, file: '
        );

        self::parseSource('issues/002-004-use-declaration.php');
    }
}
?>
