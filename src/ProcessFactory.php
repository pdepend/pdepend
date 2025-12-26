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

use React\ChildProcess\Process;
use RuntimeException;

final class ProcessFactory
{
    public function __construct(
        private readonly bool $withoutAnnotations,
    ) {
    }

    /**
     *  @throws RuntimeException
     */
    public function create(): Process
    {
        $commandArgs = $this->getCommandArgs();

        $fds = null;
        if (extension_loaded('sockets')) {
            $fds = [
                ['socket'],
                ['socket'],
                ['socket'],
            ];
        }

        return new Process(implode(' ', $commandArgs), null, null, $fds);
    }

    /**
     * @return list<string>
     *
     * @throws RuntimeException
     */
    public function getCommandArgs(): array
    {
        $phpBinary = PHP_BINARY;

        /** @var list<string> */
        $argv = $_SERVER['argv'];

        $mainScript = realpath(__DIR__ . '/../bin/pdepend');
        if (false === $mainScript && isset($argv[0]) && str_contains($argv[0], 'pdepend')) {
            $mainScript = $argv[0];
        }
        if (false === $mainScript) {
            throw new RuntimeException('Unable to determin main script');
        }

        $commandArgs = [
            $phpBinary,
            escapeshellarg($mainScript),
            '--worker',
        ];
        if ($this->withoutAnnotations) {
            $commandArgs[] = '--without-annotations';
        }

        /** @var list<string> */
        $argv = $_SERVER['argv'];
        foreach ($argv as $value) {
            if (str_starts_with($value, '--configuration=')) {
                $commandArgs[] = trim($value);
            }
        }

        return $commandArgs;
    }
}
