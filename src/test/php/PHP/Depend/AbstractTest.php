<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

/**
 * Abstract test case implementation for the PHP_Depend package.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
abstract class PHP_Depend_AbstractTest extends PHPUnit_Framework_TestCase
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
    protected function setUp()
    {
        parent::setUp();

        $run = dirname(__FILE__) . '/_run';
        if (file_exists($run) === false) {
            mkdir($run, 0755);
        }

        $this->_clearRunResources($run);

        include_once 'PHP/Depend.php';

        if (defined('STDERR') === false) {
            define('STDERR', fopen('php://stderr', true));
        }
    }

    /**
     * Resets the global iterator filter.
     *
     * @return void
     */
    protected function tearDown()
    {
        PHP_Depend_Code_Filter_Collection::getInstance()->setFilter();

        $this->_clearRunResources();
        $this->resetWorkingDirectory();

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
        $resource = self::createCodeResourceUriForTest();
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
     * @return PHP_Depend_Code_ASTNode
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
     * @return PHP_Depend_Code_Function
     */
    protected function getFirstFunctionForTestCase()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $nodeType The searched node class.
     *
     * @return PHP_Depend_Code_ASTNode
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
     * @return PHP_Depend_Code_ASTNode
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
     * @return PHP_Depend_Code_ASTNode
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
     * @return PHP_Depend_Code_Trait
     * @since 1.0.0
     */
    protected function getFirstTraitForTestCase()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getTraits()
            ->current();
    }

    /**
     * Returns the first class found in a test file associated with the given
     * test case.
     *
     * @return PHP_Depend_Code_Class
     */
    protected function getFirstClassForTestCase()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getClasses()
            ->current();
    }

    /**
     * Returns the first interface that could be found in the source file
     * associated with the calling test case.
     *
     * @return PHP_Depend_Code_Interface
     */
    protected function getFirstInterfaceForTestCase()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getInterfaces()
            ->current();
    }

    /**
     * Collects all children from a given node.
     *
     * @param PHP_Depend_Code_ASTNode $node   The current root node.
     * @param array                   $actual Previous filled list.
     *
     * @return array(string)
     */
    protected static function collectChildNodes(
        PHP_Depend_Code_ASTNode $node,
        array $actual = array()
    ) {
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
     * @param PHP_Depend_Code_ASTNode $node     The root node.
     * @param array(string)           $expected Expected class structure.
     *
     * @return void
     */
    protected static function assertGraphEquals(
        PHP_Depend_Code_ASTNode $node,
        array $expected
    ) {
        $actual = self::collectChildNodes($node);
        self::assertEquals($expected, $actual);
    }

    /**
     * Collects all children from a given node.
     *
     * @param PHP_Depend_Code_ASTNode $node The current root node.
     *
     * @return array
     */
    protected static function collectGraph(PHP_Depend_Code_ASTNode $node)
    {
        $graph = array();
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
     * @param PHP_Depend_Code_ASTNode $node  The root node.
     * @param array                   $graph Expected class structure.
     *
     * @return void
     */
    protected static function assertGraph(PHP_Depend_Code_ASTNode $node, $graph)
    {
        $actual = self::collectGraph($node);
        self::assertEquals($graph, $actual);
    }

    /**
     * Helper method to allow PHPUnit versions < 3.5.x
     *
     * @param string $expected The expected class or interface.
     * @param mixed  $actual   The actual variable/value.
     * @param string $message  Optional error/fail message.
     *
     * @return void
     * @since 0.10.2
     */
    public static function assertInstanceOf($expected, $actual, $message = '')
    {
        if (is_callable(get_parent_class(__CLASS__) . '::') . __FUNCTION__) {
            return parent::assertInstanceOf($expected, $actual, $message);
        }
        return parent::assertType($expected, $actual, $message);
    }

    /**
     * Helper method to allow PHPUnit versions < 3.5.x
     *
     * @param string $expected The expected internal type.
     * @param mixed  $actual   The actual variable/value.
     * @param string $message  Optional error/fail message.
     *
     * @return void
     * @since 0.10.2
     */
    public static function assertInternalType($expected, $actual, $message = '')
    {
        if (is_callable(get_parent_class(__CLASS__) . '::') . __FUNCTION__) {
            return parent::assertInternalType($expected, $actual, $message);
        }
        return parent::assertType($expected, $actual, $message);
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
        return $this->getMock($className, array('__construct'), array(), '', false);
    }

    /**
     * Clears all temporary resources.
     *
     * @param string $dir The root directory.
     *
     * @return void
     */
    private function _clearRunResources($dir = null)
    {
        if ($dir === null) {
            $dir = dirname(__FILE__) . '/_run';
        }

        foreach (new DirectoryIterator($dir) as $file) {
            if ($file == '.' || $file == '..' || $file == '.svn') {
                continue;
            }
            $pathName = realpath($file->getPathname());
            if ($file->isDir()) {
                $this->_clearRunResources($pathName);
                rmdir($pathName);
            } else {
                unlink($pathName);
            }
        }
    }

    /**
     * Creates a test configuration instance.
     *
     * @return PHP_Depend_Util_Configuration
     * @since 0.10.0
     */
    protected function createConfigurationFixture()
    {
        $factory = new PHP_Depend_Util_Configuration_Factory();
        $config  = $factory->createDefault();

        return $config;
    }

    /**
     * Creates a PHP_Depend instance configured with the code resource associated
     * with the calling test case.
     *
     * @return PHP_Depend
     * @since 0.10.0
     */
    protected function createPDependFixture()
    {
        $this->changeWorkingDirectory(
            $this->createCodeResourceURI('config/')
        );

        return new PHP_Depend($this->createConfigurationFixture());
    }

    /**
     * Creates a ready to use class fixture.
     *
     * @param string $name Optional class name.
     *
     * @return PHP_Depend_Code_Class
     * @since 1.0.2
     */
    protected function createClassFixture($name = null)
    {
        $name = $name ? $name : get_class($this);

        $class = new PHP_Depend_Code_Class($name);
        $class->setSourceFile(new PHP_Depend_Code_File($GLOBALS['argv'][0]));
        $class->setCache(new PHP_Depend_Util_Cache_Driver_Memory());
        $class->setContext($this->getMock('PHP_Depend_Builder_Context'));

        return $class;
    }

    /**
     * Creates a ready to use interface fixture.
     *
     * @param string $name Optional interface name.
     *
     * @return PHP_Depend_Code_Interface
     * @since 1.0.2
     */
    protected function createInterfaceFixture($name = null)
    {
        $name = $name ? $name : get_class($this);

        $interface = new PHP_Depend_Code_Interface($name);
        $interface->setSourceFile(new PHP_Depend_Code_File($GLOBALS['argv'][0]));
        $interface->setCache(new PHP_Depend_Util_Cache_Driver_Memory());

        return $interface;
    }

    /**
     * Creates a ready to use trait fixture.
     *
     * @param string $name Optional trait name.
     *
     * @return PHP_Depend_Code_Trait
     * @since 1.0.2
     */
    protected function createTraitFixture($name = null)
    {
        $name = $name ? $name : get_class($this);

        $trait = new PHP_Depend_Code_Trait($name);
        $trait->setCache(new PHP_Depend_Util_Cache_Driver_Memory());

        return $trait;
    }

    /**
     * Creates a ready to use function fixture.
     *
     * @param string $name Optional function name.
     *
     * @return PHP_Depend_Code_Function
     * @since 1.0.2
     */
    protected function createFunctionFixture($name = null)
    {
        $name = $name ? $name : get_class($this);

        $function = new PHP_Depend_Code_Function($name);
        $function->setSourceFile(new PHP_Depend_Code_File($GLOBALS['argv'][0]));
        $function->setCache(new PHP_Depend_Util_Cache_Driver_Memory());
        $function->addChild(new PHP_Depend_Code_ASTFormalParameters());

        return $function;
    }

    /**
     * Creates a ready to use method fixture.
     *
     * @param string $name Optional method name.
     *
     * @return PHP_Depend_Code_Method
     * @since 1.0.2
     */
    protected function createMethodFixture($name = null)
    {
        $name = $name ? $name : get_class($this);

        $method = new PHP_Depend_Code_Method($name);
        $method->setCache(new PHP_Depend_Util_Cache_Driver_Memory());
        $method->addChild(new PHP_Depend_Code_ASTFormalParameters());

        return $method;
    }

    /**
     * Creates a temporary resource for the given file name.
     *
     * @param string $fileName Optional temporary local file name.
     *
     * @return string
     */
    protected static function createRunResourceURI($fileName = null)
    {
        $uri = dirname(__FILE__) . '/_run/' . ($fileName ? $fileName : uniqid());
        if (file_exists($uri) === true) {
            throw new ErrorException("File '{$fileName}' already exists.");
        }
        return $uri;
    }

    /**
     * Creates a code uri for the given file name.
     *
     * @param string $fileName The code file name.
     *
     * @return string
     */
    protected static function createCodeResourceURI($fileName)
    {
        $uri = dirname(__FILE__) . '/../../../resources/files/' . $fileName;
        $uri = realpath($uri);

        if (file_exists($uri) === false) {
            throw new ErrorException("File '{$fileName}' does not exists.");
        }
        return $uri;
    }

    /**
     * Creates a code uri for the calling test case.
     *
     * @return string
     */
    protected static function createCodeResourceUriForTest()
    {
        list($class, $method) = explode('::', self::getCallingTestMethod());

        $parts = explode('_', $class);

        // Strip first two parts
        array_shift($parts);
        array_shift($parts);

        if (preg_match('(\D(\d+Test)$)', end($parts), $match)) {
            array_pop($parts);
            array_push($parts, $match[1]);

            // TODO: Fix this workaround for the existing lower case directories
            array_unshift($parts, strtolower(array_shift($parts)));
        }

        $fileName = substr(join('/', $parts), 0, -4) . "/{$method}";
        try {
            return self::createCodeResourceURI($fileName);
        } catch (ErrorException $e) {
            return self::createCodeResourceURI("{$fileName}.php");
        }
    }

    /**
     * Returns the name of the calling test method.
     *
     * @return string
     */
    protected static function getCallingTestMethod()
    {
        foreach (debug_backtrace() as $frame) {
            if (strpos($frame['function'], 'test') === 0) {
                return "{$frame['class']}::{$frame['function']}";
            }
        }
        throw new ErrorException("No calling test case found.");
    }

    /**
     * Initializes the test environment.
     *
     * @return void
     */
    public static function init()
    {
        // First register autoloader
        spl_autoload_register(array(__CLASS__, 'autoload'));

        // Is it not installed?
        if (is_file(dirname(__FILE__) . '/../../../../main/php/PHP/Depend.php')) {

            $path  = realpath(dirname(__FILE__) . '/../../../../main/php/');
            $path .= PATH_SEPARATOR . get_include_path();
            set_include_path($path);
        }

        // Set test path
        $path  = realpath(dirname(__FILE__) . '/../..');
        $path .= PATH_SEPARATOR . get_include_path();
        set_include_path($path);

        include_once 'PHP/Depend/Code/Filter/Collection.php';

        self::_initVersionCompatibility();
    }

    /**
     * Autoloader for the test cases.
     *
     * @param string $className Name of the missing class.
     *
     * @return void
     */
    public static function autoload($className)
    {
        $file = strtr($className, '_', '/') . '.php';
        if (is_file(dirname(__FILE__) . '/../../../../main/php/PHP/Depend.php')) {
            $file = dirname(__FILE__) . '/../../../../main/php/' . $file;
        }
        if (file_exists($file)) {
            include $file;
        }
    }

    /**
     * There was an api change between PHP 5.3.0alpha3 and 5.3.0beta1, the new
     * extension name "Core" was introduced and interfaces like "Iterator" are
     * now part of "Core" instead of "Standard".
     *
     * @return void
     */
    private static function _initVersionCompatibility()
    {
        $reflection = new ReflectionClass('Iterator');
        $extension  = strtolower($reflection->getExtensionName());
        $extension  = ($extension === '' ? 'standard' : $extension);

        if (defined('CORE_PACKAGE') === false ) {
            define('CORE_PACKAGE', '+' . $extension);
        }
    }

    /**
     * Parses the test code associated with the calling test method.
     *
     * @param boolean $ignoreAnnotations The parser should ignore annotations.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    protected static function parseCodeResourceForTest($ignoreAnnotations = false)
    {
        return self::parseSource(
            self::createCodeResourceUriForTest(),
            $ignoreAnnotations
        );
    }

    /**
     * Parses the given source file or directory with the default tokenizer
     * and node builder implementations.
     *
     * @param string  $testCase          Qualified name of the test case.
     * @param boolean $ignoreAnnotations The parser should ignore annotations.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public static function parseTestCaseSource($testCase, $ignoreAnnotations = false)
    {
        list($class, $method) = explode('::', $testCase);

        $fileName = substr(strtolower($class), 11, strrpos($class, '_') - 11);
        $fileName = str_replace('_', '/', $fileName) . '/' . $method;

        try {
            $fileOrDirectory = self::createCodeResourceURI($fileName);
        } catch (ErrorException $e) {
            $fileOrDirectory = self::createCodeResourceURI($fileName . '.php');
        }

        return self::parseSource($fileOrDirectory, $ignoreAnnotations);
    }

    /**
     * Parses the given source file or directory with the default tokenizer
     * and node builder implementations.
     *
     * @param string  $fileOrDirectory   A source file or a source directory.
     * @param boolean $ignoreAnnotations The parser should ignore annotations.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public static function parseSource($fileOrDirectory, $ignoreAnnotations = false)
    {
        if (file_exists($fileOrDirectory) === false) {
            $fileOrDirectory = self::createCodeResourceURI($fileOrDirectory);
        }

        if (is_dir($fileOrDirectory)) {
            $it = new PHP_Depend_Input_Iterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($fileOrDirectory)
                ),
                new PHP_Depend_Input_ExcludePathFilter(array('.svn'))
            );
        } else {
            $it = new ArrayIterator(array($fileOrDirectory));
        }

        $files = array();
        foreach ($it as $file) {
            if (is_object($file)) {
                $files[] = realpath($file->getPathname());
            } else {
                $files[] = $file;
            }
        }
        sort($files);

        $cache   = new PHP_Depend_Util_Cache_Driver_Memory();
        $builder = new PHP_Depend_Builder_Default();

        foreach ($files as $file) {
            $tokenizer = new PHP_Depend_Tokenizer_Internal();
            $tokenizer->setSourceFile($file);

            $parser = new PHP_Depend_Parser_VersionAllParser(
                $tokenizer,
                $builder,
                $cache
            );
            if ($ignoreAnnotations === true) {
                $parser->setIgnoreAnnotations();
            }

            $parser->parse();
        }
        return $builder->getPackages();
    }
}

PHP_Depend_AbstractTest::init();
