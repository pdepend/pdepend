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
 * @since     1.0.0
 */

namespace PDepend\Source\AST;

use PDepend\Source\Builder\BuilderContext;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTTraitReference} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since     1.0.0
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTTraitReference
 * @group unittest
 */
class ASTTraitReferenceTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testGetTraitDelegatesToContextGetTraitMethod
     *
     * @return void
     */
    public function testGetTraitDelegatesToContextGetTraitMethod()
    {
        $context = $this->getMock('PDepend\\Source\\Builder\\BuilderContext');
        $context->expects($this->once())
            ->method('getTrait')
            ->with($this->equalTo(__CLASS__));

        $reference = new \PDepend\Source\AST\ASTTraitReference($context, __CLASS__);
        $reference->getType();
    }
    
    /**
     * testTraitReference
     * 
     * @return \PDepend\Source\AST\ASTTraitReference
     * @since 1.0.2
     */
    public function testTraitReference()
    {
        $reference = $this->_getFirstTraitReferenceInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTTraitReference', $reference);
        
        return $reference;
    }

    /**
     * testTraitReferenceHasExpectedStartLine
     * 
     * @param \PDepend\Source\AST\ASTTraitReference $reference
     *
     * @return void
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedStartLine($reference)
    {
        $this->assertEquals(5, $reference->getStartLine());
    }

    /**
     * testTraitReferenceHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTTraitReference $reference
     *
     * @return void
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedStartColumn($reference)
    {
        $this->assertEquals(9, $reference->getStartColumn());
    }

    /**
     * testTraitReferenceHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTTraitReference $reference
     *
     * @return void
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedEndLine($reference)
    {
        $this->assertEquals(5, $reference->getEndLine());
    }

    /**
     * testTraitReferenceHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTTraitReference $reference
     *
     * @return void
     * @depends testTraitReference
     */
    public function testTraitReferenceHasExpectedEndColumn($reference)
    {
        $this->assertEquals(27, $reference->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTTraitReference
     */
    private function _getFirstTraitReferenceInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTTraitReference'
        );
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return \PDepend\Source\AST\ASTNode
     */
    protected function createNodeInstance()
    {
        return new \PDepend\Source\AST\ASTTraitReference(
            $this->getBuilderContextMock(),
            __CLASS__
        );
    }

    /**
     * Returns a mocked builder context instance.
     *
     * @return \PDepend\Source\Builder\BuilderContext
     */
    protected function getBuilderContextMock()
    {
        return $this->getMock('PDepend\\Source\\Builder\\BuilderContext');
    }
}
