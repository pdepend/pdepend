<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Abstract test case implementation for the PHP_Depend package.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_AbstractTest extends PHPUnit_Framework_TestCase
{
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
        include_once 'PHP/Depend/StorageRegistry.php';
        include_once 'PHP/Depend/Storage/MemoryEngine.php';

        PHP_Depend_StorageRegistry::set(
            PHP_Depend::TOKEN_STORAGE,
            new PHP_Depend_Storage_MemoryEngine()
        );
        PHP_Depend_StorageRegistry::set(
            PHP_Depend::PARSER_STORAGE,
            new PHP_Depend_Storage_MemoryEngine()
        );

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

        parent::tearDown();
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
     * Creates a temporary resource for the given file name.
     *
     * @param string $fileName The temporary file name.
     *
     * @return string
     */
    protected static function createRunResourceURI($fileName)
    {
        $uri = dirname(__FILE__) . '/_run/' . $fileName;
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
        $uri = dirname(__FILE__) . '/_code/' . $fileName;
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
        if (is_file(dirname(__FILE__) . '/../../../PHP/Depend.php')) {

            $path  = realpath(dirname(__FILE__) . '/../../..');
            $path .= PATH_SEPARATOR . get_include_path();
            set_include_path($path);

            PHP_CodeCoverage_Filter::getInstance()->addDirectoryToWhitelist(
                realpath(dirname(__FILE__) . '/../../../PHP') . '/'
            );
        }

        // Set test path
        $path  = realpath(dirname(__FILE__) . '/../..') ;
        $path .= PATH_SEPARATOR . get_include_path();
        set_include_path($path);

        include_once 'PHP/Depend/Code/Filter/Collection.php';

        self::initVersionCompatibility();
    }

    public static function autoload($className)
    {
        $file = strtr($className, '_', '/') . '.php';
        if (is_file(dirname(__FILE__) . '/../../../PHP/Depend.php')) {
            $file = dirname(__FILE__) . '/../../../' . $file;
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
    private static function initVersionCompatibility()
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
        return self::parseSource(self::createCodeResourceUriForTest(), $ignoreAnnotations);
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
        include_once 'PHP/Depend/Parser/VersionAllParser.php';
        include_once 'PHP/Depend/Builder/Default.php';
        include_once 'PHP/Depend/Code/Filter/Collection.php';
        include_once 'PHP/Depend/Tokenizer/Internal.php';
        include_once 'PHP/Depend/Input/ExcludePathFilter.php';
        include_once 'PHP/Depend/Input/Iterator.php';

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

        $builder = new PHP_Depend_Builder_Default();

        foreach ($files as $file) {
            $tokenizer = new PHP_Depend_Tokenizer_Internal();
            $tokenizer->setSourceFile($file);

            $parser = new PHP_Depend_Parser_VersionAllParser($tokenizer, $builder);
            if ($ignoreAnnotations === true) {
                $parser->setIgnoreAnnotations();
            }

            $parser->parse();
        }
        return $builder->getPackages();
    }
}

PHP_Depend_AbstractTest::init();
