<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHPUnit/Framework/TestCase.php';

require_once 'PHP/Depend/Parser.php';
require_once 'PHP/Depend/Code/DefaultBuilder.php';
require_once 'PHP/Depend/Code/Tokenizer/InternalTokenizer.php';
require_once 'PHP/Depend/Util/ExcludePathFilter.php';
require_once 'PHP/Depend/Util/FileFilterIterator.php';

/**
 * Abstract test case implementation for the PHP_Depend package.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_AbstractTest extends PHPUnit_Framework_TestCase
{
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
    }
    
    /**
     * Parses the given source file or directory with the default tokenizer
     * and node builder implementations.
     *
     * @param string $fileOrDirectory A source file or a source directory.
     * 
     * @return PHP_Depend_Code_NodeIterator
     */
    public static function parseSource($fileOrDirectory)
    {
        if (is_dir($fileOrDirectory)) {
            $it = new PHP_Depend_Util_FileFilterIterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($fileOrDirectory)
                ),
                new PHP_Depend_Util_ExcludePathFilter(array('.svn'))
            );
        } else {
            $it = new ArrayIterator(array($fileOrDirectory));
        }
        
        $builder = new PHP_Depend_Code_DefaultBuilder();
        
        foreach ($it as $file) {
            $tokenizer = new PHP_Depend_Code_Tokenizer_InternalTokenizer($file);
            $parser    = new PHP_Depend_Parser($tokenizer, $builder);

            $parser->parse();
        }
        return $builder->getPackages();
    }
}

PHP_Depend_AbstractTest::init();