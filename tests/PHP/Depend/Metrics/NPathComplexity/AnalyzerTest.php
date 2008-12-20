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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_SELF, 'self'),
            array(PHP_Depend_ConstantsI::T_DOUBLE_COLON, '::'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$var'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '}'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&'),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&'),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&'),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_ELSE, 'else'),
            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_ELSE, 'else'),
            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_ELSE, 'else'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_ELSEIF, 'elseif'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_ELSEIF, 'elseif'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_ELSE, 'else'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_WHILE, 'while'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_BOOLEAN_OR, '||'),
            array(PHP_Depend_ConstantsI::T_FALSE, 'false'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_WHILE, 'while'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_BOOLEAN_OR, '||'),
            array(PHP_Depend_ConstantsI::T_FALSE, 'false'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_WHILE, 'while'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&'),
            array(PHP_Depend_ConstantsI::T_FALSE, 'false'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_ECHO, 'echo'),
            array(PHP_Depend_ConstantsI::T_CONSTANT_ENCAPSED_STRING, "'echo'"),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_DO, 'do'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_ECHO, 'echo'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$a'),
            array(PHP_Depend_ConstantsI::T_LOGICAL_OR, 'or'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$b'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
            array(PHP_Depend_ConstantsI::T_WHILE, 'while'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$a'),
            array(PHP_Depend_ConstantsI::T_LOGICAL_AND, 'and'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$b'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_WHILE, 'for'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$i'),
            array(PHP_Depend_ConstantsI::T_EQUAL, '='),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '0'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$i'),
            array(PHP_Depend_ConstantsI::T_ANGLE_BRACKET_OPEN, '<'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '42'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),
            array(PHP_Depend_ConstantsI::T_INC, '++'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$i'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_FOR, 'for'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),

            array(PHP_Depend_ConstantsI::T_VARIABLE, '$i'),
            array(PHP_Depend_ConstantsI::T_EQUAL, '='),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '0'),
            array(PHP_Depend_ConstantsI::T_COMMA, ','),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$j'),
            array(PHP_Depend_ConstantsI::T_EQUAL, '='),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '42'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_VARIABLE, '$i'),
            array(PHP_Depend_ConstantsI::T_ANGLE_BRACKET_OPEN, '<'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$j'),
            array(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$j'),
            array(PHP_Depend_ConstantsI::T_ANGLE_BRACKET_CLOSE, '>'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '23'),
            array(PHP_Depend_ConstantsI::T_BOOLEAN_OR, '||'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$j'),
            array(PHP_Depend_ConstantsI::T_ANGLE_BRACKET_OPEN, '<'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '42'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_DEC, '--'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$i'),
            array(PHP_Depend_ConstantsI::T_COMMA, ','),
            array(PHP_Depend_ConstantsI::T_INC, '++'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$j'),

            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_FOREACH, 'foreach'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$array'),
            array(PHP_Depend_ConstantsI::T_AS, 'as'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$key'),
            array(PHP_Depend_ConstantsI::T_DOUBLE_ARROW, '=>'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$value'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_RETURN, 'return'),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_RETURN, 'return'),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&'),
            array(PHP_Depend_ConstantsI::T_FALSE, 'false'),
            array(PHP_Depend_ConstantsI::T_BOOLEAN_OR, '||'),
            array(PHP_Depend_ConstantsI::T_STRING, 'bar'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_RETURN, 'return'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$a'),
            array(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$b'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$c'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_SWITCH, 'switch'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CASE, 'case'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '1'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_INC, '++'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$i'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),
            array(PHP_Depend_ConstantsI::T_BREAK, 'break'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_SWITCH, 'switch'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_CASE, 'case'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '1'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_CASE, 'case'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '2'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_INC, '++'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$i'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),
            array(PHP_Depend_ConstantsI::T_BREAK, 'break'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CASE, 'case'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '3'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_CASE, 'case'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '4'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_CASE, 'case'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '5'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_DEC, '--'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$i'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),
            array(PHP_Depend_ConstantsI::T_BREAK, 'break'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_SWITCH, 'switch'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_STRING, 'a'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_CASE, 'case'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '0'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_CASE, 'case'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '1'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),

            array(PHP_Depend_ConstantsI::T_WHILE, 'for'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_BREAK, 'break'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CASE, 'case'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '2'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),

            array(PHP_Depend_ConstantsI::T_DO, 'do'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
            array(PHP_Depend_ConstantsI::T_WHILE, 'while'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_BREAK, 'break'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CASE, 'case'),
            array(PHP_Depend_ConstantsI::T_LNUMBER, '3'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_BREAK, 'break'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_DEFAULT, 'default'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),

            array(PHP_Depend_ConstantsI::T_WHILE, 'while'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_BREAK, 'break'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_TRY, 'try'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
            array(PHP_Depend_ConstantsI::T_CATCH, 'catch'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_STRING, 'E1'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$e'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_TRY, 'try'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CATCH, 'catch'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_STRING, 'E1'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$e'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CATCH, 'catch'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_STRING, 'E2'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$e'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CATCH, 'catch'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_STRING, 'E3'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$e'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CATCH, 'catch'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_STRING, 'E4'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$e'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_TRY, 'try'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
            array(PHP_Depend_ConstantsI::T_CATCH, 'catch'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_STRING, 'E1'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$e'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_FALSE, 'false'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
            array(PHP_Depend_ConstantsI::T_ELSE, 'else'),
            array(PHP_Depend_ConstantsI::T_IF, 'if'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_VARIABLE, '$a'),
            array(PHP_Depend_ConstantsI::T_EQUAL, '='),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$b'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$c'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_VARIABLE, '$a'),
            array(PHP_Depend_ConstantsI::T_EQUAL, '='),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?'),

            array(PHP_Depend_ConstantsI::T_VARIABLE, '$a'),
            array(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$b'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$c'),

            array(PHP_Depend_ConstantsI::T_COLON, ':'),

            array(PHP_Depend_ConstantsI::T_VARIABLE, '$c'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_VARIABLE, '$a'),
            array(PHP_Depend_ConstantsI::T_EQUAL, '='),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?'),

            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$a'),
            array(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$b'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),

            array(PHP_Depend_ConstantsI::T_PARENTHESIS_OPEN, '('),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$c'),
            array(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$b'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$a'),
            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),

            array(PHP_Depend_ConstantsI::T_PARENTHESIS_CLOSE, ')'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$c'),

            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_OPEN, '{'),

            array(PHP_Depend_ConstantsI::T_VARIABLE, '$a'),
            array(PHP_Depend_ConstantsI::T_LOGICAL_OR, 'or'),
            array(PHP_Depend_ConstantsI::T_TRUE, 'true'),
            array(PHP_Depend_ConstantsI::T_QUESTION_MARK, '?'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$b'),
            array(PHP_Depend_ConstantsI::T_BOOLEAN_AND, '&&'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$c'),
            array(PHP_Depend_ConstantsI::T_LOGICAL_AND, 'and'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$c'),
            array(PHP_Depend_ConstantsI::T_COLON, ':'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$d'),
            array(PHP_Depend_ConstantsI::T_LOGICAL_XOR, 'xor'),
            array(PHP_Depend_ConstantsI::T_VARIABLE, '$e'),
            array(PHP_Depend_ConstantsI::T_SEMICOLON, ';'),

            array(PHP_Depend_ConstantsI::T_CURLY_BRACE_CLOSE, '}'),
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
