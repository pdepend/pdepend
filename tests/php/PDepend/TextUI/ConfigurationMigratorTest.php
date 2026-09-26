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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

#[CoversClass(ConfigurationMigrator::class)]
#[Group('unittest')]
class ConfigurationMigratorTest extends AbstractTestCase
{
    private const XML = <<<'XML'
        <?xml version="1.0"?>
        <symfony:container xmlns:symfony="http://symfony.com/schema/dic/services"
            xmlns="http://pdepend.org/schema/dic/pdepend">
            <config>
                <image-convert>
                    <font-family>Arial</font-family>
                    <font-size>12</font-size>
                </image-convert>
                <cache>
                    <driver>file</driver>
                    <location>/tmp/pdepend</location>
                    <ttl>2592000</ttl>
                </cache>
                <parser>
                    <nesting>65536</nesting>
                </parser>
            </config>
        </symfony:container>
        XML;

    public function testGetTargetFile(): void
    {
        static::assertSame('/a/pdepend.yml', ConfigurationMigrator::getTargetFile('/a/pdepend.xml'));
        static::assertSame('/a/pdepend.yml.dist', ConfigurationMigrator::getTargetFile('/a/pdepend.xml.dist'));
        static::assertSame('/a/pdepend.yml', ConfigurationMigrator::getTargetFile('/a/pdepend.yml'));
    }

    public function testMigrate(): void
    {
        $this->skipIfNotSupported();

        $file = $this->createRunResourceURI('config') . '.xml';
        file_put_contents($file, self::XML);

        $yaml = (new ConfigurationMigrator())->migrate($file);
        unlink($file);

        static::assertSame(
            [
                'pdepend' => [
                    'image-convert' => ['font-family' => 'Arial', 'font-size' => 12],
                    'cache' => ['driver' => 'file', 'location' => '/tmp/pdepend', 'ttl' => 2592000],
                    'parser' => ['nesting' => 65536],
                ],
            ],
            Yaml::parse($yaml),
        );
    }

    public function testMigrateRefusesServices(): void
    {
        $this->skipIfNotSupported();

        $file = $this->createRunResourceURI('config') . '.xml';
        file_put_contents($file, <<<'XML'
            <?xml version="1.0"?>
            <symfony:container xmlns:symfony="http://symfony.com/schema/dic/services">
                <symfony:services>
                    <symfony:service id="foo" class="stdClass"/>
                </symfony:services>
            </symfony:container>
            XML);

        try {
            $this->expectException(RuntimeException::class);
            (new ConfigurationMigrator())->migrate($file);
        } finally {
            unlink($file);
        }
    }

    private function skipIfNotSupported(): void
    {
        if (!ConfigurationMigrator::isSupported()) {
            static::markTestSkipped('XML configuration requires Symfony 7 or older.');
        }
    }
}
