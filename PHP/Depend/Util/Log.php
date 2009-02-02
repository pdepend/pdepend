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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

/**
 * Simple logging class for debug messages and extended information.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
final class PHP_Depend_Util_Log
{
    /**
     * Log nothing.
     */
    const NONE = 0;

    /**
     * Log debug messages.
     */
    const DEBUG = 1;

    /**
     * The log output stream, defaults for the moment to stderr.
     *
     * @var resource $_stream
     */
    private static $_stream = STDERR;

    /**
     * The log severity levels, this can be an OR combined list of valid severities.
     *
     * @var integer $_severity
     */
    private static $_severity = self::NONE;

    /**
     * Sets the log severity levels, this can be an OR combined list of valid
     * severities.
     *
     * @param integer $severity The log severity levels.
     *
     * @return void
     */
    public static function setSeverity($severity)
    {
        self::$_severity = $severity;
    }

    /**
     * Logs the given message with debug severity.
     *
     * @param string $message The debug log message.
     *
     * @return void
     */
    public static function debug($message)
    {
        self::log(self::DEBUG, $message);
    }

    /**
     * Generic log method for all severities.
     *
     * @param integer $severity The log severity.
     * @param string  $message  The log message.
     *
     * @return void
     */
    public static function log($severity, $message)
    {
        if ((self::$_severity & $severity) === $severity) {
            fwrite(self::$_stream, PHP_EOL . $message);
        }
    }
}
?>
