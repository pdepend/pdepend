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
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';
require_once dirname(__FILE__) . '/AnalyzerNodeAwareDummy.php';
require_once dirname(__FILE__) . '/AnalyzerProjectAwareDummy.php';

require_once 'PHP/Depend/Parser.php';
require_once 'PHP/Depend/Code/DefaultBuilder.php';
require_once 'PHP/Depend/Code/Tokenizer/InternalTokenizer.php';
require_once 'PHP/Depend/Log/Summary/Xml.php';

/**
 * Test case for the xml summary log.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Log_Summary_XmlTest extends PHP_Depend_AbstractTest
{
    /**
     * Test code structure.
     *
     * @type PHP_Depend_Code_NodeIterator
     * @var PHP_Depend_Code_NodeIterator $packages
     */
    protected $packages = null;
    
    /**
     * The test file name.
     *
     * @type string
     * @var string $testFile
     */
    protected $testFileName = null;
    
    /**
     * The temporary file name for the logger result.
     *
     * @type string
     * @var string $resultFile
     */
    protected $resultFile = null;
    
    /**
     * Creates the package structure from a test source file.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        
        $this->testFileName = dirname(__FILE__) . '/../../_code/mixed_code.php';
        $this->testFileName = realpath($this->testFileName);
        
        $tokenizer = new PHP_Depend_Code_Tokenizer_InternalTokenizer($this->testFileName);
        $builder   = new PHP_Depend_Code_DefaultBuilder();
        $parser    = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
        
        $this->packages = $builder->getPackages();
        
        $this->resultFile = tempnam(sys_get_temp_dir(), 'log-summary.xml');
    }
    
    /**
     * Removes the temporary log files.
     *
     * @return void
     */
    protected function tearDown()
    {
        @unlink($this->resultFile);
        
        parent::tearDown();
    }

    /**
     * Tests that the logger returns the expected set of analyzers.
     *
     * @return void
     */
    public function testReturnsExceptedAnalyzers()
    {
        $logger    = new PHP_Depend_Log_Summary_Xml(__FILE__);
        $actual    = $logger->getAcceptedAnalyzers();
        $exptected = array(
            'PHP_Depend_Metrics_NodeAwareI',
            'PHP_Depend_Metrics_ProjectAwareI'
        );
        
        $this->assertEquals($exptected, $actual);
    }
    
    /**
     * Tests that {@link PHP_Depend_Log_Summary_Xml::write()} generates the 
     * expected document structure for the source, but without any applied 
     * metrics.
     *
     * @return void
     */
    public function testXmlLogWithoutMetrics()
    {
        $log = new PHP_Depend_Log_Summary_Xml($this->resultFile);
        $log->setCode($this->packages);
        $log->close();
        
        $fileName = 'xml-log-without-metrics.xml';
        $this->assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(dirname(__FILE__) . "/_expected/{$fileName}"),
            file_get_contents($this->resultFile)
        );
    }
    
    /**
     * Tests that the xml logger generates the expected xml document for an 
     * empty source code structure.
     *
     * @return void
     */
    public function testProjectAwareAnalyzerWithoutCode()
    {
        $metricsOne = array('interfs'  =>  42, 'cls'  =>  23);
        $resultOne  = new PHP_Depend_Log_Summary_AnalyzerProjectAwareDummy($metricsOne);
        
        $metricsTwo = array('ncloc'  =>  1742, 'loc'  =>  4217);
        $resultTwo  = new PHP_Depend_Log_Summary_AnalyzerProjectAwareDummy($metricsTwo);
        
        $log = new PHP_Depend_Log_Summary_Xml($this->resultFile);
        $log->setCode(new PHP_Depend_Code_NodeIterator(array()));
        $this->assertTrue($log->log($resultOne));
        $this->assertTrue($log->log($resultTwo));
        
        $fileName = 'project-aware-result-set-without-code.xml';
        $expected = dirname(__FILE__) . "/_expected/{$fileName}";
        
        $log->close();
        
        $this->assertXmlFileEqualsXmlFile($expected, $this->resultFile);
    }
    
    public function testNodeAwareAnalyzer()
    {
        $input = array(
            array('loc'  =>  42),  array('ncloc'  =>  23),
            array('loc'  =>  33),  array('ncloc'  =>  20),
            array('loc'  =>  9),   array('ncloc'  =>  7),
            array('loc'  =>  101), array('ncloc'  =>  99),
            array('loc'  =>  90),  array('ncloc'  =>  80),
            array('loc'  =>  50),  array('ncloc'  =>  45),
            array('loc'  =>  30),  array('ncloc'  =>  22),
            array('loc'  =>  9),   array('ncloc'  =>  9),
            array('loc'  =>  3),   array('ncloc'  =>  3),
            array('loc'  =>  42),  array('ncloc'  =>  23),
            array('loc'  =>  33),  array('ncloc'  =>  20),
            array('loc'  =>  9),   array('ncloc'  =>  7),
        );
        
        $metricsOne = array();
        $metricsTwo = array();
        foreach ($this->packages as $package) {
            $metricsOne[$package->getUUID()] = array_shift($input);
            $metricsTwo[$package->getUUID()] = array_shift($input);
            foreach ($package->getClasses() as $class) {
                $metricsOne[$class->getUUID()] = array_shift($input);
                $metricsTwo[$class->getUUID()] = array_shift($input);
                foreach ($class->getMethods() as $method) {
                    $metricsOne[$method->getUUID()] = array_shift($input);
                    $metricsTwo[$method->getUUID()] = array_shift($input);
                }
            }
            foreach ($package->getFunctions() as $function) {
                $metricsOne[$function->getUUID()] = array_shift($input);
                $metricsTwo[$function->getUUID()] = array_shift($input);
            }
        }
        
        $resultOne = new PHP_Depend_Log_Summary_AnalyzerNodeAwareDummy($metricsOne);
        $resultTwo = new PHP_Depend_Log_Summary_AnalyzerNodeAwareDummy($metricsTwo);
        
        $log = new PHP_Depend_Log_Summary_Xml($this->resultFile);
        $log->setCode($this->packages);
        $this->assertTrue($log->log($resultOne));
        $this->assertTrue($log->log($resultTwo));
        
        $log->close();
        
        $fileName = 'node-aware-result-set.xml';
        $this->assertXmlStringEqualsXmlString(
            $this->getNormalizedPathXml(dirname(__FILE__) . "/_expected/{$fileName}"),
            file_get_contents($this->resultFile)
        );
    }
    
    protected function getNormalizedPathXml($fileName)
    {
        $expected                     = new DOMDocument('1.0', 'UTF-8');
        $expected->preserveWhiteSpace = false;
        $expected->load($fileName);
        
        // Adjust file path
        foreach ($expected->getElementsByTagName('file') as $file) {
            $file->setAttribute('name', $this->testFileName);
        }
        
        return $expected->saveXML();
    }
}