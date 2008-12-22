<?php
/**
 * This file is part of PHP_Depend.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Metrics/NPathComplexity/Analyzer.php';

/**
 * Test case for the npath complexity analyzer.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_NPathComplexity_AnalyzerTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests a empty method an expects a
     *
     * @return unknown_type
     */
    public function testEmptyMethod()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 1), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests a method body with a simple if statement.
     *
     * @return void
     */
    public function testSimpleIfStatement()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 2), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests a method body with a simple if statement with dynamic identifier.
     *
     * @return void
     */
    public function testIfStatementWithNestedDynamicIdentifier()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SELF, 'self', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_DOUBLE_COLON, '::', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$var', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '}', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 2), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the analyzer implementation against consecutive if-statements.
     *
     * @return void
     */
    public function testConsecutiveIfStatements()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 80), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the analyzer implementation against multiple if-else-if statements.
     *
     * @return void
     */
    public function testConsecutiveIfElseIfStatements()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ELSE, 'else', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ELSE, 'else', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ELSE, 'else', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 4), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the analyzer implementation against multiple if-elseif statements.
     *
     * @return void
     */
    public function testConsecutiveIfElsifStatements()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ELSEIF, 'elseif', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ELSEIF, 'elseif', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ELSE, 'else', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $function = $this->getMock('PHP_Depend_Code_Function', array(), array(null), '', false);
        $function->expects($this->once())
                 ->method('getTokens')
                 ->will($this->returnValue($tokens));
        $function->expects($this->any())
                 ->method('getUUID')
                 ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitFunction($function);

        $this->assertEquals(array('npath' => 4), $analyzer->getNodeMetrics($function));
    }

    /**
     * Tests the analyzer implementation against an empty while statement.
     *
     * @return void
     */
    public function testEmptyWhileStatement()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_WHILE, 'while', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BOOLEAN_OR, '||', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FALSE, 'false', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 3), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the anaylzer with nested while statements.
     *
     * @return void
     */
    public function testNestedWhileStatements()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_WHILE, 'while', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BOOLEAN_OR, '||', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FALSE, 'false', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_WHILE, 'while', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FALSE, 'false', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ECHO, 'echo', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CONSTANT_ENCAPSED_STRING, "'echo'", 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 5), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the npath algorithm with a simple do-while statement.
     *
     * @return void
     */
    public function testSimpleDoStatement()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_DO, 'do', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ECHO, 'echo', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$a', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LOGICAL_OR, 'or', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$b', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_WHILE, 'while', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$a', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LOGICAL_AND, 'and', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$b', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 3), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the analyzer with a simple for statement.
     *
     * @return void
     */
    public function testSimpleForStatement()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_WHILE, 'for', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$i', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '0', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$i', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ANGLE_BRACKET_OPEN, '<', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '42', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_INC, '++', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$i', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 2), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the analyzer with a complex for statement.
     *
     * @return void
     */
    public function testComplexForStatement()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FOR, 'for', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$i', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '0', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COMMA, ',', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$j', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '42', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$i', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ANGLE_BRACKET_OPEN, '<', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$j', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$j', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ANGLE_BRACKET_CLOSE, '>', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '23', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BOOLEAN_OR, '||', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$j', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ANGLE_BRACKET_OPEN, '<', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '42', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_DEC, '--', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$i', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COMMA, ',', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_INC, '++', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$j', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 4), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the analyzer implementation with a simple foreach statement.
     *
     * @return void
     */
    public function testSimpleForeachStatement()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FOREACH, 'foreach', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$array', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_AS, 'as', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$key', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_DOUBLE_ARROW, '=>', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$value', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 2), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with a simple return statement.
     *
     * @return void
     */
    public function testSimpleReturnStatement()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_RETURN, 'return', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 1), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with a return statement that contains boolean expressions.
     *
     * @return void
     */
    public function testReturnStatementWithBooleanExpressions()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_RETURN, 'return', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FALSE, 'false', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BOOLEAN_OR, '||', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'bar', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 2), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with a return statement that contains a conditional.
     *
     * @return void
     */
    public function testReturnStatementWithConditionalStatement()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_RETURN, 'return', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$a', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$b', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$c', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 5), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with a simple switch statement that contains one case
     * child.
     *
     * @return void
     */
    public function testSimpleSwitchStatement()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SWITCH, 'switch', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CASE, 'case', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '1', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_INC, '++', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$i', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BREAK, 'break', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 1), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with a switch statement that contains multiple case
     * statements.
     *
     * @return void
     */
    public function testSwitchStatementWithMultipleCaseStatements()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SWITCH, 'switch', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CASE, 'case', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '1', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CASE, 'case', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '2', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_INC, '++', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$i', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BREAK, 'break', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CASE, 'case', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '3', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CASE, 'case', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '4', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CASE, 'case', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '5', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_DEC, '--', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$i', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BREAK, 'break', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 5), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with a switch statement that contains complex case
     * statements.
     *
     * @return void
     */
    public function testSwitchStatementWithComplexCaseStatements()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SWITCH, 'switch', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'a', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CASE, 'case', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '0', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CASE, 'case', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '1', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_WHILE, 'for', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BREAK, 'break', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CASE, 'case', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '2', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_DO, 'do', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_WHILE, 'while', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BREAK, 'break', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CASE, 'case', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LNUMBER, '3', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BREAK, 'break', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_DEFAULT, 'default', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_WHILE, 'while', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BREAK, 'break', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 8), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with a simple try statement.
     *
     * @return void
     */
    public function testSimpleTryCatchStatement()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRY, 'try', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CATCH, 'catch', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'E1', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$e', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 2), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with a try statement with multiple catch statements.
     *
     * @return void
     */
    public function testTryStatementWithMutlipleCatchStatements()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRY, 'try', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CATCH, 'catch', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'E1', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$e', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CATCH, 'catch', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'E2', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$e', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CATCH, 'catch', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'E3', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$e', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CATCH, 'catch', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'E4', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$e', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 5), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with a try statement with nested if statements.
     *
     * @return void
     */
    public function testTryCatchStatementWithNestedIfStatements()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRY, 'try', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CATCH, 'catch', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_STRING, 'E1', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$e', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_FALSE, 'false', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_ELSE, 'else', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_IF, 'if', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 5), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with a conditional statement.
     *
     * @return void
     */
    public function testSimpleConditionalStatement()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$a', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$b', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$c', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 5), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with nested conditional statements.
     *
     * @return void
     */
    public function testTwoNestedConditionalStatements()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$a', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$a', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$b', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$c', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$c', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 9), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with nested conditional statements.
     *
     * @return void
     */
    public function testThreeNestedConditionalStatements()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$a', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_EQUAL, '=', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$a', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$b', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '(', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$c', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$b', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$a', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$c', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 13), $analyzer->getNodeMetrics($method));
    }

    /**
     * Tests the algorithm with a conditional statement with boolean/logical
     * expressions.
     *
     * @return void
     */
    public function testConditionalStatementWithLogicalExpressions()
    {
        $tokens = array(
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$a', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LOGICAL_OR, 'or', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_TRUE, 'true', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$b', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$c', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LOGICAL_AND, 'and', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$c', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_COLON, ':', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$d', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_LOGICAL_XOR, 'xor', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_VARIABLE, '$e', 0, 0),
            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_SEMICOLON, ';', 0, 0),

            new PHP_Depend_Token(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}', 0, 0),
        );

        $method = $this->getMock('PHP_Depend_Code_Method', array(), array(null), '', false);
        $method->expects($this->once())
               ->method('getTokens')
               ->will($this->returnValue($tokens));
        $method->expects($this->any())
               ->method('getUUID')
               ->will($this->returnValue('uuid'));

        $analyzer = new PHP_Depend_Metrics_NPathComplexity_Analyzer();
        $analyzer->visitMethod($method);

        $this->assertEquals(array('npath' => 6), $analyzer->getNodeMetrics($method));
    }
}
?>
