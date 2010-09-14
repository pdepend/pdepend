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

require_once 'PHP/Depend/Parser.php';
require_once 'PHP/Depend/Code/DefaultBuilder.php';
require_once 'PHP/Depend/Code/Tokenizer/InternalTokenizer.php';
require_once 'PHP/Depend/Util/PHPFilterIterator.php';

/**
 * PHP_Depend analyzes php class files and generates metrics.
 * 
 * The PHP_Depend is a php port/adaption of the Java class file analyzer 
 * <a href="http://clarkware.com/software/JDepend.html">JDepend</a>.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend
{
    /**
     * List of source directories.
     *
     * @type array<string>
     * @var array(string) $directories
     */
    protected $directories = array();
    
    /**
     * Generated {@link PHP_Depend_Metrics_PackageMetrics} objects.
     *
     * @type Iterator
     * @var Iterator $packages
     */
    protected $packages = null;

    /**
     * Adds the specified directory to the list of directories to be analyzed.
     *
     * @param string $directory The php source directory.
     * 
     * @return void
     */
    public function addDirectory($directory)
    {
        $dir = realpath($directory);
        
        if (!is_dir($dir)) {
            throw new RuntimeException('Invalid directory added.');
        }
        
        $this->directories[] = $dir;
    }
    
    /**
     * Analyzes the registered directories and returns the collection of 
     * analyzed packages.
     *
     * @return Iterator
     */
    public function analyze()
    {
        $iterator = new AppendIterator();
        
        foreach ($this->directories as $directory) {
            $iterator->append(new PHP_Depend_Util_PHPFilterIterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($directory)
                )
            ));
        }
        
        $builder = new PHP_Depend_Code_DefaultBuilder();

        foreach ( $iterator as $file ) {
            $parser = new PHP_Depend_Parser(
                new PHP_Depend_Code_Tokenizer_InternalTokenizer($file), $builder
            );
            $parser->parse();
        }

        $visitor = new PHP_Depend_Metrics_PackageMetricsVisitor();

        foreach ($builder as $pkg) {
            $pkg->accept($visitor);
        }
        $this->packages = $visitor->getPackageMetrics();
        
        return $this->packages;
    }
    
    /**
     * Returns the number of analyzed php classes and interfaces.
     *
     * @return integer
     */
    public function countClasses()
    {
        if ($this->packages === null) {
            throw new RuntimeException('countClasses() doesn\'t work before the source was analyzed.');
        }
        
        $classes = 0;
        foreach ($this->packages as $package) {
            $classes += $package->getTotalClassCount();
        }
        return $classes;
    }
    
    /**
     *  Returns the number of analyzed packages.
     *
     * @return integer
     */
    public function countPackages()
    {
        if ($this->packages === null) {
            throw new RuntimeException('countPackages() doesn\'t work before the source was analyzed.');
        }
        // TODO: This is internal knownhow, it is an ArrayIterator
        //       Replace it with a custom iterator interface
        return $this->packages->count();
    }
    
    /**
     * Returns the analyzed package of the specified name.
     *
     * @param string $name The package name.
     * 
     * @return PHP_Depend_Metrics_PackageMetrics
     */
    public function getPackage($name)
    {
        if ($this->packages === null) {
            throw new RuntimeException('getPackage() doesn\'t work before the source was analyzed.');
        }
        foreach ($this->packages as $package) {
            if ($package->getName() === $name) {
                return $package;
            }
        }
        throw new OutOfBoundsException(sprintf('Unknown package "%s".', $name));
    }
    
    /**
     * Returns an iterator of the analyzed packages.
     *
     * @return Iterator
     */
    public function getPackages()
    {
        if ($this->packages === null) {
            throw new RuntimeException('getPackages() doesn\'t work before the source was analyzed.');
        }
        return $this->packages;
    }
}