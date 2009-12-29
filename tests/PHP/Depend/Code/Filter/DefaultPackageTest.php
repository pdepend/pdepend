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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/BuilderI.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Filter/DefaultPackage.php';

/**
 * Test case for the default package filter.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Code_Filter_DefaultPackageTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the filter accepts a none default package.
     *
     * @return void
     * @covers PHP_Depend_Code_Filter_DefaultPackage
     * @group pdepend
     * @group pdepend::code
     * @group pdepend::code::filter
     * @group unittest
     */
    public function testFilterAcceptsNoneDefaultPackage()
    {
        $accept = new PHP_Depend_Code_Package(__CLASS__);

        $filter = new PHP_Depend_Code_Filter_DefaultPackage();
        $this->assertTrue($filter->accept($accept));
    }

    /**
     * Tests that the filter rejects default package.
     *
     * @return void
     * @covers PHP_Depend_Code_Filter_DefaultPackage
     * @group pdepend
     * @group pdepend::code
     * @group pdepend::code::filter
     * @group unittest
     */
    public function testFilterNotAcceptsDefaultPackage()
    {
        $reject = new PHP_Depend_Code_Package(PHP_Depend_BuilderI::DEFAULT_PACKAGE);

        $filter = new PHP_Depend_Code_Filter_DefaultPackage();
        $this->assertFalse($filter->accept($reject));
    }

    /**
     * Tests that the filter rejects default package.
     *
     * @return void
     * @covers PHP_Depend_Code_Filter_DefaultPackage
     * @group pdepend
     * @group pdepend::code
     * @group pdepend::code::filter
     * @group unittest
     */
    public function testFilterAcceptsAndNotAcceptsExpectedPackage()
    {
        $accept = new PHP_Depend_Code_Package(__FUNCTION__);
        $reject = new PHP_Depend_Code_Package(PHP_Depend_BuilderI::DEFAULT_PACKAGE);

        $filter = new PHP_Depend_Code_Filter_DefaultPackage();
        $this->assertFalse($filter->accept($reject));
        $this->assertTrue($filter->accept($accept));
    }
}