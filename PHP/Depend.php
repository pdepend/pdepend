<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

/**
 * PHP_Depend analyzes php class files and generates metrics.
 *
 * The PHP_Depend is a php port/adaption of the Java class file analyzer
 * <a href="http://clarkware.com/software/JDepend.html">JDepend</a>.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
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
     * The system configuration.
     *
     * @var PHP_Depend_Util_Configuration
     * @since 0.10.0
     */
    protected $configuration = null;

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
     * A filter for source packages.
     *
     * @var PHP_Depend_Code_FilterI $_codeFilter
     */
    private $_codeFilter = null;

    /**
     * Should the parse ignore doc comment annotations?
     *
     * @var boolean $_withoutAnnotations
     */
    private $_withoutAnnotations = false;

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
     * List of all {@link PHP_Depend_Parser_Exception} that were caught during
     * the parsing process.
     *
     * @var array(PHP_Depend_Parser_Exception) $_parseExceptions
     */
    private $_parseExceptions = array();

    /**
     * Constructs a new php depend facade.
     *
     * @param PHP_Depend_Util_Configuration $configuration The system configuration.
     */
    public function __construct(PHP_Depend_Util_Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->_codeFilter = new PHP_Depend_Code_Filter_Null();
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
            throw new InvalidArgumentException(
                "Invalid directory '{$directory}' added."
            );
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
            throw new InvalidArgumentException(
                sprintf('The given file "%s" does not exist.', $file)
            );
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
     * Sets an additional code filter. These filters could be used to hide
     * external libraries and global stuff from the PDepend output.
     *
     * @param PHP_Depend_Code_FilterI $filter The code filter.
     *
     * @return void
     */
    public function setCodeFilter(PHP_Depend_Code_FilterI $filter)
    {
        $this->_codeFilter = $filter;
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
        $this->_builder = new PHP_Depend_Builder_Default();

        $this->_performParseProcess();

        // Get global filter collection
        $collection = PHP_Depend_Code_Filter_Collection::getInstance();
        $collection->setFilter($this->_codeFilter);

        if ($this->_builder->getPackages()->count() === 0) {
            return ($this->_packages = $this->_builder->getPackages());
        }

        $collection->setFilter();

        $this->_performAnalyzeProcess();

        // Set global filter for logging
        $collection->setFilter($this->_codeFilter);

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

        return ($this->_packages = $packages);
    }

    /**
     * Helper method for PHP version < 5.3, this method can be used to
     * unwire the complex object graph created by PHP_Depend, so that the
     * garbage collector can free memory consumed by PHP_Depend. Please
     * remember that this method will destroy all the data calculated by
     * PHP_Depend, so it is unusable after a call to <b>free()</b>.
     *
     * @return void
     * @since 0.9.12
     */
    public function free()
    {
        if (is_object($this->_packages)) {
            $this->_packages->free();
        }
        unset($this->_builder, $this->_packages);
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
     * Returns an <b>array</b> with all {@link PHP_Depend_Parser_Exception} that
     * were caught during the parsing process.
     *
     * @return array(PHP_Depend_Parser_Exception)
     */
    public function getExceptions()
    {
        return $this->_parseExceptions;
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

        $count = 0;
        foreach ($this->_packages as $package) {
            if ($package->isUserDefined()) {
                ++$count;
            }
        }
        return $count;
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
     * This method performs the parsing process of all source files. It expects
     * that the <b>$_builder</b> property was initialized with a concrete builder
     * implementation.
     *
     * @return void
     */
    private function _performParseProcess()
    {
        // Reset list of thrown exceptions
        $this->_parseExceptions = array();

        // Create a cache instance
        $cacheFactory = new PHP_Depend_Util_Cache_Factory($this->configuration);
        $cache = $cacheFactory->create();

        $tokenizer = new PHP_Depend_Tokenizer_Internal();

        $this->fireStartParseProcess($this->_builder);

        foreach ($this->_createFileIterator() as $file) {
            $tokenizer->setSourceFile($file);

            $parser = new PHP_Depend_Parser_VersionAllParser(
                $tokenizer,
                $this->_builder,
                $cache
            );
            $parser->setMaxNestingLevel(1024);

            // Disable annotation parsing?
            if ($this->_withoutAnnotations === true) {
                $parser->setIgnoreAnnotations();
            }

            $this->fireStartFileParsing($tokenizer);

            try {
                $parser->parse();
            } catch (PHP_Depend_Parser_Exception $e) {
                $this->_parseExceptions[] = $e;
            }
            $this->fireEndFileParsing($tokenizer);
        }

        $this->fireEndParseProcess($this->_builder);
    }

    /**
     * This method performs the analysing process of the parsed source files. It
     * creates the required analyzers for the registered listeners and then
     * applies them to the source tree.
     *
     * @return void
     */
    private function _performAnalyzeProcess()
    {
        $analyzerLoader = $this->_createAnalyzerLoader($this->_options);

        $collection = PHP_Depend_Code_Filter_Collection::getInstance();

        $this->fireStartAnalyzeProcess();

        ini_set('xdebug.max_nesting_level', 1024);

        foreach ($analyzerLoader as $analyzer) {
            // Add filters if this analyzer is filter aware
            if ($analyzer instanceof PHP_Depend_Metrics_FilterAwareI) {
                $collection->setFilter($this->_codeFilter);
            }

            $analyzer->analyze($this->_builder->getPackages());

            // Remove filters if this analyzer is filter aware
            $collection->setFilter();

            foreach ($this->_loggers as $logger) {
                $logger->log($analyzer);
            }
        }

        ini_restore('xdebug.max_nesting_level');

        $this->fireEndAnalyzeProcess();
    }

    /**
     * This method will initialize all code analysers and register the
     * interested listeners.
     *
     * @param PHP_Depend_Metrics_AnalyzerLoader $analyzerLoader The used loader
     *        instance for all code analysers.
     *
     * @return PHP_Depend_Metrics_AnalyzerLoader
     */
    private function _initAnalyseListeners(
        PHP_Depend_Metrics_AnalyzerLoader $analyzerLoader
    ) {
        // Append all listeners
        foreach ($analyzerLoader as $analyzer) {
            foreach ($this->_listeners as $listener) {
                $analyzer->addAnalyzeListener($listener);

                if ($analyzer instanceof PHP_Depend_VisitorI) {
                    $analyzer->addVisitListener($listener);
                }
            }
        }
        
        return $analyzerLoader;
    }

    /**
     * This method will create an iterator instance which contains all files
     * that are part of the parsing process.
     *
     * @return Iterator
     */
    private function _createFileIterator()
    {
        if (count($this->_directories) === 0 && count($this->_files) === 0) {
            throw new RuntimeException('No source directory and file set.');
        }

        $fileIterator = new AppendIterator();
        $fileIterator->append(new ArrayIterator($this->_files));

        foreach ($this->_directories as $directory) {
            $fileIterator->append(
                new PHP_Depend_Input_Iterator(
                    new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($directory . '/')
                    ),
                    $this->_fileFilter,
                    $directory
                )
            );
        }

        // TODO: It's important to validate this behavior, imho there is something
        //       wrong in the iterator code used above.
        // Strange: why is the iterator not unique and why does this loop fix it?
        $files = array();
        foreach ($fileIterator as $file) {
            if (is_string($file)) {
                $files[$file] = $file;
            } else {
                $pathname         = realpath($file->getPathname());
                $files[$pathname] = $pathname;
            }
        }

        ksort($files);
        // END

        return new ArrayIterator(array_values($files));
    }

    /**
     * Creates a {@link PHP_Depend_Metrics_AnalyzerLoader} instance that will be
     * used to create all analyzers required for the actually registered logger
     * instances.
     *
     * @param array $options The command line options recieved for this run.
     *
     * @return PHP_Depend_Metrics_AnalyzerLoader
     */
    private function _createAnalyzerLoader(array $options)
    {
        $analyzerSet = array();

        foreach ($this->_loggers as $logger) {
            foreach ($logger->getAcceptedAnalyzers() as $type) {
                // Check for type existence
                if (in_array($type, $analyzerSet) === false) {
                    $analyzerSet[] = $type;
                }
            }
        }

        $loader = new PHP_Depend_Metrics_AnalyzerLoader($analyzerSet, $options);
        $loader->setClassLocator(
            new PHP_Depend_Metrics_AnalyzerClassFileSystemLocator()
        );

        return $this->_initAnalyseListeners($loader);
    }
}