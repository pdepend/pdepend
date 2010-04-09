<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @subpackage Util_Cache
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Storage/AbstractEngine.php';

/**
 * Test case for the {@link PHP_Depend_Util_Cache_AbstractEngine} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util_Cache
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Util_Cache_EngineTest extends PHP_Depend_AbstractTest
{
    /**
     * testSetProbabilityThrowsExceptionForNoneIntegerValue
     *
     * @return void
     * @covers PHP_Depend_Storage_AbstractEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     * @expectedException InvalidArgumentException
     */
    public function testSetProbabilityThrowsExceptionForNoneIntegerValue()
    {
        $engine = $this->getMockForAbstractClass('PHP_Depend_Storage_AbstractEngine');
        $engine->setProbability(null);
    }
    
    /**
     * testSetProbabilityThrowsExceptionForIntegerSmallerZero
     *
     * @return void
     * @covers PHP_Depend_Storage_AbstractEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     * @expectedException InvalidArgumentException
     */
    public function testSetProbabilityThrowsExceptionForIntegerSmallerZero()
    {
        $engine = $this->getMockForAbstractClass('PHP_Depend_Storage_AbstractEngine');
        $engine->setProbability(-1);
    }
    
    /**
     * testSetProbabilityThrowsExceptionForIntegerGreaterOneHundred
     *
     * @return void
     * @covers PHP_Depend_Storage_AbstractEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     * @expectedException InvalidArgumentException
     */
    public function testSetProbabilityThrowsExceptionForIntegerGreaterOneHundred()
    {
        $engine = $this->getMockForAbstractClass('PHP_Depend_Storage_AbstractEngine');
        $engine->setProbability(101);
    }
    
    /**
     * testSetProbabilityWorksForIntegerZero
     *
     * @return void
     * @covers PHP_Depend_Storage_AbstractEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     */
    public function testSetProbabilityWorksForIntegerZero()
    {
        $engine = $this->getMockForAbstractClass('PHP_Depend_Storage_AbstractEngine');
        $engine->setProbability(0);
        $this->assertEquals(0, $engine->getProbability());
    }
    
    /**
     * testSetProbabilityWorkdForIntegerOneHundred
     *
     * @return void
     * @covers PHP_Depend_Storage_AbstractEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     */
    public function testSetProbabilityWorkdForIntegerOneHundred()
    {
        $engine = $this->getMockForAbstractClass('PHP_Depend_Storage_AbstractEngine');
        $engine->setProbability(100);
        $this->assertEquals(100, $engine->getProbability());
    }
    
    /**
     * testSetMaxLifetimeThrowsExceptionForNoneIntegerValue
     *
     * @return void
     * @covers PHP_Depend_Storage_AbstractEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     * @expectedException InvalidArgumentException
     */
    public function testSetMaxLifetimeThrowsExceptionForNoneIntegerValue()
    {
        $engine = $this->getMockForAbstractClass('PHP_Depend_Storage_AbstractEngine');
        $engine->setMaxLifetime(null);
    }
    
    /**
     * testSetMaxLifetimeThrowsExceptionForIntegerSmallerZero
     *
     * @return void
     * @covers PHP_Depend_Storage_AbstractEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     * @expectedException InvalidArgumentException
     */
    public function testSetMaxLifetimeThrowsExceptionForIntegerSmallerZero()
    {
        $engine = $this->getMockForAbstractClass('PHP_Depend_Storage_AbstractEngine');
        $engine->setMaxLifetime(-1);
    }
    
    /**
     * testSetMaxLifetimeWorksForIntegerZero
     *
     * @return void
     * @covers PHP_Depend_Storage_AbstractEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     */
    public function testSetMaxLifetimeWorksForIntegerZero()
    {
        $engine = $this->getMockForAbstractClass('PHP_Depend_Storage_AbstractEngine');
        $engine->setMaxLifetime(0);
        $this->assertEquals(0, $engine->getMaxLifetime());
    }
    
    /**
     * testSetPruneInternallySetsMaxLifetimeToZero
     *
     * @return void
     * @covers PHP_Depend_Storage_AbstractEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     */
    public function testSetPruneInternallySetsMaxLifetimeToZero()
    {
        $engine = $this->getMockForAbstractClass('PHP_Depend_Storage_AbstractEngine');
        $engine->setPrune();
        $this->assertEquals(0, $engine->getMaxLifetime());
    }
        
    /**
     * testDestructorCallsGarbageCollectMethod
     *
     * @return void
     * @covers PHP_Depend_Storage_AbstractEngine
     * @group pdepend
     * @group pdepend::util
     * @group pdepend::util::cache
     * @group unittest
     */
    public function testDestructorCallsGarbageCollectMethod()
    {
        $engine = $this->getMock('PHP_Depend_Storage_AbstractEngine', array('garbageCollect', 'store', 'restore'));
        $engine->setPrune();
        $engine->expects($this->once())
            ->method('garbageCollect');
            
        $engine->__destruct();
    }
}