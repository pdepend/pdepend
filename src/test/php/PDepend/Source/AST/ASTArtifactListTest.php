<?php
/**
 * This file is part of PDepend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
  */

namespace PDepend\Source\AST;

use PDepend\AbstractTest;

/**
 * Test case the node iterator.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\AST\ASTArtifactList
 * @group unittest
 */
class ASTArtifactListTest extends AbstractTest
{
    /**
     * Tests the ctor with an valid input array of {@link \PDepend\Source\AST\AbstractASTArtifact}
     * objects.
     *
     * @return void
     */
    public function testCreateIteratorWidthValidInput()
    {
        $nodes = array(
            new ASTClass('clazz', 0, 'clazz.php'),
            new ASTNamespace('pkg'),
            new ASTMethod('method', 0),
            new ASTFunction('func', 0),
        );
        
        $it = new ASTArtifactList($nodes);
        
        $this->assertEquals(4, $it->count());
    }
    
    /**
     * testNodeIteratorReturnsObjectsInUnmodifiedOrder
     *
     * @return void
     */
    public function testNodeIteratorReturnsObjectsInUnmodifiedOrder()
    {
        $expected = array(
            new ASTClass('clazz', 0, 'clazz.php'),
            new ASTFunction('func', 0),
            new ASTMethod('method', 0),
            new ASTNamespace('pkg'),
        );
        
        $iterator = new ASTArtifactList($expected);

        $actual = array();
        foreach ($iterator as $codeNode) {
            $actual[] = $codeNode;
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * testNodeIteratorReturnsObjectsUnique
     *
     * @return void
     */
    public function testNodeIteratorReturnsObjectsUnique()
    {
        $iterator = new ASTArtifactList(
            array(
                $object2 = new ASTClass('o2', 0, 'o2.php'),
                $object1 = new ASTClass('o1', 0, 'o1.php'),
                $object3 = new ASTClass('o3', 0, 'o3.php'),
                $object1,
                $object2,
                $object3
            )
        );

        $expected = array($object2, $object1, $object3);
        $actual   = array();
        foreach ($iterator as $codeNode) {
            $actual[] = $codeNode;
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * testIteratorUsesNodeNameAsItsIterationKey
     *
     * @return void
     */
    public function testIteratorUsesNodeNameAsItsIterationKey()
    {
        $nodes = array(
            new ASTClass('clazz', 0, 'clazz.php'),
            new ASTFunction('func', 0),
            new ASTMethod('method', 0),
            new ASTNamespace('pkg'),
        );

        $iterator = new ASTArtifactList($nodes);

        $expected = array('clazz', 'func', 'method', 'pkg');
        $actual   = array();
        foreach ($iterator as $codeNode) {
            $actual[] = $iterator->key();
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * testCurrentReturnsFalseWhenNoMoreElementExists
     *
     * @return void
     */
    public function testCurrentReturnsFalseWhenNoMoreElementExists()
    {
        $iterator = new ASTArtifactList(array());
        $this->assertFalse($iterator->current());
    }

    /**
     * testArrayBehaviorOffsetExistsReturnsFalse
     *
     * @return void
     * @since 1.0.0
     */
    public function testArrayBehaviorOffsetExistsReturnsFalse()
    {
        $iterator = new ASTArtifactList(array());
        $this->assertFalse(isset($iterator[1]));
    }

    /**
     * testArrayBehaviorOffsetExistsReturnsTrue
     *
     * @return void
     * @since 1.0.0
     */
    public function testArrayBehaviorOffsetExistsReturnsTrue()
    {
        $iterator = new ASTArtifactList(
            array(
                new ASTClass('Class'),
                new ASTInterface('Interface'),
                new ASTTrait('Trait')
            )
        );
        $this->assertTrue(isset($iterator[1]));
    }

    /**
     * testArrayBehaviorOffsetGetReturnsExpectedNode
     *
     * @return void
     * @since 1.0.0
     */
    public function testArrayBehaviorOffsetGetReturnsExpectedNode()
    {
        $iterator = new ASTArtifactList(
            array(
                $class     = new ASTClass('Class'),
                $interface = new ASTInterface('Interface'),
                $trait     = new ASTTrait('Trait')
            )
        );
        $this->assertSame($interface, $iterator[1]);
    }

    /**
     * testArrayBehaviorOffsetGetThrowsExpectedOutOfBoundsException
     *
     * @return void
     * @since 1.0.0
     * @expectedException OutOfBoundsException
     */
    public function testArrayBehaviorOffsetGetThrowsExpectedOutOfBoundsException()
    {
        $iterator = new ASTArtifactList(array());
        $iterator[0]->getName();
    }

    /**
     * testArrayBehaviorOffsetSetThrowsExpectedBadMethodCallException
     *
     * @return void
     * @since 1.0.0
     * @expectedException BadMethodCallException
     */
    public function testArrayBehaviorOffsetSetThrowsExpectedBadMethodCallException()
    {
        $iterator    = new ASTArtifactList(array());
        $iterator[0] = new ASTClass('Class');
    }

    /**
     * testArrayBehaviorOffsetUnsetThrowsExpectedBadMethodCallException
     *
     * @return void
     * @since 1.0.0
     * @expectedException BadMethodCallException
     */
    public function testArrayBehaviorOffsetUnsetThrowsExpectedBadMethodCallException()
    {
        $iterator = new ASTArtifactList(array());
        unset($iterator[0]);
    }
}
