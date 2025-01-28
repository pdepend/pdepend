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
 * @since 1.0.0
 */

namespace PDepend\Metrics;

use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Util\Cache\CacheDriver;

/**
 * This abstract class provides an analyzer that provides the basic infrastructure
 * for caching.
 *
 * @template TData
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 */
abstract class AbstractCachingAnalyzer extends AbstractAnalyzer implements AnalyzerCacheAware
{
    /**
     * Collected node metrics
     *
     * @var array<string, TData>
     */
    protected array $metrics;

    /**
     * Metrics restored from the cache. This property is only used temporary.
     *
     * @var array<string, TData>
     */
    private array $metricsCached = [];

    /** Injected cache driver. */
    private CacheDriver $cache;

    /**
     * Setter method for the system-wide used cache.
     */
    public function setCache(CacheDriver $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * Getter method for the system-wide used cache.
     */
    public function getCache(): CacheDriver
    {
        return $this->cache;
    }

    /**
     * Tries to restore the metrics for a cached node. If this method has
     * restored the metrics it will return <b>TRUE</b>, otherwise the return
     * value will be <b>FALSE</b>.
     */
    protected function restoreFromCache(ASTClass|ASTCompilationUnit|ASTFunction|ASTInterface|ASTMethod $node): bool
    {
        $id = $node->getId();
        if ($node->isCached() && isset($this->metricsCached[$id])) {
            $this->metrics[$id] = $this->metricsCached[$id];

            return true;
        }

        return false;
    }

    /**
     * Initializes the previously calculated metrics from the cache.
     */
    protected function loadCache(): void
    {
        /** @var array<string, TData> */
        $nodes = (array) $this->cache
            ->type('metrics')
            ->restore(static::class);

        $this->metricsCached = $nodes;
    }

    /**
     * Unloads the metrics cache and stores the current set of metrics in the
     * cache.
     */
    protected function unloadCache(): void
    {
        $this->cache
            ->type('metrics')
            ->store(static::class, $this->metrics);

        $this->metricsCached = [];
    }
}
