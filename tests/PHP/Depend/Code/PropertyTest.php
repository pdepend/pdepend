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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Property.php';

/**
 * Test case for the code property class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Code_PropertyTest extends PHP_Depend_AbstractTest
{
    /**
     * testGetClassForPropertyWithNamespacedInternalType
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetClassForPropertyWithNamespacedRootType()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertEquals('Foo', $property->getClass()->getName());
    }

    /**
     * testGetClassForPropertyWithNamespacedType
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetClassForPropertyWithNamespacedType()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertEquals('Baz', $property->getClass()->getName());
    }

    /**
     * testGetClassForPropertyWithNamespacedArrayRootType
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetClassForPropertyWithNamespacedArrayRootType()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertEquals('Foo', $property->getClass()->getName());
    }

    /**
     * testGetClassForPropertyWithNamespacedArrayType
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testGetClassForPropertyWithNamespacedArrayType()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertEquals('Baz', $property->getClass()->getName());
    }

    /**
     * Tests that the <b>isDefaultValueAvailable()</b> method returns the
     * expected result.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testPropertyIsDefaultValueAvailableReturnsFalseWhenNoValueExists()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertFalse($property->isDefaultValueAvailable());
        $this->assertNull($property->getDefaultValue());
    }

    /**
     * Tests that the <b>isDefaultValueAvailable()</b> method returns the
     * expected result.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testPropertyIsDefaultValueAvailableReturnsTrueWhenValueExists()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertTrue($property->isDefaultValueAvailable());
        $this->assertNull($property->getDefaultValue());
    }

    /**
     * Tests that the property default value matches the expected PHP type.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testPropertyContainsExpectDefaultValueBooleanTrue()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertTrue($property->getDefaultValue());
    }

    /**
     * Tests that the property default value matches the expected PHP type.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testPropertyContainsExpectDefaultValueBooleanFalse()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertFalse($property->getDefaultValue());
    }

    /**
     * Tests that the property default value matches the expected PHP type.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testPropertyContainsExpectDefaultValueArray()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertType('array', $property->getDefaultValue());
    }

    /**
     * Tests that the property default value matches the expected PHP type.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testPropertyContainsExpectedDefaultValueFloat()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertEquals(3.14, $property->getDefaultValue(), '', 0.001);
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Property::isArray()} method returns
     * <b>true</b> for an as array annotated property.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsArrayReturnsExpectedValueTrueForVarAnnotationWithArray()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertTrue($property->isArray());
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Property::isArray()} method returns
     * <b>false</b> for an as class/interface annotated property.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsArrayReturnsExpectedValueFalseForVarAnnotationWithClassType()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertFalse($property->isArray());
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Property::isArray()} method returns
     * <b>false</b> for an property without var annotation.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsArrayReturnsExpectedValueFalseForPropertyWithoutVarAnnotation()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertFalse($property->isArray());
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Property::isPrimitive()} method
     * returns <b>true</b> for an as integer annotated property.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsPrimitiveReturnsExpectedValueTrueForVarAnnotationWithIntegerTypeHint()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertTrue($property->isPrimitive());
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Property::isPrimitive()} method
     * returns <b>false</b> for an as class/interface annotated property.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsPrimitiveReturnsExpectedValueFalseForVarAnnotationWithClassType()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertFalse($property->isPrimitive());
    }

    /**
     * Tests that the {@link PHP_Depend_Code_Property::isPrimitive()} method
     * returns <b>false</b> for an property without var annotation.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testIsPrimitiveReturnsExpectedValueFalseForPropertyWithoutVarAnnotation()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertFalse($property->isPrimitive());
    }

    /**
     * Tests that a property node has the expected start line.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testPropertyHasExpectedStartLine()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertSame(4, $property->getStartLine());
    }

    /**
     * Tests that a property node has the expected start column.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testPropertyHasExpectedStartColumn()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertSame(13, $property->getStartColumn());
    }

    /**
     * Tests that a property node has the expected end line.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testPropertyHasExpectedEndLine()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertSame(6, $property->getEndLine());
    }

    /**
     * Tests that a property node has the expected end column.
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testPropertyHasExpectedEndColumn()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $this->assertSame(13, $property->getEndColumn());
    }

    /**
     * testFreeResetsDeclaringClassToNull
     *
     * @return void
     * @covers PHP_Depend_Code_Property
     * @group pdepend
     * @group pdepend::code
     * @group unittest
     */
    public function testFreeResetsDeclaringClassToNull()
    {
        $property = $this->_getFirstPropertyInClass(__METHOD__);
        $property->free();

        $this->assertNull($property->getDeclaringClass());
    }

    /**
     * Returns the first property found in the corresponding test file.
     *
     * @param string $testCase Qualified name of the calling test case.
     *
     * @return PHP_Depend_Code_Property
     */
    private function _getFirstPropertyInClass($testCase)
    {
        list($className, $methodName) = explode('::', $testCase);

        $packages = self::parseSource('code/property/' . $methodName . '.php');
        return $packages->current()
            ->getClasses()
            ->current()
            ->getProperties()
            ->current();
    }
}