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
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case for issue #84, where the object model should keep information about
 * primitive property and parameter types.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @group pdepend
 * @group pdepend::issues
 * @group pdepend::parser
 * @group unittest
 */
class PHP_Depend_Issues_KeepTypeInformationForPrimitivesIssue084Test
    extends PHP_Depend_Issues_AbstractTest
{
    /**
     * Tests that the parser sets the expected primitive type information.
     *
     * @param string $actual   The actual used type identifier.
     * @param string $expected The expected primitive type image.
     * 
     * @return void
     * @dataProvider dataProviderParserSetsExpectedPrimitivePropertyType
     */
    public function testParserSetsExpectedPrimitivePropertyType($actual, $expected)
    {
        $packages = self::parseTestCase(__METHOD__ . '_' . $actual);
        
        $type = $packages->current()
            ->getClasses()
            ->current()
            ->getFirstChildOfType(PHP_Depend_Code_ASTFieldDeclaration::CLAZZ)
            ->getFirstChildOfType(PHP_Depend_Code_ASTType::CLAZZ);

        $this->assertEquals($expected, $type->getImage());
    }

    /**
     * Tests that the parser sets the expected array type information.
     *
     * @return void
     */
    public function testParserSetsExpectedArrayPropertyType()
    {
        $packages = self::parseTestCase();

        $type = $packages->current()
            ->getClasses()
            ->current()
            ->getFirstChildOfType(PHP_Depend_Code_ASTFieldDeclaration::CLAZZ)
            ->getFirstChildOfType(PHP_Depend_Code_ASTType::CLAZZ);

        $this->assertTrue($type->isArray());
    }

    /**
     * Tests that the parser sets the expected array type information.
     *
     * @return void
     */
    public function testParserSetsExpectedArrayWithParenthesisPropertyType()
    {
        $packages = self::parseTestCase();

        $type = $packages->current()
            ->getClasses()
            ->current()
            ->getFirstChildOfType(PHP_Depend_Code_ASTFieldDeclaration::CLAZZ)
            ->getFirstChildOfType(PHP_Depend_Code_ASTType::CLAZZ);

        $this->assertTrue($type->isArray());
    }

    /**
     * Data provider that returns a list of actual input and expected output
     * primitive types.
     *
     * @return array
     */
    public static function dataProviderParserSetsExpectedPrimitivePropertyType()
    {
        return array(
            array('int',     'integer'),
            array('INTEger', 'integer'),
            array('float',   'float'),
            array('real',    'float'),
            array('double',  'float'),
            array('bool',    'boolean'),
            array('boolean', 'boolean'),
            array('false',   'boolean'),
            array('true',    'boolean'),
        );
    }
}
