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

namespace PDepend\Source\AST;

use PDepend\AbstractTest;

/**
 * Test case for the code parameter class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\AST\ASTParameter
 * @group unittest
 */
class ASTParameterTest extends AbstractTest
{
    /**
     * testGetIdReturnsExpectedObjectHash
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetIdReturnsExpectedObjectHash()
    {
        $parameters = $this->_getFirstMethodInClass()->getParameters();
        $this->assertEquals(spl_object_hash($parameters[0]), $parameters[0]->getId());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for a simple parameter.
     *
     * @return void
     */
    public function testParameterAllowsNullForSimpleVariableIssue67()
    {
        $parameters = $this->_getFirstMethodInClass()->getParameters();
        $this->assertTrue($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for a simple
     * parameter passed by reference.
     *
     * @return void
     */
    public function testParameterAllowsNullForSimpleVariablePassedByReferenceIssue67()
    {
        $parameters = $this->_getFirstMethodInClass()->getParameters();
        $this->assertTrue($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>false</b> for an array
     * parameter without explicit <b>null</b> default value.
     *
     * @return void
     */
    public function testParameterNotAllowsNullForArrayHintVariableIssue67()
    {
        $parameters = $this->_getFirstMethodInClass()->getParameters();
        $this->assertFalse($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for an array
     * parameter with explicit <b>null</b> default value.
     *
     * @return void
     */
    public function testParameterAllowsNullForArrayHintVariableIssue67()
    {
        $parameters = $this->_getFirstMethodInClass()->getParameters();
        $this->assertTrue($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>false</b> for a typed
     * parameter without explicit <b>null</b> default value.
     *
     * @return void
     */
    public function testParameterNotAllowsNullForTypeHintVariableIssue67()
    {
        $parameters = $this->_getFirstMethodInClass()->getParameters();
        $this->assertFalse($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for a type
     * parameter with explicit <b>null</b> default value.
     *
     * @return void
     */
    public function testParameterAllowsNullForTypeHintVariableIssue67()
    {
        $parameter = $this->_getFirstMethodInClass()->getParameters();
        $this->assertTrue($parameter[0]->allowsNull());
    }

    /**
     * Tests that the getDeclaringClass() method returns <b>null</b> for a
     * function.
     *
     * @return void
     */
    public function testParameterDeclaringClassReturnsNullForFunctionIssue67()
    {
        $parameter = $this->getFirstFunctionForTestCase()->getParameters();
        $this->assertNull($parameter[0]->getDeclaringClass());
    }

    /**
     * Tests that the getDeclaringClass() method returns the declaring class
     * of a parent function/method.
     *
     * @return void
     */
    public function testParameterDeclaringClassReturnsExpectedInstanceForMethodIssue67()
    {
        $class = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        $parameters = $class->getMethods()
            ->current()
            ->getParameters();

        $this->assertSame($class, $parameters[0]->getDeclaringClass());
    }

    /**
     * Tests that the parameter class handles a type holder as expected.
     *
     * @return void
     */
    public function testParameterReturnsExpectedTypeFromASTClassOrInterfaceReference()
    {
        $class = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();

        $parameters = $class->getMethods()
            ->current()
            ->getParameters();

        $this->assertSame($class, $parameters[0]->getClass());
    }

    /**
     * Tests that a parameter returns <b>null</b> when no type holder was set.
     *
     * @return void
     */
    public function testParameterReturnNullForTypeWhenNoASTClassOrInterfaceReferenceWasSet()
    {
        $parameters = $this->_getFirstMethodInClass()->getParameters();
        $this->assertNull($parameters[0]->getClass());
    }

    /**
     * Tests that a parameter returns the expected function instance.
     *
     * @return void
     */
    public function testParameterReturnsExpectedDeclaringFunction()
    {
        $function   = $this->getFirstFunctionForTestCase();
        $parameters = $function->getParameters();
        $this->assertSame($function, $parameters[0]->getDeclaringFunction());
    }

    /**
     * Tests that a parameter returns the expected method instance.
     *
     * @return void
     */
    public function testParameterReturnsExpectedDeclaringMethod()
    {
        $method     = $this->_getFirstMethodInClass();
        $parameters = $method->getParameters();
        $this->assertSame($method, $parameters[0]->getDeclaringFunction());
    }

    /**
     * Returns the first class method found in the test file associated with the
     * calling test method.
     *
     * @return \PDepend\Source\AST\ASTMethod
     * @since 1.0.0
     */
    private function _getFirstMethodInClass()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();
    }

    /**
     * testAcceptInvokesVisitParameterOnSuppliedVisitor
     *
     * @return void
     */
    public function testAcceptInvokesVisitParameterOnSuppliedVisitor()
    {
        $visitor = $this->getMock('\\PDepend\\Source\\ASTVisitor\\ASTVisitor');
        $visitor->expects($this->once())
            ->method('visitParameter')
            ->with($this->isInstanceOf('\\PDepend\\Source\\AST\\ASTParameter'));

        $parameter = new ASTParameter($this->getMock('PDepend\\Source\\AST\\ASTFormalParameter'));
        $parameter->accept($visitor);
    }
}
