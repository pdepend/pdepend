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
 * @covers \PDepend\Source\AST\ASTClosure
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class ASTClosureTest extends ASTNodeTestCase
{
    /**
     * testReturnsByReferenceReturnsFalseByDefault
     */
    public function testReturnsByReferenceReturnsFalseByDefault(): void
    {
        $closure = $this->getFirstClosureInFunction();
        static::assertFalse($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsFalseByDefaultForStaticClosure
     */
    public function testReturnsByReferenceReturnsFalseByDefaultForStaticClosure(): void
    {
        $closure = $this->getFirstClosureInFunction();
        static::assertFalse($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsTrueForClosure
     */
    public function testReturnsByReferenceReturnsTrueForClosure(): void
    {
        $closure = $this->getFirstClosureInFunction();
        static::assertTrue($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsTrueForStaticClosure
     */
    public function testReturnsByReferenceReturnsTrueForStaticClosure(): void
    {
        $closure = $this->getFirstClosureInFunction();
        static::assertTrue($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsTrueForAssignedClosure
     */
    public function testReturnsByReferenceReturnsTrueForAssignedClosure(): void
    {
        $closure = $this->getFirstClosureInFunction();
        static::assertTrue($closure->returnsByReference());
    }

    /**
     * testParserHandlesPureClosureStatementWithoutAssignment
     *
     * @since 1.0.0
     */
    public function testParserHandlesPureClosureStatementWithoutAssignment(): void
    {
        $closure = $this->getFirstClosureInFunction();
        static::assertInstanceOf(ASTClosure::class, $closure);
    }

    /**
     * testIsStaticReturnsFalseByDefault
     */
    public function testIsStaticReturnsFalseByDefault(): void
    {
        $closure = new ASTClosure();
        static::assertFalse($closure->isStatic());
    }

    /**
     * testIsStaticReturnsTrueWhenSetToTrue
     */
    public function testIsStaticReturnsTrueWhenSetToTrue(): void
    {
        $closure = new ASTClosure();
        $closure->setStatic(true);

        static::assertTrue($closure->isStatic());
    }

    /**
     * testIsStaticReturnsFalseWhenSetToFalse
     */
    public function testIsStaticReturnsFalseWhenSetToFalse(): void
    {
        $closure = new ASTClosure();
        $closure->setStatic(false);

        static::assertFalse($closure->isStatic());
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
     */
    public function testIsStaticReturnsFalseForNonStaticClosure(): void
    {
        $closure = $this->getFirstClosureInFunction();
        static::assertFalse($closure->isStatic());
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
     */
    public function testIsStaticReturnsTrueForStaticClosure(): void
    {
        $closure = $this->getFirstClosureInFunction();
        static::assertTrue($closure->isStatic());
    }

    /**
     * testClosureContainsExpectedNumberChildNodes
     */
    public function testClosureContainsExpectedNumberChildNodes(): void
    {
        $closure = $this->getFirstClosureInFunction();
        static::assertCount(2, $closure->getChildren());
    }

    /**
     * Tests the start line value.
     */
    public function testClosureHasExpectedStartLine(): void
    {
        $label = $this->getFirstClosureInFunction();
        static::assertEquals(4, $label->getStartLine());
    }

    /**
     * Tests the start column value.
     */
    public function testClosureHasExpectedStartColumn(): void
    {
        $label = $this->getFirstClosureInFunction();
        static::assertEquals(12, $label->getStartColumn());
    }

    /**
     * Tests the end line value.
     */
    public function testClosureHasExpectedEndLine(): void
    {
        $label = $this->getFirstClosureInFunction();
        static::assertEquals(6, $label->getEndLine());
    }

    /**
     * Tests the end column value.
     */
    public function testClosureHasExpectedEndColumn(): void
    {
        $label = $this->getFirstClosureInFunction();
        static::assertEquals(5, $label->getEndColumn());
    }

    /**
     * testStaticClosureHasExpectedStartLine
     */
    public function testStaticClosureHasExpectedStartLine(): void
    {
        $label = $this->getFirstClosureInFunction();
        static::assertEquals(4, $label->getStartLine());
    }

    /**
     * testStaticClosureHasExpectedEndLine
     */
    public function testStaticClosureHasExpectedEndLine(): void
    {
        $label = $this->getFirstClosureInFunction();
        static::assertEquals(7, $label->getEndLine());
    }

    /**
     * testStaticClosureHasExpectedStartColumn
     */
    public function testStaticClosureHasExpectedStartColumn(): void
    {
        $label = $this->getFirstClosureInFunction();
        static::assertEquals(12, $label->getStartColumn());
    }

    /**
     * testStaticClosureHasExpectedEndColumn
     */
    public function testStaticClosureHasExpectedEndColumn(): void
    {
        $label = $this->getFirstClosureInFunction();
        static::assertEquals(9, $label->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     */
    private function getFirstClosureInFunction(): ASTClosure
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTClosure::class
        );
    }
}
