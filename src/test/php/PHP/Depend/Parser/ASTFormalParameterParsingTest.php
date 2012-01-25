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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 * @since      0.10.2
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Parser} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * @since      0.10.2
 *
 * @covers PHP_Depend_Parser
 * @group pdepend
 * @group pdepend::parser
 * @group unittest
 */
class PHP_Depend_Parser_ASTFormalParameterParsingTest
    extends PHP_Depend_Parser_AbstractTest
{
    /**
     * testWithParentTypeHint
     * 
     * @return void
     */
    public function testWithParentTypeHint()
    {
        $typeHint = self::getFirstMethodFormalParameter()->getChild(0);
        self::assertInstanceOf(PHP_Depend_Code_ASTParentReference::CLAZZ, $typeHint);
    }

    /**
     * testWithParentTypeHintInFunctionThrowsExpectedException
     *
     * @return void
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testWithParentTypeHintInFunctionThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testWithParentTypeHintInRootClassThrowsExpectedException
     *
     * @return void
     * @expectedException PHP_Depend_Parser_InvalidStateException
     */
    public function testWithParentTypeHintInRootClassThrowsExpectedException()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * Returns the first formal parameter found in the associated test file.
     * 
     * @return PHP_Depend_Code_ASTFormalParameter
     */
    private static function getFirstMethodFormalParameter()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current()
            ->getMethods()
            ->current()
            ->getFirstChildOfType(PHP_Depend_Code_ASTFormalParameter::CLAZZ);
    }
}
