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

namespace PDepend\Source\Language\PHP\Features\PHP81;

use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTIdentifier;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTMethodPostfix;
use PDepend\Source\AST\ASTReturnStatement;
use PDepend\Source\AST\ASTTypeCallable;
use PDepend\Source\AST\ASTVariable;

/**
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @group unittest
 * @group php8.1
 */
class FirstClassCallableSyntaxTest extends PHPParserVersion81TestCase
{
    public function testFirstClassCallable(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $children = $method->getChildren();
        static::assertInstanceOf(ASTTypeCallable::class, $children[1]);
        $children = $children[2]->getChildren();
        static::assertCount(1, $children);
        $return = $children[0];
        static::assertInstanceOf(ASTReturnStatement::class, $return);
        $children = $return->getChildren();
        static::assertCount(1, $children);
        $prefix = $children[0];
        static::assertInstanceOf(ASTMemberPrimaryPrefix::class, $prefix);
        $children = $prefix->getChildren();
        static::assertInstanceOf(ASTVariable::class, $children[0]);

        /** @var ASTMethodPostfix $methodPostFix */
        $methodPostFix = $children[1];
        static::assertInstanceOf(ASTMethodPostfix::class, $methodPostFix);
        static::assertSame('getTime', $methodPostFix->getImage());
        $children = $methodPostFix->getChildren();
        static::assertCount(2, $children);
        static::assertInstanceOf(ASTIdentifier::class, $children[0]);
        static::assertInstanceOf(ASTArguments::class, $children[1]);
    }
}
