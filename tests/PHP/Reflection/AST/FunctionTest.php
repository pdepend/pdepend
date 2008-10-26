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

require_once dirname(__FILE__) . '/AbstractDependencyAwareTest.php';
require_once dirname(__FILE__) . '/_dummy/TestImplAstVisitor.php';

require_once 'PHP/Reflection/AST/Function.php';
require_once 'PHP/Reflection/AST/Package.php';

/**
 * Test case implementation for the PHP_Reflection_AST_Function class.
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
class PHP_Reflection_AST_FunctionTest extends PHP_Reflection_AST_AbstractDependencyAwareTest
{
    /**
     * Tests the ctor and the {@link PHP_Reflection_AST_Function::getName()} method.
     *
     * @return void
     */
    public function testCreateNewFunctionInstance()
    {
        $function = new PHP_Reflection_AST_Function('func', 0);
        $this->assertEquals('func', $function->getName());
    }
    
    /**
     * Tests that the {@link PHP_Reflection_AST_Function::getPackage()} returns as
     * default value <b>null</b> and that the package could be set and unset.
     *
     * @return void
     */
    public function testGetSetPackage()
    {
        $package  = new PHP_Reflection_AST_Package('package');
        $function = new PHP_Reflection_AST_Function('func', 0);
        
        $this->assertNull($function->getPackage());
        $function->setPackage($package);
        $this->assertSame($package, $function->getPackage());
        $function->setPackage(null);
        $this->assertNull($function->getPackage());
    }
    
    /**
     * Tests that {@link PHP_Reflection_AST_Function#getLine()} works as expected.
     *
     * @return void
     */
    public function testGetStartLineNumber()
    {
        $function = new PHP_Reflection_AST_Function('function1', 23);
        $this->assertEquals(23, $function->getLine());
    }
    
    /**
     * Tests that {@link PHP_Reflection_AST_Function#getTokens()} works as expected.
     * 
     * @return void
     */
    public function testGetTokens()
    {
        $tokens = array(
            array(PHP_Reflection_TokenizerI::T_VARIABLE, '$foo', 3),
            array(PHP_Reflection_TokenizerI::T_EQUAL, '=', 3),
            array(PHP_Reflection_TokenizerI::T_LNUMBER, '42', 3),
            array(PHP_Reflection_TokenizerI::T_SEMICOLON, ';', 3),
        );
        
        $function = new PHP_Reflection_AST_Function('function1', 42);
        $function->setTokens($tokens);
        
        $this->assertEquals($tokens, $function->getTokens());
    }
    
    /**
     * Tests the visitor accept method.
     *
     * @return void
     */
    public function testVisitorAccept()
    {
        $function = new PHP_Reflection_AST_Function('func', 0);
        $visitor  = new PHP_Reflection_AST_TestImplAstVisitor();
        
        $this->assertNull($visitor->function);
        $function->accept($visitor);
        $this->assertSame($function, $visitor->function);
    }
    
    /**
     * Creates an abstract item instance.
     *
     * @return PHP_Reflection_AST_AbstractItem
     */
    protected function createItem()
    {
        return new PHP_Reflection_AST_Function('func', 0);
    }
    
    /**
     * Generates a node instance that can handle dependencies.
     *
     * @return PHP_Reflection_AST_DependencyAwareI
     */
    protected function createDependencyNode()
    {
        return new PHP_Reflection_AST_Function('func', 0);
    }
}