<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
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
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Code_ParameterTest extends PHP_Depend_AbstractTest
{

    /**
     * testFreeResetsDeclaringFunctionToNull
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsDeclaringFunctionToNull()
    {
        $packages  = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');
        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $parameter->free();

        $this->assertNull($parameter->getDeclaringFunction());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for a simple parameter.
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterAllowsNullForSimpleVariableIssue67()
    {
        $packages  = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');
        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $this->assertTrue($parameter->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for a simple
     * parameter passed by reference.
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterAllowsNullForSimpleVariablePassedByReferenceIssue67()
    {
        $packages  = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');
        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $this->assertTrue($parameter->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>false</b> for an array
     * parameter without explicit <b>null</b> default value.
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterNotAllowsNullForArrayHintVariableIssue67()
    {
        $packages  = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');
        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $this->assertFalse($parameter->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for an array
     * parameter with explicit <b>null</b> default value.
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterAllowsNullForArrayHintVariableIssue67()
    {
        $packages  = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');
        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $this->assertTrue($parameter->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>false</b> for a typed
     * parameter without explicit <b>null</b> default value.
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterNotAllowsNullForTypeHintVariableIssue67()
    {
        $packages  = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');
        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $this->assertFalse($parameter->allowsNull());
    }

    /**
     * Tests that the allows null method returns <b>true</b> for a type
     * parameter with explicit <b>null</b> default value.
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterAllowsNullForTypeHintVariableIssue67()
    {
        $packages  = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');
        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $this->assertTrue($parameter->allowsNull());
    }

    /**
     * Tests that the getDeclaringClass() method returns <b>null</b> for a
     * function.
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterDeclaringClassReturnsNullForFunctionIssue67()
    {
        $packages  = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');
        $parameter = $packages->current()
            ->getFunctions()
            ->current()
            ->getParameters()
            ->current();

        $this->assertNull($parameter->getDeclaringClass());
    }

    /**
     * Tests that the getDeclaringClass() method returns the declaring class
     * of a parent function/method.
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterDeclaringClassReturnsExpectedInstanceForMethodIssue67()
    {
        $packages  = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');

        $class = $packages->current()
            ->getClasses()
            ->current();

        $parameter = $class->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $this->assertSame($class, $parameter->getDeclaringClass());
    }

    /**
     * Tests that the parameter class handles a type holder as expected.
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterReturnsExpectedTypeFromASTClassOrInterfaceReference()
    {
        $packages  = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');

        $class = $packages->current()
            ->getClasses()
            ->current();

        $parameter = $class->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $this->assertSame($class, $parameter->getClass());
    }

    /**
     * Tests that a parameter returns <b>null</b> when no type holder was set.
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterReturnNullForTypeWhenNoASTClassOrInterfaceReferenceWasSet()
    {
        $packages  = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');
        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters()
            ->current();
            
        $this->assertNull($parameter->getClass());
    }

    /**
     * Tests that a parameter returns the expected function instance.
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterReturnsExpectedDeclaringFunction()
    {
        $packages = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $function = $package->getFunctions()
            ->current();

        $parameter = $function->getParameters()
            ->current();

        $this->assertSame($function, $parameter->getDeclaringFunction());
    }

    /**
     * Tests that a parameter returns the expected method instance.
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterReturnsExpectedDeclaringMethod()
    {
        $packages = self::parseSource('code/parameter/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $method = $package->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $parameter = $method->getParameters()
            ->current();

        $this->assertSame($method, $parameter->getDeclaringFunction());
    }

    /**
     * testAcceptInvokesVisitParameterOnSuppliedVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_Parameter
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
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterExportThrowsReflectionExceptionForUnknownFunction()
    {
        $this->assertFalse(function_exists(__FUNCTION__));

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
     * @covers PHP_Depend_Code_Parameter
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testParameterExportsExistingFunction()
    {
        $this->assertFalse(function_exists(__FUNCTION__));

        function testParameterExportsExistingFunction($foo) {}

        $this->assertSame(
            'Parameter #0 [ <required> $foo ]',
            PHP_Depend_Code_Parameter::export(__FUNCTION__, 'foo', true)
        );
    }
}