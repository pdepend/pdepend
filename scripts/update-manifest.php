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
 * This script updates the PEAR-Package-Manifest.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_ManifestUpdater
{
    /**
     * The pear manifest file.
     *
     * @var string $_manifestFile
     */
    private $_manifestFile = null;

    /**
     * Constructs a new updater.
     *
     */
    public function __construct()
    {
        $this->_manifestFile = dirname(__FILE__) . '/../src/conf/package.xml';

        $manifest = $this->_createManifest();
        $struct   = $this->_createContentStruct();

        $contents = $manifest->getElementsByTagName('dir')->item(0);

        foreach ($struct as $key => $value) {
            $this->_insertContents($contents, array($key => $value[1]), $value[0]);
        }

        $manifest->save($this->_manifestFile);
    }

    /**
     * Inserts the new content structure.
     *
     * @param DOMElement $parent The parent directory element.
     * @param array      $struct The content structure.
     * @param string     $role   The pear manifest role.
     *
     * @return void
     */
    private function _insertContents(DOMElement $parent, array $struct, $role)
    {
        $manifest = $parent->ownerDocument;

        foreach ($struct as $name => $value) {
            if (is_array($value)) {
                $item = $manifest->createElement('dir');
                $item->setAttribute('name', $name);

                $this->_insertContents($item, $value, $role);
            } else {
                $item = $manifest->createElement('file');
                $item->setAttribute('name', $name);
                $item->setAttribute('role', $role);

                $task = $manifest->createElement('tasks:replace');
                $task->setAttribute('from', '@package_version@');
                $task->setAttribute('to', 'version');
                $task->setAttribute('type', 'package-info');

                $item->appendChild($task);
            }

            $parent->appendChild($item);
        }
    }

    /**
     * Creates the raw manifest without source contents.
     *
     * @return DOMDocument
     */
    private function _createManifest()
    {
        $manifest = new DOMDocument('1.0', 'UTF-8');

        $manifest->formatOutput       = true;
        $manifest->preserveWhiteSpace = false;

        $manifest->load($this->_manifestFile);

        $xpath = new DOMXPath($manifest);
        $xpath->registerNamespace('a', 'http://pear.php.net/dtd/package-2.0');

        $result = $xpath->query('//a:contents/a:dir[@name="/"]/a:dir');

        foreach ($result as $item) {
            $item->parentNode->removeChild($item);
        }
        return $manifest;
    }

    /**
     * Creates the content structure.
     *
     * @return array(string => array)
     */
    private function _createContentStruct()
    {
        $struct = array(
            'PHP'  =>  array('php', array()),
            // 'tests/PHP/Depend'  =>  array('test', array()),
        );

        foreach (array_keys($struct) as $name) {
            $contents         = dirname(__FILE__) . "/../src/main/php/{$name}";
            $struct[$name][1] = $this->_readContent($contents);
        }
        return $struct;
    }

    /**
     * Reads all manifest contents.
     *
     * @param string $dir The source directory.
     *
     * @return array(string => boolean|array)
     */
    private function _readContent($dir)
    {
        $struct = array();
        $files  = new DirectoryIterator($dir);
        foreach ($files as $file) {
            if ($file->isDot() || strpos($file->getFilename(), '.') === 0) {
                continue;
            }

            $fileName = $file->getFilename();
            if ($file->isDir()) {
                $struct[$fileName] = $this->_readContent($file->getPathname());
            } else {
                $struct[$fileName] = true;
            }
        }

        uasort($struct, array($this, '_sortContent'));

        return $struct;
    }

    /**
     * Sorts the content entries by type.
     *
     * @param boolean|array $contentA First sort entry.
     * @param boolean|array $contentB Second sort entry.
     *
     * @return integer
     */
    private function _sortContent($contentA, $contentB)
    {
        return strcmp(gettype($contentA), gettype($contentB));
    }

    /**
     * Starts the update process.
     *
     * @param array $args The cli arguments.
     *
     * @return void
     */
    public static function main(array $args)
    {
        $updater = new PHP_Depend_ManifestUpdater();
    }
}

PHP_Depend_ManifestUpdater::main($_SERVER['argv']);
