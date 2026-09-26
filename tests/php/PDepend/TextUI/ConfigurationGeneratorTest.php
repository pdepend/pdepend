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

use PDepend\AbstractTestCase;
use PDepend\Application;
use PDepend\Util\FileUtil;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use stdClass;

#[CoversClass(ConfigurationGenerator::class)]
#[Group('unittest')]
class ConfigurationGeneratorTest extends AbstractTestCase
{
    public function testGeneratedConfigurationKeepsDefaultCacheLocation(): void
    {
        static::assertSame(
            ['file', FileUtil::getDefaultCacheDir() . '/pdepend', 'Arial'],
            $this->loadGeneratedConfiguration('file', null, 'Arial'),
        );
    }

    public function testGeneratedConfigurationContainsAnswers(): void
    {
        static::assertSame(
            ['memory', '/tmp/my cache: dir', 'Luxi Sans, Verdana'],
            $this->loadGeneratedConfiguration('memory', '/tmp/my cache: dir', 'Luxi Sans, Verdana'),
        );
    }

    /**
     * Returns the cache driver, cache location and font family PDepend reads
     * from the generated file.
     *
     * @return array{mixed, mixed, mixed}
     */
    private function loadGeneratedConfiguration(string $driver, ?string $location, string $fontFamily): array
    {
        $file = $this->createRunResourceURI('config') . '.yml';
        file_put_contents($file, (new ConfigurationGenerator())->generate($driver, $location, $fontFamily));

        $application = new Application();
        $application->setConfigurationFile($file);
        $configuration = $application->getConfiguration();
        unlink($file);

        static::assertInstanceOf(stdClass::class, $configuration->cache);
        static::assertInstanceOf(stdClass::class, $configuration->imageConvert);

        return [
            $configuration->cache->driver,
            $configuration->cache->location,
            $configuration->imageConvert->fontFamily,
        ];
    }
}
