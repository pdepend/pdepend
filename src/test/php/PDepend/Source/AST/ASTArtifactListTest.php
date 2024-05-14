<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\AST;

use BadMethodCallException;
use OutOfBoundsException;
use PDepend\AbstractTestCase;

/**
 * Test case the node iterator.
 *
 * @covers \PDepend\Source\AST\ASTArtifactList
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTArtifactListTest extends AbstractTestCase
{
    /**
     * Tests the ctor with an valid input array of {@link \PDepend\Source\AST\AbstractASTArtifact}
     * objects.
     */
    public function testCreateIteratorWidthValidInput(): void
    {
        $nodes = [
            new ASTClass('clazz', 0, 'clazz.php'),
            new ASTNamespace('pkg'),
            new ASTMethod('method', 0),
            new ASTFunction('func', 0),
        ];

        $it = new ASTArtifactList($nodes);

        static::assertEquals(4, $it->count());
    }

    /**
     * testNodeIteratorReturnsObjectsInUnmodifiedOrder
     */
    public function testNodeIteratorReturnsObjectsInUnmodifiedOrder(): void
    {
        $expected = [
            new ASTClass('clazz', 0, 'clazz.php'),
            new ASTFunction('func', 0),
            new ASTMethod('method', 0),
            new ASTNamespace('pkg'),
        ];

        $iterator = new ASTArtifactList($expected);

        $actual = [];
        foreach ($iterator as $codeNode) {
            $actual[] = $codeNode;
        }

        static::assertEquals($expected, $actual);
    }

    /**
     * testNodeIteratorReturnsObjectsUnique
     */
    public function testNodeIteratorReturnsObjectsUnique(): void
    {
        $iterator = new ASTArtifactList(
            [
                $object2 = new ASTClass('o2', 0, 'o2.php'),
                $object1 = new ASTClass('o1', 0, 'o1.php'),
                $object3 = new ASTClass('o3', 0, 'o3.php'),
                $object1,
                $object2,
                $object3,
            ]
        );

        $expected = [$object2, $object1, $object3];
        $actual = [];
        foreach ($iterator as $codeNode) {
            $actual[] = $codeNode;
        }

        static::assertEquals($expected, $actual);
    }

    /**
     * testIteratorUsesNodeNameAsItsIterationKey
     */
    public function testIteratorUsesNodeNameAsItsIterationKey(): void
    {
        $nodes = [
            new ASTClass('clazz', 0, 'clazz.php'),
            new ASTFunction('func', 0),
            new ASTMethod('method', 0),
            new ASTNamespace('pkg'),
        ];

        $iterator = new ASTArtifactList($nodes);

        $expected = ['clazz', 'func', 'method', 'pkg'];
        $actual = [];
        foreach ($iterator as $codeNode) {
            $actual[] = $iterator->key();
        }

        static::assertEquals($expected, $actual);
    }

    /**
     * testCurrentReturnsFalseWhenNoMoreElementExists
     */
    public function testCurrentThrowsOutOfBoundsExceptionWhenNoMoreElementExists(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $iterator = new ASTArtifactList([]);
        static::assertFalse($iterator->valid());
        $iterator->current();
    }

    /**
     * testArrayBehaviorOffsetExistsReturnsFalse
     *
     * @since 1.0.0
     */
    public function testArrayBehaviorOffsetExistsReturnsFalse(): void
    {
        $iterator = new ASTArtifactList([]);
        static::assertFalse(isset($iterator[1]));
    }

    /**
     * testArrayBehaviorOffsetExistsReturnsTrue
     *
     * @since 1.0.0
     */
    public function testArrayBehaviorOffsetExistsReturnsTrue(): void
    {
        $iterator = new ASTArtifactList(
            [
                new ASTClass('Class'),
                new ASTInterface('Interface'),
                new ASTTrait('Trait'),
            ]
        );
        static::assertTrue(isset($iterator[1]));
    }

    /**
     * testArrayBehaviorOffsetGetReturnsExpectedNode
     *
     * @since 1.0.0
     */
    public function testArrayBehaviorOffsetGetReturnsExpectedNode(): void
    {
        $iterator = new ASTArtifactList(
            [
                $class = new ASTClass('Class'),
                $interface = new ASTInterface('Interface'),
                $trait = new ASTTrait('Trait'),
            ]
        );
        static::assertSame($interface, $iterator[1]);
    }

    /**
     * testArrayBehaviorOffsetGetThrowsExpectedOutOfBoundsException
     *
     * @since 1.0.0
     */
    public function testArrayBehaviorOffsetGetThrowsExpectedOutOfBoundsException(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $iterator = new ASTArtifactList([]);
        $iterator[0]->getImage();
    }

    /**
     * testArrayBehaviorOffsetSetThrowsExpectedBadMethodCallException
     *
     * @since 1.0.0
     */
    public function testArrayBehaviorOffsetSetThrowsExpectedBadMethodCallException(): void
    {
        $this->expectException(BadMethodCallException::class);

        $iterator = new ASTArtifactList([]);
        $iterator[0] = new ASTClass('Class');
    }

    /**
     * testArrayBehaviorOffsetUnsetThrowsExpectedBadMethodCallException
     *
     * @since 1.0.0
     */
    public function testArrayBehaviorOffsetUnsetThrowsExpectedBadMethodCallException(): void
    {
        $this->expectException(BadMethodCallException::class);

        $iterator = new ASTArtifactList([]);
        unset($iterator[0]);
    }
}
