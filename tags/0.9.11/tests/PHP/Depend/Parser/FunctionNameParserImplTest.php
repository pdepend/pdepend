<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Token.php';
require_once 'PHP/Depend/TokenizerI.php';
require_once 'PHP/Depend/Parser/TokenStack.php';
require_once 'PHP/Depend/Parser/FunctionNameParserImpl.php';

/**
 * Test case for the {@link PHP_Depend_Parser_FunctionNameParserImpl} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Parser_FunctionNameParserImplTest extends PHP_Depend_AbstractTest
{
    /**
     * testParserHandlesTNamespaceAsValidFunctionName
     *
     * @return void
     * @covers PHP_Depend_Parser_FunctionNameParserImpl
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserHandlesTNamespaceAsValidFunctionName()
    {
        $parser = new PHP_Depend_Parser_FunctionNameParserImpl();

        $tokenizer = $this->getMock('PHP_Depend_TokenizerI');
        $tokenizer->expects($this->once())
            ->method('peek')
            ->will($this->returnValue(PHP_Depend_TokenizerI::T_NAMESPACE));
        $tokenizer->expects($this->once())
            ->method('next')
            ->will($this->returnValue($this->getMock('PHP_Depend_Token', array(), array(), '', false)));

        $stack = $this->getMock('PHP_Depend_Parser_TokenStack');

        $parser->setTokenizer($tokenizer);
        $parser->setTokenStack($stack);

        $parser->parse();
    }

    /**
     * testParserHandlesTStringAsValidFunctionName
     *
     * @return void
     * @covers PHP_Depend_Parser_FunctionNameParserImpl
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserHandlesTStringAsValidFunctionName()
    {
        $parser = new PHP_Depend_Parser_FunctionNameParserImpl();

        $tokenizer = $this->getMock('PHP_Depend_TokenizerI');
        $tokenizer->expects($this->once())
            ->method('peek')
            ->will($this->returnValue(PHP_Depend_TokenizerI::T_STRING));
        $tokenizer->expects($this->once())
            ->method('next')
            ->will($this->returnValue($this->getMock('PHP_Depend_Token', array(), array(), '', false)));

        $stack = $this->getMock('PHP_Depend_Parser_TokenStack');

        $parser->setTokenizer($tokenizer);
        $parser->setTokenStack($stack);

        $parser->parse();
    }

    /**
     * testParserAddsValidTokenToTokenStackInstance
     *
     * @return void
     * @covers PHP_Depend_Parser_FunctionNameParserImpl
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     */
    public function testParserAddsValidTokenToTokenStackInstance()
    {
        $parser = new PHP_Depend_Parser_FunctionNameParserImpl();

        $tokenizer = $this->getMock('PHP_Depend_TokenizerI');
        $tokenizer->expects($this->once())
            ->method('peek')
            ->will($this->returnValue(PHP_Depend_TokenizerI::T_STRING));
        $tokenizer->expects($this->once())
            ->method('next')
            ->will($this->returnValue($this->getMock('PHP_Depend_Token', array(), array(), '', false)));

        $stack = $this->getMock('PHP_Depend_Parser_TokenStack');
        $stack->expects($this->once())
            ->method('add');

        $parser->setTokenizer($tokenizer);
        $parser->setTokenStack($stack);

        $parser->parse();
    }

    /**
     * testParserThrowsExceptionForUnexpectedTokenTArray
     *
     * @return void
     * @covers PHP_Depend_Parser_FunctionNameParserImpl
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     */
    public function testParserThrowsExceptionForUnexpectedTokenTArray()
    {
        $parser = new PHP_Depend_Parser_FunctionNameParserImpl();

        $tokenizer = $this->getMock('PHP_Depend_TokenizerI');
        $tokenizer->expects($this->once())
            ->method('peek')
            ->will($this->returnValue(PHP_Depend_TokenizerI::T_ARRAY));
        $tokenizer->expects($this->once())
            ->method('next')
            ->will($this->returnValue($this->getMock('PHP_Depend_Token', array(), array(), '', false)));

        $parser->setTokenizer($tokenizer);

        $parser->parse();
    }

    /**
     * testParserThrowsExceptionForUnexpectedTokenStreamEnd
     *
     * @return void
     * @covers PHP_Depend_Parser_FunctionNameParserImpl
     * @group pdepend
     * @group pdepend::parser
     * @group unittest
     * @expectedException PHP_Depend_Parser_TokenStreamEndException
     */
    public function testParserThrowsExceptionForUnexpectedTokenStreamEnd()
    {
        $parser = new PHP_Depend_Parser_FunctionNameParserImpl();

        $tokenizer = $this->getMock('PHP_Depend_TokenizerI');
        $tokenizer->expects($this->once())
            ->method('peek')
            ->will($this->returnValue(PHP_Depend_TokenizerI::T_EOF));

        $parser->setTokenizer($tokenizer);

        $parser->parse();
    }
}