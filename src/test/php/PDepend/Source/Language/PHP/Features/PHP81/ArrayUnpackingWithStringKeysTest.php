<?php
/**
 * This file is part of PDepend.
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
 */

namespace PDepend\Source\Language\PHP\Features\PHP81;

/**
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion81
 * @group unittest
 * @group php8.1
 */
class ArrayUnpackingWithStringKeysTest extends PHPParserVersion81Test
{
    /**
     * @return void
     * @group y
     */
    public function testArrayUnpackingWithStringKeys()
    {
        $method = $this->getFirstMethodForTestCase();
        $children = $method->getChildren();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTTypeArray', $children[1]);
        $children = $children[2]->getChildren();
        $this->assertCount(1, $children);
        $return = $children[0];
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTReturnStatement', $return);
        $children = $return->getChildren();
        $this->assertCount(1, $children);
        $array = $children[0];
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTArray', $array);
        $children = $array->getChildren();
        $this->assertCount(2, $children);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTArrayElement', $children[0]);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTArrayElement', $children[1]);
    }
}
