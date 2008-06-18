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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';
require_once dirname(__FILE__) . '/DefaultVisitorDummy.php';
require_once dirname(__FILE__) . '/TestListener.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/File.php';
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Property.php';

/**
 * Test case for the default visit listener implementation.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_NodeVisitor_DefaultListenerTest extends PHP_Depend_AbstractTest
{
    public function testDefaultImplementationCallsListeners()
    {
        $file = new PHP_Depend_Code_File(null);
        
        $package   = new PHP_Depend_Code_Package('package');
        $class     = $package->addType(new PHP_Depend_Code_Class('clazz'));
        $method1   = $class->addMethod(new PHP_Depend_Code_Method('m1'));
        $method2   = $class->addMethod(new PHP_Depend_Code_Method('m2'));
        $property  = $class->addProperty(new PHP_Depend_Code_Property('$p1'));
        $interface = $package->addType(new PHP_Depend_Code_Interface('interfs'));
        $method3   = $interface->addMethod(new PHP_Depend_Code_Method('m3'));
        $method4   = $interface->addMethod(new PHP_Depend_Code_Method('m4'));
        $function  = $package->addFunction(new PHP_Depend_Code_Function('func'));
        
        $class->setSourceFile($file);
        $function->setSourceFile($file);
        $interface->setSourceFile($file);
        
        $listener = new PHP_Depend_Code_NodeVisitor_TestListener();
        $visitor  = new PHP_Depend_Code_NodeVisitor_DefaultVisitorDummy();
        $visitor->addVisitListener($listener);
        $visitor->visitPackage($package);
        
        $this->assertArrayHasKey($file->getUUID() . '#start', $listener->nodes);
        $this->assertArrayHasKey($file->getUUID() . '#end', $listener->nodes);
        $this->assertArrayHasKey($package->getUUID() . '#start', $listener->nodes);
        $this->assertArrayHasKey($package->getUUID() . '#end', $listener->nodes);
        $this->assertArrayHasKey($class->getUUID() . '#start', $listener->nodes);
        $this->assertArrayHasKey($class->getUUID() . '#end', $listener->nodes);
        $this->assertArrayHasKey($function->getUUID() . '#start', $listener->nodes);
        $this->assertArrayHasKey($function->getUUID() . '#end', $listener->nodes);
        $this->assertArrayHasKey($interface->getUUID() . '#start', $listener->nodes);
        $this->assertArrayHasKey($interface->getUUID() . '#end', $listener->nodes);
        $this->assertArrayHasKey($method1->getUUID() . '#start', $listener->nodes);
        $this->assertArrayHasKey($method1->getUUID() . '#end', $listener->nodes);
        $this->assertArrayHasKey($method2->getUUID() . '#start', $listener->nodes);
        $this->assertArrayHasKey($method2->getUUID() . '#end', $listener->nodes);
        $this->assertArrayHasKey($method3->getUUID() . '#start', $listener->nodes);
        $this->assertArrayHasKey($method3->getUUID() . '#end', $listener->nodes);
        $this->assertArrayHasKey($method4->getUUID() . '#start', $listener->nodes);
        $this->assertArrayHasKey($method4->getUUID() . '#end', $listener->nodes);
        $this->assertArrayHasKey($property->getUUID() . '#start', $listener->nodes);
        $this->assertArrayHasKey($property->getUUID() . '#end', $listener->nodes);
    }
}