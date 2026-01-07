<?php

/**
 * This file is part of PDepend.
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

namespace PDepend\Source\Language\PHP\Features\PHP84;

use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTPropertyHook;
use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserVersion84;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
#[CoversClass(PHPBuilder::class)]
#[CoversClass(ASTConstantDeclarator::class)]
#[CoversClass(ASTPropertyHook::class)]
#[CoversClass(PHPParserVersion84::class)]
#[Group('unittest')]
#[Group('php8.4')]
class PropertyHookTest extends PHPParserVersion84TestCase
{
    public function testArrowHook(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertTrue($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());
    }

    public function testDefault(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertTrue($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());
    }

    public function testDefaultExpression(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertTrue($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());
    }

    public function testFinal(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertTrue($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertTrue($hooks[0]->isFinal());
    }

    public function testGetMethod(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertTrue($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());
    }

    public function testGetSet(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(2, $hooks);

        static::assertTrue($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());

        static::assertTrue($hooks[1]->isPublic());
        static::assertFalse($hooks[1]->isProtected());
        static::assertFalse($hooks[1]->isPrivate());
        static::assertFalse($hooks[1]->isFinal());
    }

    public function testParentAccess(): void
    {
        $hooks = $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()[1]
            ->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertTrue($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());
    }

    public function testPromotion(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertFalse($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertTrue($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());
    }

    public function testSetMethod(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertTrue($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());
    }

    public function testSetVisibility(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertFalse($hooks[0]->isPublic());
        static::assertTrue($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());
    }

    public function testTypedArrow(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertTrue($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());

        $param = $hooks[0]->findChildrenOfType(ASTFormalParameter::class);

        static::assertCount(1, $param);

        static::assertTrue($param[0]->hasType());
    }

    public function testTyped(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertTrue($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());

        $param = $hooks[0]->findChildrenOfType(ASTFormalParameter::class);

        static::assertCount(1, $param);

        static::assertTrue($param[0]->hasType());
    }

    public function testAttribute(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertTrue($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());
    }

    public function testComment(): void
    {
        $hooks = $this->getFirstTypeForTestCase()->findChildrenOfType(ASTPropertyHook::class);

        static::assertCount(1, $hooks);

        static::assertTrue($hooks[0]->isPublic());
        static::assertFalse($hooks[0]->isProtected());
        static::assertFalse($hooks[0]->isPrivate());
        static::assertFalse($hooks[0]->isFinal());
    }
}
