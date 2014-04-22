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

use PDepend\AbstractTest;

/**
 * Abstract test case for classes derived {@link \PDepend\Source\AST\ASTNode}ö
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\AST\ASTNode
 * @group unittest
 */
abstract class ASTNodeTest extends AbstractTest
{
//    /**
//     * testGetImageReturnsEmptyStringByDefault
//     *
//     * @return void
//     * @since 1.0.0
//     */
//    public function testGetImageReturnsEmptyStringByDefault()
//    {
//        $node = $this->createNodeInstance();
//        $this->assertSame('', $node->getImage());
//    }

    /**
     * testGetImageReturnsExpectedNodeImage
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetImageReturnsExpectedNodeImage()
    {
        $node = $this->createNodeInstance();
        $node->setImage(__FUNCTION__);

        $constant = get_class($node) . '::IMAGE';
        $this->assertEquals(defined($constant) ? constant($constant) : __FUNCTION__, $node->getImage());
    }

    /**
     * testGetCommentReturnsNullByDefault
     *
     * @return void
     */
    public function testGetCommentReturnsNullByDefault()
    {
        $node = $this->createNodeInstance();
        $this->assertNull($node->getComment());
    }

    /**
     * testGetCommentReturnsInjectedCommentValue
     *
     * @return void
     */
    public function testGetCommentReturnsInjectedCommentValue()
    {
        $node = $this->createNodeInstance();
        $node->setComment('/** Manuel */');

        $this->assertEquals('/** Manuel */', $node->getComment());
    }

    /**
     * testPrependChildSimplyAddsFirstChild
     *
     * @return void
     * @since 1.0.0
     */
    public function testPrependChildSimplyAddsFirstChild()
    {
        $node = $this->createNodeInstance();
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
        $node = $this->createNodeInstance();
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
        $node = $this->createNodeInstance();
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
        $node = $this->createNodeInstance();
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
        $node = $this->createNodeInstance();
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
        $node = $this->createNodeInstance();
        $this->assertSame(
            array(),
            $node->getParentsOfType('PDepend\\Source\\AST\\ASTScope')
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
        $parent0 = $this->getMockForAbstractClass('PDepend\\Source\\AST\\ASTScope');
        $parent1 = $this->getMockForAbstractClass('PDepend\\Source\\AST\\ASTNode');
        $parent2 = $this->getMockForAbstractClass('PDepend\\Source\\AST\\ASTScope');
        $parent3 = $this->getMockForAbstractClass('PDepend\\Source\\AST\\ASTNode');

        $node = $this->createNodeInstance();

        $parent3->addChild($node);
        $parent2->addChild($parent3);
        $parent1->addChild($parent2);
        $parent0->addChild($parent1);

        $this->assertSame(
            array($parent0, $parent2),
            $node->getParentsOfType('PDepend\\Source\\AST\\ASTScope')
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
        $node = $this->createNodeInstance();
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
        $node = $this->createNodeInstance();
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
        $node = $this->createNodeInstance();
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
        $node = $this->createNodeInstance();
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
                'PDepend\\Source\\AST\\ASTArguments'
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
     * @return void
     * @since 1.0.0
     */
    public function testGetFirstChildOfTypeReturnsFirstMatchingChildRecursive()
    {
        $child0 = $this->getMockForAbstractClass(
            'PDepend\\Source\\AST\\ASTIndexExpression'
        );
        $child1 = $this->getMockForAbstractClass(
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
     * @return void
     * @since 1.0.0
     */
    public function testFindChildrenOfTypeReturnsEmptyArrayByDefault()
    {
        $node = $this->getNodeMock();
        $this->assertSame(
            array(),
            $node->findChildrenOfType('PDepend\\Source\\AST\\ASTNode')
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
        $child1 = $this->getMockForAbstractClass('PDepend\\Source\\AST\\ASTScope');

        $node = $this->getNodeMock();
        $node->addChild($child0);
        $node->addChild($child1);

        $this->assertSame(
            array($child1),
            $node->findChildrenOfType('PDepend\\Source\\AST\\ASTScope')
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
        $child1 = $this->getMockForAbstractClass('PDepend\\Source\\AST\\ASTScope');

        $node = $this->getNodeMock();
        $node->addChild($child0);
        $child0->addChild($child1);

        $this->assertSame(
            array($child1),
            $node->findChildrenOfType('PDepend\\Source\\AST\\ASTScope')
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
        $child0 = $this->getMockForAbstractClass('PDepend\\Source\\AST\\ASTScope');
        $child1 = $this->getMockForAbstractClass('PDepend\\Source\\AST\\ASTScope');
        $child2 = $this->getMockForAbstractClass('PDepend\\Source\\AST\\ASTScope');

        $node = $this->getNodeMock();
        $node->addChild($child0);
        $child0->addChild($child1);
        $child1->addChild($child2);

        $this->assertSame(
            array($child0, $child1, $child2),
            $node->findChildrenOfType('PDepend\\Source\\AST\\ASTScope')
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
        $this->assertSame(0, $node->getStartColumn());
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

        $this->assertEquals(42, $node->getStartColumn());
    }

    /**
     * testGetStartLineReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetStartLineReturnsZeroByDefault()
    {
        $node = $this->getNodeMock();
        $this->assertSame(0, $node->getStartLine());
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

        $this->assertEquals(42, $node->getStartLine());
    }

    /**
     * testGetEndColumnReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetEndColumnReturnsZeroByDefault()
    {
        $node = $this->getNodeMock();
        $this->assertSame(0, $node->getEndColumn());
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

        $this->assertEquals(42, $node->getEndColumn());
    }

    /**
     * testGetEndLineReturnsZeroByDefault
     *
     * @return void
     */
    public function testGetEndLineReturnsZeroByDefault()
    {
        $node = $this->getNodeMock();
        $this->assertSame(0, $node->getEndLine());
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

        $this->assertEquals(42, $node->getEndLine());
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
     * @return \PDepend\Source\AST\ASTNode
     */
    private function getNodeMock()
    {
        return $this->getMockForAbstractClass('PDepend\\Source\\AST\\ASTNode');
    }

    /**
     * testPrependChildAddsChildAtFirstPosition
     *
     * @return void
     */
    public function testPrependChildAddsChildAtFirstPosition()
    {
        $child1 = $this->getMock('PDepend\\Source\\AST\\ASTNode');
        $child2 = $this->getMock('PDepend\\Source\\AST\\ASTNode');

        $parent = $this->createNodeInstance();
        $parent->prependChild($child2);
        $parent->prependChild($child1);

        $this->assertSame($child2, $parent->getChild(1));
    }

    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $methodName = 'visit' . substr(get_class($this), 22, -4);

        $visitor = $this->getMock('\\PDepend\\Source\ASTVisitor\\ASTVisitor');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo($methodName));

        $node = $this->createNodeInstance();
        $node->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $methodName = 'visit' . substr(get_class($this), 22, -4);

        $visitor = $this->getMock('\\PDepend\\Source\ASTVisitor\\ASTVisitor');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo($methodName))
            ->will($this->returnValue(42));

        $node = $this->createNodeInstance();
        $this->assertEquals(42, $node->accept($visitor));
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedFirstMatch()
    {
        $node2 = $this->getMock(
            '\PDepend\Source\AST\ASTNode',
            array(),
            array(),
            'PDepend_Source_AST_ASTNode_' . md5(microtime())
        );
        $node2->expects($this->never())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node = $this->createNodeInstance();
        $node->addChild($node2);

        $child = $node->getFirstChildOfType(get_class($node2));
        $this->assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNestedMatch()
    {
        $node1 = $this->getMock(
            '\PDepend\Source\AST\ASTNode',
            array(),
            array(),
            'PDepend_Source_AST_ASTNode_' . md5(microtime())
        );
        $node1->expects($this->never())
            ->method('getFirstChildOfType');

        $node3 = $this->getMock(
            '\PDepend\Source\AST\ASTNode',
            array(),
            array(),
            'PDepend_Source_AST_ASTNode_' . md5(microtime())
        );
        $node3->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue($node1));

        $node = $this->createNodeInstance();
        $node->addChild($node3);

        $child = $node->getFirstChildOfType(get_class($node1));
        $this->assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNull()
    {
        $name = 'PDepend_Source_AST_ASTNode_' . md5(microtime());
        
        $node2 = $this->getMock(
            '\PDepend\Source\AST\ASTNode',
            array(),
            array(),
            $name
        );
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node = $this->createNodeInstance();
        $node->addChild($node2);

        $this->assertNull($node->getFirstChildOfType($name . '_'));
    }

    /**
     * Tests the behavior of {@link \PDepend\Source\AST\ASTMethod::findChildrenOfType()}.
     *
     * @return void
     */
    public function testFindChildrenOfTypeReturnsExpectedResult()
    {
        $name = 'PDepend_Source_AST_ASTNode_' . md5(microtime());

        $node2 = $this->getMock(
            '\PDepend\Source\AST\ASTNode',
            array(),
            array(),
            $name
        );
        $node2->expects($this->once())
            ->method('findChildrenOfType')
            ->will($this->returnValue(array()));

        $node = $this->createNodeInstance();
        $node->addChild($node2);

        $children = $node->findChildrenOfType($name);
        $this->assertSame(array($node2), $children);
    }

    /**
     * Tests that the {@link \PDepend\Source\AST\ASTNode::getChild()} method throws
     * an exception for an undefined node offset.
     *
     * @return void
     * @covers \PDepend\Source\AST\ASTNode
     * @expectedException OutOfBoundsException
     */
    public function testGetChildThrowsExpectedExceptionForUndefinedOffset()
    {
        $node = $this->createNodeInstance();
        $node->getChild(42);
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return \PDepend\Source\AST\ASTNode
     */
    protected function createNodeInstance()
    {
        $class = substr(get_class($this), 0, -4);

        $reflection = new \ReflectionClass($class);
        if ($reflection->isAbstract()) {
            return $this->getMockForAbstractClass($class, array(__METHOD__));
        }
        return $reflection->newInstanceArgs(array(__METHOD__));
    }

    /**
     * Parses the given source file or directory with the default tokenizer
     * and node builder implementations.
     *
     * @param string $testCase
     * @param boolean $ignoreAnnotations
     *
     * @return \PDepend\Source\AST\ASTNamespace[]
     */
    public static function parseTestCaseSource($testCase, $ignoreAnnotations = false)
    {
        list($class, $method) = explode('::', $testCase);

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
