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

namespace PDepend\Issues;

/**
 * Test case for the Reflection API compatibility ticket #67.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTParameter
 * @group unittest
 */
class ReflectionCompatibilityIssue067Test extends AbstractFeatureTest
{
    /**
     * Tests that the parser sets the parameter flag by reference.
     *
     * @return void
     */
    public function testParserSetsFunctionParameterByReference()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isPassedByReference());
    }

    /**
     * Tests that the parser sets the parameter flag by reference.
     *
     * @return void
     */
    public function testParserSetsMultipleFunctionParameterByReference()
    {
        $parameters = $this->getParametersOfFirstFunction();

        $expected = array('$foo' => true, '$bar' => false, '$foobar' => true);
        $actual   = array();
        foreach ($parameters as $parameter) {
            $actual[$parameter->getName()] = $parameter->isPassedByReference();
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser sets the parameter flag by reference.
     *
     * @return void
     */
    public function testParserSetsFunctionParameterByReferenceWithTypeHint()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $parameter  = $parameters[0];

        $this->assertTrue($parameter->isPassedByReference());
    }

    /**
     * Tests that the parser sets the parameter flag by reference.
     *
     * @return void
     */
    public function testParserSetsMultipleFunctionParameterByReferenceWithTypeHint()
    {
        $expected = array('$foo' => true, '$bar' => true);
        $actual   = array();
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[$parameter->getName()] = $parameter->isPassedByReference();
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser sets the is array flag when the parameter contains
     * the array type hint.
     *
     * @return void
     */
    public function testParserSetsParameterArrayFlag()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isArray());
    }

    /**
     * Tests that the parser does not set the array flag when the parameter is
     * scalar without type hint.
     *
     * @return void
     */
    public function testParserDoesNotSetParameterArrayFlagForScalar()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertFalse($parameters[0]->isArray());
    }

    /**
     * Tests that the parser does not set the array flag when the parameter has
     * a class type hint.
     *
     * @return void
     */
    public function testParserDoesNotSetParameterArrayFlagForType()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertFalse($parameters[0]->isArray());
    }

    /**
     * Tests that the boolean flag has default value is <b>false</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterWithoutDefaultValue()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertFalse($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is <b>null</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueNull()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertNull($parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForNullDefaultValue
     * 
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForNullDefaultValue()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is <b>false</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueBooleanFalse()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertFalse($parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForFalseDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForFalseDefaultValue()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is also <b>true</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueBooleanTrue()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForTrueDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForTrueDefaultValue()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is a <b>float</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueFloat()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertEquals(42.23, $parameters[0]->getDefaultValue(), null, 0.001);
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForFloatDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForFloatDefaultValue()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is an <b>integer</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueInteger()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertSame(42, $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForIntegerDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForIntegerDefaultValue()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is a <b>string</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueString()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertSame('foo bar 42', $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForStringDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForStringDefaultValue()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is an <b>array</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueArray()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertSame(array(), $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForArrayDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForArrayDefaultValue()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is an <b>array</b>.
     *
     * @return void
     */
    public function testParserHandlesDefaultParameterValueNestedArray()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertSame(array(), $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForArrayDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForNestedArrayDefaultValue()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is <b>null</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueConstant()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertSame(null, $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForConstantDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForConstantDefaultValue()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is <b>null</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterDefaultValueClassConstant()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertSame(null, $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForClassConstantDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForClassConstantDefaultValue()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the parameter returns the expected result for a parameter
     * without default value.
     *
     * @return void
     */
    public function testParserHandlesParameterWithoutDefaultValueReturnsNull()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertNull($parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsFalseWhenNoDefaultValueExists
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsFalseWhenNoDefaultValueExists()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertFalse($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag for optional parameters is set to <b>false</b>
     * for all parameters.
     *
     * @return void
     */
    public function testParserHandlesParameterOptionalIsFalseForAllParameters()
    {
        $expected = array(false, false, false);
        $actual   = array();
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[] = $parameter->isOptional();
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the boolean flag for optional parameters is set to <b>false</b>
     * for all parameters.
     *
     * @return void
     */
    public function testParserHandlesParameterOptionalIsFalseForAllParametersEvenADefaultValueExists()
    {
        $expected = array(false, false, false);
        $actual   = array();
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[$parameter->getPosition()] = $parameter->isOptional();
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the boolean flag for optional parameters is set to <b>false</b>
     * for the first two parameters.
     *
     * @return void
     */
    public function testParserHandlesParameterOptionalIsFalseForFirstTwoParameters()
    {
        $expected = array(false, false, true);
        $actual   = array();
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[$parameter->getPosition()] = $parameter->isOptional();
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the boolean flag for optional parameters is set to <b>true</b>
     * for all parameters.
     *
     * @return void
     */
    public function testParserHandlesParameterOptionalIsTrueForAllParameters()
    {
        $expected = array(true, true);
        $actual   = array();
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[$parameter->getPosition()] = $parameter->isOptional();
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser sets the user-defined flag for an analyzed class.
     *
     * @return void
     */
    public function testParserSetsUserDefinedFlagForClass()
    {
        $actual = $this->_getFirstClass()->isUserDefined();
        $this->assertTrue($actual);
    }

    /**
     * Tests that the parser does not set the user-defined flag for an unknown
     * class.
     *
     * @return void
     */
    public function testParserNotSetsUserDefinedFlagForUnknownClass()
    {
        $class  = $this->_getFirstClass();
        $actual = $class->getParentClass()->isUserDefined();

        $this->assertFalse($actual);
    }

    /**
     * Tests that the parser sets the user-defined flag for an analyzed interface.
     *
     * @return void
     */
    public function testParserSetsUserDefinedFlagForInterface()
    {
        $this->assertTrue($this->_getFirstInterface()->isUserDefined());
    }

    /**
     * Tests that the parser does not sets the user-defined flag for an unknown
     * interface.
     *
     * @return void
     */
    public function testParserNotSetsUserDefinedFlagForUnknownInterface()
    {
        $interface = $this->_getFirstInterface()->getInterfaces()->current();
        $this->assertFalse($interface->isUserDefined());
    }

    /**
     * Tests that the parser flag a function with returns reference when its
     * declaraction contains an amphersand.
     *
     * @return void
     */
    public function testParserFlagsFunctionWithReturnsReference()
    {
        $this->assertTrue($this->_getFirstFunction()->returnsReference());
    }

    /**
     * Tests that the parser does not set the returns reference flag when the
     * function declaration does not contain an amphersand.
     *
     * @return void
     */
    public function testParserDoesNotFlagFunctionWithReturnsReference()
    {
        $this->assertFalse($this->_getFirstFunction()->returnsReference());
    }

    /**
     * Tests that the parser sets the returns reference flag when a method
     * declaration contains an amphersand.
     *
     * @return void
     */
    public function testParserFlagsClassMethodWithReturnsReferences()
    {
        $actual = $this->_getFirstMethod()->returnsReference();
        $this->assertTrue($actual);
    }

    /**
     * Tests that the parser does not set the returns reference flag when a
     * method declaration does not contain an amphersand.
     *
     * @return void
     */
    public function testParserDoesNotFlagClassMethodWithReturnsReferences()
    {
        $actual = $this->_getFirstMethod()->returnsReference();
        $this->assertFalse($actual);
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     *
     * @return void
     */
    public function testParserSetsFunctionStaticVariableSingleUninitialized()
    {
        $actual   = $this->_getFirstFunction()->getStaticVariables();
        $expected = array('x' => null);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     *
     * @return void
     */
    public function testParserSetsFunctionStaticVariableSingleInitialized()
    {
        $actual   = $this->_getFirstFunction()->getStaticVariables();
        $expected = array('x' => 42);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     *
     * @return void
     */
    public function testParserSetsFunctionStaticVariablesInSingleDeclaration()
    {
        $actual   = $this->_getFirstFunction()->getStaticVariables();
        $expected = array('x' => true, 'y' => null, 'z' => array());

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     *
     * @return void
     */
    public function testParserSetsFunctionStaticVariablesInMultipleDeclarations()
    {
        $actual   = $this->_getFirstFunction()->getStaticVariables();
        $expected = array('x' => false, 'y' => null, 'z' => 3.14);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser handles a static invoke as expected.
     *
     * @return void
     */
    public function testParserStaticVariablesDoNotConflictWithStaticInvoke()
    {
        $actual   = $this->_getFirstMethod()->getStaticVariables();
        $expected = array();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser handles a static object allocation as expected.
     *
     * @return void
     */
    public function testParserStaticVariablesDoNotConflictWithStaticAllocation()
    {
        $actual   = $this->_getFirstMethod()->getStaticVariables();
        $expected = array('x' => true, 'y' => false);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser throws the expected exception when no default value
     * was defined.
     *
     * @return void
     * @covers \PDepend\Source\Parser\MissingValueException
     * @expectedException \PDepend\Source\Parser\MissingValueException
     */
    public function testParserThrowsExpectedExceptionForMissingDefaultValue()
    {
        self::parseTestCase();
    }

    /**
     * Tests that the parser throws the expected exception when it reaches the
     * end of file while it parses a parameter default value.
     *
     * @return void
     * @covers \PDepend\Source\Parser\TokenStreamEndException
     * @expectedException \PDepend\Source\Parser\TokenStreamEndException
     */
    public function testParserThrowsExpectedExceptionWhenReachesEofWhileParsingDefaultValue()
    {
        self::parseTestCase();
    }

    /**
     * Tests that the parser throws an exception when the default value contains
     * an invalid token.
     *
     * @return void
     * @covers \PDepend\Source\Parser\UnexpectedTokenException
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionWhenDefaultValueContainsInvalidToken()
    {
        self::parseTestCase();
    }

    /**
     * Returns the first interface in the test case file.
     *
     * @return \PDepend\Source\AST\ASTInterface
     */
    private function _getFirstInterface()
    {
        $namespaces = self::parseTestCase();
        return $namespaces->current()
            ->getInterfaces()
            ->current();
    }

    /**
     * Returns the first class in the test case file.
     *
     * @return \PDepend\Source\AST\ASTClass
     */
    private function _getFirstClass()
    {
        $namespaces = self::parseTestCase();
        return $namespaces->current()
            ->getClasses()
            ->current();
    }

    /**
     * Returns the first method in the test case file.
     *
     * @return \PDepend\Source\AST\ASTMethod
     */
    private function _getFirstMethod()
    {
        $namespaces = self::parseTestCase();
        return $namespaces->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();
    }

    /**
     * Returns the first function in the test case file.
     *
     * @return \PDepend\Source\AST\ASTFunction
     */
    private function _getFirstFunction()
    {
        $namespaces = self::parseTestCase();
        return $namespaces->current()
            ->getFunctions()
            ->current();
    }
}
