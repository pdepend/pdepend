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

use PDepend\Source\Builder\BuilderContext\GlobalBuilderContext;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTStaticReference} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTStaticReference
 * @group unittest
 */
class ASTStaticReferenceTest extends ASTNodeTest
{
    /**
     * testGetTypeReturnsInjectedConstructorTargetArgument
     *
     * @return void
     */
    public function testGetTypeReturnsInjectedConstructorTargetArgument()
    {
        $target  = $this->getMockForAbstractClass(
            '\\PDepend\\Source\\AST\\AbstractASTClassOrInterface',
            array(__CLASS__)
        );
        $context = $this->getMockBuilder('\\PDepend\\Source\\Builder\\BuilderContext')
            ->getMock();

        $reference = new \PDepend\Source\AST\ASTStaticReference($context, $target);
        $this->assertSame($target, $reference->getType());
    }

    /**
     * testGetTypeInvokesBuilderContextWhenTypeInstanceIsNull
     *
     * @return void
     */
    public function testGetTypeInvokesBuilderContextWhenTypeInstanceIsNull()
    {
        $target = $this->getMockForAbstractClass(
            '\\PDepend\\Source\\AST\\AbstractASTClassOrInterface',
            array(__CLASS__)
        );

        $builder = $this->getMockBuilder('\\PDepend\\Source\\Builder\\Builder')
            ->getMock();
        $builder->expects($this->once())
            ->method('getClassOrInterface');

        $context = new GlobalBuilderContext($builder);

        $reference = new \PDepend\Source\AST\ASTStaticReference($context, $target);
        $reference = unserialize(serialize($reference));
        $reference->getType();
    }

    /**
     * Tests that an invalid static results in the expected exception.
     *
     * @expectedException \PDepend\Source\Parser\InvalidStateException
     * @expectedExceptionMessage The keyword "static" was used outside of a class/method scope.
     * @return void
     */
    public function testStaticReferenceAllocationOutsideOfClassScopeThrowsExpectedException()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that an invalid static results in the expected exception.
     *
     * @expectedException \PDepend\Source\Parser\InvalidStateException
     * @expectedExceptionMessage The keyword "static" was used outside of a class/method scope.
     * @return void
     */
    public function testStaticReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException()
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * testMagicSelfReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSelfReturnsExpectedSetOfPropertyNames()
    {
        $reference = $this->createNodeInstance();
        $this->assertEquals(
            array(
                'qualifiedName',
                'context',
                'comment',
                'metadata',
                'nodes'
            ),
            $reference->__sleep()
        );
    }

    /**
     * testGetImageReturnsExpectedValue
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetImageReturnsExpectedValue()
    {
        $reference = $this->createNodeInstance();
        $this->assertEquals('static', $reference->getImage());
    }

    /**
     * testStaticReference
     *
     * @return \PDepend\Source\AST\ASTStaticReference
     * @since 1.0.2
     */
    public function testStaticReference()
    {
        $reference = $this->_getFirstStaticReferenceInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTStaticReference', $reference);

        return $reference;
    }

    /**
     * testStaticReferenceHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTStaticReference $reference
     *
     * @return void
     * @depends testStaticReference
     */
    public function testStaticReferenceHasExpectedStartLine($reference)
    {
        $this->assertEquals(5, $reference->getStartLine());
    }

    /**
     * testStaticReferenceHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTStaticReference $reference
     *
     * @return void
     * @depends testStaticReference
     */
    public function testStaticReferenceHasExpectedStartColumn($reference)
    {
        $this->assertEquals(13, $reference->getStartColumn());
    }

    /**
     * testStaticReferenceHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTStaticReference $reference
     *
     * @return void
     * @depends testStaticReference
     */
    public function testStaticReferenceHasExpectedEndLine($reference)
    {
        $this->assertEquals(5, $reference->getEndLine());
    }

    /**
     * testStaticReferenceHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTStaticReference $reference
     *
     * @return void
     * @depends testStaticReference
     */
    public function testStaticReferenceHasExpectedEndColumn($reference)
    {
        $this->assertEquals(18, $reference->getEndColumn());
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return \PDepend\Source\AST\ASTStaticReference
     */
    protected function createNodeInstance()
    {
        $context = $this->getMockBuilder('\\PDepend\\Source\\Builder\\BuilderContext')
            ->getMock();

        return new \PDepend\Source\AST\ASTStaticReference(
            $context,
            $this->getMockForAbstractClass(
                '\\PDepend\\Source\\AST\\AbstractASTClassOrInterface',
                array(__CLASS__)
            )
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @return \PDepend\Source\AST\ASTStaticReference
     */
    private function _getFirstStaticReferenceInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTStaticReference'
        );
    }
}
