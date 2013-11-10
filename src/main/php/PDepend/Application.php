<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass;

/**
 * PDepend Application
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Application
{
    private $container;

    private $configurationFiles = array();

    public function getContainer()
    {
        if ($this->container === null) {
            $this->container = $this->createContainer();
        }

        return $this->container;
    }

    /**
     * @param string $configurationFile
     */
    public function addConfigurationFile($configurationFile)
    {
        $this->configurationFiles[] = $configurationFile;
    }

    /**
     * @return \PDepend\Util\Configuration
     */
    public function getConfiguration()
    {
        return $this->getContainer()->get('pdepend.configuration');
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private function createContainer()
    {
        $extensions = array(new DependencyInjection\PdependExtension());

        $container = new ContainerBuilder(new ParameterBag(array()));
        $container->prependExtensionConfig('pdepend', array());

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../../resources'));

        foreach ($extensions as $extension) {
            $container->registerExtension($extension);
        }

        foreach ($this->configurationFiles as $configurationFile) {
            $loader->load($configurationFile);
        }

        $container->compile();

        return $container;
    }

    public function getRunner()
    {
        return $this->getContainer()->get('pdepend.textui.runner'); // TODO: Use standard name? textui is detail.
    }

    /**
     * Returns available logger options and documentation messages.
     *
     * @return array(string => string)
     */
    public function getAvailableLoggerOptions()
    {
        return $this->getAvailableOptionsFor('pdepend.logger');
    }

    /**
     * Returns available analyzer options and documentation messages.
     *
     * @return array(string => string)
     */
    public function getAvailableAnalyzerOptions()
    {
        return $this->getAvailableOptionsFor('pdepend.analyzer');
    }

    /**
     * @return array(string => string)
     */
    private function getAvailableOptionsFor($serviceTag)
    {
        $container = $this->getContainer();

        $loggerServices = $container->findTaggedServiceIds($serviceTag);

        $options = array();

        foreach ($loggerServices as $loggerServiceTags) {
            foreach ($loggerServiceTags as $loggerServiceTag) {
                $options[$loggerServiceTag['option']] = $loggerServiceTag['message'];
            }
        }

        return $options;
    }
}