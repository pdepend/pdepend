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
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Metrics/Hierarchy/Analyzer.php';

/**
 * Test case for the hierarchy analyzer.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_Hierarchy_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * The used node builder.
     *
     * @type PHP_Depend_Code_DefaultBuilder
     * @var PHP_Depend_Code_DefaultBuilder $builder
     */
    protected $builder = null;
    
    /**
     * Sets up the code builder.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        
        $source        = dirname(__FILE__) . '/../../_code/mixed_code.php';
        $tokenizer     = new PHP_Depend_Code_Tokenizer_InternalTokenizer($source);
        $this->builder = new PHP_Depend_Code_DefaultBuilder();
        $parser        = new PHP_Depend_Parser($tokenizer, $this->builder);
        
        $parser->parse();
    }
    
    /**
     * Tests that the {@link PHP_Depend_Metrics_Hierarchy_Analyzer::analyze()} 
     * method creates the expected hierarchy metrics.
     *
     * @return void.
     */
    public function testAnalyzeProjectMetrics()
    {
        $analyzer = new PHP_Depend_Metrics_Hierarchy_Analyzer();
        $result   = $analyzer->analyze($this->builder->getPackages());
        $project  = $result->getProjectMetrics();
        
        $this->assertEquals(4, $project['pkg']);
        $this->assertEquals(3, $project['cls']);
        $this->assertEquals(1, $project['clsa']);
        $this->assertEquals(2, $project['clsc']);
        $this->assertEquals(1, $project['interfs']);
        $this->assertEquals(2, $project['fcs']);
        $this->assertEquals(1, $project['roots']);
        $this->assertEquals(2, $project['leafs']);
        $this->assertEquals(1, $project['maxDIT']);
    }
    
    /**
     * Tests that {@link PHP_Depend_Metrics_Hierarchy_Analyzer::analyze()} calculates
     * the expected DIT values.
     *
     * @return void
     */
    public function testGetAllNodeMetrics()
    {
        $a = new PHP_Depend_Code_Class('a');
        $b = new PHP_Depend_Code_Class('b');
        $c = new PHP_Depend_Code_Class('c');
        $d = new PHP_Depend_Code_Class('d');
        $e = new PHP_Depend_Code_Class('e');
        
        $p = new PHP_Depend_Code_Package('p');
        $p->addType($a);
        $p->addType($b);
        $p->addType($c);
        $p->addType($d);
        $p->addType($e);
        
        $a->addChildType($b);
        $a->addChildType($c);
        $c->addChildType($d);
        $d->addChildType($e);
        
        $analyzer = new PHP_Depend_Metrics_Hierarchy_Analyzer();
        $result   = $analyzer->analyze(new PHP_Depend_Code_NodeIterator(array($p)));
        $actual   = $result->getAllNodeMetrics();
        
        $expected = array(
            $a->getUUID()  =>  array('dit'  =>  0),
            $b->getUUID()  =>  array('dit'  =>  1),
            $c->getUUID()  =>  array('dit'  =>  1),
            $d->getUUID()  =>  array('dit'  =>  2),
            $e->getUUID()  =>  array('dit'  =>  3),
        );
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Tests that {@link PHP_Depend_Metrics_Hierarchy_Analyzer::analyze()} calculates
     * the expected DIT values.
     *
     * @return void
     */
    public function testGetNodeMetrics()
    {
        $a = new PHP_Depend_Code_Class('a');
        $b = new PHP_Depend_Code_Class('b');
        $c = new PHP_Depend_Code_Class('c');
        $d = new PHP_Depend_Code_Class('d');
        $e = new PHP_Depend_Code_Class('e');
        
        $p = new PHP_Depend_Code_Package('p');
        $p->addType($a);
        $p->addType($b);
        $p->addType($c);
        $p->addType($d);
        $p->addType($e);
        
        $a->addChildType($b);
        $a->addChildType($c);
        $c->addChildType($d);
        $d->addChildType($e);
        
        $analyzer = new PHP_Depend_Metrics_Hierarchy_Analyzer();
        $result   = $analyzer->analyze(new PHP_Depend_Code_NodeIterator(array($p)));
        
        $expected = array(
            $a->getUUID()  =>  array('dit'  =>  0),
            $b->getUUID()  =>  array('dit'  =>  1),
            $c->getUUID()  =>  array('dit'  =>  1),
            $d->getUUID()  =>  array('dit'  =>  2),
            $e->getUUID()  =>  array('dit'  =>  3),
        );
        
        foreach ($expected as $uuid => $info) {
            $this->assertEquals($info, $result->getNodeMetrics($uuid));
        }
    }
}