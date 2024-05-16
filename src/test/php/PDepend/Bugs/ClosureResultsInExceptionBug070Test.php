<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Bugs;

use PDepend\Source\Parser\UnexpectedTokenException;

/**
 * Test case related to bug 70.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group regressiontest
 */
class ClosureResultsInExceptionBug070Test extends AbstractRegressionTestCase
{
    /**
     * Tests that the parser does not throw an exception when it detects a
     * lambda function on file level.
     */
    public function testParserHandlesLambdaFunctionOnFileLevelBug70(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser handles a lambda function with parameters.
     */
    public function testParserHandlesLambdaFunctionWithParametersBug70(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser handles a closure function with bound variables.
     */
    public function testParserHandlesClosureFunctionWithBoundVariableBug70(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser handles a closure function with bound variables.
     */
    public function testParserHandlesClosureFunctionWithBoundVariableByRefBug70(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser handles a closure function with bound variables.
     */
    public function testParserThrowsExceptionForInvalidBoundClosureVariableBug70(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser handles a nested function within a function.
     */
    public function testParserHandlesFunctionDeclarationWithinFunctionDeclarationBug70(): void
    {
        $functions = $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions();

        static::assertEquals('bar', $functions->current()->getImage());
        $functions->next();
        static::assertEquals('foo', $functions->current()->getImage());
    }

    /**
     * Tests that the parser handles a nested closure within a function declaration.
     */
    public function testParserHandlesClosureWithinFunctionDeclarationBug70(): void
    {
        $function = $this->parseCodeResourceForTest()
            ->current()
            ->getFunctions()
            ->current();

        static::assertEquals('foo', $function->getImage());
    }
}
