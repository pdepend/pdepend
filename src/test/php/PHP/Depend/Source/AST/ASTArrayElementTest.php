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
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since     1.0.0
 */

namespace PHP\Depend\Source\AST;

/**
 * Test case for the {@link \PHP\Depend\Source\AST\ASTArrayElement} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since     1.0.0
 *
 * @covers \PHP\Depend\Source\Language\PHP\AbstractPHPParser
 * @covers \PHP\Depend\Source\AST\ASTNode
 * @covers \PHP\Depend\Source\AST\ASTArrayElement
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class ASTArrayElementTest extends \PHP\Depend\Source\AST\ASTNodeTest
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
     *
     * @return void
     */
    public function testArrayElementGraphSimpleValue()
    {
        $this->assertGraph(
            $this->_getFirstArrayElementInFunction(),
            array(
                \PHP\Depend\Source\AST\ASTVariable::CLAZZ . ' ($foo)'
            )
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
     *
     * @return void
     */
    public function testArrayElementGraphSimpleValueByReference()
    {
        $this->assertGraph(
            $this->_getFirstArrayElementInFunction(),
            array(
                \PHP\Depend\Source\AST\ASTVariable::CLAZZ . ' ($foo)'
            )
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
     *
     * @return void
     */
    public function testArrayElementGraphKeyValue()
    {
        $this->assertGraph(
            $this->_getFirstArrayElementInFunction(),
            array(
                \PHP\Depend\Source\AST\ASTVariable::CLAZZ . ' ($key)',
                \PHP\Depend\Source\AST\ASTVariable::CLAZZ . ' ($value)'
            )
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
     *
     * @return void
     */
    public function testArrayElementGraphKeyValueByReference()
    {
        $this->assertGraph(
            $this->_getFirstArrayElementInFunction(),
            array(
                \PHP\Depend\Source\AST\ASTVariable::CLAZZ . ' ($key)',
                \PHP\Depend\Source\AST\ASTVariable::CLAZZ . ' ($value)'
            )
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
     *
     * @return void
     */
    public function testArrayElementGraphWithTwoDimensions()
    {
        $this->assertGraph(
            $this->_getFirstArrayElementInFunction(),
            array(
                \PHP\Depend\Source\AST\ASTLiteral::CLAZZ                              . ' ("bar")',
                \PHP\Depend\Source\AST\ASTArray::CLAZZ                                . ' ()', array(
                    \PHP\Depend\Source\AST\ASTArrayElement::CLAZZ                     . ' ()', array(
                        \PHP\Depend\Source\AST\ASTAllocationExpression::CLAZZ         . ' (new)', array(
                            \PHP\Depend\Source\AST\ASTClassReference::CLAZZ           . ' (Object)')),
                    \PHP\Depend\Source\AST\ASTArrayElement::CLAZZ                     . ' ()', array(
                        \PHP\Depend\Source\AST\ASTLiteral::CLAZZ                      . ' (23)',
                        \PHP\Depend\Source\AST\ASTAllocationExpression::CLAZZ         . ' (new)', array(
                            \PHP\Depend\Source\AST\ASTClassReference::CLAZZ           . ' (Object)')),
                    \PHP\Depend\Source\AST\ASTArrayElement::CLAZZ                     . ' ()', array(
                        \PHP\Depend\Source\AST\ASTArray::CLAZZ                        . ' ()', array(
                            \PHP\Depend\Source\AST\ASTArrayElement::CLAZZ             . ' ()', array(
                                \PHP\Depend\Source\AST\ASTLiteral::CLAZZ              . ' ("foo")',
                                \PHP\Depend\Source\AST\ASTAllocationExpression::CLAZZ . ' (new)', array(
                                    \PHP\Depend\Source\AST\ASTClassReference::CLAZZ   . ' (Object)'))))
            )
            )
        );
    }

    /**
     * testArrayElementByReferenceReturnsFalseByDefault
     *
     * @return void
     */
    public function testArrayElementByReferenceReturnsFalseByDefault()
    {
        $array = $this->_getFirstArrayElementInFunction();
        $this->assertFalse($array->isByReference());
    }

    /**
     * testArrayElementByReferenceReturnsTrueForValue
     *
     * @return void
     */
    public function testArrayElementByReferenceReturnsTrueForValue()
    {
        $array = $this->_getFirstArrayElementInFunction();
        $this->assertTrue($array->isByReference());
    }

    /**
     * testArrayElementByReferenceReturnsFalseForKeyValue
     *
     * @return void
     */
    public function testArrayElementByReferenceReturnsFalseForKeyValue()
    {
        $array = $this->_getFirstArrayElementInFunction();
        $this->assertFalse($array->isByReference());
    }

    /**
     * testArrayElementByReferenceReturnsTrueForKeyValue
     *
     * @return void
     */
    public function testArrayElementByReferenceReturnsTrueForKeyValue()
    {
        $array = $this->_getFirstArrayElementInFunction();
        $this->assertTrue($array->isByReference());
    }

    /**
     * Tests the start line value of an array element.
     *
     * @return void
     */
    public function testArrayElementHasExpectedStartLine()
    {
        $array = $this->_getFirstArrayElementInFunction();
        $this->assertEquals(5, $array->getStartLine());
    }

    /**
     * Tests the start column value of an array element.
     *
     * @return void
     */
    public function testArrayElementHasExpectedStartColumn()
    {
        $array = $this->_getFirstArrayElementInFunction();
        $this->assertEquals(9, $array->getStartColumn());
    }

    /**
     * Tests the end line value of an array element.
     *
     * @return void
     */
    public function testArrayElementHasExpectedEndLine()
    {
        $array = $this->_getFirstArrayElementInFunction();
        $this->assertEquals(11, $array->getEndLine());
    }

    /**
     * Tests the end column value of an array element.
     *
     * @return void
     */
    public function testArrayElementHasExpectedEndColumn()
    {
        $array = $this->_getFirstArrayElementInFunction();
        $this->assertEquals(29, $array->getEndColumn());
    }

    /**
     * Returns an array element for the currently executed test case.
     *
     * @return \PHP\Depend\Source\AST\ASTArrayElement
     */
    private function _getFirstArrayElementInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            \PHP\Depend\Source\AST\ASTArrayElement::CLAZZ
        );
    }
}
