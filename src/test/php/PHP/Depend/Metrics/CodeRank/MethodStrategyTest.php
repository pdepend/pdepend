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

require_once dirname(__FILE__) . '/../../AbstractTest.php';

/**
 * Test case for the method strategy.
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
 * @covers PHP_Depend_Metrics_CodeRank_MethodStrategy
 * @group pdepend
 * @group pdepend::metrics
 * @group pdepend::metrics::coderank
 * @group unittest
 */
class PHP_Depend_Metrics_CodeRank_MethodStrategyTest extends PHP_Depend_AbstractTest
{
    /**
     * testStrategyCountsCorrectTypes
     *
     * @return void
     */
    public function testStrategyCountsCorrectTypes()
    {
        $packages = self::parseCodeResourceForTest();
        
        $uuidMap = array(
            'PDepend::CodeRankA'       =>  null,
            'PDepend::CodeRankB'       =>  null,
            'PDepend_CodeRank_ClassA'  =>  null,
            'PDepend_CodeRank_ClassB'  =>  null,
            'PDepend_CodeRank_ClassC'  =>  null,
        );
        
        foreach ($packages as $package) {
            foreach ($package->getClasses() as $class) {
                self::assertArrayHasKey($class->getName(), $uuidMap);
                $uuidMap[$class->getName()] = $class->getUuid();
            }
            if (array_key_exists($package->getName(), $uuidMap)) {
                $uuidMap[$package->getName()] = $package->getUuid();
            }
        }

        $expected = array(
            $uuidMap['PDepend_CodeRank_ClassA']  =>  array(
                'in'  =>  array(
                    $uuidMap['PDepend_CodeRank_ClassB'],
                    $uuidMap['PDepend_CodeRank_ClassC']
                ),
                'out'  =>  array(
                    $uuidMap['PDepend_CodeRank_ClassC']
                ),
                'name'  =>  'PDepend_CodeRank_ClassA',
                'type'  =>  'PHP_Depend_Code_Class'
            ),
            $uuidMap['PDepend_CodeRank_ClassB']  =>  array(
                'in'  =>  array(
                    $uuidMap['PDepend_CodeRank_ClassC'],
                    $uuidMap['PDepend_CodeRank_ClassC']
                ),
                'out'  =>  array(
                    $uuidMap['PDepend_CodeRank_ClassA']
                ),
                'name'  =>  'PDepend_CodeRank_ClassB',
                'type'  =>  'PHP_Depend_Code_Class'
            ),
            $uuidMap['PDepend_CodeRank_ClassC']  =>  array(
                'in'  =>  array(
                    $uuidMap['PDepend_CodeRank_ClassA']
                ),
                'out'  =>  array(
                    $uuidMap['PDepend_CodeRank_ClassA'],
                    $uuidMap['PDepend_CodeRank_ClassB'],
                    $uuidMap['PDepend_CodeRank_ClassB']
                ),
                'name'  =>  'PDepend_CodeRank_ClassC',
                'type'  =>  'PHP_Depend_Code_Class'
            ),
            $uuidMap['PDepend::CodeRankA']  =>  array(
                'in'  =>  array(
                    $uuidMap['PDepend::CodeRankB'],
                    $uuidMap['PDepend::CodeRankB'],
                    $uuidMap['PDepend::CodeRankB']
                ),
                'out'  =>  array(
                    $uuidMap['PDepend::CodeRankB'],
                ),
                'name'  =>  'PDepend::CodeRankA',
                'type'  =>  'PHP_Depend_Code_Package'
            ),
            $uuidMap['PDepend::CodeRankB']  =>  array(
                'in'  =>  array(
                    $uuidMap['PDepend::CodeRankA'],
                ),
                'out'  =>  array(
                    $uuidMap['PDepend::CodeRankA'],
                    $uuidMap['PDepend::CodeRankA'],
                    $uuidMap['PDepend::CodeRankA']
                ),
                'name'  =>  'PDepend::CodeRankB',
                'type'  =>  'PHP_Depend_Code_Package'
            ),
        );
    
        $strategy = new PHP_Depend_Metrics_CodeRank_MethodStrategy();
        foreach ($packages as $package) {
            $package->accept($strategy);
        }

        $actual = $strategy->getCollectedNodes();
        
        self::assertEquals($expected, $actual);
    }
}
