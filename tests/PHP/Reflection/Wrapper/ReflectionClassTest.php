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

/**
 * Test case for the reflection class wrapper.
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
class PHP_Reflection_Wrapper_ReflectionClassTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests the compatiblity of the getMethods() methods.
     *
     * @return void
     */
    public function testCompatibilityOfGetMethodsNotFiltered()
    {
        $expected = $this->createInternalClass('methods_not_filtered.php');
        $actual   = $this->createClass('methods_not_filtered.php');
        
        $expectedMethods = $expected->getMethods();
        $actualMethods   = $actual->getMethods();
        
        $this->assertEquals(1, count($expectedMethods));
        $this->assertEquals(1, count($actualMethods));
        $this->assertEquals($expectedMethods[0]->getName(), $actualMethods[0]->getName());
    }
    
    /**
     * Tests the compatiblity of the getMethods() methods.
     *
     * @return void
     */
    public function testCompatibilityOfGetMethodsFilteredByPublicAndStatic()
    {
        $expected = $this->createInternalClass('methods_filtered_public_static.php');
        $actual   = $this->createClass('methods_filtered_public_static.php');
        
        $expectedMethods = $expected->getMethods(ReflectionMethod::IS_PUBLIC|ReflectionMethod::IS_STATIC);
        $actualMethods   = $actual->getMethods(ReflectionMethod::IS_PUBLIC|ReflectionMethod::IS_STATIC);
        
        $this->assertEquals(3, count($expectedMethods));
        $this->assertEquals(3, count($actualMethods));
    }

    /**
     * Tests the compatiblity of the getMethods() methods.
     *
     * @return void
     */
    public function testCompatibilityOfGetMethodsFilteredByFinal()
    {
        $expected = $this->createInternalClass('methods_filtered_final.php');
        $actual   = $this->createClass('methods_filtered_final.php');
        
        $expectedMethods = $expected->getMethods(ReflectionMethod::IS_FINAL);
        $actualMethods   = $actual->getMethods(ReflectionMethod::IS_FINAL);
        
        $this->assertEquals(1, count($expectedMethods));
        $this->assertEquals(1, count($actualMethods));
        $this->assertEquals($expectedMethods[0]->getName(), $actualMethods[0]->getName());
    }
    
    /**
     * Tests the compatibility of the hasMethod() methods.
     *
     * @return void
     */
    public function testCompatiblityOfHasMethodWithInheritedMethod()
    {
        $expected = $this->createInternalClass('has_method_with_inheritance.php');
        $actual   = $this->createClass('has_method_with_inheritance.php');
        
        $this->assertTrue($expected->hasMethod('current'));
        $this->assertTrue($actual->hasMethod('current'));

        $this->assertFalse($expected->hasMethod('foobar'));
        $this->assertFalse($actual->hasMethod('foobar'));
    }
    
    /**
     * Tests the compatibility of the hasMethod() methods.
     *
     * @return void
     */
    public function testCompatibilityOfHasMethodWithInheritedInterfaceMethods()
    {
        $expected = $this->createInternalClass('has_method_with_interface_inheritance.php');
        $actual   = $this->createClass('has_method_with_interface_inheritance.php');
        
        $this->assertTrue($expected->hasMethod('current'));
        $this->assertTrue($actual->hasMethod('current'));

        $this->assertFalse($expected->hasMethod('foobar'));
        $this->assertFalse($actual->hasMethod('foobar'));
    }
    
    /**
     * Returns an instance of PHP_Reflection_Wrapper_ReflectionClass for the
     * first defined class.
     *
     * @param string $file The source file name.
     * 
     * @return PHP_Reflection_Wrapper_ReflectionClass
     */
    protected function createClass($file)
    {
        $packages = self::parseSource('/wrapper/class/' . $file);
        $this->assertEquals(1, $packages->count());
        
        $types = $packages->current()->getTypes();
        $this->assertEquals(1, $types->count());
        
        return new PHP_Reflection_Wrapper_ReflectionClass($types->current());
    }
    
    /**
     * Returns a ReflectionClass instance for first defined class.
     *
     * @param string $file the test file name
     * 
     * @return ReflectionClass
     */
    protected function createInternalClass($file)
    {
        // Include source file
        $uri = $this->createResourceURI('/wrapper/class/' . $file);
        $this->assertType('string', $uri);
        $this->assertFileExists($uri);
        
        include $uri;
        
        // Create test class/interface name
        $class = 'test_wrapper_class_' . pathinfo($file, PATHINFO_FILENAME);

        $this->assertTrue(class_exists($class, false));
        
        return new ReflectionClass($class);
    }
}