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

namespace PDepend;

spl_autoload_register(function ($class) {
    if (substr($class, 0, strlen(__NAMESPACE__)) !== __NAMESPACE__) {
        return;
    }

    $file = __DIR__ . strtr(str_replace(__NAMESPACE__, '', $class), '\\', '/') . '.php';

    if (file_exists($file)) {
        include_once $file;
    }
});

$replacements = array(
    __DIR__ . '/../../../../vendor/phpunit/phpunit-mock-objects/src/Generator.php' => array(
        "if (version_compare(PHP_VERSION, '7.1', '>=') && \$parameter->allowsNull() && !\$parameter->isVariadic()) {",
        "if (version_compare(PHP_VERSION, '7.1', '>=') && version_compare(PHP_VERSION, '8.0', '<') && \$parameter->allowsNull() && !\$parameter->isVariadic()) {",
    ),
    __DIR__ . '/../../../../vendor/phpunit/phpunit-mock-objects/src/Framework/MockObject/Generator.php' => array(
        'final private function',
        'private function',
    ),
);

foreach ($replacements as $file => $replacement) {
    list($from, $to) = $replacement;

    echo "$file: ";

    if (!file_exists($file)) {
        echo "File not found.\n";

        continue;
    }

    $contents = @file_get_contents($file) ?: '';
    $newContents = str_replace($from, $to, $contents);

    if ($newContents !== $contents) {
        file_put_contents($file, $newContents);
        echo "Content changed.\n";

        continue;
    }

    echo "Replace pattern not found.\n";
}

require_once __DIR__ . '/../../../../vendor/autoload.php';
