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
 * @since 1.0.0
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTArrayElement} class.
 *
 * @covers \PDepend\Source\AST\ASTArrayElement
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 *
 * @group unittest
 */
class ASTArrayElementTest extends ASTNodeTestCase
{
    /**
     * testArrayElementGraphSimpleValue
     *
     * Source:
     * <code>
     * array($foo)
     * </code>
     *
     * AST:
     * <code>
     * - ASTArray
     *   - ASTArrayElement
     *     - ASTVariable    ->  $foo
     * </code>
     */
    public function testArrayElementGraphSimpleValue(): void
    {
        $this->assertGraph(
            $this->getFirstArrayElementInFunction(),
            [
                ASTVariable::class . ' ($foo)',
            ]
        );
    }

    /**
     * testArrayElementGraphSimpleValueByReference
     *
     * Source:
     * <code>
     * array(&$foo)
     * </code>
     *
     * AST:
     * <code>
     * - ASTArray
     *   - ASTArrayElement
     *     - ASTVariable    ->  $foo
     * </code>
     */
    public function testArrayElementGraphSimpleValueByReference(): void
    {
        $this->assertGraph(
            $this->getFirstArrayElementInFunction(),
            [
                ASTVariable::class . ' ($foo)',
            ]
        );
    }

    /**
     * testArrayElementGraphKeyValue
     *
     * Source:
     * <code>
     * array($key => $value)
     * </code>
     *
     * AST:
     * <code>
     * - ASTArray
     *   - ASTArrayElement
     *     - ASTVariable    ->  $key
     *     - ASTVariable    ->  $value
     * </code>
     */
    public function testArrayElementGraphKeyValue(): void
    {
        $this->assertGraph(
            $this->getFirstArrayElementInFunction(),
            [
                ASTVariable::class . ' ($key)',
                ASTVariable::class . ' ($value)',
            ]
        );
    }

    /**
     * testArrayElementGraphKeyValueByReference
     *
     * Source:
     * <code>
     * array($key => &$value)
     * </code>
     *
     * AST:
     * <code>
     * - ASTArray
     *   - ASTArrayElement
     *     - ASTVariable    ->  $key
     *     - ASTVariable    ->  $value
     * </code>
     */
    public function testArrayElementGraphKeyValueByReference(): void
    {
        $this->assertGraph(
            $this->getFirstArrayElementInFunction(),
            [
                ASTVariable::class . ' ($key)',
                ASTVariable::class . ' ($value)',
            ]
        );
    }

    /**
     * testArrayElementGraphWithTwoDimensions
     *
     * Source:
     * <code>
     * array(
     *     "bar"  =>  array(
     *         new Object,
     *         23 => new Object,
     *         array("foo"  =>  new Object)
     *     )
     * )
     * </code>
     *
     * AST:
     * <code>
     * - ASTArray
     *   - ASTArrayElement
     *     - ASTLiteral                     -> "bar"
     *     - ASTArray
     *       - ASTArrayElement
     *         - ASTAllocationExpression    ->  new
     *           - ASTClassReference        ->  Object
     *       - ASTArrayElement
     *         - ASTLiteral                 ->  23
     *         - ASTAllocationExpression    ->  new
     *           - ASTClassReference        ->  Object
     *       - ASTArrayElement
     *         - ASTArray
     *           - ASTArrayElement
     *             - ASTLiteral               ->  "foo"
     *             - ASTAllocationExpression  ->  new
     *               - ASTClassReference      ->  Object
     * </code>
     */
    public function testArrayElementGraphWithTwoDimensions(): void
    {
        $this->assertGraph(
            $this->getFirstArrayElementInFunction(),
            [
                ASTLiteral::class . ' ("bar")',
                ASTArray::class . ' ()', [
                    ASTArrayElement::class . ' ()', [
                        ASTAllocationExpression::class . ' (new)', [
                            ASTClassReference::class . ' (Object)']],
                    ASTArrayElement::class . ' ()', [
                        ASTLiteral::class . ' (23)',
                        ASTAllocationExpression::class . ' (new)', [
                            ASTClassReference::class . ' (Object)']],
                    ASTArrayElement::class . ' ()', [
                        ASTArray::class . ' ()', [
                            ASTArrayElement::class . ' ()', [
                                ASTLiteral::class . ' ("foo")',
                                ASTAllocationExpression::class . ' (new)', [
                                    ASTClassReference::class . ' (Object)']]]],
                ],
            ]
        );
    }

    /**
     * testArrayElementByReferenceReturnsFalseByDefault
     */
    public function testArrayElementByReferenceReturnsFalseByDefault(): void
    {
        $array = $this->getFirstArrayElementInFunction();
        static::assertFalse($array->isByReference());
    }

    /**
     * testArrayElementByReferenceReturnsTrueForValue
     */
    public function testArrayElementByReferenceReturnsTrueForValue(): void
    {
        $array = $this->getFirstArrayElementInFunction();
        static::assertTrue($array->isByReference());
    }

    /**
     * testArrayElementByReferenceReturnsFalseForKeyValue
     */
    public function testArrayElementByReferenceReturnsFalseForKeyValue(): void
    {
        $array = $this->getFirstArrayElementInFunction();
        static::assertFalse($array->isByReference());
    }

    /**
     * testArrayElementByReferenceReturnsTrueForKeyValue
     */
    public function testArrayElementByReferenceReturnsTrueForKeyValue(): void
    {
        $array = $this->getFirstArrayElementInFunction();
        static::assertTrue($array->isByReference());
    }

    /**
     * Tests the start line value of an array element.
     */
    public function testArrayElementHasExpectedStartLine(): void
    {
        $array = $this->getFirstArrayElementInFunction();
        static::assertEquals(5, $array->getStartLine());
    }

    /**
     * Tests the start column value of an array element.
     */
    public function testArrayElementHasExpectedStartColumn(): void
    {
        $array = $this->getFirstArrayElementInFunction();
        static::assertEquals(9, $array->getStartColumn());
    }

    /**
     * Tests the end line value of an array element.
     */
    public function testArrayElementHasExpectedEndLine(): void
    {
        $array = $this->getFirstArrayElementInFunction();
        static::assertEquals(11, $array->getEndLine());
    }

    /**
     * Tests the end column value of an array element.
     */
    public function testArrayElementHasExpectedEndColumn(): void
    {
        $array = $this->getFirstArrayElementInFunction();
        static::assertEquals(29, $array->getEndColumn());
    }

    /**
     * Returns an array element for the currently executed test case.
     */
    private function getFirstArrayElementInFunction(): ASTArrayElement
    {
        return $this->getFirstNodeOfTypeInFunction(
            ASTArrayElement::class
        );
    }
}
