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
 * @since     0.10.0
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Autoload} class.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2012 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 * @since     0.10.0
 *
 * @covers PHP_Depend_Autoload
 * @group pdepend
 * @group pdepend::autoload
 * @group unittest
 */
class PHP_Depend_AutoloadTest extends PHP_Depend_AbstractTest
{
    /**
     * The original include path.
     *
     * @var string
     */
    protected $includePath = null;

    /**
     * Stores the original include path.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->includePath = get_include_path();
    }

    /**
     * Restores the original include path.
     *
     * @return void
     */
    protected function tearDown()
    {
        set_include_path($this->includePath);

        foreach (spl_autoload_functions() as $callback) {
            if ($callback[0] instanceof PHP_Depend_Autoload) {
                spl_autoload_unregister($callback);
            }
        }
        
        parent::tearDown();
    }

    /**
     * testAutoloadLoadsClassInPhpDependNamespace
     *
     * @return void
     */
    public function testAutoloadLoadsClassInPhpDependNamespace()
    {
        $className = 'PHP_Depend_AutoloadLoadsClassInPhpDependNamespace';

        $autoloader = new PHP_Depend_Autoload();
        $autoloader->register();

        set_include_path(self::createCodeResourceUriForTest());
        $exists = class_exists($className, true);
        set_include_path($this->includePath);

        self::assertTrue($exists);
    }

    /**
     * testAutoloadNotLoadsClassFromDifferentNamespace
     *
     * @return void
     */
    public function testAutoloadNotLoadsClassFromDifferentNamespace()
    {
        $className = 'PHP_AutoloadNotLoadsClassFromDifferentNamespace';

        $autoloader = new PHP_Depend_Autoload();
        $autoloader->register();

        set_include_path(self::createCodeResourceUriForTest());
        $exists = class_exists($className, true);
        set_include_path($this->includePath);

        self::assertFalse($exists);
    }
}
