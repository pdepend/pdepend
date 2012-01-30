<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 * @since     0.10.0
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTNode} class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 * @since     0.10.0
 *
 * @covers PHP_Depend_Code_ASTNode
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_CommonASTNodeTest extends PHP_Depend_AbstractTest
{
    /**
     * testGetImageReturnsEmptyStringByDefault
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetImageReturnsEmptyStringByDefault()
    {
        $node = $this->getNodeMock();
        $this->assertSame('', $node->getImage());
    }

    /**
     * testGetImageReturnsExpectedNodeImage
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetImageReturnsExpectedNodeImage()
    {
        $node = $this->getNodeMock();
        $node->setImage(__FUNCTION__);

        $this->assertEquals(__FUNCTION__, $node->getImage());
    }

    /**
     * testGetCommentReturnsNullByDefault
     *
     * @return void
     */
    public function testGetCommentReturnsNullByDefault()
    {
        $node = $this->getNodeMock();
        self::assertNull($node->getComment());
    }

    /**
     * testGetCommentReturnsInjectedCommentValue
     *
     * @return void
     */
    public function testGetCommentReturnsInjectedCommentValue()
    {
        $node = $this->getNodeMock();
        $node->setComment('/** Manuel */');
        
        self::assertEquals('/** Manuel */', $node->getComment());
    }

    /**
     * testPrependChildSimplyAddsFirstChild
     *
     * @return void
     * @since 1.0.0
     */
    public function testPrependChildSimplyAddsFirstChild()
    {
        $node = $this->getNodeMock();
        $node->prependChild($child = $this->getNodeMock());

        $this->assertSame($child, $node->getChild(0));
    }

    /**
     * testPrependChildMovesFirstChild
     *
     * @return void
     * @since 1.0.0
     */
    public function testPrependChildMovesFirstChild()
    {
        $node = $this->getNodeMock();
        $node->prependChild($child0 = $this->getNodeMock());
        $node->prependChild($child1 = $this->getNodeMock());

        $this->assertSame($child0, $node->getChild(1));
    }

    /**
     * testPrependChildPrependsNewChild
     *
     * @return void
     * @since 1.0.0
     */
    public function testPrependChildPrependsNewChild()
    {
        $node = $this->getNodeMock();
        $node->prependChild($child0 = $this->getNodeMock());
        $node->prependChild($child1 = $this->getNodeMock());

        $this->assertSame($child1, $node->getChild(0));
    }

    /**
     * testGetParentReturnsNullByDefault
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetParentReturnsNullByDefault()
    {
        $node = $this->getNodeMock();
        $this->assertNull($node->getParent());
    }

    /**
     * testGetParentReturnsExpectedNode
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetParentReturnsExpectedNode()
    {
        $node = $this->getNodeMock();
        $node->setParent($parent = $this->getNodeMock());

        $this->assertSame($parent, $node->getParent());
    }

    /**
     * testGetParentsOfTypeReturnsEmptyArrayByDefault
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetParentsOfTypeReturnsEmptyArrayByDefault()
    {
        $node = $this->getNodeMock();
        $this->assertSame(
            array(),
            $node->getParentsOfType(PHP_Depend_Code_ASTScope::CLAZZ)
        );
    }

    /**
     * testGetParentsOfTypeReturnsExpectedParentNodes
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetParentsOfTypeReturnsExpectedParentNodes()
    {
        $parent0 = $this->getMockForAbstractClass(PHP_Depend_Code_ASTScope::CLAZZ);
        $parent1 = $this->getMockForAbstractClass(PHP_Depend_Code_ASTNode::CLAZZ);
        $parent2 = $this->getMockForAbstractClass(PHP_Depend_Code_ASTScope::CLAZZ);
        $parent3 = $this->getMockForAbstractClass(PHP_Depend_Code_ASTNode::CLAZZ);

        $node = $this->getNodeMock();

        $parent3->addChild($node);
        $parent2->addChild($parent3);
        $parent1->addChild($parent2);
        $parent0->addChild($parent1);

        $this->assertSame(
            array($parent0, $parent2),
            $node->getParentsOfType(PHP_Depend_Code_ASTScope::CLAZZ)
        );
    }

    /**
     * testGetChildrenReturnsEmptyArrayByDefault
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetChildrenReturnsEmptyArrayByDefault()
    {
        $node = $this->getNodeMock();
        $this->assertSame(array(), $node->getChildren());
    }

    /**
     * testGetChildrenReturnsArrayWithExpectedNodes
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetChildrenReturnsArrayWithExpectedNodes()
    {
        $node = $this->getNodeMock();
        $node->addChild($child0 = $this->getNodeMock());
        $node->addChild($child1 = $this->getNodeMock());

        $this->assertSame(array($child0, $child1), $node->getChildren());
    }

    /**
     * testGetChildThrowsExpectedExceptionForInvalidChildIndex
     *
     * @return void
     * @expectedException OutOfBoundsException
     * @since 1.0.0
     */
    public function testGetChildThrowsExpectedExceptionForInvalidChildIndex()
    {
        $node = $this->getNodeMock();
        $node->addChild($child0 = $this->getNodeMock());
        $node->addChild($child1 = $this->getNodeMock());

        $node->getChild(2);
    }

    /**
     * testGetChildReturnsExpectedNodeInstance
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetChildReturnsExpectedNodeInstance()
    {
        $node = $this->getNodeMock();
        $node->addChild($child0 = $this->getNodeMock());
        $node->addChild($child1 = $this->getNodeMock());
        $node->addChild($child2 = $this->getNodeMock());

        $this->assertSame($child1, $node->getChild(1));
    }

    /**
     * testGetFirstChildOfTypeReturnsNullByDefault
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetFirstChildOfTypeReturnsNullByDefault()
    {
        $this->assertNull(
            $this->getNodeMock()->getFirstChildOfType(
                PHP_Depend_Code_ASTArguments::CLAZZ
            )
        );
    }

    /**
     * testGetFirstChildOfTypeReturnsFirstMatchingChild
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetFirstChildOfTypeReturnsFirstMatchingChild()
    {
        $child0 = $this->getMockForAbstractClass(
            PHP_Depend_Code_ASTIndexExpression::CLAZZ
        );

        $node = $this->getNodeMock();
        $node->addChild($child0);

        $this->assertSame(
            $child0,
            $node->getFirstChildOfType(PHP_Depend_Code_ASTIndexExpression::CLAZZ)
        );
    }

    /**
     * testGetFirstChildOfTypeReturnsFirstMatchingChildRecursive
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetFirstChildOfTypeReturnsFirstMatchingChildRecursive()
    {
        $child0 = $this->getMockForAbstractClass(
            PHP_Depend_Code_ASTIndexExpression::CLAZZ
        );
        $child1 = $this->getMockForAbstractClass(
            PHP_Depend_Code_ASTArguments::CLAZZ
        );

        $node = $this->getNodeMock();
        $node->addChild($child0);
        $child0->addChild($child1);

        $this->assertSame(
            $child1,
            $node->getFirstChildOfType(PHP_Depend_Code_ASTArguments::CLAZZ)
        );
    }

    /**
     * testFindChildrenOfTypeReturnsEmptyArrayByDefault
     *
     * @return void
     * @since 1.0.0
     */
    public function testFindChildrenOfTypeReturnsEmptyArrayByDefault()
    {
        $node = $this->getNodeMock();
        $this->assertSame(
            array(),
            $node->findChildrenOfType(PHP_Depend_Code_ASTNode::CLAZZ)
        );
    }

    /**
     * testFindChildrenOfTypeReturnsDirectChild
     *
     * @return void
     * @since 1.0.0
     */
    public function testFindChildrenOfTypeReturnsDirectChild()
    {
        $child0 = $this->getNodeMock();
        $child1 = $this->getMockForAbstractClass(PHP_Depend_Code_ASTScope::CLAZZ);

        $node = $this->getNodeMock();
        $node->addChild($child0);
        $node->addChild($child1);

        $this->assertSame(
            array($child1),
            $node->findChildrenOfType(PHP_Depend_Code_ASTScope::CLAZZ)
        );
    }

    /**
     * testFindChildrenOfTypeReturnsIndirectChild
     *
     * @return void
     * @since 1.0.0
     */
    public function testFindChildrenOfTypeReturnsIndirectChild()
    {
        $child0 = $this->getNodeMock();
        $child1 = $this->getMockForAbstractClass(PHP_Depend_Code_ASTScope::CLAZZ);

        $node = $this->getNodeMock();
        $node->addChild($child0);
        $child0->addChild($child1);

        $this->assertSame(
            array($child1),
            $node->findChildrenOfType(PHP_Depend_Code_ASTScope::CLAZZ)
        );
    }

    /**
     * testFindChildrenOfTypeReturnsDirectAndIndirectChild
     *
     * @return void
     * @since 1.0.0
     */
    public function testFindChildrenOfTypeReturnsDirectAndIndirectChild()
    {
        $child0 = $this->getMockForAbstractClass(PHP_Depend_Code_ASTScope::CLAZZ);
        $child1 = $this->getMockForAbstractClass(PHP_Depend_Code_ASTScope::CLAZZ);
        $child2 = $this->getMockForAbstractClass(PHP_Depend_Code_ASTScope::CLAZZ);

        $node = $this->getNodeMock();
        $node->addChild($child0);
        $child0->addChild($child1);
        $child1->addChild($child2);

        $this->assertSame(
            array($child0, $child1, $child2),
            $node->findChildrenOfType(PHP_Depend_Code_ASTScope::CLAZZ)
        );
    }

    /**
     * testGetStartColumnReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetStartColumnReturnsZeroByDefault()
    {
        $node = $this->getNodeMock();
        self::assertSame(0, $node->getStartColumn());
    }

    /**
     * testGetStartColumnReturnsInjectedEndLineValue
     *
     * @return void
     */
    public function testGetStartColumnReturnsInjectedEndLineValue()
    {
        $node = $this->getNodeMock();
        $node->setStartColumn(42);

        self::assertEquals(42, $node->getStartColumn());
    }

    /**
     * testGetStartLineReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetStartLineReturnsZeroByDefault()
    {
        $node = $this->getNodeMock();
        self::assertSame(0, $node->getStartLine());
    }

    /**
     * testGetStartLineReturnsInjectedEndLineValue
     *
     * @return void
     */
    public function testGetStartLineReturnsInjectedEndLineValue()
    {
        $node = $this->getNodeMock();
        $node->setStartLine(42);

        self::assertEquals(42, $node->getStartLine());
    }

    /**
     * testGetEndColumnReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetEndColumnReturnsZeroByDefault()
    {
        $node = $this->getNodeMock();
        self::assertSame(0, $node->getEndColumn());
    }

    /**
     * testGetEndColumnReturnsInjectedEndLineValue
     *
     * @return void
     */
    public function testGetEndColumnReturnsInjectedEndLineValue()
    {
        $node = $this->getNodeMock();
        $node->setEndColumn(42);

        self::assertEquals(42, $node->getEndColumn());
    }

    /**
     * testGetEndLineReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetEndLineReturnsZeroByDefault()
    {
        $node = $this->getNodeMock();
        self::assertSame(0, $node->getEndLine());
    }

    /**
     * testGetEndLineReturnsInjectedEndLineValue
     *
     * @return void
     */
    public function testGetEndLineReturnsInjectedEndLineValue()
    {
        $node = $this->getNodeMock();
        $node->setEndLine(42);

        self::assertEquals(42, $node->getEndLine());
    }

    /**
     * testConfigureLinesAndColumnsSetsExpectedStartLine
     *
     * @return void
     * @since 1.0.0
     */
    public function testConfigureLinesAndColumnsSetsExpectedStartLine()
    {
        $node = $this->getNodeMock();
        $node->configureLinesAndColumns(13, 17, 23, 42);

        $this->assertEquals(13, $node->getStartLine());
    }

    /**
     * testConfigureLinesAndColumnsSetsExpectedEndLine
     *
     * @return void
     * @since 1.0.0
     */
    public function testConfigureLinesAndColumnsSetsExpectedEndLine()
    {
        $node = $this->getNodeMock();
        $node->configureLinesAndColumns(13, 17, 23, 42);

        $this->assertEquals(17, $node->getEndLine());
    }

    /**
     * testConfigureLinesAndColumnsSetsExpectedStartColumn
     *
     * @return void
     * @since 1.0.0
     */
    public function testConfigureLinesAndColumnsSetsExpectedStartColumn()
    {
        $node = $this->getNodeMock();
        $node->configureLinesAndColumns(13, 17, 23, 42);

        $this->assertEquals(23, $node->getStartColumn());
    }

    /**
     * testConfigureLinesAndColumnsSetsExpectedEndColumn
     *
     * @return void
     * @since 1.0.0
     */
    public function testConfigureLinesAndColumnsSetsExpectedEndColumn()
    {
        $node = $this->getNodeMock();
        $node->configureLinesAndColumns(13, 17, 23, 42);

        $this->assertEquals(42, $node->getEndColumn());
    }

    /**
     * testSleepReturnsExpectedSetOfPropertyNames
     *
     * @return void
     * @since 1.0.0
     */
    public function testSleepReturnsExpectedSetOfPropertyNames()
    {
        $node = $this->getNodeMock();
        $this->assertEquals(array('comment', 'metadata', 'nodes'), $node->__sleep());
    }

    /**
     * testUnserializeSetsParentNodeOnChildren
     *
     * @return void
     * @since 1.0.0
     */
    public function testUnserializeSetsParentNodeOnChildren()
    {
        $node = $this->getNodeMock();
        $node->addChild($this->getNodeMock());
        $node->addChild($this->getNodeMock());

        $copy = unserialize(serialize($node));

        $this->assertSame($copy, $copy->getChild(1)->getParent());
    }

    /**
     * Returns a mocked ast node instance.
     *
     * @return PHP_Depend_Code_ASTNode
     */
    private function getNodeMock()
    {
        return $this->getMockForAbstractClass(PHP_Depend_Code_ASTNode::CLAZZ);
    }
}
