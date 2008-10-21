<?php
/**
 * This file is part of PHP_Reflection.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case related to method parsing.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Parser_MethodTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests that the parser handles a comment token after 'function'.
     *
     * @return void
     */
    public function testParserHandlesCommentAfterFunctionToken()
    {
        $method = self::_testParseMethod('comment_after_function_token.php');
        $this->assertEquals('world', $method->getName());
    }
    
    /**
     * Tests that the parser skips a comment after the method name.
     *
     * @return void
     */
    public function testParserHandlesCommentAfterMethodName()
    {
        $method = self::_testParseMethod('comment_after_method_name.php');
        $this->assertEquals(1, $method->getParameters()->count());
    }
    
    /**
     * Tests that the parser handles a comment between reference return token
     * and method name correct.
     *
     * @return void
     */
    public function testParserHandlesCommentBetweenReferenceReturnAndMethodName()
    {
        $method = self::_testParseMethod('comment_after_method_reference_token.php');
        $this->assertEquals('world', $method->getName());
    }
    
    /**
     * Tests that the parser flags a final method correct.
     *
     * @return void
     */
    public function testParserMarksMethodAsFinal()
    {
        $method = self::_testParseMethod('modifiers_final.php');
        $this->assertTrue($method->isFinal());
    }
    
    /**
     * Tests that the parser flags a static method correct.
     *
     * @return void
     */
    public function testParserMarkesMethodAsStatic()
    {
        $method = self::_testParseMethod('modifiers_static.php');
        $this->assertTrue($method->isStatic());
    }
    
    /**
     * Tests that the parser sets the correct end line for an interface method.
     *
     * @return void
     */
    public function testParserSetsCorrectEndLineValueForAInterfaceMethod()
    {
        $method = self::_testParseMethod('interface_method_end_line.php');
        $this->assertEquals(5, $method->getEndLine());
    }
    
    /**
     * Tests that the parser sets the correct end line for an interface method.
     *
     * @return void
     */
    public function testParserSetsCorrectEndLineValueForAnAbstractMethod()
    {
        $method = self::_testParseMethod('class_abstract_method_end_line.php');
        $this->assertEquals(5, $method->getEndLine());
    }
    
    /**
     * Tests that the parser marks an interface declared with 'static' correct.
     *
     * @return void
     */
    public function testParserMarksInterfaceMethodAsStatic()
    {
        $method = self::_testParseMethod('interface_method_static.php');
        $this->assertTrue($method->isStatic());
    }
    
    /**
     * Tests that the parse handles a/multipe comment(s) between method
     * signature and opening curly brace.
     *
     * @return void
     */
    public function testParserHandlesCommentBetweenSignatureAndCurlyBodyBrace()
    {
        $method = self::_testParseMethod('comment_after_method_signatur.php');
        $this->assertEquals('hello', $method->getName());
    }
    
    /**
     * Parses a source file and extracts the first method instance.
     *
     * @param string $file The source file.
     * 
     * @return PHP_Reflection_AST_MethodI
     */
    private static function _testParseMethod($file)
    {
        $packages = self::parseSource("/parser/methods/{$file}");
        self::assertEquals(1, $packages->count());
        
        $package = $packages->current();
        self::assertEquals(1, $package->getTypes()->count());
        
        $type = $package->getTypes()->current();
        self::assertEquals(1, $type->getMethods()->count());
        
        $method = $type->getMethods()->current();
        self::assertNotNull($method);
        self::assertType('PHP_Reflection_AST_MethodI', $method);
        
        return $method;
    }
    
}