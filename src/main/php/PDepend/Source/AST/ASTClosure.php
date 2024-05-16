<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.9.12
 */

namespace PDepend\Source\AST;

/**
 * This node class represents a closure-expression.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.9.12
 */
class ASTClosure extends AbstractASTNode implements ASTCallable
{
    /**
     * @return ASTType|null
     */
    public function getReturnType()
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof ASTType) {
                return $node;
            }
        }

        return null;
    }

    /**
     * This method will return <b>true</b> when this closure returns by
     * reference.
     *
     * @return bool
     */
    public function returnsByReference()
    {
        return $this->getMetadataBoolean(5);
    }

    /**
     * This method can be used to flag this closure as returns by reference.
     *
     * @param bool $returnsReference Does this closure return by reference?
     */
    public function setReturnsByReference($returnsReference): void
    {
        $this->setMetadataBoolean(5, (bool) $returnsReference);
    }

    /**
     * Returns whether this closure was defined as static or not.
     *
     * This method will return <b>TRUE</b> when the closure was declared as
     * followed:
     *
     * <code>
     * $closure = static function( $e ) {
     *   return pow( $e, 2 );
     * }
     * </code>
     *
     * And it will return <b>FALSE</b> when we declare the closure as usual:
     *
     * <code>
     * $closure = function( $e ) {
     *   return pow( $e, 2 );
     * }
     * </code>
     *
     * @return bool
     * @since  1.0.0
     */
    public function isStatic()
    {
        return $this->getMetadataBoolean(6);
    }

    /**
     * This method can be used to flag this closure instance as static.
     *
     * @param bool $static Whether this closure is static or not.
     * @since  1.0.0
     */
    public function setStatic($static): void
    {
        $this->setMetadataBoolean(6, (bool) $static);
    }

    /**
     * Returns the total number of the used property bag.
     *
     * @return int
     * @see    ASTNode#getMetadataSize()
     * @since  1.0.0
     */
    protected function getMetadataSize()
    {
        return 7;
    }
}
