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

class PHP_Depend
{
    protected $directories = array();
    
    protected $packages = null;

    public function addDirectory($directory)
    {
        $dir = realpath($directory);
        
        if (!is_dir($dir)) {
            throw new RuntimeException('Invalid directory added.');
        }
        
        $this->directories[] = $dir;
    }
    
    public function analyze()
    {
        $iterator = new AppendIterator();
        
        foreach ($this->directories as $directory) {
            $iterator->append(
                new PHP_Depend_Util_PHPFilterIterator( 
                    new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($directory)
                    )
                )
            );
        }
        
        $builder = new PHP_Depend_Code_DefaultBuilder();

        foreach ( $iterator as $file ) 
        {
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
    
    public function countClasses()
    {
        if ($this->packages === null) {
            throw new RuntimeException('Invalid state');
        }
        
        $classes = 0;
        foreach ($this->packages as $package) {
            $classes += $package->getTC();
        }
        return $classes;
    }
    
    public function getPackage($name)
    {
        if ($this->packages === null) {
            throw new RuntimeException('Invalid state');
        }
        foreach ($this->packages as $package) {
            if ($package->getName() === $name) {
                return $package;
            }
        }
        return null;
    }
    
    public function getPackages()
    {
        if ($this->packages === null) {
            throw new RuntimeException('Invalid state');
        }
        return $this->packages;
    }
    
    public function countPackages()
    {
        if ($this->packages === null) {
            throw new RuntimeException('Invalid state');
        }
        // TODO: This is internal knownhow, it is an ArrayIterator
        //       Replace it with a custom iterator interface
        return $this->packages->count();
    }
}