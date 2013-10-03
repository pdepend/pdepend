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

namespace PDepend\Bugs;

use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Test case for the keyword substitution bug no 76.
 *
 * http://tracker.pdepend.org/pdepend/issue_tracker/issue/76
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \stdClass
 * @group regressiontest
 */
class TokenizerKeywordSubstitutionBug76Test extends AbstractRegressionTest
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
        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceURI($sourceFile));

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = $token->type;
        }

        $this->assertEquals($tokenTypes, $actual);
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
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_VARIABLE,
                    Tokens::T_OBJECT_OPERATOR,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-002-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_VARIABLE,
                    Tokens::T_OBJECT_OPERATOR,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-003-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_VARIABLE,
                    Tokens::T_OBJECT_OPERATOR,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-004-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_VARIABLE,
                    Tokens::T_OBJECT_OPERATOR,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-005-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_VARIABLE,
                    Tokens::T_OBJECT_OPERATOR,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-006-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_VARIABLE,
                    Tokens::T_OBJECT_OPERATOR,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-007-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_VARIABLE,
                    Tokens::T_OBJECT_OPERATOR,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-008-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_VARIABLE,
                    Tokens::T_OBJECT_OPERATOR,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-009-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_VARIABLE,
                    Tokens::T_OBJECT_OPERATOR,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-010-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_VARIABLE,
                    Tokens::T_OBJECT_OPERATOR,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-011-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_VARIABLE,
                    Tokens::T_OBJECT_OPERATOR,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-012-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_STRING,
                    Tokens::T_DOUBLE_COLON,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-013-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_STRING,
                    Tokens::T_DOUBLE_COLON,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-014-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_STRING,
                    Tokens::T_DOUBLE_COLON,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-015-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_STRING,
                    Tokens::T_DOUBLE_COLON,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-016-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_STRING,
                    Tokens::T_DOUBLE_COLON,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-017-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_STRING,
                    Tokens::T_DOUBLE_COLON,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-018-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_STRING,
                    Tokens::T_DOUBLE_COLON,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-019-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_STRING,
                    Tokens::T_DOUBLE_COLON,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-020-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_STRING,
                    Tokens::T_DOUBLE_COLON,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
            array(
                'bugs/076-021-tokenizer-keyword-substitution.php',
                array(
                    Tokens::T_OPEN_TAG,
                    Tokens::T_ECHO,
                    Tokens::T_STRING,
                    Tokens::T_DOUBLE_COLON,
                    Tokens::T_STRING,
                    Tokens::T_SEMICOLON
                )
            ),
        );
    }
}
