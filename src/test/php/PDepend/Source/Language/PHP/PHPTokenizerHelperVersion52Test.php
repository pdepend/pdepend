<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTest;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPTokenizerHelperVersion52} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\PHPTokenizerHelperVersion52
 * @group unittest
 */
class PHPTokenizerHelperVersion52Test extends AbstractTest
{
    /**
     * Tests that the helper class creates the expected token array with
     * namespace separators.
     *
     * @return void
     */
    public function testHelperCreatesExpectedTokenArrayWithBackslash()
    {
        $tokens = PHPTokenizerHelperVersion52::tokenize(
            '<?php namespace \foo\bar; ?>'
        );

        // Replace second token for this test
        $tokens[1] = array(T_STRING, 'namespace', 1);

        $this->assertSame(
            array(
                array(T_OPEN_TAG, '<?php ', 1),
                array(T_STRING, 'namespace', 1),
                array(T_WHITESPACE, ' ', 1),
                '\\',
                array(T_STRING, 'foo', 1),
                '\\',
                array(T_STRING, 'bar', 1),
                ';',
                array(T_WHITESPACE, ' ', 1),
                array(T_CLOSE_TAG, '?>', 1)
            ),
            $this->appendLineNumberInPHP522($tokens)
        );
    }

    /**
     * Tests that the helper class creates the expected token array with
     * namespace separators.
     *
     * @return void
     */
    public function testHelperCreatesExpectedTokenArrayWithNamespacedAllocation()
    {
        $tokens = PHPTokenizerHelperVersion52::tokenize(
            '<?php new \foo\Bar(); ?>'
        );

        $this->assertSame(
            array(
                array(T_OPEN_TAG, '<?php ', 1),
                array(T_NEW, 'new', 1),
                array(T_WHITESPACE, ' ', 1),
                '\\',
                array(T_STRING, 'foo', 1),
                '\\',
                array(T_STRING, 'Bar', 1),
                '(',
                ')',
                ';',
                array(T_WHITESPACE, ' ', 1),
                array(T_CLOSE_TAG, '?>', 1)
            ),
            $this->appendLineNumberInPHP522($tokens)
        );
    }

    /**
     * Tests that the helper class creates the expected token array with
     * namespace separators.
     *
     * @return void
     */
    public function testHelperCreatesExpectedTokenArrayWithNamespacedQualifiedName()
    {
        $tokens = PHPTokenizerHelperVersion52::tokenize(
            '<?php \foo::bar(); ?>'
        );

        $this->assertSame(
            array(
                array(T_OPEN_TAG, '<?php ', 1),
                '\\',
                array(T_STRING, 'foo', 1),
                array(T_DOUBLE_COLON, '::', 1),
                array(T_STRING, 'bar', 1),
                '(',
                ')',
                ';',
                array(T_WHITESPACE, ' ', 1),
                array(T_CLOSE_TAG, '?>', 1)
            ),
            $this->appendLineNumberInPHP522($tokens)
        );
    }

    /**
     * Tests that the helper ignores backslashes used as escape character.
     *
     * @return void
     */
    public function testHelperIgnoresBackslashAsDoubleQuoteEscapeCharacter()
    {
        $tokens = PHPTokenizerHelperVersion52::tokenize(
            '<?php echo "foo\"bar";'
        );

        $this->assertSame(
            array(
                array(T_OPEN_TAG, '<?php ', 1),
                array(T_ECHO, 'echo', 1),
                array(T_WHITESPACE, ' ', 1),
                array(T_CONSTANT_ENCAPSED_STRING, '"foo\"bar"', 1),
                ';'
            ),
            $this->appendLineNumberInPHP522($tokens)
        );
    }

    /**
     * Tests that the helper ignores backslashes used as escape character.
     *
     * @return void
     */
    public function testHelperIgnoresBackslashAsSignleQuoteEscapeCharacter()
    {
        $tokens = PHPTokenizerHelperVersion52::tokenize(
            "<?php echo 'foo\'bar';"
        );

        $this->assertSame(
            array(
                array(T_OPEN_TAG, '<?php ', 1),
                array(T_ECHO, 'echo', 1),
                array(T_WHITESPACE, ' ', 1),
                array(T_CONSTANT_ENCAPSED_STRING, "'foo\'bar'", 1),
                ';'
            ),
            $this->appendLineNumberInPHP522($tokens)
        );
    }

    /**
     * Tests that the helper ignores a class name in a string.
     *
     * @return void
     */
    public function testHelperIgnoresQualifiedClassNameInDoubleQuotedString()
    {
        $tokens = PHPTokenizerHelperVersion52::tokenize(
            '<?php $clazz = "foo\\bar";'
        );

        $this->assertSame(
            array(
                array(T_OPEN_TAG, '<?php ', 1),
                array(T_VARIABLE, '$clazz', 1),
                array(T_WHITESPACE, ' ', 1),
                '=',
                array(T_WHITESPACE, ' ', 1),
                array(T_CONSTANT_ENCAPSED_STRING, '"foo\\bar"', 1),
                ';'
            ),
            $this->appendLineNumberInPHP522($tokens)
        );
    }

    /**
     * This method will append the third token array element, which contains the
     * line number of a token. This feature was introduced with PHP version
     * 5.2.2.
     *
     * @param array(mixed) $tokens The input token array.
     *
     * @return array(mixed)
     * @todo Refactor, has become useless after dropping PHP 5.3 support.
     */
    protected function appendLineNumberInPHP522(array $tokens)
    {
        return $tokens;
    }
}
