<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Parser.php';
require_once 'PHP/Depend/StorageRegistry.php';
require_once 'PHP/Depend/VisitorI.php';
require_once 'PHP/Depend/Builder/Default.php';
require_once 'PHP/Depend/Code/Filter/Composite.php';
require_once 'PHP/Depend/Code/Filter/DefaultPackage.php';
require_once 'PHP/Depend/Code/Filter/InternalPackage.php';
require_once 'PHP/Depend/Metrics/AnalyzerLoader.php';
require_once 'PHP/Depend/Metrics/Dependency/Analyzer.php';
require_once 'PHP/Depend/Tokenizer/CacheDecorator.php';
require_once 'PHP/Depend/Tokenizer/Internal.php';
require_once 'PHP/Depend/Input/CompositeFilter.php';
require_once 'PHP/Depend/Input/Iterator.php';

/**
 * PHP_Depend analyzes php class files and generates metrics.
 *
 * The PHP_Depend is a php port/adaption of the Java class file analyzer
 * <a href="http://clarkware.com/software/JDepend.html">JDepend</a>.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend
{
    /**
     * Marks the storage used for runtime tokens.
     */
    const TOKEN_STORAGE = 1;

    /**
     * Marks the storag engine used for parser artifacts.
     */
    const PARSER_STORAGE = 2;

    /**
     * List of source directories.
     *
     * @var array(string) $_directories
     */
    private $_directories = array();

    /**
     * List of source code file names.
     *
     * @var array(string) $_files
     */
    private $_files = array();

    /**
     * The used code node builder.
     *
     * @var PHP_Depend_BuilderI $_builder
     */
    private $_builder = null;

    /**
     * Generated {@link PHP_Depend_Code_Package} objects.
     *
     * @var Iterator $_packages
     */
    private $_packages = null;

    /**
     * List of all registered {@link PHP_Depend_Log_LoggerI} instances.
     *
     * @var array(PHP_Depend_Log_LoggerI) $_loggers
     */
    private $_loggers = array();

    /**
     * A composite filter for input files.
     *
     * @var PHP_Depend_Input_CompositeFilter $_fileFilter
     */
    private $_fileFilter = null;

    /**
     * A composite filter for source packages.
     *
     * @var PHP_Depend_Code_Filter_Composite $_codeFilter
     */
    private $_codeFilter = null;

    /**
     * Should the parse ignore doc comment annotations?
     *
     * @var boolean $_withoutAnnotations
     */
    private $_withoutAnnotations = false;

    /**
     * Should PHP_Depend treat <b>+global</b> as a regular project package?
     *
     * @var boolean $_supportBadDocumentation
     */
    private $_supportBadDocumentation = false;

    /**
     * List or registered listeners.
     *
     * @var array(PHP_Depend_ProcessListenerI) $_listeners
     */
    private $_listeners = array();

    /**
     * List of analyzer options.
     *
     * @var array(string=>mixed) $_options
     */
    private $_options = array();

    /**
     * Configured storage engines.
     *
     * @var array(PHP_Depend_Storage_AbstractEngine) $_storages
     */
    private $_storages = array(
        self::TOKEN_STORAGE   =>  null,
        self::PARSER_STORAGE  =>  null,
    );

    /**
     * Constructs a new php depend facade.
     */
    public function __construct()
    {
        $this->_codeFilter = new PHP_Depend_Code_Filter_Composite();
        $this->_fileFilter = new PHP_Depend_Input_CompositeFilter();
    }

    /**
     * Adds the specified directory to the list of directories to be analyzed.
     *
     * @param string $directory The php source directory.
     *
     * @return void
     */
    public function addDirectory($directory)
    {
        $dir = realpath($directory);

        if (!is_dir($dir)) {
            throw new InvalidArgumentException("Invalid directory '{$directory}' added.");
        }

        $this->_directories[] = $dir;
    }

    /**
     * Adds a single source code file to the list of files to be analysed.
     *
     * @param string $file The source file name.
     *
     * @return void
     */
    public function addFile($file)
    {
        $fileName = realpath($file);

        if (!is_file($fileName)) {
            throw new InvalidArgumentException("Invalid file '{$file}' added.");
        }

        $this->_files[] = $fileName;
    }

    /**
     * Adds a logger to the output list.
     *
     * @param PHP_Depend_Log_LoggerI $logger The logger instance.
     *
     * @return void
     */
    public function addLogger(PHP_Depend_Log_LoggerI $logger)
    {
        $this->_loggers[] = $logger;
    }

    /**
     * Adds a new input/file filter.
     *
     * @param PHP_Depend_Input_FilterI $filter New input/file filter instance.
     *
     * @return void
     */
    public function addFileFilter(PHP_Depend_Input_FilterI $filter)
    {
        $this->_fileFilter->append($filter);
    }

    /**
     * Sets a storage instance for a special usage.
     *
     * @param integer                           $type   The target identifier.
     * @param PHP_Depend_Storage_AbstractEngine $engine The storage instance.
     *
     * @return void
     */
    public function setStorage($type, PHP_Depend_Storage_AbstractEngine $engine)
    {
        switch ($type) {
        case self::TOKEN_STORAGE:
            $engine->setPrune();
            break;

        case self::PARSER_STORAGE:
            $engine->getMaxLifetime(86400);
            $engine->setProbability(5);
            break;

        default:
            $message = sprintf('Unknown storage identifier "%s" given.', $type);
            throw new InvalidArgumentException($message);
        }

        $this->_storages[$type] = $engine;
    }

    /**
     * Adds an additional code filter. These filters could be used to hide
     * external libraries and global stuff from the PDepend output.
     *
     * @param PHP_Depend_Code_FilterI $filter The code filter.
     *
     * @return void
     */
    public function addCodeFilter(PHP_Depend_Code_FilterI $filter)
    {
        $this->_codeFilter->addFilter($filter);
    }

    /**
     * Sets analyzer options.
     *
     * @param array(string=>mixed) $options The analyzer options.
     *
     * @return void
     */
    public function setOptions(array $options = array())
    {
        $this->_options = $options;
    }

    /**
     * Should the parse ignore doc comment annotations?
     *
     * @return void
     */
    public function setWithoutAnnotations()
    {
        $this->_withoutAnnotations = true;
    }

    /**
     * Should PHP_Depend support projects with a bad documentation. If this
     * option is set to <b>true</b>, PHP_Depend will treat the default package
     * <b>+global</b> as a regular project package.
     *
     * @return void
     */
    public function setSupportBadDocumentation()
    {
        $this->_supportBadDocumentation = true;
    }

    /**
     * Adds a process listener.
     *
     * @param PHP_Depend_ProcessListenerI $listener The listener instance.
     *
     * @return void
     */
    public function addProcessListener(PHP_Depend_ProcessListenerI $listener)
    {
        if (in_array($listener, $this->_listeners, true) === false) {
            $this->_listeners[] = $listener;
        }
    }

    /**
     * Analyzes the registered directories and returns the collection of
     * analyzed packages.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function analyze()
    {
        if (count($this->_directories) === 0 && count($this->_files) === 0) {
            throw new RuntimeException('No source directory and file set.');
        }

        $this->_initStorages();

        $accept = $this->_createAnalyzerList();
        $loader = new PHP_Depend_Metrics_AnalyzerLoader($accept, $this->_options);

        $iterator = new AppendIterator();
        $iterator->append(new ArrayIterator($this->_files));

        foreach ($this->_directories as $directory) {
            $iterator->append(new PHP_Depend_Input_Iterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($directory)
                ), $this->_fileFilter
            ));
        }

        $this->_builder = new PHP_Depend_Builder_Default();

        $tokenizer = new PHP_Depend_Tokenizer_Internal();
        $tokenizer = new PHP_Depend_Tokenizer_CacheDecorator($tokenizer);

        $this->fireStartParseProcess($this->_builder);

        foreach ($iterator as $file) {
            $tokenizer->setSourceFile($file);

            $parser = new PHP_Depend_Parser($tokenizer, $this->_builder);

            // Disable annotation parsing?
            if ($this->_withoutAnnotations === true) {
                $parser->setIgnoreAnnotations();
            }

            $this->fireStartFileParsing($tokenizer);
            $parser->parse();
            $this->fireEndFileParsing($tokenizer);
        }

        $this->fireEndParseProcess($this->_builder);

        // Initialize defaul filters
        if ($this->_supportBadDocumentation === false) {
            $filter = new PHP_Depend_Code_Filter_DefaultPackage();
            $this->_codeFilter->addFilter($filter);
        }

        $filter = new PHP_Depend_Code_Filter_InternalPackage();
        $this->_codeFilter->addFilter($filter);

        // Get global filter collection
        $collection = PHP_Depend_Code_Filter_Collection::getInstance();
        $collection->addFilter($this->_codeFilter);

        if ($this->_builder->getPackages()->count() === 0) {
            $message = "The parser doesn't detect package informations "
                     . "within the analyzed project, please check the "
                     . "documentation blocks for @package-annotations or use "
                     . "the --bad-documentation option.";

            throw new RuntimeException($message);
        }

        $collection->removeFilter($this->_codeFilter);

        // Append all listeners
        foreach ($loader as $analyzer) {
            foreach ($this->_listeners as $listener) {
                $analyzer->addAnalyzeListener($listener);

                if ($analyzer instanceof PHP_Depend_VisitorI) {
                    $analyzer->addVisitListener($listener);
                }
            }
        }

        $this->fireStartAnalyzeProcess();

        foreach ($loader as $analyzer) {
            // Add filters if this analyzer is filter aware
            if ($analyzer instanceof PHP_Depend_Metrics_FilterAwareI) {
                $collection->addFilter($this->_codeFilter);
            }

            $analyzer->analyze($this->_builder->getPackages());

            // Remove filters if this analyzer is filter aware
            if ($analyzer instanceof PHP_Depend_Metrics_FilterAwareI) {
                $collection->removeFilter($this->_codeFilter);
            }

            foreach ($this->_loggers as $logger) {
                $logger->log($analyzer);
            }
        }

        $this->fireEndAnalyzeProcess();

        // Set global filter for logging
        $collection->addFilter($this->_codeFilter);

        $packages = $this->_builder->getPackages();

        $this->fireStartLogProcess();

        foreach ($this->_loggers as $logger) {
            // Check for code aware loggers
            if ($logger instanceof PHP_Depend_Log_CodeAwareI) {
                $logger->setCode($packages);
            }
            $logger->close();
        }

        $this->fireEndLogProcess();

        // Remove global filter
        // $collection->removeFilter($this->_codeFilter);

        $this->_packages = $packages;

        return $packages;
    }

    /**
     * Returns the number of analyzed php classes and interfaces.
     *
     * @return integer
     */
    public function countClasses()
    {
        if ($this->_packages === null) {
            $msg = 'countClasses() doesn\'t work before the source was analyzed.';
            throw new RuntimeException($msg);
        }

        $classes = 0;
        foreach ($this->_packages as $package) {
            $classes += $package->getTypes()->count();
        }
        return $classes;
    }

    /**
     *  Returns the number of analyzed packages.
     *
     * @return integer
     */
    public function countPackages()
    {
        if ($this->_packages === null) {
            $msg = 'countPackages() doesn\'t work before the source was analyzed.';
            throw new RuntimeException($msg);
        }
        // TODO: This is internal knownhow, it is an ArrayIterator
        //       Replace it with a custom iterator interface
        return $this->_packages->count();
    }

    /**
     * Returns the analyzed package of the specified name.
     *
     * @param string $name The package name.
     *
     * @return PHP_Depend_Code_Package
     */
    public function getPackage($name)
    {
        if ($this->_packages === null) {
            $msg = 'getPackage() doesn\'t work before the source was analyzed.';
            throw new RuntimeException($msg);
        }
        foreach ($this->_packages as $package) {
            if ($package->getName() === $name) {
                return $package;
            }
        }
        throw new OutOfBoundsException(sprintf('Unknown package "%s".', $name));
    }

    /**
     * Returns an iterator of the analyzed packages.
     *
     * @return Iterator
     */
    public function getPackages()
    {
        if ($this->_packages === null) {
            $msg = 'getPackages() doesn\'t work before the source was analyzed.';
            throw new RuntimeException($msg);
        }
        return $this->_packages;
    }

    /**
     * Send the start parsing process event.
     *
     * @param PHP_Depend_BuilderI $builder The used node builder instance.
     *
     * @return void
     */
    protected function fireStartParseProcess(PHP_Depend_BuilderI $builder)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startParseProcess($builder);
        }
    }

    /**
     * Send the end parsing process event.
     *
     * @param PHP_Depend_BuilderI $builder The used node builder instance.
     *
     * @return void
     */
    protected function fireEndParseProcess(PHP_Depend_BuilderI $builder)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endParseProcess($builder);
        }
    }

    /**
     * Sends the start file parsing event.
     *
     * @param PHP_Depend_TokenizerI $tokenizer The used tokenizer instance.
     *
     * @return void
     */
    protected function fireStartFileParsing(PHP_Depend_TokenizerI $tokenizer)
    {
        foreach ($this->_listeners as $listener) {
            $listener->startFileParsing($tokenizer);
        }
    }

    /**
     * Sends the end file parsing event.
     *
     * @param PHP_Depend_TokenizerI $tokenizer The used tokenizer instance.
     *
     * @return void
     */
    protected function fireEndFileParsing(PHP_Depend_TokenizerI $tokenizer)
    {
        foreach ($this->_listeners as $listener) {
            $listener->endFileParsing($tokenizer);
        }
    }

    /**
     * Sends the start analyzing process event.
     *
     * @return void
     */
    protected function fireStartAnalyzeProcess()
    {
        foreach ($this->_listeners as $listener) {
            $listener->startAnalyzeProcess();
        }
    }

    /**
     * Sends the end analyzing process event.
     *
     * @return void
     */
    protected function fireEndAnalyzeProcess()
    {
        foreach ($this->_listeners as $listener) {
            $listener->endAnalyzeProcess();
        }
    }

    /**
     * Sends the start log process event.
     *
     * @return void
     */
    protected function fireStartLogProcess()
    {
        foreach ($this->_listeners as $listener) {
            $listener->startLogProcess();
        }
    }

    /**
     * Sends the end log process event.
     *
     * @return void
     */
    protected function fireEndLogProcess()
    {
        foreach ($this->_listeners as $listener) {
            $listener->endLogProcess();
        }
    }

    /**
     * This method initializes the storage strategies for node tokens that are
     * used during a single PHP_Depend run and the parser cache storage.
     *
     * @return void
     */
    private function _initStorages()
    {
        foreach ($this->_storages as $identifier => $storage) {
            // Fallback for unconfigured storage engines
            if ($storage === null) {
                // Include memory storage class definition
                include_once 'PHP/Depend/Storage/MemoryEngine.php';

                $storage = new PHP_Depend_Storage_MemoryEngine();
            }

            PHP_Depend_StorageRegistry::set($identifier, $storage);
        }
    }

    /**
     * Creates an <b>array</b> with all expected analyzers.
     *
     * @return array(string)
     */
    private function _createAnalyzerList()
    {
        $resultSets = array();

        foreach ($this->_loggers as $logger) {
            foreach ($logger->getAcceptedAnalyzers() as $type) {
                // Check for type existence
                if (!in_array($type, $resultSets)) {
                    $resultSets[] = $type;
                }
            }
        }
        return $resultSets;
    }
}