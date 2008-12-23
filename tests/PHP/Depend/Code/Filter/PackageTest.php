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

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Filter/Package.php';

/**
 * Test case for the {@link PHP_Depend_Code_Filter_Package} class.
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
class PHP_Depend_Code_Filter_PackageTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the package filter works a expected for packages.
     *
     * @return void
     */
    public function testFilterPackageIterator()
    {
        $pkgIn1  = new PHP_Depend_Code_Package('in1');
        $pkgIn2  = new PHP_Depend_Code_Package('in2');
        $pkgOut1 = new PHP_Depend_Code_Package('out1');
        $pkgOut2 = new PHP_Depend_Code_Package('out2');
        
        $packages = array($pkgIn1, $pkgIn2, $pkgOut1, $pkgOut2);
        $iterator = new PHP_Depend_Code_NodeIterator($packages);
        
        $filter = new PHP_Depend_Code_Filter_Package(array('out1', 'out2'));
        $iterator->addFilter($filter);
        
        $expected = array('in1'  =>  true, 'in2'  =>  true);
        
        foreach ($iterator as $pkg) {
            $this->assertArrayHasKey($pkg->getName(), $expected);
            unset($expected[$pkg->getName()]);
        }
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the package filter works as expected on a class and interface
     * iterator.
     *
     * @return void
     */
    public function testFilterClassAndInterfaceIterator()
    {
        $pkgIn  = new PHP_Depend_Code_Package('in');
        $clsIn1 = $pkgIn->addType(new PHP_Depend_Code_Class('in1'));
        $clsIn2 = $pkgIn->addType(new PHP_Depend_Code_Interface('in2'));
        
        $pkgOut  = new PHP_Depend_Code_Package('out');
        $clsOut1 = $pkgOut->addType(new PHP_Depend_Code_Class('out1'));
        $clsOut2 = $pkgOut->addType(new PHP_Depend_Code_Interface('out2'));
        
        $classes  = array($clsIn1, $clsIn2, $clsOut1, $clsOut2);
        $iterator = new PHP_Depend_Code_NodeIterator($classes);
        
        $filter = new PHP_Depend_Code_Filter_Package(array('out'));
        $iterator->addFilter($filter);
        
        $expected = array('in1'  =>  true, 'in2'  =>  true);
        
        foreach ($iterator as $cls) {
            $this->assertArrayHasKey($cls->getName(), $expected);
            unset($expected[$cls->getName()]);
        }
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests the package filter for functions.
     *
     * @return void
     */
    public function testFilterFunctionIterator()
    {
        $pkgIn  = new PHP_Depend_Code_Package('in');
        $fcnIn1 = $pkgIn->addFunction(new PHP_Depend_Code_Function('in1'));
        $fcnIn2 = $pkgIn->addFunction(new PHP_Depend_Code_Function('in2'));
        
        $pkgOut  = new PHP_Depend_Code_Package('out');
        $fcnOut1 = $pkgOut->addFunction(new PHP_Depend_Code_Function('out1'));
        $fcnOut2 = $pkgOut->addFunction(new PHP_Depend_Code_Function('out2'));
        
        $functions = array($fcnIn1, $fcnIn2, $fcnOut1, $fcnOut2);
        $iterator  = new PHP_Depend_Code_NodeIterator($functions);
        
        $filter = new PHP_Depend_Code_Filter_Package(array('out'));
        $iterator->addFilter($filter);
        
        $expected = array('in1'  =>  true, 'in2'  =>  true);
        
        foreach ($iterator as $fcn) {
            $this->assertArrayHasKey($fcn->getName(), $expected);
            unset($expected[$fcn->getName()]);
        }
        $this->assertEquals(0, count($expected));
    }
    
    public function testFilterMethodIterator()
    {
        $pkgIn  = new PHP_Depend_Code_Package('in');
        $clsIn  = $pkgIn->addType(new PHP_Depend_Code_Class('in'));
        $mtdIn1 = $clsIn->addMethod(new PHP_Depend_Code_Method('in1'));
        $mtdIn2 = $clsIn->addMethod(new PHP_Depend_Code_Method('in2'));
        
        $pkgOut  = new PHP_Depend_Code_Package('out');
        $clsOut  = $pkgOut->addType(new PHP_Depend_Code_Class('out'));
        $mtdOut1 = $clsOut->addMethod(new PHP_Depend_Code_Method('out1'));
        $mtdOut2 = $clsOut->addMethod(new PHP_Depend_Code_Method('out2'));
        
        $methods  = array($mtdIn1, $mtdIn2, $mtdOut1, $mtdOut2);
        $iterator = new PHP_Depend_Code_NodeIterator($methods);
        
        $filter = new PHP_Depend_Code_Filter_Package(array('out'));
        $iterator->addFilter($filter);
        
        $expected = array('in1'  =>  true, 'in2'  =>  true);
        
        foreach ($iterator as $mtd) {
            $this->assertArrayHasKey($mtd->getName(), $expected);
            unset($expected[$mtd->getName()]);
        }
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the package filter works with wild cards.
     *
     * @return void
     */
    public function testFilterPackageWithWildcard()
    {
        $pkgIn1  = new PHP_Depend_Code_Package('ezcGraph');
        $pkgIn2  = new PHP_Depend_Code_Package('Zend_Controller');
        $pkgOut1 = new PHP_Depend_Code_Package('PHP_Depend_Code');
        $pkgOut2 = new PHP_Depend_Code_Package('PHP_Depend_Metrics');
        
        $packages = array($pkgIn1, $pkgIn2, $pkgOut1, $pkgOut2);
        $iterator = new PHP_Depend_Code_NodeIterator($packages);
        
        $filter = new PHP_Depend_Code_Filter_Package(array('ezc*', 'Zend_*'));
        $iterator->addFilter($filter);
        
        $expected = array('PHP_Depend_Code'  =>  true, 'PHP_Depend_Metrics'  =>  true);
        
        foreach ($iterator as $mtd) {
            $this->assertArrayHasKey($mtd->getName(), $expected);
            unset($expected[$mtd->getName()]);
        }
        $this->assertEquals(0, count($expected));
    }
}