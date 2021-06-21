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

use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTReturnStatement;

/**
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion80
 * @group unittest
 * @group php8
 */
class MatchExpressionTest extends PHPParserVersion80Test
{
    /**
     * @return void
     */
    public function testMatchExpression()
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType('PDepend\\Source\\AST\\ASTReturnStatement');
        $match = $returns[0]->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFunctionPostfix', $match);
        $this->assertSame('match', $match->getImage());
        /**
         * @var array{
         *     \PDepend\Source\AST\ASTIdentifier,
         *     \PDepend\Source\AST\ASTMatchArgument,
         *     \PDepend\Source\AST\ASTMatchBlock,
         * } $children
         */
        $children = $match->getChildren();
        $this->assertCount(3, $children);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTIdentifier', $children[0]);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMatchArgument', $children[1]);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMatchBlock', $children[2]);
        $this->assertSame('match', $children[0]->getImage());
        /** @var \PDepend\Source\AST\ASTVariable[] $arguments */
        $arguments = $children[1]->getChildren();
        $this->assertCount(1, $arguments);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $arguments[0]);
        $this->assertSame('$in', $arguments[0]->getImage());
        /** @var \PDepend\Source\AST\ASTMatchEntry[] $entries */
        $entries = $children[2]->getChildren();
        $this->assertCount(3, $entries);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMatchEntry', $entries[0]);
        /** @var \PDepend\Source\AST\ASTLiteral[] $literals */
        $literals = $entries[0]->getChildren();
        $this->assertCount(2, $literals);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $literals[0]);
        $this->assertSame("'a'", $literals[0]->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $literals[1]);
        $this->assertSame("'A'", $literals[1]->getImage());

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMatchEntry', $entries[1]);
        /** @var \PDepend\Source\AST\ASTLiteral[] $literals */
        $literals = $entries[1]->getChildren();
        $this->assertCount(2, $literals);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $literals[0]);
        $this->assertSame("'b'", $literals[0]->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $literals[1]);
        $this->assertSame("'B'", $literals[1]->getImage());

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMatchEntry', $entries[2]);
        /** @var array{\PDepend\Source\AST\ASTSwitchLabel, \PDepend\Source\AST\ASTThrowStatement} $pair */
        $pair = $entries[2]->getChildren();
        $this->assertCount(2, $pair);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSwitchLabel', $pair[0]);
        $this->assertSame('default', $pair[0]->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTThrowStatement', $pair[1]);
        $this->assertSame('throw', $pair[1]->getImage());
        $this->assertCount(1, $pair[1]->getChildren());
        $new = $pair[1]->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTThrowStatement', $pair[1]);
        $this->assertSame('new', $new->getImage());
        $this->assertSame(array('\InvalidArgumentException', ''), array_map(function ($node) {
            return $node->getImage();
        }, $new->getChildren()));
        $this->assertSame(array(
            array('PDepend\\Source\\AST\\ASTLiteral', 'Invalid code ['),
            array('PDepend\\Source\\AST\\ASTVariable', '$in'),
            array('PDepend\\Source\\AST\\ASTLiteral', ']'),
        ), array_map(function ($node) {
            return array(get_class($node), $node->getImage());
        }, $new->getChild(1)->getChild(0)->getChildren()));
    }

    /**
     * @return void
     */
    public function testMatchExpressionWithMultipleKeyExpressions()
    {
        /** @var ASTMethod $method */
        $method = $this->getFirstMethodForTestCase();
        /** @var ASTReturnStatement[] $returns */
        $returns = $method->findChildrenOfType('PDepend\\Source\\AST\\ASTReturnStatement');
        $match = $returns[0]->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFunctionPostfix', $match);
        $this->assertSame('match', $match->getImage());
        /**
         * @var array{
         *     \PDepend\Source\AST\ASTIdentifier,
         *     \PDepend\Source\AST\ASTMatchArgument,
         *     \PDepend\Source\AST\ASTMatchBlock,
         * } $children
         */
        $children = $match->getChildren();
        $this->assertCount(3, $children);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTIdentifier', $children[0]);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMatchArgument', $children[1]);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMatchBlock', $children[2]);
        $this->assertSame('match', $children[0]->getImage());
        /** @var \PDepend\Source\AST\ASTVariable[] $arguments */
        $arguments = $children[1]->getChildren();
        $this->assertCount(1, $arguments);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $arguments[0]);
        $this->assertSame('$in', $arguments[0]->getImage());
        /** @var \PDepend\Source\AST\ASTMatchEntry[] $entries */
        $entries = $children[2]->getChildren();
        $this->assertCount(3, $entries);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMatchEntry', $entries[0]);
        /** @var \PDepend\Source\AST\ASTLiteral[] $literals */
        $literals = $entries[0]->getChildren();
        $this->assertCount(3, $literals);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $literals[0]);
        $this->assertSame("'a'", $literals[0]->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $literals[1]);
        $this->assertSame("'b'", $literals[1]->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $literals[2]);
        $this->assertSame("'AB'", $literals[2]->getImage());

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMatchEntry', $entries[1]);
        /** @var \PDepend\Source\AST\ASTLiteral[] $literals */
        $literals = $entries[1]->getChildren();
        $this->assertCount(2, $literals);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $literals[0]);
        $this->assertSame("1", $literals[0]->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $literals[1]);
        $this->assertSame("'One'", $literals[1]->getImage());

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMatchEntry', $entries[2]);
        /** @var array{\PDepend\Source\AST\ASTSwitchLabel, \PDepend\Source\AST\ASTThrowStatement} $pair */
        $pair = $entries[2]->getChildren();
        $this->assertCount(2, $pair);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSwitchLabel', $pair[0]);
        $this->assertSame('default', $pair[0]->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTThrowStatement', $pair[1]);
        $this->assertSame('throw', $pair[1]->getImage());
        $this->assertCount(1, $pair[1]->getChildren());
        $new = $pair[1]->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTThrowStatement', $pair[1]);
        $this->assertSame('new', $new->getImage());
        $this->assertSame(array('\InvalidArgumentException', ''), array_map(function ($node) {
            return $node->getImage();
        }, $new->getChildren()));
        $this->assertSame(array(
            array('PDepend\\Source\\AST\\ASTLiteral', 'Invalid code ['),
            array('PDepend\\Source\\AST\\ASTVariable', '$in'),
            array('PDepend\\Source\\AST\\ASTLiteral', ']'),
        ), array_map(function ($node) {
            return array(get_class($node), $node->getImage());
        }, $new->getChild(1)->getChild(0)->getChildren()));
    }

    /**
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     * @expectedExceptionMessage Unexpected token: ,, line: 5, col: 25
     * @return void
     */
    public function testMatchExpressionWithTooManyArguments()
    {
        $this->parseCodeResourceForTest();
    }
}
