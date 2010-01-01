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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the Reflection API compatibility ticket #67.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Issues_ReflectionCompatibilityIssue67Test extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the parser sets the parameter flag by reference.
     *
     * @return void
     */
    public function testParserSetsFunctionParameterByReference()
    {
        $packages = self::parseSource('issues/067-001-parameter-by-reference.php');

        $package    = $packages->current();
        $functions  = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $parameter = $parameters->current();
        $this->assertSame('$foo', $parameter->getName());
        $this->assertTrue($parameter->isPassedByReference());
    }

    /**
     * Tests that the parser sets the parameter flag by reference.
     *
     * @return void
     */
    public function testParserSetsMultipleFunctionParameterByReference()
    {
        $packages = self::parseSource('issues/067-002-parameter-by-reference.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $expected = array('$foo' => true, '$bar' => false, '$foobar' => true);
        foreach ($parameters as $parameter) {
            $this->assertSame($expected[$parameter->getName()], $parameter->isPassedByReference());
        }
    }

    /**
     * Tests that the parser sets the parameter flag by reference.
     *
     * @return void
     */
    public function testParserSetsFunctionParameterByReferenceWithTypeHint()
    {
        $packages = self::parseSource('issues/067-003-parameter-by-reference.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $parameter = $parameters->current();
        $this->assertSame('$foo', $parameter->getName());
        $this->assertTrue($parameter->isPassedByReference());
    }

    /**
     * Tests that the parser sets the parameter flag by reference.
     *
     * @return void
     */
    public function testParserSetsMultipleFunctionParameterByReferenceWithTypeHint()
    {
        $packages = self::parseSource('issues/067-004-parameter-by-reference.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $expected = array('$foo' => true, '$bar' => true);
        foreach ($parameters as $parameter) {
            $this->assertSame($expected[$parameter->getName()], $parameter->isPassedByReference());
        }
    }

    /**
     * Tests that the parser sets the is array flag when the parameter contains
     * the array type hint.
     *
     * @return void
     */
    public function testParserSetsParameterArrayFlag()
    {
        $packages = self::parseSource('issues/067-005-parameter-type-hint-array.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertTrue($parameters->current()->isArray());
    }

    /**
     * Tests that the parser does not set the array flag when the parameter is
     * scalar without type hint.
     *
     * @return void
     */
    public function testParserDoesNotSetParameterArrayFlagForScalar()
    {
        $packages = self::parseSource('issues/067-006-parameter-type-hint-array.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertFalse($parameters->current()->isArray());
    }

    /**
     * Tests that the parser does not set the array flag when the parameter has
     * a class type hint.
     *
     * @return void
     */
    public function testParserDoesNotSetParameterArrayFlagForType()
    {
        $packages = self::parseSource('issues/067-007-parameter-type-hint-array.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertFalse($parameters->current()->isArray());
    }

    /**
     * Tests that the boolean flag has default value is <b>false</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterWithoutDefaultValue()
    {
        $packages = self::parseSource('issues/067-008-parameter-default-value-none.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertFalse($parameters->current()->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is <b>null</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueNull()
    {
        $packages = self::parseSource('issues/067-009-parameter-default-value-null.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertTrue($parameters->current()->isDefaultValueAvailable());
        $this->assertNull($parameters->current()->getDefaultValue());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is <b>false</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueBooleanFalse()
    {
        $packages = self::parseSource('issues/067-011-parameter-default-value-false.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertTrue($parameters->current()->isDefaultValueAvailable());
        $this->assertFalse($parameters->current()->getDefaultValue());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is also <b>true</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueBooleanTrue()
    {
        $packages = self::parseSource('issues/067/' . __FUNCTION__ . '.php');
        $package   = $packages->current();

        $parameter = $package->getFunctions()
            ->current()
            ->getParameters()
            ->current();

        $this->assertTrue($parameter->isDefaultValueAvailable());
        $this->assertTrue($parameter->getDefaultValue());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is a <b>float</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueFloat()
    {
        $packages = self::parseSource('issues/067-012-parameter-default-value-float.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertTrue($parameters->current()->isDefaultValueAvailable());
        $this->assertType('float', $parameters->current()->getDefaultValue());
        $this->assertEquals(42.23, $parameters->current()->getDefaultValue(), null, 0.001);
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is an <b>integer</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueInteger()
    {
        $packages = self::parseSource('issues/067-013-parameter-default-value-integer.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertTrue($parameters->current()->isDefaultValueAvailable());
        $this->assertSame(42, $parameters->current()->getDefaultValue());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is a <b>string</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueString()
    {
        $packages = self::parseSource('issues/067-014-parameter-default-value-string.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertTrue($parameters->current()->isDefaultValueAvailable());
        $this->assertSame('foo bar 42', $parameters->current()->getDefaultValue());
    }

    /**
     * Tests that the parameter returns the expected result for a parameter
     * without default value.
     *
     * @@return void
     */
    public function testParserHandlesParameterWithoutDefaultValueReturnsNull()
    {
        $packages = self::parseSource('issues/067/' . __FUNCTION__ . '.php');
        $package  = $packages->current();

        $parameter = $package->getFunctions()
            ->current()
            ->getParameters()
            ->current();

        $this->assertFalse($parameter->isDefaultValueAvailable());
        $this->assertNull($parameter->getDefaultValue());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is an <b>array</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueArray()
    {
        $packages = self::parseSource('issues/067-015-parameter-default-value-array.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertTrue($parameters->current()->isDefaultValueAvailable());
        $this->assertSame(array(), $parameters->current()->getDefaultValue());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is <b>null</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueConstant()
    {
        $packages = self::parseSource('issues/067-016-parameter-default-value-constant.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertTrue($parameters->current()->isDefaultValueAvailable());
        $this->assertSame(null, $parameters->current()->getDefaultValue());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is <b>null</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueClassConstant()
    {
        $packages = self::parseSource('issues/067-017-parameter-default-value-class-constant.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertTrue($parameters->current()->isDefaultValueAvailable());
        $this->assertSame(null, $parameters->current()->getDefaultValue());
    }

    /**
     * Tests that the boolean flag for optional parameters is set to <b>false</b>
     * for all parameters.
     *
     * @return void
     */
    public function testParserHandlesParameterOptionalIsFalseForAllParameters()
    {
        $packages = self::parseSource('issues/067-018-parameter-optional.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $expected = array(false, false, false);

        foreach ($parameters as $parameter) {
            $this->assertSame($expected[$parameter->getPosition()], $parameter->isOptional());
        }
    }

    /**
     * Tests that the boolean flag for optional parameters is set to <b>false</b>
     * for all parameters.
     *
     * @return void
     */
    public function testParserHandlesParameterOptionalIsFalseForAllParametersEvenADefaultValueExists()
    {
        $packages = self::parseSource('issues/067-019-parameter-optional.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $expected = array(false, false, false);
        foreach ($parameters as $parameter) {
            $this->assertSame($expected[$parameter->getPosition()], $parameter->isOptional());
        }
    }

    /**
     * Tests that the boolean flag for optional parameters is set to <b>false</b>
     * for the first two parameters.
     *
     * @return void
     */
    public function testParserHandlesParameterOptionalIsFalseForFirstTwoParameters()
    {
        $packages = self::parseSource('issues/067-020-parameter-optional.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $expected = array(false, false, true);
        foreach ($parameters as $parameter) {
            $this->assertSame($expected[$parameter->getPosition()], $parameter->isOptional());
        }
    }

    /**
     * Tests that the boolean flag for optional parameters is set to <b>true</b>
     * for all parameters.
     *
     * @return void
     */
    public function testParserHandlesParameterOptionalIsTrueForAllParameters()
    {
        $packages = self::parseSource('issues/067-021-parameter-optional.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $expected = array(true, true);
        foreach ($parameters as $parameter) {
            $this->assertSame($expected[$parameter->getPosition()], $parameter->isOptional());
        }
    }

    /**
     * Tests that the parser sets the user-defined flag for an analyzed class.
     *
     * @return void
     */
    public function testParserSetsUserDefinedFlagForClass()
    {
        $packages = self::parseSource('issues/067-031-user-defined-class.php');

        $package = $packages->current();
        $classes = $package->getClasses();
        $class   = $classes->current();

        $this->assertTrue($class->isUserDefined());
    }

    /**
     * Tests that the parser does not set the user-defined flag for an unknown
     * class.
     *
     * @return void
     */
    public function testParserNotSetsUserDefinedFlagForUnknownClass()
    {
        $packages = self::parseSource('issues/067-031-user-defined-class.php');

        $package = $packages->current();
        $classes = $package->getClasses();

        $class = $classes->current();

        $this->assertFalse($class->getParentClass()->isUserDefined());
    }

    /**
     * Tests that the parser sets the user-defined flag for an analyzed interface.
     *
     * @return void
     */
    public function testParserSetsUserDefinedFlagForInterface()
    {
        $packages = self::parseSource('issues/067-032-user-defined-interface.php');

        $package    = $packages->current();
        $interfaces = $package->getInterfaces();
        $interface  = $interfaces->current();

        $this->assertTrue($interface->isUserDefined());
    }

    /**
     * Tests that the parser does not sets the user-defined flag for an unknown
     * interface.
     *
     * @return void
     */
    public function testParserNotSetsUserDefinedFlagForUnknownInterface()
    {
        $packages = self::parseSource('issues/067-032-user-defined-interface.php');
        $package  = $packages->current();

        $interface = $package->getInterfaces()->current();
        $this->assertTrue($interface->isUserDefined());

        $interface = $interface->getInterfaces()->current();
        $this->assertFalse($interface->isUserDefined());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is an <b>array</b>.
     *
     * @return void
     */
    public function testParserHandlesDefaultParameterValueNestedArray()
    {
        $packages = self::parseSource('issues/067-033-parameter-default-value-nested-arrays.php');

        $package   = $packages->current();
        $functions = $package->getFunctions();
        $function   = $functions->current();
        $parameters = $function->getParameters();

        $this->assertTrue($parameters->current()->isDefaultValueAvailable());
        $this->assertSame(array(), $parameters->current()->getDefaultValue());
    }

    /**
     * Tests that the parser flag a function with returns reference when its
     * declaraction contains an amphersand.
     *
     * @return void
     */
    public function testParserFlagsFunctionWithReturnsReference()
    {
        $packages = self::parseSource('issues/067-037-' . __FUNCTION__ . '.php');
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $this->assertTrue($function->returnsReference());
    }

    /**
     * Tests that the parser does not set the returns reference flag when the
     * function declaration does not contain an amphersand.
     *
     * @return void
     */
    public function testParserDoesNotFlagFunctionWithReturnsReference()
    {
        $packages = self::parseSource('issues/067-038-' . __FUNCTION__ . '.php');
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $this->assertFalse($function->returnsReference());
    }

    /**
     * Tests that the parser sets the returns reference flag when a method
     * declaration contains an amphersand.
     *
     * @return void
     */
    public function testParserFlagsClassMethodWithReturnsReferences()
    {
        $packages = self::parseSource('issues/067-039-' . __FUNCTION__ . '.php');
        $method   = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $this->assertTrue($method->returnsReference());
    }

    /**
     * Tests that the parser does not set the returns reference flag when a
     * method declaration does not contain an amphersand.
     *
     * @return void
     */
    public function testParserDoesNotFlagClassMethodWithReturnsReferences()
    {
        $packages = self::parseSource('issues/067-040-' . __FUNCTION__ . '.php');
        $method   = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $this->assertFalse($method->returnsReference());
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     *
     * @return void
     */
    public function testParserSetsFunctionStaticVariableSingleUninitialized()
    {
        $packages = self::parseSource('issues/067/' . __FUNCTION__ . '.php');
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $expected = array(
            'x'  =>  null
        );

        $this->assertSame($expected, $function->getStaticVariables());
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     *
     * @return void
     */
    public function testParserSetsFunctionStaticVariableSingleInitialized()
    {
        $packages = self::parseSource('issues/067/' . __FUNCTION__ . '.php');
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $expected = array(
            'x'  =>  42
        );

        $this->assertSame($expected, $function->getStaticVariables());
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     *
     * @return void
     */
    public function testParserSetsFunctionStaticVariablesInSingleDeclaration()
    {
        $packages = self::parseSource('issues/067/' . __FUNCTION__ . '.php');
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $expected = array(
            'x'  =>  true,
            'y'  =>  null,
            'z'  =>  array()
        );

        $this->assertSame($expected, $function->getStaticVariables());
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     *
     * @return void
     */
    public function testParserSetsFunctionStaticVariablesInMultipleDeclarations()
    {
        $packages = self::parseSource('issues/067/' . __FUNCTION__ . '.php');
        $function = $packages->current()
            ->getFunctions()
            ->current();

        $expected = array(
            'x'  =>  false,
            'y'  =>  null,
            'z'  =>  3.14
        );

        $this->assertSame($expected, $function->getStaticVariables());
    }

    /**
     * Tests that the parser handles a static invoke as expected.
     *
     * @return void
     */
    public function testParserStaticVariablesDoNotConflictWithStaticInvoke()
    {
        $packages = self::parseSource('issues/067/' . __FUNCTION__ . '.php');
        $function = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $this->assertSame(array(), $function->getStaticVariables());
    }

    /**
     * Tests that the parser handles a static object allocation as expected.
     *
     * @return void
     */
    public function testParserStaticVariablesDoNotConflictWithStaticAllocation()
    {
        $packages = self::parseSource('issues/067/' . __FUNCTION__ . '.php');
        $function = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();

        $expected = array(
            'x'  =>  true,
            'y'  =>  false
        );

        $this->assertSame($expected, $function->getStaticVariables());
    }

    /**
     * Tests that the parser throws the expected exception when no default value
     * was defined.
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForMissingDefaultValue()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_MissingValueException',
            'Missing default value on line: 2, col: 21, file: '
        );

        self::parseSource('issues/067-034-parameter-missing-default-value.php');
    }

    /**
     * Tests that the parser throws the expected exception when it reaches the
     * end of file while it parses a parameter default value.
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionWhenReachesEofWhileParsingDefaultValue()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_TokenStreamEndException',
            'Unexpected end of token stream in file: '
        );

        self::parseSource('issues/067-035-parameter-missing-default-value-eof.php');
    }

    /**
     * Tests that the parser throws an exception when the default value contains
     * an invalid token.
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionWhenDefaultValueContainsInvalidToken()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: *, line: 2, col: 24, file: '
        );

        self::parseSource('issues/067-036-parameter-default-value-invalid-token.php');
    }
}
?>
