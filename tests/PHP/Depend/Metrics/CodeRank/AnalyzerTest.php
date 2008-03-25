<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../../AbstractTest.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Metrics/CodeRank/Analyzer.php';

/**
 * 
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_CodeRank_AnalyzerTest extends PHP_Depend_AbstractTest
{
    public function testGetClassRank()
    {
        $package      = new PHP_Depend_Code_Package('util');
        $list         = new PHP_Depend_Code_Class('List', 1, null);
        $order        = new PHP_Depend_Code_Class('Order', 1, null);
        $arrayList    = new PHP_Depend_Code_Class('ArrayList', 1, null);
        $collection   = new PHP_Depend_Code_Class('Collection', 1, null);
        $abstractList = new PHP_Depend_Code_Class('AbstractList', 1, null);
        
        $abstractList->addDependency($arrayList);
        $collection->addDependency($list);
        $list->addDependency($abstractList);
        $list->addDependency($order);
        
        $package->addClass($list);
        $package->addClass($order);
        $package->addClass($arrayList);
        $package->addClass($collection);
        $package->addClass($abstractList);
        
        $analyzer = new PHP_Depend_Metrics_CodeRank_Analyzer();
        $analyzer->visitPackage($package);
        
        $rank = $analyzer->getClassRank();
        $this->assertEquals(5, count($rank));
        
        $values = array(
            'Collection'    =>  array(0.5863688, 0.15),
            'List'          =>  array(0.513375, 0.2775),
            'AbstractList'  =>  array(0.2775, 0.2679375),
            'ArrayList'     =>  array(0.15, 0.3777469),
            'Order'         =>  array(0.15, 0.2679375),
        );
        
        foreach ($rank as $class) {
            $this->assertArrayHasKey($class->getName(), $values);
            // Check forward code rank
            $this->assertEquals(
                $values[$class->getName()][0], 
                $class->getCodeRank(),
                '',
                0.00005
            );
            // Check reverse code rank
            $this->assertEquals(
                $values[$class->getName()][1],
                $class->getReverseCodeRank(),
                '',
                0.00005
            );
        }
        
    }
}