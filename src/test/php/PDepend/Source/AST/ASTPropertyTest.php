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
 * Test case for the code property class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\AST\ASTProperty
 * @group unittest
 */
class ASTPropertyTest extends AbstractTestCase
{
    /**
     * testGetDeclaringClass
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetDeclaringClass()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertEquals(__FUNCTION__, $property->getDeclaringClass()->getName());
    }

    /**
     * testGetClassForPropertyWithNamespacedInternalType
     *
     * @return void
     */
    public function testGetClassForPropertyWithNamespacedRootType()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertEquals('Foo', $property->getClass()->getName());
    }

    /**
     * testGetClassForPropertyWithNamespacedType
     *
     * @return void
     */
    public function testGetClassForPropertyWithNamespacedType()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertEquals('Baz', $property->getClass()->getName());
    }

    /**
     * testGetClassForPropertyWithNamespacedArrayRootType
     *
     * @return void
     */
    public function testGetClassForPropertyWithNamespacedArrayRootType()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertEquals('Foo', $property->getClass()->getName());
    }

    /**
     * testGetClassForPropertyWithNamespacedArrayType
     *
     * @return void
     */
    public function testGetClassForPropertyWithNamespacedArrayType()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertEquals('Baz', $property->getClass()->getName());
    }

    /**
     * testGetClassReturnsNullForPropertyWithScalarType
     *
     * @return void
     */
    public function testGetClassReturnsNullForPropertyWithScalarType()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertNull($property->getClass());
    }

    /**
     * testGetClassReturnsNullForPropertyWithoutTypeHint
     *
     * @return void
     */
    public function testGetClassReturnsNullForPropertyWithoutTypeHint()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertNull($property->getClass());
    }

    /**
     * testGetClassReturnsNullForPropertyWithoutDocComment
     *
     * @return void
     */
    public function testGetClassReturnsNullForPropertyWithoutDocComment()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertNull($property->getClass());
    }

    /**
     * testGetSourceFileReturnsNullByDefault
     *
     * @return void
     */
    public function testGetCompilationUnitReturnsNullByDefault()
    {
        $property = $this->getMockWithoutConstructor('PDepend\\Source\\AST\\ASTProperty');
        $this->assertNull($property->getCompilationUnit());
    }

    /**
     * testGetSourceFileReturnsInjectedFileInstance
     *
     * @return void
     */
    public function testGetCompilationUnitReturnsInjectedFileInstance()
    {
        $compilationUnit = new ASTCompilationUnit(__FILE__);

        $property = $this->getMockWithoutConstructor('PDepend\\Source\\AST\\ASTProperty');
        $property->setCompilationUnit($compilationUnit);

        $this->assertSame($compilationUnit, $property->getCompilationUnit());
    }

    /**
     * testGetDocCommentReturnsNullByDefault
     *
     * @return void
     */
    public function testGetDocCommentReturnsNullByDefault()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertNull($property->getComment());
    }

    /**
     * testGetDocCommentReturnsExpectedPropertyComment
     *
     * @return void
     */
    public function testGetDocCommentReturnsExpectedPropertyComment()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertEquals('/** Manuel */', $property->getComment());
    }

    /**
     * Tests that the <b>isDefaultValueAvailable()</b> method returns the
     * expected result.
     *
     * @return void
     */
    public function testPropertyIsDefaultValueAvailableReturnsFalseWhenNoValueExists()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertFalse($property->isDefaultValueAvailable());
    }

    /**
     * Tests that the <b>isDefaultValueAvailable()</b> method returns the
     * expected result.
     *
     * @return void
     */
    public function testPropertyIsDefaultValueAvailableReturnsTrueWhenValueExists()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertTrue($property->isDefaultValueAvailable());
    }

    /**
     * testIsDefaultValueAvailableReturnsExpectedTrueForNullValue
     *
     * @return void
     */
    public function testIsDefaultValueAvailableReturnsExpectedTrueForNullValue()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertTrue($property->isDefaultValueAvailable());
    }

    /**
     * testGetDefaultValueReturnsByDefaultExpectedNull
     *
     * @return void
     */
    public function testGetDefaultValueReturnsByDefaultExpectedNull()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertNull($property->getDefaultValue());
    }

    /**
     * testGetDefaultValueReturnsExpectedNullForNullDefaultValue
     *
     * @return void
     */
    public function testGetDefaultValueReturnsExpectedNullForNullDefaultValue()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertNull($property->getDefaultValue());
    }

    /**
     * Tests that the property default value matches the expected PHP type.
     *
     * @return void
     */
    public function testPropertyContainsExpectDefaultValueBooleanTrue()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertTrue($property->getDefaultValue());
    }

    /**
     * Tests that the property default value matches the expected PHP type.
     *
     * @return void
     */
    public function testPropertyContainsExpectDefaultValueBooleanFalse()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertFalse($property->getDefaultValue());
    }

    /**
     * Tests that the property default value matches the expected PHP type.
     *
     * @return void
     */
    public function testPropertyContainsExpectDefaultValueArray()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertIsArray($property->getDefaultValue());
    }

    /**
     * Tests that the property default value matches the expected PHP type.
     *
     * @return void
     */
    public function testPropertyContainsExpectedDefaultValueFloat()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertEqualsWithDelta(3.14, $property->getDefaultValue(), 0.001);
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTProperty::isArray()} method
     * returns <b>true</b> for an as array annotated property.
     *
     * @return void
     */
    public function testIsArrayReturnsExpectedValueTrueForVarAnnotationWithArray()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertTrue($property->isArray());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTProperty::isArray()} method
     * returns <b>false</b> for an as class/interface annotated property.
     *
     * @return void
     */
    public function testIsArrayReturnsExpectedValueFalseForVarAnnotationWithClassType()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertFalse($property->isArray());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTProperty::isArray()} method
     * returns <b>false</b> for an property without var annotation.
     *
     * @return void
     */
    public function testIsArrayReturnsExpectedValueFalseForPropertyWithoutVarAnnotation()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertFalse($property->isArray());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTProperty::isScalar()}
     * method returns <b>true</b> for an as integer annotated property.
     *
     * @return void
     */
    public function testIsPrimitiveReturnsExpectedValueTrueForVarAnnotationWithIntegerTypeHint()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertTrue($property->isScalar());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTProperty::isScalar()} method
     * returns <b>false</b> for an as class/interface annotated property.
     *
     * @return void
     */
    public function testIsPrimitiveReturnsExpectedValueFalseForVarAnnotationWithClassType()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertFalse($property->isScalar());
    }

    /**
     * testGetDefaultValueReturnsExpectedStringFromHeredoc
     *
     * @return void
     * @since 0.10.9
     */
    public function testGetDefaultValueReturnsExpectedStringFromHeredoc()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertEquals('Testing!', $property->getDefaultValue());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTProperty::isScalar()} method
     * returns <b>false</b> for an property without var annotation.
     *
     * @return void
     */
    public function testIsPrimitiveReturnsExpectedValueFalseForPropertyWithoutVarAnnotation()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertFalse($property->isScalar());
    }

    /**
     * testIsPublicReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsPublicReturnsFalseByDefault()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertFalse($property->isPublic());
    }

    /**
     * testIsPublicReturnsTrueForPublicProperty
     *
     * @return void
     */
    public function testIsPublicReturnsTrueForPublicProperty()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertTrue($property->isPublic());
    }

    /**
     * testIsProtectedReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsProtectedReturnsFalseByDefault()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertFalse($property->isProtected());
    }

    /**
     * testIsProtectedReturnsTrueForProtectedProperty
     *
     * @return void
     */
    public function testIsProtectedReturnsTrueForProtectedProperty()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertTrue($property->isProtected());
    }

    /**
     * testIsPrivateReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsPrivateReturnsFalseByDefault()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertFalse($property->isPrivate());
    }

    /**
     * testIsPrivateReturnsTrueForPrivateProperty
     *
     * @return void
     */
    public function testIsPrivateReturnsTrueForPrivateProperty()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertTrue($property->isPrivate());
    }

    /**
     * testIsStaticReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsStaticReturnsFalseByDefault()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertFalse($property->isStatic());
    }

    /**
     * testIsStaticReturnsTrueForStaticProperty
     *
     * @return void
     */
    public function testIsStaticReturnsTrueForStaticProperty()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertTrue($property->isStatic());
    }

    /**
     * testAcceptCallsVisitorMethodVisitProperty
     *
     * @return void
     */
    public function testAcceptCallsVisitorMethodVisitProperty()
    {
        $visitor = $this->getMockBuilder('\\PDepend\\Source\\ASTVisitor\\ASTVisitor')
            ->getMock();
        $visitor->expects($this->once())
            ->method('visitProperty');

        $property = $this->getMockBuilder('PDepend\\Source\\AST\\ASTProperty')
            ->onlyMethods(array('__construct'))
            ->disableOriginalConstructor()
            ->setMockClassName(substr('package_' . md5(microtime()), 0, 18) . '_ASTProperty')
            ->getMock();
        $property->accept($visitor);
    }

    /**
     * Tests that a property node has the expected start line.
     *
     * @return void
     */
    public function testPropertyHasExpectedStartLine()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertEquals(4, $property->getStartLine());
    }

    /**
     * Tests that a property node has the expected start column.
     *
     * @return void
     */
    public function testPropertyHasExpectedStartColumn()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertEquals(13, $property->getStartColumn());
    }

    /**
     * Tests that a property node has the expected end line.
     *
     * @return void
     */
    public function testPropertyHasExpectedEndLine()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertEquals(6, $property->getEndLine());
    }

    /**
     * Tests that a property node has the expected end column.
     *
     * @return void
     */
    public function testPropertyHasExpectedEndColumn()
    {
        $property = $this->getFirstPropertyInClass();
        $this->assertEquals(13, $property->getEndColumn());
    }

    /**
     * Returns the first property found in the corresponding test file.
     *
     * @return \PDepend\Source\AST\ASTProperty
     */
    private function getFirstPropertyInClass()
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getProperties()
            ->current();
    }
}
