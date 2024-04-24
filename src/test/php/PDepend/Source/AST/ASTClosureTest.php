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

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTClosure} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTNode
 * @covers \PDepend\Source\AST\ASTClosure
 * @group unittest
 */
class ASTClosureTest extends ASTNodeTest
{
    /**
     * testReturnsByReferenceReturnsFalseByDefault
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsFalseByDefault()
    {
        $closure = $this->getFirstClosureInFunction();
        $this->assertFalse($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsFalseByDefaultForStaticClosure
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsFalseByDefaultForStaticClosure()
    {
        $closure = $this->getFirstClosureInFunction();
        $this->assertFalse($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsTrueForClosure
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsTrueForClosure()
    {
        $closure = $this->getFirstClosureInFunction();
        $this->assertTrue($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsTrueForStaticClosure
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsTrueForStaticClosure()
    {
        $closure = $this->getFirstClosureInFunction();
        $this->assertTrue($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsTrueForAssignedClosure
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsTrueForAssignedClosure()
    {
        $closure = $this->getFirstClosureInFunction();
        $this->assertTrue($closure->returnsByReference());
    }

    /**
     * testParserHandlesPureClosureStatementWithoutAssignment
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserHandlesPureClosureStatementWithoutAssignment()
    {
        $closure = $this->getFirstClosureInFunction();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClosure', $closure);
    }

    /**
     * testIsStaticReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsStaticReturnsFalseByDefault()
    {
        $closure = new \PDepend\Source\AST\ASTClosure();
        $this->assertFalse($closure->isStatic());
    }

    /**
     * testIsStaticReturnsTrueWhenSetToTrue
     *
     * @return void
     */
    public function testIsStaticReturnsTrueWhenSetToTrue()
    {
        $closure = new \PDepend\Source\AST\ASTClosure();
        $closure->setStatic(true);
        
        $this->assertTrue($closure->isStatic());
    }

    /**
     * testIsStaticReturnsFalseWhenSetToFalse
     *
     * @return void
     */
    public function testIsStaticReturnsFalseWhenSetToFalse()
    {
        $closure = new \PDepend\Source\AST\ASTClosure();
        $closure->setStatic(false);

        $this->assertFalse($closure->isStatic());
    }

    /**
     * testIsStaticReturnsFalseForNonStaticClosure
     *
     * Source:
     * <code>
     * return function($x, $y) {
     *     return pow($x, $y);
     * }
     * </code>
     *
     * @return void
     */
    public function testIsStaticReturnsFalseForNonStaticClosure()
    {
        $closure = $this->getFirstClosureInFunction();
        $this->assertFalse($closure->isStatic());
    }

    /**
     * testIsStaticReturnsTrueForStaticClosure
     *
     * Source:
     * <code>
     * return static function($x, $y) {
     *     return pow($x, $y);
     * }
     * </code>
     *
     * @return void
     */
    public function testIsStaticReturnsTrueForStaticClosure()
    {
        $closure = $this->getFirstClosureInFunction();
        $this->assertTrue($closure->isStatic());
    }

    /**
     * testClosureContainsExpectedNumberChildNodes
     *
     * @return void
     */
    public function testClosureContainsExpectedNumberChildNodes()
    {
        $closure = $this->getFirstClosureInFunction();
        $this->assertCount(2, $closure->getChildren());
    }

    /**
     * Tests the start line value.
     *
     * @return void
     */
    public function testClosureHasExpectedStartLine()
    {
        $label = $this->getFirstClosureInFunction();
        $this->assertEquals(4, $label->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     */
    public function testClosureHasExpectedStartColumn()
    {
        $label = $this->getFirstClosureInFunction();
        $this->assertEquals(12, $label->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     */
    public function testClosureHasExpectedEndLine()
    {
        $label = $this->getFirstClosureInFunction();
        $this->assertEquals(6, $label->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     */
    public function testClosureHasExpectedEndColumn()
    {
        $label = $this->getFirstClosureInFunction();
        $this->assertEquals(5, $label->getEndColumn());
    }

    /**
     * testStaticClosureHasExpectedStartLine
     *
     * @return void
     */
    public function testStaticClosureHasExpectedStartLine()
    {
        $label = $this->getFirstClosureInFunction();
        $this->assertEquals(4, $label->getStartLine());
    }

    /**
     * testStaticClosureHasExpectedEndLine
     *
     * @return void
     */
    public function testStaticClosureHasExpectedEndLine()
    {
        $label = $this->getFirstClosureInFunction();
        $this->assertEquals(7, $label->getEndLine());
    }

    /**
     * testStaticClosureHasExpectedStartColumn
     *
     * @return void
     */
    public function testStaticClosureHasExpectedStartColumn()
    {
        $label = $this->getFirstClosureInFunction();
        $this->assertEquals(12, $label->getStartColumn());
    }

    /**
     * testStaticClosureHasExpectedEndColumn
     *
     * @return void
     */
    public function testStaticClosureHasExpectedEndColumn()
    {
        $label = $this->getFirstClosureInFunction();
        $this->assertEquals(9, $label->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTClosure
     */
    private function getFirstClosureInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTClosure'
        );
    }
}
