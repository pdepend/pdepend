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

namespace PDepend\DependencyInjection;

use Exception;
use PDepend\AbstractTest;
use PDepend\TestExtension;
use ReflectionMethod;

/**
 * Test cases for the {@link \PDepend\Application} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\DependencyInjection\TreeBuilderFactory
 * @group unittest
 */
class ConfigurationTest extends AbstractTest
{
    /**
     * @return bool
     */
    private function isStronglyTyped()
    {
        try {
            $method = new ReflectionMethod(
                'Symfony\\Component\\Config\\Definition\\ConfigurationInterface',
                'getConfigTreeBuilder'
            );

            if (method_exists($method, 'hasReturnType') && $method->hasReturnType()) {
                return true;
            }
        } catch (Exception $exception) {
            // keep "weak"
        }

        return false;
    }

    public function testSymfonyGte7()
    {
        if (!self::isStronglyTyped()) {
            $this->markTestSkipped('This test requires Symfony >= 7');
        }

        $config = new Configuration(array(new TestExtension()));

        $this->assertInstanceOf(
            'Symfony\\Component\\Config\\Definition\\Builder\\TreeBuilder',
            $config->getConfigTreeBuilder()
        );

        $method = new ReflectionMethod(
            'PDepend\\DependencyInjection\\Configuration',
            'getConfigTreeBuilder'
        );

        $this->assertSame(
            'Symfony\\Component\\Config\\Definition\\Builder\\TreeBuilder',
            $method->getReturnType()->getName()
        );
    }

    public function testSymfonyLt7()
    {
        if (self::isStronglyTyped()) {
            $this->markTestSkipped('This test requires Symfony < 7');
        }

        $config = new Configuration(array(new TestExtension()));

        $this->assertInstanceOf(
            'Symfony\\Component\\Config\\Definition\\Builder\\TreeBuilder',
            $config->getConfigTreeBuilder()
        );

        if (PHP_VERSION < 7) {
            return;
        }

        $method = new ReflectionMethod(
            'PDepend\\DependencyInjection\\Configuration',
            'getConfigTreeBuilder'
        );

        $this->assertNull($method->getReturnType());
    }
}
