<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
  */

namespace PDepend\Source\Tokenizer;

use PDepend\AbstractTest;

/**
 * Test case for the {@link \PDepend\Source\Tokenizer\Token} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Tokenizer\Token
 * @group unittest
 */
class TokenTest extends AbstractTest
{
    /**
     * testConstructorSetsTypeProperty
     *
     * @return void
     */
    public function testConstructorSetsTypeProperty()
    {
        $token = new Token(1, 2, 4, 8, 16, 32);
        $this->assertEquals(1, $token->type);
    }

    /**
     * testConstructorSetsImageProperty
     *
     * @return void
     */
    public function testConstructorSetsImageProperty()
    {
        $token = new Token(1, 2, 4, 8, 16, 32);
        $this->assertEquals(2, $token->image);
    }

    /**
     * testConstructorSetsStartLineProperty
     *
     * @return void
     */
    public function testConstructorSetsStartLineProperty()
    {
        $token = new Token(1, 2, 4, 8, 16, 32);
        $this->assertEquals(4, $token->startLine);
    }

    /**
     * testConstructorSetsEndLineProperty
     *
     * @return void
     */
    public function testConstructorSetsEndLineProperty()
    {
        $token = new Token(1, 2, 4, 8, 16, 32);
        $this->assertEquals(8, $token->endLine);
    }

    /**
     * testConstructorSetsStartColumnProperty
     *
     * @return void
     */
    public function testConstructorSetsStartColumnProperty()
    {
        $token = new Token(1, 2, 4, 8, 16, 32);
        $this->assertEquals(16, $token->startColumn);
    }

    /**
     * testConstructorSetsEndColumnProperty
     *
     * @return void
     */
    public function testConstructorSetsEndColumnProperty()
    {
        $token = new Token(1, 2, 4, 8, 16, 32);
        $this->assertEquals(32, $token->endColumn);
    }
}
