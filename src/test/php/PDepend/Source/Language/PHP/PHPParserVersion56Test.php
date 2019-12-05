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
 * @since 2.3
 */

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTest;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Util\Cache\CacheDriver;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserVersion56} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 2.3
 *
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion56
 * @group unittest
 */
class PHPParserVersion56Test extends AbstractTest
{
    /**
     * testComplexExpressionInParameterInitializer
     *
     * @return void
     */
    public function testComplexExpressionInParameterInitializer()
    {
        $node = $this->getFirstFunctionForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTFormalParameter');

        $this->assertNotNull($node);
    }

    /**
     * testComplexExpressionInConstantInitializer
     *
     * @return void
     */
    public function testComplexExpressionInConstantDeclarator()
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTConstantDeclarator');

        $this->assertNotNull($node);
    }

    /**
     * testComplexExpressionInFieldDeclaration
     *
     * @return void
     */
    public function testComplexExpressionInFieldDeclaration()
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTFieldDeclaration');

        $this->assertNotNull($node);
    }

    /**
     * testPowExpressionInMethodBody
     *
     * @return void
     */
    public function testPowExpressionInMethodBody()
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTReturnStatement');

        $this->assertSame('**', $node->getChild(0)->getChild(1)->getImage());
    }

    /**
     * testPowExpressionInFieldDeclaration
     *
     * @return void
     */
    public function testPowExpressionInFieldDeclaration()
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTFieldDeclaration');

        $this->assertNotNull($node);
    }

    /**
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     * @expectedExceptionMessageRegExp (Unexpected token: list, line: 4, col: 21, file: )
     */
    public function testListKeywordAsMethodNameThrowsException()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     * @expectedExceptionMessageRegExp (Unexpected token: list, line: 2, col: 10, file: )
     */
    public function testListKeywordAsFunctionNameThrowsException()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * @return void
     */
    public function testUseStatement()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     * @expectedExceptionMessageRegExp (^Unexpected token: \{, line: 2, col: 24, file: )
     */
    public function testGroupUseStatementThrowsException()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     * @expectedExceptionMessageRegExp (^Unexpected token: ::, line: 8, col: 24, file: )
     */
    public function testUniformVariableSyntaxThrowsException()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * @return void
     */
    public function testEllipsisOperatorInFunctionCall()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    /**
     * Tests that the parser throws an exception when it detects an invalid
     * token in a method or property declaration.
     *
     * @return void
     */
    public function testParserThrowsUnexpectedTokenExceptionForInvalidTokenInPropertyDeclaration()
    {
        $this->setExpectedException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException',
            'Unexpected token: const, line: 4, col: 13, file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * @param \PDepend\Source\Tokenizer\Tokenizer $tokenizer
     * @param \PDepend\Source\Builder\Builder $builder
     * @param \PDepend\Util\Cache\CacheDriver $cache
     * @return \PDepend\Source\Language\PHP\AbstractPHPParser
     */
    protected function createPHPParser(Tokenizer $tokenizer, Builder $builder, CacheDriver $cache)
    {
        return $this->getMockForAbstractClass(
            'PDepend\\Source\\Language\\PHP\\PHPParserVersion56',
            array($tokenizer, $builder, $cache)
        );
    }

    /**
     * Tests that the parser throws an exception when it detects a reserved keyword
     * in constant class names.
     *
     * @return void
     */
    public function testReservedKeyword()
    {
        $this->setExpectedException(
            '\\PDepend\\Source\\Parser\\UnexpectedTokenException',
            'Unexpected token: NEW, line: 5, col: 11, file: '
        );

        $this->parseCodeResourceForTest();
    }
}
