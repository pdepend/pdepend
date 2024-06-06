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

use PDepend\AbstractTestCase;

/**
 * Test case for the {@link \PDepend\Source\AST\AbstractASTArtifact} class.
 *
 * @covers \PDepend\Source\AST\AbstractASTArtifact
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTArtifactTest extends AbstractTestCase
{
    /**
     * testGetNameReturnsValueOfFirstConstructorArgument
     */
    public function testGetNameReturnsValueOfFirstConstructorArgument(): void
    {
        $item = $this->getItemMock();
        static::assertEquals(__CLASS__, $item->getImage());
    }

    /**
     * testSetNameOverridesPreviousItemName
     *
     * @since 1.0.0
     */
    public function testSetNameOverridesPreviousItemName(): void
    {
        $item = $this->getItemMock();
        $item->setName(__FUNCTION__);

        static::assertEquals(__FUNCTION__, $item->getImage());
    }

    /**
     * testGetIdReturnsMd5HashByDefault
     */
    public function testGetIdReturnsMd5HashByDefault(): void
    {
        $item = $this->getItemMock();
        static::assertMatchesRegularExpression('(^[a-f0-9]{32}$)', $item->getId());
    }

    /**
     * testGetIdReturnsInjectedIdValue
     */
    public function testGetIdReturnsInjectedIdValue(): void
    {
        $item = $this->getItemMock();
        $item->setId(__METHOD__);

        static::assertEquals(__METHOD__, $item->getId());
    }

    /**
     * testGetSourceFileReturnsNullByDefault
     */
    public function testGetSourceFileReturnsNullByDefault(): void
    {
        $item = $this->getItemMock();
        static::assertNull($item->getCompilationUnit());
    }

    /**
     * testGetSourceFileReturnsInjectedFileInstance
     */
    public function testGetSourceFileReturnsInjectedFileInstance(): void
    {
        $file = new ASTCompilationUnit(__FILE__);

        $item = $this->getItemMock();
        $item->setCompilationUnit($file);

        static::assertSame($file, $item->getCompilationUnit());
    }

    /**
     * testGetDocCommentReturnsNullByDefault
     */
    public function testGetDocCommentReturnsNullByDefault(): void
    {
        $item = $this->getItemMock();
        static::assertNull($item->getComment());
    }

    /**
     * testGetDocCommentReturnsInjectedDocCommentValue
     */
    public function testGetDocCommentReturnsInjectedDocCommentValue(): void
    {
        $item = $this->getItemMock();
        $item->setComment('/** Manuel */');

        static::assertSame('/** Manuel */', $item->getComment());
    }

    /**
     * Returns a mocked item instance.
     */
    protected function getItemMock(): AbstractASTArtifact
    {
        return $this->getMockForAbstractClass(
            AbstractASTArtifact::class,
            [__CLASS__]
        );
    }
}
