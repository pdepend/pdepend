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
 * @since 0.9.20
 */

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\AST\ASTConstantDefinition;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTReturnStatement;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserGeneric} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\Language\PHP\PHPParserGeneric
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 *
 * @link https://github.com/pdepend/pdepend/issues/180
 * @since 2.1.0
 */
class PHPParserGenericVersion56Test extends AbstractTestCase
{
    /**
     * testShiftLeftInConstantInitializer
     */
    public function testShiftLeftInConstantInitializer(): void
    {
        $class = $this->getFirstClassForTestCase();
        $const = $class->getChild(0);

        $this->assertInstanceOf(ASTConstantDefinition::class, $const);
    }

    /**
     * testShiftRightInConstantInitializer
     */
    public function testShiftRightInConstantInitializer(): void
    {
        $class = $this->getFirstClassForTestCase();
        $const = $class->getChild(0);

        $this->assertInstanceOf(ASTConstantDefinition::class, $const);
    }

    /**
     * testShiftLeftInConstantInitializer
     */
    public function testMultipleShiftLeftInConstantInitializer(): void
    {
        $class = $this->getFirstClassForTestCase();
        $const = $class->getChild(0);

        $this->assertInstanceOf(ASTConstantDefinition::class, $const);
    }

    /**
     * testShiftRightInConstantInitializer
     */
    public function testMultipleShiftRightInConstantInitializer(): void
    {
        $class = $this->getFirstClassForTestCase();
        $const = $class->getChild(0);

        $this->assertInstanceOf(ASTConstantDefinition::class, $const);
    }

    /**
     * testConstantSupportForScalarArrayValues
     *
     * @link https://github.com/pdepend/pdepend/issues/209
     */
    public function testConstantSupportForArrayWithValues(): void
    {
        $class = $this->getFirstClassForTestCase();
        $const = $class->getChild(0);

        $this->assertInstanceOf(ASTConstantDefinition::class, $const);
    }

    /**
     * testConstantSupportForArrayWithKeyValuePairs
     *
     * @link https://github.com/pdepend/pdepend/issues/209
     */
    public function testConstantSupportForArrayWithKeyValuePairs(): void
    {
        $class = $this->getFirstClassForTestCase();
        $const = $class->getChild(0);

        $this->assertInstanceOf(ASTConstantDefinition::class, $const);
    }

    /**
     * testConstantSupportForArrayWithSelfReferenceInClass
     *
     * @link https://github.com/pdepend/pdepend/issues/192
     */
    public function testConstantSupportForArrayWithSelfReferenceInClass(): void
    {
        $class = $this->getFirstClassForTestCase();
        $const = $class->getChild(0);

        $this->assertInstanceOf(ASTConstantDefinition::class, $const);
    }

    /**
     * testConstantSupportForArrayWithSelfReferenceInInterface
     *
     * @link https://github.com/pdepend/pdepend/issues/192
     */
    public function testConstantSupportForArrayWithSelfReferenceInInterface(): void
    {
        $class = $this->getFirstInterfaceForTestCase();
        $const = $class->getChild(0);

        $this->assertInstanceOf(ASTConstantDefinition::class, $const);
    }

    /**
     * testComplexExpressionInParameterInitializer
     */
    public function testComplexExpressionInParameterInitializer(): void
    {
        $node = $this->getFirstFunctionForTestCase()
            ->getFirstChildOfType(ASTFormalParameter::class);

        $this->assertNotNull($node);
    }

    /**
     * testComplexExpressionInConstantDeclarator
     */
    public function testComplexExpressionInConstantDeclarator(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTConstantDeclarator::class);

        $this->assertNotNull($node);
    }

    /**
     * testComplexExpressionInFieldDeclaration
     */
    public function testComplexExpressionInFieldDeclaration(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTFieldDeclaration::class);

        $this->assertNotNull($node);
    }

    /**
     * testPowExpressionInMethodBody
     */
    public function testPowExpressionInMethodBody(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTReturnStatement::class);

        $this->assertSame('**', $node->getChild(0)->getChild(1)->getImage());
    }

    /**
     * testPowExpressionInFieldDeclaration
     */
    public function testPowExpressionInFieldDeclaration(): void
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType(ASTFieldDeclaration::class);

        $this->assertNotNull($node);
    }

    public function testEllipsisOperatorInFunctionCall(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }
}
