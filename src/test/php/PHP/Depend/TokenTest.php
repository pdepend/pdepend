<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/AbstractTest.php';
 
/**
 * Test case for the {@link PHP_Depend_Token} class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 *
 * @covers PHP_Depend_Token
 * @group pdepend
 * @group unittest
 */
class PHP_Depend_TokenTest extends PHP_Depend_AbstractTest
{
    /**
     * testConstructorSetsTypeProperty
     *
     * @return void
     */
    public function testConstructorSetsTypeProperty()
    {
        $token = new PHP_Depend_Token(1, 2, 4, 8, 16, 32);
        self::assertEquals(1, $token->type);
    }

    /**
     * testConstructorSetsImageProperty
     *
     * @return void
     */
    public function testConstructorSetsImageProperty()
    {
        $token = new PHP_Depend_Token(1, 2, 4, 8, 16, 32);
        self::assertEquals(2, $token->image);
    }

    /**
     * testConstructorSetsStartLineProperty
     *
     * @return void
     */
    public function testConstructorSetsStartLineProperty()
    {
        $token = new PHP_Depend_Token(1, 2, 4, 8, 16, 32);
        self::assertEquals(4, $token->startLine);
    }

    /**
     * testConstructorSetsEndLineProperty
     *
     * @return void
     */
    public function testConstructorSetsEndLineProperty()
    {
        $token = new PHP_Depend_Token(1, 2, 4, 8, 16, 32);
        self::assertEquals(8, $token->endLine);
    }

    /**
     * testConstructorSetsStartColumnProperty
     *
     * @return void
     */
    public function testConstructorSetsStartColumnProperty()
    {
        $token = new PHP_Depend_Token(1, 2, 4, 8, 16, 32);
        self::assertEquals(16, $token->startColumn);
    }

    /**
     * testConstructorSetsEndColumnProperty
     *
     * @return void
     */
    public function testConstructorSetsEndColumnProperty()
    {
        $token = new PHP_Depend_Token(1, 2, 4, 8, 16, 32);
        self::assertEquals(32, $token->endColumn);
    }
}
