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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 * @since      0.9.10
 */

require_once 'PHP/Depend/Metrics/AnalyzerClassLocator.php';

/**
 * Locator that searches for PHP_Depend analyzers that follow the PHP_Depend
 * convention and are present the PHP_Depend source tree.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * @since      0.9.10
 */
class PHP_Depend_Metrics_AnalyzerClassFileSystemLocator
    implements PHP_Depend_Metrics_AnalyzerClassLocator
{
    /**
     * The root search directory.
     *
     * @var string
     */
    private $_searchDirectory = null;

    /**
     * Mapping of installed analyzer class files and classes.
     *
     * @var array(string=>string)
     */
    private $_analyzers = null;

    /**
     * Constructs a new locator instance.
     *
     * @param string $searchDirectory The root search directory.
     */
    public function __construct($searchDirectory = null)
    {
        if ($searchDirectory === null) {
            $this->_searchDirectory = dirname(__FILE__);
        } else {
            $this->_searchDirectory = $searchDirectory;
        }
    }

    /**
     * Returns an associative array with analyzer source files and the corresponding
     * analyzer class.
     *
     * @return array(string=>string)
     */
    public function find()
    {
        if ($this->_analyzers === null) {
            $this->_analyzers = $this->_find();
        }
        return $this->_analyzers;
    }

    /**
     * Performs a recursive search for analyzers in the configured search
     * directory.
     *
     * @return array(string=>string)
     */
    private function _find()
    {
        $result = array();

        $dirs = new DirectoryIterator($this->_searchDirectory);
        foreach ($dirs as $dir) {
            if (!$dir->isDir() || $dir->isDot()) {
                continue;
            }
            $files = new DirectoryIterator($dir->getPathname());
            foreach ($files as $file) {
                if ($file->getFilename() !== 'Analyzer.php') {
                    continue;
                }
                include_once $file->getPathname();

                $package   = $dir->getFilename();
                $className = sprintf('PHP_Depend_Metrics_%s_Analyzer', $package);

                $result[$file->getPathname()] = $className;
            }
        }
        return $result;
    }
}