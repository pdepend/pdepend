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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_Filter_Package} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Code_Filter_Package
 * @group pdepend
 * @group pdepend::code
 * @group pdepend::code::filter
 * @group unittest
 */
class PHP_Depend_Code_Filter_PackageTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the package filter accepts valid packages.
     *
     * @return void
     */
    public function testFilterAcceptsPackage()
    {
        $filter = new PHP_Depend_Code_Filter_Package(array(__FUNCTION__, __METHOD__));
        $this->assertTrue($filter->accept(new PHP_Depend_Code_Package(__CLASS__)));
    }

    /**
     * Tests that the package filter not accepts invalid packages.
     *
     * @return void
     */
    public function testFilterNotAcceptsPackage()
    {
        $filter = new PHP_Depend_Code_Filter_Package(array(__CLASS__, __FUNCTION__));
        $this->assertFalse($filter->accept(new PHP_Depend_Code_Package(__CLASS__)));
    }

    /**
     * Tests that the package filter accepts and rejects the expected package.
     *
     * @return void
     */
    public function testFilterAcceptsAndNotAcceptsExpectedPackage()
    {
        $filter = new PHP_Depend_Code_Filter_Package(array(__CLASS__));
        $this->assertFalse($filter->accept(new PHP_Depend_Code_Package(__CLASS__)));
        $this->assertTrue($filter->accept(new PHP_Depend_Code_Package(__FUNCTION__)));
    }

    /**
     * Tests that the filter accepts a class with a valid package.
     *
     * @return void
     */
    public function testFilterAcceptsClass()
    {
        $package = new PHP_Depend_Code_Package(__FUNCTION__);
        $class   = $package->addType(new PHP_Depend_Code_Class('Clazz'));

        $filter = new PHP_Depend_Code_Filter_Package(array(__CLASS__));
        $this->assertTrue($filter->accept($class));
    }

    /**
     * Tests that the filter rejects a class with an invalid package.
     *
     * @return void
     */
    public function testFilterNotAcceptsClass()
    {
        $package = new PHP_Depend_Code_Package(__FUNCTION__);
        $class   = $package->addType(new PHP_Depend_Code_Class('Clazz'));

        $filter = new PHP_Depend_Code_Filter_Package(array(__FUNCTION__));
        $this->assertFalse($filter->accept($class));
    }

    /**
     * Tests that the filter accepts an interface with a valid package.
     *
     * @return void
     */
    public function testFilterAcceptsInterface()
    {
        $package   = new PHP_Depend_Code_Package(__FUNCTION__);
        $interface = $package->addType(new PHP_Depend_Code_Interface('Iface'));

        $filter = new PHP_Depend_Code_Filter_Package(array(__CLASS__));
        $this->assertTrue($filter->accept($interface));
    }

    /**
     * Tests that the filter not accepts an interface with an invalid package.
     *
     * @return void
     */
    public function testFilterNotAcceptsInterface()
    {
        $package   = new PHP_Depend_Code_Package(__FUNCTION__);
        $interface = $package->addType(new PHP_Depend_Code_Interface('Iface'));

        $filter = new PHP_Depend_Code_Filter_Package(array(__FUNCTION__));
        $this->assertFalse($filter->accept($interface));
    }

    /**
     * Tests that the filter accepts a function with a valid package.
     *
     * @return void
     */
    public function testFilterAcceptsFunction()
    {
        $package  = new PHP_Depend_Code_Package(__FUNCTION__);
        $function = $package->addFunction(new PHP_Depend_Code_Function('Func'));

        $filter = new PHP_Depend_Code_Filter_Package(array(__CLASS__));
        $this->assertTrue($filter->accept($function));
    }

    /**
     * Tests that the filter not accepts a function with an invalid package.
     *
     * @return void
     */
    public function testFilterNotAcceptsFunction()
    {
        $package  = new PHP_Depend_Code_Package(__FUNCTION__);
        $function = $package->addFunction(new PHP_Depend_Code_Function('Func'));

        $filter = new PHP_Depend_Code_Filter_Package(array(__FUNCTION__));
        $this->assertFalse($filter->accept($function));
    }

    /**
     * Tests that the package filter works with wild cards.
     *
     * @return void
     */
    public function testFilterAcceptsPackageWithWildcard()
    {
        $pdepend = new PHP_Depend_Code_Package('PHP_Depend_Code');

        $filter = new PHP_Depend_Code_Filter_Package(array('ezc*', 'Zend_*'));
        $this->assertTrue($filter->accept($pdepend));
    }

    /**
     * Tests that the package filter rejects unmatching packages.
     *
     * @return void
     */
    public function testFilterNotAcceptsPackageWithWildcard()
    {
        $ezcGraph = new PHP_Depend_Code_Package('ezcGraph');

        $filter = new PHP_Depend_Code_Filter_Package(array('ezc*', 'Zend_*'));
        $this->assertFalse($filter->accept($ezcGraph));
    }

    /**
     * Tests that the package filter selects the accepts and rejects the expected
     * packages.
     *
     * @return void
     */
    public function testFilterAcceptsAndNotAcceptsPackageWithWildcard()
    {
        $zendFW  = new PHP_Depend_Code_Package('Zend_Controller');
        $pdepend = new PHP_Depend_Code_Package('PHP_Depend_Code');

        $filter = new PHP_Depend_Code_Filter_Package(array('ezc*', 'Zend_*'));
        $this->assertFalse($filter->accept($zendFW));
        $this->assertTrue($filter->accept($pdepend));
    }
}
