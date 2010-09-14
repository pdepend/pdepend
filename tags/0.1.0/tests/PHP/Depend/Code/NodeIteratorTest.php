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

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/Package.php';

/**
 * Test case the node iterator.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Code_NodeIteratorTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the ctor with an invalid input array which must result in an exception.
     *
     * @return void
     */
    public function testCreateIteratorWithInvalidInputFail()
    {
        $this->setExpectedException('RuntimeException');
        
        $nodes = array(
            new PHP_Depend_Code_Class('clazz1', 'clazz1.php'),
            new PHP_Depend_Code_Package('pkg'),
            new stdClass(),
            new PHP_Depend_Code_Class('clazz2', 'clazz2.php'),
        );
        
        new PHP_Depend_Code_NodeIterator($nodes);
    }
    
    /**
     * Tests the ctor with an valid input array of {@link PHP_Depend_Code_Node}
     * objects.
     *
     * @return void
     */
    public function testCreateIteratorWidthValidInput()
    {
        $nodes = array(
            new PHP_Depend_Code_Class('clazz', 'clazz.php'),
            new PHP_Depend_Code_Package('pkg'),
            new PHP_Depend_Code_Method('method'),
            new PHP_Depend_Code_Function('func'),
        );
        
        $it = new PHP_Depend_Code_NodeIterator($nodes);
        
        $this->assertEquals(4, $it->count());
    }
    
    /**
     * Tests that the iterator returns the node name as key.
     *
     * @return void
     */
    public function testNodeIteratorLoopWithKey()
    {
        $names = array('clazz', 'pkg', 'method', 'func');
        $nodes = array(
            new PHP_Depend_Code_Class('clazz', 'clazz.php'),
            new PHP_Depend_Code_Package('pkg'),
            new PHP_Depend_Code_Method('method'),
            new PHP_Depend_Code_Function('func'),
        );
        
        $it = new PHP_Depend_Code_NodeIterator($nodes);
        
        for ($i = 0, $it->rewind(); $it->valid(); ++$i, $it->next()) {
            $this->assertSame($nodes[$i], $it->current());
            $this->assertEquals($names[$i], $it->key());
        }
    }
}