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

namespace PDepend\Bugs;

/**
 * Test case for bug #98. The default package contains software artifacts like
 * functions or classes that are broken. This can result in a fatal error during
 * the analysis phase.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \stdClass
 * @group regressiontest
 */
class DefaultPackageContainsBrokenAritfactsBug098Test extends AbstractRegressionTest
{
    /**
     * Tests that the result does not contain a function with a broken signature.
     *
     * @return void
     */
    public function testDefaultPackageDoesNotContainFunctionWithBrokenSignature()
    {
        $pdepend = $this->createEngineFixture();
        $pdepend->addFile(self::createCodeResourceUriForTest());
        $pdepend->analyze();

        $functions = $pdepend->getNamespaces()
            ->current()
            ->getFunctions();

        $this->assertEquals(1, count($functions));
    }

    /**
     * Tests that the result does not contain a class with a broken method.
     *
     * @return void
     */
    public function testDefaultPackageDoesNotContainClassWithBrokenMethod()
    {
        $pdepend = $this->createEngineFixture();
        $pdepend->addFile(self::createCodeResourceUriForTest());
        $pdepend->analyze();

        $classes = $pdepend->getNamespaces()
            ->current()
            ->getClasses();

        $this->assertEquals(1, count($classes));
    }

    /**
     * Tests that the result does not contain an interface with a broken body.
     *
     * @return void
     */
    public function testDefaultPackageDoesNotContainsInterfaceWithBrokenBody()
    {
        $pdepend = $this->createEngineFixture();
        $pdepend->addFile(self::createCodeResourceUriForTest());
        $pdepend->analyze();

        $interfaces = $pdepend->getNamespaces()
            ->current()
            ->getInterfaces();

        $this->assertEquals(1, count($interfaces));
    }
}
