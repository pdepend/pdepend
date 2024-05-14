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

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTArrayElement;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTFunctionPostfix;
use PDepend\Source\AST\ASTHeredoc;
use PDepend\Source\AST\ASTInstanceOfExpression;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Util\Cache\CacheDriver;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\AbstractPHPParser} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class PHPParserVersion73Test extends AbstractTestCase
{
    public function testHereDocAndNowDoc(): void
    {
        /** @var ASTHeredoc $heredoc */
        $heredoc = $this->getFirstNodeOfTypeInFunction('', ASTArray::class);
        $arrayElements = $heredoc->getChildren();
        $children = $arrayElements[0]->getChildren();
        $children = $children[0]->getChildren();

        /** @var ASTLiteral $literal */
        $literal = $children[0];

        static::assertSame('foobar!', $literal->getImage());

        $children = $arrayElements[1]->getChildren();
        $children = $children[0]->getChildren();

        /** @var ASTLiteral $literal */
        $literal = $children[0];

        static::assertSame('second,', $literal->getImage());
    }

    public function testDestructuringArrayReference(): void
    {
        $functionChildren = $this->getFirstFunctionForTestCase()->getChildren();
        $statements = $functionChildren[1]->getChildren();
        $assignments = $statements[1]->getChildren();
        $listElements = $assignments[0]->getChildren();
        $children = $listElements[0]->getChildren();

        /** @var ASTArrayElement $aElement */
        $aElement = $children[0];
        $arrayElement = $children[1];
        $children = $arrayElement->getChildren();
        $subElements = $children[0]->getChildren();

        /** @var ASTArrayElement $bElement */
        $bElement = $subElements[0];

        /** @var ASTArrayElement $cElement */
        $cElement = $subElements[1];

        $aElements = $aElement->getChildren();

        /** @var ASTVariable $aVariable */
        $aVariable = $aElements[0];

        $bElements = $bElement->getChildren();

        /** @var ASTVariable $bVariable */
        $bVariable = $bElements[0];

        $cElements = $cElement->getChildren();

        /** @var ASTVariable $cVariable */
        $cVariable = $cElements[0];

        static::assertTrue($aElement->isByReference());
        static::assertSame('$a', $aVariable->getImage());

        static::assertFalse($bElement->isByReference());
        static::assertSame('$b', $bVariable->getImage());

        static::assertTrue($cElement->isByReference());
        static::assertSame('$c', $cVariable->getImage());
    }

    public function testInstanceOfLiterals(): void
    {
        $functionChildren = $this->getFirstFunctionForTestCase()->getChildren();
        $statements = $functionChildren[1]->getChildren();
        $expressions = $statements[0]->getChildren();
        $expression = $expressions[0]->getChildren();

        /** @var ASTLiteral $instanceOf */
        $literal = $expression[0];

        /** @var ASTInstanceOfExpression $instanceOf */
        $instanceOf = $expression[1];

        /** @var ASTClassOrInterfaceReference[] $variables */
        $variables = $instanceOf->getChildren();

        static::assertCount(2, $expression);
        static::assertInstanceOf(ASTLiteral::class, $literal);
        static::assertSame('false', $literal->getImage());
        static::assertInstanceOf(ASTClassOrInterfaceReference::class, $variables[0]);
        static::assertSame('DateTimeInterface', $variables[0]->getImage());
    }

    public function testTrailingCommasInCall(): void
    {
        $functionChildren = $this->getFirstFunctionForTestCase()->getChildren();
        $statements = $functionChildren[1]->getChildren();

        /** @var ASTFunctionPostfix[] $calls */
        $calls = $statements[0]->getChildren();

        static::assertCount(1, $calls);
        static::assertInstanceOf(ASTFunctionPostfix::class, $calls[0]);

        $children = $calls[0]->getChildren();

        /** @var ASTArguments $arguments */
        $arguments = $children[1];

        static::assertInstanceOf(ASTArguments::class, $arguments);

        $arguments = $arguments->getChildren();

        static::assertCount(1, $arguments);
        static::assertInstanceOf(ASTVariable::class, $arguments[0]);
        static::assertSame('$i', $arguments[0]->getImage());
    }

    public function testTrailingCommasInUnsetCall(): void
    {
        $functionChildren = $this->getFirstFunctionForTestCase()->getChildren();
        $statements = $functionChildren[1]->getChildren();

        /** @var ASTFunctionPostfix[] $calls */
        $calls = $statements[0]->getChildren();

        static::assertCount(1, $calls);
        static::assertSame('$i', $calls[0]->getImage());
    }

    /**
     * @return AbstractPHPParser
     */
    protected function createPHPParser(Tokenizer $tokenizer, Builder $builder, CacheDriver $cache)
    {
        return $this->getAbstractClassMock(
            'PDepend\\Source\\Language\\PHP\\AbstractPHPParser',
            [$tokenizer, $builder, $cache]
        );
    }
}
