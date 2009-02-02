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
 * @subpackage TextUI
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend.php';
require_once 'PHP/Depend/TextUI/Runner.php';
require_once 'PHP/Depend/Util/Configuration.php';
require_once 'PHP/Depend/Util/ConfigurationInstance.php';
require_once 'PHP/Depend/Util/Log.php';

/**
 * Handles the command line stuff and starts the text ui runner.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage TextUI
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_TextUI_Command
{
    /**
     * Marks a cli error exit.
     */
    const CLI_ERROR = 1742;

    /**
     * Marks an input error exit.
     */
    const INPUT_ERROR = 1743;

    /**
     * Mapping between command line identifiers and optimzation strategies.
     *
     * @var array(string=>integer) $_optimizations
     */
    private $_optimizations = array(
        PHP_Depend_TextUI_Runner::OPTIMZATION_BEST =>
            'Provides lowest memory usage with best possible performance.',
        PHP_Depend_TextUI_Runner::OPTIMZATION_NONE =>
            'Highest memory usage without any caching.'
    );

    /**
     * Collected log options.
     *
     * @var array(string=>string) $_logOptions
     */
    private $_logOptions = null;

    /**
     * Collected analyzer options.
     *
     * @var array(string=>string) $_analyzerOptions
     */
    private $_analyzerOptions = null;

    /**
     * The recieved cli options
     *
     * @var array(string=>mixed) $_options
     */
    private $_options = array();

    /**
     * The used text ui runner.
     *
     * @var PHP_Depend_TextUI_Runner $_runner
     */
    private $_runner = null;

    /**
     * Performs the main cli process and returns the exit code.
     *
     * @return integer
     */
    public function run()
    {
        // Create a new text ui runner
        $this->_runner = new PHP_Depend_TextUI_Runner();

        if ($this->handleArguments() === false) {
            $this->printHelp();
            return self::CLI_ERROR;
        }
        if (isset($this->_options['--help'])) {
            $this->printHelp();
            return PHP_Depend_TextUI_Runner::SUCCESS_EXIT;
        }
        if (isset($this->_options['--usage'])) {
            $this->printUsage();
            return PHP_Depend_TextUI_Runner::SUCCESS_EXIT;
        }
        if (isset($this->_options['--version'])) {
            $this->printVersion();
            return PHP_Depend_TextUI_Runner::SUCCESS_EXIT;
        }

        // Get a copy of all options
        $options = $this->_options;

        // Get an array with all available log options
        $logOptions = $this->collectLogOptions();

        // Get an array with all available analyzer options
        $analyzerOptions = $this->collectAnalyzerOptions();

        foreach ($options as $option => $value) {
            if (isset($logOptions[$option])) {
                // Reduce recieved option list
                unset($options[$option]);
                // Register logger
                $this->_runner->addLogger(substr($option, 2), $value);
            } else if (isset($analyzerOptions[$option])) {
                // Reduce recieved option list
                unset($options[$option]);

                if (isset($analyzerOptions[$option]['value']) && is_bool($value)) {
                    echo 'Option ', $option, ' requires a value.', PHP_EOL;
                    return self::INPUT_ERROR;
                } else if ($analyzerOptions[$option]['value'] === '*') {
                    $value = array_map('trim', explode(',', $value));
                }
                $this->_runner->addOption(substr($option, 2), $value);
            }
        }

        if (isset($options['--without-annotations'])) {
            // Disable annotation parsing
            $this->_runner->setWithoutAnnotations();
            // Remove option
            unset($options['--without-annotations']);
        }

        if (isset($options['--optimization'])) {
            // Check optimization strategy
            if (!isset($this->_optimizations[$options['--optimization']])) {
                echo 'Invalid optimization ', $options['--optimization'],
                     ' given.', PHP_EOL;
                return self::INPUT_ERROR;
            }

            // Set optimization strategy
            $this->_runner->setOptimization($options['--optimization']);
            // Remove option
            unset($options['--optimization']);
        }

        if (count($options) > 0) {
            $this->printHelp();
            echo "Unknown option '", key($options), "' given.\n";
            return self::CLI_ERROR;
        }

        try {
            // Output current pdepend version and author
            $this->printVersion();

            $startTime = time();

            $result = $this->_runner->run();

            echo PHP_EOL, 'Time: ', date('i:s', time() - $startTime);
            if (function_exists('memory_get_peak_usage')) {
                $memory = (memory_get_peak_usage(true) / (1024 * 1024));
                printf('; Memory: %4.2fMb', $memory);
            }
            echo PHP_EOL;


            return $result;
        } catch (RuntimeException $e) {
            // Print error message
            echo $e->getMessage(), "\n";
            // Return exit code
            return $e->getCode();
        }
    }

    /**
     * Parses the cli arguments.
     *
     * @return boolean
     */
    protected function handleArguments()
    {
        if (!isset($_SERVER['argv'])) {
            if (false === (boolean) ini_get('register_argc_argv')) {
                // @codeCoverageIgnoreStart
                echo "Please enable register_argc_argv in your php.ini.\n\n";
            } else {
                // @codeCoverageIgnoreEnd
                echo "Unknown error, no \$argv array available.\n\n";
            }
            return false;
        }

        // Get cli arguments
        $argv = $_SERVER['argv'];

        // Remove the pdepend command line file
        array_shift($argv);

        if (count($argv) === 0) {
            return false;
        }

        // Last argument must be a list of source directories
        if (strpos(end($argv), '--') !== 0) {
            $this->_runner->setSourceArguments(explode(',', array_pop($argv)));
        }

        for ($i = 0, $c = count($argv); $i < $c; ++$i) {

            // Is it an ini_set option?
            if ($argv[$i] === '-d' && isset($argv[$i + 1])) {
                if (strpos($argv[++$i], '=') === false) {
                    ini_set($argv[$i], 'on');
                } else {
                    // Split key=value
                    list($key, $value) = explode('=', $argv[$i]);
                    // set ini option
                    ini_set($key, $value);
                }
            } else if (strpos($argv[$i], '=') === false) {
                $this->_options[$argv[$i]] = true;
            } else {
                // Split key=value
                list($key, $value) = explode('=', $argv[$i]);
                // Set option
                $this->_options[$key] = $value;
            }
        }

        // Check for suffix option
        if (isset($this->_options['--suffix'])) {
            // Get file extensions
            $extensions = explode(',', $this->_options['--suffix']);
            // Set allowed file extensions
            $this->_runner->setFileExtensions($extensions);
            // Remove from options array
            unset($this->_options['--suffix']);
        }

        // Check for ignore option
        if (isset($this->_options['--ignore'])) {
            // Get exclude directories
            $directories = explode(',', $this->_options['--ignore']);
            // Set exclude directories
            $this->_runner->setExcludeDirectories($directories);
            // Remove from options array
            unset($this->_options['--ignore']);
        }

        // Check for exclude package option
        if (isset($this->_options['--exclude'])) {
            // Get exclude directories
            $packages = explode(',', $this->_options['--exclude']);
            // Set exclude packages
            $this->_runner->setExcludePackages($packages);
            // Remove from options array
            unset($this->_options['--exclude']);
        }

        // Check for the bad documentation option
        if (isset($this->_options['--bad-documentation'])) {
            // Enable bad documentation support
            $this->_runner->setSupportBadDocumentation();
            // Remove from options array
            unset($this->_options['--bad-documentation']);
        }

        // Check for configuration option
        if (isset($this->_options['--configuration'])) {
            // Get config file
            $configFile = $this->_options['--configuration'];
            // Remove option from array
            unset($this->_options['--configuration']);

            // First check config file
            if (file_exists($configFile) === false) {
                // Print error message
                echo "The configuration file '{$configFile}' doesn't exist.\n\n";
                // Return error
                return false;
            }

            // Load configuration file
            $config = new PHP_Depend_Util_Configuration($configFile, null, true);
            // Store in config registry
            PHP_Depend_Util_ConfigurationInstance::set($config);
        }

        if (isset($this->_options['--debug'])) {
            // Remove option from array
            unset($this->_options['--debug']);
            // Enable debug logging
            PHP_Depend_Util_Log::setSeverity(PHP_Depend_Util_Log::DEBUG);
        }

        return true;
    }

    /**
     * Outputs the current PHP_Depend version.
     *
     * @return void
     */
    protected function printVersion()
    {
        echo "PHP_Depend @package_version@ by Manuel Pichler\n\n";
    }

    /**
     * Outputs the base usage of PHP_Depend.
     *
     * @return void
     */
    protected function printUsage()
    {
        $this->printVersion();
        echo "Usage: pdepend [options] [logger] <dir[,dir[,...]]>\n\n";
    }

    /**
     * Outputs the main help of PHP_Depend.
     *
     * @return void
     */
    protected function printHelp()
    {
        $this->printUsage();

        $l = $this->printLogOptions();
        $l = $this->printAnalyzerOptions($l);

        $this->_printOption('--configuration=<file>',
                            'Optional PHP_Depend configuration file.', $l);
        echo PHP_EOL;

        $this->_printOption('--suffix=<ext[,...]>',
                            'List of valid PHP file extensions.', $l);
        $this->_printOption('--ignore=<dir[,...]>',
                            'List of exclude directories.', $l);
        $this->_printOption('--exclude=<pkg[,...]>',
                            'List of exclude packages.', $l);
        echo PHP_EOL;

        $this->_printOption('--without-annotations',
                            'Do not parse doc comment annotations.', $l);
        $this->_printOption('--bad-documentation',
                            'Fallback for projects with bad doc comments.', $l);
        echo PHP_EOL;

        $this->_printOption('--optimization=<mode>',
                            'Runtime switch to influence the internal processing.',
                            $l);
        foreach ($this->_optimizations as $name => $help) {
            $this->_printOption('               "' . $name . '"', $help, $l);
        }
        echo PHP_EOL;

        $this->_printOption('--debug', 'Prints debugging information.', $l);
        $this->_printOption('--help', 'Print this help text.', $l);
        $this->_printOption('--version', 'Print the current version.', $l);
        $this->_printOption('-d key[=value]', 'Sets a php.ini value.', $l);
        echo PHP_EOL;
    }

    /**
     * Prints all available log options and returns the length of the longest
     * option.
     *
     * @return integer
     */
    protected function printLogOptions()
    {
        $maxLength = 0;
        $options   = array();
        foreach ($this->collectLogOptions() as $option => $path) {
            // Build log option identifier
            $identifier = "{$option}=<file>";
            // Store in options array
            $options[$identifier] = (string) simplexml_load_file($path)->message;

            if (($length = strlen($identifier)) > $maxLength) {
                $maxLength = $length;
            }
        }

        // Calculate the max message length
        $messageLength = 77 - $maxLength;

        ksort($options);

        $last = null;
        foreach ($options as $option => $message) {

            $current = substr($option, 0, strrpos($option, '-'));
            if ($last !== null && $last !== $current) {
                echo "\n";
            }
            $last = $current;

            $this->_printOption($option, $message, $maxLength);
        }
        echo "\n";

        return $maxLength;
    }

    /**
     * Collects all logger options and the configuration name.
     *
     * @return array(string=>string)
     */
    protected function collectLogOptions()
    {
        if ($this->_logOptions !== null) {
            return $this->_logOptions;
        }

        $this->_logOptions = array();

        // Get all include paths
        $paths = explode(PATH_SEPARATOR, get_include_path());

        foreach ($paths as $path) {

            $path .= '/PHP/Depend/Log';

            if (is_dir($path) === false) {
                continue;
            }

            $dirs = new DirectoryIterator($path);

            foreach ($dirs as $dir) {
                if (!$dir->isDir() || substr($dir->getFilename(), 0, 1) === '.') {
                    continue;
                }

                $files = new DirectoryIterator($dir->getPathname());
                foreach ($files as $file) {
                    if (!$file->isFile()) {
                        continue;
                    }
                    if (substr($file->getFilename(), -4, 4) !== '.xml') {
                        continue;
                    }

                    $option = '--' . strtolower($dir->getFilename())
                            . '-' . strtolower(substr($file->getFilename(), 0, -4));

                    $this->_logOptions[$option] = $file->getPathname();
                }
            }
        }
        return $this->_logOptions;
    }

    /**
     * Prints the analyzer options.
     *
     * @param integer $length Length of the longest option.
     *
     * @return integer
     */
    protected function printAnalyzerOptions($length)
    {
        $options = $this->collectAnalyzerOptions();
        if (count($options) === 0) {
            return $length;
        }

        ksort($options);

        foreach ($options as $option => $info) {

            if (isset($info['value'])) {
                if ($info['value'] === '*') {
                    $option .= '=<*[,...]>';
                } else {
                    $option .= '=<value>';
                }
            }

            $this->_printOption($option, $info['message'], $length);
        }
        echo "\n";

        return $length;
    }

    /**
     * Collects cli options for installed analyzers.
     *
     * @return array(string=>array)
     */
    protected function collectAnalyzerOptions()
    {
        if ($this->_analyzerOptions !== null) {
            return $this->_analyzerOptions;
        }
        $this->_analyzerOptions = array();

        // Get all include paths
        $paths = explode(PATH_SEPARATOR, get_include_path());
        foreach ($paths as $path) {
            // Get all analyzer configurations
            $files = glob("{$path}/PHP/Depend/Metrics/*/Analyzer.xml");

            foreach ($files as $file) {

                // Create a simple xml instance
                $sxml = simplexml_load_file($file);

                // Check for options
                if (!isset($sxml->options->option)) {
                    continue;
                }

                foreach ($sxml->options->option as $option) {
                    $identifier = '--' . (string) $option['name'];
                    $message    = (string) $option->message;

                    $value = null;
                    if (isset($option['value'])) {
                        $value = (string) $option['value'];
                    }

                    $this->_analyzerOptions[$identifier] = array(
                        'message'  =>  $message,
                        'value'    =>  $value
                    );
                }
            }
        }
        return $this->_analyzerOptions;
    }

    /**
     * Prints a single option.
     *
     * @param string  $option  The option identifier.
     * @param string  $message The option help message.
     * @param integer $length  The length of the longest option.
     *
     * @return void
     */
    private function _printOption($option, $message, $length)
    {
        // Calculate the max message length
        $mlength = 77 - $length;

        $option = str_pad($option, $length, ' ', STR_PAD_RIGHT);
        echo '  ', $option, ' ';

        $lines = explode("\n", wordwrap($message, $mlength, "\n"));
        echo array_shift($lines);

        while (($line = array_shift($lines)) !== null) {
            echo "\n", str_repeat(' ', $length + 3), $line;
        }
        echo "\n";
    }

    /**
     * Main method that starts the command line runner.
     *
     * @return integer The exit code.
     */
    public static function main()
    {
        $command = new PHP_Depend_TextUI_Command();
        return $command->run();
    }
}