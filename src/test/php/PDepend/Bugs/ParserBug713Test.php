<?php

namespace PDepend\Bugs;

use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Test case for bug #713.
 *
 * @ticket 713
 * @covers \stdClass
 * @group regressiontest
 */
class ParserBug713Test extends AbstractRegressionTest
{
    public function testConstantArrayIndexIsset()
    {
        $tokenizer = new PHPTokenizerInternal();
        $tokenizer->setSourceFile($this->createCodeResourceURI('bugs/713/testConstantArrayIndexIsset.php'));

        $actual = array();
        while (is_object($token = $tokenizer->next())) {
            $actual[] = $token->type;
        }

        $tokenTypes = array(
            Tokens::T_OPEN_TAG,
            Tokens::T_STRING,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_CONSTANT_ENCAPSED_STRING,
            Tokens::T_COMMA,
            Tokens::T_SQUARED_BRACKET_OPEN,
            Tokens::T_SQUARED_BRACKET_CLOSE,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_SEMICOLON,
            Tokens::T_IF,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_ISSET,
            Tokens::T_PARENTHESIS_OPEN,
            Tokens::T_STRING,
            Tokens::T_SQUARED_BRACKET_OPEN,
            Tokens::T_CONSTANT_ENCAPSED_STRING,
            Tokens::T_SQUARED_BRACKET_CLOSE,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_PARENTHESIS_CLOSE,
            Tokens::T_CURLY_BRACE_OPEN,
            Tokens::T_CURLY_BRACE_CLOSE
        );

        $this->assertEquals($tokenTypes, $actual);
    }
}
