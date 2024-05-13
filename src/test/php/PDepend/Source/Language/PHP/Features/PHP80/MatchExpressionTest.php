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

namespace PDepend\Source\Language\PHP\Features\PHP80;

use PDepend\Source\AST\ASTFunctionPostfix;
use PDepend\Source\AST\ASTIdentifier;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMatchArgument;
use PDepend\Source\AST\ASTMatchBlock;
use PDepend\Source\AST\ASTMatchEntry;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTReturnStatement;
use PDepend\Source\AST\ASTSwitchLabel;
use PDepend\Source\AST\ASTThrowStatement;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\Parser\UnexpectedTokenException;

/**
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion80
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 * @group php8
 */
class MatchExpressionTest extends PHPParserVersion80TestCase
{
    public function testMatchExpression(): void
    {
        $this->checkMatchExpression();
    }

    public function testMatchExpressionWithNamespace(): void
    {
        $this->checkMatchExpression('Baz');
    }

    private function checkMatchExpression($namespacePrefix = null): void
    {
        $matchImage = implode('\\', array_filter([$namespacePrefix, 'match']));

        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType(ASTReturnStatement::class);
        $match = $returns[0]->getChild(0);

        $this->assertInstanceOf(ASTFunctionPostfix::class, $match);
        $this->assertSame($matchImage, $match->getImage());
        $this->assertSame('match', $match->getImageWithoutNamespace());
        /**
         * @var array{
         *     ASTIdentifier,
         *     ASTMatchArgument,
         *     ASTMatchBlock,
         * } $children
         */
        $children = $match->getChildren();
        $this->assertCount(3, $children);
        $this->assertInstanceOf(ASTIdentifier::class, $children[0]);
        $this->assertInstanceOf(ASTMatchArgument::class, $children[1]);
        $this->assertInstanceOf(ASTMatchBlock::class, $children[2]);
        $this->assertSame($matchImage, $children[0]->getImage());
        $this->assertSame('match', $children[0]->getImageWithoutNamespace());
        /** @var ASTVariable[] $arguments */
        $arguments = $children[1]->getChildren();
        $this->assertCount(1, $arguments);
        $this->assertInstanceOf(ASTVariable::class, $arguments[0]);
        $this->assertSame('$in', $arguments[0]->getImage());
        /** @var ASTMatchEntry[] $entries */
        $entries = $children[2]->getChildren();
        $this->assertCount(3, $entries);
        $this->assertInstanceOf(ASTMatchEntry::class, $entries[0]);
        /** @var ASTLiteral[] $literals */
        $literals = $entries[0]->getChildren();
        $this->assertCount(2, $literals);
        $this->assertInstanceOf(ASTLiteral::class, $literals[0]);
        $this->assertSame("'a'", $literals[0]->getImage());
        $this->assertInstanceOf(ASTLiteral::class, $literals[1]);
        $this->assertSame("'A'", $literals[1]->getImage());

        $this->assertInstanceOf(ASTMatchEntry::class, $entries[1]);
        /** @var ASTLiteral[] $literals */
        $literals = $entries[1]->getChildren();
        $this->assertCount(2, $literals);
        $this->assertInstanceOf(ASTLiteral::class, $literals[0]);
        $this->assertSame("'b'", $literals[0]->getImage());
        $this->assertInstanceOf(ASTLiteral::class, $literals[1]);
        $this->assertSame("'B'", $literals[1]->getImage());

        $this->assertInstanceOf(ASTMatchEntry::class, $entries[2]);
        /** @var array{ASTSwitchLabel, ASTThrowStatement} $pair */
        $pair = $entries[2]->getChildren();
        $this->assertCount(2, $pair);
        $this->assertInstanceOf(ASTSwitchLabel::class, $pair[0]);
        $this->assertSame('default', $pair[0]->getImage());
        $this->assertInstanceOf(ASTThrowStatement::class, $pair[1]);
        $this->assertSame('throw', $pair[1]->getImage());
        $this->assertCount(1, $pair[1]->getChildren());
        $new = $pair[1]->getChild(0);
        $this->assertInstanceOf(ASTThrowStatement::class, $pair[1]);
        $this->assertSame('new', $new->getImage());
        $this->assertSame(['\InvalidArgumentException', ''], array_map(
            static fn($node) => $node->getImage(),
            $new->getChildren(),
        ));
        $this->assertSame([
            [ASTLiteral::class, 'Invalid code ['],
            [ASTVariable::class, '$in'],
            [ASTLiteral::class, ']'],
        ], array_map(
            static fn($node) => [$node::class, $node->getImage()],
            $new->getChild(1)->getChild(0)->getChildren(),
        ));
    }

    public function testMatchExpressionWithMultipleKeyExpressions(): void
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType(ASTReturnStatement::class);
        $match = $returns[0]->getChild(0);

        $this->assertInstanceOf(ASTFunctionPostfix::class, $match);
        $this->assertSame('match', $match->getImage());
        /**
         * @var array{
         *     ASTIdentifier,
         *     ASTMatchArgument,
         *     ASTMatchBlock,
         * } $children
         */
        $children = $match->getChildren();
        $this->assertCount(3, $children);
        $this->assertInstanceOf(ASTIdentifier::class, $children[0]);
        $this->assertInstanceOf(ASTMatchArgument::class, $children[1]);
        $this->assertInstanceOf(ASTMatchBlock::class, $children[2]);
        $this->assertSame('match', $children[0]->getImage());
        /** @var ASTVariable[] $arguments */
        $arguments = $children[1]->getChildren();
        $this->assertCount(1, $arguments);
        $this->assertInstanceOf(ASTVariable::class, $arguments[0]);
        $this->assertSame('$in', $arguments[0]->getImage());
        /** @var ASTMatchEntry[] $entries */
        $entries = $children[2]->getChildren();
        $this->assertCount(3, $entries);
        $this->assertInstanceOf(ASTMatchEntry::class, $entries[0]);
        /** @var ASTLiteral[] $literals */
        $literals = $entries[0]->getChildren();
        $this->assertCount(3, $literals);
        $this->assertInstanceOf(ASTLiteral::class, $literals[0]);
        $this->assertSame("'a'", $literals[0]->getImage());
        $this->assertInstanceOf(ASTLiteral::class, $literals[1]);
        $this->assertSame("'b'", $literals[1]->getImage());
        $this->assertInstanceOf(ASTLiteral::class, $literals[2]);
        $this->assertSame("'AB'", $literals[2]->getImage());

        $this->assertInstanceOf(ASTMatchEntry::class, $entries[1]);
        /** @var ASTLiteral[] $literals */
        $literals = $entries[1]->getChildren();
        $this->assertCount(2, $literals);
        $this->assertInstanceOf(ASTLiteral::class, $literals[0]);
        $this->assertSame("1", $literals[0]->getImage());
        $this->assertInstanceOf(ASTLiteral::class, $literals[1]);
        $this->assertSame("'One'", $literals[1]->getImage());

        $this->assertInstanceOf(ASTMatchEntry::class, $entries[2]);
        /** @var array{ASTSwitchLabel, ASTThrowStatement} $pair */
        $pair = $entries[2]->getChildren();
        $this->assertCount(2, $pair);
        $this->assertInstanceOf(ASTSwitchLabel::class, $pair[0]);
        $this->assertSame('default', $pair[0]->getImage());
        $this->assertInstanceOf(ASTThrowStatement::class, $pair[1]);
        $this->assertSame('throw', $pair[1]->getImage());
        $this->assertCount(1, $pair[1]->getChildren());
        $new = $pair[1]->getChild(0);
        $this->assertInstanceOf(ASTThrowStatement::class, $pair[1]);
        $this->assertSame('new', $new->getImage());
        $this->assertSame(['\InvalidArgumentException', ''], array_map(
            static fn($node) => $node->getImage(),
            $new->getChildren(),
        ));
        $this->assertSame([
            [ASTLiteral::class, 'Invalid code ['],
            [ASTVariable::class, '$in'],
            [ASTLiteral::class, ']'],
        ], array_map(
            static fn($node) => [$node::class, $node->getImage()],
            $new->getChild(1)->getChild(0)->getChildren(),
        ));
    }

    public function testMatchExpressionWithTooManyArguments(): void
    {
        $this->expectException(UnexpectedTokenException::class);
        $this->expectExceptionMessage('Unexpected token: ,, line: 5, col: 25');

        $this->parseCodeResourceForTest();
    }
}
