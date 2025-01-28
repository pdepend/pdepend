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

namespace PDepend\DependencyInjection;

use RuntimeException;
use stdClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as SymfonyExtension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * PDepend DependencyInjection Extension for Symfony DIC
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class PdependExtension extends SymfonyExtension
{
    /**
     * @param array<array<array<array<string>>>> $configs
     * @throws RuntimeException
     *
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $extensionManager = new ExtensionManager();

        foreach ($configs as $config) {
            if (!isset($config['extensions'])) {
                continue;
            }

            foreach ($config['extensions'] as $config) {
                if (!isset($config['class'])) {
                    continue;
                }
                if (!is_a($config['class'], Extension::class, true)) {
                    throw new RuntimeException(
                        sprintf('Class "%s" is not a valid Extension', $config['class']),
                    );
                }

                $extensionManager->activateExtension($config['class']);
            }
        }

        $configuration = new Configuration($extensionManager->getActivatedExtensions());

        /** @var array<string, array<string, mixed>> */
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../../resources'));
        $loader->load('services.xml');

        foreach ($extensionManager->getActivatedExtensions() as $extension) {
            /** @var array<mixed> */
            $extensionConfig = $config['extensions'][$extension->getName()];

            $tempContainer = new ContainerBuilder(new ParameterBag([]));
            $tempContainer->addObjectResource($extension);

            // load extension into temporary container
            $extension->load($extensionConfig, $tempContainer);

            // merge temporary container into normal one
            $container->merge($tempContainer);
        }

        $settings = $this->createSettings($config);

        $configurationDefinition = $container->findDefinition('pdepend.configuration');
        $configurationDefinition->setArguments([$settings]);
    }

    /**
     * @param array<string, array<string, mixed>> $config
     */
    private function createSettings(array $config): stdClass
    {
        $settings = new stdClass();

        $settings->cache = new stdClass();
        $settings->cache->driver = $config['cache']['driver'];
        $settings->cache->location = $config['cache']['location'];
        $settings->cache->ttl = $config['cache']['ttl'];

        $settings->imageConvert = new stdClass();
        $settings->imageConvert->fontSize = $config['image_convert']['font_size'];
        $settings->imageConvert->fontFamily = $config['image_convert']['font_family'];

        $settings->parser = new stdClass();
        $settings->parser->nesting = $config['parser']['nesting'];

        return $settings;
    }

    public function getNamespace(): string
    {
        return 'http://pdepend.org/schema/dic/pdepend';
    }
}
