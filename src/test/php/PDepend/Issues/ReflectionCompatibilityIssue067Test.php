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

namespace PDepend\Issues;

use PDepend\Source\AST\ASTNode;

/**
 * Test case for the Reflection API compatibility ticket #67.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTParameter
 * @group unittest
 */
class ReflectionCompatibilityIssue067Test extends AbstractFeatureTestCase
{
    /**
     * Tests that the parser sets the parameter flag by reference.
     *
     * @return void
     */
    public function testParserSetsFunctionParameterByReference(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isPassedByReference());
    }

    /**
     * Tests that the parser sets the parameter flag by reference.
     *
     * @return void
     */
    public function testParserSetsMultipleFunctionParameterByReference(): void
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
    public function testParserSetsFunctionParameterByReferenceWithTypeHint(): void
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
    public function testParserSetsMultipleFunctionParameterByReferenceWithTypeHint(): void
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
    public function testParserSetsParameterArrayFlag(): void
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
    public function testParserDoesNotSetParameterArrayFlagForScalar(): void
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
    public function testParserDoesNotSetParameterArrayFlagForType(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertFalse($parameters[0]->isArray());
    }

    /**
     * Tests that the boolean flag has default value is <b>false</b>.
     *
     * @return void
     */
    public function testParserHandlesParameterWithoutDefaultValue(): void
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
    public function testParserHandlesParameterDefaultValueNull(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertNull($parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForNullDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForNullDefaultValue(): void
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
    public function testParserHandlesParameterDefaultValueBooleanFalse(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertFalse($parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForFalseDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForFalseDefaultValue(): void
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
    public function testParserHandlesParameterDefaultValueBooleanTrue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForTrueDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForTrueDefaultValue(): void
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
    public function testParserHandlesParameterDefaultValueFloat(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertEqualsWithDelta(42.23, $parameters[0]->getDefaultValue(), 0.001);
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForFloatDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForFloatDefaultValue(): void
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
    public function testParserHandlesParameterDefaultValueInteger(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertSame(42, $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForIntegerDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForIntegerDefaultValue(): void
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
    public function testParserHandlesParameterDefaultValueString(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertSame('foo bar 42', $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForStringDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForStringDefaultValue(): void
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
    public function testParserHandlesParameterDefaultValueArray(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertSame(array(), $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForArrayDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForArrayDefaultValue(): void
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
    public function testParserHandlesDefaultParameterValueNestedArray(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertSame(array(), $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForArrayDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForNestedArrayDefaultValue(): void
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
    public function testParserHandlesParameterDefaultValueConstant(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertSame('E_MY_ERROR', $parameters[0]->getDefaultValue()->getImage());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForConstantDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForConstantDefaultValue(): void
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
    public function testParserHandlesParameterDefaultValueClassConstant(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
        $this->assertSame('\\PDepend\\Code', $parameters[0]->getDefaultValue()->getImage());
        /** @var ASTNode $node */
        $node = $parameters[0]->getDefaultValue()->getChild(0);
        $image = implode($node->getImage(), array_map(function (ASTNode $node) {
            return $node->getImage();
        }, $node->getChildren()));
        $this->assertSame('\\PDepend\\Code::CONSTANT', $image);
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForClassConstantDefaultValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsTrueForClassConstantDefaultValue(): void
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
    public function testParserHandlesParameterWithoutDefaultValueReturnsNull(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertNull($parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsFalseWhenNoDefaultValueExists
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsFalseWhenNoDefaultValueExists(): void
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
    public function testParserHandlesParameterOptionalIsFalseForAllParameters(): void
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
    public function testParserHandlesParameterOptionalIsFalseForAllParametersEvenADefaultValueExists(): void
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
    public function testParserHandlesParameterOptionalIsFalseForFirstTwoParameters(): void
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
    public function testParserHandlesParameterOptionalIsTrueForAllParameters(): void
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
    public function testParserSetsUserDefinedFlagForClass(): void
    {
        $actual = $this->getFirstClass()->isUserDefined();
        $this->assertTrue($actual);
    }

    /**
     * Tests that the parser does not set the user-defined flag for an unknown
     * class.
     *
     * @return void
     */
    public function testParserNotSetsUserDefinedFlagForUnknownClass(): void
    {
        $class  = $this->getFirstClass();
        $actual = $class->getParentClass()->isUserDefined();

        $this->assertFalse($actual);
    }

    /**
     * Tests that the parser sets the user-defined flag for an analyzed interface.
     *
     * @return void
     */
    public function testParserSetsUserDefinedFlagForInterface(): void
    {
        $this->assertTrue($this->getFirstInterface()->isUserDefined());
    }

    /**
     * Tests that the parser does not sets the user-defined flag for an unknown
     * interface.
     *
     * @return void
     */
    public function testParserNotSetsUserDefinedFlagForUnknownInterface(): void
    {
        $interface = $this->getFirstInterface()->getInterfaces()->current();
        $this->assertFalse($interface->isUserDefined());
    }

    /**
     * Tests that the parser flag a function with returns reference when its
     * declaraction contains an amphersand.
     *
     * @return void
     */
    public function testParserFlagsFunctionWithReturnsReference(): void
    {
        $this->assertTrue($this->getFirstFunction()->returnsReference());
    }

    /**
     * Tests that the parser does not set the returns reference flag when the
     * function declaration does not contain an amphersand.
     *
     * @return void
     */
    public function testParserDoesNotFlagFunctionWithReturnsReference(): void
    {
        $this->assertFalse($this->getFirstFunction()->returnsReference());
    }

    /**
     * Tests that the parser sets the returns reference flag when a method
     * declaration contains an amphersand.
     *
     * @return void
     */
    public function testParserFlagsClassMethodWithReturnsReferences(): void
    {
        $actual = $this->getFirstMethod()->returnsReference();
        $this->assertTrue($actual);
    }

    /**
     * Tests that the parser does not set the returns reference flag when a
     * method declaration does not contain an amphersand.
     *
     * @return void
     */
    public function testParserDoesNotFlagClassMethodWithReturnsReferences(): void
    {
        $actual = $this->getFirstMethod()->returnsReference();
        $this->assertFalse($actual);
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     *
     * @return void
     */
    public function testParserSetsFunctionStaticVariableSingleUninitialized(): void
    {
        $actual   = $this->getFirstFunction()->getStaticVariables();
        $expected = array('x' => null);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     *
     * @return void
     */
    public function testParserSetsFunctionStaticVariableSingleInitialized(): void
    {
        $actual   = $this->getFirstFunction()->getStaticVariables();
        $expected = array('x' => 42);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     *
     * @return void
     */
    public function testParserSetsFunctionStaticVariablesInSingleDeclaration(): void
    {
        $actual   = $this->getFirstFunction()->getStaticVariables();
        $expected = array('x' => true, 'y' => null, 'z' => array());

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     *
     * @return void
     */
    public function testParserSetsFunctionStaticVariablesInMultipleDeclarations(): void
    {
        $actual   = $this->getFirstFunction()->getStaticVariables();
        $expected = array('x' => false, 'y' => null, 'z' => 3.14);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser handles a static invoke as expected.
     *
     * @return void
     */
    public function testParserStaticVariablesDoNotConflictWithStaticInvoke(): void
    {
        $actual   = $this->getFirstMethod()->getStaticVariables();
        $expected = array();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser handles a static object allocation as expected.
     *
     * @return void
     */
    public function testParserStaticVariablesDoNotConflictWithStaticAllocation(): void
    {
        $actual   = $this->getFirstMethod()->getStaticVariables();
        $expected = array('x' => true, 'y' => false);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser throws the expected exception when no default value
     * was defined.
     *
     * @return void
     * @covers \PDepend\Source\Parser\MissingValueException
     */
    public function testParserThrowsExpectedExceptionForMissingDefaultValue(): void
    {
        $this->expectException(\PDepend\Source\Parser\MissingValueException::class);

        $this->parseTestCase();
    }

    /**
     * Tests that the parser throws the expected exception when it reaches the
     * end of file while it parses a parameter default value.
     *
     * @return void
     * @covers \PDepend\Source\Parser\TokenStreamEndException
     */
    public function testParserThrowsExpectedExceptionWhenReachesEofWhileParsingDefaultValue(): void
    {
        $this->expectException(\PDepend\Source\Parser\TokenStreamEndException::class);

        $this->parseTestCase();
    }

    /**
     * Returns the first interface in the test case file.
     *
     * @return \PDepend\Source\AST\ASTInterface
     */
    private function getFirstInterface()
    {
        $namespaces = $this->parseTestCase();
        return $namespaces->current()
            ->getInterfaces()
            ->current();
    }

    /**
     * Returns the first class in the test case file.
     *
     * @return \PDepend\Source\AST\ASTClass
     */
    private function getFirstClass()
    {
        $namespaces = $this->parseTestCase();
        return $namespaces->current()
            ->getClasses()
            ->current();
    }

    /**
     * Returns the first method in the test case file.
     *
     * @return \PDepend\Source\AST\ASTMethod
     */
    private function getFirstMethod()
    {
        $namespaces = $this->parseTestCase();
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
    private function getFirstFunction()
    {
        $namespaces = $this->parseTestCase();
        return $namespaces->current()
            ->getFunctions()
            ->current();
    }
}
