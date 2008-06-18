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
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/File.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/NodeIterator/PackageFilter.php';
require_once 'PHP/Depend/Code/NodeIterator/StaticFilter.php';
require_once 'PHP/Depend/Metrics/Inheritance/Analyzer.php';

/**
 * Test case for the inheritance analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
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
        $system  = new PHP_Depend_Code_Package('system');
        $library = new PHP_Depend_Code_Package('library');
        
        $file = new PHP_Depend_Code_File(null);
        
        $A = $system->addType(new PHP_Depend_Code_Class('A'));
        $B = $A->addChildType($system->addType(new PHP_Depend_Code_Class('B')));
        $C = $A->addChildType($system->addType(new PHP_Depend_Code_Class('C')));
        $D = $A->addChildType($system->addType(new PHP_Depend_Code_Class('D')));
        $E = $A->addChildType($system->addType(new PHP_Depend_Code_Class('E')));
        $F = $B->addChildType($system->addType(new PHP_Depend_Code_Class('F')));
        $G = $B->addChildType($system->addType(new PHP_Depend_Code_Class('G')));
        $H = $D->addChildType($system->addType(new PHP_Depend_Code_Class('H')));
        $I = $E->addChildType($system->addType(new PHP_Depend_Code_Class('I')));
        $J = $E->addChildType($system->addType(new PHP_Depend_Code_Class('J')));
        $K = $H->addChildType($system->addType(new PHP_Depend_Code_Class('K')));
        $L = $J->addChildType($system->addType(new PHP_Depend_Code_Class('L')));
        $M = $L->addChildType($system->addType(new PHP_Depend_Code_Class('M')));
        $N = $system->addType(new PHP_Depend_Code_Class('N'));
        $O = $N->addChildType($system->addType(new PHP_Depend_Code_Class('O')));
        $P = $N->addChildType($system->addType(new PHP_Depend_Code_Class('P')));
        $Q = $library->addType(new PHP_Depend_Code_Class('Q'));
        $R = $Q->addChildType($system->addType(new PHP_Depend_Code_Class('R')));
        $S = $system->addType(new PHP_Depend_Code_Class('S'));
        $T = $system->addType(new PHP_Depend_Code_Interface('T'));
        $U = $T->addChildType($system->addType(new PHP_Depend_Code_Class('U')));
        
        $A->setSourceFile($file);
        $B->setSourceFile($file);
        $C->setSourceFile($file);
        $D->setSourceFile($file);
        $E->setSourceFile($file);
        $F->setSourceFile($file);
        $G->setSourceFile($file);
        $H->setSourceFile($file);
        $I->setSourceFile($file);
        $J->setSourceFile($file);
        $K->setSourceFile($file);
        $L->setSourceFile($file);
        $M->setSourceFile($file);
        $N->setSourceFile($file);
        $O->setSourceFile($file);
        $P->setSourceFile($file);
        $Q->setSourceFile($file);
        $R->setSourceFile($file);
        $S->setSourceFile($file);
        $T->setSourceFile($file);
        $U->setSourceFile($file);
        
        $filter = PHP_Depend_Code_NodeIterator_StaticFilter::getInstance();
        $filter->addFilter(new PHP_Depend_Code_NodeIterator_PackageFilter(array('library')));
        
        $packages = new PHP_Depend_Code_NodeIterator(array($system, $library));
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
        $system  = new PHP_Depend_Code_Package('system');
        $library = new PHP_Depend_Code_Package('library');
        
        $file = new PHP_Depend_Code_File(null);
        
        $A = $system->addType(new PHP_Depend_Code_Class('A'));
        $B = $A->addChildType($system->addType(new PHP_Depend_Code_Class('B')));
        $C = $A->addChildType($system->addType(new PHP_Depend_Code_Class('C')));
        $D = $A->addChildType($system->addType(new PHP_Depend_Code_Class('D')));
        $E = $A->addChildType($system->addType(new PHP_Depend_Code_Class('E')));
        $F = $B->addChildType($system->addType(new PHP_Depend_Code_Class('F')));
        $G = $B->addChildType($system->addType(new PHP_Depend_Code_Class('G')));
        $H = $D->addChildType($system->addType(new PHP_Depend_Code_Class('H')));
        $I = $E->addChildType($system->addType(new PHP_Depend_Code_Class('I')));
        $J = $E->addChildType($system->addType(new PHP_Depend_Code_Class('J')));
        $K = $H->addChildType($system->addType(new PHP_Depend_Code_Class('K')));
        $L = $J->addChildType($system->addType(new PHP_Depend_Code_Class('L')));
        $M = $L->addChildType($system->addType(new PHP_Depend_Code_Class('M')));
        $N = $system->addType(new PHP_Depend_Code_Class('N'));
        $O = $N->addChildType($system->addType(new PHP_Depend_Code_Class('O')));
        $P = $N->addChildType($system->addType(new PHP_Depend_Code_Class('P')));
        $Q = $library->addType(new PHP_Depend_Code_Class('Q'));
        $R = $Q->addChildType($system->addType(new PHP_Depend_Code_Class('R')));
        $S = $system->addType(new PHP_Depend_Code_Class('S'));
        $T = $system->addType(new PHP_Depend_Code_Interface('T'));
        $U = $T->addChildType($system->addType(new PHP_Depend_Code_Class('U')));
        
        $A->setSourceFile($file);
        $B->setSourceFile($file);
        $C->setSourceFile($file);
        $D->setSourceFile($file);
        $E->setSourceFile($file);
        $F->setSourceFile($file);
        $G->setSourceFile($file);
        $H->setSourceFile($file);
        $I->setSourceFile($file);
        $J->setSourceFile($file);
        $K->setSourceFile($file);
        $L->setSourceFile($file);
        $M->setSourceFile($file);
        $N->setSourceFile($file);
        $O->setSourceFile($file);
        $P->setSourceFile($file);
        $Q->setSourceFile($file);
        $R->setSourceFile($file);
        $S->setSourceFile($file);
        $T->setSourceFile($file);
        $U->setSourceFile($file);
        
        $filter = PHP_Depend_Code_NodeIterator_StaticFilter::getInstance();
        $filter->addFilter(new PHP_Depend_Code_NodeIterator_PackageFilter(array('library')));
        
        $packages = new PHP_Depend_Code_NodeIterator(array($system, $library));
        $analyzer = new PHP_Depend_Metrics_Inheritance_Analyzer();
        $analyzer->analyze($packages);
        
        $project = $analyzer->getProjectMetrics();
        
        $this->assertArrayHasKey('ahh', $project);
        $this->assertEquals(1, $project['ahh']);
    }
}