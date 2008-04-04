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

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Parser.php';
require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/DefaultBuilder.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Tokenizer/InternalTokenizer.php';
require_once 'PHP/Depend/Metrics/CodeRank/Analyzer.php';
require_once 'PHP/Depend/Util/FileExtensionFilter.php';
require_once 'PHP/Depend/Util/FileFilterIterator.php';

/**
 * Test case for the code metric analyzer class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_CodeRank_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the result of the class rank calculation against previous computed
     * values.
     *
     * @return void
     */
    public function testGetClassRank()
    {
        $source = dirname(__FILE__) . '/../../data/code-5.2.x';
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
        
        $analyzer = new PHP_Depend_Metrics_CodeRank_Analyzer();
        foreach ($builder->getPackages() as $package) {
            $analyzer->visitPackage($package);
        }
        
        $expected = array(
            'pkg1Foo'     =>  array(0.15, 0.181875),
            'pkg2FooI'    =>  array(0.15, 0.181875),
            'pkg2Bar'     =>  array(0.15, 0.1755),
            'pkg2Foobar'  =>  array(0.15, 0.1755),
            'pkg1Barfoo'  =>  array(0.15, 0.207375),
            'pkg2Barfoo'  =>  array(0.15, 0.207375),
            'pkg1Foobar'  =>  array(0.15, 0.411375),
            'pkg1FooI'    =>  array(0.5325, 0.15),
            'pkg1Bar'     =>  array(0.59625, 0.15),
            'pkg3FooI'    =>  array(0.21375, 0.2775),
            'Iterator'    =>  array(0.3316875, 0.15),
            'Bar'         =>  array(0.15, 0.15)
        );
        
        foreach ($analyzer->getClassRank() as $rank) {
            // Get expected value set
            $value = $expected[$rank->getName()];
            $this->assertEquals($value[0], $rank->getCodeRank(), '', 0.00005);
            $this->assertEquals($value[1], $rank->getReverseCodeRank(), '', 0.00005);
        }
    }
    
    /**
     * Tests the calculated package rank.
     *
     * @return void
     */
    public function testGetPackageRank()
    {
        $source = dirname(__FILE__) . '/../../data/code-5.2.x';
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
        
        $analyzer = new PHP_Depend_Metrics_CodeRank_Analyzer();
        foreach ($builder->getPackages() as $package) {
            $analyzer->visitPackage($package);
        }
        
        $expected = array(
            'package1'  =>  array(0.2775, 0.385875),
            'package2'  =>  array(0.15, 0.47799375),
            'package3'  =>  array(0.385875, 0.2775),
            'global'    =>  array(0.47799375, 0.15),
        );
        
        foreach ($analyzer->getPackageRank() as $rank) {
            // Get expected value set
            $value = $expected[$rank->getName()];
            $this->assertEquals($value[0], $rank->getCodeRank(), '', 0.00005);
            $this->assertEquals($value[1], $rank->getReverseCodeRank(), '', 0.00005);
        }
    }
}