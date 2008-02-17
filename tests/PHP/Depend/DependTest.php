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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once 'PHP/Depend.php';
require_once 'PHP/Depend/Util/FileExtensionFilter.php';

/**
 * Test case for PHP_Depend facade.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_DependTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the {@link PHP_Depend::addDirectory()} method fails with an
     * exception for an invalid directory.
     *
     * @return void
     */
    public function testAddInvalidDirectoryFail()
    {
        $this->setExpectedException('RuntimeException', 'Invalid directory added.');
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/foobar');
    }
    /**
     * Tests that the {@link PHP_Depend::addDirectory()} method with an existing
     * directory.
     *
     * @return void
     */
    public function testAddDirectory()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-5.2.x');
    }
    /**
     * Tests that the {@link PHP_Depend::addFilter()} method with an extension
     * filter.
     *
     * @return void
     */
    public function testAddFilter()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addFilter(new PHP_Depend_Util_FileExtensionFilter(array('php')));
    }
    
    /**
     * Tests the {@link PHP_Depend::analyze()} method and the return value. 
     *
     * @return void
     */
    public function testAnalyze()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-5.2.x');
        $pdepend->addFilter(new PHP_Depend_Util_FileExtensionFilter(array('php')));
        
        $metrics = $pdepend->analyze();
        
        $expected = array(
            'package1'                                    =>  true,
            'package2'                                    =>  true,
            'package3'                                    =>  true,
            PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE  =>  true
        );
        
        $this->assertType('Iterator', $metrics);
        foreach ($metrics as $metric) {
            $this->assertType('PHP_Depend_Metrics_PackageMetrics', $metric);
            
            unset($expected[$metric->getName()]);
        }
        
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that the {@link PHP_Depend::containsCycles()} method returns 
     * <b>true</b> for source code with cycles.
     *
     * @return void
     */
    public function testContainsCyclesWithCodeThatContainsACycle()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-with-cycle');
        $pdepend->analyze();
        
        $this->assertTrue($pdepend->containsCycles());
    }
    
    /**
     * Tests that the {@link PHP_Depend::containsCycles()} method returns 
     * <b>false</b> for source code without cycles.
     *
     * @return void
     */
    public function testContainsCyclesWithCodeThatDoesNotContainACycle()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-without-cycle');
        $pdepend->analyze();
        
        $this->assertFalse($pdepend->containsCycles());
    }
    
    /**
     * Tests that the {@link PHP_Depend::containsCycles()} method fails with an
     * exception if the code was not analyzed before.
     *
     * @return void
     */
    public function testContainsCyclesWithoutAnalyzeFail()
    {
        $this->setExpectedException('RuntimeException', 'containsCycles() doesn\'t work before the source was analyzed.');
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-5.2.x');
        $pdepend->containsCycles();
    }
    
    /**
     * Tests that the {@link PHP_Depend::countClasses()} method returns the 
     * expected number of classes.
     *
     * @return void
     */
    public function testCountClasses()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-5.2.x');
        $pdepend->analyze();
        
        $this->assertEquals(10, $pdepend->countClasses());
    }
    
    /**
     * Tests that the {@link PHP_Depend::countClasses()} method fails with an
     * exception if the code was not analyzed before.
     *
     * @return void
     */
    public function testCountClassesWithoutAnalyzeFail()
    {
        $this->setExpectedException('RuntimeException', 'countClasses() doesn\'t work before the source was analyzed.');
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-5.2.x');
        $pdepend->countClasses();
    }
    
    /**
     * Tests that the {@link PHP_Depend::countPackages()} method returns the 
     * expected number of packages.
     *
     * @return void
     */
    public function testCountPackages()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-5.2.x');
        $pdepend->analyze();
        
        $this->assertEquals(4, $pdepend->countPackages());
    }
    
    /**
     * Tests that the {@link PHP_Depend::countPackages()} method fails with an
     * exception if the code was not analyzed before.
     *
     * @return void
     */
    public function testCountPackagesWithoutAnalyzeFail()
    {
        $this->setExpectedException('RuntimeException', 'countPackages() doesn\'t work before the source was analyzed.');
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-5.2.x');
        $pdepend->countPackages();
    }
    
    /**
     * Tests that the {@link PHP_Depend::getPackage()} method returns the 
     * expected {@link PHP_Depend_Metrics_PackageMetrics} objects.
     *
     * @return void
     */
    public function testGetPackage()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-5.2.x');
        $pdepend->analyze();
        
        $this->assertType('PHP_Depend_Metrics_PackageMetrics', $pdepend->getPackage('package1'));
        $this->assertType('PHP_Depend_Metrics_PackageMetrics', $pdepend->getPackage('package2'));
        $this->assertType('PHP_Depend_Metrics_PackageMetrics', $pdepend->getPackage('package3'));
        $this->assertType('PHP_Depend_Metrics_PackageMetrics', $pdepend->getPackage(PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE));
    }
    
    /**
     * Tests that the {@link PHP_Depend::getPackage()} method fails with an
     * exception if the code was not analyzed before.
     *
     * @return void
     */
    public function testGetPackageWithoutAnalyzeFail()
    {
        $this->setExpectedException('RuntimeException', 'getPackage() doesn\'t work before the source was analyzed.');
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-5.2.x');
        $pdepend->getPackage('package1');
    }
    
    /**
     * Tests that the {@link PHP_Depend::getPackage()} method fails with an
     * exception if you request an invalid package.
     *
     * @return void
     */
    public function testGetPackageWithUnknownPackageFail()
    {
        $this->setExpectedException('OutOfBoundsException', 'Unknown package "package0".');
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-5.2.x');
        $pdepend->analyze();
        $pdepend->getPackage('package0');
    }
    
    /**
     * Tests that the {@link PHP_Depend::getPackages()} method returns the 
     * expected {@link PHP_Depend_Metrics_PackageMetrics} objects and reuses
     * the result of {@link PHP_Depend::analyze()}.
     *
     * @return void
     */
    public function testGetPackages()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-5.2.x');
        
        $metrics0 = $pdepend->analyze();
        $metrics1 = $pdepend->getPackages();
        
        $this->assertNotNull($metrics0);
        $this->assertNotNull($metrics1);
        
        $this->assertSame($metrics0, $metrics1);
    }
    
    /**
     * Tests that the {@link PHP_Depend::getPackages()} method fails with an
     * exception if the code was not analyzed before.
     *
     * @return void
     */
    public function testGetPackagesWithoutAnalyzeFail()
    {
        $this->setExpectedException('RuntimeException', 'getPackages() doesn\'t work before the source was analyzed.');
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/data/code-5.2.x');
        $pdepend->getPackages();
    }
}