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
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

/**
 * This factory creates singleton instances of available loggers.
 * 
 * The identifiers used for loggers follow a simple convention. Every upper case
 * word in the class file name and the logger directory is separated by a hyphen.
 * Only the last word of an identifier is used for the class file name, all
 * other words are used for the directory name.
 * 
 * <code>
 *   --my-custom-log-xml
 * </code>
 * 
 * Refers to the following file: <b>PHP/Depend/Log/MyCustomLog/Xml.php</b>, but 
 * you can not reference a file named <b>PHP/Depend/Log/MyCustom/LogXml.php</b>.  
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Log_LoggerFactory
{
    /**
     * Set of created logger instances.
     *
     * @var array(string=>PHP_Depend_Log_LoggerI) $_instances
     */
    private $_instances = array();
    
    /**
     * Creates a new logger or returns an existing instance for the given 
     * <b>$identifier</b>.
     *
     * @param string $identifier The logger identifier.
     * @param string $fileName   The log output file name.
     * 
     * @return PHP_Depend_Log_LoggerI
     */
    public function createLogger($identifier, $fileName)
    {
        if (!isset($this->_instances[$identifier])) {
            // Extract all parts from the logger identifier
            $words = explode('-', $identifier);
            
            // Change all words to upper case
            $words = array_map('ucfirst', $words);
            
            // By definition the logger class name must be a single word.
            // Everything else is part of the package name.
            $class   = array_pop($words);
            $package = implode('', $words);
            
            $className = sprintf('PHP_Depend_Log_%s_%s', $package, $class);
            $classFile = sprintf('PHP/Depend/Log/%s/%s.php', $package, $class);
            
            if ($this->_fileExists($classFile) === false) {
                throw new RuntimeException("Unknown logger class '{$className}'.");
            }
            include_once $classFile;
            
            // Create a new logger instance.
            $logger = new $className();
            
            // TODO: Refactor this into an external log configurator or a similar
            //       concept.
            if ($logger instanceof PHP_Depend_Log_FileAwareI) {
                $logger->setLogFile($fileName);
            }
            
            $this->_instances[$identifier] = $logger;
        }
        return $this->_instances[$identifier];
    }
    
    /**
     * This method checks that the given file exists within the include_path.
     *
     * @param string $file A relative file uri.
     * 
     * @return boolean
     */
    private function _fileExists($file)
    {
        // Check default
        if (file_exists($file) === true) {
            return true;
        }
        // Get configured include_path 
        $paths = explode(PATH_SEPARATOR, get_include_path());
        // Check each path for the given file
        foreach ($paths as $path) {
            if (realpath($path . DIRECTORY_SEPARATOR . $file) !== false) {
                return true;
            }
        }
        return false;
    }
}
