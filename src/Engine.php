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
use Fidry\CpuCoreCounter\CpuCoreCounter;
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
use PDepend\Util\Cache\CacheFactory;
use PDepend\Util\Configuration;
use React\EventLoop\Loop;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use SplFileObject;
use stdClass;
use Throwable;

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

    private bool $isWorker = false;

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
     * List of all {@link Throwable} that were caught during
     * the parsing process.
     *
     * @var list<Throwable>
     */
    private array $parseExceptions = [];

    /** number of threads to use for parsing */
    private ?int $threads = null;

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

    public function setWorker(): void
    {
        $this->isWorker = true;
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
     * number of threads to use for parsing
     */
    public function setThreads(?int $threads): void
    {
        $this->threads = $threads;
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
     * @return Throwable[]
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
     */
    protected function fireStartParseProcess(int $fileCount): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startParseProcess($fileCount);
        }
    }

    /**
     * Send the end parsing process event.
     */
    protected function fireEndParseProcess(): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endParseProcess();
        }
    }

    /**
     * Sends the start file parsing event.
     */
    protected function fireStartFileParsing(): void
    {
        foreach ($this->listeners as $listener) {
            $listener->startFileParsing();
        }
    }

    /**
     * Sends the end file parsing event.
     */
    protected function fireEndFileParsing(): void
    {
        foreach ($this->listeners as $listener) {
            $listener->endFileParsing();
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

        if ($this->isWorker) {
            $this->runWorker();

            return;
        }

        $files = $this->createFileIterator();
        $fileCount = count($files);
        $this->fireStartParseProcess($fileCount);

        if ($fileCount > 1 && (!defined('PHP_WINDOWS_VERSION_BUILD') || extension_loaded('sockets'))) {
            $threadCount = $this->threads ?? (new CpuCoreCounter())->getCount();
            if ($threadCount > 1) {
                if ($this->runMultiProcessParse($files, $fileCount, $threadCount)) {
                    $this->fireEndParseProcess();

                    return;
                }
            }
        }

        $tokenizer = new PHPTokenizerInternal();

        foreach ($files as $file) {
            $this->fireStartFileParsing();
            $this->parseFile($tokenizer, $file);
            $this->fireEndFileParsing();
        }

        $this->fireEndParseProcess();
    }

    private function runWorker(): void
    {
        $stdin = fopen('php://stdin', 'rb');
        if ($stdin === false) {
            throw new RuntimeException('Unable to acquire input stream');
        }

        $tokenizer = new PHPTokenizerInternal();

        while (($file = fgets($stdin)) !== false) {
            $file = trim($file);
            if ($file === '') {
                continue;
            }

            $this->parseFile($tokenizer, $file);

            if ($this->parseExceptions) {
                echo base64_encode(serialize(array_pop($this->parseExceptions))) . "\n";
                $this->parseExceptions = [];

                continue;
            }

            $namespaces = $this->builder->getNamespaces();
            echo base64_encode(serialize($namespaces)) . "\n";
            $this->builder = new PHPBuilder();
        }
    }

    /**
     * @param ArrayIterator<int, string> $files
     */
    private function runMultiProcessParse(ArrayIterator $files, int $fileCount, int $coreCount): bool
    {
        $processFactory = new ProcessFactory($this->withoutAnnotations);
        $loop = Loop::get();
        $proccessCount = min($fileCount, $coreCount);
        $buffers = [];
        for ($proccessNo = 0; $proccessNo < $proccessCount; $proccessNo++) {
            $process = $processFactory->create();
            $buffers[$proccessNo] = '';
            $process->start($loop);

            if (!$process->stdout instanceof ReadableStreamInterface
                || !$process->stdin instanceof WritableStreamInterface
            ) {
                return false;
            }

            $process->stdout->on('data', function (string $chunk) use (&$buffers, $proccessNo, $files, $process): void {
                $buffers[$proccessNo] .= $chunk;

                while (($pos = strpos($buffers[$proccessNo], "\n")) !== false) {
                    $this->fireStartFileParsing();
                    $line = substr($buffers[$proccessNo], 0, $pos);
                    $buffers[$proccessNo] = substr($buffers[$proccessNo], $pos + 1);

                    $serializedData = base64_decode($line, true);
                    if ($serializedData === false) {
                        throw new RuntimeException('Unable to decode message: ' . $line);
                    }

                    $data = unserialize($serializedData);

                    if ($data instanceof Throwable) {
                        $this->parseExceptions[] = $data;
                    }

                    if ($files->valid()) {
                        $process->stdin->write($files->current() . "\n");
                        $files->next();
                    } else {
                        $process->stdin->end();
                    }
                    $this->fireEndFileParsing();
                }
            });

            $process->stdin->write($files->current() . "\n");
            $files->next();
        }
        $loop->run();
        $cache = $this->cacheFactory->create();
        $this->builder->setCache($cache);

        return true;
    }

    private function parseFile(PHPTokenizerInternal $tokenizer, string $file): void
    {
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

        try {
            $parser->parse();
        } catch (Throwable $e) {
            $this->parseExceptions[] = $e;
        }
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
