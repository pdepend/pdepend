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

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\Parser\MissingValueException;
use PDepend\Source\Parser\TokenStreamEndException;

/**
 * Test case for the Reflection API compatibility ticket #67.
 *
 * @covers \PDepend\Source\AST\ASTParameter
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ReflectionCompatibilityIssue067Test extends AbstractFeatureTestCase
{
    /**
     * Tests that the parser sets the parameter flag by reference.
     */
    public function testParserSetsFunctionParameterByReference(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->isPassedByReference());
    }

    /**
     * Tests that the parser sets the parameter flag by reference.
     */
    public function testParserSetsMultipleFunctionParameterByReference(): void
    {
        $parameters = $this->getParametersOfFirstFunction();

        $expected = ['$foo' => true, '$bar' => false, '$foobar' => true];
        $actual = [];
        foreach ($parameters as $parameter) {
            $actual[$parameter->getImage()] = $parameter->isPassedByReference();
        }
        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser sets the parameter flag by reference.
     */
    public function testParserSetsFunctionParameterByReferenceWithTypeHint(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $parameter = $parameters[0];

        static::assertTrue($parameter->isPassedByReference());
    }

    /**
     * Tests that the parser sets the parameter flag by reference.
     */
    public function testParserSetsMultipleFunctionParameterByReferenceWithTypeHint(): void
    {
        $expected = ['$foo' => true, '$bar' => true];
        $actual = [];
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[$parameter->getImage()] = $parameter->isPassedByReference();
        }
        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser sets the is array flag when the parameter contains
     * the array type hint.
     */
    public function testParserSetsParameterArrayFlag(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->isArray());
    }

    /**
     * Tests that the parser does not set the array flag when the parameter is
     * scalar without type hint.
     */
    public function testParserDoesNotSetParameterArrayFlagForScalar(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertFalse($parameters[0]->isArray());
    }

    /**
     * Tests that the parser does not set the array flag when the parameter has
     * a class type hint.
     */
    public function testParserDoesNotSetParameterArrayFlagForType(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertFalse($parameters[0]->isArray());
    }

    /**
     * Tests that the boolean flag has default value is <b>false</b>.
     */
    public function testParserHandlesParameterWithoutDefaultValue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertFalse($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is <b>null</b>.
     */
    public function testParserHandlesParameterDefaultValueNull(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertNull($parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForNullDefaultValue
     */
    public function testIsDefaultValueAvailableReturnsTrueForNullDefaultValue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is <b>false</b>.
     */
    public function testParserHandlesParameterDefaultValueBooleanFalse(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertFalse($parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForFalseDefaultValue
     */
    public function testIsDefaultValueAvailableReturnsTrueForFalseDefaultValue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is also <b>true</b>.
     */
    public function testParserHandlesParameterDefaultValueBooleanTrue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForTrueDefaultValue
     */
    public function testIsDefaultValueAvailableReturnsTrueForTrueDefaultValue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is a <b>float</b>.
     */
    public function testParserHandlesParameterDefaultValueFloat(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertEqualsWithDelta(42.23, $parameters[0]->getDefaultValue(), 0.001);
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForFloatDefaultValue
     */
    public function testIsDefaultValueAvailableReturnsTrueForFloatDefaultValue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is an <b>integer</b>.
     */
    public function testParserHandlesParameterDefaultValueInteger(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertSame(42, $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForIntegerDefaultValue
     */
    public function testIsDefaultValueAvailableReturnsTrueForIntegerDefaultValue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is a <b>string</b>.
     */
    public function testParserHandlesParameterDefaultValueString(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertSame('foo bar 42', $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForStringDefaultValue
     */
    public function testIsDefaultValueAvailableReturnsTrueForStringDefaultValue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is an <b>array</b>.
     */
    public function testParserHandlesParameterDefaultValueArray(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertSame([], $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForArrayDefaultValue
     */
    public function testIsDefaultValueAvailableReturnsTrueForArrayDefaultValue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is an <b>array</b>.
     */
    public function testParserHandlesDefaultParameterValueNestedArray(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertSame([], $parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForArrayDefaultValue
     */
    public function testIsDefaultValueAvailableReturnsTrueForNestedArrayDefaultValue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is <b>null</b>.
     */
    public function testParserHandlesParameterDefaultValueConstant(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $value = $parameters[0]->getDefaultValue();
        static::assertInstanceOf(ASTNode::class, $value);
        static::assertSame('E_MY_ERROR', $value->getImage());
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForConstantDefaultValue
     */
    public function testIsDefaultValueAvailableReturnsTrueForConstantDefaultValue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag has default value is <b>true</b> and the
     * default value is <b>null</b>.
     */
    public function testParserHandlesParameterDefaultValueClassConstant(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        $value = $parameters[0]->getDefaultValue();
        static::assertInstanceOf(ASTNode::class, $value);
        static::assertSame('\\PDepend\\Code', $value->getImage());

        $value = $parameters[0]->getDefaultValue();
        static::assertInstanceOf(ASTNode::class, $value);
        $node = $value->getChild(0);
        static::assertInstanceOf(ASTNode::class, $node);
        $image = implode(
            $node->getImage(),
            array_map(static fn(ASTNode $node) => $node->getImage(), $node->getChildren()),
        );
        static::assertSame('\\PDepend\\Code::CONSTANT', $image);
    }

    /**
     * testIsDefaultValueAvailableReturnsTrueForClassConstantDefaultValue
     */
    public function testIsDefaultValueAvailableReturnsTrueForClassConstantDefaultValue(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertTrue($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the parameter returns the expected result for a parameter
     * without default value.
     */
    public function testParserHandlesParameterWithoutDefaultValueReturnsNull(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertNull($parameters[0]->getDefaultValue());
    }

    /**
     * testIsDefaultValueAvailableReturnsFalseWhenNoDefaultValueExists
     */
    public function testIsDefaultValueAvailableReturnsFalseWhenNoDefaultValueExists(): void
    {
        $parameters = $this->getParametersOfFirstFunction();
        static::assertFalse($parameters[0]->isDefaultValueAvailable());
    }

    /**
     * Tests that the boolean flag for optional parameters is set to <b>false</b>
     * for all parameters.
     */
    public function testParserHandlesParameterOptionalIsFalseForAllParameters(): void
    {
        $expected = [false, false, false];
        $actual = [];
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[] = $parameter->isOptional();
        }
        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the boolean flag for optional parameters is set to <b>false</b>
     * for all parameters.
     */
    public function testParserHandlesParameterOptionalIsFalseForAllParametersEvenADefaultValueExists(): void
    {
        $expected = [false, false, false];
        $actual = [];
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[$parameter->getPosition()] = $parameter->isOptional();
        }
        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the boolean flag for optional parameters is set to <b>false</b>
     * for the first two parameters.
     */
    public function testParserHandlesParameterOptionalIsFalseForFirstTwoParameters(): void
    {
        $expected = [false, false, true];
        $actual = [];
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[$parameter->getPosition()] = $parameter->isOptional();
        }
        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the boolean flag for optional parameters is set to <b>true</b>
     * for all parameters.
     */
    public function testParserHandlesParameterOptionalIsTrueForAllParameters(): void
    {
        $expected = [true, true];
        $actual = [];
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[$parameter->getPosition()] = $parameter->isOptional();
        }
        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser sets the user-defined flag for an analyzed class.
     */
    public function testParserSetsUserDefinedFlagForClass(): void
    {
        $actual = $this->getFirstClass()->isUserDefined();
        static::assertTrue($actual);
    }

    /**
     * Tests that the parser does not set the user-defined flag for an unknown
     * class.
     */
    public function testParserNotSetsUserDefinedFlagForUnknownClass(): void
    {
        $class = $this->getFirstClass();
        $actual = $class->getParentClass()?->isUserDefined();

        static::assertFalse($actual);
    }

    /**
     * Tests that the parser sets the user-defined flag for an analyzed interface.
     */
    public function testParserSetsUserDefinedFlagForInterface(): void
    {
        static::assertTrue($this->getFirstInterface()->isUserDefined());
    }

    /**
     * Tests that the parser does not sets the user-defined flag for an unknown
     * interface.
     */
    public function testParserNotSetsUserDefinedFlagForUnknownInterface(): void
    {
        $interface = $this->getFirstInterface()->getInterfaces()->current();
        static::assertFalse($interface->isUserDefined());
    }

    /**
     * Tests that the parser flag a function with returns reference when its
     * declaraction contains an amphersand.
     */
    public function testParserFlagsFunctionWithReturnsReference(): void
    {
        static::assertTrue($this->getFirstFunction()->returnsReference());
    }

    /**
     * Tests that the parser does not set the returns reference flag when the
     * function declaration does not contain an amphersand.
     */
    public function testParserDoesNotFlagFunctionWithReturnsReference(): void
    {
        static::assertFalse($this->getFirstFunction()->returnsReference());
    }

    /**
     * Tests that the parser sets the returns reference flag when a method
     * declaration contains an amphersand.
     */
    public function testParserFlagsClassMethodWithReturnsReferences(): void
    {
        $actual = $this->getFirstMethod()->returnsReference();
        static::assertTrue($actual);
    }

    /**
     * Tests that the parser does not set the returns reference flag when a
     * method declaration does not contain an amphersand.
     */
    public function testParserDoesNotFlagClassMethodWithReturnsReferences(): void
    {
        $actual = $this->getFirstMethod()->returnsReference();
        static::assertFalse($actual);
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     */
    public function testParserSetsFunctionStaticVariableSingleUninitialized(): void
    {
        $actual = $this->getFirstFunction()->getStaticVariables();
        $expected = ['x' => null];

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     */
    public function testParserSetsFunctionStaticVariableSingleInitialized(): void
    {
        $actual = $this->getFirstFunction()->getStaticVariables();
        $expected = ['x' => 42];

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     */
    public function testParserSetsFunctionStaticVariablesInSingleDeclaration(): void
    {
        $actual = $this->getFirstFunction()->getStaticVariables();
        $expected = ['x' => true, 'y' => null, 'z' => []];

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the <b>getStaticVariables()</b> method returns the expected
     * result.
     */
    public function testParserSetsFunctionStaticVariablesInMultipleDeclarations(): void
    {
        $actual = $this->getFirstFunction()->getStaticVariables();
        $expected = ['x' => false, 'y' => null, 'z' => 3.14];

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser handles a static invoke as expected.
     */
    public function testParserStaticVariablesDoNotConflictWithStaticInvoke(): void
    {
        $actual = $this->getFirstMethod()->getStaticVariables();
        $expected = [];

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser handles a static object allocation as expected.
     */
    public function testParserStaticVariablesDoNotConflictWithStaticAllocation(): void
    {
        $actual = $this->getFirstMethod()->getStaticVariables();
        $expected = ['x' => true, 'y' => false];

        static::assertEquals($expected, $actual);
    }

    /**
     * Tests that the parser throws the expected exception when no default value
     * was defined.
     *
     * @covers \PDepend\Source\Parser\MissingValueException
     */
    public function testParserThrowsExpectedExceptionForMissingDefaultValue(): void
    {
        $this->expectException(MissingValueException::class);

        $this->parseTestCase();
    }

    /**
     * Tests that the parser throws the expected exception when it reaches the
     * end of file while it parses a parameter default value.
     *
     * @covers \PDepend\Source\Parser\TokenStreamEndException
     */
    public function testParserThrowsExpectedExceptionWhenReachesEofWhileParsingDefaultValue(): void
    {
        $this->expectException(TokenStreamEndException::class);

        $this->parseTestCase();
    }

    /**
     * Returns the first interface in the test case file.
     */
    private function getFirstInterface(): ASTInterface
    {
        $namespaces = $this->parseTestCase();

        return $namespaces->current()
            ->getInterfaces()
            ->current();
    }

    /**
     * Returns the first class in the test case file.
     */
    private function getFirstClass(): ASTClass
    {
        $namespaces = $this->parseTestCase();

        return $namespaces->current()
            ->getClasses()
            ->current();
    }

    /**
     * Returns the first method in the test case file.
     */
    private function getFirstMethod(): ASTMethod
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
     */
    private function getFirstFunction(): ASTFunction
    {
        $namespaces = $this->parseTestCase();

        return $namespaces->current()
            ->getFunctions()
            ->current();
    }
}
