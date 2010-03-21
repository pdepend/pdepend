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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case related to bug 70.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Bugs_ClosureResultsInExceptionBug070Test extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the parser does not throw an exception when it detects a
     * lmabda function on file level.
     *
     * @return void
     */
    public function testParserHandlesLambdaFunctionOnFileLevelBug70()
    {
        self::parseSource('bugs/070-001-closure-results-in-exception.php');
    }

    /**
     * Tests that the parser handles a lambda function with parameters.
     *
     * @return void
     */
    public function testParserHandlesLambdaFunctionWithParametersBug70()
    {
        self::parseSource('bugs/070-002-closure-results-in-exception.php');
    }

    /**
     * Tests that the parser handles a closure function with bound variables.
     *
     * @return void
     */
    public function testParserHandlesClosureFunctionWithBoundVariableBug70()
    {
        self::parseSource('bugs/070-003-closure-results-in-exception.php');
    }

    /**
     * Tests that the parser handles a closure function with bound variables.
     *
     * @return void
     */
    public function testParserHandlesClosureFunctionWithBoundVariableByRefBug70()
    {
        self::parseSource('bugs/070-004-closure-results-in-exception.php');
    }

    /**
     * Tests that the parser handles a closure function with bound variables.
     *
     * @return void
     */
    public function testParserThrowsExceptionForInvalidBoundClosureVariableBug70()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_UnexpectedTokenException',
            'Unexpected token: lexical, line: 3, col: 17, file: '
        );

        self::parseSource('bugs/070-005-closure-results-in-exception.php');
    }

    /**
     * Tests that the parser handles a nested function within a function.
     *
     * @return void
     */
    public function testParserHandlesFunctionDeclarationWithinFunctionDeclarationBug70()
    {
        $packages = self::parseSource('bugs/070-006-closure-results-in-exception.php');

        $functions = $packages->current()->getFunctions();
        $this->assertSame(2, count($functions));
        $this->assertSame('bar', $functions->current()->getName());
        $functions->next();
        $this->assertSame('foo', $functions->current()->getName());
    }

    /**
     * Tests that the parser handles a nested closure within a function declaration.
     *
     * @return void
     */
    public function testParserHandlesClosureWithinFunctionDeclarationBug70()
    {
        $packages = self::parseSource('bugs/070-007-closure-results-in-exception.php');
        $function = $packages->current()
                              ->getFunctions()
                              ->current();

        $this->assertSame('foo', $function->getName());
    }
}
?>
