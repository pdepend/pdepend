<?php
/**
 * This file is part of PDepend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace PHP\Depend\Report;

/**
 * Test case for the logger factory.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
class GeneratorFactoryTest extends \PHP_Depend_AbstractTest
{
    /**
     * Tests that {@link \PHP\Depend\Report\GeneratorFactory::createGenerator()}
     * returns the expected instance for a valid identifier.
     *
     * @return void
     */
    public function testCreateGeneratorWithValidIdentifier()
    {
        $factory = new GeneratorFactory();
        $generator = $factory->createGenerator('summary-xml', 'pdepend.xml');
        
        $this->assertInstanceOf(\PHP\Depend\Report\Summary\Xml::CLAZZ, $generator);
    }
    
    /**
     * Tests the singleton behaviour of the logger factory method 
     * {@link \PHP\Depend\Report\GeneratorFactory::createGenerator()}.
     *
     * @return void
     */
    public function testCreateGeneratorSingletonBehaviour()
    {
        $factory = new GeneratorFactory();
        $generator1 = $factory->createGenerator('summary-xml', 'pdepend1.xml');
        $generator2 = $factory->createGenerator('summary-xml', 'pdepend2.xml');

        $this->assertInstanceOf(\PHP\Depend\Report\Summary\Xml::CLAZZ, $generator1);
        $this->assertSame($generator1, $generator2);
    }
    
    /**
     * Tests that {@link \PHP\Depend\Report\GeneratorFactory::createGenerator()}
     * fails with an exception for an invalid logger identifier.
     *
     * @return void
     */
    public function testCreateGeneratorWithInvalidIdentifierFail()
    {
        $this->setExpectedException(
            '\RuntimeException',
            "Unknown generator class '\\PHP\\Depend\\Report\\FooBar\\Xml'."
        );
        
        $factory = new GeneratorFactory();
        $factory->createGenerator('foo-bar-xml', 'pdepend.xml');
    }
}
