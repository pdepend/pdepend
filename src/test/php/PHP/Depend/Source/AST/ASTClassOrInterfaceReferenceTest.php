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
 */

namespace PHP\Depend\Source\AST;

use PHP\Depend\Source\Builder\BuilderContext;

/**
 * description
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @covers \PHP\Depend\Source\Language\PHP\AbstractPHPParser
 * @covers \PHP\Depend\Source\AST\ASTClassOrInterfaceReference
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class ASTClassOrInterfaceReferenceTest
    extends \PHP\Depend\Source\AST\ASTNodeTest
{
    /**
     * testReturnValueOfMagicSleepContainsContextProperty
     *
     * @return void
     */
    public function testReturnValueOfMagicSleepContainsContextProperty()
    {
        $reference = new \PHP\Depend\Source\AST\ASTClassOrInterfaceReference(
            $this->getBuilderContextMock(), __CLASS__
        );
        $this->assertEquals(
            array(
                'context',
                'comment',
                'metadata',
                'nodes'
            ),
            $reference->__sleep()
        );
    }

    /**
     * testGetTypeDelegatesToBuilderContextGetClassOrInterface
     *
     * @return void
     */
    public function testGetTypeDelegatesToBuilderContextGetClassOrInterface()
    {
        $context = $this->getBuilderContextMock();
        $context->expects($this->once())
            ->method('getClassOrInterface')
            ->with($this->equalTo(__CLASS__))
            ->will($this->returnValue($this));

        $reference = new \PHP\Depend\Source\AST\ASTClassOrInterfaceReference(
            $context, __CLASS__
        );

        $reference->getType();
    }

    /**
     * testGetTypeCachesReturnValueOfBuilderContextGetClassOrInterface
     *
     * @return void
     */
    public function testGetTypeCachesReturnValueOfBuilderContextGetClassOrInterface()
    {
        $context = $this->getBuilderContextMock();
        $context->expects($this->exactly(1))
            ->method('getClassOrInterface')
            ->with($this->equalTo(__CLASS__))
            ->will($this->returnValue($this));

        $reference = new \PHP\Depend\Source\AST\ASTClassOrInterfaceReference(
            $context, __CLASS__
        );

        $reference->getType();
    }

    /**
     * testReferenceHasExpectedStartLine
     *
     * @return void
     */
    public function testReferenceHasExpectedStartLine()
    {
        $reference = $this->_getFirstReferenceInFunction(__METHOD__);
        $this->assertEquals(2, $reference->getStartLine());
    }

    /**
     * testReferenceHasExpectedStartColumn
     *
     * @return void
     */
    public function testReferenceHasExpectedStartColumn()
    {
        $reference = $this->_getFirstReferenceInFunction(__METHOD__);
        $this->assertEquals(14, $reference->getStartColumn());
    }

    /**
     * testReferenceHasExpectedEndLine
     *
     * @return void
     */
    public function testReferenceHasExpectedEndLine()
    {
        $reference = $this->_getFirstReferenceInFunction(__METHOD__);
        $this->assertEquals(2, $reference->getEndLine());
    }

    /**
     * testReferenceHasExpectedEndColumn
     *
     * @return void
     */
    public function testReferenceHasExpectedEndColumn()
    {
        $reference = $this->_getFirstReferenceInFunction(__METHOD__);
        $this->assertEquals(29, $reference->getEndColumn());
    }

    /**
     * testReferenceInInterfaceExtendsHasExpectedStartLine
     *
     * @return void
     * @since 0.10.5
     */
    public function testReferenceInInterfaceExtendsHasExpectedStartLine()
    {
        $reference = $this->_getFirstReferenceInInterface();
        $this->assertEquals(3, $reference->getStartLine());
    }

    /**
     * testReferenceInInterfaceExtendsHasExpectedStartColumn
     *
     * @return void
     * @since 0.10.5
     */
    public function testReferenceInInterfaceExtendsHasExpectedStartColumn()
    {
        $reference = $this->_getFirstReferenceInInterface();
        $this->assertEquals(13, $reference->getStartColumn());
    }

    /**
     * testReferenceInInterfaceExtendsHasExpectedEndLine
     *
     * @return void
     * @since 0.10.5
     */
    public function testReferenceInInterfaceExtendsHasExpectedEndLine()
    {
        $reference = $this->_getFirstReferenceInInterface();
        $this->assertEquals(3, $reference->getEndLine());
    }

    /**
     * testReferenceInInterfaceExtendsHasExpectedEndColumn
     *
     * @return void
     * @since 0.10.5
     */
    public function testReferenceInInterfaceExtendsHasExpectedEndColumn()
    {
        $reference = $this->_getFirstReferenceInInterface();
        $this->assertEquals(15, $reference->getEndColumn());
    }

    /**
     * testReferenceInClassImplementsHasExpectedStartLine
     *
     * @return void
     * @since 0.10.5
     */
    public function testReferenceInClassImplementsHasExpectedStartLine()
    {
        $reference = $this->_getFirstReferenceInClass();
        $this->assertEquals(2, $reference->getStartLine());
    }

    /**
     * testReferenceInClassImplementsHasExpectedStartColumn
     *
     * @return void
     * @since 0.10.5
     */
    public function testReferenceInClassImplementsHasExpectedStartColumn()
    {
        $reference = $this->_getFirstReferenceInClass();
        $this->assertEquals(68, $reference->getStartColumn());
    }

    /**
     * testReferenceInClassImplementsHasExpectedEndLine
     *
     * @return void
     * @since 0.10.5
     */
    public function testReferenceInClassImplementsHasExpectedEndLine()
    {
        $reference = $this->_getFirstReferenceInClass();
        $this->assertEquals(2, $reference->getEndLine());
    }

    /**
     * testReferenceInClassImplementsHasExpectedEndColumn
     *
     * @return void
     * @since 0.10.5
     */
    public function testReferenceInClassImplementsHasExpectedEndColumn()
    {
        $reference = $this->_getFirstReferenceInClass();
        $this->assertEquals(68, $reference->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PHP\Depend\Source\AST\ASTClassOrInterfaceReference
     */
    private function _getFirstReferenceInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, \PHP\Depend\Source\AST\ASTClassOrInterfaceReference::CLAZZ
        );
    }

    /**
     * Returns the first reference node for the currently executed test case.
     *
     * @return \PHP\Depend\Source\AST\ASTClassOrInterfaceReference
     * @since 0.10.5
     */
    private function _getFirstReferenceInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            self::getCallingTestMethod(),
            \PHP\Depend\Source\AST\ASTClassOrInterfaceReference::CLAZZ
        );
    }

    /**
     * Returns the first reference node for the currently executed test case.
     *
     * @return \PHP\Depend\Source\AST\ASTClassOrInterfaceReference
     * @since 0.10.5
     */
    private function _getFirstReferenceInInterface()
    {
        return $this->getFirstNodeOfTypeInInterface(
            self::getCallingTestMethod(),
            \PHP\Depend\Source\AST\ASTClassOrInterfaceReference::CLAZZ
        );
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return \PHP\Depend\Source\AST\ASTNode
     */
    protected function createNodeInstance()
    {
        return new \PHP\Depend\Source\AST\ASTClassOrInterfaceReference(
            $this->getBuilderContextMock(),
            __CLASS__
        );
    }

    /**
     * Returns a mocked builder context instance.
     *
     * @return \PHP\Depend\Source\Builder\BuilderContext
     */
    protected function getBuilderContextMock()
    {
        return $this->getMock(BuilderContext::CLAZZ);
    }
}
