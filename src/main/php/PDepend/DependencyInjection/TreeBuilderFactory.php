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

use PDepend\Util\FileUtil;
use PDepend\Util\Workarounds;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This is the class that validates and merges configuration
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class TreeBuilderFactory
{
    const DEFAULT_TTL = 2592000; //30 days

    /**
     * @var array<Extension>
     */
    private $extensions = array();

    /**
     * @param array<Extension> $extensions
     */
    public function __construct(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $home = FileUtil::getUserHomeDirOrSysTempDir();
        $workarounds = new Workarounds();

        $defaultCacheDriver = ($workarounds->hasSerializeReferenceIssue()) ? 'memory' : 'file';

        $name = 'pdepend';
        $treeBuilder = new TreeBuilder($name);
        // @codeCoverageIgnoreStart
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = method_exists($treeBuilder, 'getRootNode')
            ? $treeBuilder->getRootNode()
            : $treeBuilder->root($name); // @phpstan-ignore-line
        // @codeCoverageIgnoreEnd

        $nodes = $rootNode->children();

        $cacheNode = $nodes->arrayNode('cache')->addDefaultsIfNotSet()->children();
        $cacheNode->enumNode('driver')->defaultValue($defaultCacheDriver)->values(array('file', 'memory'));
        $cacheNode->scalarNode('location')->info('This value is only used for the file cache.')->defaultValue($home . '/.pdepend');
        $cacheNode->integerNode('ttl')->info('This value is only used for the file cache. Value in seconds.')->defaultValue(self::DEFAULT_TTL);

        $imageConvertNode = $nodes->arrayNode('image_convert')->addDefaultsIfNotSet()->children();
        $imageConvertNode->scalarNode('font_size')->defaultValue('11');
        $imageConvertNode->scalarNode('font_family')->defaultValue('Arial');

        $parserNode = $nodes->arrayNode('parser')->addDefaultsIfNotSet()->children();
        $parserNode->integerNode('nesting')->defaultValue(65536);

        $extensionsNode = $nodes->arrayNode('extensions')->addDefaultsIfNotSet()->children();

        foreach ($this->extensions as $extension) {
            $extensionNode = $extensionsNode->arrayNode($extension->getName());
            $extension->getConfig($extensionNode);
        }

        return $treeBuilder;
    }
}
