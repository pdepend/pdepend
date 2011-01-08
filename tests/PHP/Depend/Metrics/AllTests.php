<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/AnalyzerLoaderTest.php';
require_once dirname(__FILE__) . '/AnalyzerIteratorTest.php';
require_once dirname(__FILE__) . '/ClassLevel/AnalyzerTest.php';
require_once dirname(__FILE__) . '/CodeRank/AllTests.php';
require_once dirname(__FILE__) . '/Coupling/AnalyzerTest.php';
require_once dirname(__FILE__) . '/CrapIndex/AllTests.php';
require_once dirname(__FILE__) . '/CyclomaticComplexity/AnalyzerTest.php';
require_once dirname(__FILE__) . '/Dependency/AnalyzerTest.php';
require_once dirname(__FILE__) . '/Hierarchy/AnalyzerTest.php';
require_once dirname(__FILE__) . '/Inheritance/AnalyzerTest.php';
require_once dirname(__FILE__) . '/NodeCount/AnalyzerTest.php';
require_once dirname(__FILE__) . '/NodeLoc/AnalyzerTest.php';
require_once dirname(__FILE__) . '/NPathComplexity/AnalyzerTest.php';

/**
 * Main test suite for the PHP_Depend_Metrics package.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Metrics_AllTests
{
    /**
     * Creates the phpunit test suite for this package.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHP_Depend_Metrics - AllTests');

        $suite->addTest(PHP_Depend_Metrics_CodeRank_AllTests::suite());
        $suite->addTest(PHP_Depend_Metrics_CrapIndex_AllTests::suite());

        $suite->addTestSuite('PHP_Depend_Metrics_AnalyzerLoaderTest');
        $suite->addTestSuite('PHP_Depend_Metrics_AnalyzerIteratorTest');

        $suite->addTestSuite('PHP_Depend_Metrics_ClassLevel_AnalyzerTest');
        $suite->addTestSuite('PHP_Depend_Metrics_Coupling_AnalyzerTest');
        $suite->addTestSuite('PHP_Depend_Metrics_CyclomaticComplexity_AnalyzerTest');
        $suite->addTestSuite('PHP_Depend_Metrics_Dependency_AnalyzerTest');
        $suite->addTestSuite('PHP_Depend_Metrics_Hierarchy_AnalyzerTest');
        $suite->addTestSuite('PHP_Depend_Metrics_Inheritance_AnalyzerTest');
        $suite->addTestSuite('PHP_Depend_Metrics_NodeCount_AnalyzerTest');
        $suite->addTestSuite('PHP_Depend_Metrics_NodeLoc_AnalyzerTest');
        $suite->addTestSuite('PHP_Depend_Metrics_NPathComplexity_AnalyzerTest');

        return $suite;
    }
}