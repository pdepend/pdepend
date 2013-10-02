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
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
  */

namespace PHP\Depend;

use PHP\Depend\Metrics\AnalyzerClassFileSystemLocator;
use PHP\Depend\Metrics\AnalyzerFilterAware;
use PHP\Depend\Metrics\AnalyzerLoader;
use PHP\Depend\ProcessListener;
use PHP\Depend\Report\GeneratorCodeAware;
use PHP\Depend\Source\AST\ASTArtifactList\ArtifactFilter;
use PHP\Depend\Source\AST\ASTArtifactList\CollectionArtifactFilter;
use PHP\Depend\Source\AST\ASTArtifactList\NullArtifactFilter;
use PHP\Depend\Source\Builder\Builder;
use PHP\Depend\Source\Language\PHP\PHPBuilder;
use PHP\Depend\Source\Language\PHP\PHPParserGeneric;
use PHP\Depend\Source\Language\PHP\PHPTokenizerInternal;
use PHP\Depend\Source\Tokenizer\Tokenizer;
use PHP\Depend\Util\Configuration;
use PHP\Depend\TreeVisitor\TreeVisitor;

/**
 * PDepend analyzes php class files and generates metrics.
 *
 * The PDepend is a php port/adaption of the Java class file analyzer
 * <a href="http://clarkware.com/software/JDepend.html">JDepend</a>.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class Application
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
     * @var \PHP\Depend\Util\Configuration
     * @since 0.10.0
     */
    protected $configuration = null;

    /**
     * List of source directories.
     *
     * @var array(string)
     */
    private $directories = array();

    /**
     * List of source code file names.
     *
     * @var array(string)
     */
    private $files = array();

    /**
     * The used code node builder.
     *
     * @var \PHP\Depend\Source\Builder\Builder
     */
    private $builder = null;

    /**
     * Generated {@link \PHP\Depend\Source\AST\ASTNamespace} objects.
     *
     * @var Iterator
     */
    private $packages = null;

    /**
     * List of all registered {@link \PHP\Depend\Report\Generator} instances.
     *
     * @var \PHP\Depend\Report\Generator[]
     */
    private $generators = array();

    /**
     * A composite filter for input files.
     *
     * @var \PHP\Depend\Input\CompositeFilter
     */
    private $fileFilter = null;

    /**
     * A filter for source packages.
     *
     * @var \PHP\Depend\Source\AST\ASTArtifactList\ArtifactFilter
     */
    private $codeFilter = null;

    /**
     * Should the parse ignore doc comment annotations?
     *
     * @var boolean
     */
    private $withoutAnnotations = false;

    /**
     * List or registered listeners.
     *
     * @var ProcessListener[]
     */
    private $listeners = array();

    /**
     * List of analyzer options.
     *
     * @var array(string=>mixed)
     */
    private $options = array();

    /**
     * List of all {@link \PHP\Depend\Source\Parser\ParserException} that were caught during
     * the parsing process.
     *
     * @var \PHP\Depend\Source\Parser\ParserException[]
     */
    private $parseExceptions = array();

    /**
     * The configured cache factory.
     *
     * @var \PHP\Depend\Util\Cache\Factory
     * @since 1.0.0
     */
    private $cacheFactory;

    /**
     * Constructs a new php depend facade.
     *
     * @param \PHP\Depend\Util\Configuration $configuration The system configuration.
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->codeFilter = new NullArtifactFilter();
        $this->fileFilter = new \PHP\Depend\Input\CompositeFilter();

        $this->cacheFactory = new \PHP\Depend\Util\Cache\Factory($configuration);
    }

    /**
     * Adds the specified directory to the list of directories to be analyzed.
     *
     * @param string $directory The php source directory.
     * @return void
     */
    public function addDirectory($directory)
    {
        $dir = realpath($directory);

        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(
                "Invalid directory '{$directory}' added."
            );
        }

        $this->directories[] = $dir;
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
            throw new \InvalidArgumentException(
                sprintf('The given file "%s" does not exist.', $file)
            );
        }

        $this->files[] = $fileName;
    }

    /**
     * Adds a logger to the output list.
     *
     * @param \PHP\Depend\Report\Generator $generator The logger instance.
     * @return void
     */
    public function addReportGenerator(\PHP\Depend\Report\Generator $generator)
    {
        $this->generators[] = $generator;
    }

    /**
     * Adds a new input/file filter.
     *
     * @param \PHP\Depend\Input\Filter $filter New input/file filter instance.
     *
     * @return void
     */
    public function addFileFilter(\PHP\Depend\Input\Filter $filter)
    {
        $this->fileFilter->append($filter);
    }

    /**
     * Sets an additional code filter. These filters could be used to hide
     * external libraries and global stuff from the PDepend output.
     *
     * @param \PHP\Depend\Source\AST\ASTArtifactList\ArtifactFilter $filter
     *
     * @return void
     */
    public function setCodeFilter(ArtifactFilter $filter)
    {
        $this->codeFilter = $filter;
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
        $this->options = $options;
    }

    /**
     * Should the parse ignore doc comment annotations?
     *
     * @return void
     */
    public function setWithoutAnnotations()
    {
        $this->withoutAnnotations = true;
    }

    /**
     * Adds a process listener.
     *
     * @param ProcessListener $listener The listener instance.
     * @return void
     */
    public function addProcessListener(ProcessListener $listener)
    {
        if (in_array($listener, $this->listeners, true) === false) {
            $this->listeners[] = $listener;
        }
    }

    /**
     * Analyzes the registered directories and returns the collection of
     * analyzed packages.
     *
     * @return \PHP\Depend\Source\AST\ASTNamespace[]
     */
    public function analyze()
    {
        $this->builder = new PHPBuilder();

        $this->performParseProcess();

        // Get global filter collection
        $collection = CollectionArtifactFilter::getInstance();
        $collection->setFilter($this->codeFilter);

        $collection->setFilter();

        $this->performAnalyzeProcess();

        // Set global filter for logging
        $collection->setFilter($this->codeFilter);

        $packages = $this->builder->getPackages();

        $this->fireStartLogProcess();

        foreach ($this->generators as $generator) {
            // Check for code aware loggers
            if ($generator instanceof GeneratorCodeAware) {
                $generator->setArtifacts($packages);
            }
            $generator->close();
        }

        $this->fireEndLogProcess();

        return ($this->packages = $packages);
    }

    /**
     * Returns the number of analyzed php classes and interfaces.
     *
     * @return integer
     */
    public function countClasses()
    {
        if ($this->packages === null) {
            $msg = 'countClasses() doesn\'t work before the source was analyzed.';
            throw new \RuntimeException($msg);
        }

        $classes = 0;
        foreach ($this->packages as $package) {
            $classes += $package->getTypes()->count();
        }
        return $classes;
    }

    /**
     * Returns an <b>array</b> with all {@link \PHP\Depend\Source\Parser\ParserException}
     * that were caught during the parsing process.
     *
     * @return \PHP\Depend\Source\Parser\ParserException[]
     */
    public function getExceptions()
    {
        return $this->parseExceptions;
    }

    /**
     *  Returns the number of analyzed packages.
     *
     * @return integer
     */
    public function countPackages()
    {
        if ($this->packages === null) {
            $msg = 'countPackages() doesn\'t work before the source was analyzed.';
            throw new \RuntimeException($msg);
        }

        $count = 0;
        foreach ($this->packages as $package) {
            if ($package->isUserDefined()) {
                ++$count;
            }
        }
        return $count;
    }

    /**
     * Returns the analyzed package of the specified name.
     *
     * @param string $name
     * @return \PHP\Depend\Source\AST\ASTNamespace
     * @throws \OutOfBoundsException
     */
    public function getPackage($name)
    {
        if ($this->packages === null) {
            $msg = 'getPackage() doesn\'t work before the source was analyzed.';
            throw new \RuntimeException($msg);
        }
        foreach ($this->packages as $package) {
            if ($package->getName() === $name) {
                return $package;
            }
        }
        throw new \OutOfBoundsException(sprintf('Unknown package "%s".', $name));
    }

    /**
     * Returns an iterator of the analyzed packages.
     *
     * @return Iterator
     */
    public function getPackages()
    {
        if ($this->packages === null) {
            $msg = 'getPackages() doesn\'t work before the source was analyzed.';
            throw new \RuntimeException($msg);
        }
        return $this->packages;
    }

    /**
     * Send the start parsing process event.
     *
     * @param \PHP\Depend\Source\Builder\Builder $builder The used node builder instance.
     * @return void
     */
    protected function fireStartParseProcess(Builder $builder)
    {
        foreach ($this->listeners as $listener) {
            $listener->startParseProcess($builder);
        }
    }

    /**
     * Send the end parsing process event.
     *
     * @param \PHP\Depend\Source\Builder\Builder $builder The used node builder instance.
     * @return void
     */
    protected function fireEndParseProcess(Builder $builder)
    {
        foreach ($this->listeners as $listener) {
            $listener->endParseProcess($builder);
        }
    }

    /**
     * Sends the start file parsing event.
     *
     * @param \PHP\Depend\Source\Tokenizer\Tokenizer $tokenizer
     * @return void
     */
    protected function fireStartFileParsing(Tokenizer $tokenizer)
    {
        foreach ($this->listeners as $listener) {
            $listener->startFileParsing($tokenizer);
        }
    }

    /**
     * Sends the end file parsing event.
     *
     * @param \PHP\Depend\Source\Tokenizer\Tokenizer $tokenizer
     * @return void
     */
    protected function fireEndFileParsing(Tokenizer $tokenizer)
    {
        foreach ($this->listeners as $listener) {
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
        foreach ($this->listeners as $listener) {
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
        foreach ($this->listeners as $listener) {
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
        foreach ($this->listeners as $listener) {
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
        foreach ($this->listeners as $listener) {
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
    private function performParseProcess()
    {
        // Reset list of thrown exceptions
        $this->parseExceptions = array();

        $tokenizer = new PHPTokenizerInternal();

        $this->fireStartParseProcess($this->builder);

        foreach ($this->createFileIterator() as $file) {
            $tokenizer->setSourceFile($file);

            $parser = new PHPParserGeneric(
                $tokenizer,
                $this->builder,
                $this->cacheFactory->create()
            );
            $parser->setMaxNestingLevel($this->configuration->parser->nesting);

            // Disable annotation parsing?
            if ($this->withoutAnnotations === true) {
                $parser->setIgnoreAnnotations();
            }

            $this->fireStartFileParsing($tokenizer);

            try {
                $parser->parse();
            } catch (\PHP\Depend\Source\Parser\ParserException $e) {
                $this->parseExceptions[] = $e;
            }
            $this->fireEndFileParsing($tokenizer);
        }

        $this->fireEndParseProcess($this->builder);
    }

    /**
     * This method performs the analysing process of the parsed source files. It
     * creates the required analyzers for the registered listeners and then
     * applies them to the source tree.
     *
     * @return void
     */
    private function performAnalyzeProcess()
    {
        $analyzerLoader = $this->createAnalyzerLoader($this->options);

        $collection = CollectionArtifactFilter::getInstance();

        $this->fireStartAnalyzeProcess();

        ini_set('xdebug.max_nesting_level', $this->configuration->parser->nesting);

        foreach ($analyzerLoader as $analyzer) {
            // Add filters if this analyzer is filter aware
            if ($analyzer instanceof AnalyzerFilterAware) {
                $collection->setFilter($this->codeFilter);
            }

            $analyzer->analyze($this->builder->getPackages());

            // Remove filters if this analyzer is filter aware
            $collection->setFilter();

            foreach ($this->generators as $logger) {
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
     * @param \PHP\Depend\Metrics\AnalyzerLoader $analyzerLoader
     * @return \PHP\Depend\Metrics\AnalyzerLoader
     */
    private function initAnalyseListeners(AnalyzerLoader $analyzerLoader)
    {
        // Append all listeners
        foreach ($analyzerLoader as $analyzer) {
            foreach ($this->listeners as $listener) {
                $analyzer->addAnalyzeListener($listener);

                if ($analyzer instanceof TreeVisitor) {
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
    private function createFileIterator()
    {
        if (count($this->directories) === 0 && count($this->files) === 0) {
            throw new \RuntimeException('No source directory and file set.');
        }

        $fileIterator = new \AppendIterator();
        $fileIterator->append(new \ArrayIterator($this->files));

        foreach ($this->directories as $directory) {
            $fileIterator->append(
                new \PHP\Depend\Input\Iterator(
                    new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($directory . '/')
                    ),
                    $this->fileFilter,
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

        return new \ArrayIterator(array_values($files));
    }

    /**
     * Creates a {@link \PHP\Depend\Metrics\AnalyzerLoader} instance that will be
     * used to create all analyzers required for the actually registered logger
     * instances.
     *
     * @param array $options The command line options recieved for this run.
     * @return \PHP\Depend\Metrics\AnalyzerLoader
     */
    private function createAnalyzerLoader(array $options)
    {
        $analyzerSet = array();

        foreach ($this->generators as $logger) {
            foreach ($logger->getAcceptedAnalyzers() as $type) {
                // Check for type existence
                if (in_array($type, $analyzerSet) === false) {
                    $analyzerSet[] = $type;
                }
            }
        }

        $cacheKey = md5(serialize($this->files) . serialize($this->directories));

        $loader = new AnalyzerLoader(
            new AnalyzerClassFileSystemLocator(),
            $this->cacheFactory->create($cacheKey),
            $analyzerSet,
            $options
        );

        return $this->initAnalyseListeners($loader);
    }
}
