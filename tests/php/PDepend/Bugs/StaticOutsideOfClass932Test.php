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

use PDepend\Source\Language\PHP\AbstractPHPParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Test case for ticket 932, parsing "static" outside of a class scope.
 *
 * Using "static" outside of a class scope is a runtime error in PHP,
 * but it is syntactically valid, so the parser must not fail on it.
 */
#[CoversClass(AbstractPHPParser::class)]
#[Group('unittest')]
class StaticOutsideOfClass932Test extends AbstractRegressionTestCase
{
    /**
     * Tests that a static method call in the global scope can be parsed.
     */
    public function testStaticMethodCallInGlobalScope(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that a static method call in a closure can be parsed.
     */
    public function testStaticMethodCallInClosure(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that a "new static()" allocation in a closure can be parsed.
     */
    public function testStaticAllocationInClosure(): void
    {
        $this->parseCodeResourceForTest();
    }

    /**
     * Tests that static constant, property and class-constant accesses in
     * the global scope can be parsed.
     */
    public function testStaticMemberAccessInGlobalScope(): void
    {
        $this->parseCodeResourceForTest();
    }
}
