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
 * @since 1.0.0
 */

namespace PDepend\Source\AST\ASTArtifactList;

use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTTrait;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTArtifactList\NullArtifactFilter} class.
 *
 * @covers \PDepend\Source\AST\ASTArtifactList\NullArtifactFilter
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 *
 * @group unittest
 */
class NullArtifactFilterTest extends AbstractTestCase
{
    /**
     * testAcceptsReturnsTrueForClass
     */
    public function testAcceptsReturnsTrueForClass(): void
    {
        $filter = new NullArtifactFilter();
        static::assertTrue($filter->accept(new ASTClass(__CLASS__)));
    }

    /**
     * testAcceptsReturnsTrueForFile
     */
    public function testAcceptsReturnsTrueForFile(): void
    {
        $filter = new NullArtifactFilter();
        static::assertTrue($filter->accept(new ASTCompilationUnit(__FILE__)));
    }

    /**
     * testAcceptsReturnsTrueForFunction
     */
    public function testAcceptsReturnsTrueForFunction(): void
    {
        $filter = new NullArtifactFilter();
        static::assertTrue($filter->accept(new ASTFunction(__CLASS__)));
    }

    /**
     * testAcceptsReturnsTrueForInterface
     */
    public function testAcceptsReturnsTrueForInterface(): void
    {
        $filter = new NullArtifactFilter();
        static::assertTrue($filter->accept(new ASTInterface(__CLASS__)));
    }

    /**
     * testAcceptsReturnsTrueForMethod
     */
    public function testAcceptsReturnsTrueForMethod(): void
    {
        $filter = new NullArtifactFilter();
        static::assertTrue($filter->accept(new ASTMethod(__CLASS__)));
    }

    /**
     * testAcceptsReturnsTrueForPackage
     */
    public function testAcceptsReturnsTrueForPackage(): void
    {
        $filter = new NullArtifactFilter();
        static::assertTrue($filter->accept(new ASTNamespace(__CLASS__)));
    }

    /**
     * testAcceptsReturnsTrueForTrait
     */
    public function testAcceptsReturnsTrueForTrait(): void
    {
        $filter = new NullArtifactFilter();
        static::assertTrue($filter->accept(new ASTTrait(__CLASS__)));
    }
}
