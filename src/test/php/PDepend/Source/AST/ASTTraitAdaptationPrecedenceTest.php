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

/**
 * Test case for the {@link \PDepend\Source\AST\ASTTraitAdaptationPrecedence} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since     1.0.0
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTTraitAdaptationPrecedence
 * @group unittest
 */
class ASTTraitAdaptationPrecedenceTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testTraitAdaptationPrecedenceHasExpectedNumberOfTraitReferences
     *
     * @return void
     */
    public function testTraitAdaptationPrecedenceHasExpectedNumberOfTraitReferences()
    {
        $stmt = $this->_getFirstTraitAdaptationPrecedenceInClass();
        $this->assertEquals(
            3,
            count(
                $stmt->findChildrenOfType(
                    'PDepend\\Source\\AST\\ASTTraitReference'
                )
            )
        );
    }

    /**
     * testTraitAdaptationPrecedenceWithoutQualifiedReferenceThrowsExpectedException
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\InvalidStateException
     */
    public function testTraitAdaptationPrecedenceWithoutQualifiedReferenceThrowsExpectedException()
    {
        $this->_getFirstTraitAdaptationPrecedenceInClass();
    }

    /**
     * testTraitAdaptationPrecedence
     *
     * @return \PDepend\Source\AST\ASTTraitAdaptationPrecedence
     * @since 1.0.2
     */
    public function testTraitAdaptationPrecedence()
    {
        $precedence = $this->_getFirstTraitAdaptationPrecedenceInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTTraitAdaptationPrecedence', $precedence);

        return $precedence;
    }

    /**
     * testTraitAdaptationPrecedenceHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTTraitAdaptationPrecedence $precedence
     *
     * @return void
     * @depends testTraitAdaptationPrecedence
     */
    public function testTraitAdaptationPrecedenceHasExpectedStartLine($precedence)
    {
        $this->assertEquals(6, $precedence->getStartLine());
    }

    /**
     * testTraitAdaptationPrecedenceHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTTraitAdaptationPrecedence $precedence
     *
     * @return void
     * @depends testTraitAdaptationPrecedence
     */
    public function testTraitAdaptationPrecedenceHasExpectedStartColumn($precedence)
    {
        $this->assertEquals(9, $precedence->getStartColumn());
    }

    /**
     * testTraitAdaptationPrecedenceHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTTraitAdaptationPrecedence $precedence
     *
     * @return void
     * @depends testTraitAdaptationPrecedence
     */
    public function testTraitAdaptationPrecedenceHasExpectedEndLine($precedence)
    {
        $this->assertEquals(8, $precedence->getEndLine());
    }

    /**
     * testTraitAdaptationPrecedenceHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTTraitAdaptationPrecedence $precedence
     *
     * @return void
     * @depends testTraitAdaptationPrecedence
     */
    public function testTraitAdaptationPrecedenceHasExpectedEndColumn($precedence)
    {
        $this->assertEquals(56, $precedence->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTTraitAdaptationPrecedence
     */
    private function _getFirstTraitAdaptationPrecedenceInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTTraitAdaptationPrecedence'
        );
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
        $this->assertEquals(6, $reference->getStartLine());
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
        $this->assertEquals(6, $reference->getEndLine());
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
        $this->assertEquals(36, $reference->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTTraitReference
     */
    private function _getFirstTraitReferenceInClass()
    {
        return $this->_getFirstTraitAdaptationPrecedenceInClass()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTTraitReference');
    }
}
