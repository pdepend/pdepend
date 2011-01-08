#!/usr/bin/env php
<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */


/**
 * This script creates an executable phar archive.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_PharBuilder
{
    /**
     * Interal phar alias name.
     */
    const PHAR_ALIAS = 'pdepend-@version@.phar';

    /**
     * Creates phar file name.
     */
    const PHAR_FILENAME = 'pdepend-@version@.phar';

    /**
     * The php depend version number.
     *
     * @var string
     */
    protected $version = null;

    /**
     * Default root directory for an archive build.
     *
     * @var string
     */
    protected $rootDirectory = null;

    /**
     * Temporary directory with sources applied to the phar archive.
     *
     * @var string
     */
    protected $tempDirectory = null;

    /**
     * User supplied target directory for the generated phar archive. This
     * property defaults to <b>${root}/build</b>.
     *
     * @var string
     */
    protected $targetDirectory = null;

    /**
     * User supplied source directory for the generated phar archive. This
     * property defaults to <b>${root}</b>.
     *
     * @var string
     */
    protected $sourceDirectory = null;

    /**
     * Constructs a new phar archive builder.
     */
    public function __construct()
    {
        $this->rootDirectory = realpath(dirname(__FILE__) . '/../');
    }

    /**
     * Destruct the builder and removes temporary resources.
     */
    public function __destruct()
    {
        if (file_exists($this->tempDirectory) === true) {
            shell_exec(sprintf('rm -rf %s', escapeshellarg($this->tempDirectory)));
        }
    }

    /**
     * Generates a new phar archive.
     *
     * @param array $args The command line arguments.
     *
     * @return void
     */
    public function run(array $args)
    {
        $this->parseArguments($args);

        $tempName = $this->copySource();

        $phar = new Phar($this->getTargetPathname(), 0, $this->getAlias());
        $phar->startBuffering();

        $phar->buildFromDirectory($tempName);
        $phar->compressFiles(Phar::GZ);
        $phar->setStub($this->getStub());
        
        $phar->stopBuffering();

        chmod($this->getTargetPathname(), 0775);
    }

    /**
     * Creates a temporary source tree with PHP_Depend's main source used for
     * the phar archive. The return value of this method is the qualified name
     * of the temp directory.
     *
     * @return string
     */
    protected function copySource()
    {
        $this->tempName = sys_get_temp_dir() . '/' . uniqid('pdepend_');
        mkdir($this->tempName);

        shell_exec(
            sprintf(
                'cp -rf %s %s',
                escapeshellarg($this->getSourceDirectory()) . '/PHP',
                escapeshellarg($this->tempName)
            )
        );

        file_put_contents(
            sprintf('%s/pdepend.php', $this->tempName),
            trim(
                str_replace(
                    '#!/usr/bin/env php',
                    '',
                    file_get_contents(
                        sprintf('%s/pdepend.php', $this->getSourceDirectory())
                    )
                )
            )
        );

        return $this->tempName;
    }

    /**
     * Returns the PHP_Depend version number. The return value defaults to the
     * version number specified in the <b>package.xml</b> file. 
     *
     * @return string
     */
    protected function getVersion()
    {
        if ($this->version === null) {
            $sxml = simplexml_load_file($this->getSourceDirectory() . '/package.xml');

            $this->version = $sxml->version->release;
        }
        return $this->version;
    }

    /**
     * Returns the directory where the source can be found.
     *
     * @return string
     */
    public function getSourceDirectory()
    {
        if ($this->sourceDirectory === null) {
            $this->sourceDirectory = $this->rootDirectory;
        }
        return $this->sourceDirectory;
    }

    /**
     * Returns the full qualified pathname for the generated phar archive.
     *
     * @return string
     */
    protected function getTargetPathname()
    {
        return $this->getTargetDirectory() . '/' . $this->getTargetFilename();
    }

    /**
     * Returns the filename for the generated phar archive.
     *
     * @return string
     */
    protected function getTargetFilename()
    {
        return str_replace('@version@', $this->getVersion(), self::PHAR_FILENAME);
    }

    /**
     * Returns the target directory for the phar archive.
     *
     * @return string
     */
    protected function getTargetDirectory()
    {
        if ($this->targetDirectory === null) {
            $this->targetDirectory = $this->rootDirectory . '/build';
        }
        return $this->targetDirectory;
    }

    /**
     * Returns the internal phar archive alias.
     *
     * @return string
     */
    protected function getAlias()
    {
        return str_replace('@version@', $this->getVersion(), self::PHAR_ALIAS);
    }

    /**
     * Returns the php stub used to trigger PHP_Depend's cli interface.
     *
     * @return string
     */
    protected function getStub()
    {
        return sprintf(
            '#!/usr/bin/env php' . PHP_EOL .
            '<?php' . PHP_EOL .
            'Phar::mapPhar("%s");' . PHP_EOL .
            'require "phar://%s/pdepend.php";' . PHP_EOL .
            '__HALT_COMPILER();' . PHP_EOL,
            $this->getAlias(),
            $this->getAlias()
        );
    }

    /**
     * Parses the command line arguments passed to the builder script.
     *
     * @param array $args The command line arguments.
     *
     * @return void
     */
    protected function parseArguments(array $args)
    {
        reset($args);

        while (($arg = current($args)) !== false) {

            switch ($arg) {

            case '--version':
                $this->version = next($args);
                break;

            case '--target':
                $this->targetDirectory = realpath(next($args));
                break;

            case '--source':
                $this->sourceDirectory = realpath(next($args));
                break;

            }

            next($args);
        }
    }

    /**
     * Starts the phar generation process.
     *
     * @param array $args The cli arguments.
     *
     * @return void
     */
    public static function main(array $args)
    {
        // Drop filename
        array_shift($args);

        $builder = new PHP_Depend_PharBuilder();
        $builder->run($args);
    }
}

PHP_Depend_PharBuilder::main($_SERVER['argv']);
