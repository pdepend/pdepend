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

namespace PDepend\Source\AST\ASTArtifactList;

use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTNamespace;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTArtifactList\PackageArtifactFilter}
 * class.
 *
 * @covers \PDepend\Source\AST\ASTArtifactList\PackageArtifactFilter
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class PackageArtifactFilterTest extends AbstractTestCase
{
    /**
     * Tests that the package filter accepts valid packages.
     */
    public function testFilterAcceptsPackage(): void
    {
        $filter = new PackageArtifactFilter([__FUNCTION__, __METHOD__]);
        static::assertTrue($filter->accept(new ASTNamespace(__CLASS__)));
    }

    /**
     * Tests that the package filter not accepts invalid packages.
     */
    public function testFilterNotAcceptsPackage(): void
    {
        $filter = new PackageArtifactFilter([__CLASS__, __FUNCTION__]);
        static::assertFalse($filter->accept(new ASTNamespace(__CLASS__)));
    }

    /**
     * Tests that the package filter accepts and rejects the expected package.
     */
    public function testFilterAcceptsAndNotAcceptsExpectedPackage(): void
    {
        $filter = new PackageArtifactFilter([__CLASS__]);
        static::assertFalse($filter->accept(new ASTNamespace(__CLASS__)));
        static::assertTrue($filter->accept(new ASTNamespace(__FUNCTION__)));
    }

    /**
     * Tests that the filter accepts a class with a valid package.
     */
    public function testFilterAcceptsClass(): void
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $class = $namespace->addType(new ASTClass('Clazz'));

        $filter = new PackageArtifactFilter([__CLASS__]);
        static::assertTrue($filter->accept($class));
    }

    /**
     * Tests that the filter rejects a class with an invalid package.
     */
    public function testFilterNotAcceptsClass(): void
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $class = $namespace->addType(new ASTClass('Clazz'));

        $filter = new PackageArtifactFilter([__FUNCTION__]);
        static::assertFalse($filter->accept($class));
    }

    /**
     * Tests that the filter accepts an interface with a valid package.
     */
    public function testFilterAcceptsInterface(): void
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $interface = $namespace->addType(new ASTInterface('Iface'));

        $filter = new PackageArtifactFilter([__CLASS__]);
        static::assertTrue($filter->accept($interface));
    }

    /**
     * Tests that the filter not accepts an interface with an invalid package.
     */
    public function testFilterNotAcceptsInterface(): void
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $interface = $namespace->addType(new ASTInterface('Iface'));

        $filter = new PackageArtifactFilter([__FUNCTION__]);
        static::assertFalse($filter->accept($interface));
    }

    /**
     * Tests that the filter accepts a function with a valid package.
     */
    public function testFilterAcceptsFunction(): void
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $function = $namespace->addFunction(new ASTFunction('Func'));

        $filter = new PackageArtifactFilter([__CLASS__]);
        static::assertTrue($filter->accept($function));
    }

    /**
     * Tests that the filter not accepts a function with an invalid package.
     */
    public function testFilterNotAcceptsFunction(): void
    {
        $namespace = new ASTNamespace(__FUNCTION__);
        $function = $namespace->addFunction(new ASTFunction('Func'));

        $filter = new PackageArtifactFilter([__FUNCTION__]);
        static::assertFalse($filter->accept($function));
    }

    /**
     * Tests that the package filter works with wild cards.
     */
    public function testFilterAcceptsPackageWithWildcard(): void
    {
        $pdepend = new ASTNamespace('PDepend_Code');

        $filter = new PackageArtifactFilter(['ezc*', 'Zend_*']);
        static::assertTrue($filter->accept($pdepend));
    }

    /**
     * Tests that the package filter rejects unmatching packages.
     */
    public function testFilterNotAcceptsPackageWithWildcard(): void
    {
        $ezcGraph = new ASTNamespace('ezcGraph');

        $filter = new PackageArtifactFilter(['ezc*', 'Zend_*']);
        static::assertFalse($filter->accept($ezcGraph));
    }

    /**
     * Tests that the package filter selects the accepts and rejects the expected
     * packages.
     */
    public function testFilterAcceptsAndNotAcceptsPackageWithWildcard(): void
    {
        $zendFW = new ASTNamespace('Zend_Controller');
        $pdepend = new ASTNamespace('PDepend_Code');

        $filter = new PackageArtifactFilter(['ezc*', 'Zend_*']);
        static::assertFalse($filter->accept($zendFW));
        static::assertTrue($filter->accept($pdepend));
    }
}
