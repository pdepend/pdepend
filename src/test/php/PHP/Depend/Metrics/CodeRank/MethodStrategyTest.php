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

namespace PHP\Depend\Metrics\CodeRank;

use PHP\Depend\AbstractTest;

/**
 * Test case for the method strategy.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @covers \PHP\Depend\Metrics\CodeRank\MethodStrategy
 * @group unittest
 */
class MethodStrategyTest extends AbstractTest
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
                $this->assertArrayHasKey($class->getName(), $uuidMap);
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
                'type'  =>  'PHP\\Depend\\Source\\AST\\ASTClass'
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
                'type'  =>  'PHP\\Depend\\Source\\AST\\ASTClass'
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
                'type'  =>  'PHP\\Depend\\Source\\AST\\ASTClass'
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
                'type'  =>  'PHP\\Depend\\Source\\AST\\ASTNamespace'
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
                'type'  =>  'PHP\\Depend\\Source\\AST\\ASTNamespace'
            ),
        );
    
        $strategy = new \PHP\Depend\Metrics\CodeRank\MethodStrategy();
        foreach ($packages as $package) {
            $package->accept($strategy);
        }

        $actual = $strategy->getCollectedNodes();
        
        $this->assertEquals($expected, $actual);
    }
}
