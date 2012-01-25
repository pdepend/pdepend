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
class PHP_Depend_Bugs_TokenizerKeywordSubstitutionBug76Test extends PHP_Depend_AbstractTest
{
    /**
     * This method tests that the parser does not substitute keyword tokens in
     * a class or object operator chain.
     *
     * @param string         $sourceFile Name of the text file.
     * @param array(integer) $tokenTypes List of all expected token types.
     *
     * @return void
     * @dataProvider dataProviderTokenizerKeywordSubstitutionInOperatorChain
     */
    public function testTokenizerKeywordSubstitutionInOperatorChain($sourceFile, array $tokenTypes)
    {
        $tokenizer = new PHP_Depend_Tokenizer_Internal();
        $tokenizer->setSourceFile($this->createCodeResourceURI($sourceFile));

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = $token->type;
        }

        self::assertEquals($tokenTypes, $actual);
    }

    /**
     * Data provider for the substitution bug in object and class operator
     * chains.
     *
     * @return array
     */
    public static function dataProviderTokenizerKeywordSubstitutionInOperatorChain()
    {
        return array(
            array(
                'bugs/076-001-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_VARIABLE,
                    PHP_Depend_ConstantsI::T_OBJECT_OPERATOR,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-002-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_VARIABLE,
                    PHP_Depend_ConstantsI::T_OBJECT_OPERATOR,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-003-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_VARIABLE,
                    PHP_Depend_ConstantsI::T_OBJECT_OPERATOR,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-004-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_VARIABLE,
                    PHP_Depend_ConstantsI::T_OBJECT_OPERATOR,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-005-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_VARIABLE,
                    PHP_Depend_ConstantsI::T_OBJECT_OPERATOR,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-006-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_VARIABLE,
                    PHP_Depend_ConstantsI::T_OBJECT_OPERATOR,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-007-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_VARIABLE,
                    PHP_Depend_ConstantsI::T_OBJECT_OPERATOR,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-008-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_VARIABLE,
                    PHP_Depend_ConstantsI::T_OBJECT_OPERATOR,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-009-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_VARIABLE,
                    PHP_Depend_ConstantsI::T_OBJECT_OPERATOR,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-010-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_VARIABLE,
                    PHP_Depend_ConstantsI::T_OBJECT_OPERATOR,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-011-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_VARIABLE,
                    PHP_Depend_ConstantsI::T_OBJECT_OPERATOR,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-012-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_DOUBLE_COLON,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-013-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_DOUBLE_COLON,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-014-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_DOUBLE_COLON,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-015-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_DOUBLE_COLON,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-016-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_DOUBLE_COLON,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-017-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_DOUBLE_COLON,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-018-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_DOUBLE_COLON,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-019-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_DOUBLE_COLON,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-020-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_DOUBLE_COLON,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-021-tokenizer-keyword-substitution.php',
                array(
                    PHP_Depend_ConstantsI::T_OPEN_TAG,
                    PHP_Depend_ConstantsI::T_ECHO,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_DOUBLE_COLON,
                    PHP_Depend_ConstantsI::T_STRING,
                    PHP_Depend_ConstantsI::T_SEMICOLON
                )
            ),
        );
    }
}
?>
