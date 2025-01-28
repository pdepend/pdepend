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

namespace PDepend;

use AppendIterator;
use ArrayIterator;
use GlobIterator;
use InvalidArgumentException;
use OutOfBoundsException;
use PDepend\Input\CompositeFilter;
use PDepend\Input\Filter;
use PDepend\Input\Iterator;
use PDepend\Metrics\Analyzer;
use PDepend\Metrics\AnalyzerCacheAware;
use PDepend\Metrics\AnalyzerFactory;
use PDepend\Metrics\AnalyzerFilterAware;
use PDepend\Report\CodeAwareGenerator;
use PDepend\Report\ReportGenerator;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTArtifactList\ArtifactFilter;
use PDepend\Source\AST\ASTArtifactList\CollectionArtifactFilter;
use PDepend\Source\AST\ASTArtifactList\NullArtifactFilter;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\ASTVisitor\ASTVisitor;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Source\Parser\ParserException;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Util\Cache\CacheFactory;
use PDepend\Util\Configuration;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use SplFileObject;
use stdClass;

/**
 * PDepend analyzes php class files and generates metrics.
 *
 * The PDepend is a php port/adaption of the Java class file analyzer
 * <a href="http://clarkware.com/software/JDepend.html">JDepend</a>.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Engine
{
    /** Prefix for PHP streams. */
    protected string $phpStreamPrefix = 'php://';

    /**
     * List of source directories.
     *
     * @var array<string>
     */
    private array $directories = [];

    /**
     * List of source code file names.
     *
     * @var array<string>
     */
    private array $files = [];

    /**
     * The used code node builder.
     *
     * @var PHPBuilder<ASTNamespace>
     */
    private PHPBuilder $builder;

    /**
     * Generated {@link ASTNamespace} objects.
     *
     * @var ASTArtifactList<ASTNamespace>
     */
    private ASTArtifactList $namespaces;

    /**
     * List of all registered {@link ReportGenerator} instances.
     *
     * @var ReportGenerator[]
     */
    private array $generators = [];

    /** A composite filter for input files. */
    private readonly CompositeFilter $fileFilter;

    /** A filter for namespace. */
    private ArtifactFilter $codeFilter;

    /** Should the parse ignore doc comment annotations? */
    private bool $withoutAnnotations = false;

    /**
     * List or registered listeners.
     *
     * @var ProcessListener[]
     */
    private array $listeners = [];

    /**
     * List of analyzer options.
     *
     * @var array<string, array<int, string>|string>
     */
    private array $options = [];

    /**
     * List of all {@link ParserException} that were caught during
     * the parsing process.
     *
     * @var ParserException[]
     */
    private array $parseExceptions = [];

    /**
     * Constructs a new php depend facade.
     *
     * @param Configuration $configuration The system configuration.
     * @param CacheFactory $cacheFactory The configured cache factory.
     */
    public function __construct(
        protected Configuration $configuration,
        private readonly CacheFactory $cacheFactory,
        private readonly AnalyzerFactory $analyzerFactory,
    ) {
        $this->codeFilter = new NullArtifactFilter();
        $this->fileFilter = new CompositeFilter();
    }

    /**
     * Adds the specified directory to the list of directories to be analyzed.
     *
     * @param string $directory The php source directory.
     * @throws InvalidArgumentException
     */
    public function addDirectory(string $directory): void
    {
        $dir = realpath($directory);

        if ($dir === false || !is_dir($dir)) {
            throw new InvalidArgumentException("Invalid directory '{$directory}' added.");
        }

        $this->directories[] = $dir;
    }

    /**
     * Adds a single source code file to the list of files to be analysed.
     *
     * @param string $file The source file name.
     * @throws InvalidArgumentException
     */
    public function addFile(string $file): void
    {
        if ($file === '-') {
            $file = $this->phpStreamPrefix . 'stdin';
        }

        if ($this->isPhpStream($file)) {
            $this->files[] = $file;

            return;
        }

        $realPath = realpath($file);
        if (!$realPath || !is_file($file)) {
            throw new InvalidArgumentException(sprintf('The given file "%s" does not exist.', $file));
        }

        $this->files[] = $realPath;
    }

    /**
     * Adds a logger to the output list.
     *
     * @param ReportGenerator $generator The logger instance.
     */
    public function addReportGenerator(ReportGenerator $generator): void
    {
        $this->generators[] = $generator;
    }

    /**
     * Adds a new input/file filter.
     *
     * @param Filter $filter New input/file filter instance.
     */
    public function addFileFilter(Filter $filter): void
    {
        $this->fileFilter->append($filter);
    }

    /**
     * Sets an additional code filter. These filters could be used to hide
     * external libraries and global stuff from the PDepend output.
     */
    public function setCodeFilter(ArtifactFilter $filter): void
    {
        $this->codeFilter = $filter;
    }

    /**
     * Sets analyzer options.
     *
     * @param array<string, array<int, string>|string> $options The analyzer options.
     */
    public function setOptions(array $options = []): void
    {
        $this->options = $options;
    }

    /**
     * Should the parse ignore doc comment annotations?
     */
    public function setWithoutAnnotations(): void
    {
        $this->withoutAnnotations = true;
    }

    /**
     * Adds a process listener.
     *
     * @param ProcessListener $listener The listener instance.
     */
    public function addProcessListener(ProcessListener $listener): void
    {
        if (!in_array($listener, $this->listeners, true)) {
            $this->listeners[] = $listener;
        }
    }

    /**
     * Analyzes the registered directories and returns the collection of
     * analyzed namespace.
     *
     * @return ASTArtifactList<ASTNamespace>
     * @throws InvalidArgumentException
     */
    public function analyze(): ASTArtifactList
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

        $namespaces = $this->builder->getNamespaces();

        $this->fireStartLogProcess();

        foreach ($this->generators as $generator) {
            // Check for code aware loggers
            if ($generator instanceof CodeAwareGenerator) {
                $generator->setArtifacts($namespaces);
            }
            $generator->close();
        }

        $this->fireEndLogProcess();

        return ($this->namespaces = $namespaces);
    }

    /**
     * Returns the number of analyzed php classes and interfaces.
     *
     * @throws RuntimeException
     */
    public function countClasses(): int
    {
        if (!isset($this->namespaces)) {
            $msg = 'countClasses() doesn\'t work before the source was analyzed.';

            throw new RuntimeException($msg);
        }

        $classes = 0;
        foreach ($this->namespaces as $namespace) {
            $classes += count($namespace->getTypes());
        }

        return $classes;
    }

    /**
     * Returns an <b>array</b> with all {@link ParserException}
     * that were caught during the parsing process.
     *
     * @return ParserException[]
     */
    public function getExceptions(): array
    {
        return $this->parseExceptions;
    }

    /**
     * Returns the number of analyzed namespaces.
     *
     * @throws RuntimeException
     */
    public function countNamespaces(): int
    {
        if (!isset($this->namespaces)) {
            $msg = 'countNamespaces() doesn\'t work before the source was analyzed.';

            throw new RuntimeException($msg);
        }

        $count = 0;
        foreach ($this->namespaces as $namespace) {
            if ($namespace->isUserDefined()) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Returns the analyzed namespace for the given name.
     *
     * @throws OutOfBoundsException
     * @throws RuntimeException
     */
    public function getNamespace(string $name): ASTNamespace
    {
        if (!isset($this->namespaces)) {
            $msg = 'getNamespace() doesn\'t work before the source was analyzed.';

            throw new RuntimeException($msg);
        }
        foreach ($this->namespaces as $namespace) {
            if ($namespace->getImage() === $name) {
                return $namespace;
            }
        }

        throw new OutOfBoundsException(sprintf('Unknown namespace "%s".', $name));
    }

    /**
     * Returns an array with the analyzed namespace.
     *
     * @return ASTArtifactList<ASTNamespace>
     * @throws RuntimeException
     */
    public function getNamespaces(): ASTArtifactList
    {
        if (!isset($this->namespaces)) {
            $msg = 'getNamespaces() doesn\'t work before the source was analyzed.';

            throw new RuntimeException($msg);
        }

        return $this->namespaces;
    }

    /**
     * Send the start parsing process event.
     *
     * @param Builder<ASTNamespace> $builder The used node builder instance.
     */
    protected function fireStartParseProcess(Builder $builder): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startParseProcess($builder);
        }
    }

    /**
     * Send the end parsing process event.
     *
     * @param Builder<ASTNamespace> $builder The used node builder instance.
     */
    protected function fireEndParseProcess(Builder $builder): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endParseProcess($builder);
        }
    }

    /**
     * Sends the start file parsing event.
     */
    protected function fireStartFileParsing(Tokenizer $tokenizer): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startFileParsing($tokenizer);
        }
    }

    /**
     * Sends the end file parsing event.
     */
    protected function fireEndFileParsing(Tokenizer $tokenizer): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endFileParsing($tokenizer);
        }
    }

    /**
     * Sends the start analyzing process event.
     */
    protected function fireStartAnalyzeProcess(): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startAnalyzeProcess();
        }
    }

    /**
     * Sends the end analyzing process event.
     */
    protected function fireEndAnalyzeProcess(): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endAnalyzeProcess();
        }
    }

    /**
     * Sends the start log process event.
     */
    protected function fireStartLogProcess(): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startLogProcess();
        }
    }

    /**
     * Sends the end log process event.
     */
    protected function fireEndLogProcess(): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endLogProcess();
        }
    }

    /**
     * This method performs the parsing process of all source files. It expects
     * that the <b>$_builder</b> property was initialized with a concrete builder
     * implementation.
     */
    private function performParseProcess(): void
    {
        // Reset list of thrown exceptions
        $this->parseExceptions = [];

        $tokenizer = new PHPTokenizerInternal();

        $this->fireStartParseProcess($this->builder);

        foreach ($this->createFileIterator() as $file) {
            $tokenizer->setSourceFile($file);

            $parser = new PHPParserGeneric(
                $tokenizer,
                $this->builder,
                $this->cacheFactory->create(),
            );
            assert($this->configuration->parser instanceof stdClass);
            assert(is_int($this->configuration->parser->nesting));
            $parser->setMaxNestingLevel($this->configuration->parser->nesting);

            // Disable annotation parsing?
            if ($this->withoutAnnotations) {
                $parser->setIgnoreAnnotations();
            }

            $this->fireStartFileParsing($tokenizer);

            try {
                $parser->parse();
            } catch (ParserException $e) {
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
     * @throws InvalidArgumentException
     */
    private function performAnalyzeProcess(): void
    {
        $analyzerLoader = $this->createAnalyzers($this->options);

        $collection = CollectionArtifactFilter::getInstance();

        $this->fireStartAnalyzeProcess();

        assert($this->configuration->parser instanceof stdClass);
        assert(is_int($this->configuration->parser->nesting));
        ini_set('xdebug.max_nesting_level', $this->configuration->parser->nesting);

        foreach ($analyzerLoader as $analyzer) {
            // Add filters if this analyzer is filter aware
            if ($analyzer instanceof AnalyzerFilterAware) {
                $collection->setFilter($this->codeFilter);
            }

            $analyzer->analyze($this->builder->getNamespaces());

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
     * This method will create an iterator instance which contains all files
     * that are part of the parsing process.
     *
     * @return ArrayIterator<int, string>
     * @throws RuntimeException
     */
    private function createFileIterator(): ArrayIterator
    {
        if (count($this->directories) === 0 && count($this->files) === 0) {
            throw new RuntimeException('No source directory and file set.');
        }

        /** @var AppendIterator<int, SplFileInfo|string, \Iterator<int, SplFileInfo|string>> */
        $fileIterator = new AppendIterator();

        foreach ($this->files as $file) {
            $fileIterator->append(
                $this->isPhpStream($file)
                    ? new ArrayIterator([new SplFileObject($file)])
                    : new Iterator(new GlobIterator($file), $this->fileFilter),
            );
        }

        foreach ($this->directories as $directory) {
            $fileIterator->append(
                new Iterator(
                    new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator(
                            $directory . '/',
                            RecursiveDirectoryIterator::FOLLOW_SYMLINKS,
                        ),
                    ),
                    $this->fileFilter,
                    $directory,
                ),
            );
        }

        // TODO: It's important to validate this behavior, imho there is something
        //       wrong in the iterator code used above.
        // Strange: why is the iterator not unique and why does this loop fix it?
        $files = [];
        foreach ($fileIterator as $file) {
            if (is_string($file)) {
                $files[$file] = $file;
            } else {
                $pathname = $file->getRealPath() ?: $file->getPathname();
                $files[$pathname] = $pathname;
            }
        }

        foreach ($files as $key => $file) {
            if (!$this->fileFilter->accept($file, $file)) {
                unset($files[$key]);
            }
        }

        ksort($files);
        // END

        return new ArrayIterator(array_values($files));
    }

    /**
     * @param array<string, array<int, string>|string> $options
     * @return Analyzer[]
     * @throws InvalidArgumentException
     */
    private function createAnalyzers(array $options): array
    {
        $analyzers = $this->analyzerFactory->createRequiredForGenerators($this->generators);

        $cacheKey = md5(serialize($this->files) . serialize($this->directories));
        $cache = $this->cacheFactory->create($cacheKey);

        foreach ($analyzers as $analyzer) {
            if ($analyzer instanceof AnalyzerCacheAware) {
                $analyzer->setCache($cache);
            }
            $analyzer->setOptions($options);

            foreach ($this->listeners as $listener) {
                $analyzer->addAnalyzeListener($listener);

                if ($analyzer instanceof ASTVisitor) {
                    $analyzer->addVisitListener($listener);
                }
            }
        }

        return $analyzers;
    }

    private function isPhpStream(string $path): bool
    {
        return str_starts_with($path, $this->phpStreamPrefix);
    }
}
