<?php
/**
 * This file is part of PHP_Depend.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';
require_once dirname(__FILE__) . '/_dummy/TestImplAnalyzerNodeAware.php';
require_once dirname(__FILE__) . '/_dummy/TestImplAnalyzerProjectAware.php';

require_once 'PHP/Depend/Log/Summary/Xml.php';

/**
 * Test case for the xml summary log.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Log_Summary_XmlTest extends PHP_Depend_AbstractTest
{
    /**
     * Test data for lines of code.
     *
     * @var array(array) $_locData
     */
    private static $_locData = array(
        array('loc'  =>  42),  array('ncloc'  =>  23),
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

    /**
     * Tests that the logger returns the expected set of analyzers.
     *
     * @return void
     */
    public function testReturnsExceptedAnalyzers()
    {
        $logger    = new PHP_Depend_Log_Summary_Xml();
        $actual    = $logger->getAcceptedAnalyzers();
        $exptected = array(
            'PHP_Depend_Metrics_NodeAwareI',
            'PHP_Depend_Metrics_ProjectAwareI'
        );
        
        $this->assertEquals($exptected, $actual);
    }
    
    /**
     * Tests that the logger throws an exception if the log target wasn't 
     * configured.
     *
     * @return void
     */
    public function testThrowsExceptionForInvalidLogTarget()
    {
        $this->setExpectedException(
            'PHP_Depend_Log_NoLogOutputException',
            "The log target is not configured for 'PHP_Depend_Log_Summary_Xml'."
        );
        
        $logger = new PHP_Depend_Log_Summary_Xml();
        $logger->close();
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
        $expectedFile = self::getNormalizedPathXml('/xml-log-without-metrics.xml');
        $actualFile   = self::createRunResourceURI('/summary.xml');
        
        $packages = self::parseSource('/log/summary/');
        
        $log = new PHP_Depend_Log_Summary_Xml();
        $log->setLogFile($actualFile);
        $log->setCode($packages);
        
        $this->assertFileNotExists($actualFile);
        $log->close();
        $this->assertFileExists($actualFile);

        $this->assertXmlFileEqualsXmlFile($expectedFile, $actualFile);
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
        $resultOne  = new PHP_Depend_Log_Summary_TestImplAnalyzerProjectAware($metricsOne);
        
        $metricsTwo = array('ncloc'  =>  1742, 'loc'  =>  4217);
        $resultTwo  = new PHP_Depend_Log_Summary_TestImplAnalyzerProjectAware($metricsTwo);
        
        $expectedFile = self::getNormalizedPathXml('/project-aware-result-set-without-code.xml');
        $actualFile   = self::createRunResourceURI('/summary.xml');
        
        $log = new PHP_Depend_Log_Summary_Xml();
        $log->setLogFile($actualFile);
        $log->setCode(self::parseSource('/log/summary/empty/'));
        $this->assertTrue($log->log($resultOne));
        $this->assertTrue($log->log($resultTwo));
        
        $this->assertFileNotExists($actualFile);
        $log->close();
        $this->assertFileExists($actualFile);
        
        $this->assertXmlFileEqualsXmlFile($expectedFile, $actualFile);
    }
    
    /**
     * Tests that the logger handles multiple node aware analyzers correct.
     *
     * @return void
     */
    public function testNodeAwareAnalyzer()
    {
        $expectedFile = self::getNormalizedPathXml('/node-aware-result-set.xml');
        $actualFile   = self::createRunResourceURI('/summary.xml');
        
        // Create example source
        $packages = self::parseSource('/log/summary/');
        // Create dummy analyzers
        list($analyzer1, $analyzer2) = self::_createNodeAnalyzers($packages);
        
        $log = new PHP_Depend_Log_Summary_Xml();
        $log->setLogFile($actualFile);
        $log->setCode($packages);
        $this->assertTrue($log->log($analyzer1));
        $this->assertTrue($log->log($analyzer2));

        $this->assertFileNotExists($actualFile);
        $log->close();
        $this->assertFileExists($actualFile);
copy($actualFile, '/tmp/summary.xml');        
        $this->assertXmlFileEqualsXmlFile($expectedFile, $actualFile);
    }
    
    /**
     * Normalizes the file references within the expected result document.
     *
     * @param string $fileName File name of the expected result document.
     * 
     * @return string The uri of the result document.
     */
    protected static function getNormalizedPathXml($fileName)
    {
        $fileName = dirname(__FILE__) . "/_expected/{$fileName}";
        if (file_exists($fileName) === false) {
            throw new ErrorException("Invalid expected file '{$fileName}'."); 
        }
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        
        $dom->formatOutput       = true;
        $dom->preserveWhiteSpace = false;
        
        if ($dom->load($fileName) === false) {
            throw new ErrorException("Invalid xml in file '{$fileName}'.");
        }
        
        $path = self::createResourceURI('/log/summary/') . '/';
        
        // Adjust path
        foreach ($dom->getElementsByTagName('file') as $fileXml) {
            $sourceFile = $fileXml->getAttribute('name');
            $sourceFile = $path . basename($sourceFile);

            $fileXml->setAttribute('name', $sourceFile);
        }
        
        $expected = self::createRunResourceURI('/phpunit.expected.xml');
        $dom->save($expected);
        
        return $expected;
    }
    
    /**
     * Creates an array with dummy node analyzers
     *
     * @param PHP_Reflection_AST_Iterator $packages The package iterator
     * 
     * @return array(PHP_Depend_Metrics_NodeAwareI)
     */
    private static function _createNodeAnalyzers($packages)
    {
        // Create a copy of the loc data
        $data = self::$_locData;
        
        $m1 = $m2 = array();
        foreach ($packages as $pkg) {
            $m1[$pkg->getUUID()] = array_shift($data);
            $m2[$pkg->getUUID()] = array_shift($data);
            foreach ($pkg->getClasses() as $class) {
                $m1[$class->getUUID()] = array_shift($data);
                $m2[$class->getUUID()] = array_shift($data);
                foreach ($class->getMethods() as $method) {
                    $m1[$method->getUUID()] = array_shift($data);
                    $m2[$method->getUUID()] = array_shift($data);
                }
            }
            foreach ($pkg->getFunctions() as $function) {
                $m1[$function->getUUID()] = array_shift($data);
                $m2[$function->getUUID()] = array_shift($data);
            }
        }
        
        return array(
            new PHP_Depend_Log_Summary_TestImplAnalyzerNodeAware($m1),
            new PHP_Depend_Log_Summary_TestImplAnalyzerNodeAware($m2)
        );         
    }
}