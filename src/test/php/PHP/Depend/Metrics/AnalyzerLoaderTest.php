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
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the analyzer loader.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @covers PHP_Depend_Metrics_AnalyzerLoader
 * @group pdepend
 * @group pdepend::metrics
 * @group unittest
 */
class PHP_Depend_Metrics_AnalyzerLoaderTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the analyzer loader loads the correct analyzer instances.
     *
     * @return void
     */
    public function testLoadKnownAnalyzersByInstance()
    {
        $expected = array(
            'PHP_Depend_Metrics_CodeRank_Analyzer',
            'PHP_Depend_Metrics_Hierarchy_Analyzer',
        );
        
        $loader = new PHP_Depend_Metrics_AnalyzerLoader(
            new PHP_Depend_Metrics_AnalyzerClassFileSystemLocator(),
            $this->getMock( 'PHP_Depend_Util_Cache_Driver' ),
            $expected
        );

        $actual = array();
        foreach ($loader->getIterator() as $analyzer) {
            $actual[] = get_class($analyzer);
        }
        sort($actual);

        self::assertEquals($expected, $actual);
    }

    /**
     * testLoaderOnlyReturnsEnabledAnalyzerInstances
     *
     * @return void
     */
    public function testLoaderOnlyReturnsEnabledAnalyzerInstances()
    {
        $analyzer = $this->getMock('PHP_Depend_Metrics_AnalyzerI');
        $analyzer->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $reflection = $this->getMock('ReflectionObject', array('newInstance'), array($analyzer));
        $reflection->expects($this->once())
            ->method('newInstance')
            ->will($this->returnValue($analyzer));

        $locator = $this->getMock('PHP_Depend_Metrics_AnalyzerClassLocator');
        $locator->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(array($reflection)));

        $loader = new PHP_Depend_Metrics_AnalyzerLoader(
            $locator,
            $this->getMock( 'PHP_Depend_Util_Cache_Driver' ),
            array('PHP_Depend_Metrics_AnalyzerI')
        );

        self::assertEquals(1, iterator_count($loader->getIterator()));
    }


    /**
     * testLoaderNotReturnsDisabledAnalyzerInstances
     *
     * @return void
     */
    public function testLoaderNotReturnsDisabledAnalyzerInstances()
    {
        $analyzer = $this->getMock('PHP_Depend_Metrics_AnalyzerI');
        $analyzer->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));

        $reflection = $this->getMock('ReflectionObject', array('newInstance'), array($analyzer));
        $reflection->expects($this->once())
            ->method('newInstance')
            ->will($this->returnValue($analyzer));

        $locator = $this->getMock('PHP_Depend_Metrics_AnalyzerClassLocator');
        $locator->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(array($reflection)));

        $loader = new PHP_Depend_Metrics_AnalyzerLoader(
            $locator,
            $this->getMock( 'PHP_Depend_Util_Cache_Driver' ),
            array('PHP_Depend_Metrics_AnalyzerI')
        );

        self::assertEquals(0, iterator_count($loader->getIterator()));
    }
}
