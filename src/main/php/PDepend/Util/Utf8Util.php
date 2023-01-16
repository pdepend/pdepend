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

namespace PDepend\Util;

/**
 * This is a simply utility class that will ensure the encoding of a raw string
 * into an UTF8 encoded string. It will try using "iconv" extension if
 * available, or "mbstring" extension if available, or native PHP function if
 * available, or finally a polyfill if nothing is available.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 2.2.x
 */
final class Utf8Util
{
    /**
     * @param string $raw
     *
     * @return string
     */
    public static function ensureEncoding($raw)
    {
        $encoding = 'UTF8';
        if (function_exists('mb_detect_encoding')) {
            $encoding = mb_detect_encoding($raw) ?: $encoding;
        }

        $text = '';

        // Try to convert raw text to UTF8 using "iconv", if exists.
        if (function_exists('iconv')) {
            $text = @iconv($encoding, 'UTF8//IGNORE', $raw) ?: '';
        }

        // Then try with "mbstring" extension, if available.
        if ($text === '' && extension_loaded('mbstring')) {
            $text = mb_convert_encoding($raw, 'UTF-8', mb_list_encodings());
        }

        // Then try with native PHP function, if not removed.
        if ($text === '' && function_exists('utf8_encode')) {
            $text = @utf8_encode($raw);
        }

        // Then finally use a polyfill.
        if ($text === '') {
            $text = self::polyfillUtf8Encode($raw);
        }

        return $text;
    }

    /**
     * Polyfill exported from 
     * @link https://github.com/symfony/polyfill-php72/blob/main/Php72.php
     *
     * @param string $s
     *
     * @return string
     */
    private static function polyfillUtf8Encode($s)
    {
        $s .= $s;
        $len = \strlen($s);

        for ($i = $len >> 1, $j = 0; $i < $len; ++$i, ++$j) {
            switch (true) {
                case $s[$i] < "\x80": $s[$j] = $s[$i]; break;
                case $s[$i] < "\xC0": $s[$j] = "\xC2"; $s[++$j] = $s[$i]; break;
                default: $s[$j] = "\xC3"; $s[++$j] = \chr(\ord($s[$i]) - 64); break;
            }
        }

        return substr($s, 0, $j);
    }
}
