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
 * Test case for bug #16.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @ticket 16
 *
 * @group regressiontest
 */
class ParserBug016Test extends AbstractRegressionTestCase
{
    /**
     * Tests that the parser detect a type within an instance of operator.
     *
     * <code>
     * if ($object instanceof SplObjectStorage) {
     *
     * }
     * </code>
     *
     * http://bugs.pdepend.org/index.php?do=details&task_id=16
     */
    public function testParserDetectsTypeWithinInstanceOfOperator(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $dependencies = $function->getDependencies();

        static::assertEquals(1, $dependencies->count());
        static::assertEquals('SplObjectStorage', $dependencies[0]->getImage());
    }

    /**
     * Tests that the parser ignores dynamic(with variables) instanceof operations.
     *
     * <code>
     * $class = 'SplObjectStorage';
     * if ($object instanceof $class) {
     *
     * }
     * </code>
     *
     * http://bugs.pdepend.org/index.php?do=details&task_id=16
     *
     * @todo TODO: It would be a cool feature if PDepend would replace such
     *             combinations (T_VARIABLE = T_CONSTANT_ENCAPSED_STRING with
     *             T_INSTANCEOF + T_VARIABLE).
     */
    public function testParserIgnoresDynamicInstanceOfOperator(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $dependencies = $function->getDependencies();

        static::assertEquals(0, $dependencies->count());
    }
}
