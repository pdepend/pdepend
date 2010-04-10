<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/BuilderI.php';
require_once 'PHP/Depend/Code/ASTClassOrInterfaceReference.php';
require_once 'PHP/Depend/Code/Class.php';

/**
 * description
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Code_ASTClassOrInterfaceReferenceTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * @return void
     * @group ast
     */
    public function testGetTypeInvokesBuildClassOrInterface()
    {
        $class = new PHP_Depend_Code_Class(null);

        $builder = $this->getMock('PHP_Depend_BuilderI');
        $builder->expects($this->once())
            ->method('getClassOrInterface')
            ->with($this->equalTo(__CLASS__))
            ->will($this->returnValue($class));

        $classOrInterfaceReference = new PHP_Depend_Code_ASTClassOrInterfaceReference(
            $builder, __CLASS__
        );

        $this->assertType(
            'PHP_Depend_Code_Class',
            $classOrInterfaceReference->getType()
        );
    }

    /**
     * Tests the start line value of an arguments instance.
     *
     * @return void
     * @group ast
     */
    public function testReferenceHasExpectedStartLine()
    {
        $reference = $this->_getFirstReferenceInFunction(__METHOD__);
        $this->assertSame(2, $reference->getStartLine());
    }

    /**
     * Tests the start column value of an arguments instance.
     *
     * @return void
     * @group ast
     */
    public function testReferenceHasExpectedStartColumn()
    {
        $reference = $this->_getFirstReferenceInFunction(__METHOD__);
        $this->assertSame(14, $reference->getStartColumn());
    }

    /**
     * Tests the end line value of an arguments instance.
     *
     * @return void
     * @group ast
     */
    public function testReferenceHasExpectedEndLine()
    {
        $reference = $this->_getFirstReferenceInFunction(__METHOD__);
        $this->assertSame(2, $reference->getEndLine());
    }

    /**
     * Tests the end column value of an arguments instance.
     *
     * @return void
     * @group ast
     */
    public function testReferenceHasExpectedEndColumn()
    {
        $reference = $this->_getFirstReferenceInFunction(__METHOD__);
        $this->assertSame(29, $reference->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTClassOrInterfaceReference
     */
    private function _getFirstReferenceInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ
        );
    }

    /**
     * Creates a concrete node implementation.
     *
     * @return PHP_Depend_Code_ASTNode
     */
    protected function createNodeInstance()
    {
        return new PHP_Depend_Code_ASTClassOrInterfaceReference(
            $this->getMock('PHP_Depend_BuilderI'),
            __METHOD__
        );
    }
}
