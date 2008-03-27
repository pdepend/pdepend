<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

require_once 'PHP/Depend/Parser.php';
require_once 'PHP/Depend/Code/DefaultBuilder.php';
require_once 'PHP/Depend/Code/Tokenizer/InternalTokenizer.php';

/**
 * Test case implementation for the PHP_Depend code parser.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_ParserTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the main parse method.
     *
     * @return void
     */
    public function testParseMixedCode()
    {
        $sourceFile = dirname(__FILE__) . '/data/mixed_code.php';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $builder    = new PHP_Depend_Code_DefaultBuilder();
        $parser     = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
        
        $expected = array(
            'pkg1'                                        =>  true, 
            'pkg2'                                        =>  true, 
            'pkg3'                                        =>  true,
            PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE  =>  true
        );
        $packages = array();
        
        $this->assertEquals(4, $builder->getPackages()->count());
        
        foreach ($builder->getPackages() as $package) {
            $this->assertArrayHasKey($package->getName(), $expected);
            unset($expected[$package->getName()]);
            $packages[$package->getName()] = $package;
        }
        $this->assertEquals(0, count($expected));
        
        $this->assertEquals(1, $packages['pkg1']->getFunctions()->count());
        $this->assertEquals(1, $packages['pkg1']->getClasses()->count());
        $this->assertFalse($packages['pkg1']->getClasses()->current()->isAbstract());
        
        $this->assertEquals(1, $packages['pkg2']->getClasses()->count());
        $this->assertTrue($packages['pkg2']->getClasses()->current()->isAbstract());
        
        $this->assertEquals(1, $packages['pkg3']->getClasses()->count());
        $this->assertTrue($packages['pkg3']->getClasses()->current()->isAbstract());
    }
    
    /**
     * Tests that the parser throws an exception if it reaches the end of the
     * stream but not all class curly braces are closed.
     *
     * @return void
     */
    public function testParserWithUnclosedClassFail()
    {
        $this->setExpectedException(
            'RuntimeException', 
            'Invalid state, unclosed class body.'
        );
        
        $sourceFile = dirname(__FILE__) . '/data/not_closed_class.txt';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $builder    = new PHP_Depend_Code_DefaultBuilder();
        $parser     = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
    }
    
    /**
     * Tests that the parser throws an exception if it reaches the end of the
     * stream but not all function curly braces are closed.
     *
     * @return void
     */
    public function testParserWithUnclosedFunctionFail()
    {
        $this->setExpectedException('RuntimeException', 'Invalid state, unclosed function body.');
        
        $sourceFile = dirname(__FILE__) . '/data/not_closed_function.txt';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $builder    = new PHP_Depend_Code_DefaultBuilder();
        $parser     = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
    }
    
    /**
     * Tests that the parser throws an exception if it finds an invalid 
     * function signature.
     *
     * @return void
     */
    public function testParserWithInvalidFunction1Fail()
    {
        $this->setExpectedException('RuntimeException', 'Invalid function signature.');
        
        $sourceFile = dirname(__FILE__) . '/data/invalid_function1.txt';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $builder    = new PHP_Depend_Code_DefaultBuilder();
        $parser     = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
    }
    
    /**
     * Tests that the parser throws an exception if it finds an invalid 
     * function signature.
     *
     * @return void
     */
    public function testParserWithInvalidFunction2Fail()
    {
        $sourceFile = dirname(__FILE__) . '/data/invalid_function2.txt';
        
        $this->setExpectedException(
            'RuntimeException', 
            "Invalid token \"Bar\" on line 3 in file: {$sourceFile}."
        );
        
        $tokenizer = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $builder   = new PHP_Depend_Code_DefaultBuilder();
        $parser    = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
    }
    
    /**
     * Test case for parser bug 01 that doesn't add dependencies for static
     * method calls.
     * 
     * @return void
     */
    public function testParserStaticCallBug01()
    {
        $sourceFile = dirname(__FILE__) . '/data/bugs/01.php';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $builder    = new PHP_Depend_Code_DefaultBuilder();
        $parser     = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
        
        $packages = $builder->getPackages();
        $packages->next();
        $this->assertEquals(2, $packages->count());
                
        $package = $packages->current();
        $this->assertEquals('package0', $package->getName());
        $classes  = $package->getClasses();
        $this->assertEquals(1, $classes->count()); 
        $methods  = $classes->current()->getMethods();
        $this->assertEquals(1, $methods->count());
        $this->assertEquals(1, $methods->current()->getDependencies()->count());
    }
    
    /**
     * Tests that the parser handles function with reference return values
     * correct.
     *
     * @return void
     */
    public function testParserReferenceReturnValueBug08()
    {
        $sourceFile = dirname(__FILE__) . '/data/bugs/08.php';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $builder    = new PHP_Depend_Code_DefaultBuilder();
        $parser     = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
        
        $package = $builder->getPackages()->current();

        // Get function
        $function = $package->getFunctions()->current();
        $this->assertEquals('barBug08', $function->getName());

        // Get class method
        $method = $package->getClasses()
                          ->current()
                          ->getMethods()
                          ->current();
        $this->assertEquals('fooBug08', $method->getName());
    }
    
    /**
     * Tests that the parser sets the correct line number for a function.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionLineNumber()
    {
        $sourceFile = dirname(__FILE__) . '/data/mixed_code.php';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $builder    = new PHP_Depend_Code_DefaultBuilder();
        $parser     = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
        
        $packages = $builder->getPackages();
        $packages->next();
        
        $function = $packages->current()->getFunctions()->current();
        $this->assertEquals(7, $function->getLine());
    }
    
    /**
     * Tests that the parser sets the correct tokens for a function.
     *
     * @return void
     */
    public function testParserSetsCorrectFunctionTokens()
    {
        $sourceFile = dirname(__FILE__) . '/data/mixed_code.php';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $builder    = new PHP_Depend_Code_DefaultBuilder();
        $parser     = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
        
        $tokens = array(
            array(PHP_Depend_Code_Tokenizer::T_FOREACH, 'foreach', 8),
            array(PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN, '(', 8),
            array(PHP_Depend_Code_Tokenizer::T_VARIABLE, '$foo', 8),
            array(PHP_Depend_Code_Tokenizer::T_AS, 'as', 8),
            array(PHP_Depend_Code_Tokenizer::T_VARIABLE, '$bar', 8),
            array(PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE, ')', 8),
            array(PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN, '{', 8),
            array(PHP_Depend_Code_Tokenizer::T_STRING, 'FooBar', 9),
            array(PHP_Depend_Code_Tokenizer::T_DOUBLE_COLON, '::', 9),
            array(PHP_Depend_Code_Tokenizer::T_STRING, 'y', 9),
            array(PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN, '(', 9),
            array(PHP_Depend_Code_Tokenizer::T_VARIABLE, '$bar', 9),
            array(PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE, ')', 9),
            array(PHP_Depend_Code_Tokenizer::T_SEMICOLON, ';', 9),
            array(PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE, '}', 10),
        );
        
        $packages = $builder->getPackages();
        $packages->next();
        
        $function = $packages->current()->getFunctions()->current();
        $this->assertEquals($tokens, $function->getTokens());
    }
    
    /**
     * Tests that the parser sets the correct line number for a class.
     *
     * @return void
     */
    public function testParserSetsCorrectClassLineNumber()
    {
        $sourceFile = dirname(__FILE__) . '/data/mixed_code.php';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $builder    = new PHP_Depend_Code_DefaultBuilder();
        $parser     = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
        
        $packages = $builder->getPackages();
        $packages->next();
        $packages->next();

        $class = $packages->current()->getClasses()->current();

        $this->assertEquals(15, $class->getLine());
    }
    
    /**
     * Tests that the parser sets the correct line number for methods.
     *
     * @return void
     */
    public function testParserSetsCorrectMethodLineNumber()
    {
        $sourceFile = dirname(__FILE__) . '/data/mixed_code.php';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $builder    = new PHP_Depend_Code_DefaultBuilder();
        $parser     = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
        
        $packages = $builder->getPackages();
        $packages->next();
        $packages->next();

        $method = $packages->current()
                           ->getClasses()
                           ->current()
                           ->getMethods()
                           ->current();
 
        $this->assertEquals(16, $method->getLine());
    }
    
    /**
     * Tests that the parser handles PHP 5.3 object namespace + class chaining.
     *
     * @return void
     */
    public function testParserParseNewInstancePHP53()
    {
        $sourceFile = dirname(__FILE__) . '/data/php-5.3/new.txt';
        $tokenizer  = new PHP_Depend_Code_Tokenizer_InternalTokenizer($sourceFile);
        $builder    = new PHP_Depend_Code_DefaultBuilder();
        $parser     = new PHP_Depend_Parser($tokenizer, $builder);
        
        $parser->parse();
        
        $packages = $builder->getPackages();
        
        $this->assertEquals(3, $packages->count());
        $packages->next();
        $this->assertEquals('php::depend1', $packages->current()->getName());
        $packages->next();
        $this->assertEquals('php::depend2', $packages->current()->getName());
    }
}