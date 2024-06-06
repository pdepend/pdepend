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

use ArrayIterator;
use DirectoryIterator;
use ErrorException;
use Exception;
use Imagick;
use PDepend\Input\ExcludePathFilter;
use PDepend\Input\Iterator;
use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTArtifactList\CollectionArtifactFilter;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTClosure;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTTrait;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Builder\BuilderContext;
use PDepend\Source\Language\PHP\AbstractPHPParser;
use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Util\Cache\CacheDriver;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

/**
 * Abstract test case implementation for the PDepend namespace.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * The current working directory.
     *
     * @since 0.10.0
     */
    protected ?string $workingDirectory = null;

    /**
     * Removes temporary test contents.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $run = __DIR__ . '/_run';
        if (file_exists($run) === false) {
            mkdir($run, 0o755);
        }

        $this->clearRunResources($run);

        if (defined('STDERR') === false) {
            define('STDERR', fopen('php://stderr', '1'));
        }
    }

    /**
     * Resets the global iterator filter.
     */
    protected function tearDown(): void
    {
        CollectionArtifactFilter::getInstance()->setFilter();

        $this->clearRunResources();
        $this->resetWorkingDirectory();

        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }

        parent::tearDown();
    }

    /**
     * Changes the current working directory to an directory associated with the
     * calling test case.
     *
     * @param string $directory Optional working directory.
     * @since 0.10.0
     */
    protected function changeWorkingDirectory(?string $directory = null): void
    {
        if (null === $directory) {
            $directory = $this->getTestWorkingDirectory();
        }

        $cwd = getcwd();
        static::assertNotFalse($cwd);
        $this->workingDirectory = $cwd;
        chdir($directory);
    }

    /**
     * Returns the working directory for the currently executed test.
     *
     * @since 1.0.0
     */
    protected function getTestWorkingDirectory(): string
    {
        $resource = $this->createCodeResourceUriForTest();
        if (is_file($resource)) {
            return dirname($resource);
        }

        return $resource;
    }

    /**
     * Resets a previous changed working directory.
     *
     * @since 0.10.0
     */
    protected function resetWorkingDirectory(): void
    {
        if ($this->workingDirectory) {
            chdir($this->workingDirectory);
        }
        $this->workingDirectory = null;
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @template T of ASTNode
     * @param class-string<T> $nodeType The searched node class.
     * @return T
     */
    protected function getFirstNodeOfTypeInFunction(string $nodeType): ASTNode
    {
        $node = $this->getFirstFunctionForTestCase()
            ->getFirstChildOfType($nodeType);
        static::assertNotNull($node);

        return $node;
    }

    /**
     * Returns the first function found in a test file associated with the
     * given test case.
     */
    protected function getFirstFunctionForTestCase(): ASTFunction
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();
    }

    protected function getFirstClosureForTestCase(): ASTClosure
    {
        $node = $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current()
            ->getFirstChildOfType(ASTClosure::class);
        static::assertNotNull($node);

        return $node;
    }

    /**
     * Create a TextUi Runner
     */
    protected function createTextUiRunner(): TextUI\Runner
    {
        $application = $this->createTestApplication();

        return $application->getRunner();
    }

    protected function createTestApplication(): Application
    {
        $application = new Application();
        $application->setConfigurationFile(__DIR__ . '/../../resources/pdepend.xml.dist');

        return $application;
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @template T of ASTNode
     * @param class-string<T> $nodeType The searched node class.
     * @return T
     * @since 1.0.0
     */
    protected function getFirstNodeOfTypeInTrait(string $nodeType): ASTNode
    {
        $node = $this->getFirstTraitForTestCase()
            ->getFirstChildOfType($nodeType);
        static::assertNotNull($node);

        return $node;
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @template T of ASTNode
     * @param class-string<T> $nodeType The searched node class.
     * @return T
     */
    protected function getFirstNodeOfTypeInClass(string $nodeType): ASTNode
    {
        $node = $this->getFirstClassForTestCase()
            ->getFirstChildOfType($nodeType);
        static::assertNotNull($node);

        return $node;
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @template T of ASTNode
     * @param class-string<T> $nodeType The searched node class.
     * @return T
     */
    protected function getFirstNodeOfTypeInInterface(string $nodeType): ASTNode
    {
        $node = $this->getFirstInterfaceForTestCase()
            ->getFirstChildOfType($nodeType);
        static::assertNotNull($node);

        return $node;
    }

    /**
     * Returns the first trait found in a test file associated with the given
     * test case.
     *
     * @since 1.0.0
     */
    protected function getFirstTraitForTestCase(): ASTTrait
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getTraits()
            ->current();
    }

    /**
     * Returns the first class found in a test file associated with the given
     * test case.
     */
    protected function getFirstClassForTestCase(): ASTClass
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();
    }

    /**
     * Returns the first method that could be found in the source file
     * associated with the calling test case.
     */
    protected function getFirstClassMethodForTestCase(): ASTMethod
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current();
    }

    /**
     * Returns the first interface that could be found in the source file
     * associated with the calling test case.
     */
    protected function getFirstInterfaceForTestCase(): ASTInterface
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getInterfaces()
            ->current();
    }

    /**
     * Returns the first method that could be found in the source file
     * associated with the calling test case.
     */
    protected function getFirstInterfaceMethodForTestCase(): ASTMethod
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getInterfaces()
            ->current()
            ->getMethods()
            ->current();
    }

    /**
     * Returns the first class or interface that could be found in the code under
     * test for the calling test case.
     */
    protected function getFirstTypeForTestCase(): AbstractASTClassOrInterface
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current();
    }

    /**
     * Returns the first method that could be found in the code under test for
     * the calling test case.
     */
    protected function getFirstMethodForTestCase(): ASTMethod
    {
        return $this->getFirstTypeForTestCase()
            ->getMethods()
            ->current();
    }

    protected function getFirstFormalParameterForTestCase(): ASTFormalParameter
    {
        $node = $this->getFirstFunctionForTestCase()
            ->getFirstChildOfType(ASTFormalParameter::class);
        static::assertNotNull($node);

        return $node;
    }

    /**
     * Collects all children from a given node.
     *
     * @param ASTNode $node The current root node.
     * @param array<string> $actual Previous filled list.
     * @return array<string>
     */
    protected static function collectChildNodes(ASTNode $node, array $actual = []): array
    {
        foreach ($node->getChildren() as $child) {
            $actual[] = $child::class;
            $actual = self::collectChildNodes($child, $actual);
        }

        return $actual;
    }

    /**
     * Tests that the given node and its children represent the expected ast
     * object graph.
     *
     * @param array<string> $expected
     */
    protected static function assertGraphEquals(ASTNode $node, array $expected): void
    {
        static::assertEquals($expected, self::collectChildNodes($node));
    }

    /**
     * Collects all children from a given node.
     *
     * @param ASTNode $node The current root node.
     * @return array<mixed>
     */
    protected static function collectGraph(ASTNode $node): array
    {
        $graph = [];
        foreach ($node->getChildren() as $child) {
            $graph[] = $child::class . ' (' . $child->getImage() . ')';
            if (0 < count($child->getChildren())) {
                $graph[] = self::collectGraph($child);
            }
        }

        return $graph;
    }

    /**
     * Tests that the given node and its children represent the expected ast
     * object graph.
     *
     * @param ASTNode $node The root node.
     * @param array<mixed> $graph Expected class structure.
     */
    protected static function assertGraph(ASTNode $node, array $graph): void
    {
        $actual = self::collectGraph($node);
        static::assertEquals($graph, $actual);
    }

    /**
     * Creates a mocked class instance without calling the constructor.
     *
     * @template T of ASTNode
     * @param class-string<T> $className Name of the class to mock.
     * @return T
     * @since 0.10.0
     */
    protected function getMockWithoutConstructor(string $className): ASTNode
    {
        $mock = $this->getMockBuilder($className)
            ->onlyMethods(['__construct'])
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    /**
     * Clears all temporary resources.
     */
    private function clearRunResources(?string $dir = null): void
    {
        if ($dir === null) {
            $dir = __DIR__ . '/_run';
        }

        foreach (new DirectoryIterator($dir) as $file) {
            if ($file->isDot() || $file->getFilename() === '.svn') {
                continue;
            }
            $pathName = realpath($file->getPathname());
            static::assertNotFalse($pathName);
            if ($file->isDir()) {
                $this->clearRunResources($pathName);
                rmdir($pathName);
            } else {
                unlink($pathName);
            }
        }
    }

    /**
     * Creates a test configuration instance.
     *
     * @since 0.10.0
     */
    protected function createConfigurationFixture(): Util\Configuration
    {
        $application = $this->createTestApplication();

        return $application->getConfiguration();
    }

    protected function createCacheFixture(): CacheDriver&MockObject
    {
        $cache = $this->getMockBuilder(CacheDriver::class)
            ->getMock();

        return $cache;
    }

    /**
     * Creates a PDepend instance configured with the code resource associated
     * with the calling test case.
     *
     * @since 0.10.0
     */
    protected function createEngineFixture(): Engine
    {
        $this->changeWorkingDirectory(
            self::createCodeResourceURI('config/')
        );

        $application = $this->createTestApplication();

        $configuration = $application->getConfiguration();
        $cacheFactory = new Util\Cache\CacheFactory($configuration);
        $analyzerFactory = $application->getAnalyzerFactory();

        return new Engine($configuration, $cacheFactory, $analyzerFactory);
    }

    /**
     * @throws RuntimeException
     */
    protected function silentRun(TextUI\Runner $runner): int
    {
        $error = null;

        ob_start();

        try {
            $exitCode = $runner->run();
        } catch (Exception $exception) {
            $error = $exception;
        }

        ob_end_clean();

        if ($error) {
            throw $error;
        }

        return $exitCode;
    }

    /**
     * Creates a ready to use class fixture.
     *
     * @param string $name Optional class name.
     * @since 1.0.2
     */
    protected function createClassFixture(?string $name = null): ASTClass
    {
        $name = $name ?: static::class;

        $class = new ASTClass($name);
        $class->setCompilationUnit(new ASTCompilationUnit($GLOBALS['argv'][0]));
        $class->setCache(new MemoryCacheDriver());
        $context = $this->getMockBuilder(BuilderContext::class)
            ->getMock();
        $class->setContext($context);

        return $class;
    }

    /**
     * Creates a ready to use interface fixture.
     *
     * @param string $name Optional interface name.
     * @since 1.0.2
     */
    protected function createInterfaceFixture(?string $name = null): ASTInterface
    {
        $name = $name ?: static::class;

        $interface = new ASTInterface($name);
        $interface->setCompilationUnit(new ASTCompilationUnit($GLOBALS['argv'][0]));
        $interface->setCache(new MemoryCacheDriver());

        return $interface;
    }

    /**
     * Creates a ready to use trait fixture.
     *
     * @param string $name Optional trait name.
     * @since 1.0.2
     */
    protected function createTraitFixture(?string $name = null): ASTTrait
    {
        $name = $name ?: static::class;

        $trait = new ASTTrait($name);
        $trait->setCache(new MemoryCacheDriver());

        return $trait;
    }

    /**
     * Creates a ready to use function fixture.
     *
     * @param string $name Optional function name.
     * @since 1.0.2
     */
    protected function createFunctionFixture(?string $name = null): ASTFunction
    {
        $name = $name ?: static::class;

        $function = new ASTFunction($name);
        $function->setCompilationUnit(new ASTCompilationUnit($GLOBALS['argv'][0]));
        $function->setCache(new MemoryCacheDriver());
        $function->addChild(new ASTFormalParameters());

        return $function;
    }

    /**
     * Creates a ready to use method fixture.
     *
     * @param string $name Optional method name.
     * @since 1.0.2
     */
    protected function createMethodFixture(?string $name = null): ASTMethod
    {
        $name = $name ?: static::class;

        $method = new ASTMethod($name);
        $method->setCache(new MemoryCacheDriver());
        $method->addChild(new ASTFormalParameters());

        return $method;
    }

    /**
     * Creates a temporary resource for the given file name.
     */
    protected function createRunResourceURI(?string $fileName = null): string
    {
        return tempnam(sys_get_temp_dir(), $fileName ?: uniqid()) ?: '';
    }

    /**
     * Creates a code uri for the given file name.
     *
     * @param string $fileName The code file name.
     * @throws ErrorException
     */
    protected static function createCodeResourceURI(string $fileName): string
    {
        $uri = realpath(__DIR__ . '/../../resources/files') . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($uri) === false) {
            throw new ErrorException("File '{$fileName}' does not exists.");
        }

        return $uri;
    }

    /**
     * Creates a code uri for the calling test case.
     */
    protected function createCodeResourceUriForTest(): string
    {
        [$class, $method] = explode('::', $this->getCallingTestMethod());

        if (1 === count($parts = explode('\\', $class))) {
            $parts = explode('\\', $class);
        }

        // Strip first two parts
        array_shift($parts);

        $part = end($parts);
        static::assertNotFalse($part);
        if (!preg_match('(Version\d+Test$)', $part) && preg_match('(\D(\d+Test)$)', $part, $match)) {
            array_pop($parts);
            $parts[] = $match[1];

            // TODO: Fix this workaround for the existing lower case directories
            array_unshift($parts, strtolower(array_shift($parts)));
        }

        $fileName = substr(implode(DIRECTORY_SEPARATOR, $parts), 0, -4) . DIRECTORY_SEPARATOR . $method;

        try {
            return self::createCodeResourceURI($fileName);
        } catch (ErrorException) {
            return self::createCodeResourceURI("{$fileName}.php");
        }
    }

    /**
     * Returns the name of the calling test method.
     *
     * @throws ErrorException
     */
    protected function getCallingTestMethod(): string
    {
        foreach (debug_backtrace() as $frame) {
            if (str_starts_with($frame['function'], 'test')) {
                return ($frame['class'] ?? '') . '::' . $frame['function'];
            }
        }

        throw new ErrorException('No calling test case found.');
    }

    /**
     * Parses the test code associated with the calling test method.
     *
     * @param bool $ignoreAnnotations The parser should ignore annotations.
     * @return ASTArtifactList<ASTNamespace>
     */
    protected function parseCodeResourceForTest(bool $ignoreAnnotations = false): ASTArtifactList
    {
        return $this->parseSource(
            $this->createCodeResourceUriForTest(),
            $ignoreAnnotations
        );
    }

    /**
     * Parses the given source file or directory with the default tokenizer
     * and node builder implementations.
     *
     * @return ASTArtifactList<ASTNamespace>
     */
    public function parseTestCaseSource(string $testCase, bool $ignoreAnnotations = false): ASTArtifactList
    {
        [$class, $method] = explode('::', $testCase);

        $fileName = substr(strtolower($class), 8, strrpos($class, '\\') - 8);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $fileName) . DIRECTORY_SEPARATOR . $method;

        try {
            $fileOrDirectory = self::createCodeResourceURI($fileName);
        } catch (ErrorException) {
            $fileOrDirectory = self::createCodeResourceURI($fileName . '.php');
        }

        return $this->parseSource($fileOrDirectory, $ignoreAnnotations);
    }

    /**
     * Parses the given source file or directory with the default tokenizer
     * and node builder implementations.
     *
     * @return ASTArtifactList<ASTNamespace>
     * @throws ErrorException
     */
    public function parseSource(string $fileOrDirectory, bool $ignoreAnnotations = false): ASTArtifactList
    {
        if (file_exists($fileOrDirectory) === false) {
            $fileOrDirectory = self::createCodeResourceURI($fileOrDirectory);
        }

        if (is_dir($fileOrDirectory)) {
            $it = new Iterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($fileOrDirectory)
                ),
                new ExcludePathFilter(['.svn'])
            );
        } else {
            $it = new ArrayIterator([$fileOrDirectory]);
        }

        $files = [];
        foreach ($it as $file) {
            if (is_object($file)) {
                $files[] = realpath($file->getPathname());
            } else {
                $files[] = $file;
            }
        }
        sort($files);

        $cache = new MemoryCacheDriver();
        $builder = new PHPBuilder();

        foreach ($files as $file) {
            static::assertNotFalse($file);
            $tokenizer = new PHPTokenizerInternal();
            $tokenizer->setSourceFile($file);

            $parser = $this->createPHPParser($tokenizer, $builder, $cache);
            if ($ignoreAnnotations) {
                $parser->setIgnoreAnnotations();
            }

            $parser->parse();
        }

        return $builder->getNamespaces();
    }

    /**
     * @param PHPBuilder<mixed> $builder
     */
    protected function createPHPParser(Tokenizer $tokenizer, PHPBuilder $builder, CacheDriver $cache): AbstractPHPParser
    {
        return new PHPParserGeneric(
            $tokenizer,
            $builder,
            $cache
        );
    }

    /**
     * @param array<int, string> $requiredFormats
     */
    protected function requireImagick(array $requiredFormats = ['PNG', 'SVG']): void
    {
        if (!extension_loaded('imagick')) {
            static::markTestSkipped('No pecl/imagick extension.');
        }

        $formats = Imagick::queryFormats();

        if (count(array_intersect($requiredFormats, $formats)) < count($requiredFormats)) {
            static::markTestSkipped('Imagick PNG and SVG support are not both installed.');
        }
    }
}
