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

use Exception;
use Imagick;
use PDepend\Input\ExcludePathFilter;
use PDepend\Input\Iterator;
use PDepend\Source\AST\ASTArtifactList\CollectionArtifactFilter;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTTrait;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Util\Cache\CacheDriver;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

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
     * @var string
     * @since 0.10.0
     */
    protected $workingDirectory = null;

    /**
     * Removes temporary test contents.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $run = __DIR__ . '/_run';
        if (file_exists($run) === false) {
            mkdir($run, 0755);
        }

        $this->clearRunResources($run);

        if (defined('STDERR') === false) {
            define('STDERR', fopen('php://stderr', true));
        }
    }

    /**
     * Resets the global iterator filter.
     *
     * @return void
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
     *
     * @return void
     * @since 0.10.0
     */
    protected function changeWorkingDirectory($directory = null)
    {
        if (null === $directory) {
            $directory = $this->getTestWorkingDirectory();
        }

        $this->workingDirectory = getcwd();
        chdir($directory);
    }

    /**
     * Returns the working directory for the currently executed test.
     *
     * @return string
     * @since 1.0.0
     */
    protected function getTestWorkingDirectory()
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
     * @return void
     * @since 0.10.0
     */
    protected function resetWorkingDirectory()
    {
        if ($this->workingDirectory) {
            chdir($this->workingDirectory);
        }
        $this->workingDirectory = null;
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @param string $nodeType The searched node class.
     *
     * @return \PDepend\Source\AST\ASTNode
     */
    protected function getFirstNodeOfTypeInFunction($testCase, $nodeType)
    {
        return $this->getFirstFunctionForTestCase($testCase)
            ->getFirstChildOfType($nodeType);
    }

    /**
     * Returns the first function found in a test file associated with the
     * given test case.
     *
     * @return ASTFunction
     */
    protected function getFirstFunctionForTestCase()
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();
    }

    /**
     * @return \PDepend\Source\AST\ASTClosure
     */
    protected function getFirstClosureForTestCase()
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current()
            ->getFirstChildOfType('PDepend\\Source\\AST\\ASTClosure');
    }

    /**
     * Create a TextUi Runner
     *
     * @return \PDepend\TextUI\Runner
     */
    protected function createTextUiRunner()
    {
        $application = $this->createTestApplication();

        return $application->getRunner();
    }

    protected function createTestApplication()
    {
        $application = new \PDepend\Application();
        $application->setConfigurationFile(__DIR__ . '/../../resources/pdepend.xml.dist');

        return $application;
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $nodeType The searched node class.
     *
     * @return \PDepend\Source\AST\ASTNode
     * @since 1.0.0
     */
    protected function getFirstNodeOfTypeInTrait($nodeType)
    {
        return $this->getFirstTraitForTestCase()
            ->getFirstChildOfType($nodeType);
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @param string $nodeType The searched node class.
     *
     * @return \PDepend\Source\AST\ASTNode
     */
    protected function getFirstNodeOfTypeInClass($testCase, $nodeType)
    {
        return $this->getFirstClassForTestCase($testCase)
            ->getFirstChildOfType($nodeType);
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @param string $nodeType The searched node class.
     *
     * @return \PDepend\Source\AST\ASTNode
     */
    protected function getFirstNodeOfTypeInInterface($testCase, $nodeType)
    {
        return $this->getFirstInterfaceForTestCase($testCase)
            ->getFirstChildOfType($nodeType);
    }

    /**
     * Returns the first trait found in a test file associated with the given
     * test case.
     *
     * @return \PDepend\Source\AST\ASTTrait
     * @since 1.0.0
     */
    protected function getFirstTraitForTestCase()
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getTraits()
            ->current();
    }

    /**
     * Returns the first class found in a test file associated with the given
     * test case.
     *
     * @return \PDepend\Source\AST\ASTClass
     */
    protected function getFirstClassForTestCase()
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();
    }

    /**
     * Returns the first method that could be found in the source file
     * associated with the calling test case.
     *
     * @return \PDepend\Source\AST\ASTMethod
     */
    protected function getFirstClassMethodForTestCase()
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
     *
     * @return \PDepend\Source\AST\ASTInterface
     */
    protected function getFirstInterfaceForTestCase()
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getInterfaces()
            ->current();
    }

    /**
     * Returns the first method that could be found in the source file
     * associated with the calling test case.
     *
     * @return \PDepend\Source\AST\ASTMethod
     */
    protected function getFirstInterfaceMethodForTestCase()
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
     *
     * @return \PDepend\Source\AST\AbstractASTClassOrInterface
     */
    protected function getFirstTypeForTestCase()
    {
        return $this->parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current();
    }

    /**
     * Returns the first method that could be found in the code under test for
     * the calling test case.
     *
     * @return \PDepend\Source\AST\ASTMethod
     */
    protected function getFirstMethodForTestCase()
    {
        return $this->getFirstTypeForTestCase()
            ->getMethods()
            ->current();
    }

    /**
     * @return \PDepend\Source\AST\ASTFormalParameter
     */
    protected function getFirstFormalParameterForTestCase()
    {
        return $this->getFirstFunctionForTestCase()
            ->getFirstChildOfType(
                'PDepend\\Source\\AST\\ASTFormalParameter'
            );
    }

    /**
     * Collects all children from a given node.
     *
     * @param \PDepend\Source\AST\ASTNode $node   The current root node.
     * @param array                   $actual Previous filled list.
     *
     * @return array<string>
     */
    protected static function collectChildNodes(ASTNode $node, array $actual = [])
    {
        foreach ($node->getChildren() as $child) {
            $actual[] = get_class($child);
            $actual   = self::collectChildNodes($child, $actual);
        }
        return $actual;
    }

    /**
     * Tests that the given node and its children represent the expected ast
     * object graph.
     *
     * @param \PDepend\Source\AST\ASTNode $node
     * @param array<string> $expected
     *
     * @return void
     */
    protected static function assertGraphEquals(ASTNode $node, array $expected)
    {
        self::assertEquals($expected, self::collectChildNodes($node));
    }

    /**
     * Collects all children from a given node.
     *
     * @param \PDepend\Source\AST\ASTNode $node The current root node.
     *
     * @return array
     */
    protected static function collectGraph(ASTNode $node)
    {
        $graph = [];
        foreach ($node->getChildren() as $child) {
            $graph[] = get_class($child) . ' (' . $child->getImage() . ')';
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
     * @param \PDepend\Source\AST\ASTNode $node  The root node.
     * @param array                   $graph Expected class structure.
     *
     * @return void
     */
    protected static function assertGraph(ASTNode $node, $graph)
    {
        $actual = self::collectGraph($node);
        self::assertEquals($graph, $actual);
    }

    /**
     * Creates a mocked class instance without calling the constructor.
     *
     * @param string $className Name of the class to mock.
     *
     * @return stdClass
     * @since 0.10.0
     */
    protected function getMockWithoutConstructor($className)
    {
        $mock = $this->getMockBuilder($className)
            ->onlyMethods(['__construct'])
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    /**
     * Clears all temporary resources.
     *
     * @param string $dir
     * @return void
     */
    private function clearRunResources($dir = null)
    {
        if ($dir === null) {
            $dir = __DIR__ . '/_run';
        }

        foreach (new \DirectoryIterator($dir) as $file) {
            if ($file == '.' || $file == '..' || $file == '.svn') {
                continue;
            }
            $pathName = realpath($file->getPathname());
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
     * @return \PDepend\Util\Configuration
     * @since 0.10.0
     */
    protected function createConfigurationFixture()
    {
        $application = $this->createTestApplication();

        return $application->getConfiguration();
    }

    /**
     * @return \PDepend\Util\Cache\CacheDriver
     */
    protected function createCacheFixture()
    {
        $cache = $this->getMockBuilder('\\PDepend\\Util\\Cache\\CacheDriver')
            ->getMock();

        return $cache;
    }

    /**
     * Creates a PDepend instance configured with the code resource associated
     * with the calling test case.
     *
     * @return \PDepend\Engine
     * @since 0.10.0
     */
    protected function createEngineFixture()
    {
        $this->changeWorkingDirectory(
            self::createCodeResourceURI('config/')
        );

        $application = $this->createTestApplication();

        $configuration = $application->getConfiguration();
        $cacheFactory = new \PDepend\Util\Cache\CacheFactory($configuration);
        $analyzerFactory = $application->getAnalyzerFactory();

        return new Engine($configuration, $cacheFactory, $analyzerFactory);
    }

    /**
     * @param \PDepend\TextUI\Runner $runner
     *
     * @return int
     */
    protected function silentRun($runner)
    {
        $error = null;

        ob_start();

        try {
            $exitCode = $runner->run();
        } catch (\Exception $exception) {
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
     *
     * @return \PDepend\Source\AST\ASTClass
     * @since 1.0.2
     */
    protected function createClassFixture($name = null)
    {
        $name = $name ? $name : get_class($this);

        $class = new ASTClass($name);
        $class->setCompilationUnit(new ASTCompilationUnit($GLOBALS['argv'][0]));
        $class->setCache(new MemoryCacheDriver());
        $context = $this->getMockBuilder('PDepend\\Source\\Builder\\BuilderContext')
            ->getMock();
        $class->setContext($context);

        return $class;
    }

    /**
     * Creates a ready to use interface fixture.
     *
     * @param string $name Optional interface name.
     *
     * @return \PDepend\Source\AST\ASTInterface
     * @since 1.0.2
     */
    protected function createInterfaceFixture($name = null)
    {
        $name = $name ? $name : get_class($this);

        $interface = new ASTInterface($name);
        $interface->setCompilationUnit(new ASTCompilationUnit($GLOBALS['argv'][0]));
        $interface->setCache(new MemoryCacheDriver());

        return $interface;
    }

    /**
     * Creates a ready to use trait fixture.
     *
     * @param string $name Optional trait name.
     * @return \PDepend\Source\AST\ASTTrait
     * @since 1.0.2
     */
    protected function createTraitFixture($name = null)
    {
        $name = $name ? $name : get_class($this);

        $trait = new ASTTrait($name);
        $trait->setCache(new MemoryCacheDriver());

        return $trait;
    }

    /**
     * Creates a ready to use function fixture.
     *
     * @param string $name Optional function name.
     * @return \PDepend\Source\AST\ASTFunction
     * @since 1.0.2
     */
    protected function createFunctionFixture($name = null)
    {
        $name = $name ? $name : get_class($this);

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
     * @return \PDepend\Source\AST\ASTMethod
     * @since 1.0.2
     */
    protected function createMethodFixture($name = null)
    {
        $name = $name ? $name : get_class($this);

        $method = new ASTMethod($name);
        $method->setCache(new MemoryCacheDriver());
        $method->addChild(new ASTFormalParameters());

        return $method;
    }

    /**
     * Creates a temporary resource for the given file name.
     *
     * @param string $fileName
     * @return string
     * @throws \ErrorException
     */
    protected function createRunResourceURI($fileName = null)
    {
        return tempnam(sys_get_temp_dir(), $fileName ?: uniqid());
    }

    /**
     * Creates a code uri for the given file name.
     *
     * @param string $fileName The code file name.
     * @return string
     * @throws \ErrorException
     */
    protected static function createCodeResourceURI($fileName)
    {
        $uri = realpath(__DIR__ . '/../../resources/files') . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($uri) === false) {
            throw new \ErrorException("File '{$fileName}' does not exists.");
        }
        return $uri;
    }

    /**
     * Creates a code uri for the calling test case.
     *
     * @return string
     */
    protected function createCodeResourceUriForTest()
    {
        list($class, $method) = explode('::', $this->getCallingTestMethod());

        if (1 === count($parts = explode('\\', $class))) {
            $parts = explode('\\', $class);
        }


        // Strip first two parts
        array_shift($parts);

        if (!preg_match('(Version\d+Test$)', end($parts)) && preg_match('(\D(\d+Test)$)', end($parts), $match)) {
            array_pop($parts);
            array_push($parts, $match[1]);

            // TODO: Fix this workaround for the existing lower case directories
            array_unshift($parts, strtolower(array_shift($parts)));
        }

        $fileName = substr(implode(DIRECTORY_SEPARATOR, $parts), 0, -4) . DIRECTORY_SEPARATOR . $method;
        try {
            return self::createCodeResourceURI($fileName);
        } catch (\ErrorException $e) {
            return self::createCodeResourceURI("{$fileName}.php");
        }
    }

    /**
     * Returns the name of the calling test method.
     *
     * @return string
     */
    protected function getCallingTestMethod()
    {
        foreach (debug_backtrace() as $frame) {
            if (str_starts_with($frame['function'], 'test')) {
                return "{$frame['class']}::{$frame['function']}";
            }
        }
        throw new \ErrorException("No calling test case found.");
    }

    /**
     * Initializes the test environment.
     *
     * @return void
     */
    public static function init()
    {
        // First register autoloader
        spl_autoload_register([__CLASS__, 'autoload']);

        // Is it not installed?
        if (is_file(__DIR__ . '/../../../main/php/PDepend/Engine.php')) {
            $path  = realpath(__DIR__ . '/../../../main/php/');
            $path .= PATH_SEPARATOR . get_include_path();
            set_include_path($path);
        }

        // Set test path
        $path  = realpath(__DIR__ . '/..');
        $path .= PATH_SEPARATOR . get_include_path();
        set_include_path($path);
    }

    /**
     * Autoloader for the test cases.
     *
     * @param string $className Name of the missing class.
     * @return void
     */
    public static function autoload($className)
    {
        $file = strtr($className, '\\', DIRECTORY_SEPARATOR) . '.php';
        if (is_file(__DIR__ . '/../../../main/php/PDepend/Engine.php')) {
            $file = __DIR__ . '/../../../main/php/' . $file;
        }
        if (file_exists($file)) {
            include $file;
        }
    }

    /**
     * Parses the test code associated with the calling test method.
     *
     * @param boolean $ignoreAnnotations The parser should ignore annotations.
     * @return \PDepend\Source\AST\ASTNamespace[]
     */
    protected function parseCodeResourceForTest($ignoreAnnotations = false)
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
     * @param string $testCase
     * @param boolean $ignoreAnnotations
     * @return \PDepend\Source\AST\ASTNamespace[]
     */
    public function parseTestCaseSource($testCase, $ignoreAnnotations = false)
    {
        list($class, $method) = explode('::', $testCase);

        $fileName = substr(strtolower($class), 8, strrpos($class, '\\') - 8);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $fileName) . DIRECTORY_SEPARATOR . $method;

        try {
            $fileOrDirectory = self::createCodeResourceURI($fileName);
        } catch (\ErrorException $e) {
            $fileOrDirectory = self::createCodeResourceURI($fileName . '.php');
        }

        return $this->parseSource($fileOrDirectory, $ignoreAnnotations);
    }

    /**
     * Parses the given source file or directory with the default tokenizer
     * and node builder implementations.
     *
     * @param string  $fileOrDirectory
     * @param boolean $ignoreAnnotations
     * @return \PDepend\Source\AST\ASTNamespace[]
     */
    public function parseSource($fileOrDirectory, $ignoreAnnotations = false)
    {
        if (file_exists($fileOrDirectory) === false) {
            $fileOrDirectory = self::createCodeResourceURI($fileOrDirectory);
        }

        if (is_dir($fileOrDirectory)) {
            $it = new Iterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($fileOrDirectory)
                ),
                new ExcludePathFilter(['.svn'])
            );
        } else {
            $it = new \ArrayIterator([$fileOrDirectory]);
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

        $cache   = new MemoryCacheDriver();
        $builder = new PHPBuilder();

        foreach ($files as $file) {
            $tokenizer = new PHPTokenizerInternal();
            $tokenizer->setSourceFile($file);

            $parser = $this->createPHPParser($tokenizer, $builder, $cache);
            if ($ignoreAnnotations === true) {
                $parser->setIgnoreAnnotations();
            }

            $parser->parse();
        }
        return $builder->getNamespaces();
    }

    /**
     * @param \PDepend\Source\Tokenizer\Tokenizer $tokenizer
     * @param \PDepend\Source\Builder\Builder<mixed> $builder
     * @param \PDepend\Util\Cache\CacheDriver $cache
     * @return \PDepend\Source\Language\PHP\AbstractPHPParser
     */
    protected function createPHPParser(Tokenizer $tokenizer, Builder $builder, CacheDriver $cache)
    {
        return new PHPParserGeneric(
            $tokenizer,
            $builder,
            $cache
        );
    }

    protected function getAbstractClassMock($originalClassName, array $arguments = [], $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true, $mockedMethods = [], $cloneArguments = false)
    {
        return $this->getMockForAbstractClass($originalClassName, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload, $mockedMethods, $cloneArguments);
    }

    /**
     * @param array<int, string> $requiredFormats
     * @return void
     */
    protected function requireImagick(array $requiredFormats = ['PNG', 'SVG'])
    {
        if (extension_loaded('imagick') === false) {
            $this->markTestSkipped('No pecl/imagick extension.');
        }

        $formats = Imagick::queryFormats();

        if (count(array_intersect($requiredFormats, $formats)) < count($requiredFormats)) {
            $this->markTestSkipped('Imagick PNG and SVG support are not both installed.');
        }
    }
}

AbstractTestCase::init();
