<?php
/**
 * This file is part of PHP_Reflection.
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
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Parser test case related to function parsing.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Parser_FunctionTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests that the parser skips a comment tokens after 'function'.
     *
     * @return void
     */
    public function testParserHandlesCommentAfterFunctionToken()
    {
        $function = self::_testParseFunction('comment_after_function_token.php');
        $this->assertEquals('hello', $function->getName());
    }
    /**
     * Tests that the parser skips a comment tokens after a function name.
     *
     * @return void
     */
    public function testParserHandlesCommentAfterFunctionName()
    {
        $function = self::_testParseFunction('comment_after_function_name.php');
        $this->assertEquals(1, $function->getParameters()->count());
    }
    
    /**
     * Tests that the parser skips a comment between function token and reference
     * operator.
     *
     * @return void
     */
    public function testParserHandlesCommentBetweenFunctionTokenAndReturnByRefToken()
    {
        $function = self::_testParseFunction('comment_after_function_reference_token.php');
        $this->assertEquals('world', $function->getName());
    }
    
    /**
     * Tests that the parse handles a/multipe comment(s) between function
     * signature and opening curly brace.
     *
     * @return void
     */
    public function testParserHandlesCommentBetweenSignatureAndCurlyBodyBrace()
    {
        $function = self::_testParseFunction('comment_after_function_signatur.php');
        $this->assertEquals('hello', $function->getName());
    }
    
    /**
     * Tests that the parser sets the returns reference flag.
     *
     * @return void
     */
    public function testParserSetsReturnsReferenceFlag()
    {
        $function = self::_testParseFunction('returns_reference_function.php');
        $this->assertTrue($function->returnsReference());
    }
    
    /**
     * Tests that the parser sets no returns reference flag.
     *
     * @return void
     */
    public function testParserSetsNoReturnsReferenceFlag()
    {
        $function = self::_testParseFunction('returns_no_reference_function.php');
        $this->assertFalse($function->returnsReference());
    }
    
    /**
     * Tests that the parser sets the correct default parameter value.
     *
     * @return void
     */
    public function testParserSetsDefaultParameterValue()
    {
        $function = self::_testParseFunction('parameter_default_value_null.php');
        
        $parameters = $function->getParameters();
        $this->assertEquals(1, $parameters->count());
        
        $parameter = $parameters->current();
        $this->assertNotNull($parameter->getDefaultValue());
        
        $defaultValue = $parameter->getDefaultValue();
        $this->assertSame(PHP_Reflection_AST_MemberNullValue::flyweight(), $defaultValue);
    }
    
    /**
     * Parses a source file and extracts the first function instance.
     *
     * @param string $file The source file.
     * 
     * @return PHP_Reflection_AST_FunctionI
     */
    private static function _testParseFunction($file)
    {
        $packages = self::parseSource("/parser/functions/{$file}");
        self::assertEquals(1, $packages->count());
        
        $package = $packages->current();
        self::assertEquals(1, $package->getFunctions()->count());
        
        $function = $package->getFunctions()->current();
        self::assertNotNull($function);
        self::assertType('PHP_Reflection_AST_FunctionI', $function);
        
        return $function;
    }
}