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
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/AbstractSourceElementTest.php';

require_once 'PHP/Reflection/AST/Class.php';
require_once 'PHP/Reflection/AST/ClassOrInterfaceConstant.php';
require_once 'PHP/Reflection/AST/Interface.php';
require_once 'PHP/Reflection/AST/Method.php';

/**
 * Test case for the code interface class.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_AST_InterfaceTest extends PHP_Reflection_AST_AbstractSourceElementTest 
{
    /**
     * Tests the constant getter method.
     *
     * @return void
     */
    public function testGetConstant()
    {
        $const1 = new PHP_Reflection_AST_ClassOrInterfaceConstant('const1');
        $const2 = new PHP_Reflection_AST_ClassOrInterfaceConstant('const2');
        
        $interface = new PHP_Reflection_AST_Interface('iface');
        $interface->addConstant($const1);
        $interface->addConstant($const2);
        
        $this->assertSame($const1, $interface->getConstant('const1'));
        $this->assertSame($const2, $interface->getConstant('const2'));
    }
    
    /**
     * Tests the constant getter method with a not defined constant, which should
     * result in an exception.
     *
     * @return void
     */
    public function testGetConstantForInvalidConstantNameFail()
    {
        $interface = new PHP_Reflection_AST_Interface('iface');
        
        $this->setExpectedException(
            'PHP_Reflection_Exceptions_UnknownNodeException',
            'Unknown child node requested: const1'
        );
        $interface->getConstant('const1');
    }
    
    /**
     * Tests the method getter method.
     *
     * @return void
     */
    public function testGetMethod()
    {
        $method1 = new PHP_Reflection_AST_Method('method1');
        $method2 = new PHP_Reflection_AST_Method('method2');
        
        $interface = new PHP_Reflection_AST_Interface('iface');
        $interface->addMethod($method1);
        $interface->addMethod($method2);
        
        $this->assertSame($method1, $interface->getMethod('method1'));
        $this->assertSame($method2, $interface->getMethod('method2'));
    }
    
    /**
     * Tests the method getter method with a not defined method, which should
     * result in an exception.
     *
     * @return void
     */
    public function testGetMethodForInvalidConstantNameFail()
    {
        $interface = new PHP_Reflection_AST_Interface('iface');
        
        $this->setExpectedException(
            'PHP_Reflection_Exceptions_UnknownNodeException',
            'Unknown child node requested: method1'
        );
        $interface->getMethod('method1');
    }
    
    /**
     * Tests the result of the <b>getParentInterfaces()</b> method.
     *
     * @return void
     */
    public function testGetParentInterfaces()
    {
        $interfsA = new PHP_Reflection_AST_Interface('interfsA');
        $interfsB = new PHP_Reflection_AST_Interface('interfsB');
        $interfsC = new PHP_Reflection_AST_Interface('interfsC');
        $interfsD = new PHP_Reflection_AST_Interface('interfsD');
        $interfsE = new PHP_Reflection_AST_Interface('interfsE');
        $interfsF = new PHP_Reflection_AST_Interface('interfsF');
        
        $interfsB->addParentInterface($interfsA); // interface B extends A {}
        $interfsC->addParentInterface($interfsA); // interface C extends A {}
        $interfsD->addParentInterface($interfsC); // interface D extends C, E
        $interfsD->addParentInterface($interfsE); // interface D extends C, E
        $interfsE->addParentInterface($interfsF); // interface E extends F
        
        $this->assertEquals(0, $interfsA->getParentInterfaces()->count());
        
        $parents = $interfsB->getParentInterfaces();
        $this->assertEquals(1, $parents->count());
        $this->assertSame($interfsA, $parents->current());
        
        $parents = $interfsC->getParentInterfaces();
        $this->assertEquals(1, $parents->count());
        $this->assertSame($interfsA, $parents->current());
        
        $parents = $interfsD->getParentInterfaces();
        $this->assertEquals(4, $parents->count());
        $this->assertSame($interfsC, $parents->current());
        $parents->next();
        $this->assertSame($interfsE, $parents->current());
        $parents->next();
        $this->assertSame($interfsA, $parents->current());
        $parents->next();
        $this->assertSame($interfsF, $parents->current());
        
        $parents = $interfsE->getParentInterfaces();
        $this->assertEquals(1, $parents->count());
        $this->assertSame($interfsF, $parents->current());
        
        $this->assertEquals(0, $interfsF->getParentInterfaces()->count());
    }
    
    /**
     * Tests that {@link PHP_Reflection_AST_Interface::isSubtypeOf()} returns
     * <b>false</b> for an input class.
     *
     * @return void
     */
    public function testIsSubtypeOfReturnsFalseForNonParents()
    {
        $interfsA = new PHP_Reflection_AST_Interface('A');
        $interfsB = new PHP_Reflection_AST_Interface('B');
        $classC   = new PHP_Reflection_AST_Class('C');
        
        $this->assertFalse($interfsA->isSubtypeOf($interfsB));
        
        // TODO: This should be fixed in the code and throw an exception.
        $classC->addImplementedInterface($interfsA); // interface A extends C {}
        $this->assertFalse($interfsA->isSubtypeOf($classC));
    }
    
    /**
     * Checks the {@link PHP_Reflection_AST_Interface::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy()
    {
        $interfsA = new PHP_Reflection_AST_Interface('A');
        $interfsB = new PHP_Reflection_AST_Interface('B');
        $interfsC = new PHP_Reflection_AST_Interface('C');
        $interfsD = new PHP_Reflection_AST_Interface('D');
        $interfsE = new PHP_Reflection_AST_Interface('E');
        $interfsF = new PHP_Reflection_AST_Interface('F');
        
        $interfsB->addParentInterface($interfsA); // interface B extends A, C {}
        $interfsB->addParentInterface($interfsC); // interface B extends A, C {}
        $interfsC->addParentInterface($interfsD); // interface C extends D, E {}
        $interfsC->addParentInterface($interfsE); // interface C extends D, E {}
        $interfsA->addParentInterface($interfsF); // interface A extends F
        
        $this->assertTrue($interfsA->isSubtypeOf($interfsA));
        $this->assertFalse($interfsA->isSubtypeOf($interfsB));
        $this->assertFalse($interfsA->isSubtypeOf($interfsC));
        $this->assertFalse($interfsA->isSubtypeOf($interfsD));
        $this->assertFalse($interfsA->isSubtypeOf($interfsE));
        $this->assertTrue($interfsA->isSubtypeOf($interfsF));
        
        $this->assertTrue($interfsB->isSubtypeOf($interfsA));
        $this->assertTrue($interfsB->isSubtypeOf($interfsB));
        $this->assertTrue($interfsB->isSubtypeOf($interfsC));
        $this->assertTrue($interfsB->isSubtypeOf($interfsD));
        $this->assertTrue($interfsB->isSubtypeOf($interfsE));
        $this->assertTrue($interfsB->isSubtypeOf($interfsF));
        
        $this->assertFalse($interfsC->isSubtypeOf($interfsA));
        $this->assertFalse($interfsC->isSubtypeOf($interfsB));
        $this->assertTrue($interfsC->isSubtypeOf($interfsC));
        $this->assertTrue($interfsC->isSubtypeOf($interfsD));
        $this->assertTrue($interfsC->isSubtypeOf($interfsE));
        $this->assertFalse($interfsC->isSubtypeOf($interfsF));
        
        $this->assertFalse($interfsD->isSubtypeOf($interfsA));
        $this->assertFalse($interfsD->isSubtypeOf($interfsB));
        $this->assertFalse($interfsD->isSubtypeOf($interfsC));
        $this->assertTrue($interfsD->isSubtypeOf($interfsD));
        $this->assertFalse($interfsD->isSubtypeOf($interfsE));
        $this->assertFalse($interfsD->isSubtypeOf($interfsF));
        
        $this->assertFalse($interfsE->isSubtypeOf($interfsA));
        $this->assertFalse($interfsE->isSubtypeOf($interfsB));
        $this->assertFalse($interfsE->isSubtypeOf($interfsC));
        $this->assertFalse($interfsE->isSubtypeOf($interfsD));
        $this->assertTrue($interfsE->isSubtypeOf($interfsE));
        $this->assertFalse($interfsE->isSubtypeOf($interfsF));
        
        $this->assertFalse($interfsF->isSubtypeOf($interfsA));
        $this->assertFalse($interfsF->isSubtypeOf($interfsB));
        $this->assertFalse($interfsF->isSubtypeOf($interfsC));
        $this->assertFalse($interfsF->isSubtypeOf($interfsD));
        $this->assertFalse($interfsF->isSubtypeOf($interfsE));
        $this->assertTrue($interfsF->isSubtypeOf($interfsF));
    }
    
    /**
     * Creates an abstract item instance.
     *
     * @return PHP_Reflection_AST_AbstractSourceElement
     */
    protected function createItem()
    {
        return new PHP_Reflection_AST_Interface('interfs');
    }
    
    /**
     * Generates a node instance that can handle dependencies.
     *
     * @return PHP_Reflection_AST_DependencyAwareI
     */
    protected function createDependencyNode()
    {
        return new PHP_Reflection_AST_Interface('interfs');
    }   
}