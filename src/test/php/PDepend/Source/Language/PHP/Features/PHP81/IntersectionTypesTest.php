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

use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTIntersectionType;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTType;
use PDepend\Source\AST\ASTVariableDeclarator;
use PDepend\Source\Parser\ParserException;

/**
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @group unittest
 * @group php8.1
 */
class IntersectionTypesTest extends PHPParserVersion81TestCase
{
    public function testIntersectionTypes(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();

        /** @var ASTFormalParameter $parameter */
        $parameter = $method->getFirstChildOfType(ASTFormalParameter::class);
        $children = $parameter->getChildren();

        static::assertInstanceOf(ASTIntersectionType::class, $children[0]);

        /** @var ASTIntersectionType $intersectionType */
        $intersectionType = $children[0];
        static::assertSame('Iterator&\Countable&\ArrayAccess', $intersectionType->getImage());

        static::assertInstanceOf(ASTVariableDeclarator::class, $children[1]);

        /** @var ASTVariableDeclarator $variable */
        $variable = $children[1];
        static::assertSame('$iterator', $variable->getImage());
    }

    public function testIntersectionTypesWithByReference(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();

        /** @var ASTFormalParameter $parameter */
        $parameter = $method->getFirstChildOfType(ASTFormalParameter::class);
        $children = $parameter->getChildren();

        static::assertInstanceOf(ASTIntersectionType::class, $children[0]);

        /** @var ASTIntersectionType $intersectionType */
        $intersectionType = $children[0];
        static::assertSame('Iterator&\Countable&\ArrayAccess', $intersectionType->getImage());
    }

    public function testIntersectionTypesAsReturn(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();

        /** @var ASTType $return */
        $return = $method->getFirstChildOfType(
            ASTType::class
        );

        static::assertInstanceOf(ASTIntersectionType::class, $return);
        static::assertSame('Iterator&\Countable&\ArrayAccess', $return->getImage());
    }

    public function testIntersectionTypesCantBeMixedWithUnionTypes(): void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage('Unexpected token');

        $this->getFirstMethodForTestCase();
    }

    public function testIntersectionTypesCantBeScalar(): void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage('int can not be used in an intersection type');

        $this->getFirstMethodForTestCase();
    }
}
