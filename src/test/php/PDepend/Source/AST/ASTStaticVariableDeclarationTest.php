<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTStaticVariableDeclaration} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTStaticVariableDeclaration
 * @group unittest
 */
class ASTStaticVariableDeclarationTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testStaticVariableDeclaration
     *
     * @return \PDepend\Source\AST\ASTStringIndexExpression
     * @since 1.0.2
     */
    public function testStaticVariableDeclaration()
    {
        $declaration = $this->_getFirstStaticVariableDeclarationInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTStaticVariableDeclaration', $declaration);

        return $declaration;
    }

    /**
     * Tests that the declaration has the expected start line value.
     *
     * @param \PDepend\Source\AST\ASTStringIndexExpression $declaration
     * 
     * @return void
     * @depends testStaticVariableDeclaration
     */
    public function testStaticVariableDeclarationHasExpectedStartLine($declaration)
    {
        $this->assertSame(4, $declaration->getStartLine());
    }

    /**
     * Tests that the declaration has the expected start column value.
     *
     * @param \PDepend\Source\AST\ASTStringIndexExpression $declaration
     *
     * @return void
     * @depends testStaticVariableDeclaration
     */
    public function testStaticVariableDeclarationHasExpectedStartColumn($declaration)
    {
        $this->assertSame(5, $declaration->getStartColumn());
    }

    /**
     * Tests that the declaration has the expected end line value.
     *
     * @param \PDepend\Source\AST\ASTStringIndexExpression $declaration
     *
     * @return void
     * @depends testStaticVariableDeclaration
     */
    public function testStaticVariableDeclarationHasExpectedEndLine($declaration)
    {
        $this->assertSame(5, $declaration->getEndLine());
    }

    /**
     * Tests that the declaration has the expected end column value.
     *
     * @param \PDepend\Source\AST\ASTStringIndexExpression $declaration
     *
     * @return void
     * @depends testStaticVariableDeclaration
     */
    public function testStaticVariableDeclarationHasExpectedEndColumn($declaration)
    {
        $this->assertSame(23, $declaration->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTStringIndexExpression
     */
    private function _getFirstStaticVariableDeclarationInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTStaticVariableDeclaration'
        );
    }

    /**
     * Creates a static variable declaration node.
     *
     * @return \PDepend\Source\AST\ASTStaticVariableDeclaration
     */
    protected function createNodeInstance()
    {
        return new \PDepend\Source\AST\ASTStaticVariableDeclaration(__FUNCTION__);
    }
}
