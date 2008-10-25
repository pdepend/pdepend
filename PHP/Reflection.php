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
 * @category  PHP
 * @package   PHP_Reflection
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/Parser.php';
require_once 'PHP/Reflection/Builder/Default.php';
require_once 'PHP/Reflection/Input/CompositeFilter.php';
require_once 'PHP/Reflection/Input/ExcludePathFilter.php';
require_once 'PHP/Reflection/Input/FileFilterIterator.php';
require_once 'PHP/Reflection/Input/FileExtensionFilter.php';
require_once 'PHP/Reflection/Tokenizer/Internal.php';

/**
 * This class implements a simple facade around the native reflection package.
 *
 * @category  PHP
 * @package   PHP_Reflection
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Reflection
{
    /**
     * List of source directories.
     *
     * @type array<string>
     * @var array(string) $_directories
     */
    private $_directories = array();
    
    /**
     * List of php source files.
     *
     * @type array<string>
     * @var array(string) $_files
     */
    private $_files = array();
    
    /**
     * List of allowed file extensions.
     *
     * @type array<string>
     * @var array(string) $_extensions
     */
    private $_extensions = array('php');
    
    /**
     * List of paths to exclude.
     *
     * @type array<string>
     * @var array(string) $_excludePaths
     */
    private $_excludePaths = array('.svn', 'CVS');
    
    /**
     * Should the parse ignore doc comment annotations?
     *
     * @type boolean
     * @var boolean $_withoutAnnotations
     */
    private $_withoutAnnotations = false;

    /**
     * Adds the specified resource to the list of source to be parse.
     *
     * @param string $fileOrDirectory The php source file or directory.
     * 
     * @return void
     */
    public function addInputSource($fileOrDirectory)
    {
        $path = realpath($fileOrDirectory);
        
        if (is_dir($path) === true) {
            $this->_directories[] = $path;
        } else if (is_file($path) === true) {
            $this->_files[] = $path;
        } else {
            throw new RuntimeException("Invalid source '{$fileOrDirectory}' added.");
        }
    }
    
    /**
     * Adds a file extension for valid input files. 
     *
     * @param string $extension The file extension
     * 
     * @return void
     */
    public function addExtension($extension)
    {
        if (in_array($extension, $this->_extensions) === false) {
            $this->_extensions[] = (string) $extension;
        }
    }
    
    /**
     * Sets a list of file extensions for valid input files. This method will
     * replace all previous settings.
     *
     * @param array(string) $extensions List of file extensions.
     * 
     * @return void
     */
    public function setExtensions(array $extensions)
    {
        $this->_extensions = array();
        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }
    }
    
    /**
     * Adds a directory pattern to the list of exclude path. You can use <b>*</b>
     * as wildcard within the <b>$excludePath</b> parameter. 
     *
     * @param string $excludePath The exclude path.
     * 
     * @return void
     */
    public function addExcludePath($excludePath)
    {
        if (in_array($excludePath, $this->_excludePaths) === false) {
            $this->_excludePaths[] = (string) $excludePath;
        }
    }
    
    /**
     * Sets a list of exclude paths patterns. You can use <b>*</b> as wildcard 
     * with the exclude paths. 
     *
     * @param array $excludePaths List of exclude paths.
     * 
     * @return void
     */
    public function setExcludePaths(array $excludePaths)
    {
        $this->_excludePaths = array();
        foreach ($excludePaths as $excludePath) {
            $this->addExcludePath($excludePath);
        }
    }
    
    /**
     * Should the parse ignore doc comment annotations?
     *
     * @return void
     */
    public function setWithoutAnnotations()
    {
        $this->_withoutAnnotations = true;
    }
    
    /**
     * This method parses different directories with php source files and generates
     * a object of the source. 
     *
     * @param string $fileOrDirectory Optional file or directory to parse. 
     * 
     * @return Iterator
     */
    public function parse($fileOrDirectory = null)
    {
        if ($fileOrDirectory !== null) {
            $this->addInputSource($fileOrDirectory);
        }

        $builder   = new PHP_Reflection_Builder_Default();
        $tokenizer = new PHP_Reflection_Tokenizer_Internal();
        
        $parser = new PHP_Reflection_Parser($builder, $tokenizer);
        // Disable annotation parsing?
        if ($this->_withoutAnnotations === true) {
            $parser->setIgnoreAnnotations();
        }
        
        $files = $this->_createInputIterator();
        $parser->parse($files);
        
        return $builder->getPackages();
    }
    
    /**
     * Creates an iterator will for all registered input directories and files.
     *
     * @return Iterator
     */
    private function _createInputIterator()
    {
        // Create new append iterator
        $iterator = new AppendIterator();
        
        // Create filter instance
        $filter = $this->_createInputFilter();
        
        // Append all configured directories
        if (count($this->_directories) > 0) {
            foreach ($this->_directories as $directory) {
                $iterator->append(
                    new PHP_Reflection_Input_FileFilterIterator(
                        new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($directory . '/')
                ), $filter));
            }
        }
        // Append single files
        if (count($this->_files) > 0) {
            $iterator->append(new ArrayIterator($this->_files));
        }
        
        $files = iterator_to_array($iterator);
        $files = array_unique($files);
        
        return new ArrayIterator($files);
    }
    
    /**
     * Creates an input filter based on the registered file extensions and 
     * exclude directories.
     *
     * @return PHP_Reflection_Input_FilterI
     */
    private function _createInputFilter()
    {
        $composite = new PHP_Reflection_Input_CompositeFilter();
        
        $extensions = $this->_extensions;
        if (count($extensions) > 0) {
            $filter = new PHP_Reflection_Input_FileExtensionFilter($extensions);
            $composite->append($filter);
        }
        
        $excludePaths = $this->_excludePaths;
        if (count($excludePaths) > 0) {
            $filter = new PHP_Reflection_Input_ExcludePathFilter($excludePaths);
            $composite->append($filter);
        }
        
        return $composite;
    }
}