<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Abstract test case implementation for the PHP_Depend package.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Removes test contents of a previous crached test run.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        
        // Remove old test contents
        self::_clearRun();
    }
    
    /**
     * Resets the global iterator filter.
     *
     * @return void
     */
    protected function tearDown()
    {
        // Reset code filter
        // TODO: PHP_Reflection code should not be needed here
        PHP_Reflection_AST_Iterator_StaticFilter::getInstance()->clear();
        
        // Remove test contents
        self::_clearRun();
        
        // Call parent tear down
        parent::tearDown();
    }
    
    /**
     * Initializes the test environment.
     *
     * @return void
     */
    public static function init()
    {
        // Is it not installed?
        if (is_file(dirname(__FILE__) . '/../../../PHP/Depend.php')) {
            
            $path  = realpath(dirname(__FILE__) . '/../../..');
            $path .= PATH_SEPARATOR . get_include_path();
            set_include_path($path);
            
            $whitelist = realpath(dirname(__FILE__) . '/../../../PHP') . '/';
            PHPUnit_Util_Filter::addDirectoryToWhitelist($whitelist);
        }
        
        // Set test path
        $path  = realpath(dirname(__FILE__) . '/../..') ;
        $path .= PATH_SEPARATOR . get_include_path();
        set_include_path($path);
        
        include_once 'PHP/Reflection/AST/Iterator/StaticFilter.php';
    }
    
    /**
     * Creates a resource uri for a file or directory within the test code
     * directory.
     *
     * @param string $fileOrDirectory A file or directory name.
     * 
     * @return string
     */
    protected static function createResourceURI($fileOrDirectory)
    {
        $uri = dirname(__FILE__) . '/_code/' . $fileOrDirectory;
        if (file_exists($uri) === false) {
            throw new ErrorException("Unknown file or directory '{$fileOrDirectory}'.");
        }
        return realpath($uri);
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
     * Parses the given source file or directory with the default tokenizer
     * and node builder implementations.
     *
     * @param string  $fileOrDirectory   A source file or a source directory.
     * @param boolean $ignoreAnnotations The parser should ignore annotations.
     * 
     * @return PHP_Reflection_AST_Iterator
     */
    protected static function parseSource($fileOrDirectory, $ignoreAnnotations = false)
    {
        // Include the reflection facade
        include_once 'PHP/Reflection.php';
        
        // Create a new clean reflection facade instance
        $reflection = new PHP_Reflection();
        
        // Should we ignore annotations?
        if ($ignoreAnnotations === true) {
            $reflection->setWithoutAnnotations();
        }
        // Parse source and return result
        return $reflection->parse(self::createResourceURI($fileOrDirectory));
    }
    
    /**
     * Removes all contents from the test temp run directory.
     *
     * @return void
     */
    private static function _clearRun()
    {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(dirname(__FILE__) . '/_run/')
        );
        // Remove all files, links etc.
        foreach ($it as $file) {
            $path = $file->getPathname();
            if ($file->isDir() === false && strpos($path, '.svn') === false) {
                unlink($file->getPathname());
            }
        }
        // Remove all directories
        foreach ($it as $file) {
            if ($file->isDir() === false) {
                continue;
            }
            $name = $file->getFilename();
            $path = $file->getPathname();
            if ($name === '.' || $name === '..' || strpos($path, '.svn') !== false) {
                continue;
            }
            rmdir($name);
        }
    }
}

PHP_Depend_AbstractTest::init();