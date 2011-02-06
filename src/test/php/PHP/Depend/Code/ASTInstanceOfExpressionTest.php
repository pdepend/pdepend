<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTInstanceOfExpression.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTInstanceOfExpression} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Code_ASTInstanceOfExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTInstanceOfExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitInstanceOfExpression'));

        $expr = new PHP_Depend_Code_ASTInstanceOfExpression();
        $expr->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTInstanceOfExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitInstanceOfExpression'))
            ->will($this->returnValue(42));

        $expr = new PHP_Depend_Code_ASTInstanceOfExpression();
        self::assertEquals(42, $expr->accept($visitor));
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     * 
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTInstanceOfExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testInstanceOfExpressionGraphWithStringIdentifier()
    {
        $this->assertInstanceOfGraphStatic(
            self::parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
                __FUNCTION__
        );
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTInstanceOfExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testInstanceOfExpressionGraphWithLocalNamespaceIdentifier()
    {
        $this->assertInstanceOfGraphStatic(
            self::parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
                'foo\bar\Baz'
        );
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTInstanceOfExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testInstanceOfExpressionGraphWithAbsoluteNamespaceIdentifier()
    {
        $this->assertInstanceOfGraphStatic(
            self::parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
                '\foo\bar\Baz'
        );
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTInstanceOfExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testInstanceOfExpressionGraphWithAliasedNamespaceIdentifier()
    {
        $this->assertInstanceOfGraphStatic(
            self::parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
                '\foo\bar\Baz'
        );
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTInstanceOfExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testInstanceOfExpressionGraphWithStdClass()
    {
        $this->assertInstanceOfGraphStatic(
            self::parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
                'stdClass'
        );
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTInstanceOfExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testInstanceOfExpressionGraphWithPHPIncompleteClass()
    {
        $this->assertInstanceOfGraphStatic(
            self::parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
                '__PHP_Incomplete_Class'
        );
    }

    /**
     * Tests that the created instanceof object graph has the expected structure.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTInstanceOfExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testInstanceOfExpressionGraphWithStaticProperty()
    {
        $this->assertInstanceOfGraphProperty(
            self::parseCodeResourceForTest()
                ->current()
                ->getFunctions()
                ->current(),
                '::'
        );
    }

    /**
     * Tests the instanceof expression object graph.
     *
     * @param object $parent The parent ast node.
     * @param string $image  The expected type image.
     *
     * @return void
     */
    protected function assertInstanceOfGraphStatic($parent, $image)
    {
        $this->assertInstanceOfGraph(
            $parent,
            $image,
            PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ
        );
    }

    /**
     * Tests the instanceof expression object graph.
     *
     * @param object $parent The parent ast node.
     * @param string $image  The expected type image.
     *
     * @return void
     */
    protected function assertInstanceOfGraphProperty($parent, $image)
    {
        $this->assertInstanceOfGraph(
            $parent,
            $image,
            PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
        );
    }

    /**
     * Tests the instanceof expression object graph.
     *
     * @param object $parent The parent ast node.
     * @param string $image  The expected type image.
     * @param string $type   The expected class or interface type.
     *
     * @return void
     */
    protected function assertInstanceOfGraph($parent, $image, $type)
    {
        $instanceOf = $parent->getFirstChildOfType(
            PHP_Depend_Code_ASTInstanceOfExpression::CLAZZ
        );

        $reference = $instanceOf->getChild(0);
        $this->assertType($type, $reference);
        $this->assertEquals($image, $reference->getImage());
    }

    /**
     * Creates a arguments node.
     *
     * @return PHP_Depend_Code_ASTInstanceOfExpression
     */
    protected function createNodeInstance()
    {
        return new PHP_Depend_Code_ASTInstanceOfExpression();
    }
}
