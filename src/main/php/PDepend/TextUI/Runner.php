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

namespace PDepend\TextUI;

use Exception;
use PDepend\Engine;
use PDepend\Input\ExcludePathFilter;
use PDepend\Input\ExtensionFilter;
use PDepend\ProcessListener;
use PDepend\Report\ReportGeneratorFactory;
use PDepend\Source\AST\ASTArtifactList\PackageArtifactFilter;
use RuntimeException;

/**
 * The command line runner starts a PDepend process.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Runner
{
    /** Marks the default success exit. */
    public const SUCCESS_EXIT = 0;

    /** Marks an internal exception exit. */
    public const EXCEPTION_EXIT = 2;

    /**
     * List of allowed file extensions. Default file extensions are <b>php</b>
     * and <p>php5</b>.
     *
     * @var array<string>
     */
    private array $extensions = ['php', 'php5'];

    /**
     * List of exclude directories. Default exclude dirs are <b>.svn</b> and
     * <b>CVS</b>.
     *
     * @var array<string>
     */
    private array $excludeDirectories = ['.git', '.svn', 'CVS'];

    /**
     * List of exclude namespaces.
     *
     * @var array<string>
     */
    private array $excludeNamespaces = [];

    /**
     * List of source code directories and files.
     *
     * @var array<string>
     */
    private array $sourceArguments = [];

    /** Should the parse ignore doc comment annotations? */
    private bool $withoutAnnotations = false;

    /**
     * List of log identifiers and log files.
     *
     * @var array<string, string>
     */
    private array $loggerMap = [];

    /**
     * List of cli options for loggers or analyzers.
     *
     * @var array<string, array<int, string>|string>
     */
    private array $options = [];

    /**
     * This of process listeners that will be hooked into PDepend's analyzing
     * process.
     *
     * @var ProcessListener[]
     */
    private array $processListeners = [];

    /**
     * List of error messages for all parsing errors.
     *
     * @var array<string>
     */
    private array $parseErrors = [];

    public function __construct(
        private readonly ReportGeneratorFactory $reportGeneratorFactory,
        private Engine $engine,
    ) {
    }

    /**
     * Sets a list of allowed file extensions.
     *
     * NOTE: If you call this method, it will replace the default file extensions.
     *
     * @param array<string> $extensions
     */
    public function setFileExtensions(array $extensions): void
    {
        $this->extensions = $extensions;
    }

    /**
     * Sets a list of exclude directories.
     *
     * NOTE: If this method is called, it will overwrite the default settings.
     *
     * @param array<string> $excludeDirectories
     */
    public function setExcludeDirectories(array $excludeDirectories): void
    {
        $this->excludeDirectories = $excludeDirectories;
    }

    /**
     * Sets a list of exclude packages.
     *
     * @param array<string> $excludePackages
     */
    public function setExcludeNamespaces(array $excludePackages): void
    {
        $this->excludeNamespaces = $excludePackages;
    }

    /**
     * Sets a list of source directories and files.
     *
     * @param array<string> $sourceArguments
     */
    public function setSourceArguments(array $sourceArguments): void
    {
        $this->sourceArguments = $sourceArguments;
    }

    /**
     * Should the parser ignore doc comment annotations?
     */
    public function setWithoutAnnotations(): void
    {
        $this->withoutAnnotations = true;
    }

    /**
     * Adds a logger to this runner.
     */
    public function addReportGenerator(string $generatorId, string $reportFile): void
    {
        $this->loggerMap[$generatorId] = $reportFile;
    }

    /**
     * Adds a logger or analyzer option.
     *
     * @param array<int, string>|string $value
     */
    public function addOption(string $identifier, array|string $value): void
    {
        $this->options[$identifier] = $value;
    }

    /**
     * Adds a process listener instance that will be hooked into PDepend's
     * analyzing process.
     */
    public function addProcessListener(ProcessListener $processListener): void
    {
        $this->processListeners[] = $processListener;
    }

    /**
     * Starts the main PDepend process and returns <b>true</b> after a successful
     * execution.
     *
     * @throws RuntimeException An exception with a readable error message and
     *                          an exit code.
     */
    public function run(): int
    {
        $engine = $this->engine;
        $engine->setOptions($this->options);

        if (count($this->extensions) > 0) {
            $filter = new ExtensionFilter($this->extensions);
            $engine->addFileFilter($filter);
        }

        if (count($this->excludeDirectories) > 0) {
            $exclude = $this->excludeDirectories;
            $filter = new ExcludePathFilter($exclude);
            $engine->addFileFilter($filter);
        }

        if (count($this->excludeNamespaces) > 0) {
            $exclude = $this->excludeNamespaces;
            $filter = new PackageArtifactFilter($exclude);
            $engine->setCodeFilter($filter);
        }

        if ($this->withoutAnnotations) {
            $engine->setWithoutAnnotations();
        }

        // Try to set all source directories.
        try {
            foreach ($this->sourceArguments as $sourceArgument) {
                if (is_dir($sourceArgument)) {
                    $engine->addDirectory($sourceArgument);
                } else {
                    $engine->addFile($sourceArgument);
                }
            }
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), self::EXCEPTION_EXIT, $e);
        }

        if (count($this->loggerMap) === 0) {
            throw new RuntimeException('No output specified.', self::EXCEPTION_EXIT);
        }

        // To append all registered loggers.
        try {
            foreach ($this->loggerMap as $generatorId => $reportFile) {
                // Create a new logger
                $generator = $this->reportGeneratorFactory->createGenerator($generatorId, $reportFile);

                $engine->addReportGenerator($generator);
            }
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), self::EXCEPTION_EXIT, $e);
        }

        foreach ($this->processListeners as $processListener) {
            $engine->addProcessListener($processListener);
        }

        try {
            $engine->analyze();

            foreach ($engine->getExceptions() as $exception) {
                $this->parseErrors[] = $exception->getMessage();
            }
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), self::EXCEPTION_EXIT, $e);
        }

        return self::SUCCESS_EXIT;
    }

    /**
     * This method will return <b>true</b> when there were errors during the
     * parse process.
     */
    public function hasParseErrors(): bool
    {
        return (count($this->parseErrors) > 0);
    }

    /**
     * This method will return an <b>array</b> with error messages for all
     * failures that happened during the parsing process.
     *
     * @return array<string>
     */
    public function getParseErrors(): array
    {
        return $this->parseErrors;
    }
}
