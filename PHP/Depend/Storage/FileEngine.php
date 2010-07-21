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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Storage
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once 'PHP/Depend/Storage/AbstractEngine.php';
require_once 'PHP/Depend/Util/FileUtil.php';

/**
 * This class implements a simple file backend that storages the data in a file.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Storage
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Storage_FileEngine extends PHP_Depend_Storage_AbstractEngine
{
    /**
     * The root directory for this storage engine.
     *
     * @var string $_dirname
     */
    private $_dirname = '';

    /**
     * Microtime when the engine instance was created.
     *
     * @var string
     */
    private $_engineInstanceKey = '';

    /**
     * List of all storage groups that were used with this storage engine.
     *
     * @var array(string=>string) $_groups
     */
    private $_groups = array();

    /**
     * Constructs a new file storage instance and calculates the root directory
     * for the file storage.
     *
     * @param string $cacheDir Optional cache directory.
     */
    public function __construct($cacheDir = null)
    {
        $this->_engineInstanceKey = strtr(microtime(), ' ', '_');

        if ($cacheDir === null) {
            $cacheDir = PHP_Depend_Util_FileUtil::getSysTempDir();
        }

        $this->_dirname = $cacheDir . '/pdepend_storage';

        // Append the user identifier on *NIX systems
        if (function_exists('posix_getuid') === true) {
            $this->_dirname .= '-' . posix_getuid();
        }
    }

    /**
     * This method will store the given <b>$data</b> record under a key that is
     * build from the other parameters.
     *
     * @param mixed  $data    The data object that should be stored.
     * @param string $key     A unique identifier for the given <b>$data</b>.
     * @param string $group   A data group identifier which can be used to group
     *                        records.
     * @param mixed  $version An optional version for the stored record.
     *
     * @return void
     */
    public function store($data, $key, $group, $version = '@package_version@')
    {
        $pathname = $this->_createPathname($key, $group, $version);

        $fp = fopen($pathname, 'w');
        flock($fp, LOCK_EX);
        fwrite($fp, serialize($data));
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * This method will restore a record and return it to the calling client.
     * The return value will be <b>null</b> if no record for the given identifier
     * exists.
     *
     * @param string $key     A unique identifier for the given <b>$data</b>.
     * @param string $group   A data group identifier which can be used to group
     *                        records.
     * @param mixed  $version An optional version for the stored record.
     *
     * @return mixed
     */
    public function restore($key, $group, $version = '@package_version@')
    {
        $pathname = $this->_createPathname($key, $group, $version);
        if (file_exists($pathname)) {
            $fp = fopen($pathname, 'r');
            flock($fp, LOCK_EX);

            $data = unserialize(fread($fp, filesize($pathname)));

            flock($fp, LOCK_UN);
            fclose($fp);

            return $data;
        }
        return null;
    }

    /**
     * This method implements a garbage collection mechanism for this storage
     * engine.
     *
     * @return void
     * @see PHP_Depend_Storage_AbstractEngine#garbageCollect()
     */
    protected function garbageCollect($version = '@package_version@')
    {
        $directories = array($this->_dirname);
        if (count($this->_groups) > 0) {
            foreach ($this->_groups as $group) {
                $directories[] = $this->_dirname . '/' . $group;
            }
        }

        $lifetime = time() - $this->getMaxLifetime();

        if ($this->hasPrune()) {
            $pattern = '*.' . $this->_engineInstanceKey . '.' . $version . '.data';
        } else {
            $pattern = '*.data';
        }

        foreach ($directories as $directory) {
            foreach (glob($directory . '/' . $pattern) as $filename) {
                if (filemtime($filename) < $lifetime) {
                    @unlink($filename);
                }
            }
        }
    }

    /**
     * This method creates a qualified class name for a record.
     *
     * @param string $key     A unique identifier for the given <b>$data</b>.
     * @param string $group   A data group identifier which can be used to group
     *                        records.
     * @param mixed  $version An optional version for the stored record.
     *
     * @return string
     */
    private function _createPathname($key, $group, $version)
    {
        $storageDirname = $this->_dirname;

        if ($group !== null) {
            // Store group for garbage collection
            $this->_groups[$group] = $group;

            $storageDirname .= '/' . $group;
        }

        if (file_exists($storageDirname) === false) {
            mkdir($storageDirname, 0755, true);
        }

        if ($this->hasPrune()) {
            $key .= '.' . $this->_engineInstanceKey;
        }

        return $storageDirname . '/' . $key . '.' . $version . '.data';
    }
}