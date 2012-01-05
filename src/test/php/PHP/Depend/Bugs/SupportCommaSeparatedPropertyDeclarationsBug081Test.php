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
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case for bug 081 where PHP_Depend does not handle comma separated property
 * declarations.
 *
 * http://tracker.pdepend.org/pdepend/issue_tracker/issue/81/
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers stdClass
 * @group pdepend
 * @group pdepend::bugs
 * @group regressiontest
 */
class PHP_Depend_Bugs_SupportCommaSeparatedPropertyDeclarationsBug081Test
    extends PHP_Depend_Bugs_AbstractTest
{
    /**
     * Tests that the parser handles a comma separated property declaration.
     * 
     * @return void
     */
    public function testParserHandlesSimpleCommaSeparatedPropertyDeclaration()
    {
        $properties = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getProperties();

        self::assertEquals(2, count($properties));
    }

    /**
     * Tests that the parser handles a comma separated property declaration.
     *
     * @return void
     */
    public function testParserSetsSameVisibilityForAllPropertyDeclarations()
    {
        $properties = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getProperties();

        $actual = array();
        foreach ($properties as $property) {
            $actual[] = array(
                'private'  =>  $property->isPrivate(),
                'public'   =>  $property->isPublic()
            );
        }

        self::assertEquals(
            array(
                array('private' => false, 'public' => true),
                array('private' => false, 'public' => true),
                array('private' => true, 'public' => false),
                array('private' => true, 'public' => false),
            ),
            $actual
        );
    }

    /**
     * Tests that the parser handles a comma separated property declaration.
     *
     * @return void
     */
    public function testParserSetsExpectedStaticModifierForAllPropertyDeclarations()
    {
        $properties = self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getProperties();

        $actual = array();
        foreach ($properties as $property) {
            $actual[] = $property->isStatic();
        }

        self::assertEquals(array(true, true), $actual);
    }
}
