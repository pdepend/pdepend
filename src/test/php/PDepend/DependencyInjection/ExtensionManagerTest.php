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

use PDepend\AbstractTestCase;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test cases for the {@link \PDepend\Application} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\DependencyInjection\ExtensionManager
 * @covers \PDepend\DependencyInjection\Extension
 * @covers \PDepend\DependencyInjection\TreeBuilder
 * @group unittest
 */
class ExtensionManagerTest extends AbstractTestCase
{
    public function testExtensionManager()
    {
        $extensionManager = new ExtensionManager();

        $this->assertSame(array(), $extensionManager->getActivatedExtensions());

        $message = null;

        try {
            $extensionManager->activateExtension('CannotFindIt');
        } catch (RuntimeException $exception) {
            $message = $exception->getMessage();
        }

        $this->assertSame(
            'Cannot find extension class "CannotFindIt" for PDepend. Maybe the plugin is not installed?',
            $message
        );
        $this->assertSame(array(), $extensionManager->getActivatedExtensions());

        $message = null;

        try {
            $extensionManager->activateExtension('PDepend\\DependencyInjection\\ExtensionManager');
        } catch (RuntimeException $exception) {
            $message = $exception->getMessage();
        }

        $this->assertSame(
            'Class "PDepend\\DependencyInjection\\ExtensionManager" is not a valid Extension',
            $message
        );
        $this->assertSame(array(), $extensionManager->getActivatedExtensions());

        $extensionManager->activateExtension('PDepend\\TestExtension');
        $extensions = $extensionManager->getActivatedExtensions();

        $this->assertSame(array('test'), array_keys($extensions));

        $extension = $extensions['test'];

        $this->assertInstanceOf('PDepend\\TestExtension', $extension);
        $this->assertSame(array(), $extension->getCompilerPasses());

        $container = new ContainerBuilder();
        $extension->load(array('foo' => 'bar'), $container);

        $this->assertSame(array('foo' => 'bar'), $container->getParameter('test.parameters'));
    }
}
