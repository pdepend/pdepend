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
    /**
     * Marks the default success exit.
     */
    public const SUCCESS_EXIT = 0;

    /**
     * Marks an internal exception exit.
     */
    public const EXCEPTION_EXIT = 2;

    /**
     * List of allowed file extensions. Default file extensions are <b>php</b>
     * and <p>php5</b>.
     *
     * @var array<string>
     */
    private $extensions = ['php', 'php5'];

    /**
     * List of exclude directories. Default exclude dirs are <b>.svn</b> and
     * <b>CVS</b>.
     *
     * @var array<string>
     */
    private $excludeDirectories = ['.git', '.svn', 'CVS'];

    /**
     * List of exclude namespaces.
     *
     * @var array<string>
     */
    private $excludeNamespaces = [];

    /**
     * List of source code directories and files.
     *
     * @var array<string>
     */
    private $sourceArguments = [];

    /**
     * Should the parse ignore doc comment annotations?
     *
     * @var bool
     */
    private $withoutAnnotations = false;

    /**
     * List of log identifiers and log files.
     *
     * @var array<string, string>
     */
    private $loggerMap = [];

    /**
     * List of cli options for loggers or analyzers.
     *
     * @var array<string, mixed>
     */
    private $options = [];

    /**
     * This of process listeners that will be hooked into PDepend's analyzing
     * process.
     *
     * @var ProcessListener[]
     */
    private $processListeners = [];

    /**
     * List of error messages for all parsing errors.
     *
     * @var array<string>
     */
    private $parseErrors = [];

    /**
     * @var ReportGeneratorFactory
     */
    private $reportGeneratorFactory;

    /**
     * @var Engine
     */
    private $engine;

    public function __construct(ReportGeneratorFactory $reportGeneratorFactory, Engine $engine)
    {
        $this->reportGeneratorFactory = $reportGeneratorFactory;
        $this->engine = $engine;
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
     *
     * @param string $generatorId
     * @param string $reportFile
     */
    public function addReportGenerator($generatorId, $reportFile): void
    {
        $this->loggerMap[$generatorId] = $reportFile;
    }

    /**
     * Adds a logger or analyzer option.
     *
     * @param string $identifier
     * @param array<string>|string $value
     */
    public function addOption($identifier, $value): void
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
     * @return int
     * @throws RuntimeException An exception with a readable error message and
     *                          an exit code.
     */
    public function run()
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

        if ($this->withoutAnnotations === true) {
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
            throw new RuntimeException($e->getMessage(), self::EXCEPTION_EXIT);
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
            throw new RuntimeException($e->getMessage(), self::EXCEPTION_EXIT);
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
     *
     * @return bool
     */
    public function hasParseErrors()
    {
        return (count($this->parseErrors) > 0);
    }

    /**
     * This method will return an <b>array</b> with error messages for all
     * failures that happened during the parsing process.
     *
     * @return array<string>
     */
    public function getParseErrors()
    {
        return $this->parseErrors;
    }
}
