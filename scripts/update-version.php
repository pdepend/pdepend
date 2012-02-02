#!/usr/bin/env php
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
 * Utility class that we use to recalculate the cache hash/version.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_CacheVersionUpdater
{
    /**
     * The source directory.
     *
     * @var string
     */
    private $_rootDirectory = null;

    /**
     * The source sub directories that we will process.
     *
     * @var array(string)
     */
    private $_localPaths = array(
        '/Builder',
        '/Code',
        '/Metrics',
        '/Parser',
        '/Tokenizer',
        '/Parser.php',
        '/ConstantsI.php'
    );

    /**
     * The target file, where this script will persist the new cache version.
     *
     * @var string
     */
    private $_targetFile = '/Util/Cache/Driver.php';

    /**
     * Regular expression used to replace a previous cache version.
     *
     * @var string
     */
    private $_targetRegexp = '(@version:[a-f0-9]{32}:@)';

    /**
     * Constructs a new cache version updater instance.
     */
    public function __construct()
    {
        $this->_rootDirectory = realpath(dirname(__FILE__) . '/../src/main/php/PHP/Depend');
    }

    /**
     * Processes all source files and generates a combined version for all files.
     * The it replaces the old version key within the project source with the
     * newly calculated value.
     *
     * @return void
     */
    public function run()
    {
        $checksum = '';

        foreach ($this->_localPaths as $localPath) {
            $path = $this->_rootDirectory . $localPath;
            foreach ($this->readFiles($path) as $file) {
                $checksum = $this->hash($file, $checksum);
            }
        }

        $file = $this->_rootDirectory . $this->_targetFile;

        $code = file_get_contents($file);
        $code = preg_replace($this->_targetRegexp, "@version:{$checksum}:@", $code);
        file_put_contents($file, $code);
    }

    /**
     * Generates a hash value for the given <b>$path</b> in combination with a
     * previous calculated <b>$checksum</b>.
     *
     * @param string $path     Path to the current context file.
     * @param string $checksum Hash/Checksum for all previously parsed files.
     *
     * @return string
     */
    protected function hash($path, $checksum)
    {
        return md5($checksum . md5_file($path));
    }

    /**
     * Reads all files below the given <b>$path</b>.
     *
     * @param string $path The parent directory or a file.
     *
     * @return array(string)
     */
    protected function readFiles($path)
    {
        if ($this->accept($path)) {
            return array($path);
        }
        $files = array();
        foreach ($this->createFileIterator($path) as $file) {
            if ($this->accept($file)) {
                $files[] = (string) $file;
            }
        }
        return $files;
    }

    /**
     * Does the given path represent a file that has the expected file extension?
     *
     * @param string $path Path to a file or directory.
     *
     * @return boolean
     */
    protected function accept($path)
    {
        return (is_file($path) && '.php' === substr($path, -4, 4));
    }

    /**
     * Creates an iterator with all files below the given directory.
     *
     * @param string $path Path to a directory.
     *
     * @return Iterator
     */
    protected function createFileIterator($path)
    {
        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path)
        );
    }

    /**
     * The main method starts the cache version updater.
     *
     * @param array $args Cli arguments.
     */
    public static function main(array $args)
    {
        $updater = new PHP_Depend_CacheVersionUpdater();
        $updater->run();
    }
}

PHP_Depend_CacheVersionUpdater::main($argv);
