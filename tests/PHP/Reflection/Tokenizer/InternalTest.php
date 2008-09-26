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
 * @subpackage Tokenizer
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Reflection/Tokenizer/Internal.php';

/**
 * Test case for the internal tokenizer implementation.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Tokenizer
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Tokenizer_InternalTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests the tokenizer with a source file that contains only classes.
     *
     * @return void
     */
    public function testInternalTokenizerWithClasses()
    {
        $sourceFile = self::createResourceURI('tokenizer/abstract_and_concrete_class.php');
        $tokenizer  = new PHP_Reflection_Tokenizer_Internal($sourceFile);
        
        $expected = array(
            PHP_Reflection_TokenizerI::T_OPEN_TAG,
            PHP_Reflection_TokenizerI::T_DOC_COMMENT,
            PHP_Reflection_TokenizerI::T_ABSTRACT,
            PHP_Reflection_TokenizerI::T_CLASS,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE,
            PHP_Reflection_TokenizerI::T_CLASS,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_EXTENDS,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Reflection_TokenizerI::T_PUBLIC,
            PHP_Reflection_TokenizerI::T_FUNCTION,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_DOUBLE_COLON,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Reflection_TokenizerI::T_SEMICOLON,
            PHP_Reflection_TokenizerI::T_VARIABLE,
            PHP_Reflection_TokenizerI::T_EQUAL,
            PHP_Reflection_TokenizerI::T_CONSTANT_ENCAPSED_STRING,
            PHP_Reflection_TokenizerI::T_SEMICOLON,
            PHP_Reflection_TokenizerI::T_VARIABLE,
            PHP_Reflection_TokenizerI::T_EQUAL,
            PHP_Reflection_TokenizerI::T_TRUE,
            PHP_Reflection_TokenizerI::T_SEMICOLON,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE
        );
        
        $this->assertEquals($sourceFile, (string) $tokenizer->getSourceFile());
        
        foreach ($expected as $id) {
            $token = $tokenizer->next();
            
            $this->assertNotNull($token);
            $this->assertEquals($id, $token[0]);
        }
    }
 
    /**
     * Tests the tokenizer with a source file that contains mixed content of
     * classes and functions.
     *
     * @return void
     */   
    public function testInternalTokenizerWithMixedContent()
    {
        $sourceFile = self::createResourceURI('tokenizer/function_and_class.php');
        $tokenizer  = new PHP_Reflection_Tokenizer_Internal($sourceFile);
        
        $expected = array(
            array(PHP_Reflection_TokenizerI::T_OPEN_TAG, 1),
            array(PHP_Reflection_TokenizerI::T_COMMENT, 2),
            array(PHP_Reflection_TokenizerI::T_FUNCTION, 5),
            array(PHP_Reflection_TokenizerI::T_STRING, 5),
            array(PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN, 5),
            array(PHP_Reflection_TokenizerI::T_VARIABLE, 5),
            array(PHP_Reflection_TokenizerI::T_COMMA, 5),
            array(PHP_Reflection_TokenizerI::T_VARIABLE, 5),
            array(PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE, 5),
            array(PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN, 5),
            array(PHP_Reflection_TokenizerI::T_NEW, 6),
            array(PHP_Reflection_TokenizerI::T_STRING, 6),
            array(PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN, 6),
            array(PHP_Reflection_TokenizerI::T_VARIABLE, 6),
            array(PHP_Reflection_TokenizerI::T_COMMA, 6),
            array(PHP_Reflection_TokenizerI::T_VARIABLE, 6),
            array(PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE, 6),
            array(PHP_Reflection_TokenizerI::T_SEMICOLON, 6),
            array(PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE, 7),
            array(PHP_Reflection_TokenizerI::T_DOC_COMMENT, 10),
            array(PHP_Reflection_TokenizerI::T_CLASS, 13),
            array(PHP_Reflection_TokenizerI::T_STRING, 13),
            array(PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN, 13),
            array(PHP_Reflection_TokenizerI::T_COMMENT, 14),
            array(PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE, 15),
            array(PHP_Reflection_TokenizerI::T_CLOSE_TAG, 16)
        );
        
        $this->assertEquals($sourceFile, (string) $tokenizer->getSourceFile());
        
        foreach ($expected as $idAndLine) {
            $token = $tokenizer->next();
            
            $this->assertNotNull($token);
            $this->assertEquals($idAndLine[0], $token[0]);
            $this->assertEquals($idAndLine[1], $token[2]);
        }
    }
    
    /**
     * Tests that the tokenizer returns <b>T_BOF</b> if there is no previous
     * token.
     *
     * @return void
     */
    public function testInternalTokenizerReturnsBOFTokenForPrevCall()
    {
        $sourceFile = realpath(dirname(__FILE__) . '/../_code/func_class.php');
        $tokenizer  = new PHP_Reflection_Tokenizer_Internal($sourceFile);
        
        $this->assertEquals(PHP_Reflection_TokenizerI::T_BOF, $tokenizer->prev());
    }
    
    /**
     * Tests the tokenizer with a combination of procedural code and functions.
     *
     * @return void
     */
    public function testInternalTokenizerWithProceduralCodeAndFunction()
    {
        $sourceFile = self::createResourceURI('tokenizer/function_and_function_call.php');
        $tokenizer  = new PHP_Reflection_Tokenizer_Internal($sourceFile);
        
        $expected = array(
            PHP_Reflection_TokenizerI::T_OPEN_TAG,
            PHP_Reflection_TokenizerI::T_FUNCTION,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Reflection_TokenizerI::T_VARIABLE,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Reflection_TokenizerI::T_VARIABLE,
            PHP_Reflection_TokenizerI::T_EQUAL,
            PHP_Reflection_TokenizerI::T_NEW,
            PHP_Reflection_TokenizerI::T_VARIABLE,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Reflection_TokenizerI::T_SEMICOLON,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE,
            PHP_Reflection_TokenizerI::T_VARIABLE,
            PHP_Reflection_TokenizerI::T_EQUAL,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Reflection_TokenizerI::T_CONSTANT_ENCAPSED_STRING,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Reflection_TokenizerI::T_SEMICOLON,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Reflection_TokenizerI::T_ARRAY,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Reflection_TokenizerI::T_VARIABLE,
            PHP_Reflection_TokenizerI::T_COMMA,
            PHP_Reflection_TokenizerI::T_CONSTANT_ENCAPSED_STRING,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Reflection_TokenizerI::T_SEMICOLON,
            PHP_Reflection_TokenizerI::T_CLOSE_TAG
        );
        
        $this->assertEquals($sourceFile, (string) $tokenizer->getSourceFile());
        
        foreach ($expected as $id) {
            $token = $tokenizer->next();
            
            $this->assertNotNull($token);
            $this->assertEquals($id, $token[0]);
        }
    }
    
    /**
     * Test case for undetected static method call added.
     *
     * @return void
     */
    public function testInternalTokenizerStaticCallBug01()
    {
        $sourceFile = self::createResourceURI('bugs/01.php');
        $tokenizer  = new PHP_Reflection_Tokenizer_Internal($sourceFile);
        
        $expected = array(
            PHP_Reflection_TokenizerI::T_OPEN_TAG,
            PHP_Reflection_TokenizerI::T_DOC_COMMENT,
            PHP_Reflection_TokenizerI::T_CLASS,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Reflection_TokenizerI::T_PUBLIC,
            PHP_Reflection_TokenizerI::T_FUNCTION,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_DOUBLE_COLON,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Reflection_TokenizerI::T_SEMICOLON,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE,            
        );
        
        foreach ($expected as $id) {
            $token = $tokenizer->next();
            
            $this->assertNotNull($token);
            $this->assertEquals($id, $token[0]);
        }
    }
    
    /**
     * Tests that the tokenizer handles the following syntax correct.
     * 
     * <code>
     * class Foo {
     *     public function formatBug09($x) {
     *         self::${$x};
     *     }
     * }
     * </code>
     * 
     * http://bugs.xplib.de/index.php?do=details&task_id=9&project=3
     * 
     * @return void
     */
    public function testInternalTokenizerDollarSyntaxBug09()
    {
        $sourceFile = self::createResourceURI('bugs/09.php');
        $tokenizer  = new PHP_Reflection_Tokenizer_Internal($sourceFile);
        
        $expected = array(
            PHP_Reflection_TokenizerI::T_OPEN_TAG,
            PHP_Reflection_TokenizerI::T_DOC_COMMENT,
            PHP_Reflection_TokenizerI::T_CLASS,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Reflection_TokenizerI::T_PUBLIC,
            PHP_Reflection_TokenizerI::T_FUNCTION,
            PHP_Reflection_TokenizerI::T_STRING,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN,
            PHP_Reflection_TokenizerI::T_VARIABLE,
            PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Reflection_TokenizerI::T_SELF, // SELF
            PHP_Reflection_TokenizerI::T_DOUBLE_COLON,
            PHP_Reflection_TokenizerI::T_DOLLAR,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN,
            PHP_Reflection_TokenizerI::T_VARIABLE,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE,
            PHP_Reflection_TokenizerI::T_SEMICOLON,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE,
            PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE,            
        );
        
        foreach ($expected as $id) {
            $token = $tokenizer->next();
            
            $this->assertNotNull($token);
            $this->assertEquals($id, $token[0]);
        }
    }
    
    /**
     * Test case for the inline html bug.
     *
     * @return void
     */
    public function testTokenizerWithInlineHtmlBug24()
    {
        $sourceFile = dirname(__FILE__) . '/../_code/bugs/24.php';
        $tokenizer  = new PHP_Reflection_Tokenizer_Internal($sourceFile);
        
        $expected = array(
            array(PHP_Reflection_TokenizerI::T_OPEN_TAG, 1),
            array(PHP_Reflection_TokenizerI::T_CLASS, 2),
            array(PHP_Reflection_TokenizerI::T_STRING, 2),
            array(PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN, 3),
            array(PHP_Reflection_TokenizerI::T_FUNCTION, 4),
            array(PHP_Reflection_TokenizerI::T_STRING, 4),
            array(PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN, 4),
            array(PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE, 4),
            array(PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN, 5),
            array(PHP_Reflection_TokenizerI::T_CLOSE_TAG, 6),
            array(PHP_Reflection_TokenizerI::T_OPEN_TAG, 7) ,
            array(PHP_Reflection_TokenizerI::T_ECHO, 7),
            array(PHP_Reflection_TokenizerI::T_STRING, 7),
            array(PHP_Reflection_TokenizerI::T_SEMICOLON, 7),
            array(PHP_Reflection_TokenizerI::T_CLOSE_TAG,  7),
            array(PHP_Reflection_TokenizerI::T_OPEN_TAG, 8),
            array(PHP_Reflection_TokenizerI::T_ECHO, 8),
            array(PHP_Reflection_TokenizerI::T_STRING, 8),
            array(PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN, 8),
            array(PHP_Reflection_TokenizerI::T_VARIABLE, 8),
            array(PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE, 8),
            array(PHP_Reflection_TokenizerI::T_SEMICOLON, 8),
            array(PHP_Reflection_TokenizerI::T_CLOSE_TAG, 8),
            array(PHP_Reflection_TokenizerI::T_OPEN_TAG, 10),
            array(PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE, 11),
            array(PHP_Reflection_TokenizerI::T_FUNCTION, 13),
            array(PHP_Reflection_TokenizerI::T_STRING, 13),
            array(PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN, 13),
            array(PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE, 13),
            array(PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN, 14),
            array(PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE, 16),
            array(PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE, 17),         
        );
        
        foreach ($expected as $idAndLine) {
            $token = $tokenizer->next();
            
            $this->assertNotNull($token);
            $this->assertEquals($idAndLine[0], $token[0]);
            $this->assertEquals($idAndLine[1], $token[2]);
        }
    }
}