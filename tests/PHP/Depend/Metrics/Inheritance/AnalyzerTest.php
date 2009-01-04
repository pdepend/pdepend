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
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Reflection/AST/Iterator/PackageFilter.php';
require_once 'PHP/Reflection/AST/Iterator/StaticFilter.php';
require_once 'PHP/Depend/Metrics/Inheritance/Analyzer.php';

/**
 * Test case for the inheritance analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_Inheritance_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the analyzer calculates the correct average number of derived
     * classes.
     * 
     * @return void
     */
    public function testAnalyzerCalculatesCorrectANDCValue()
    {
        // TODO: Refactory this code into PHP_Reflection and remove this dependency
        $filter = PHP_Reflection_AST_Iterator_StaticFilter::getInstance();
        $filter->addFilter(new PHP_Reflection_AST_Iterator_PackageFilter(array('library')));
        
        $packages = self::parseSource('/metrics/inheritance/andc');
        $analyzer = new PHP_Depend_Metrics_Inheritance_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('andc', $project);
        $this->assertEquals(0.7368, $project['andc'], null, 0.0001);
    }
    
    /**
     * Tests that the analyzer calculates the correct average hierarchy height.
     * 
     * @return void
     */
    public function testAnalyzerCalculatesCorrectAHHValue()
    {        
        // TODO: Refactory this code into PHP_Reflection and remove this dependency
        $filter = PHP_Reflection_AST_Iterator_StaticFilter::getInstance();
        $filter->addFilter(new PHP_Reflection_AST_Iterator_PackageFilter(array('library')));
        
        $packages = self::parseSource('/metrics/inheritance/ahh');
        $analyzer = new PHP_Depend_Metrics_Inheritance_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('ahh', $project);
        $this->assertEquals(1, $project['ahh']);
    }
}