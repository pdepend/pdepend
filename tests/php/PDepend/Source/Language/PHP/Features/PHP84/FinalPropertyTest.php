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

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTPropertyHook;
use PDepend\Source\AST\State;
use PDepend\Source\Language\PHP\PHPParserVersion84;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
#[CoversClass(ASTFieldDeclaration::class)]
#[CoversClass(PHPParserVersion84::class)]
#[Group('unittest')]
#[Group('php8.4')]
class FinalPropertyTest extends PHPParserVersion84TestCase
{
    /**
     * testFinalProperty
     */
    public function testFinalProperty(): void
    {
        $property = $this->getFirstPropertyForTestCase();

        static::assertTrue($property->isPublic());
        static::assertFalse($property->isStatic());
        static::assertSame(State::IS_FINAL, $property->getModifiers() & State::IS_FINAL);
    }

    /**
     * testFinalStaticPropertyAfterVisibility
     */
    public function testFinalStaticPropertyAfterVisibility(): void
    {
        $property = $this->getFirstPropertyForTestCase();

        static::assertTrue($property->isProtected());
        static::assertTrue($property->isStatic());
        static::assertSame(State::IS_FINAL, $property->getModifiers() & State::IS_FINAL);
    }

    /**
     * testFinalPropertyWithHook
     */
    public function testFinalPropertyWithHook(): void
    {
        $property = $this->getFirstPropertyForTestCase();

        static::assertTrue($property->isPublic());
        static::assertSame(State::IS_FINAL, $property->getModifiers() & State::IS_FINAL);
        static::assertCount(1, $property->findChildrenOfType(ASTPropertyHook::class));
    }

    private function getFirstPropertyForTestCase(): ASTFieldDeclaration
    {
        $class = $this->getFirstTypeForTestCase();
        static::assertInstanceOf(ASTClass::class, $class);

        $properties = $class->findChildrenOfType(ASTFieldDeclaration::class);
        static::assertCount(1, $properties);

        return $properties[0];
    }
}
