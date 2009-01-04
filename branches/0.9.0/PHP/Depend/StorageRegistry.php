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
 * @category  PHP
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.pdepend.org/
 */

/**
 * The main storage registry.
 *
 * @category  PHP
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.pdepend.org/
 */
final class PHP_Depend_StorageRegistry
{
    /**
     * This property holds all registered storage instances.
     *
     * @var array(string=>PHP_Depend_Storage_AbstractEngine) $_engines
     */
    private static $_engines = array();

    /**
     * This method will return a configured storage instance for this given
     * <b>$name</b>, otherwise it throws an exception if no storage exists for
     * this identifier.
     *
     * @param string $name The storage identifier.
     *
     * @return PHP_Depend_Storage_AbstractEngine
     * @throws InvalidArgumentException When no storage engine was registered for
     *                                  the given <b>$name</b>.
     */
    public static function get($name)
    {
        if (isset(self::$_engines[$name])) {
            return self::$_engines[$name];
        }
        $message = sprintf('Invalid storage identifier "%s" given.', $name);
        throw new InvalidArgumentException($message);
    }

    /**
     * This method can be used to register a storage engine under the given
     * identifier <b>$name</b>.
     *
     * @param string                            $name   The engine identifier.
     * @param PHP_Depend_Storage_AbstractEngine $engine The used storage engine.
     *
     * @return void
     */
    public static function set($name, PHP_Depend_Storage_AbstractEngine $engine)
    {
        self::$_engines[$name] = $engine;
    }
}
?>
