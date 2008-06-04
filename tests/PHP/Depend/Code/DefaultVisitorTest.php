<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';
require_once dirname(__FILE__) . '/DefaultVisitorDummy.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/File.php';
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Property.php';

/**
 * Test case for the default visitor implementation.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_DefaultVisitorTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the execution order of the default visitor implementation.
     *
     * @return void
     */
    public function testDefaultVisitOrder()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        
        $package1 = new PHP_Depend_Code_Package('pkgA');
        
        $class2 = $package1->addType(new PHP_Depend_Code_Class('classB'));
        $class2->setSourceFile($file);
        $class2->addMethod(new PHP_Depend_Code_Method('methodBB'));
        $class2->addMethod(new PHP_Depend_Code_Method('methodBA'));
        $class2->addProperty(new PHP_Depend_Code_Property('propBB'));
        $class2->addProperty(new PHP_Depend_Code_Property('propBA'));
        
        $class1 = $package1->addType(new PHP_Depend_Code_Class('classA'));
        $class1->setSourceFile($file);
        $class1->addMethod(new PHP_Depend_Code_Method('methodAB'));
        $class1->addMethod(new PHP_Depend_Code_Method('methodAA'));
        $class1->addProperty(new PHP_Depend_Code_Property('propAB'));
        $class1->addProperty(new PHP_Depend_Code_Property('propAA'));
        
        $package2 = new PHP_Depend_Code_Package('pkgB');
        
        $interface1 = $package2->addType(new PHP_Depend_Code_Interface('interfsC'));
        
        $function1 = $package2->addFunction(new PHP_Depend_Code_Function('funcD'));
        $function1->setSourceFile($file);
        
        $interface1->setSourceFile($file);
        $interface1->addMethod(new PHP_Depend_Code_Method('methodCB'));
        $interface1->addMethod(new PHP_Depend_Code_Method('methodCA'));
        
        $visitor = new PHP_Depend_Code_DefaultVisitorDummy();        
        foreach (array($package1, $package2) as $package) {
            $package->accept($visitor);
        }
        
        $expected = array(
            'pkgA',
            'classA',
            __FILE__,
            'propAA',
            'propAB',
            'methodAA',
            'methodAB',
            'classB',
            __FILE__,
            'propBA',
            'propBB',
            'methodBA',
            'methodBB',
            'pkgB',
            'interfsC',
            __FILE__,
            'methodCA',
            'methodCB',
            'funcD',
            __FILE__
        );
        
        $this->assertEquals($expected, $visitor->visits);
    }
}