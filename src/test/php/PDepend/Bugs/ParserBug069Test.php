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
 * Test case for bug #69.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @ticket 69
 * @covers \stdClass
 * @group regressiontest
 */
class ParserBug069Test extends AbstractRegressionTest
{
    /**
     * Tests that parser handles a php 5.3 static method call correct.
     *
     * <code>
     * PDepend\Parser::call();
     * </code>
     *
     * @return void
     */
    public function testStaticMethodCallInFunctionBody()
    {
        $namespace = $this->getFirstFunctionForTestCase()
            ->getDependencies()
            ->current()
            ->getNamespace();

        $this->assertEquals('PDepend', $namespace->getName());
    }

    /**
     * Tests that parser handles a php 5.3 static method call correct.
     *
     * <code>
     * \PDepend\Parser::call();
     * </code>
     *
     * @return void
     */
    public function testStaticMethodLeadingBackslashCallInFunctionBody()
    {
        $namespace = $this->getFirstFunctionForTestCase()
            ->getDependencies()
            ->current()
            ->getNamespace();

        $this->assertEquals('PDepend', $namespace->getName());
    }

    /**
     * Tests that parser does not handle a php 5.3 function call as dependency.
     *
     * <code>
     * \PDepend\Parser\call();
     * </code>
     *
     * @return void
     */
    public function testNotHandlesQualifiedFunctionCallAsDependencyInFunctionBody()
    {
        $function = self::parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $this->assertSame(0, $function->getDependencies()->count());
    }

    /**
     * Tests that parser handles a php 5.3 property access as dependency.
     *
     * <code>
     * \PDepend\Parser::$prop;
     * </code>
     *
     * @return void
     */
    public function testQualifiedPropertyAccessAsDependencyInFunctionBody()
    {
        $namespace = $this->getFirstFunctionForTestCase()
            ->getDependencies()
            ->current()
            ->getNamespace();

        $this->assertEquals('PDepend', $namespace->getName());
    }

    /**
     * Tests that parser handles a php 5.3 constant access as dependency.
     *
     * <code>
     * \PDepend\Parser::CONSTANT;
     * </code>
     *
     * @return void
     */
    public function testQualifiedConstantAccessAsDependencyInFunctionBody()
    {
        $namespace = $this->getFirstFunctionForTestCase()
            ->getDependencies()
            ->current()
            ->getNamespace();

        $this->assertEquals('PDepend', $namespace->getName());
    }
}
