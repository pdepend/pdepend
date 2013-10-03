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

namespace PDepend\Metrics;

use PDepend\Metrics\AnalyzerCacheAware;
use PDepend\Metrics\AnalyzerIterator;
use PDepend\Util\Cache\CacheDriver;

/**
 * This class provides a simple way to load all required analyzers by class,
 * implemented interface or parent class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class AnalyzerLoader implements \IteratorAggregate
{
    /**
     * All matching analyzer instances.
     *
     * @var \PDepend\Metrics\Analyzer[]
     */
    private $analyzers;
    
    private $acceptedTypes;

    private $options;

    /**
     * The system wide used cache.
     *
     * @var \PDepend\Util\Cache\CacheDriver
     * @since 1.0.0
     */
    private $cache;

    /**
     * Used locator for installed analyzer classes.
     *
     * @var \PDepend\Metrics\AnalyzerClassLocator
     */
    private $classLocator;

    /**
     * Constructs a new analyzer loader.
     *
     * @param \PDepend\Metrics\AnalyzerClassLocator $classLocator
     * @param \PDepend\Util\Cache\CacheDriver $cache
     * @param array $acceptedTypes
     * @param array $options
     */
    public function __construct(
        AnalyzerClassLocator $classLocator,
        CacheDriver $cache,
        array $acceptedTypes,
        array $options = array()
    ) {
        $this->cache        = $cache;
        $this->classLocator = $classLocator;

        $this->options       = $options;
        $this->acceptedTypes = $acceptedTypes;
    }

    /**
     * Returns a countable iterator of {@link \PDepend\Metrics\Analyzer}
     * instances that match against the given accepted types.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        if ($this->analyzers === null) {
            $this->initAnalyzers();
        }
        return new AnalyzerIterator($this->analyzers);
    }

    /**
     * Initializes all accepted analyzers.
     *
     * @return void
     * @since 0.9.10
     */
    private function initAnalyzers()
    {
        $this->analyzers = array();
        $this->loadAcceptedAnalyzers($this->acceptedTypes);
    }

    /**
     * Loads all accepted node analyzers.
     *
     * @param array $acceptedTypes Accepted/expected analyzer types.
     * @return \PDepend\Metrics\Analyzer[]
     */
    private function loadAcceptedAnalyzers(array $acceptedTypes)
    {
        $analyzers = array();
        foreach ($this->classLocator->findAll() as $reflection) {
            if ($this->isInstanceOf($reflection, $acceptedTypes)) {
                $analyzers[] = $this->createOrReturnAnalyzer($reflection);
            }
        }
        return $analyzers;
    }

    /**
     * This method checks if the given analyzer class implements one of the
     * expected analyzer types.
     *
     * @param \ReflectionClass $reflection
     * @param array $expectedTypes
     * @return boolean
     * @since 0.9.10
     */
    private function isInstanceOf(\ReflectionClass $reflection, array $expectedTypes)
    {
        foreach ($expectedTypes as $type) {
            if (interface_exists($type) && $reflection->implementsInterface($type)) {
                return true;
            }
            if (class_exists($type) && $reflection->isSubclassOf($type)) {
                return true;
            }
            if (strcasecmp($reflection->getName(), $type) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * This method creates a new analyzer instance or returns a previously
     * created instance of the given reflection instance.
     *
     * @param \ReflectionClass $reflection
     * @return \PDepend\Metrics\Analyzer
     * @since 0.9.10
     */
    private function createOrReturnAnalyzer(\ReflectionClass $reflection)
    {
        $name = $reflection->getName();
        if (!isset($this->analyzers[$name])) {
            $this->analyzers[$name] = $this->createAndConfigure($reflection);
        }
        return $this->analyzers[$name];
    }

    /**
     * Creates an analyzer instance of the given reflection class instance.
     *
     * @param \ReflectionClass $reflection
     * @return \PDepend\Metrics\Analyzer
     * @since 0.9.10
     */
    private function createAndConfigure(\ReflectionClass $reflection)
    {
        if ($reflection->getConstructor()) {
            $analyzer = $reflection->newInstance($this->options);
        } else {
            $analyzer = $reflection->newInstance();
        }
        return $this->configure($analyzer);
    }

    /**
     * Initializes the given analyzer instance.
     *
     * @param \PDepend\Metrics\Analyzer $analyzer Context analyzer instance.
     * @return \PDepend\Metrics\Analyzer
     * @since 0.9.10
     */
    private function configure(\PDepend\Metrics\Analyzer $analyzer)
    {
        if ($analyzer instanceof AnalyzerCacheAware) {
            $analyzer->setCache($this->cache);
        }

        if (!($analyzer instanceof AggregateAnalyzer)) {
            return $analyzer;
        }
        
        $required = $this->loadAcceptedAnalyzers($analyzer->getRequiredAnalyzers());
        foreach ($required as $requiredAnalyzer) {
            $analyzer->addAnalyzer($requiredAnalyzer);
        }
        return $analyzer;
    }
}
