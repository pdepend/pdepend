<?php
/**
 * This file is part of PDepend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
  */

namespace PDepend;

use PDepend\Source\AST\ASTNamespace;

/**
 * Test case for \PDepend\Engine facade.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Engine
 * @group unittest
 */
class EngineTest extends AbstractTest
{
    /**
     * Tests that the {@link \PDepend\Engine::addDirectory()} method
     * fails with an exception for an invalid directory.
     *
     * @return void
     */
    public function testAddInvalidDirectoryFail()
    {
        $dir = dirname(__FILE__) . '/foobar';
        $msg = "Invalid directory '{$dir}' added.";
        
        $this->setExpectedException('InvalidArgumentException', $msg);
        
        $engine = $this->createEngineFixture();
        $engine->addDirectory($dir);
    }
    /**
     * Tests that the {@link \PDepend\Engine::addDirectory()} method
     * with an existing directory.
     *
     * @return void
     */
    public function testAddDirectory()
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
    }

    /**
     * testAnalyzeMethodReturnsAnIterator
     *
     * @return void
     */
    public function testAnalyzeMethodReturnsAnIterator()
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        $engine->addFileFilter(new Input\ExtensionFilter(array('php')));

        $this->assertInstanceOf('Iterator', $engine->analyze());
    }
    
    /**
     * Tests the {@link \PDepend\Engine::analyze()} method and the return
     * value.
     *
     * @return void
     */
    public function testAnalyze()
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        $engine->addFileFilter(new Input\ExtensionFilter(array('php')));
        
        $metrics = $engine->analyze();
        
        $expected = array(
            'package1'  =>  true,
            'package2'  =>  true,
            'package3'  =>  true
        );
        
        foreach ($metrics as $metric) {
            unset($expected[$metric->getName()]);
        }
        
        $this->assertEquals(0, count($expected));
    }
    
    /**
     * Tests that {@link \PDepend\Engine::analyze()} throws an exception
     * if no source directory was set.
     *
     * @return void
     */
    public function testAnalyzeThrowsAnExceptionForNoSourceDirectory()
    {
        $engine = $this->createEngineFixture();
        $this->setExpectedException('RuntimeException', 'No source directory and file set.');
        $engine->analyze();
    }
    
    /**
     * testAnalyzeReturnsEmptyIteratorWhenNoPackageExists
     *
     * @return void
     */
    public function testAnalyzeReturnsEmptyIteratorWhenNoPackageExists()
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        $engine->addFileFilter(new Input\ExtensionFilter(array(__METHOD__)));
       
        $this->assertEquals(0, count($engine->analyze()));
    }
    
    /**
     * Tests that {@link \PDepend\Engine::analyze()} configures the
     * ignore annotations option correct.
     *
     * @return void
     */
    public function testAnalyzeSetsWithoutAnnotations()
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        $engine->addFileFilter(new Input\ExtensionFilter(array('inc')));
        $engine->setWithoutAnnotations();
        $namespaces = $engine->analyze();
        
        $this->assertEquals(2, $namespaces->count());
        $this->assertEquals('pdepend.test', $namespaces->current()->getName());
        
        $function = $namespaces->current()->getFunctions()->current();
        
        $this->assertNotNull($function);
        $this->assertEquals('foo', $function->getName());
        $this->assertEquals(0, $function->getExceptionClasses()->count());
    }
    
    /**
     * Tests that the {@link \PDepend\Engine::countClasses()} method
     * returns the expected number of classes.
     *
     * @return void
     */
    public function testCountClasses()
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        $engine->addFileFilter(new Input\ExtensionFilter(array('php')));
        $engine->analyze();
        
        $this->assertEquals(10, $engine->countClasses());
    }
    
    /**
     * Tests that the {@link \PDepend\Engine::countClasses()} method fails
     * with an exception if the code was not analyzed before.
     *
     * @return void
     */
    public function testCountClassesWithoutAnalyzeFail()
    {
        $this->setExpectedException(
            'RuntimeException', 
            'countClasses() doesn\'t work before the source was analyzed.'
        );
        
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        $engine->countClasses();
    }
    
    /**
     * Tests that the {@link \PDepend\Engine::countNamespaces()} method
     * returns the expected number of namespaces.
     *
     * @return void
     */
    public function testCountNamespaces()
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        $engine->analyze();
        
        $this->assertEquals(4, $engine->countNamespaces());
    }
    
    /**
     * Tests that the {@link \PDepend\Engine::countNamespaces()} method
     * fails with an exception if the code was not analyzed before.
     *
     * @return void
     */
    public function testCountNamespacesWithoutAnalyzeFail()
    {
        $this->setExpectedException(
            'RuntimeException',
            'countNamespaces() doesn\'t work before the source was analyzed.'
        );
        
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        $engine->countNamespaces();
    }
    
    /**
     * Tests that the {@link \PDepend\Engine::getNamespace()} method
     * returns the expected {@link \PDepend\Source\AST\ASTNamespace} objects.
     *
     * @return void
     */
    public function testGetNamespace()
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        $engine->analyze();
        
        $namespaces = array(
            'package1', 
            'package2', 
            'package3'
        );
        
        $className = '\\PDepend\\Source\\AST\\ASTNamespace';
        
        foreach ($namespaces as $namespace) {
            $this->assertInstanceOf($className, $engine->getNamespace($namespace));
        }
    }
    
    /**
     * Tests that the {@link \PDepend\Engine::getNamespace()} method fails
     * with an exception if the code was not analyzed before.
     *
     * @return void
     */
    public function testGetNamespaceWithoutAnalyzeFail()
    {
        $this->setExpectedException(
            'RuntimeException',
            'getNamespace() doesn\'t work before the source was analyzed.'
        );
        
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        $engine->getNamespace('package1');
    }
    
    /**
     * Tests that the {@link \PDepend\Engine::getNamespace()} method fails
     * with an exception if you request an invalid package.
     *
     * @return void
     */
    public function testGetNamespacesWithUnknownPackageFail()
    {
        $this->setExpectedException(
            'OutOfBoundsException',
            'Unknown namespace "nspace".'
        );
        
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        $engine->analyze();
        $engine->getNamespace('nspace');
    }
    
    /**
     * Tests that the {@link \PDepend\Engine::getNamespaces()} method
     * returns the expected {@link \PDepend\Source\AST\ASTNamespace} objects
     * and reuses the result of {@link \PDepend\Engine::analyze()}.
     *
     * @return void
     */
    public function testGetNamespaces()
    {
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        
        $namespace1 = $engine->analyze();
        $namespace2 = $engine->getNamespaces();
        
        $this->assertNotNull($namespace1);
        
        $this->assertSame($namespace1, $namespace2);
    }
    
    /**
     * Tests that the {@link \PDepend\Engine::getNamespaces()} method
     * fails with an exception if the code was not analyzed before.
     *
     * @return void
     */
    public function testGetNamespacesWithoutAnalyzeFail()
    {
        $this->setExpectedException(
            'RuntimeException',
            'getNamespaces() doesn\'t work before the source was analyzed.'
        );
        
        $engine = $this->createEngineFixture();
        $engine->addDirectory(self::createCodeResourceUriForTest());
        $engine->getNamespaces();
    }

    /**
     * Tests the newly added support for single file handling.
     *
     * @return \PDepend\Source\AST\ASTNamespace
     */
    public function testSupportForSingleFileIssue90()
    {
        $engine = $this->createEngineFixture();
        $engine->addFile(self::createCodeResourceUriForTest());
        $engine->analyze();

        $namespaces = $engine->getNamespaces();
        $this->assertSame(1, count($namespaces));

        return $namespaces[0];
    }

    /**
     * @param \PDepend\Source\AST\ASTNamespace $namespace
     * @return void
     * @depends PDepend\EngineTest::testSupportForSingleFileIssue90
     */
    public function testSupportForSingleFileIssue90ExpectedNumberOfClasses(ASTNamespace $namespace)
    {
        $this->assertSame(1, count($namespace->getClasses()));
    }

    /**
     * @param \PDepend\Source\AST\ASTNamespace $namespace
     * @return void
     * @depends PDepend\EngineTest::testSupportForSingleFileIssue90
     */
    public function testSupportForSingleFileIssue90ExpectedNumberOfInterfaces(ASTNamespace $namespace)
    {
        $this->assertSame(1, count($namespace->getInterfaces()));
    }

    /**
     * Tests that the addFile() method throws the expected exception when an
     * added file does not exist.
     *
     * @return void
     * @expectedException InvalidArgumentException
     */
    public function testAddFileMethodThrowsExpectedExceptionForFileThatNotExists()
    {
        $engine = $this->createEngineFixture();
        $engine->addFile(self::createRunResourceURI('pdepend_'));
    }
}
