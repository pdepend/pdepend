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

use PDepend\DependencyInjection\PdependExtension;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Converts a PDepend 2 XML configuration file to YAML.
 *
 * The conversion relies on Symfony's XmlFileLoader, which was removed in
 * Symfony 8, so it is only available with Symfony 7 or older.
 */
final class ConfigurationMigrator
{
    public static function isSupported(): bool
    {
        return class_exists(XmlFileLoader::class);
    }

    /**
     * Returns the YAML file name matching the given XML file name,
     * pdepend.xml.dist becomes pdepend.yml.dist.
     */
    public static function getTargetFile(string $xmlFile): string
    {
        return preg_replace('(\.xml(\.dist)?$)', '.yml$1', $xmlFile) ?? $xmlFile;
    }

    /**
     * Returns the pdepend settings of the given XML file as YAML.
     *
     * @throws RuntimeException When the file contains more than pdepend settings.
     */
    public function migrate(string $xmlFile): string
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new PdependExtension());

        $loader = new XmlFileLoader($container, new FileLocator(dirname($xmlFile)));
        $loader->load($xmlFile);

        $services = array_diff(array_keys($container->getDefinitions()), ['service_container']);
        if ($services !== [] || $container->getParameterBag()->all() !== []) {
            throw new RuntimeException(sprintf(
                'The configuration file "%s" defines services or parameters, which cannot be migrated automatically.',
                $xmlFile,
            ));
        }

        $config = [];
        foreach ($container->getExtensionConfig('pdepend') as $values) {
            $config = array_replace_recursive($config, $values);
        }

        return Yaml::dump(['pdepend' => $config], 10, 2);
    }
}
