<?php

/**
 * This file is part of PDepend.
 *
 * Copyright (c) 2008-2026 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2026 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\TextUI;

use Symfony\Component\Yaml\Yaml;

/**
 * Renders a pdepend.yml configuration file from the answers given.
 */
final class ConfigurationGenerator
{
    /**
     * @param string $cacheDriver Either "file" or "memory".
     * @param ?string $cacheLocation Cache directory, null keeps the default location.
     * @param string $fontFamily Font family used in the generated charts.
     */
    public function generate(string $cacheDriver, ?string $cacheLocation, string $fontFamily): string
    {
        $location = $cacheLocation === null
            ? '    # location: ~/.cache/pdepend # Defaults to $XDG_CACHE_HOME/pdepend'
            : '    location: ' . $this->dump($cacheLocation);

        return <<<YAML
            pdepend:
              cache:
                driver: {$this->dump($cacheDriver)}
            {$location}
                # ttl: 2592000 # Seconds, only used by the file cache
              image-convert:
                font-family: {$this->dump($fontFamily)}
                # font-size: 11

            YAML;
    }

    private function dump(string $value): string
    {
        return Yaml::dump($value);
    }
}
