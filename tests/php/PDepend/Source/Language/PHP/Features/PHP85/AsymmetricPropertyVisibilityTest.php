<?php

/**
 * This file is part of PDepend.
 *
 * Copyright (c) 2008-2026 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2026 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\Language\PHP\Features\PHP85;

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\Language\PHP\PHPParserVersion85;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @copyright 2008-2026 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
#[CoversClass(PHPTokenizerInternal::class)]
#[CoversClass(ASTFieldDeclaration::class)]
#[CoversClass(PHPParserVersion85::class)]
#[Group('unittest')]
#[Group('php8.5')]
class AsymmetricPropertyVisibilityTest extends PHPParserVersion85TestCase
{
    /**
     * testStaticProperty
     */
    public function testStaticProperty(): void
    {
        $class = $this->getFirstTypeForTestCase();
        assert($class instanceof ASTClass);

        /** @var ASTFieldDeclaration[] $properties */
        $properties = $class->getChildren();

        static::assertCount(1, $properties);

        static::assertTrue($properties[0]->isPublic());
        static::assertFalse($properties[0]->isProtected());
        static::assertFalse($properties[0]->isPrivate());
        static::assertFalse($properties[0]->isProtectedSet());
        static::assertTrue($properties[0]->isPrivateSet());
        static::assertTrue($properties[0]->isStatic());
    }
}
