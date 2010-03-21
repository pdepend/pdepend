<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once 'PHP/Depend.php';
require_once 'PHP/Depend/Input/ExtensionFilter.php';
require_once 'PHP/Depend/Storage/EngineI.php';

/**
 * Test case for PHP_Depend facade.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
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
        $dir = dirname(__FILE__) . '/foobar';
        $msg = "Invalid directory '{$dir}' added.";
        
        $this->setExpectedException('InvalidArgumentException', $msg);
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory($dir);
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
        $pdepend->addDirectory(dirname(__FILE__) . '/_code/code-5.2.x');
    }
    
    /**
     * Tests the {@link PHP_Depend::analyze()} method and the return value. 
     *
     * @return void
     */
    public function testAnalyze()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/_code/code-5.2.x');
        $pdepend->addFileFilter(new PHP_Depend_Input_ExtensionFilter(array('php')));
        
        $metrics = $pdepend->analyze();
        
        $expected = array(
            'package1'                                     =>  true,
            'package2'                                     =>  true,
            'package3'                                     =>  true
        );
        
        $this->assertType('Iterator', $metrics);
        foreach ($metrics as $metric) {
            $this->assertType('PHP_Depend_Code_Package', $metric);
            
            unset($expected[$metric->getName()]);
        }
        
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that {@PHP_Depend::analyze()} throws an exception if no source
     * directory was set.
     *
     * @return void
     */
    public function testAnalyzeThrowsAnExceptionForNoSourceDirectory()
    {
        $pdepend = new PHP_Depend();
        $this->setExpectedException('RuntimeException', 'No source directory and file set.');
        $pdepend->analyze();
    }
    
    /**
     * testAnalyzerReturnsEmptyIteratorWhenNoPackageExists
     *
     * @return void
     * @covers PHP_Depend
     * @group pdepend
     * @group unittest
     */
    public function testAnalyzerReturnsEmptyIteratorWhenNoPackageExists()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/_code/code-without-comments');
        $pdepend->addFileFilter(new PHP_Depend_Input_ExtensionFilter(array(__METHOD__)));
       
        $this->assertEquals(0, $pdepend->analyze()->count()); 
    }
    
    /**
     * Tests that {PHP_Depend::analyze()} configures the ignore annotations
     * option correct.
     *
     * @return void
     */
    public function testAnalyzeSetsWithoutAnnotations()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/_code');
        $pdepend->addFileFilter(new PHP_Depend_Input_ExtensionFilter(array('inc')));
        $pdepend->setWithoutAnnotations();
        $packages = $pdepend->analyze();
        
        $this->assertEquals(2, $packages->count());
        $this->assertEquals('pdepend.test', $packages->current()->getName());
        
        $function = $packages->current()->getFunctions()->current();
        
        $this->assertNotNull($function);
        $this->assertEquals('foo', $function->getName());
        $this->assertEquals(0, $function->getExceptionClasses()->count());
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
        $pdepend->addDirectory(dirname(__FILE__) . '/_code/code-5.2.x');
        $pdepend->addFileFilter(new PHP_Depend_Input_ExtensionFilter(array('php')));
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
        $this->setExpectedException(
            'RuntimeException', 
            'countClasses() doesn\'t work before the source was analyzed.'
        );
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/_code/code-5.2.x');
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
        $pdepend->addDirectory(dirname(__FILE__) . '/_code/code-5.2.x');
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
        $this->setExpectedException(
            'RuntimeException', 
            'countPackages() doesn\'t work before the source was analyzed.'
        );
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/_code/code-5.2.x');
        $pdepend->countPackages();
    }
    
    /**
     * Tests that the {@link PHP_Depend::getPackage()} method returns the 
     * expected {@link PHP_Depend_Code_Package} objects.
     *
     * @return void
     */
    public function testGetPackage()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/_code/code-5.2.x');
        $pdepend->analyze();
        
        $packages = array(
            'package1', 
            'package2', 
            'package3'
        );
        
        $className = 'PHP_Depend_Code_Package';
        
        foreach ($packages as $package) {
            $this->assertType($className, $pdepend->getPackage($package));
        }
    }
    
    /**
     * Tests that the {@link PHP_Depend::getPackage()} method fails with an
     * exception if the code was not analyzed before.
     *
     * @return void
     */
    public function testGetPackageWithoutAnalyzeFail()
    {
        $this->setExpectedException(
            'RuntimeException', 
            'getPackage() doesn\'t work before the source was analyzed.'
        );
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/_code/code-5.2.x');
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
        $this->setExpectedException(
            'OutOfBoundsException', 
            'Unknown package "package0".'
        );
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/_code/code-5.2.x');
        $pdepend->analyze();
        $pdepend->getPackage('package0');
    }
    
    /**
     * Tests that the {@link PHP_Depend::getPackages()} method returns the 
     * expected {@link PHP_Depend_Code_Package} objects and reuses the result of
     * {@link PHP_Depend::analyze()}.
     *
     * @return void
     */
    public function testGetPackages()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/_code/code-5.2.x');
        
        $package1 = $pdepend->analyze();
        $package2 = $pdepend->getPackages();
        
        $this->assertNotNull($package1);
        $this->assertNotNull($package2);
        
        $this->assertSame($package1, $package2);
    }
    
    /**
     * Tests that the {@link PHP_Depend::getPackages()} method fails with an
     * exception if the code was not analyzed before.
     *
     * @return void
     */
    public function testGetPackagesWithoutAnalyzeFail()
    {
        $this->setExpectedException(
            'RuntimeException', 
            'getPackages() doesn\'t work before the source was analyzed.'
        );
        
        $pdepend = new PHP_Depend();
        $pdepend->addDirectory(dirname(__FILE__) . '/_code/code-5.2.x');
        $pdepend->getPackages();
    }

    /**
     * Tests the newly added support for single file handling.
     *
     * @return void
     */
    public function testSupportForSingleFileIssue90()
    {
        $pdepend = new PHP_Depend();
        $pdepend->addFile(dirname(__FILE__) . '/_code/issues/090.php');
        $pdepend->analyze();

        $packages = $pdepend->getPackages();
        $this->assertSame(1, $packages->count());

        $package = $packages->current();
        $this->assertSame(1, $package->getClasses()->count());
        $this->assertSame(1, $package->getInterfaces()->count());
    }

    /**
     * Tests that the addFile() method throws the expected exception when an
     * added file does not exist.
     *
     * @return void
     */
    public function testAddFileMethodThrowsExpectedExceptionForFileThatNotExists()
    {
        $pdepend = new PHP_Depend();

        $fileName = '/tmp/' . uniqid('pdepend_', true) . '.php';
        $this->assertFileNotExists($fileName);

        $this->setExpectedException(
            'InvalidArgumentException',
            'does not exist.'
        );

        $pdepend->addFile($fileName);
    }

    /**
     * testSetStorageSetsPruneFlagOnTokenCache
     *
     * @return void
     * @covers PHP_Depend
     * @group pdepend
     * @group unittest
     */
    public function testSetStorageSetsPruneFlagOnTokenCache()
    {
        $cache = $this->getMock('PHP_Depend_Storage_EngineI');
        $cache->expects($this->once())
            ->method('setPrune');

        $pdepend = new PHP_Depend();
        $pdepend->setStorage(PHP_Depend::TOKEN_STORAGE, $cache);
    }

    /**
     * testSetStorageSetsMaxLifetimeAndProbabilityOnParserCache
     *
     * @return void
     * @covers PHP_Depend
     * @group pdepend
     * @group unittest
     */
    public function testSetStorageSetsMaxLifetimeAndProbabilityOnParserCache()
    {
        $cache = $this->getMock('PHP_Depend_Storage_EngineI');
        $cache->expects($this->once())
            ->method('setProbability');
        $cache->expects($this->once())
            ->method('setMaxLifetime');

        $pdepend = new PHP_Depend();
        $pdepend->setStorage(PHP_Depend::PARSER_STORAGE, $cache);
    }

    /**
     * Tests that the setStorage() method throws an exception when an invalid
     * storage type was given.
     *
     * @return void
     */
    public function testSetStorageThrowsTheExpectedExceptionForAnUnknownStorageType()
    {
        $pdepend = new PHP_Depend();

        $this->setExpectedException(
            'InvalidArgumentException',
            'Unknown storage identifier'
        );

        $pdepend->setStorage(
            PHP_INT_MAX,
            $this->getMock('PHP_Depend_Storage_EngineI')
        );
    }
}
