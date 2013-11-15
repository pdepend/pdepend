<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\AST\ASTArtifactList;

use PDepend\AbstractTest;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTNamespace;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTArtifactList\PackageArtifactFilter}
 * class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\AST\ASTArtifactList\PackageArtifactFilter
 * @group unittest
 */
class PackageArtifactFilterTest extends AbstractTest
{
    /**
     * Tests that the package filter accepts valid packages.
     *
     * @return void
     */
    public function testFilterAcceptsPackage()
    {
        $filter = new PackageArtifactFilter(array(__FUNCTION__, __METHOD__));
        $this->assertTrue($filter->accept(new ASTNamespace(__CLASS__)));
    }

    /**
     * Tests that the package filter not accepts invalid packages.
     *
     * @return void
     */
    public function testFilterNotAcceptsPackage()
    {
        $filter = new PackageArtifactFilter(array(__CLASS__, __FUNCTION__));
        $this->assertFalse($filter->accept(new ASTNamespace(__CLASS__)));
    }

    /**
     * Tests that the package filter accepts and rejects the expected package.
     *
     * @return void
     */
    public function testFilterAcceptsAndNotAcceptsExpectedPackage()
    {
        $filter = new PackageArtifactFilter(array(__CLASS__));
        $this->assertFalse($filter->accept(new ASTNamespace(__CLASS__)));
        $this->assertTrue($filter->accept(new ASTNamespace(__FUNCTION__)));
    }

    /**
     * Tests that the filter accepts a class with a valid package.
     *
     * @return void
     */
    public function testFilterAcceptsClass()
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $class   = $namespace->addType(new ASTClass('Clazz'));

        $filter = new PackageArtifactFilter(array(__CLASS__));
        $this->assertTrue($filter->accept($class));
    }

    /**
     * Tests that the filter rejects a class with an invalid package.
     *
     * @return void
     */
    public function testFilterNotAcceptsClass()
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $class   = $namespace->addType(new ASTClass('Clazz'));

        $filter = new PackageArtifactFilter(array(__FUNCTION__));
        $this->assertFalse($filter->accept($class));
    }

    /**
     * Tests that the filter accepts an interface with a valid package.
     *
     * @return void
     */
    public function testFilterAcceptsInterface()
    {
        $namespace   = new ASTNamespace(__FUNCTION__);
        $interface = $namespace->addType(new ASTInterface('Iface'));

        $filter = new PackageArtifactFilter(array(__CLASS__));
        $this->assertTrue($filter->accept($interface));
    }

    /**
     * Tests that the filter not accepts an interface with an invalid package.
     *
     * @return void
     */
    public function testFilterNotAcceptsInterface()
    {
        $namespace   = new ASTNamespace(__FUNCTION__);
        $interface = $namespace->addType(new ASTInterface('Iface'));

        $filter = new PackageArtifactFilter(array(__FUNCTION__));
        $this->assertFalse($filter->accept($interface));
    }

    /**
     * Tests that the filter accepts a function with a valid package.
     *
     * @return void
     */
    public function testFilterAcceptsFunction()
    {
        $namespace  = new ASTNamespace(__FUNCTION__);
        $function = $namespace->addFunction(new ASTFunction('Func'));

        $filter = new PackageArtifactFilter(array(__CLASS__));
        $this->assertTrue($filter->accept($function));
    }

    /**
     * Tests that the filter not accepts a function with an invalid package.
     *
     * @return void
     */
    public function testFilterNotAcceptsFunction()
    {
        $namespace  = new ASTNamespace(__FUNCTION__);
        $function = $namespace->addFunction(new ASTFunction('Func'));

        $filter = new PackageArtifactFilter(array(__FUNCTION__));
        $this->assertFalse($filter->accept($function));
    }

    /**
     * Tests that the package filter works with wild cards.
     *
     * @return void
     */
    public function testFilterAcceptsPackageWithWildcard()
    {
        $pdepend = new ASTNamespace('PDepend_Code');

        $filter = new PackageArtifactFilter(array('ezc*', 'Zend_*'));
        $this->assertTrue($filter->accept($pdepend));
    }

    /**
     * Tests that the package filter rejects unmatching packages.
     *
     * @return void
     */
    public function testFilterNotAcceptsPackageWithWildcard()
    {
        $ezcGraph = new ASTNamespace('ezcGraph');

        $filter = new PackageArtifactFilter(array('ezc*', 'Zend_*'));
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
        $zendFW  = new ASTNamespace('Zend_Controller');
        $pdepend = new ASTNamespace('PDepend_Code');

        $filter = new PackageArtifactFilter(array('ezc*', 'Zend_*'));
        $this->assertFalse($filter->accept($zendFW));
        $this->assertTrue($filter->accept($pdepend));
    }
}
