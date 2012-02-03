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

require_once dirname(__FILE__) . '/ASTNodeTest.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTClosure} class.
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
 * @covers PHP_Depend_Parser
 * @covers PHP_Depend_Code_ASTNode
 * @covers PHP_Depend_Code_ASTClosure
 * @group pdepend
 * @group pdepend::ast
 * @group unittest
 */
class PHP_Depend_Code_ASTClosureTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testReturnsByReferenceReturnsFalseByDefault
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsFalseByDefault()
    {
        $closure = $this->_getFirstClosureInFunction();
        $this->assertFalse($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsFalseByDefaultForStaticClosure
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsFalseByDefaultForStaticClosure()
    {
        $closure = $this->_getFirstClosureInFunction();
        $this->assertFalse($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsTrueForClosure
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsTrueForClosure()
    {
        $closure = $this->_getFirstClosureInFunction();
        $this->assertTrue($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsTrueForStaticClosure
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsTrueForStaticClosure()
    {
        $closure = $this->_getFirstClosureInFunction();
        $this->assertTrue($closure->returnsByReference());
    }

    /**
     * testReturnsByReferenceReturnsTrueForAssignedClosure
     *
     * @return void
     */
    public function testReturnsByReferenceReturnsTrueForAssignedClosure()
    {
        $closure = $this->_getFirstClosureInFunction();
        $this->assertTrue($closure->returnsByReference());
    }

    /**
     * testParserHandlesPureClosureStatementWithoutAssignment
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserHandlesPureClosureStatementWithoutAssignment()
    {
        $closure = $this->_getFirstClosureInFunction();
        $this->assertInstanceOf(PHP_Depend_Code_ASTClosure::CLAZZ, $closure);
    }

    /**
     * testIsStaticReturnsFalseByDefault
     *
     * @return void
     */
    public function testIsStaticReturnsFalseByDefault()
    {
        $closure = new PHP_Depend_Code_ASTClosure();
        $this->assertFalse($closure->isStatic());
    }

    /**
     * testIsStaticReturnsTrueWhenSetToTrue
     *
     * @return void
     */
    public function testIsStaticReturnsTrueWhenSetToTrue()
    {
        $closure = new PHP_Depend_Code_ASTClosure();
        $closure->setStatic(true);
        
        $this->assertTrue($closure->isStatic());
    }

    /**
     * testIsStaticReturnsFalseWhenSetToFalse
     *
     * @return void
     */
    public function testIsStaticReturnsFalseWhenSetToFalse()
    {
        $closure = new PHP_Depend_Code_ASTClosure();
        $closure->setStatic(false);

        $this->assertFalse($closure->isStatic());
    }

    /**
     * testIsStaticReturnsFalseForNonStaticClosure
     *
     * Source:
     * <code>
     * return function($x, $y) {
     *     return pow($x, $y);
     * }
     * </code>
     * 
     * @return void
     */
    public function testIsStaticReturnsFalseForNonStaticClosure()
    {
        $closure = $this->_getFirstClosureInFunction();
        $this->assertFalse($closure->isStatic());
    }

    /**
     * testIsStaticReturnsTrueForStaticClosure
     *
     * Source:
     * <code>
     * return static function($x, $y) {
     *     return pow($x, $y);
     * }
     * </code>
     *
     * @return void
     */
    public function testIsStaticReturnsTrueForStaticClosure()
    {
        $closure = $this->_getFirstClosureInFunction();
        $this->assertTrue($closure->isStatic());
    }

    /**
     * testClosureContainsExpectedNumberChildNodes
     *
     * @return void
     */
    public function testClosureContainsExpectedNumberChildNodes()
    {
        $closure = $this->_getFirstClosureInFunction();
        $this->assertEquals(2, count($closure->getChildren()));
    }

    /**
     * Tests the start line value.
     *
     * @return void
     */
    public function testClosureHasExpectedStartLine()
    {
        $label = $this->_getFirstClosureInFunction();
        $this->assertEquals(4, $label->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     */
    public function testClosureHasExpectedStartColumn()
    {
        $label = $this->_getFirstClosureInFunction();
        $this->assertEquals(12, $label->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     */
    public function testClosureHasExpectedEndLine()
    {
        $label = $this->_getFirstClosureInFunction();
        $this->assertEquals(6, $label->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     */
    public function testClosureHasExpectedEndColumn()
    {
        $label = $this->_getFirstClosureInFunction();
        $this->assertEquals(5, $label->getEndColumn());
    }

    /**
     * testStaticClosureHasExpectedStartLine
     *
     * @return void
     */
    public function testStaticClosureHasExpectedStartLine()
    {
        $label = $this->_getFirstClosureInFunction();
        $this->assertEquals(4, $label->getStartLine());
    }

    /**
     * testStaticClosureHasExpectedEndLine
     *
     * @return void
     */
    public function testStaticClosureHasExpectedEndLine()
    {
        $label = $this->_getFirstClosureInFunction();
        $this->assertEquals(7, $label->getEndLine());
    }

    /**
     * testStaticClosureHasExpectedStartColumn
     *
     * @return void
     */
    public function testStaticClosureHasExpectedStartColumn()
    {
        $label = $this->_getFirstClosureInFunction();
        $this->assertEquals(12, $label->getStartColumn());
    }

    /**
     * testStaticClosureHasExpectedEndColumn
     *
     * @return void
     */
    public function testStaticClosureHasExpectedEndColumn()
    {
        $label = $this->_getFirstClosureInFunction();
        $this->assertEquals(9, $label->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return PHP_Depend_Code_ASTClosure
     */
    private function _getFirstClosureInFunction()
    {
        return $this->getFirstNodeOfTypeInFunction(
            self::getCallingTestMethod(),
            PHP_Depend_Code_ASTClosure::CLAZZ
        );
    }
}
