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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the logger factory.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Log_LoggerFactoryTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that {@link PHP_Depend_Log_LoggerFactory::createLogger()} returns
     * the expected instance for a valid identifier.
     *
     * @return void
     */
    public function testCreateLoggerWithValidIdentifier()
    {
        $factory = new PHP_Depend_Log_LoggerFactory();
        $logger  = $factory->createLogger('summary-xml', 'pdepend.xml');
        
        $this->assertInstanceOf(PHP_Depend_Log_Summary_Xml::CLAZZ, $logger);
    }
    
    /**
     * Tests the singleton behaviour of the logger factory method 
     * {@link PHP_Depend_Log_LoggerFactory::createLogger()}.
     *
     * @return void
     */
    public function testCreateLoggerSingletonBehaviour()
    {
        $factory = new PHP_Depend_Log_LoggerFactory();
        $logger1 = $factory->createLogger('summary-xml', 'pdepend1.xml');
        $logger2 = $factory->createLogger('summary-xml', 'pdepend2.xml');

        $this->assertInstanceOf(PHP_Depend_Log_Summary_Xml::CLAZZ, $logger1);
        $this->assertSame($logger1, $logger2);
    }
    
    /**
     * Tests that {@link PHP_Depend_Log_LoggerFactory::createLogger()} fails with
     * an exception for an invalid logger identifier.
     *
     * @return void
     */
    public function testCreateLoggerWithInvalidIdentifierFail()
    {
        $this->setExpectedException(
            'RuntimeException',
            "Unknown logger class 'PHP_Depend_Log_FooBar_Xml'."
        );
        
        $factory = new PHP_Depend_Log_LoggerFactory();
        $factory->createLogger('foo-bar-xml', 'pdepend.xml');
    }
}
