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

namespace PDepend\TextUI;

use PDepend\Application;
use PDepend\Input\ExcludePathFilter;
use PDepend\Input\ExtensionFilter;
use PDepend\ProcessListener;
use PDepend\Report\GeneratorFactory;
use PDepend\Source\AST\ASTArtifactList\PackageArtifactFilter;
use PDepend\Util\Configuration;

/**
 * The command line runner starts a PDepend process.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Runner
{
    /**
     * Marks the default success exit.
     */
    const SUCCESS_EXIT = 0;

    /**
     * Marks an internal exception exit.
     */
    const EXCEPTION_EXIT = 2;

    /**
     * The system configuration.
     *
     * @var Configuration
     * @since 0.10.0
     */
    protected $configuration = null;

    /**
     * List of allowed file extensions. Default file extensions are <b>php</b>
     * and <p>php5</b>.
     *
     * @var array(string)
     */
    private $extensions = array('php', 'php5');

    /**
     * List of exclude directories. Default exclude dirs are <b>.svn</b> and
     * <b>CVS</b>.
     *
     * @var array(string)
     */
    private $excludeDirectories = array('.git', '.svn', 'CVS');

    /**
     * List of exclude packages.
     *
     * @var array(string)
     */
    private $excludePackages = array();

    /**
     * List of source code directories and files.
     *
     * @var array(string)
     */
    private $sourceArguments = array();

    /**
     * Should the parse ignore doc comment annotations?
     *
     * @var boolean
     */
    private $withoutAnnotations = false;

    /**
     * List of log identifiers and log files.
     *
     * @var array(string=>string)
     */
    private $loggerMap = array();

    /**
     * List of cli options for loggers or analyzers.
     *
     * @var array(string=>mixed)
     */
    private $options = array();

    /**
     * This of process listeners that will be hooked into PDepend's analyzing
     * process.
     *
     * @var ProcessListener[]
     */
    private $processListeners = array();

    /**
     * List of error messages for all parsing errors.
     *
     * @var array(string)
     */
    private $parseErrors = array();

    /**
     * Sets the system configuration.
     *
     * @param \PDepend\Util\Configuration $configuration The system configuration.
     * @return void
     * @since 0.10.0
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Sets a list of allowed file extensions.
     *
     * NOTE: If you call this method, it will replace the default file extensions.
     *
     * @param array(string) $extensions List of file extensions.
     * @return void
     */
    public function setFileExtensions(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Sets a list of exclude directories.
     *
     * NOTE: If this method is called, it will overwrite the default settings.
     *
     * @param array(string) $excludeDirectories All exclude directories.
     * @return void
     */
    public function setExcludeDirectories(array $excludeDirectories)
    {
        $this->excludeDirectories = $excludeDirectories;
    }

    /**
     * Sets a list of exclude packages.
     *
     * @param array(string) $excludePackages Exclude packages.
     * @return void
     */
    public function setExcludePackages(array $excludePackages)
    {
        $this->excludePackages = $excludePackages;
    }

    /**
     * Sets a list of source directories and files.
     *
     * @param array(string) $sourceArguments The source directories.
     * @return void
     */
    public function setSourceArguments(array $sourceArguments)
    {
        $this->sourceArguments = $sourceArguments;
    }

    /**
     * Should the parser ignore doc comment annotations?
     *
     * @return void
     */
    public function setWithoutAnnotations()
    {
        $this->withoutAnnotations = true;
    }

    /**
     * Adds a logger to this runner.
     *
     * @param string $generatorId The logger identifier.
     * @param string $reportFile The log file name.
     * @return void
     */
    public function addReportGenerator($generatorId, $reportFile)
    {
        $this->loggerMap[$generatorId] = $reportFile;
    }

    /**
     * Adds a logger or analyzer option.
     *
     * @param string $identifier The option identifier.
     * @param string|array $value The option value.
     * @return void
     */
    public function addOption($identifier, $value)
    {
        $this->options[$identifier] = $value;
    }

    /**
     * Adds a process listener instance that will be hooked into PDepend's
     * analyzing process.
     *
     * @param ProcessListener $processListener A process listener.
     * @return void
     */
    public function addProcessListener(ProcessListener $processListener)
    {
        $this->processListeners[] = $processListener;
    }

    /**
     * Starts the main PDepend process and returns <b>true</b> after a successful
     * execution.
     *
     * @return boolean
     * @throws \RuntimeException An exception with a readable error message and
     * an exit code.
     */
    public function run()
    {
        $pdepend = new Application($this->configuration);
        $pdepend->setOptions($this->options);

        if (count($this->extensions) > 0) {
            $filter = new ExtensionFilter($this->extensions);
            $pdepend->addFileFilter($filter);
        }

        if (count($this->excludeDirectories) > 0) {
            $exclude = $this->excludeDirectories;
            $filter  = new ExcludePathFilter($exclude);
            $pdepend->addFileFilter($filter);
        }

        if (count($this->excludePackages) > 0) {
            $exclude = $this->excludePackages;
            $filter  = new PackageArtifactFilter($exclude);
            $pdepend->setCodeFilter($filter);
        }

        if ($this->withoutAnnotations === true) {
            $pdepend->setWithoutAnnotations();
        }

        // Try to set all source directories.
        try {
            foreach ($this->sourceArguments as $sourceArgument) {
                if (is_file($sourceArgument)) {
                    $pdepend->addFile($sourceArgument);
                } else {
                    $pdepend->addDirectory($sourceArgument);
                }
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), self::EXCEPTION_EXIT);
        }

        if (count($this->loggerMap) === 0) {
            throw new \RuntimeException('No output specified.', self::EXCEPTION_EXIT);
        }

        $generatorFactory = new GeneratorFactory();

        // To append all registered loggers.
        try {
            foreach ($this->loggerMap as $generatorId => $reportFile) {
                // Create a new logger
                $generator = $generatorFactory->createGenerator($generatorId, $reportFile);

                $pdepend->addReportGenerator($generator);
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), self::EXCEPTION_EXIT);
        }

        foreach ($this->processListeners as $processListener) {
            $pdepend->addProcessListener($processListener);
        }

        try {
            $pdepend->analyze();

            foreach ($pdepend->getExceptions() as $exception) {
                $this->parseErrors[] = $exception->getMessage();
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), self::EXCEPTION_EXIT);
        }

        return self::SUCCESS_EXIT;
    }

    /**
     * This method will return <b>true</b> when there were errors during the
     * parse process.
     *
     * @return boolean
     */
    public function hasParseErrors()
    {
        return (count($this->parseErrors) > 0);
    }

    /**
     * This method will return an <b>array</b> with error messages for all
     * failures that happened during the parsing process.
     *
     * @return array(string)
     */
    public function getParseErrors()
    {
        return $this->parseErrors;
    }
}
