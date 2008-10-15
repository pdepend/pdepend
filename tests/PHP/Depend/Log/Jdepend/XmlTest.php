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
require_once dirname(__FILE__) . '/../_dummy/TestImplAnalyzer.php';

require_once 'PHP/Depend/Log/Jdepend/Xml.php';
require_once 'PHP/Depend/Metrics/Dependency/Analyzer.php';

require_once 'PHP/Reflection/Parser.php';
require_once 'PHP/Reflection/Ast/Iterator/GlobalPackageFilter.php';
require_once 'PHP/Reflection/Ast/Iterator/InternalPackageFilter.php';
require_once 'PHP/Reflection/Builder/Default.php';
require_once 'PHP/Reflection/Input/FileExtensionFilter.php';
require_once 'PHP/Reflection/Input/FileFilterIterator.php';
require_once 'PHP/Reflection/Tokenizer/Internal.php';

/**
 * Test case for the jdepend xml logger.
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
class PHP_Depend_Log_Jdepend_XmlTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the logger returns the expected set of analyzers.
     *
     * @return void
     */
    public function testReturnsExceptedAnalyzers()
    {
        $logger    = new PHP_Depend_Log_Jdepend_Xml();
        $actual    = $logger->getAcceptedAnalyzers();
        $exptected = array('PHP_Depend_Metrics_Dependency_Analyzer');
        
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
            "The log target is not configured for 'PHP_Depend_Log_Jdepend_Xml'."
        );
        
        $logger = new PHP_Depend_Log_Jdepend_Xml();
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
        $expectedFile = self::getNormalizedPathXml('/_expected/pdepend-log.xml');
        $actualFile   = self::createRunResourceURI('/pdepend-log.xml');
        
        $packages = self::parseSource('/log/jdepend/complex/');
        $analyzer = new PHP_Depend_Metrics_Dependency_Analyzer();
        $analyzer->analyze($packages);
        
        $filter = new PHP_Reflection_Ast_Iterator_GlobalPackageFilter();
        $packages->addFilter($filter);
        $filter = new PHP_Reflection_Ast_Iterator_InternalPackageFilter();
        $packages->addFilter($filter);
        
        $log = new PHP_Depend_Log_Jdepend_Xml();
        $log->setLogFile($actualFile);
        $log->setCode($packages);
        $log->log($analyzer);
        $log->close();
        
        $this->assertXmlFileEqualsXmlFile($expectedFile, $actualFile);
    }
    
    public function testXmlLogAcceptsOnlyTheCorrectAnalyzer()
    {
        $logger = new PHP_Depend_Log_Jdepend_Xml();
        
        $this->assertFalse($logger->log(new PHP_Depend_Log_TestImplAnalyzer()));
        $this->assertTrue($logger->log(new PHP_Depend_Metrics_Dependency_Analyzer()));
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
        $expected                     = new DOMDocument('1.0', 'UTF-8');
        $expected->preserveWhiteSpace = false;
        $expected->formatOutput       = true;
        $expected->load(dirname(__FILE__) . $fileName);
        
        $xpath = new DOMXPath($expected);
        $result = $xpath->query('//Class[@sourceFile]');
        
        $path = self::createResourceURI('/log/jdepend/complex') . '/';
        
        // Adjust file path
        foreach ($result as $class) {
            $sourceFile = $class->getAttribute('sourceFile');
            $sourceFile = $path . basename($sourceFile);
            
            $class->setAttribute('sourceFile', $sourceFile);
        }
        
        $tempFile = self::createRunResourceURI('/expected.xml');
        $expected->save($tempFile);
        
        return $tempFile;
    }
    
}
