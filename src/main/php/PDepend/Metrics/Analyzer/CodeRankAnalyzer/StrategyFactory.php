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

namespace PDepend\Metrics\Analyzer\CodeRankAnalyzer;

use InvalidArgumentException;

/**
 * Factory for the different code rank strategies.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class StrategyFactory
{
    /** The identifier for the inheritance strategy. */
    private const STRATEGY_INHERITANCE = 'inheritance';

    /** The identifier for the property strategy. */
    private const STRATEGY_PROPERTY = 'property';

    /** The identifier for the method strategy. */
    private const STRATEGY_METHOD = 'method';

    /** The default strategy. */
    private string $defaultStrategy = self::STRATEGY_INHERITANCE;

    /**
     * List of all valid properties.
     *
     * @var array<string, class-string<CodeRankStrategyI>>
     */
    private array $validStrategies = [
        self::STRATEGY_INHERITANCE => InheritanceStrategy::class,
        self::STRATEGY_METHOD => MethodStrategy::class,
        self::STRATEGY_PROPERTY => PropertyStrategy::class,
    ];

    /**
     * Creates the default code rank strategy.
     */
    public function createDefaultStrategy(): CodeRankStrategyI
    {
        return $this->createStrategy($this->defaultStrategy);
    }

    /**
     * Creates a code rank strategy for the given identifier.
     *
     * @param string $strategyName The strategy identifier.
     * @throws InvalidArgumentException If the given <b>$id</b> is not valid or
     *                                  no matching class declaration exists.
     */
    public function createStrategy(string $strategyName): CodeRankStrategyI
    {
        if (!isset($this->validStrategies[$strategyName])) {
            throw new InvalidArgumentException(
                sprintf('Cannot load file for identifier "%s".', $strategyName),
            );
        }

        $className = $this->validStrategies[$strategyName];

        if (!class_exists($className)) {
            $fileName = str_replace('\\', '/', $className) . '.php';

            include_once $fileName;
        }

        return new $className();
    }
}
