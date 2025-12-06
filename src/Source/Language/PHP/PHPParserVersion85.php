<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2025 Oliver Eglseder <oliver.eglseder@co-stack.com>.
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
 * @copyright 2025 Oliver Eglseder <oliver.eglseder@co-stack.com>. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 3.0
 */

namespace PDepend\Source\Language\PHP;

use PDepend\Source\AST\ASTNode;
use PDepend\Source\Tokenizer\Tokens;

/**
 * Concrete parser implementation that supports features up to PHP version 8.4
 *
 * @copyright 2025 Oliver Eglseder <oliver.eglseder@co-stack.com>. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @since 3.0
 */
abstract class PHPParserVersion85 extends PHPParserVersion84
{
    protected function parseOptionalExpressionForVersion(): ?ASTNode
    {
        return $this->parseExpressionVersion85()
            ?: parent::parseOptionalExpressionForVersion();
    }

    protected function parseExpressionVersion85(): ?ASTNode
    {
        $this->consumeComments();
        $nextTokenType = $this->tokenizer->peek();

        if ($nextTokenType === Tokens::T_PIPE) {
            $token = $this->consumeToken($nextTokenType);

            $expr = $this->builder->buildASTPipe();
            $expr->configureLinesAndColumns(
                $token->startLine,
                $token->endLine,
                $token->startColumn,
                $token->endColumn
            );

            return $expr;
        }

        return null;
    }
}
