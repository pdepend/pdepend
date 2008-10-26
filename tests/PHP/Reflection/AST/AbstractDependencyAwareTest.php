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

/**
 * Abstract base test case for all node types that can handle dependencies.
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
abstract class PHP_Reflection_AST_AbstractDependencyAwareTest 
       extends PHP_Reflection_AST_AbstractSourceElementTest
{
    /**
     * Tests that a new {@link PHP_Reflection_AST_DependencyAwareI} instance returns 
     * an empty {@link PHP_Reflection_AST_Iterator} for dependencies.
     *
     * @return void
     */
    public function testGetDependencyAwareIterator()
    {
        $node         = $this->createDependencyNode();
        $dependencies = $node->getDependencies();
        
        $this->assertType('PHP_Reflection_AST_Iterator', $dependencies);
        $this->assertEquals(0, $dependencies->count());
    }
    
    /**
     * Tests that the add {@link PHP_Reflection_AST_DependencyAwareI::addDependency()} 
     * adds a new depended object to the internal list, but it should accept each
     * type only once.
     * 
     * @return void
     *
     */
    public function testAddDependency()
    {
        $node = $this->createDependencyNode();
        $dep0 = new PHP_Reflection_AST_Class('dep0', 0, 'dep0.php');
        $dep1 = new PHP_Reflection_AST_Class('dep1', 0, 'dep1.php');
        
        $this->assertEquals(0, $node->getDependencies()->count());
        $node->addDependency($dep0);
        $this->assertEquals(1, $node->getDependencies()->count());
        $node->addDependency($dep0);
        $this->assertEquals(1, $node->getDependencies()->count());
        $node->addDependency($dep1);
        $this->assertEquals(2, $node->getDependencies()->count());
    }
    
    /**
     * Tests that the {@link PHP_Reflection_AST_DependencyAwareI::removeDependency()} 
     * method works as expected.
     *
     * @return void
     */
    public function testRemoveDependency()
    {
        $node = $this->createDependencyNode();
        $dep0 = new PHP_Reflection_AST_Class('dep0', 0, 'dep0.php');
        $dep1 = new PHP_Reflection_AST_Class('dep1', 0, 'dep1.php');
        
        $this->assertEquals(0, $node->getDependencies()->count());
        $node->addDependency($dep0);
        $this->assertEquals(1, $node->getDependencies()->count());
        $node->addDependency($dep1);
        $this->assertEquals(2, $node->getDependencies()->count());
        
        $node->removeDependency($dep1);
        $this->assertEquals(1, $node->getDependencies()->count());
        $node->removeDependency($dep0);
        $this->assertEquals(0, $node->getDependencies()->count());
    } 
    
    /**
     * Generates a node instance that can handle dependencies.
     *
     * @return PHP_Reflection_AST_DependencyAwareI
     */
    protected abstract function createDependencyNode();
}