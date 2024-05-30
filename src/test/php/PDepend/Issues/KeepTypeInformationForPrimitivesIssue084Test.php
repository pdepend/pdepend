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

namespace PDepend\Issues;

use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTType;

/**
 * Test case for issue #84, where the object model should keep information about
 * primitive property and parameter types.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class KeepTypeInformationForPrimitivesIssue084Test extends AbstractFeatureTestCase
{
    /**
     * Tests that the parser sets the expected primitive type information.
     *
     * @param string $actual The actual used type identifier.
     * @param string $expected The expected primitive type image.
     * @dataProvider dataProviderParserSetsExpectedPrimitivePropertyType
     */
    public function testParserSetsExpectedPrimitivePropertyType(string $actual, string $expected): void
    {
        $type = $this->parseTestCase(__METHOD__ . '_' . $actual)
            ->current()
            ->getClasses()
            ->current()
            ->getFirstChildOfType(ASTFieldDeclaration::class)
            ?->getFirstChildOfType(ASTType::class);

        static::assertEquals($expected, $type?->getImage());
    }

    /**
     * Tests that the parser sets the expected array type information.
     */
    public function testParserSetsExpectedArrayPropertyType(): void
    {
        $type = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTFieldDeclaration::class)
            ?->getFirstChildOfType(ASTType::class);

        static::assertTrue($type?->isArray());
    }

    /**
     * Tests that the parser sets the expected array type information.
     */
    public function testParserSetsExpectedArrayWithParenthesisPropertyType(): void
    {
        $type = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTFieldDeclaration::class)
            ?->getFirstChildOfType(ASTType::class);

        static::assertTrue($type?->isArray());
    }

    /**
     * Data provider that returns a list of actual input and expected output
     * primitive types.
     *
     * @return list<list<string>>
     */
    public static function dataProviderParserSetsExpectedPrimitivePropertyType(): array
    {
        return [
            ['int', 'integer'],
            ['INTEger', 'integer'],
            ['float', 'float'],
            ['real', 'float'],
            ['double', 'float'],
            ['bool', 'boolean'],
            ['boolean', 'boolean'],
            ['false', 'boolean'],
            ['true', 'boolean'],
        ];
    }
}
