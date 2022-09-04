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
 * @since 2.11
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTScalarType;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 8.2.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 2.12
 */
abstract class PHPParserVersion82 extends PHPParserVersion81
{
    protected $possiblePropertyTypes = array(
        Tokens::T_STRING,
        Tokens::T_ARRAY,
        Tokens::T_QUESTION_MARK,
        Tokens::T_BACKSLASH,
        Tokens::T_CALLABLE,
        Tokens::T_SELF,
        Tokens::T_NULL,
        Tokens::T_FALSE,
        Tokens::T_TRUE,
    );

    /**
     * Since PHP 8.2, readonly is allowed as class modifier.
     */
    const READONLY_CLASS_ALLOWED = true;

    /**
     * Tests if the given image is a PHP 8.2 type hint.
     *
     * @param string $image
     *
     * @return bool
     */
    protected function isScalarOrCallableTypeHint($image)
    {
        if (strtolower($image) === 'true') {
            return true;
        }

        return parent::isScalarOrCallableTypeHint($image);
    }

    protected function isTypeHint($tokenType)
    {
        if (in_array($tokenType, array(Tokens::T_TRUE, Tokens::T_PARENTHESIS_OPEN), true)) {
            return true;
        }

        return parent::isTypeHint($tokenType);
    }

    protected function parseSingleTypeHint()
    {
        $this->consumeComments();

        switch ($this->tokenizer->peek()) {
            case Tokens::T_PARENTHESIS_OPEN:
                $this->consumeToken(Tokens::T_PARENTHESIS_OPEN);
                $this->consumeComments();
                $type = $this->parseTypeHint();
                $this->consumeComments();
                $this->consumeToken(Tokens::T_PARENTHESIS_CLOSE);
                $this->consumeComments();

                return $type;

            case Tokens::T_TRUE:
                $type = new ASTScalarType('true');
                $this->tokenStack->add($this->tokenizer->next());
                $this->consumeComments();

                return $type;

            default:
                return parent::parseSingleTypeHint();
        }
    }

    /**
     * @param ASTNode $type
     *
     * @return bool
     */
    protected function canNotBeStandAloneType($type)
    {
        return false;
    }
}
