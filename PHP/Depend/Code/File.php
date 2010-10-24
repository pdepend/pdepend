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
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once 'PHP/Depend/Code/NodeI.php';

/**
 * This class provides an interface to a single source file.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Code_File implements PHP_Depend_Code_NodeI
{
    /**
     * The unique identifier for this function.
     *
     * @var string
     */
    protected $uuid = null;

    /**
     * The source file name/path.
     *
     * @var string
     */
    protected $fileName = null;

    /**
     * The comment for this type.
     *
     * @var string
     */
    protected $docComment = null;

    /**
     * Names of all packages that were defined in this file.
     *
     * @var array(string)
     */
    protected $packageNames = array();

    protected $startLine = 0;

    protected $endLine = 0;

    /**
     * Normalized code in this file.
     *
     * @var string $_source
     */
    private $_source = null;

    /**
     * Constructs a new source file instance.
     *
     * @param string $fileName The source file name/path.
     */
    public function __construct($fileName)
    {
        if ($fileName !== null) {
            $this->fileName = realpath($fileName);
        }
    }

    /**
     * Returns the physical file name for this object.
     *
     * @return string
     */
    public function getName()
    {
        return $this->fileName;
    }

    /**
     * Returns the physical file name for this object.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Returns a uuid for this code node.
     *
     * @return string
     */
    public function getUUID()
    {
        return $this->uuid;
    }

    /**
     * Sets the unique identifier for this file instance.
     *
     * @param string $uuid Identifier for this file.
     *
     * @return void
     * @since 0.9.12
     */
    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Returns normalized source code with stripped whitespaces.
     *
     * @return array(integer=>string)
     */
    public function getSource()
    {
        $this->readSource();
        return $this->_source;
    }

    /**
     * Returns an <b>array</b> with all tokens within this file.
     *
     * @return array(array)
     */
    public function getTokens()
    {
return getCached($this->fileName);
        $storage = PHP_Depend_StorageRegistry::get(PHP_Depend::TOKEN_STORAGE);
        return (array) $storage->restore(md5($this->fileName), __CLASS__);
    }

    /**
     * Sets the tokens for this file.
     *
     * @param array(array) $tokens The generated tokens.
     *
     * @return void
     */
    public function setTokens(array $tokens)
    {
return setCached($this->fileName, $tokens);
        $storage = PHP_Depend_StorageRegistry::get(PHP_Depend::TOKEN_STORAGE);
        $storage->store($tokens, md5($this->fileName), __CLASS__);
    }

    /**
     * Returns the doc comment for this item or <b>null</b>.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->docComment;
    }

    /**
     * Sets the doc comment for this item.
     *
     * @param string $docComment The doc comment block.
     *
     * @return void
     */
    public function setDocComment($docComment)
    {
        $this->docComment = $docComment;
    }

    public function getStartLine()
    {
        return $this->startLine;
    }

    public function getEndLine()
    {
        return $this->endLine;
    }

    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Depend_VisitorI $visitor The context visitor
     *                                              implementation.
     *
     * @return void
     */
    public function accept(PHP_Depend_VisitorI $visitor)
    {
        $visitor->visitFile($this);
    }

    protected $childNodes = array();

    public function addChild(PHP_Depend_Code_AbstractItem $item)
    {
        $this->childNodes[$item->getUUID()] = $item;
    }

    public function addPackage(PHP_Depend_Code_Package $package)
    {
        $this->packageNames[] = $package->getName();
    }

    public function __sleep()
    {
        return array(
            'uuid',
            'fileName',
            'startLine',
            'endLine',
            'childNodes',
            'docComment',
            'packageNames'
        );
    }

    public function __wakeup()
    {
        foreach ($this->childNodes as $childNode) {
            $childNode->setSourceFile($this);
        }

        $builder = PHP_Depend_Builder_Registry::getDefault();
        foreach ($this->packageNames as $packageName) {
            $builder->buildPackage($packageName);
        }
    }

    /**
     * This method can be called by the PHP_Depend runtime environment or a
     * utilizing component to free up memory. This methods are required for
     * PHP version < 5.3 where cyclic references can not be resolved
     * automatically by PHP's garbage collector.
     *
     * @return void
     * @since 0.9.12
     */
    public function free()
    {
        // Nothing todo here
    }

    /**
     * Returns the string representation of this class.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->fileName === null ? '' : $this->fileName);
    }

    /**
     * Reads the source file if required.
     *
     * @return void
     */
    protected function readSource()
    {
        if ($this->_source === null) {
            $source = file_get_contents($this->fileName);

            $this->_source = str_replace(array("\r\n", "\r"), "\n", $source);

            $this->startLine = 1;
            $this->endLine   = substr_count($this->_source, "\n") + 1;
        }
    }
}
