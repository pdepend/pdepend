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
 *
 * @since 0.9.5
 */

namespace PDepend\Source\AST;

/**
 * This class encapsulates possible default values for functions, methods,
 * parameters and properties when they are dynamic such as new class
 * instances as allowed since PHP 8.1.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 2.11.0
 */
class ASTDynamicValue extends ASTValue
{
    /**
     * Boolean flag that is <b>true</b> when a PHP-expression was set.
     *
     * @var bool
     */
    private $expressionAvailable = false;

    /**
     * The parsed PHP-expression.
     *
     * @var ASTNode|null
     */
    private $expression = null;

    /**
     * This method will return the parsed PHP expression.
     *
     * @return ASTNode|null
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * This method will set the parsed PHP-expression. Please note that this method
     * acts as a one-way-function, only the first call with set the value all
     * following calls will be ignored.
     *
     * @param ASTNode $expression The parsed PHP-expression.
     *
     * @return void
     */
    public function setExpression(ASTNode $expression)
    {
        if ($this->expressionAvailable === false) {
            $this->expression          = $expression;
            $this->expressionAvailable = true;
        }
    }

    /**
     * This method will return <b>true</b> when the PHP-expression is already set.
     *
     * @return bool
     */
    public function isExpressionAvailable()
    {
        return $this->expressionAvailable;
    }
}
