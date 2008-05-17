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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';
require_once dirname(__FILE__) . '/ResultSetProjectAwareDummy.php';

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
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
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
     * Creates the package structure from a test source file.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        
        $fileName  = dirname(__FILE__) . '/../../_code/mixed_code.php';
        
        $tokenizer = new PHP_Depend_Code_Tokenizer_InternalTokenizer($fileName);
        $builder   = new PHP_Depend_Code_DefaultBuilder();
        $parser    = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
        
        $this->packages = $builder->getPackages();
    }
    
    public function testNodeAwareResultSetWithoutCode()
    {
        $metricsOne = array('interfs'  =>  42, 'cls'  =>  23);
        $resultOne  = new PHP_Depend_Log_Summary_ResultSetProjectAwareDummy($metricsOne);
        
        $metricsTwo = array('ncloc'  =>  1742, 'loc'  =>  4217);
        $resultTwo  = new PHP_Depend_Log_Summary_ResultSetProjectAwareDummy($metricsTwo);
        
        $log = new PHP_Depend_Log_Summary_Xml(new PHP_Depend_Code_NodeIterator(array()));
        $this->assertTrue($log->accept($resultOne));
        $this->assertTrue($log->accept($resultTwo));
        
        $log->write();
    }
}