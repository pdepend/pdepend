<?php
/**
 * This file is part of PHP_Reflection.
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
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/AST/AbstractSourceElement.php';
require_once 'PHP/Reflection/AST/MemberValueI.php';

/**
 * This class represents an array value for php variables.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_AST_ArrayExpression
       extends PHP_Reflection_AST_AbstractSourceElement
    implements PHP_Reflection_AST_MemberValueI, Countable
{
    /**
     * Identifier for this node type.
     */
    const NODE_NAME = '#array-expression';

    /**
     * Elements within this array.
     *
     * @var array(PHP_Reflection_AST_ArrayElement) $_elements
     */
    private $_elements = array();

    /**
     * Constructs a new array value object.
     */
    public function __construct($line = 0)
    {
        parent::__construct(self::NODE_NAME, $line);
    }

    /**
     * Adds an element to this array.
     *
     * @param PHP_Reflection_AST_ArrayElement $element The element object.
     *
     * @return void
     */
    public function addElement(PHP_Reflection_AST_ArrayElement $element)
    {
        $this->_elements[] = $element;
    }

    /**
     * Returns the elements of this array.
     *
     * @return array(PHP_Reflection_AST_ArrayElement)
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Returns the number of values within this array
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_elements);
    }

    /**
     * Returns the php type of this value.
     *
     * @return integer
     */
    public function getType()
    {
        return self::IS_ARRAY;
    }

    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Reflection_VisitorI $visitor The context visitor implementation.
     *
     * @return void
     */
    public function accept(PHP_Reflection_VisitorI $visitor)
    {
        $visitor->visitArrayExpression($this);
    }
}