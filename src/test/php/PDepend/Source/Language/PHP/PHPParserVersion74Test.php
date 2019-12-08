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

use OutOfBoundsException;
use PDepend\AbstractTest;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTVariableDeclarator;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserVersion74} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion74
 * @group unittest
 */
class PHPParserVersion74Test extends AbstractTest
{
    /**
     * @return void
     */
    public function testTypedProperties()
    {
        /** @var ASTClass $class */
        $class = $this->getFirstClassForTestCase();
        $children = $class->getChildren();
        /** @var ASTFieldDeclaration $intDeclaration */
        $intDeclaration = $children[0];
        $intChildren = $intDeclaration->getChildren();
        /** @var ASTVariableDeclarator $intVariable */
        $intVariable = $intChildren[1];
        /** @var ASTFieldDeclaration $stringDeclaration */
        $stringDeclaration = $children[1];
        $stringChildren = $stringDeclaration->getChildren();
        /** @var ASTVariableDeclarator $intVariable */
        $stringVariable = $stringChildren[1];
        /** @var ASTFieldDeclaration $mixedDeclaration */
        $mixedDeclaration = $children[2];

        $this->assertTrue($intDeclaration->hasType());

        $intType = $intDeclaration->getType();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $intType);
        $this->assertSame('int', $intType->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFieldDeclaration', $intDeclaration);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariableDeclarator', $intVariable);
        $this->assertSame('$id', $intVariable->getImage());

        $this->assertTrue($stringDeclaration->hasType());

        $stringType = $stringDeclaration->getType();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTScalarType', $stringType);
        $this->assertSame('string', $stringType->getImage());;
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFieldDeclaration', $stringDeclaration);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariableDeclarator', $stringVariable);
        $this->assertSame('$name', $stringVariable->getImage());

        $this->assertFalse($mixedDeclaration->hasType());

        $message = null;

        try {
            $mixedDeclaration->getType();
        } catch (OutOfBoundsException $exception) {
            $message = $exception->getMessage();
        }

        $this->assertSame('The parameter does not has a type specification.', $message);
    }

    public function testTypedPropertiesSyntaxError()
    {
        $this->setExpectedException(
            'PDepend\\Source\\Parser\\UnexpectedTokenException',
            'Unexpected token: string, line: 4, col: 16, file:'
        );

        $this->parseCodeResourceForTest();
    }
}
