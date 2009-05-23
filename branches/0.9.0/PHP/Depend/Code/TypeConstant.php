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
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once 'PHP/Depend/Code/AbstractItem.php';

/**
 * An instance of this class represents a class or interface constant within the
 * analyzed source code.
 *
 * <code>
 * <?php
 * class PHP_Depend_BuilderI
 * {
 *     const DEFAULT_PACKAGE = '+global';
 * }
 * </code>
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Code_TypeConstant extends PHP_Depend_Code_AbstractItem
{
    /**
     * The parent type object.
     *
     * @var PHP_Depend_Code_AbstractClassOrInterface $_parent
     */
    private $_parent = null;

    /**
     * The source tokens used for this constant declaration.
     *
     * @var array(PHP_Depend_Token) $_tokens
     * @since 0.9.6
     */
    private $_tokens = null;

    /**
     * Returns the parent type object or <b>null</b>
     *
     * @return PHP_Depend_Code_AbstractClassOrInterface|null
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Sets the parent type object.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $parent The parent class.
     *
     * @return void
     */
    public function setParent(
        PHP_Depend_Code_AbstractClassOrInterface $parent = null
    ) {
        $this->_parent = $parent;
    }

    /**
     * Returns the source tokens used for this constant declaration.
     *
     * @return array(PHP_Depend_Token)
     * @since 0.9.6
     */
    public function getTokens()
    {
        return $this->_tokens;
    }

    /**
     * Sets the source tokens used for this constant declaration.
     *
     * @param array(PHP_Depend_Token) $tokens The source tokens.
     *
     * @return void
     * @since 0.9.6
     */
    public function setTokens(array $tokens)
    {
        if ($this->_tokens === null) {
            $this->_tokens = $tokens;
        }
    }

    /**
     * Returns the line number where the item declaration can be found.
     *
     * @return integer
     */
    public function getStartLine()
    {
        assert(($token = reset($this->_tokens)) instanceof PHP_Depend_Token);
        return $token->startLine;
    }

    /**
     * Returns the line number where the item declaration ends.
     *
     * @return integer The last source line for this item.
     */
    public function getEndLine()
    {
        assert(($token = end($this->_tokens)) instanceof PHP_Depend_Token);
        return $token->endLine;
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
        $visitor->visitTypeConstant($this);
    }
}