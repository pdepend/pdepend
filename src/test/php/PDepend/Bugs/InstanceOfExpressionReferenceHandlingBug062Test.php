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
 * Test case for bug 62 where reference in an instanceof operator weren't handled
 * correct.
 *
 * http://tracker.pdepend.org/pdepend/issue_tracker/issue/62
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @group regressiontest
 */
class InstanceOfExpressionReferenceHandlingBug062Test extends AbstractRegressionTestCase
{
    /**
     * Tests that the parser handles an interface within an instanceof operator
     * correct.
     */
    public function testParserTreatsTypeInInstanceOfOperatorGenericWithInterface(): void
    {
        $namespace = $this->parseCodeResourceForTest()->current();

        static::assertSame(1, $namespace->getClasses()->count());
        $class = $namespace->getClasses()->current();
        static::assertSame('Bar', $class->getImage());

        static::assertSame(1, $namespace->getInterfaces()->count());
        $interface = $namespace->getInterfaces()->current();
        static::assertSame('IFoo', $interface->getImage());

        static::assertSame(1, $class->getMethods()->count());
        $method = $class->getMethods()->current();

        static::assertSame(
            $interface,
            $method->getDependencies()->current()
        );
    }

    /**
     * Tests that the parser handles an interface within an instanceof operator
     * correct.
     */
    public function testParserTreatsTypeInInstanceOfOperatorGenericWithClass(): void
    {
        $namespace = $this->parseCodeResourceForTest()->current();

        $classes = $namespace->getClasses();
        static::assertSame(2, $classes->count());
        $class1 = $classes->current();
        static::assertSame('Foo', $class1->getImage());
        $classes->next();
        $class2 = $classes->current();
        static::assertSame('Bar', $class2->getImage());

        static::assertSame(1, $class2->getMethods()->count());
        $method = $class2->getMethods()->current();
        static::assertSame($class1, $method->getDependencies()->current());
    }
}
