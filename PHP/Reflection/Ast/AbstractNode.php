<?php
/**
 * This file is part of PHP_Reflection.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @package    PHP_Reflection
 * @subpackage Ast
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/Ast/NodeI.php';
require_once 'PHP/Reflection/Util/UUID.php';

/**
 * This is a base implementation of the node interface.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Ast
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
abstract class PHP_Reflection_Ast_AbstractNode implements PHP_Reflection_Ast_NodeI
{
    /**
     * The name of this node.
     *
     * @var string $_name
     */
    private $_name = null;
    
    /**
     * The unique identifier of this node.
     *
     * @var PHP_Reflection_Util_UUID $_uuid
     */
    private $_uuid = null;
    
    /**
     * Constructs a new item for the given <b>$name</b> and <b>$startLine</b>.
     *
     * @param string $name The item name.
     */
    public function __construct($name)
    {
        $this->_name = $name;
        $this->_uuid = new PHP_Reflection_Util_UUID();
    }
    
    /**
     * Returns the item name.
     *
     * @return string
     */
    public final function getName()
    {
        return $this->_name;
    }
    
    /**
     * Returns a uuid for this code node.
     *
     * @return string
     */
    public final function getUUID()
    {
        return (string) $this->_uuid;
    }
    
    /**
     * Compares two node instances to be equal. You should always use this method
     * instead of a direct comparsion of two nodes, because the syntax tree uses
     * proxy implementations to represent some items.
     *
     * @param PHP_Reflection_Ast_NodeI $node The node to compare to.
     * 
     * @return boolean
     */
    public function equals(PHP_Reflection_Ast_NodeI $node)
    {
        return ($this->getUUID() === $node->getUUID());
    }
    
    /**
     * Returns the input <b>$node</b> when the given node instance is not 
     * affected by a user defined filter, otherwise the return value will be
     * <b>null</b>. 
     *
     * @param PHP_Reflection_Ast_NodeI $node The context node instance.
     * 
     * @return PHP_Reflection_Ast_NodeI|null
     */
    protected final function filterNode(PHP_Reflection_Ast_NodeI $node = null)
    {
        // Ignore null nodes
        if ($node === null) {
            return null;
        }
        
        // TODO: Refactor this iterator filter stuff
        if (PHP_Reflection_Ast_Iterator_StaticFilter::getInstance()->accept($node)) {
            return $node;
        }
        return null;
    }
}