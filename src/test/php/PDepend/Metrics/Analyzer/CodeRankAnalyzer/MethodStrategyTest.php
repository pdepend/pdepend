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
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Metrics\Analyzer\CodeRankAnalyzer;

use PDepend\AbstractTest;
use PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy;

/**
 * Test case for the method strategy.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Metrics\Analyzer\CodeRankAnalyzer\MethodStrategy
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
        $namespaces = self::parseCodeResourceForTest();
        
        $idMap = array(
            'PDepend::CodeRankA'       =>  null,
            'PDepend::CodeRankB'       =>  null,
            'PDepend_CodeRank_ClassA'  =>  null,
            'PDepend_CodeRank_ClassB'  =>  null,
            'PDepend_CodeRank_ClassC'  =>  null,
        );
        
        foreach ($namespaces as $namespace) {
            foreach ($namespace->getClasses() as $class) {
                $this->assertArrayHasKey($class->getName(), $idMap);
                $idMap[$class->getName()] = $class->getId();
            }
            if (array_key_exists($namespace->getName(), $idMap)) {
                $idMap[$namespace->getName()] = $namespace->getId();
            }
        }

        $expected = array(
            $idMap['PDepend_CodeRank_ClassA']  =>  array(
                'in'  =>  array(
                    $idMap['PDepend_CodeRank_ClassB'],
                    $idMap['PDepend_CodeRank_ClassC']
                ),
                'out'  =>  array(
                    $idMap['PDepend_CodeRank_ClassC']
                ),
                'name'  =>  'PDepend_CodeRank_ClassA',
                'type'  =>  'PDepend\\Source\\AST\\ASTClass'
            ),
            $idMap['PDepend_CodeRank_ClassB']  =>  array(
                'in'  =>  array(
                    $idMap['PDepend_CodeRank_ClassC'],
                    $idMap['PDepend_CodeRank_ClassC']
                ),
                'out'  =>  array(
                    $idMap['PDepend_CodeRank_ClassA']
                ),
                'name'  =>  'PDepend_CodeRank_ClassB',
                'type'  =>  'PDepend\\Source\\AST\\ASTClass'
            ),
            $idMap['PDepend_CodeRank_ClassC']  =>  array(
                'in'  =>  array(
                    $idMap['PDepend_CodeRank_ClassA']
                ),
                'out'  =>  array(
                    $idMap['PDepend_CodeRank_ClassA'],
                    $idMap['PDepend_CodeRank_ClassB'],
                    $idMap['PDepend_CodeRank_ClassB']
                ),
                'name'  =>  'PDepend_CodeRank_ClassC',
                'type'  =>  'PDepend\\Source\\AST\\ASTClass'
            ),
            $idMap['PDepend::CodeRankA']  =>  array(
                'in'  =>  array(
                    $idMap['PDepend::CodeRankB'],
                    $idMap['PDepend::CodeRankB'],
                    $idMap['PDepend::CodeRankB']
                ),
                'out'  =>  array(
                    $idMap['PDepend::CodeRankB'],
                ),
                'name'  =>  'PDepend::CodeRankA',
                'type'  =>  'PDepend\\Source\\AST\\ASTNamespace'
            ),
            $idMap['PDepend::CodeRankB']  =>  array(
                'in'  =>  array(
                    $idMap['PDepend::CodeRankA'],
                ),
                'out'  =>  array(
                    $idMap['PDepend::CodeRankA'],
                    $idMap['PDepend::CodeRankA'],
                    $idMap['PDepend::CodeRankA']
                ),
                'name'  =>  'PDepend::CodeRankB',
                'type'  =>  'PDepend\\Source\\AST\\ASTNamespace'
            ),
        );
    
        $strategy = new MethodStrategy();
        foreach ($namespaces as $namespace) {
            $namespace->accept($strategy);
        }

        $actual = $strategy->getCollectedNodes();
        
        $this->assertEquals($expected, $actual);
    }
}
