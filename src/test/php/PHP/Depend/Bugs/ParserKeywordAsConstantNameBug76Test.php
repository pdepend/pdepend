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

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the keyword substitution bug no 76.
 *
 * http://tracker.pdepend.org/pdepend/issue_tracker/issue/76
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
class PHP_Depend_Bugs_ParserKeywordAsConstantNameBug76Test extends PHP_Depend_AbstractTest
{
    /**
     * This method tests that the parser handles reserved keywords in type
     * constant names correct.
     *
     * @param string         $sourceFile   Name of the test file.
     * @param array(integer) $constantName Name of the expected constant
     *
     * @return void
     * @dataProvider dataProviderReservedKeywordAsTypeConstantName
     */
    public function testReservedKeywordAsTypeConstantName($sourceFile, $constantName)
    {
        $packages = self::parseSource($sourceFile);

        $constants = $packages->current()
            ->getTypes()
            ->current()
            ->getConstants();

        $this->assertArrayHasKey($constantName, $constants);
    }

    /**
     * Data provider for the reserved keyword as constant name test.
     *
     * @return array
     */
    public static function dataProviderReservedKeywordAsTypeConstantName()
    {
        return array(
            array(
                'bugs/076-022-tokenizer-keyword-substitution.php',
                'null'
            ),
            array(
                'bugs/076-023-tokenizer-keyword-substitution.php',
                'use'
            ),
            array(
                'bugs/076-024-tokenizer-keyword-substitution.php',
                'goto'
            ),
            array(
                'bugs/076-025-tokenizer-keyword-substitution.php',
                'self'
            ),
            array(
                'bugs/076-026-tokenizer-keyword-substitution.php',
                'true'
            ),
            array(
                'bugs/076-027-tokenizer-keyword-substitution.php',
                'false'
            ),
            array(
                'bugs/076-028-tokenizer-keyword-substitution.php',
                'parent'
            ),
            array(
                'bugs/076-029-tokenizer-keyword-substitution.php',
                'namespace'
            ),
            array(
                'bugs/076-030-tokenizer-keyword-substitution.php',
                '__dir__'
            ),
            array(
                'bugs/076-031-tokenizer-keyword-substitution.php',
                '__NaMeSpAcE__'
            ),
        );
    }
}
?>
