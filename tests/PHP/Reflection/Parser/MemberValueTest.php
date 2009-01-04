<?php
/**
 * This file is part of PHP_Reflection.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @package    PHP_Reflection
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the member value handling of the parser.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Parser_MemberValueTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests that the parser sets a scalar null value instance for class constants
     * defined with <b>null</b>
     *
     * @return void
     */
    public function testParserHandlesNullClassOrInterfaceConstant()
    {
        $value = $this->_testParserHandlesConstantValue('class_null_constant.php');
        
        $this->assertEquals(PHP_Reflection_AST_MemberValueI::IS_NULL, $value->getType());
        $this->assertNull($value->getValue());
    }
    
    /**
     * Tests that the parser sets a scalar true value instance for class constants
     * defined with <b>true</b>
     *
     * @return void
     */
    public function testParserHandlesTrueClassOrInterfaceConstant()
    {
        $value = $this->_testParserHandlesConstantValue('interface_true_constant.php');
        
        $this->assertEquals(PHP_Reflection_AST_MemberValueI::IS_BOOLEAN, $value->getType());
        $this->assertTrue($value->getValue());
    }
    
    /**
     * Tests that the parser sets a scalar false value instance for class constants
     * defined with <b>false</b>
     *
     * @return void
     */
    public function testParserHandlesFalseClassOrInterfaceConstant()
    {
        $value = $this->_testParserHandlesConstantValue('interface_false_constant.php');
        
        $this->assertEquals(PHP_Reflection_AST_MemberValueI::IS_BOOLEAN, $value->getType());
        $this->assertFalse($value->getValue());        
    }
    
    /**
     * Tests that the parser sets a scalar value instance for class constants
     * defined with a <b>float</b>
     *
     * @return void
     */
    public function testParserHandlesDoubleClassOrInterfaceConstant()
    {
        $value = $this->_testParserHandlesConstantValue('class_double_constant.php');
        
        $this->assertEquals(PHP_Reflection_AST_MemberValueI::IS_DOUBLE, $value->getType());
        $this->assertType('float', $value->getValue());
        $this->assertEquals(3.14, $value->getValue());
    }
    
    /**
     * Tests that the parser sets a scalar value instance for interface constants
     * defined with a <b>float</b>
     *
     * @return void
     */
    public function testParserHandlesNegativeDoubleClassOrInterfaceConstant()
    {
        $value = $this->_testParserHandlesConstantValue('interface_double_constant.php');
        
        $this->assertEquals(PHP_Reflection_AST_MemberValueI::IS_DOUBLE, $value->getType());
        $this->assertType('float', $value->getValue());
        $this->assertEquals(-3.14, $value->getValue());
    }
    
    /**
     * Tests that the parser sets a scalar value instance for class constants
     * defined with an <b>integer</b>
     *
     * @return void
     */
    public function testParserHandlesIntegerClassOrInterfaceConstant()
    {
        $value = $this->_testParserHandlesConstantValue('interface_integer_constant.php');
        
        $this->assertEquals(PHP_Reflection_AST_MemberValueI::IS_INTEGER, $value->getType());
        $this->assertType('integer', $value->getValue());
        $this->assertEquals(23, $value->getValue());
    }
    
    /**
     * Tests that the parser sets a scalar value instance for interface constants
     * declared with a negative <b>integer</b> value.
     *
     * @return void
     */
    public function testParserHandlesSignedIntegerClassOrInterfaceConstant()
    {
        $value = $this->_testParserHandlesConstantValue('interface_integer_signed_constant.php');
        
        $this->assertEquals(PHP_Reflection_AST_MemberValueI::IS_INTEGER, $value->getType());
        $this->assertType('integer', $value->getValue());
        $this->assertEquals(-42, $value->getValue());
    }
    
    /**
     * Tests that the parser sets a scalar value instance for class constants
     * declared with an <b>string</b>
     *
     * @return void
     */
    public function testParserHandlesSingleQuoteStringClassOrInterfaceConstant()
    {
        $value = $this->_testParserHandlesConstantValue('class_string_constant.php');
        
        $this->assertEquals(PHP_Reflection_AST_MemberValueI::IS_STRING, $value->getType());
        $this->assertType('string', $value->getValue());
        $this->assertEquals('Hello World', $value->getValue());        
    }
    
    /**
     * Tests that the parser sets a scalar value instance for interface constants
     * declared with an <b>string</b>
     *
     * @return void
     */
    public function testParserHandlesDoubleQuoteStringClassOrInterfaceConstant()
    {
        $value = $this->_testParserHandlesConstantValue('interface_string_constant.php');
        
        $this->assertEquals(PHP_Reflection_AST_MemberValueI::IS_STRING, $value->getType());
        $this->assertType('string', $value->getValue());
        $this->assertEquals('Hello World', $value->getValue());        
    }
    
    /**
     * Tests that the parser sets a scalar value instance for class constants
     * defined with an <b>integer</b>
     *
     * @return void
     */
    public function testParserHandlesNegativeIntegerClassOrInterfaceConstant()
    {
        $value = $this->_testParserHandlesConstantValue('class_integer_constant.php');
        
        $this->assertEquals(PHP_Reflection_AST_MemberValueI::IS_INTEGER, $value->getType());
        $this->assertType('integer', $value->getValue());
        $this->assertEquals(-23, $value->getValue());
    }
    
    /**
     * Tests that parser sets a constant reference for a class constant with a
     * value declared with php's <b>define()</b> function.
     *
     * @return void
     */
    public function testParserHandlesConstantValueForClassOrInterfaceConstant()
    {
        $value = $this->_testParserHandlesConstantValue('interface_constant_constant.php');
        
        $this->assertType('PHP_Reflection_AST_ConstantValue', $value);
        $this->assertEquals(PHP_Reflection_AST_ConstantValue::IS_CONSTANT, $value->getType());
        $this->assertEquals('T_NAMESPACE', $value->getName());
    }
    
    /**
     * Tests that the parser handles a simple array without keys and just static
     * scalar values correct.
     *
     * @return void
     */
    public function testParserHandlesArrayWithoutKeysAndJustStaticScalarsClassProperty()
    {
        $expected = array(
            array('key' => null, 'value' => 42),
            array('key' => null, 'value' => 23),
            array('key' => null, 'value' => 17),
        );
        
        $value = $this->_testParserHandlesArrayValue('class_array_no_keys_static_values_property.php');
        $this->assertEquals(count($expected), $value->count());
        
        foreach ($value->getElements() as $element) {
            // Get test data for element
            $test = array_shift($expected);
            
            // Get element key and compare result
            $key = $element->getKey();
            if ($key === null) {
                $this->assertNull($key);
            } else {
                $this->assertType('PHP_Reflection_AST_MemberNumericValue', $key);
                $this->assertEquals($test['key'], $key->getValue());
            }
            
            // Get element value and compare result
            $value = $element->getValue();
            $this->assertType('PHP_Reflection_AST_MemberNumericValue', $value);
            $this->assertEquals($test['value'], $value->getValue());
        }
        
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the parser handles a mix of implicit and explicit keys correct.
     *
     * @return void
     */
    public function testParserHandlesAMixOfImplicitAndExpicitArrayKeysCorrect()
    {
        $expected = array(
            array('key' => 42,   'value' => 'a'),
            array('key' => 23,   'value' => true),
            array('key' => 17,   'value' => false),
            array('key' => null, 'value' => 0.5),
        );
        
        $array = $this->_testParserHandlesArrayValue('class_array_static_keys_static_values_property.php');
        $this->assertEquals(count($expected), $array->count());
        
        foreach ($array->getElements() as $element) {
            // Get test data for element
            $test = array_shift($expected);
            
            // Get element key and compare result
            $key = $element->getKey();
            if ($key === null) {
                $this->assertNull($key);
            } else {
                $this->assertType('PHP_Reflection_AST_MemberNumericValue', $key);
                $this->assertEquals($test['key'], $key->getValue());
            }
            
            // Get element value and compare result
            $value = $element->getValue();
            $this->assertType('PHP_Reflection_AST_MemberValueI', $value);
            $this->assertEquals($test['value'], $value->getValue());
        }
        
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the parser creates a correct array for global constant keys
     * and values.
     *
     * @return void
     */
    public function testParserHandlesArrayWithConstantKeyValuePairsCorrect()
    {
        $expected = array(
            array('key' => 'T_NAMESPACE', 'value' => '__LINE__'),
            array('key' => 'T_STRING',    'value' => '__FILE__'),
            array('key' => 'T_ARRAY',     'value' => '__CLASS__')
        );
        
        $array = $this->_testParserHandlesArrayValue('class_array_constant_keys_and_values_property.php');
        $this->assertEquals(count($expected), $array->count());
        
        foreach ($array->getElements() as $element) {
            // Get test element
            $test = array_shift($expected);

            $key = $element->getKey();
            $this->assertType('PHP_Reflection_AST_ConstantValue', $key);
            $this->assertEquals($test['key'], $key->getName());
            
            $value = $element->getValue();
            $this->assertType('PHP_Reflection_AST_ConstantValue', $value);
            $this->assertEquals($test['value'], $value->getName());
        }
        
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the parser handles a internal class constant as default 
     * property value correct.
     *
     * @return void
     */
    public function testParserHandlesPropertyWithInternalClassOrInterfaceConstantValue()
    {
        $value = $this->_testParserHandlesStaticScalarValue('class_property_internal_class_or_interface_constant.php');
        $this->assertType('PHP_Reflection_AST_ClassOrInterfaceConstantValue', $value);
        
        $constName = $value->getName();
        $this->assertEquals('IS_PUBLIC', $constName);
        
        $classOrInterface = $value->getReference();
        $this->assertType('PHP_Reflection_AST_ClassOrInterfaceProxy', $classOrInterface);
        $this->assertEquals('ReflectionProperty', $classOrInterface->getName());
        
        $package = $classOrInterface->getPackage();
        $this->assertEquals('+reflection', $package->getName());
    }
    
    /**
     * Tests that the parser handles a userland class constant as default 
     * property value correct.
     *
     * @return void
     */
    public function testParserHandlesPropertyWithUserlandClassOrInterfaceConstantValue()
    {
        $value = $this->_testParserHandlesStaticScalarValue('class_property_userland_class_or_interface_constant.php');
        $this->assertType('PHP_Reflection_AST_ClassOrInterfaceConstantValue', $value);
        
        $constName = $value->getName();
        $this->assertEquals('IS_PUBLIC', $constName);
        
        $classOrInterface = $value->getReference();
        $this->assertType('PHP_Reflection_AST_ClassOrInterfaceProxy', $classOrInterface);
        $this->assertEquals('myReflectionProperty', $classOrInterface->getName());
        
        $package = $classOrInterface->getPackage();
        $this->assertEquals('php::reflection', $package->getName());        
    }
    
    /**
     * Tests that the parser handles a self:: class constant as default 
     * property value correct.
     *
     * @return void
     */
    public function testParserHandlesPropertyWithSelfClassConstantValue()
    {
        $value = $this->_testParserHandlesConstantValue('class_property_self_class_constant.php');
        $this->assertType('PHP_Reflection_AST_ClassOrInterfaceConstantValue', $value);
        
        $constName = $value->getName();
        $this->assertEquals('T_TEST', $constName);
        
        $class = $value->getReference();
        $this->assertType('PHP_Reflection_AST_Class', $class);
        $this->assertEquals('PHP_Reflection', $class->getName());
        
        $package = $class->getPackage();
        $this->assertEquals('+unknown', $package->getName());    
    }
    
    /**
     * Tests that the parser handles stupid signed numeric values.
     *
     * @return void
     */
    public function testParserHandlesPropertyValueWithMultipleSignedModifiers()
    {
        $value = $this->_testParserHandlesStaticScalarValue('class_property_signed_value.php');
        $this->assertType('PHP_Reflection_AST_MemberNumericValue', $value);
        
        $this->assertFalse($value->isNegative());
        $scalar = $value->getValue();
          
    }
    
    /**
     * Parses the source, extracts the first constant of a class or interface
     * and checks some basic constraints.
     *
     * @param string $file The test source file.
     * 
     * @return PHP_Reflection_AST_StaticScalarValueI
     */
    private function _testParserHandlesConstantValue($file)
    {
        $packages = self::parseSource("/parser/member-values/{$file}");
        
        $classOrInterface = $packages->current()->getTypes()->current();
        $this->assertNotNull($classOrInterface);
        $this->assertType('object', $classOrInterface);
        
        $const = $classOrInterface->getConstants()->current();
        $this->assertNotNull($const);
        $this->assertType('object', $const);
         
        $value = $const->getValue();
        $this->assertNotNull($value);
        $this->assertType('PHP_Reflection_AST_StaticScalarValueI', $value);
        
        return $value;
    }
    
    /**
     * Parses the source, extracts the first property of a class and checks some
     * basic constraints.
     *
     * @param string $file The test source file.
     * 
     * @return PHP_Reflection_AST_ArrayExpression
     */
    private function _testParserHandlesArrayValue($file)
    {
        $packages = self::parseSource("/parser/member-values/{$file}");
        
        $class = $packages->current()->getClasses()->current();
        $this->assertNotNull($class);
        $this->assertType('object', $class);
        
        $prop = $class->getProperties()->current();
        $this->assertNotNull($prop);
        $this->assertType('object', $prop);
         
        $value = $prop->getValue();
        $this->assertNotNull($value);
        $this->assertType('PHP_Reflection_AST_ArrayExpression', $value);
        $this->assertEquals(PHP_Reflection_AST_MemberValueI::IS_ARRAY, $value->getType());
        
        return $value;
    }
    
    /**
     * Parses the source, extracts the first property of a class and checks some
     * basic constraints.
     *
     * @param string $file The test source file.
     * 
     * @return PHP_Reflection_AST_StaticScalarValue
     */
    private function _testParserHandlesStaticScalarValue($file)
    {
        $packages = self::parseSource("/parser/member-values/{$file}");
        
        $class = $packages->current()->getClasses()->current();
        $this->assertNotNull($class);
        $this->assertType('object', $class);

        $prop = $class->getProperties()->current();
        $this->assertNotNull($prop);
        $this->assertType('object', $prop);
         
        $value = $prop->getValue();
        $this->assertNotNull($value);
        $this->assertType('PHP_Reflection_AST_StaticScalarValueI', $value);
        
        return $value;
    }
}