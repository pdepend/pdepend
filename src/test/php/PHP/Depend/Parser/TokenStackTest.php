<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 * @since      0.10.0
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Parser_TokenStack} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * @since      0.10.0
 *
 * @covers PHP_Depend_Parser_TokenStack
 * @group pdepend
 * @group pdepend::parser
 * @group unittest
 */
class PHP_Depend_Parser_TokenStackTest extends PHP_Depend_Parser_AbstractTest
{
    /**
     * testAddReturnsGivenTokenInstance
     * 
     * @return void
     */
    public function testAddReturnsGivenTokenInstance()
    {
        $stack = new PHP_Depend_Parser_TokenStack();
        $token = $this->createToken();

        self::assertSame($token, $stack->add($token));
    }

    /**
     * testPopReturnsExpectedTokenArray
     *
     * @return void
     */
    public function testPopReturnsExpectedTokenArray()
    {
        $stack = new PHP_Depend_Parser_TokenStack();
        $stack->push();

        $expected = array(
            $stack->add($this->createToken()),
            $stack->add($this->createToken()),
            $stack->add($this->createToken())
        );

        self::assertSame($expected, $stack->pop());
    }

    /**
     * testPopOnlyReturnsExpectedTokenArrayInCurrentScope
     *
     * @return void
     */
    public function testPopOnlyReturnsExpectedTokenArrayInCurrentScope()
    {
        $stack = new PHP_Depend_Parser_TokenStack();
        $stack->push();
        $stack->add($this->createToken());
        $stack->add($this->createToken());
        $stack->push();

        $expected = array(
            $stack->add($this->createToken()),
            $stack->add($this->createToken())
        );

        self::assertSame($expected, $stack->pop());
    }

    /**
     * testPopOnRootReturnsExpectedTokenArrayWithAllTokens
     *
     * @return void
     */
    public function testPopOnRootReturnsExpectedTokenArrayWithAllTokens()
    {
        $stack = new PHP_Depend_Parser_TokenStack();
        $stack->push();

        $expected = array(
            $stack->add($this->createToken()),
            $stack->add($this->createToken())
        );

        $stack->push();
        $expected[] = $stack->add($this->createToken());
        $expected[] = $stack->add($this->createToken());
        $stack->pop();

        self::assertSame($expected, $stack->pop());
    }

    /**
     * Returns a test token instance.
     *
     * @return PHP_Depend_Token
     */
    protected function createToken()
    {
        return new PHP_Depend_Token(1, __CLASS__, 13, 17, 23, 42);
    }
}
