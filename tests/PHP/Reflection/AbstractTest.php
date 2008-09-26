<?php
/**
 * This file is part of PHP_Reflection.
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
 * @package   PHP_Reflection
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Abstract test case implementation for the PHP_Reflection package.
 *
 * @category  QualityAssurance
 * @package   PHP_Reflection
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
abstract class PHP_Reflection_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Resets the global iterator filter.
     *
     * @return void
     */
    protected function tearDown()
    {
        PHP_Reflection_Ast_Iterator_StaticFilter::getInstance()->clear();
        
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
        if (is_file(dirname(__FILE__) . '/../../../PHP/Reflection.php')) {
            
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
        
//        include_once 'PHP/Depend/Code/Iterator/StaticFilter.php';
    }
    
    /**
     * Parses the given source file or directory with the default tokenizer
     * and node builder implementations.
     *
     * @param string  $fileOrDirectory   A source file or a source directory.
     * @param boolean $ignoreAnnotations The parser should ignore annotations.
     * 
     * @return PHP_Reflection_Ast_Iterator
     */
    protected static function parseSource($fileOrDirectory, $ignoreAnnotations = false)
    {
        include_once 'PHP/Reflection/Parser.php';
        include_once 'PHP/Reflection/Builder/Default.php';
        include_once 'PHP/Reflection/Ast/Iterator/StaticFilter.php';
        include_once 'PHP/Reflection/Tokenizer/Internal.php';
        include_once 'PHP/Reflection/Input/ExcludePathFilter.php';
        include_once 'PHP/Reflection/Input/FileFilterIterator.php';
        
        $fileOrDirectory = self::createResourceURI($fileOrDirectory);
        if (is_dir($fileOrDirectory)) {
            $it = new PHP_Reflection_Input_FileFilterIterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($fileOrDirectory)
                ),
                new PHP_Reflection_Input_ExcludePathFilter(array('.svn'))
            );
        } else {
            $it = new ArrayIterator(array($fileOrDirectory));
        }
        
        $tokenizer = new PHP_Reflection_Tokenizer_Internal();
        $builder   = new PHP_Reflection_Builder_Default();
        $parser    = new PHP_Reflection_Parser($builder, $tokenizer);
    
        if ($ignoreAnnotations === true) {
            $parser->setIgnoreAnnotations();
        }
        $parser->parse($it);
        return $builder->getPackages();
    }
    
    /**
     * Creates a valid uri for the given file or directory name. If the file
     * doesn't exist this method will return <b>false</b>.
     *
     * @param string $fileOrDirectory Local file or directory name.
     * 
     * @return string|boolean
     */
    protected static function createResourceURI($fileOrDirectory)
    {
        return realpath(dirname(__FILE__) . '/_code/' . $fileOrDirectory);
    }
}

PHP_Reflection_AbstractTest::init();
?>
