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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Abstract test case for classes derived {@link PHP_Depend_Code_ASTNode}ö
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Code_ASTNode
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
abstract class PHP_Depend_Code_ASTNodeTest extends PHP_Depend_AbstractTest
{
    /**
     * testPrependChildAddsChildAtFirstPosition
     *
     * @return void
     */
    public function testPrependChildAddsChildAtFirstPosition()
    {
        $child1 = $this->getMock(PHP_Depend_Code_ASTNode::CLAZZ);
        $child2 = $this->getMock(PHP_Depend_Code_ASTNode::CLAZZ);

        $parent = $this->createNodeInstance();
        $parent->prependChild($child2);
        $parent->prependChild($child1);

        self::assertSame($child2, $parent->getChild(1));
    }

    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $methodName = 'visit' . substr(get_class($this), 19, -4);

        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
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
        $methodName = 'visit' . substr(get_class($this), 19, -4);

        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo($methodName))
            ->will($this->returnValue(42));

        $node = $this->createNodeInstance();
        self::assertEquals(42, $node->accept($visitor));
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedFirstMatch()
    {
        $node2 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node2->expects($this->never())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node = $this->createNodeInstance();
        $node->addChild($node2);

        $child = $node->getFirstChildOfType(get_class($node2));
        self::assertSame($node2, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNestedMatch()
    {
        $node1 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node1->expects($this->never())
            ->method('getFirstChildOfType');

        $node3 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            'PHP_Depend_Code_ASTNodeI_' . md5(microtime())
        );
        $node3->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue($node1));

        $node = $this->createNodeInstance();
        $node->addChild($node3);

        $child = $node->getFirstChildOfType(get_class($node1));
        self::assertSame($node1, $child);
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::getFirstChildOfType()}.
     *
     * @return void
     */
    public function testGetFirstChildOfTypeReturnsTheExpectedNull()
    {
        $name = 'PHP_Depend_Code_ASTNodeI_' . md5(microtime());
        
        $node2 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
            array(),
            array(),
            $name
        );
        $node2->expects($this->once())
            ->method('getFirstChildOfType')
            ->will($this->returnValue(null));

        $node = $this->createNodeInstance();
        $node->addChild($node2);

        self::assertNull($node->getFirstChildOfType($name . '_'));
    }

    /**
     * Tests the behavior of {@link PHP_Depend_Code_Method::findChildrenOfType()}.
     *
     * @return void
     */
    public function testFindChildrenOfTypeReturnsExpectedResult()
    {
        $name = 'PHP_Depend_Code_ASTNodeI_' . md5(microtime());

        $node2 = $this->getMock(
            'PHP_Depend_Code_ASTNodeI',
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
        self::assertSame(array($node2), $children);
    }

    /**
     * Tests that the {@link PHP_Depend_Code_ASTNode::getChild()} method throws
     * an exception for an undefined node offset.
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
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
     * @return PHP_Depend_Code_ASTNode
     */
    protected function createNodeInstance()
    {
        $class = substr(get_class($this), 0, -4);

        include_once strtr($class, '_', '/') . '.php';

        $reflection = new ReflectionClass($class);
        if ($reflection->isAbstract()) {
            return $this->getMockForAbstractClass($class, array(__METHOD__));
        }
        return $reflection->newInstanceArgs(array(__METHOD__));
    }

    /**
     * Parses the given source file or directory with the default tokenizer
     * and node builder implementations.
     *
     * @param string  $testCase          Qualified test case name.
     * @param boolean $ignoreAnnotations The parser should ignore annotations.
     *
     * @return PHP_Depend_Code_NodeIterator
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
