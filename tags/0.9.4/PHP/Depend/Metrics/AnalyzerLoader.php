<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Metrics/AggregateAnalyzerI.php';

/**
 * This class provides a simple way to load all required analyzers by class,
 * implemented interface or parent class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_AnalyzerLoader implements IteratorAggregate
{
    /**
     * Mapping of all installed analyzers.
     *
     * @var array(string=>string) $_installedAnalyzers
     */
    private $_installedAnalyzers = null;

    /**
     * All matching analyzer instances.
     *
     * @var array(PHP_Depend_Metrics_AnalyzerI) $_analyzers
     */
    private $_analyzers = array();

    /**
     * Constructs a new analyzer loader.
     *
     * @param array(string)        $acceptedTypes Accepted/expected analyzer types.
     * @param array(string=>mixed) $options       List of cli options.
     */
    public function __construct(array $acceptedTypes, array $options = array())
    {
        $this->_loadAcceptedAnalyzers($acceptedTypes, $options);
    }

    /**
     * Returns a countable iterator of {@link PHP_Depend_Metrics_AnalyzerI}
     * instances that match against the given accepted types.
     *
     * @return Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_analyzers);
    }

    /**
     * Loads all accepted node analyzers.
     *
     * @param array(string)        $acceptedTypes Accepted/expected analyzer types.
     * @param array(string=>mixed) $options       List of cli options.
     *
     * @return array(PHP_Depend_Metrics_AnalyzerI)
     */
    private function _loadAcceptedAnalyzers(array $acceptedTypes, array $options)
    {
        // First init list of installed analyzers
        $this->_initInstalledAnalyzers();

        $analyzers = array();
        foreach ($this->_installedAnalyzers as $fileName => $className) {

            // Include class definition
            include_once $fileName;

            $parents    = class_parents($className, false);
            $implements = class_implements($className, false);

            $providedTypes = array($className);
            $providedTypes = array_merge($providedTypes, $parents);
            $providedTypes = array_merge($providedTypes, $implements);

            // Skip if this analyzer doesn't provide an accepted type
            if (count(array_intersect($acceptedTypes, $providedTypes)) === 0) {
                continue;
            }

            // Fist check for already loaded instance
            if (isset($this->_analyzers[$className])) {
                // Store reference
                $analyzers[] = $this->_analyzers[$className];

                continue;
            }
            // Create a new instance
            $analyzer = new $className($options);

            if ($analyzer instanceof PHP_Depend_Metrics_AggregateAnalyzerI) {
                $classNames = $analyzer->getRequiredAnalyzers();
                $required   = $this->_loadAcceptedAnalyzers($classNames, $options);

                foreach ($required as $requiredAnalyzer) {
                    $analyzer->addAnalyzer($requiredAnalyzer);
                }
            }

            // Add analyzer to the return value array
            $analyzers[] = $analyzer;

            // Add analyzer to global array
            $this->_analyzers[$className] = $analyzer;
        }

        return $analyzers;
    }

    /**
     * Loads a list of all installed analyzers.
     *
     * @return void
     */
    private function _initInstalledAnalyzers()
    {
        // Only load once
        if ($this->_installedAnalyzers !== null) {
            return;
        }

        // Init object property
        $this->_installedAnalyzers = array();

        $dirs = new DirectoryIterator(dirname(__FILE__));
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

                $this->_installedAnalyzers[$file->getPathname()] = $className;
            }
        }
    }
}