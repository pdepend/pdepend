<?php
/**
 * This file is part of PHP_Reflection.
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
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';
require_once dirname(__FILE__) . '/_dummy/TestImplNode.php';

require_once 'PHP/Reflection/AST/Iterator.php';
require_once 'PHP/Reflection/AST/Class.php';
require_once 'PHP/Reflection/AST/Function.php';
require_once 'PHP/Reflection/AST/Method.php';
require_once 'PHP/Reflection/AST/Package.php';

/**
 * Test case the node iterator.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_AST_IteratorTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests the ctor with an invalid input array which must result in an exception.
     *
     * @return void
     */
    public function testCreateIteratorWithInvalidInputFail()
    {
        $this->setExpectedException('RuntimeException');
        new PHP_Reflection_AST_Iterator(array(new stdClass()));
    }
    
    /**
     * Tests the ctor with an valid input array of {@link PHP_Reflection_AST_NodeI}
     * objects.
     *
     * @return void
     */
    public function testCreateIteratorWithValidInput()
    {
        $nodes = array(
            new PHP_Reflection_AST_TestImplNode('clazz'),
            new PHP_Reflection_AST_TestImplNode('pkg'),
            new PHP_Reflection_AST_TestImplNode('method'),
            new PHP_Reflection_AST_TestImplNode('func'),
        );
        
        $it = new PHP_Reflection_AST_Iterator($nodes);
        
        $this->assertEquals(4, $it->count());
    }
    
    /**
     * Tests that the iterator returns the node name as key.
     *
     * @return void
     */
    public function testIteratorLoopWithKey()
    {
        $names = array('clazz', 'func', 'method', 'pkg');
        $nodes = array(
            new PHP_Reflection_AST_TestImplNode('clazz'),
            new PHP_Reflection_AST_TestImplNode('func'),
            new PHP_Reflection_AST_TestImplNode('method'),
            new PHP_Reflection_AST_TestImplNode('pkg'),
        );
        
        $it = new PHP_Reflection_AST_Iterator($nodes);
        
        for ($i = 0, $it->rewind(); $it->valid(); ++$i, $it->next()) {
            $this->assertSame($nodes[$i], $it->current());
            $this->assertEquals($names[$i], $it->key());
        }
    }
}