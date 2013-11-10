<?php

namespace PDepend\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use PDepend\Util\FileUtil;
use PDepend\Util\Workarounds;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $home = FileUtil::getUserHomeDirOrSysTempDir();
        $workarounds = new Workarounds();

        $defaultCacheDriver = ($workarounds->hasSerializeReferenceIssue()) ? 'memory' : 'file';

        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pdepend');

        $rootNode
            ->children()
                ->arrayNode('cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('driver')->defaultValue($defaultCacheDriver)->values(array('file', 'memory'))->end()
                        ->scalarNode('location')->defaultValue($home . '/.pdepend')->end()
                    ->end()
                ->end()
                ->arrayNode('image_convert')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('font_size')->defaultValue('11')->end()
                        ->scalarNode('font_family')->defaultValue('Arial')->end()
                    ->end()
                ->end()
                ->arrayNode('parser')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('nesting')->defaultValue(8192)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

