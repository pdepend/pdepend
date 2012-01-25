<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the api compatiblity between PHP's native reflection api and
 * this userland implementation.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Code_Property
 * @group pdepend
 * @group pdepend::code
 * @group unittest
 */
class PHP_Depend_Code_ReflectionPropertyTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that userland implementation redeclares all methods.
     *
     * @return void
     */
    public function testAllReflectionApiMethodsAvailable()
    {
        $expectedClass = 'PHP_Depend_Code_Property';
        $reflection    = new ReflectionClass($expectedClass);

        $nativeReflection = new ReflectionClass('ReflectionProperty');
        foreach ($nativeReflection->getMethods() as $nativeMethod) {

            if ($nativeMethod->isStatic() === true
                || $nativeMethod->isPublic() === false
            ) {
                continue;
            }

            $this->assertTrue(
                $reflection->hasMethod($nativeMethod->getName()),
                sprintf(
                    'Missing method %s::%s()',
                    $expectedClass,
                    $nativeMethod->getName()
                )
            );

            $this->assertSame(
                $expectedClass,
                $reflection->getMethod($nativeMethod->getName())
                    ->getDeclaringClass()
                    ->getName(),
                sprintf(
                    'Missing method %s::%s().',
                    $expectedClass,
                    $nativeMethod->getName()
                )
            );
        }
    }

    /**
     * Tests that the returned modifiers are similar for PHP_Depend and the
     * native reflection api.
     *
     * @return void
     */
    public function testGetModifiersForVarProperty()
    {
        $pdepend = self::parseProperty(__FUNCTION__);

        $class  = new ReflectionClass(__FUNCTION__);
        $native = $class->getProperties();
        $native = reset($native);

        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PRIVATE,
            $pdepend->getModifiers() & ReflectionProperty::IS_PRIVATE
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PROTECTED,
            $pdepend->getModifiers() & ReflectionProperty::IS_PROTECTED
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PUBLIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_PUBLIC
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_STATIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_STATIC
        );
    }

    /**
     * Tests that the returned modifiers are similar for PHP_Depend and the
     * native reflection api.
     *
     * @return void
     */
    public function testGetModifiersForPublicProperty()
    {
        $pdepend = self::parseProperty(__FUNCTION__);

        $class  = new ReflectionClass(__FUNCTION__);
        $native = $class->getProperties();
        $native = reset($native);

        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PRIVATE,
            $pdepend->getModifiers() & ReflectionProperty::IS_PRIVATE
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PROTECTED,
            $pdepend->getModifiers() & ReflectionProperty::IS_PROTECTED
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PUBLIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_PUBLIC
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_STATIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_STATIC
        );
    }

    /**
     * Tests that the returned modifiers are similar for PHP_Depend and the
     * native reflection api.
     *
     * @return void
     */
    public function testGetModifiersForProtectedProperty()
    {
        $pdepend = self::parseProperty(__FUNCTION__);

        $class  = new ReflectionClass(__FUNCTION__);
        $native = $class->getProperties();
        $native = reset($native);

        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PRIVATE,
            $pdepend->getModifiers() & ReflectionProperty::IS_PRIVATE
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PROTECTED,
            $pdepend->getModifiers() & ReflectionProperty::IS_PROTECTED
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PUBLIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_PUBLIC
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_STATIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_STATIC
        );
    }

    /**
     * Tests that the returned modifiers are similar for PHP_Depend and the
     * native reflection api.
     *
     * @return void
     */
    public function testGetModifiersForPrivateProperty()
    {
        $pdepend = self::parseProperty(__FUNCTION__);

        $class  = new ReflectionClass(__FUNCTION__);
        $native = $class->getProperties();
        $native = reset($native);

        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PRIVATE,
            $pdepend->getModifiers() & ReflectionProperty::IS_PRIVATE
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PROTECTED,
            $pdepend->getModifiers() & ReflectionProperty::IS_PROTECTED
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PUBLIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_PUBLIC
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_STATIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_STATIC
        );
    }

    /**
     * Tests that the returned modifiers are similar for PHP_Depend and the
     * native reflection api.
     *
     * @return void
     */
    public function testGetModifiersForPublicStaticProperty()
    {
        $pdepend = self::parseProperty(__FUNCTION__);

        $class  = new ReflectionClass(__FUNCTION__);
        $native = $class->getProperties();
        $native = reset($native);

        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PRIVATE,
            $pdepend->getModifiers() & ReflectionProperty::IS_PRIVATE
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PROTECTED,
            $pdepend->getModifiers() & ReflectionProperty::IS_PROTECTED
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PUBLIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_PUBLIC
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_STATIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_STATIC
        );
    }

    /**
     * Tests that the returned modifiers are similar for PHP_Depend and the
     * native reflection api.
     *
     * @return void
     */
    public function testGetModifiersForProtectedStaticProperty()
    {
        $pdepend = self::parseProperty(__FUNCTION__);

        $class  = new ReflectionClass(__FUNCTION__);
        $native = $class->getProperties();
        $native = reset($native);

        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PRIVATE,
            $pdepend->getModifiers() & ReflectionProperty::IS_PRIVATE
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PROTECTED,
            $pdepend->getModifiers() & ReflectionProperty::IS_PROTECTED
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PUBLIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_PUBLIC
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_STATIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_STATIC
        );
    }

    /**
     * Tests that the returned modifiers are similar for PHP_Depend and the
     * native reflection api.
     *
     * @return void
     */
    public function testGetModifiersForPrivateStaticProperty()
    {
        $pdepend = self::parseProperty(__FUNCTION__);

        $class  = new ReflectionClass(__FUNCTION__);
        $native = $class->getProperties();
        $native = reset($native);

        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PRIVATE,
            $pdepend->getModifiers() & ReflectionProperty::IS_PRIVATE
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PROTECTED,
            $pdepend->getModifiers() & ReflectionProperty::IS_PROTECTED
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_PUBLIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_PUBLIC
        );
        $this->assertSame(
            $native->getModifiers() & ReflectionProperty::IS_STATIC,
            $pdepend->getModifiers() & ReflectionProperty::IS_STATIC
        );
    }

    /**
     * Tests that the <b>setAccessible()</b> method throws an unsupported
     * method call exception.
     *
     * @return void
     */
    public function testSetAccessibleThrowsExceptionBecauseThisFeatureIsNotSupported()
    {
        $property = new PHP_Depend_Code_Property(
            $this->getMock('PHP_Depend_Code_ASTFieldDeclaration'),
            $this->getMock('PHP_Depend_Code_ASTVariableDeclarator', array(), array(null))
        );

        $this->setExpectedException(
            'ReflectionException',
            'PHP_Depend_Code_Property::setAccessible() is not supported.'
        );

        $property->setAccessible(true);
    }

    /**
     * Tests that the <b>getValue()</b> method throws an unsupported method
     * exception.
     *
     * @return void
     */
    public function testGetValueThrowsExceptionBecauseThisFeatureIsNotSupported()
    {
        $property = new PHP_Depend_Code_Property(
            $this->getMock('PHP_Depend_Code_ASTFieldDeclaration'),
            $this->getMock('PHP_Depend_Code_ASTVariableDeclarator', array(), array(null))
        );

        $this->setExpectedException(
            'ReflectionException',
            'PHP_Depend_Code_Property::getValue() is not supported.'
        );

        $property->getValue(new stdClass());
    }

    /**
     * Tests that the <b>setValue()</b> method throws an unsupported method
     * exception.
     *
     * @return void
     */
    public function testSetValueThrowsExceptionBecauseThisFeatureIsNotSupported()
    {
        $property = new PHP_Depend_Code_Property(
            $this->getMock('PHP_Depend_Code_ASTFieldDeclaration'),
            $this->getMock('PHP_Depend_Code_ASTVariableDeclarator', array(), array(null))
        );

        $this->setExpectedException(
            'ReflectionException',
            'PHP_Depend_Code_Property::setValue() is not supported.'
        );

        $property->setValue(new stdClass(), 42);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForPropertyWithoutDefaultValueIssue67()
    {
        $pdepend = self::parseProperty(__FUNCTION__);
        
        $class  = new ReflectionClass(__FUNCTION__);
        $native = $class->getProperties();
        $native = reset($native);

        $this->assertSame((string) $native, (string) $pdepend);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForPropertyWithArrayDefaultValueIssue67()
    {
        $pdepend = self::parseProperty(__FUNCTION__);

        $class  = new ReflectionClass(__FUNCTION__);
        $native = $class->getProperties();
        $native = reset($native);

        $this->assertSame((string) $native, (string) $pdepend);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForPropertyWithBooleanDefaultValueIssue67()
    {
        $pdepend = self::parseProperty(__FUNCTION__);

        $class  = new ReflectionClass(__FUNCTION__);
        $native = $class->getProperties();
        $native = reset($native);

        $this->assertSame((string) $native, (string) $pdepend);
    }

    /**
     * Tests that the output of __toString() is compatible with the native
     * reflection api.
     *
     * @return void
     */
    public function testToStringReturnsExpectedStringForPropertyDeclaredStaticIssue67()
    {
        $pdepend = self::parseProperty(__FUNCTION__);

        $class  = new ReflectionClass(__FUNCTION__);
        $native = $class->getProperties();
        $native = reset($native);

        $this->assertSame((string) $native, (string) $pdepend);
    }

    /**
     * This method will return the first property of the first class found in
     * the given file.
     *
     * @param string $testCase File name of the test file.
     *
     * @return PHP_Depend_Code_Property
     */
    protected static function parseProperty($testCase)
    {
        // Include test code for native reflection
        include_once self::createCodeResourceUri('issues/067/' . $testCase . '.php');
        
        $packages = self::parseSource('issues/067/' . $testCase . '.php');

        return $packages->current()
            ->getClasses()
            ->current()
            ->getProperties()
            ->current();
    }
}
