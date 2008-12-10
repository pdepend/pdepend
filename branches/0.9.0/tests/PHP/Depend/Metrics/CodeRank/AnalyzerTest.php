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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Parser.php';
require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/DefaultBuilder.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Tokenizer/InternalTokenizer.php';
require_once 'PHP/Depend/Metrics/CodeRank/Analyzer.php';
require_once 'PHP/Depend/Util/FileExtensionFilter.php';
require_once 'PHP/Depend/Util/FileFilterIterator.php';
require_once 'PHP/Depend/Util/UUID.php';

/**
 * Test case for the code metric analyzer class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_CodeRank_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * Test input data.
     *
     * @type array<array>
     * @var array(string=>array) $_input
     */
    private $_input = array(
        'package1'    =>  array('cr'  =>  0.2775,     'rcr'  =>  0.385875),
        'package2'    =>  array('cr'  =>  0.15,       'rcr'  =>  0.47799375),
        'package3'    =>  array('cr'  =>  0.385875,   'rcr'  =>  0.2775),
        '+standard'   =>  array('cr'  =>  0.47799375, 'rcr'  =>  0.15),
        '+global'     =>  array('cr'  =>  0.15,       'rcr'  =>  0.15),
        'pkg1Foo'     =>  array('cr'  =>  0.15,       'rcr'  =>  0.181875),
        'pkg2FooI'    =>  array('cr'  =>  0.15,       'rcr'  =>  0.181875),
        'pkg2Bar'     =>  array('cr'  =>  0.15,       'rcr'  =>  0.1755),
        'pkg2Foobar'  =>  array('cr'  =>  0.15,       'rcr'  =>  0.1755),
        'pkg1Barfoo'  =>  array('cr'  =>  0.15,       'rcr'  =>  0.207375),
        'pkg2Barfoo'  =>  array('cr'  =>  0.15,       'rcr'  =>  0.207375),
        'pkg1Foobar'  =>  array('cr'  =>  0.15,       'rcr'  =>  0.411375),
        'pkg1FooI'    =>  array('cr'  =>  0.5325,     'rcr'  =>  0.15),
        'pkg1Bar'     =>  array('cr'  =>  0.59625,    'rcr'  =>  0.15),
        'pkg3FooI'    =>  array('cr'  =>  0.21375,    'rcr'  =>  0.2775),
        'Iterator'    =>  array('cr'  =>  0.3316875,  'rcr'  =>  0.15),
        'Bar'         =>  array('cr'  =>  0.15,       'rcr'  =>  0.15)
    );
    
    /**
     * The expected test data.
     *
     * @type array<array>
     * @var array(string=>array) $_expected
     */
    private $_expected = array();
    
    /**
     * The code rank analyzer.
     *
     * @type PHP_Depend_Metrics_CodeRank_Analyzer
     * @var PHP_Depend_Metrics_CodeRank_Analyzer $_analyzer
     */
    private $_analyzer = null;
    
    /**
     * Creates the expected metrics array.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        
        $source = dirname(__FILE__) . '/../../_code/code-5.2.x';
        $files  = new PHP_Depend_Util_FileFilterIterator(
            new DirectoryIterator($source),
            new PHP_Depend_Util_FileExtensionFilter(array('php'))
        );
        
        $builder = new PHP_Depend_Code_DefaultBuilder();
        
        foreach ($files as $file) {
            
            $path = $file->getRealPath();
            $tokz = new PHP_Depend_Code_Tokenizer_InternalTokenizer($path);
            
            $parser = new PHP_Depend_Parser($tokz, $builder);
            $parser->parse();
        }
        
        $this->_analyzer = new PHP_Depend_Metrics_CodeRank_Analyzer();
        $this->_analyzer->analyze($builder->getPackages());

        $this->_expected = array();
        foreach ($builder->getPackages() as $package) {
            $this->_expected[] = array($package, $this->_input[$package->getName()]);
            foreach ($package->getTypes() as $type) {
                $this->_expected[] = array($type, $this->_input[$type->getName()]);
            }
        }
    }
    
    /**
     * Tests the result of the class rank calculation against previous computed
     * values.
     *
     * @return void
     */
    public function testGetNodeMetrics()
    {
        foreach ($this->_expected as $key => $info) {
            $metrics = $this->_analyzer->getNodeMetrics($info[0]);
            
            $this->assertEquals($info[1]['cr'], $metrics['cr'], '', 0.00005);
            $this->assertEquals($info[1]['rcr'], $metrics['rcr'], '', 0.00005);
            
            unset($this->_expected[$key]);
        }
        $this->assertEquals(0, count($this->_expected));
    }
    
    /**
     * Tests that {@link PHP_Depend_Metrics_CodeRank_Analyzer::getNodeMetrics()}
     * returns an empty <b>array</b> for an unknown identifier.
     *
     * @return void
     */
    public function testGetNodeMetricsInvalidIdentifier()
    {
        $class   = new PHP_Depend_Code_Class('PDepend');
        $metrics = $this->_analyzer->getNodeMetrics($class);
        
        $this->assertType('array', $metrics);
        $this->assertEquals(0, count($metrics));
    }
}