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

/**
 * Test case related to bug 65.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group regressiontest
 */
class ClassDeclarationWithoutBodyBug065Test extends AbstractRegressionTestCase
{
    /**
     * Tests that the parser does not end in an endless loop when it detects an
     * interface without a body.
     */
    public function testInterfaceDeclarationWithoutBody(): void
    {
        $this->expectException(
            'RuntimeException'
        );
        $this->expectExceptionMessage(
            'Unexpected end of token stream in file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects an
     * interface declaration with extend but without a body.
     */
    public function testInterfaceDeclarationWithExtendWithoutBody(): void
    {
        $this->expectException(
            'RuntimeException'
        );
        $this->expectExceptionMessage(
            'Unexpected end of token stream in file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects an
     * interface declaration with extend with invalid end of interface list.
     */
    public function testInterfaceDeclarationWithInvalidInterfaceList(): void
    {
        $this->expectException(
            'RuntimeException'
        );
        $this->expectExceptionMessage(
            'Unexpected token: {, line: 2, col: 28, file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects a
     * class declaration without a body.
     */
    public function testClassDeclarationWithoutBody(): void
    {
        $this->expectException(
            'RuntimeException'
        );
        $this->expectExceptionMessage(
            'Unexpected end of token stream in file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects a
     * class declaration with extend but without a parent class name and a body.
     */
    public function testClassDeclarationWithExtendsWithoutClassName(): void
    {
        $this->expectException(
            'RuntimeException'
        );
        $this->expectExceptionMessage(
            'Unexpected end of token stream in file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects a
     * class declaration with implements but without a interface name and a body.
     */
    public function testClassDeclarationWithExtendsWithoutInterfaceName(): void
    {
        $this->expectException(
            'RuntimeException'
        );
        $this->expectExceptionMessage(
            'Unexpected end of token stream in file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects a
     * class declaration with parent interface but without a body.
     */
    public function testClassDeclarationWithParentInterfaceWithoutBody(): void
    {
        $this->expectException(
            'RuntimeException'
        );
        $this->expectExceptionMessage(
            'Unexpected end of token stream in file: '
        );

        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that the parser does not end in an endless loop when it detects a
     * class declaration with an incomplete parent interface list and without a body.
     */
    public function testClassDeclarationWithIncompleteParentInterfaceWithoutBody(): void
    {
        $this->expectException(
            'RuntimeException'
        );
        $this->expectExceptionMessage(
            'Unexpected end of token stream in file: '
        );

        $this->parseCodeResourceForTest();
    }
}
