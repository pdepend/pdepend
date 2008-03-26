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

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Metrics/CodeRank/Analyzer.php';

/**
 * 
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
            $tokenizer = new PHP_Depend_Code_Tokenizer_InternalTokenizer($file->getRealPath());
            
            $parser = new PHP_Depend_Parser($tokenizer, $builder);
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
            'Iterator'    =>  array(0.3316875, 0.15)
        );
        
        foreach ($analyzer->getClassRank() as $rank) {
            $this->assertEquals(
                $expected[$rank->getName()][0],
                $rank->getCodeRank(),
                '',
                0.00005
            );
            $this->assertEquals(
                $expected[$rank->getName()][1],
                $rank->getReverseCodeRank(),
                '',
                0.00005
            );
        }
    }
}