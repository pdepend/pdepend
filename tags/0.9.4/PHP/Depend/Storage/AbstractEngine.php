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
 * @subpackage Storage
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

/**
 * This is an abstract base class for all storage strategies.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Storage
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
abstract class PHP_Depend_Storage_AbstractEngine
{
    /**
     * Should the engine be pruned on destruction?
     *
     * @var boolean $_prune
     */
    private $_prune = false;

    /**
     * The probability that the automatic garbage collection is trigged on
     * engine destruction?
     *
     * @var integer $_probability
     */
    private $_probability = 5;

    /**
     * The max lifetime of stored object, this property is used by the garbage
     * collection mechanism.
     *
     * @var integer $_maxLifetime
     */
    private $_maxLifetime = 86400;

    /**
     * Destructs this storage engine and triggers a storage prune when the
     * property <b>$_prune</b> is <b>true</b> or it may trigger a garbage
     * collection run.
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->hasPrune() === true) {
            $this->garbageCollect();
        } else if ((rand() % 100) <= $this->getProbability()) {
            $this->garbageCollect();
        }
    }

    /**
     * This method will return <b>true</b> when that property was set.
     *
     * @return boolean
     */
    public function hasPrune()
    {
        return $this->_prune;
    }

    /**
     * Activates the prune behavior of this storage engine.
     *
     * @return void
     */
    public function setPrune()
    {
        $this->setMaxLifetime(0);
        $this->_prune = true;
    }

    /**
     * This method returns the probability that the garbage collection mechanism
     * is triggered on engine destruction.
     *
     * @return integer
     */
    public function getProbability()
    {
        return $this->_probability;
    }

    /**
     * This method stts the probability that influences the garbage collection
     * triggered on engine destruction.
     *
     * @param integer $propability A value between 0 and 100.
     *
     * @return void
     * @throws InvalidArgumentException When the given propability is not an
     *                                  integer or not between 0 and 100.
     */
    public function setProbability($propability)
    {
        if (!is_int($propability) || $propability < 0 || $propability > 100) {
            $message = 'The propability must be an integer between 0 and 100.';
            throw new InvalidArgumentException($message);
        }
        $this->_probability = $propability;
    }

    /**
     * This method returns the max lifetime in seconds of stored objects. Older
     * records will be deleted during a garbage collection run.
     *
     * @return integer
     */
    public function getMaxLifetime()
    {
        return $this->_maxLifetime;
    }

    /**
     * This method sets the max lifetime in seconds for stored records. Older
     * records will be deleted during a garbage collection run.
     *
     * @param integer $maxLifetime The max lifetime for stored records.
     *
     * @return void
     * @throws InvalidArgumentException When the given maxLifetime value is not
     *                                  of type integer or it is lower than 0.
     */
    public function setMaxLifetime($maxLifetime)
    {
        if (!is_int($maxLifetime) || $maxLifetime < 0) {
            $message = 'The max liftime must be an integer greater than 0.';
            throw new InvalidArgumentException($message);
        }
        $this->_maxLifetime = $maxLifetime;
    }

    /**
     * Garbage collection method, this is an empty implementation and should be
     * overwritten by concrete engines.
     *
     * @return void
     */
    protected function garbageCollect()
    {

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
    public abstract function store($data, $key, $group, $version = null);

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
    public abstract function restore($key, $group, $version = null);
}
?>
