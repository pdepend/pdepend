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
 * @covers \PDepend\Source\AST\ASTProperty
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTPropertyTest extends AbstractTestCase
{
    /**
     * testGetDeclaringClass
     *
     * @since 1.0.0
     */
    public function testGetDeclaringClass(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertEquals(__FUNCTION__, $property->getDeclaringClass()->getImage());
    }

    /**
     * testGetClassForPropertyWithNamespacedInternalType
     */
    public function testGetClassForPropertyWithNamespacedRootType(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertEquals('Foo', $property->getClass()?->getImage());
    }

    /**
     * testGetClassForPropertyWithNamespacedType
     */
    public function testGetClassForPropertyWithNamespacedType(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertEquals('Baz', $property->getClass()?->getImage());
    }

    /**
     * testGetClassForPropertyWithNamespacedArrayRootType
     */
    public function testGetClassForPropertyWithNamespacedArrayRootType(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertEquals('Foo', $property->getClass()?->getImage());
    }

    /**
     * testGetClassForPropertyWithNamespacedArrayType
     */
    public function testGetClassForPropertyWithNamespacedArrayType(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertEquals('Baz', $property->getClass()?->getImage());
    }

    /**
     * testGetClassReturnsNullForPropertyWithScalarType
     */
    public function testGetClassReturnsNullForPropertyWithScalarType(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertNull($property->getClass());
    }

    /**
     * testGetClassReturnsNullForPropertyWithoutTypeHint
     */
    public function testGetClassReturnsNullForPropertyWithoutTypeHint(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertNull($property->getClass());
    }

    /**
     * testGetClassReturnsNullForPropertyWithoutDocComment
     */
    public function testGetClassReturnsNullForPropertyWithoutDocComment(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertNull($property->getClass());
    }

    /**
     * testGetSourceFileReturnsNullByDefault
     */
    public function testGetCompilationUnitReturnsNullByDefault(): void
    {
        $property = $this->getMockWithoutConstructor(ASTProperty::class);
        static::assertNull($property->getCompilationUnit());
    }

    /**
     * testGetSourceFileReturnsInjectedFileInstance
     */
    public function testGetCompilationUnitReturnsInjectedFileInstance(): void
    {
        $compilationUnit = new ASTCompilationUnit(__FILE__);

        $property = $this->getMockWithoutConstructor(ASTProperty::class);
        $property->setCompilationUnit($compilationUnit);

        static::assertSame($compilationUnit, $property->getCompilationUnit());
    }

    /**
     * testGetDocCommentReturnsNullByDefault
     */
    public function testGetDocCommentReturnsNullByDefault(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertNull($property->getComment());
    }

    /**
     * testGetDocCommentReturnsExpectedPropertyComment
     */
    public function testGetDocCommentReturnsExpectedPropertyComment(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertEquals('/** Manuel */', $property->getComment());
    }

    /**
     * Tests that the <b>isDefaultValueAvailable()</b> method returns the
     * expected result.
     */
    public function testPropertyIsDefaultValueAvailableReturnsFalseWhenNoValueExists(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertFalse($property->isDefaultValueAvailable());
    }

    /**
     * Tests that the <b>isDefaultValueAvailable()</b> method returns the
     * expected result.
     */
    public function testPropertyIsDefaultValueAvailableReturnsTrueWhenValueExists(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertTrue($property->isDefaultValueAvailable());
    }

    /**
     * testIsDefaultValueAvailableReturnsExpectedTrueForNullValue
     */
    public function testIsDefaultValueAvailableReturnsExpectedTrueForNullValue(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertTrue($property->isDefaultValueAvailable());
    }

    /**
     * testGetDefaultValueReturnsByDefaultExpectedNull
     */
    public function testGetDefaultValueReturnsByDefaultExpectedNull(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertNull($property->getDefaultValue());
    }

    /**
     * testGetDefaultValueReturnsExpectedNullForNullDefaultValue
     */
    public function testGetDefaultValueReturnsExpectedNullForNullDefaultValue(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertNull($property->getDefaultValue());
    }

    /**
     * Tests that the property default value matches the expected PHP type.
     */
    public function testPropertyContainsExpectDefaultValueBooleanTrue(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertTrue($property->getDefaultValue());
    }

    /**
     * Tests that the property default value matches the expected PHP type.
     */
    public function testPropertyContainsExpectDefaultValueBooleanFalse(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertFalse($property->getDefaultValue());
    }

    /**
     * Tests that the property default value matches the expected PHP type.
     */
    public function testPropertyContainsExpectDefaultValueArray(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertIsArray($property->getDefaultValue());
    }

    /**
     * Tests that the property default value matches the expected PHP type.
     */
    public function testPropertyContainsExpectedDefaultValueFloat(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertEqualsWithDelta(3.14, $property->getDefaultValue(), 0.001);
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTProperty::isArray()} method
     * returns <b>true</b> for an as array annotated property.
     */
    public function testIsArrayReturnsExpectedValueTrueForVarAnnotationWithArray(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertTrue($property->isArray());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTProperty::isArray()} method
     * returns <b>false</b> for an as class/interface annotated property.
     */
    public function testIsArrayReturnsExpectedValueFalseForVarAnnotationWithClassType(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertFalse($property->isArray());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTProperty::isArray()} method
     * returns <b>false</b> for an property without var annotation.
     */
    public function testIsArrayReturnsExpectedValueFalseForPropertyWithoutVarAnnotation(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertFalse($property->isArray());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTProperty::isScalar()}
     * method returns <b>true</b> for an as integer annotated property.
     */
    public function testIsPrimitiveReturnsExpectedValueTrueForVarAnnotationWithIntegerTypeHint(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertTrue($property->isScalar());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTProperty::isScalar()} method
     * returns <b>false</b> for an as class/interface annotated property.
     */
    public function testIsPrimitiveReturnsExpectedValueFalseForVarAnnotationWithClassType(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertFalse($property->isScalar());
    }

    /**
     * testGetDefaultValueReturnsExpectedStringFromHeredoc
     *
     * @since 0.10.9
     */
    public function testGetDefaultValueReturnsExpectedStringFromHeredoc(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertEquals('Testing!', $property->getDefaultValue());
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTProperty::isScalar()} method
     * returns <b>false</b> for an property without var annotation.
     */
    public function testIsPrimitiveReturnsExpectedValueFalseForPropertyWithoutVarAnnotation(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertFalse($property->isScalar());
    }

    /**
     * testIsPublicReturnsFalseByDefault
     */
    public function testIsPublicReturnsFalseByDefault(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertFalse($property->isPublic());
    }

    /**
     * testIsPublicReturnsTrueForPublicProperty
     */
    public function testIsPublicReturnsTrueForPublicProperty(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertTrue($property->isPublic());
    }

    /**
     * testIsProtectedReturnsFalseByDefault
     */
    public function testIsProtectedReturnsFalseByDefault(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertFalse($property->isProtected());
    }

    /**
     * testIsProtectedReturnsTrueForProtectedProperty
     */
    public function testIsProtectedReturnsTrueForProtectedProperty(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertTrue($property->isProtected());
    }

    /**
     * testIsPrivateReturnsFalseByDefault
     */
    public function testIsPrivateReturnsFalseByDefault(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertFalse($property->isPrivate());
    }

    /**
     * testIsPrivateReturnsTrueForPrivateProperty
     */
    public function testIsPrivateReturnsTrueForPrivateProperty(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertTrue($property->isPrivate());
    }

    /**
     * testIsStaticReturnsFalseByDefault
     */
    public function testIsStaticReturnsFalseByDefault(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertFalse($property->isStatic());
    }

    /**
     * testIsStaticReturnsTrueForStaticProperty
     */
    public function testIsStaticReturnsTrueForStaticProperty(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertTrue($property->isStatic());
    }

    /**
     * Tests that a property node has the expected start line.
     */
    public function testPropertyHasExpectedStartLine(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertEquals(4, $property->getStartLine());
    }

    /**
     * Tests that a property node has the expected start column.
     */
    public function testPropertyHasExpectedStartColumn(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertEquals(13, $property->getStartColumn());
    }

    /**
     * Tests that a property node has the expected end line.
     */
    public function testPropertyHasExpectedEndLine(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertEquals(6, $property->getEndLine());
    }

    /**
     * Tests that a property node has the expected end column.
     */
    public function testPropertyHasExpectedEndColumn(): void
    {
        $property = $this->getFirstPropertyInClass();
        static::assertEquals(13, $property->getEndColumn());
    }

    /**
     * Returns the first property found in the corresponding test file.
     */
    private function getFirstPropertyInClass(): ASTProperty
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getProperties()
            ->current();
    }
}
