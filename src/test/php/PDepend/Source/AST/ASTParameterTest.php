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

namespace PDepend\Source\AST;

use PDepend\AbstractTestCase;

/**
 * Test case for the code parameter class.
 *
 * @covers \PDepend\Source\AST\ASTParameter
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTParameterTest extends AbstractTestCase
{
    /**
     * testGetIdReturnsExpectedObjectHash
     *
     * @since 1.0.0
     */
    public function testGetIdReturnsExpectedObjectHash(): void
    {
        $parameters = $this->getFirstMethodInClass()->getParameters();
        static::assertEquals(spl_object_hash($parameters[0]), $parameters[0]->getId());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for a simple parameter.
     */
    public function testParameterAllowsNullForSimpleVariableIssue67(): void
    {
        $parameters = $this->getFirstMethodInClass()->getParameters();
        static::assertTrue($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for a simple
     * parameter passed by reference.
     */
    public function testParameterAllowsNullForSimpleVariablePassedByReferenceIssue67(): void
    {
        $parameters = $this->getFirstMethodInClass()->getParameters();
        static::assertTrue($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>false</b> for an array
     * parameter without explicit <b>null</b> default value.
     */
    public function testParameterNotAllowsNullForArrayHintVariableIssue67(): void
    {
        $parameters = $this->getFirstMethodInClass()->getParameters();
        static::assertFalse($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for an array
     * parameter with explicit <b>null</b> default value.
     */
    public function testParameterAllowsNullForArrayHintVariableIssue67(): void
    {
        $parameters = $this->getFirstMethodInClass()->getParameters();
        static::assertTrue($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>false</b> for a typed
     * parameter without explicit <b>null</b> default value.
     */
    public function testParameterNotAllowsNullForTypeHintVariableIssue67(): void
    {
        $parameters = $this->getFirstMethodInClass()->getParameters();
        static::assertFalse($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for a type
     * parameter with explicit <b>null</b> default value.
     */
    public function testParameterAllowsNullForTypeHintVariableIssue67(): void
    {
        $parameter = $this->getFirstMethodInClass()->getParameters();
        static::assertTrue($parameter[0]->allowsNull());
    }

    /**
     * Tests that the getDeclaringClass() method returns <b>null</b> for a
     * function.
     */
    public function testParameterDeclaringClassReturnsNullForFunctionIssue67(): void
    {
        $parameter = $this->getFirstFunctionForTestCase()->getParameters();
        static::assertNull($parameter[0]->getDeclaringClass());
    }

    /**
     * Tests that the getDeclaringClass() method returns the declaring class
     * of a parent function/method.
     */
    public function testParameterDeclaringClassReturnsExpectedInstanceForMethodIssue67(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        $parameters = $class->getMethods()
            ->current()
            ->getParameters();

        static::assertSame($class, $parameters[0]->getDeclaringClass());
    }

    /**
     * Tests that the parameter class handles a type holder as expected.
     */
    public function testParameterReturnsExpectedTypeFromASTClassOrInterfaceReference(): void
    {
        $class = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        $parameters = $class->getMethods()
            ->current()
            ->getParameters();

        static::assertSame($class, $parameters[0]->getClass());
    }

    /**
     * Tests that a parameter returns <b>null</b> when no type holder was set.
     */
    public function testParameterReturnNullForTypeWhenNoASTClassOrInterfaceReferenceWasSet(): void
    {
        $parameters = $this->getFirstMethodInClass()->getParameters();
        static::assertNull($parameters[0]->getClass());
    }

    /**
     * Tests that a parameter returns the expected function instance.
     */
    public function testParameterReturnsExpectedDeclaringFunction(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $parameters = $function->getParameters();
        static::assertSame($function, $parameters[0]->getDeclaringFunction());
    }

    /**
     * Tests that a parameter returns the expected method instance.
     */
    public function testParameterReturnsExpectedDeclaringMethod(): void
    {
        $method = $this->getFirstMethodInClass();
        $parameters = $method->getParameters();
        static::assertSame($method, $parameters[0]->getDeclaringFunction());
    }

    /**
     * Returns the first class method found in the test file associated with the
     * calling test method.
     *
     * @since 1.0.0
     */
    private function getFirstMethodInClass(): ASTMethod
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();
    }
}
