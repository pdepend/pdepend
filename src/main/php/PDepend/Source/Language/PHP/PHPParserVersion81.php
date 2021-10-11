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
 * @since 2.11
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTCallable;
use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTConstant;
use PDepend\Source\AST\ASTIdentifier;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTScalarType;
use PDepend\Source\AST\ASTType;
use PDepend\Source\AST\State;
use PDepend\Source\Parser\ParserException;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 8.1.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 2.11
 */
abstract class PHPParserVersion81 extends PHPParserVersion80
{
    /**
     * Tests if the given image is a PHP 8.1 type hint.
     *
     * @param string $image
     * @return boolean
     */
    protected function isScalarOrCallableTypeHint($image)
    {
        if (strtolower($image) === 'never') {
           return true;
        }

        return parent::isScalarOrCallableTypeHint($image);
    }

    /**
     * Parses a scalar type hint or a callable type hint.
     *
     * @param string $image
     * @return \PDepend\Source\AST\ASTType
     */
    protected function parseScalarOrCallableTypeHint($image)
    {
        if (strtolower($image) === 'never') {
            return $this->builder->buildAstScalarType($image);
        }

        return parent::parseScalarOrCallableTypeHint($image);
    }

    /**
     * @inheritDoc
     */
    protected function parseConstructFormalParameterModifiers()
    {
        $modifier = parent::parseConstructFormalParameterModifiers();

        if ($this->tokenizer->peek() === Tokens::T_READONLY) {
            $modifier |= State::IS_READONLY;
            $this->tokenizer->next();
        }

        return $modifier;
    }
}