<?php
/**
 * This file is part of PHP_Depend.
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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/AbstractDependencyTest.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Interface.php';

/**
 * Test case for the code interface class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_InterfaceTest extends PHP_Depend_Code_AbstractDependencyTest
{
    /**
     * Tests that {@link PHP_Depend_Code_Interface::getImplementingClasses()}
     * only returns associated classes and no interfaces.
     *
     * @return void
     */
    public function testGetImplementingClassesReturnsOnlyClasses()
    {
        $i0 = new PHP_Depend_Code_Interface('i0');
        $i1 = new PHP_Depend_Code_Interface('i1');
        $i2 = new PHP_Depend_Code_Interface('i2');
        $c0 = new PHP_Depend_Code_Class('c0');
        $c1 = new PHP_Depend_Code_Class('c1');
        
        $i0->addChildType($i1);
        $i0->addChildType($i2);
        $i0->addChildType($c0);
        $i0->addChildType($c1);
        
        $classes = $i0->getImplementingClasses();
        $this->assertEquals(2, $classes->count());
        $this->assertSame($c0, $classes->current());
        $classes->next();
        $this->assertSame($c1, $classes->current());
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Interface::getChildInterfaces()}
     * only returns associated interfaces and no classes.
     *
     * @return void
     */
    public function testGetChildInterfaces()
    {
        $i0 = new PHP_Depend_Code_Interface('i0');
        $i1 = new PHP_Depend_Code_Interface('i1');
        $i2 = new PHP_Depend_Code_Interface('i2');
        $c0 = new PHP_Depend_Code_Class('c0');
        $c1 = new PHP_Depend_Code_Class('c1');
        
        $i0->addChildType($i1);
        $i0->addChildType($i2);
        $i0->addChildType($c0);
        $i0->addChildType($c1);
        
        $interfaces = $i0->getChildInterfaces();
        $this->assertEquals(2, $interfaces->count());
        $this->assertSame($i1, $interfaces->current());
        $interfaces->next();
        $this->assertSame($i2, $interfaces->current());
    }
    
    /**
     * Tests the result of the <b>getParentInterfaces()</b> method.
     *
     * @return void
     */
    public function testGetParentInterfaces()
    {
        $interfsA = new PHP_Depend_Code_Interface('interfsA');
        $interfsB = new PHP_Depend_Code_Interface('interfsB');
        $interfsC = new PHP_Depend_Code_Interface('interfsC');
        $interfsD = new PHP_Depend_Code_Interface('interfsD');
        $interfsE = new PHP_Depend_Code_Interface('interfsE');
        $interfsF = new PHP_Depend_Code_Interface('interfsF');
        
        $interfsA->addChildType($interfsB); // interface B extends A {}
        $interfsA->addChildType($interfsC); // interface C extends A {}
        $interfsC->addChildType($interfsD); // interface D extends C, E
        $interfsE->addChildType($interfsD); // interface D extends C, E
        $interfsF->addChildType($interfsE); // interface E extends F
        
        $this->assertEquals(0, $interfsA->getParentInterfaces()->count());
        
        $parents = $interfsB->getParentInterfaces();
        $this->assertEquals(1, $parents->count());
        $this->assertSame($interfsA, $parents->current());
        
        $parents = $interfsC->getParentInterfaces();
        $this->assertEquals(1, $parents->count());
        $this->assertSame($interfsA, $parents->current());
        
        $parents = $interfsD->getParentInterfaces();
        $this->assertEquals(4, $parents->count());
        $this->assertSame($interfsA, $parents->current());
        $parents->next();
        $this->assertSame($interfsC, $parents->current());
        $parents->next();
        $this->assertSame($interfsE, $parents->current());
        $parents->next();
        $this->assertSame($interfsF, $parents->current());
        
        $parents = $interfsE->getParentInterfaces();
        $this->assertEquals(1, $parents->count());
        $this->assertSame($interfsF, $parents->current());
        
        $this->assertEquals(0, $interfsF->getParentInterfaces()->count());
    }
    
    /**
     * Tests that {@link PHP_Depend_Code_Interface::isSubtypeOf()} returns
     * <b>false</b> for an input class.
     *
     * @return void
     */
    public function testIsSubtypeOfReturnsFalseForNonParents()
    {
        $interfsA = new PHP_Depend_Code_Interface('A');
        $interfsB = new PHP_Depend_Code_Interface('B');
        $classC   = new PHP_Depend_Code_Class('C');
        
        $this->assertFalse($interfsA->isSubtypeOf($interfsB));
        
        // TODO: This should be fixed in the code and throw an exception.
        $classC->addChildType($interfsA); // interface A extends C {}
        $this->assertFalse($interfsA->isSubtypeOf($classC));
    }
    
    /**
     * Checks the {@link PHP_Depend_Code_Interface::isSubtypeOf()} method.
     *
     * @return void
     */
    public function testIsSubtypeOnInheritanceHierarchy()
    {
        $interfsA = new PHP_Depend_Code_Interface('A');
        $interfsB = new PHP_Depend_Code_Interface('B');
        $interfsC = new PHP_Depend_Code_Interface('C');
        $interfsD = new PHP_Depend_Code_Interface('D');
        $interfsE = new PHP_Depend_Code_Interface('E');
        $interfsF = new PHP_Depend_Code_Interface('F');
        
        $interfsA->addChildType($interfsB); // interface B extends A, C {}
        $interfsC->addChildType($interfsB); // interface B extends A, C {}
        $interfsD->addChildType($interfsC); // interface C extends D, E {}
        $interfsE->addChildType($interfsC); // interface C extends D, E {}
        $interfsF->addChildType($interfsA); // interface A extends F
        
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
     * @return PHP_Depend_Code_AbstractItem
     */
    protected function createItem()
    {
        return new PHP_Depend_Code_Interface('interfs');
    }
    
    /**
     * Generates a node instance that can handle dependencies.
     *
     * @return PHP_Depend_Code_DependencyNodeI
     */
    protected function createDependencyNode()
    {
        return new PHP_Depend_Code_Interface('interfs');
    }   
}