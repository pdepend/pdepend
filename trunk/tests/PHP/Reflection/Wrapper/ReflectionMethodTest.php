<?php
/**
 * This file is part of PHP_Reflection.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @subpackage Wrapper
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Reflection/Wrapper/ReflectionMethod.php';

/**
 * Test case for PHP's internal ReflectionMethod class wrapper.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Wrapper
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Wrapper_ReflectionMethodTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests the compatibility of the isFinal() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheIsFinalMethod()
    {
        $expected = $this->createInternalMethod('final.php');
        $actual   = $this->createMethod('final.php');
        
        $this->assertTrue($expected->isFinal());
        $this->assertEquals($expected->isFinal(), $actual->isFinal());
        $this->assertEquals(
            Reflection::getModifierNames($expected->getModifiers()), 
            Reflection::getModifierNames($actual->getModifiers())
        );
    }
    
    /**
     * Tests the compatibility of the isAbstract() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheIsAbstractMethod()
    {
        $expected = $this->createInternalMethod('abstract.php');
        $actual   = $this->createMethod('abstract.php');
        
        $this->assertTrue($expected->isAbstract());
        $this->assertEquals($expected->isAbstract(), $actual->isAbstract());
        $this->assertEquals(
            Reflection::getModifierNames($expected->getModifiers()), 
            Reflection::getModifierNames($actual->getModifiers())
        );
    }
    
    /**
     * Tests the compatibility of the isPublic() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheIsPublicMethodImplicit()
    {
        $expected = $this->createInternalMethod('public_implicit.php');
        $actual   = $this->createMethod('public_implicit.php');
        
        $this->assertTrue($expected->isPublic());
        $this->assertEquals($expected->isPublic(), $actual->isPublic());
        $this->assertEquals(
            Reflection::getModifierNames($expected->getModifiers()), 
            Reflection::getModifierNames($actual->getModifiers())
        );
    }
    
    /**
     * Tests the compatibility of the isPublic() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheIsPublicMethodExplicit()
    {
        $expected = $this->createInternalMethod('public_explicit.php');
        $actual   = $this->createMethod('public_explicit.php');
        
        $this->assertTrue($expected->isPublic());
        $this->assertEquals($expected->isPublic(), $actual->isPublic());
        $this->assertEquals(
            Reflection::getModifierNames($expected->getModifiers()), 
            Reflection::getModifierNames($actual->getModifiers())
        );
    }
    
    /**
     * Tests the compatibility of the isPrivate() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheIsPrivateMethod()
    {
        $expected = $this->createInternalMethod('private.php');
        $actual   = $this->createMethod('private.php');
        
        $this->assertTrue($expected->isPrivate());
        $this->assertEquals($expected->isPrivate(), $actual->isPrivate());
        $this->assertEquals(
            Reflection::getModifierNames($expected->getModifiers()), 
            Reflection::getModifierNames($actual->getModifiers())
        );
    }
    
    /**
     * Tests the compatibility of the isProtected() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheIsProtectedMethod()
    {
        $expected = $this->createInternalMethod('protected.php');
        $actual   = $this->createMethod('protected.php');
        
        $this->assertTrue($expected->isProtected());
        $this->assertEquals($expected->isProtected(), $actual->isProtected());
        $this->assertEquals(
            Reflection::getModifierNames($expected->getModifiers()), 
            Reflection::getModifierNames($actual->getModifiers())
        );
    }
    
    /**
     * Tests the compatibility of the isStatic() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheIsStaticMethod()
    {
        $expected = $this->createInternalMethod('static.php');
        $actual   = $this->createMethod('static.php');
        
        $this->assertTrue($expected->isStatic());
        $this->assertEquals($expected->isStatic(), $actual->isStatic());
        $this->assertEquals(
            Reflection::getModifierNames($expected->getModifiers()), 
            Reflection::getModifierNames($actual->getModifiers())
        );
    }
    
    /**
     * Test the compatibility of the isConstructor() methods for a PHP 4 ctor.
     * 
     * @return void
     */
    public function testCompatibilityOfTheIsConstructorMethodWhereClassEqualsCtor()
    {
        $expected = $this->createInternalMethod('constructor_php4.php');
        $actual   = $this->createMethod('constructor_php4.php');
        
        $this->assertTrue($expected->isConstructor());
        $this->assertEquals($expected->isConstructor(), $actual->isConstructor());
        $this->assertEquals(
            Reflection::getModifierNames($expected->getModifiers()),
            Reflection::getModifierNames($actual->getModifiers())
        );
    }
    
    /**
     * Tests the comaptibility of the isConstructor() methods for a php 4 ctor. 
     *
     * @return void
     */
    public function testCompatibilityOfTheIsConstructorMethodWithUnderscoreConstruct()
    {
        $expected = $this->createInternalMethod('constructor_php5.php');
        $actual   = $this->createMethod('constructor_php5.php');
        
        $this->assertTrue($expected->isConstructor());
        $this->assertEquals($expected->isConstructor(), $actual->isConstructor());
        $this->assertEquals(
            Reflection::getModifierNames($expected->getModifiers()),
            Reflection::getModifierNames($actual->getModifiers())
        );
    }
    
    /**
     * Tests the behavior of isConstructor() when a PHP 4 and 5 ctor exists.
     *
     * @return void
     */
    public function testCompatibilityOfTheIsConstructorMethodWhenPhp4AndPhp5CtorExists()
    {
        $php4Actual = $this->createMethod('constructor_php4_and_php5.php', 'test_wrapper_method_constructor_php4_and_php5');
        $this->assertFalse($php4Actual->isConstructor());
        $php5Actual = $this->createMethod('constructor_php4_and_php5.php', '__construct');
        $this->assertTrue($php5Actual->isConstructor());
    }
    
    /**
     * Tests the compatibility of the isDestructor() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheIsDestructorMethodForValidDtor()
    {
        $expected = $this->createInternalMethod('destructor.php');
        $actual   = $this->createMethod('destructor.php');
        
        $this->assertTrue($expected->isDestructor());
        $this->assertTrue($actual->isDestructor());
        $this->assertEquals(
            Reflection::getModifierNames($expected->getModifiers()),
            Reflection::getModifierNames($actual->getModifiers())
        );
    }
    
    /**
     * Tests the compatibility of the isDestructor() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheIsDestructorMethodForInvalidDtor()
    {
        $expected = $this->createInternalMethod('destructor_no.php');
        $actual   = $this->createMethod('destructor_no.php');
        
        $this->assertFalse($expected->isDestructor());
        $this->assertFalse($actual->isDestructor());
        $this->assertEquals(
            Reflection::getModifierNames($expected->getModifiers()),
            Reflection::getModifierNames($actual->getModifiers())
        );
    }
    
    /**
     * Tests the compatibility of the getModifiers() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheGetModifiersMethod()
    {
        $this->markTestIncomplete('There are much more modifiers than the public documentation mentions.');
    }
    
    /**
     * Tests that the getClosure() method throws an exception.
     *
     * @return void
     */
    public function testGetClosureThrowsAnException()
    {
        $this->setExpectedException(
            'ReflectionException',
            'Method getClosure() is not implemented.'
        );
        
        $actual = $this->createMethod('closure.php');
        $actual->getClosure();
    }
    
    /**
     * Tests the compatibility of the getDeclaringClass() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheGetDeclaringClassMethod()
    {
        $expected = $this->createInternalMethod('declaring_class.php');
        $actual   = $this->createMethod('declaring_class.php');
        
        $expectedClass = $expected->getDeclaringClass();
        $actualClass   = $actual->getDeclaringClass();
        
        $this->assertType(get_class($expectedClass), $actualClass);
    }
    
    /**
     * Tests the compatibility of the getFileName() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheGetFileNameMethod()
    {
        $expected = $this->createInternalMethod('filename.php');
        $actual   = $this->createMethod('filename.php');
        
        $this->assertType('string', $expected->getFileName());
        $this->assertEquals($expected->getFileName(), $actual->getFileName());
    }
    
    /**
     * Tests the compatibility of the getStartLine() and getEndLine() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheGetStartLineAndGetEndLineMethod()
    {
        $expected = $this->createInternalMethod('start_end_line.php');
        $actual   = $this->createMethod('start_end_line.php');
        
        $this->assertEquals($expected->getStartLine(), $actual->getStartLine());
        $this->assertEquals($expected->getEndLine(), $actual->getEndLine());
    }
    
    /**
     * Tests the compatibility of the getDocComment() methods.
     *
     * @return void
     */
    public function testCompatibilityOfTheGetDocCommentMethod()
    {
        $expected = $this->createInternalMethod('doc_comment.php');
        $actual   = $this->createMethod('doc_comment.php');
        
        $this->assertContains('This is a doc comment.', $expected->getDocComment());
        $this->assertEquals($expected->getDocComment(), $actual->getDocComment());
    }
    
    /**
     * Returns an instance of PHP_Reflection_Wrapper_ReflectionMethod for the
     * first class/interface defined in the given file.
     *
     * @param string $file The source file name.
     * @param string $name Optional name of the expected method.
     * 
     * @return PHP_Reflection_Wrapper_ReflectionMethod
     */
    protected function createMethod($file, $name = null)
    {
        $packages = self::parseSource('/wrapper/method/' . $file);
        $this->assertEquals(1, $packages->count());
        
        $types = $packages->current()->getTypes();
        $this->assertEquals(1, $types->count());
        
        if ($name === null) {
            $methods = $types->current()->getMethods();
            $this->assertEquals(1, $methods->count());
            
            $method = $methods->current();
        } else {
            $method = $types->current()->getMethod($name);
        }
        return new PHP_Reflection_Wrapper_ReflectionMethod($method);
    }
    
    /**
     * Returns a ReflectionMethod instance for the first method detected in the
     * given test file.
     *
     * @param string $file the test file name
     * @param string $name An optinal method name.
     * 
     * @return ReflectionMethod
     */
    protected function createInternalMethod($file, $name = null)
    {
        // Include source file
        $uri = $this->createResourceURI('/wrapper/method/' . $file);
        $this->assertType('string', $uri);
        $this->assertFileExists($uri);
        
        include $uri;
        
        // Create test class/interface name
        $class = 'test_wrapper_method_' . pathinfo($file, PATHINFO_FILENAME);

        $this->assertTrue(class_exists($class, false));
        
        $reflection = new ReflectionClass($class);

        
        if ($name === null) {
            $methods = $reflection->getMethods();
            $this->assertEquals(1, count($methods));
        
            return $methods[0];
        } else {
            return $reflection->getMethod($name);
        }
    }
}