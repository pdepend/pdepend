<?php
/**
 * This file is part of PHP_Reflection.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @subpackage Visitor
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';
require_once dirname(__FILE__) . '/_dummy/TestImplAbstractVisitor.php';

require_once 'PHP/Reflection/AST/Class.php';
require_once 'PHP/Reflection/AST/File.php';
require_once 'PHP/Reflection/AST/Function.php';
require_once 'PHP/Reflection/AST/Interface.php';
require_once 'PHP/Reflection/AST/Method.php';
require_once 'PHP/Reflection/AST/Iterator.php';
require_once 'PHP/Reflection/AST/Package.php';
require_once 'PHP/Reflection/AST/Property.php';

/**
 * Test case for the default visitor implementation.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Visitor
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Visitor_ImplAbstractVisitorTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests the execution order of the default visitor implementation.
     *
     * @return void
     */
    public function testDefaultVisitOrder()
    {
        $file = new PHP_Reflection_AST_File(__FILE__);
        
        $package1 = new PHP_Reflection_AST_Package('pkgA');
        
        $class2 = $package1->addType(new PHP_Reflection_AST_Class('classB'));
        $class2->setSourceFile($file);
        $class2->addMethod(new PHP_Reflection_AST_Method('methodBB'));
        $class2->addMethod(new PHP_Reflection_AST_Method('methodBA'));
        $class2->addProperty(new PHP_Reflection_AST_Property('propBB'));
        $class2->addProperty(new PHP_Reflection_AST_Property('propBA'));
        
        $class1 = $package1->addType(new PHP_Reflection_AST_Class('classA'));
        $class1->setSourceFile($file);
        $class1->addMethod(new PHP_Reflection_AST_Method('methodAB'));
        $class1->addMethod(new PHP_Reflection_AST_Method('methodAA'));
        $class1->addProperty(new PHP_Reflection_AST_Property('propAB'));
        $class1->addProperty(new PHP_Reflection_AST_Property('propAA'));
        
        $package2 = new PHP_Reflection_AST_Package('pkgB');
        
        $interface1 = $package2->addType(new PHP_Reflection_AST_Interface('interfsC'));
        
        $function1 = $package2->addFunction(new PHP_Reflection_AST_Function('funcD'));
        $function1->setSourceFile($file);
        
        $interface1->setSourceFile($file);
        $interface1->addMethod(new PHP_Reflection_AST_Method('methodCB'));
        $interface1->addMethod(new PHP_Reflection_AST_Method('methodCA'));
        
        $visitor = new PHP_Reflection_Visitor_TestImplAbstractVisitor();        
        foreach (array($package1, $package2) as $package) {
            $package->accept($visitor);
        }
        
        $expected = array(
            'pkgA',
            'classB',
            __FILE__,
            'propBB',
            'propBA',
            'methodBB',
            'methodBA',
            'classA',
            __FILE__,
            'propAB',
            'propAA',
            'methodAB',
            'methodAA',
            'pkgB',
            'interfsC',
            __FILE__,
            'methodCB',
            'methodCA',
            'funcD',
            __FILE__
        );
        
        $this->assertEquals($expected, $visitor->visits);
    }
}