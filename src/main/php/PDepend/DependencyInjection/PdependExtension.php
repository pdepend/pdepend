<?php

namespace PDepend\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;


class PdependExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../../../resources'));
        $loader->load('services.xml');

        $settings = $this->createSettings($config);

        $configurationDefinition = $container->findDefinition('pdepend.configuration');
        $configurationDefinition->setArguments(array($settings));
    }

    private function createSettings($config)
    {
        $settings = new \stdClass();

        $settings->cache           = new \stdClass();
        $settings->cache->driver = $config['cache']['driver'];
        $settings->cache->location = $config['cache']['location'];

        $settings->imageConvert             = new \stdClass();
        $settings->imageConvert->fontSize   = $config['image_convert']['font_size'];
        $settings->imageConvert->fontFamily = $config['image_convert']['font_family'];

        $settings->parser          = new \stdClass();
        $settings->parser->nesting = $config['parser']['nesting'];

        return $settings;
    }

    public function getNamespace()
    {
        return 'http://pdepend.org/schema/dic/pdepend';
    }
}
