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
 * Test case for the {@link \PDepend\Source\AST\ASTParentReference} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTParentReference
 * @group unittest
 */
class ASTParentReferenceTest extends ASTNodeTest
{
    /**
     * The mocked reference instance.
     *
     * @var \PDepend\Source\AST\ASTClassOrInterfaceReference
     */
    protected $referenceMock = null;

    /**
     * testGetTypeDelegatesCallToInjectedReferenceObject
     *
     * @return void
     */
    public function testGetTypeDelegatesCallToInjectedReferenceObject()
    {
        $reference = $this->createNodeInstance();
        $this->referenceMock->expects($this->once())
            ->method('getType');


        $reference->getType();
    }

    /**
     * testMagicSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     */
    public function testMagicSleepReturnsExpectedSetOfPropertyNames()
    {
        $reference = $this->createNodeInstance();
        $this->assertEquals(
            array(
                'reference',
                'context',
                'comment',
                'metadata',
                'nodes'
            ),
            $reference->__sleep()
        );
    }

    /**
     * testParentReferenceAllocationOutsideOfClassScopeThrowsExpectedException
     *
     * @return void
     */
    public function testParentReferenceAllocationOutsideOfClassScopeThrowsExpectedException()
    {
        $this->expectException(\PDepend\Source\Parser\InvalidStateException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParentReferenceInClassWithoutParentThrowsException
     *
     * @return void
     */
    public function testParentReferenceInClassWithoutParentThrowsException()
    {
        $this->expectException(\PDepend\Source\Parser\InvalidStateException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParentReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException
     *
     * @return void
     */
    public function testParentReferenceMemberPrimaryPrefixOutsideOfClassScopeThrowsExpectedException()
    {
        $this->expectException(\PDepend\Source\Parser\InvalidStateException::class);

        $this->parseCodeResourceForTest();
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
        $this->assertEquals('parent', $reference->getImage());
    }

    /**
     * testParentReferenceHasExpectedStartLine
     *
     * @return void
     */
    public function testParentReferenceHasExpectedStartLine()
    {
        $reference = $this->getFirstParentReferenceInClass(__METHOD__);
        $this->assertEquals(5, $reference->getStartLine());
    }

    /**
     * testParentReferenceHasExpectedStartColumn
     *
     * @return void
     */
    public function testParentReferenceHasExpectedStartColumn()
    {
        $reference = $this->getFirstParentReferenceInClass(__METHOD__);
        $this->assertEquals(20, $reference->getStartColumn());
    }

    /**
     * testParentReferenceHasExpectedEndLine
     *
     * @return void
     */
    public function testParentReferenceHasExpectedEndLine()
    {
        $reference = $this->getFirstParentReferenceInClass(__METHOD__);
        $this->assertEquals(5, $reference->getEndLine());
    }

    /**
     * testParentReferenceHasExpectedEndColumn
     *
     * @return void
     */
    public function testParentReferenceHasExpectedEndColumn()
    {
        $reference = $this->getFirstParentReferenceInClass(__METHOD__);
        $this->assertEquals(25, $reference->getEndColumn());
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return \PDepend\Source\AST\ASTParentReference
     */
    protected function createNodeInstance()
    {
        $this->referenceMock = $this->getMockBuilder('\PDepend\Source\AST\ASTClassOrInterfaceReference')
            ->disableOriginalConstructor()
            ->getMock();

        return new \PDepend\Source\AST\ASTParentReference($this->referenceMock);
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PDepend\Source\AST\ASTParentReference
     */
    private function getFirstParentReferenceInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase,
            'PDepend\\Source\\AST\\ASTParentReference'
        );
    }
}
