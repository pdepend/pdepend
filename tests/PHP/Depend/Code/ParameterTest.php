<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/ASTClassOrInterfaceReference.php';
require_once 'PHP/Depend/Code/ASTFormalParameter.php';
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/Parameter.php';
require_once 'PHP/Depend/Code/Value.php';

/**
 * Test case for the code parameter class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Code_Parameter
 */
class PHP_Depend_Code_ParameterTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the allows null method returns <b>true</b> for a simple parameter.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterAllowsNullForSimpleVariableIssue67()
    {
        $parameters = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();

        self::assertTrue($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for a simple
     * parameter passed by reference.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterAllowsNullForSimpleVariablePassedByReferenceIssue67()
    {
        $parameters = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();

        self::assertTrue($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>false</b> for an array
     * parameter without explicit <b>null</b> default value.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterNotAllowsNullForArrayHintVariableIssue67()
    {
        $parameters = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();

        self::assertFalse($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for an array
     * parameter with explicit <b>null</b> default value.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterAllowsNullForArrayHintVariableIssue67()
    {
        $parameters = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();

        self::assertTrue($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>false</b> for a typed
     * parameter without explicit <b>null</b> default value.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterNotAllowsNullForTypeHintVariableIssue67()
    {
        $parameters = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();

        self::assertFalse($parameters[0]->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for a type
     * parameter with explicit <b>null</b> default value.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterAllowsNullForTypeHintVariableIssue67()
    {
        $packages  = self::parseCodeResourceForTest();
        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();

        self::assertTrue($parameter[0]->allowsNull());
    }

    /**
     * Tests that the getDeclaringClass() method returns <b>null</b> for a
     * function.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterDeclaringClassReturnsNullForFunctionIssue67()
    {
        $packages  = self::parseCodeResourceForTest();
        $parameter = $packages->current()
            ->getFunctions()
            ->current()
            ->getParameters();

        self::assertNull($parameter[0]->getDeclaringClass());
    }

    /**
     * Tests that the getDeclaringClass() method returns the declaring class
     * of a parent function/method.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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

        self::assertSame($class, $parameters[0]->getDeclaringClass());
    }

    /**
     * Tests that the parameter class handles a type holder as expected.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
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

        self::assertSame($class, $parameters[0]->getClass());
    }

    /**
     * Tests that a parameter returns <b>null</b> when no type holder was set.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterReturnNullForTypeWhenNoASTClassOrInterfaceReferenceWasSet()
    {
        $parameters = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();
            
        self::assertNull($parameters[0]->getClass());
    }

    /**
     * Tests that a parameter returns the expected function instance.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterReturnsExpectedDeclaringFunction()
    {
        $function = self::parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        $parameters = $function->getParameters();
        self::assertSame($function, $parameters[0]->getDeclaringFunction());
    }

    /**
     * Tests that a parameter returns the expected method instance.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterReturnsExpectedDeclaringMethod()
    {
        $method = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $parameters = $method->getParameters();
        self::assertSame($method, $parameters[0]->getDeclaringFunction());
    }

    /**
     * testAcceptInvokesVisitParameterOnSuppliedVisitor
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testAcceptInvokesVisitParameterOnSuppliedVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_VisitorI');
        $visitor->expects($this->once())
            ->method('visitParameter')
            ->with($this->isInstanceOf('PHP_Depend_Code_Parameter'));

        $parameter = new PHP_Depend_Code_Parameter($this->getMock('PHP_Depend_Code_ASTFormalParameter'));
        $parameter->accept($visitor);
    }

    /**
     * Tests that the export function throws the expected exception for an unknown
     * function.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterExportThrowsReflectionExceptionForUnknownFunction()
    {
        self::assertFalse(function_exists(__FUNCTION__));

        $this->setExpectedException(
            'ReflectionException',
            'PHP_Depend_Code_Parameter::export() is not supported.'
        );

        PHP_Depend_Code_Parameter::export(__FUNCTION__, 'foo', true);
    }

    /**
     * Tests that export creates the expected string representation of a
     * function parameter.
     *
     * @return void
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterExportsExistingFunction()
    {
        self::assertFalse(function_exists(__FUNCTION__));

        function testParameterExportsExistingFunction($foo) {}

        self::assertSame(
            'Parameter #0 [ <required> $foo ]',
            PHP_Depend_Code_Parameter::export(__FUNCTION__, 'foo', true)
        );
    }
}