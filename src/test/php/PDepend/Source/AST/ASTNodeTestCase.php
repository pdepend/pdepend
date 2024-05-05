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

use OutOfBoundsException;
use PDepend\AbstractTestCase;
use ReflectionClass;

/**
 * Abstract test case for classes derived {@link \PDepend\Source\AST\ASTNode}ö
 *
 * @covers \PDepend\Source\AST\AbstractASTNode
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
abstract class ASTNodeTestCase extends AbstractTestCase
{
    /**
     * testGetImageReturnsExpectedNodeImage
     *
     * @since 1.0.0
     */
    public function testGetImageReturnsExpectedNodeImage(): void
    {
        $node = $this->createNodeInstance();
        $node->setImage(__FUNCTION__);

        $constant = $node::class . '::IMAGE';
        $this->assertEquals(defined($constant) ? constant($constant) : __FUNCTION__, $node->getImage());
    }

    /**
     * testGetCommentReturnsNullByDefault
     */
    public function testGetCommentReturnsNullByDefault(): void
    {
        $node = $this->createNodeInstance();
        $this->assertNull($node->getComment());
    }

    /**
     * testGetCommentReturnsInjectedCommentValue
     */
    public function testGetCommentReturnsInjectedCommentValue(): void
    {
        $node = $this->createNodeInstance();
        $node->setComment('/** Manuel */');

        $this->assertEquals('/** Manuel */', $node->getComment());
    }

    /**
     * testPrependChildSimplyAddsFirstChild
     *
     * @since 1.0.0
     */
    public function testPrependChildSimplyAddsFirstChild(): void
    {
        $node = $this->createNodeInstance();
        $node->prependChild($child = $this->getNodeMock());

        $this->assertSame($child, $node->getChild(0));
    }

    /**
     * testPrependChildMovesFirstChild
     *
     * @since 1.0.0
     */
    public function testPrependChildMovesFirstChild(): void
    {
        $node = $this->createNodeInstance();
        $node->prependChild($child0 = $this->getNodeMock());
        $node->prependChild($child1 = $this->getNodeMock());

        $this->assertSame($child0, $node->getChild(1));
    }

    /**
     * testPrependChildPrependsNewChild
     *
     * @since 1.0.0
     */
    public function testPrependChildPrependsNewChild(): void
    {
        $node = $this->createNodeInstance();
        $node->prependChild($child0 = $this->getNodeMock());
        $node->prependChild($child1 = $this->getNodeMock());

        $this->assertSame($child1, $node->getChild(0));
    }

    /**
     * testGetParentReturnsNullByDefault
     *
     * @since 1.0.0
     */
    public function testGetParentReturnsNullByDefault(): void
    {
        $node = $this->createNodeInstance();
        $this->assertNull($node->getParent());
    }

    /**
     * testGetParentReturnsExpectedNode
     *
     * @since 1.0.0
     */
    public function testGetParentReturnsExpectedNode(): void
    {
        $node = $this->createNodeInstance();
        $node->setParent($parent = $this->getNodeMock());

        $this->assertSame($parent, $node->getParent());
    }

    /**
     * testGetParentsOfTypeReturnsEmptyArrayByDefault
     *
     * @since 1.0.0
     */
    public function testGetParentsOfTypeReturnsEmptyArrayByDefault(): void
    {
        $node = $this->createNodeInstance();
        $this->assertSame(
            [],
            $node->getParentsOfType('PDepend\\Source\\AST\\ASTScope')
        );
    }

    /**
     * testGetParentsOfTypeReturnsExpectedParentNodes
     *
     * @since 1.0.0
     */
    public function testGetParentsOfTypeReturnsExpectedParentNodes(): void
    {
        $parent0 = $this->getAbstractClassMock('PDepend\\Source\\AST\\ASTScope');
        $parent1 = $this->getAbstractClassMock('PDepend\\Source\\AST\\AbstractASTNode');
        $parent2 = $this->getAbstractClassMock('PDepend\\Source\\AST\\ASTScope');
        $parent3 = $this->getAbstractClassMock('PDepend\\Source\\AST\\AbstractASTNode');

        $node = $this->createNodeInstance();

        $parent3->addChild($node);
        $parent2->addChild($parent3);
        $parent1->addChild($parent2);
        $parent0->addChild($parent1);

        $this->assertSame(
            [$parent0, $parent2],
            $node->getParentsOfType('PDepend\\Source\\AST\\ASTScope')
        );
    }

    /**
     * testGetChildrenReturnsEmptyArrayByDefault
     *
     * @since 1.0.0
     */
    public function testGetChildrenReturnsEmptyArrayByDefault(): void
    {
        $node = $this->createNodeInstance();
        $this->assertSame([], $node->getChildren());
    }

    /**
     * testGetChildrenReturnsArrayWithExpectedNodes
     *
     * @since 1.0.0
     */
    public function testGetChildrenReturnsArrayWithExpectedNodes(): void
    {
        $node = $this->createNodeInstance();
        $node->addChild($child0 = $this->getNodeMock());
        $node->addChild($child1 = $this->getNodeMock());

        $this->assertSame([$child0, $child1], $node->getChildren());
    }

    /**
     * testGetChildThrowsExpectedExceptionForInvalidChildIndex
     *
     * @since 1.0.0
     */
    public function testGetChildThrowsExpectedExceptionForInvalidChildIndex(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $node = $this->createNodeInstance();
        $node->addChild($child0 = $this->getNodeMock());
        $node->addChild($child1 = $this->getNodeMock());

        $node->getChild(2);
    }

    /**
     * testGetChildReturnsExpectedNodeInstance
     *
     * @since 1.0.0
     */
    public function testGetChildReturnsExpectedNodeInstance(): void
    {
        $node = $this->createNodeInstance();
        $node->addChild($child0 = $this->getNodeMock());
        $node->addChild($child1 = $this->getNodeMock());
        $node->addChild($child2 = $this->getNodeMock());

        $this->assertSame($child1, $node->getChild(1));
    }

    /**
     * testGetFirstChildOfTypeReturnsNullByDefault
     *
     * @since 1.0.0
     */
    public function testGetFirstChildOfTypeReturnsNullByDefault(): void
    {
        $this->assertNull(
            $this->getNodeMock()->getFirstChildOfType(
                'PDepend\\Source\\AST\\ASTArguments'
            )
        );
    }

    /**
     * testGetFirstChildOfTypeReturnsFirstMatchingChild
     *
     * @since 1.0.0
     */
    public function testGetFirstChildOfTypeReturnsFirstMatchingChild(): void
    {
        $child0 = $this->getAbstractClassMock(
            'PDepend\\Source\\AST\\ASTIndexExpression'
        );

        $node = $this->getNodeMock();
        $node->addChild($child0);

        $this->assertSame(
            $child0,
            $node->getFirstChildOfType('PDepend\\Source\\AST\\ASTIndexExpression')
        );
    }

    /**
     * testGetFirstChildOfTypeReturnsFirstMatchingChildRecursive
     *
     * @since 1.0.0
     */
    public function testGetFirstChildOfTypeReturnsFirstMatchingChildRecursive(): void
    {
        $child0 = $this->getAbstractClassMock(
            'PDepend\\Source\\AST\\ASTIndexExpression'
        );
        $child1 = $this->getAbstractClassMock(
            'PDepend\\Source\\AST\\ASTArguments'
        );

        $node = $this->getNodeMock();
        $node->addChild($child0);
        $child0->addChild($child1);

        $this->assertSame(
            $child1,
            $node->getFirstChildOfType('PDepend\\Source\\AST\\ASTArguments')
        );
    }

    /**
     * testFindChildrenOfTypeReturnsEmptyArrayByDefault
     *
     * @since 1.0.0
     */
    public function testFindChildrenOfTypeReturnsEmptyArrayByDefault(): void
    {
        $node = $this->getNodeMock();
        $this->assertSame(
            [],
            $node->findChildrenOfType('PDepend\\Source\\AST\\ASTNode')
        );
    }

    /**
     * testFindChildrenOfTypeReturnsDirectChild
     *
     * @since 1.0.0
     */
    public function testFindChildrenOfTypeReturnsDirectChild(): void
    {
        $child0 = $this->getNodeMock();
        $child1 = $this->getAbstractClassMock('PDepend\\Source\\AST\\ASTScope');

        $node = $this->getNodeMock();
        $node->addChild($child0);
        $node->addChild($child1);

        $this->assertSame(
            [$child1],
            $node->findChildrenOfType('PDepend\\Source\\AST\\ASTScope')
        );
    }

    /**
     * testFindChildrenOfTypeReturnsIndirectChild
     *
     * @since 1.0.0
     */
    public function testFindChildrenOfTypeReturnsIndirectChild(): void
    {
        $child0 = $this->getNodeMock();
        $child1 = $this->getAbstractClassMock('PDepend\\Source\\AST\\ASTScope');

        $node = $this->getNodeMock();
        $node->addChild($child0);
        $child0->addChild($child1);

        $this->assertSame(
            [$child1],
            $node->findChildrenOfType('PDepend\\Source\\AST\\ASTScope')
        );
    }

    /**
     * testFindChildrenOfTypeReturnsDirectAndIndirectChild
     *
     * @since 1.0.0
     */
    public function testFindChildrenOfTypeReturnsDirectAndIndirectChild(): void
    {
        $child0 = $this->getAbstractClassMock('PDepend\\Source\\AST\\ASTScope');
        $child1 = $this->getAbstractClassMock('PDepend\\Source\\AST\\ASTScope');
        $child2 = $this->getAbstractClassMock('PDepend\\Source\\AST\\ASTScope');

        $node = $this->getNodeMock();
        $node->addChild($child0);
        $child0->addChild($child1);
        $child1->addChild($child2);

        $this->assertSame(
            [$child0, $child1, $child2],
            $node->findChildrenOfType('PDepend\\Source\\AST\\ASTScope')
        );
    }

    /**
     * testGetStartColumnReturnsZeroByDefault
     */
    public function testGetStartColumnReturnsZeroByDefault(): void
    {
        $node = $this->getNodeMock();
        $this->assertSame(0, $node->getStartColumn());
    }

    /**
     * testGetStartColumnReturnsInjectedEndLineValue
     */
    public function testGetStartColumnReturnsInjectedEndLineValue(): void
    {
        $node = $this->getNodeMock();
        $node->configureLinesAndColumns(0, 0, 42, 0);

        $this->assertEquals(42, $node->getStartColumn());
    }

    /**
     * testGetStartLineReturnsZeroByDefault
     */
    public function testGetStartLineReturnsZeroByDefault(): void
    {
        $node = $this->getNodeMock();
        $this->assertSame(0, $node->getStartLine());
    }

    /**
     * testGetStartLineReturnsInjectedEndLineValue
     */
    public function testGetStartLineReturnsInjectedEndLineValue(): void
    {
        $node = $this->getNodeMock();
        $node->configureLinesAndColumns(42, 0, 0, 0);

        $this->assertEquals(42, $node->getStartLine());
    }

    /**
     * testGetEndColumnReturnsZeroByDefault
     */
    public function testGetEndColumnReturnsZeroByDefault(): void
    {
        $node = $this->getNodeMock();
        $this->assertSame(0, $node->getEndColumn());
    }

    /**
     * testGetEndColumnReturnsInjectedEndLineValue
     */
    public function testGetEndColumnReturnsInjectedEndLineValue(): void
    {
        $node = $this->getNodeMock();
        $node->configureLinesAndColumns(0, 0, 0, 42);

        $this->assertEquals(42, $node->getEndColumn());
    }

    /**
     * testGetEndLineReturnsZeroByDefault
     */
    public function testGetEndLineReturnsZeroByDefault(): void
    {
        $node = $this->getNodeMock();
        $this->assertSame(0, $node->getEndLine());
    }

    /**
     * testGetEndLineReturnsInjectedEndLineValue
     */
    public function testGetEndLineReturnsInjectedEndLineValue(): void
    {
        $node = $this->getNodeMock();
        $node->configureLinesAndColumns(0, 42, 0, 0);

        $this->assertEquals(42, $node->getEndLine());
    }

    /**
     * testConfigureLinesAndColumnsSetsExpectedStartLine
     *
     * @since 1.0.0
     */
    public function testConfigureLinesAndColumnsSetsExpectedStartLine(): void
    {
        $node = $this->getNodeMock();
        $node->configureLinesAndColumns(13, 17, 23, 42);

        $this->assertEquals(13, $node->getStartLine());
    }

    /**
     * testConfigureLinesAndColumnsSetsExpectedEndLine
     *
     * @since 1.0.0
     */
    public function testConfigureLinesAndColumnsSetsExpectedEndLine(): void
    {
        $node = $this->getNodeMock();
        $node->configureLinesAndColumns(13, 17, 23, 42);

        $this->assertEquals(17, $node->getEndLine());
    }

    /**
     * testConfigureLinesAndColumnsSetsExpectedStartColumn
     *
     * @since 1.0.0
     */
    public function testConfigureLinesAndColumnsSetsExpectedStartColumn(): void
    {
        $node = $this->getNodeMock();
        $node->configureLinesAndColumns(13, 17, 23, 42);

        $this->assertEquals(23, $node->getStartColumn());
    }

    /**
     * testConfigureLinesAndColumnsSetsExpectedEndColumn
     *
     * @since 1.0.0
     */
    public function testConfigureLinesAndColumnsSetsExpectedEndColumn(): void
    {
        $node = $this->getNodeMock();
        $node->configureLinesAndColumns(13, 17, 23, 42);

        $this->assertEquals(42, $node->getEndColumn());
    }

    /**
     * testSleepReturnsExpectedSetOfPropertyNames
     *
     * @since 1.0.0
     */
    public function testSleepReturnsExpectedSetOfPropertyNames(): void
    {
        $node = $this->getNodeMock();
        $this->assertEquals(['comment', 'metadata', 'nodes'], $node->__sleep());
    }

    /**
     * testUnserializeSetsParentNodeOnChildren
     *
     * @since 1.0.0
     */
    public function testUnserializeSetsParentNodeOnChildren(): void
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
     * @return ASTNode
     */
    private function getNodeMock()
    {
        return $this->getAbstractClassMock('PDepend\\Source\\AST\\AbstractASTNode');
    }

    /**
     * testPrependChildAddsChildAtFirstPosition
     */
    public function testPrependChildAddsChildAtFirstPosition(): void
    {
        $child1 = $this->getMockBuilder('PDepend\\Source\\AST\\AbstractASTNode')
            ->getMock();
        $child2 = $this->getMockBuilder('PDepend\\Source\\AST\\AbstractASTNode')
            ->getMock();

        $parent = $this->createNodeInstance();
        $parent->prependChild($child2);
        $parent->prependChild($child1);

        $this->assertSame($child2, $parent->getChild(1));
    }

    /**
     * testAcceptInvokesVisitOnGivenVisitor
     */
    public function testAcceptInvokesVisitOnGivenVisitor(): void
    {
        $methodName = 'visit' . substr(get_class($this), 22, -4);

        $visitor = $this->getMockBuilder('\\PDepend\\Source\ASTVisitor\\ASTVisitor')
            ->getMock();
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo($methodName));

        $node = $this->createNodeInstance();
        $node->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     */
    public function testAcceptReturnsReturnValueOfVisitMethod(): void
    {
        $methodName = 'visit' . substr(get_class($this), 22, -4);

        $visitor = $this->getMockBuilder('\\PDepend\\Source\ASTVisitor\\ASTVisitor')
            ->getMock();
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo($methodName))
            ->will($this->returnValue(42));

        $node = $this->createNodeInstance();
        $this->assertEquals(42, $node->accept($visitor));
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedFirstMatch(): void
    {
        $node2 = $this->getMockBuilder('\PDepend\Source\AST\AbstractASTNode')
            ->setMockClassName('PDepend_Source_AST_ASTNode_' . md5(microtime()))
            ->getMock();
        $node2->expects($this->never())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node = $this->createNodeInstance();
        $node->addChild($node2);

        $child = $node->getFirstChildOfType($node2::class);
        $this->assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNestedMatch(): void
    {
        $node1 = $this->getMockBuilder('\PDepend\Source\AST\AbstractASTNode')
            ->setMockClassName('PDepend_Source_AST_ASTNode_' . md5(microtime()))
            ->getMock();
        $node1->expects($this->never())
            ->method('getFirstChildOfType');

        $node3 = $this->getMockBuilder('\PDepend\Source\AST\AbstractASTNode')
            ->setMockClassName('PDepend_Source_AST_ASTNode_' . md5(microtime()))
            ->getMock();
        $node3->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue($node1));

        $node = $this->createNodeInstance();
        $node->addChild($node3);

        $child = $node->getFirstChildOfType($node1::class);
        $this->assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNull(): void
    {
        $name = 'PDepend_Source_AST_ASTNode_' . md5(microtime());

        $node2 = $this->getMockBuilder('\PDepend\Source\AST\AbstractASTNode')
            ->setMockClassName($name)
            ->getMock();
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node = $this->createNodeInstance();
        $node->addChild($node2);

        $this->assertNull($node->getFirstChildOfType($name . '_'));
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::findChildrenOfType()}.
     */
    public function testFindChildrenOfTypeReturnsExpectedResult(): void
    {
        $name = 'PDepend_Source_AST_ASTNode_' . md5(microtime());

        $node2 = $this->getMockBuilder('\PDepend\Source\AST\AbstractASTNode')
            ->setMockClassName($name)
            ->getMock();
        $node2->expects($this->once())
            ->method('findChildrenOfType')
            ->will($this->returnValue([]));

        $node = $this->createNodeInstance();
        $node->addChild($node2);

        $children = $node->findChildrenOfType($name);
        $this->assertSame([$node2], $children);
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNode::getChild()} method throws
     * an exception for an undefined node offset.
     */
    public function testGetChildThrowsExpectedExceptionForUndefinedOffset(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $node = $this->createNodeInstance();
        $node->getChild(42);
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return ASTNode
     */
    protected function createNodeInstance()
    {
        $class = substr(get_class($this), 0, -4);

        $reflection = new ReflectionClass($class);
        if ($reflection->isAbstract()) {
            return $this->getAbstractClassMock($class, [__METHOD__]);
        }
        return $reflection->newInstanceArgs([__METHOD__]);
    }

    /**
     * Parses the given source file or directory with the default tokenizer
     * and node builder implementations.
     *
     * @param string $testCase
     * @param bool   $ignoreAnnotations
     *
     * @return \PDepend\Source\AST\ASTNamespace[]
     */
    public function parseTestCaseSource($testCase, $ignoreAnnotations = false)
    {
        [$class, $method] = explode('::', $testCase);

        return parent::parseSource(
            sprintf(
                'code/%s/%s.php',
                substr($class, strrpos($class, '_') + 1, -4),
                $method
            ),
            $ignoreAnnotations
        );
    }
}
