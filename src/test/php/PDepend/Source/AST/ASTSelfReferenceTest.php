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

use PDepend\Source\Builder\BuilderContext\GlobalBuilderContext;
use PDepend\Source\Builder\BuilderContext;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTSelfReference} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTSelfReference
 * @group unittest
 */
class ASTSelfReferenceTest extends \PDepend\Source\AST\ASTNodeTest
{
    /**
     * testGetTypeReturnsInjectedConstructorTargetArgument
     *
     * @return void
     */
    public function testGetTypeReturnsInjectedConstructorTargetArgument()
    {
        $target  = $this->getMockForAbstractClass('\\PDepend\\Source\\AST\\AbstractASTClassOrInterface', array(__CLASS__));
        $context = $this->getMock('PDepend\\Source\\Builder\\BuilderContext');

        $reference = new \PDepend\Source\AST\ASTSelfReference($context, $target);
        $this->assertSame($target, $reference->getType());
    }

    /**
     * testGetTypeInvokesBuilderContextWhenTypeInstanceIsNull
     *
     * @return void
     */
    public function testGetTypeInvokesBuilderContextWhenTypeInstanceIsNull()
    {
        $target = $this->getMockForAbstractClass('\\PDepend\\Source\\AST\\AbstractASTClassOrInterface', array(__CLASS__));

        $builder = $this->getMock('\\PDepend\\Source\\Builder\\Builder');
        $builder->expects($this->once())
            ->method('getClassOrInterface');

        $context = new GlobalBuilderContext($builder);

        $reference = new \PDepend\Source\AST\ASTSelfReference($context, $target);
        $reference = unserialize(serialize($reference));
        $reference->getType();
    }

    /**
     * testSelfReferenceAllocationOutsideOfClassScopeThrowsExpectedException
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\InvalidStateException
     */
    public function testSelfReferenceAllocationOutsideOfClassScopeThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testSelfReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\InvalidStateException
     */
    public function testSelfReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
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
        $this->assertEquals('self', $reference->getImage());
    }

    /**
     * testSelfReference
     *
     * @return \PDepend\Source\AST\ASTSelfReference
     * @since 1.0.2
     */
    public function testSelfReference()
    {
        $reference = $this->_getFirstSelfReferenceInClass();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSelfReference', $reference);

        return $reference;
    }

    /**
     * testSelfReferenceHasExpectedStartLine
     *
     * @param \PDepend\Source\AST\ASTSelfReference $reference
     *
     * @return void
     * @depends testSelfReference
     */
    public function testSelfReferenceHasExpectedStartLine($reference)
    {
        $this->assertEquals(5, $reference->getStartLine());
    }

    /**
     * testSelfReferenceHasExpectedStartColumn
     *
     * @param \PDepend\Source\AST\ASTSelfReference $reference
     *
     * @return void
     * @depends testSelfReference
     */
    public function testSelfReferenceHasExpectedStartColumn($reference)
    {
        $this->assertEquals(13, $reference->getStartColumn());
    }

    /**
     * testSelfReferenceHasExpectedEndLine
     *
     * @param \PDepend\Source\AST\ASTSelfReference $reference
     *
     * @return void
     * @depends testSelfReference
     */
    public function testSelfReferenceHasExpectedEndLine($reference)
    {
        $this->assertEquals(5, $reference->getEndLine());
    }

    /**
     * testSelfReferenceHasExpectedEndColumn
     *
     * @param \PDepend\Source\AST\ASTSelfReference $reference
     *
     * @return void
     * @depends testSelfReference
     */
    public function testSelfReferenceHasExpectedEndColumn($reference)
    {
        $this->assertEquals(16, $reference->getEndColumn($reference));
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return \PDepend\Source\AST\ASTSelfReference
     */
    protected function createNodeInstance()
    {
        return new \PDepend\Source\AST\ASTSelfReference(
            $this->getMock('PDepend\\Source\\Builder\\BuilderContext'),
            $this->getMockForAbstractClass('\\PDepend\\Source\\AST\\AbstractASTClassOrInterface', array(__CLASS__))
        );
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTSelfReference
     */
    private function _getFirstSelfReferenceInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTSelfReference'
        );
    }
}
