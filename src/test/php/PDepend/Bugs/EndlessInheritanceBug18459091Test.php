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
 * @link       https://www.pivotaltracker.com/story/show/18459091
 * @since     1.0.0
 */

namespace PDepend\Bugs;

use PDepend\Metrics\Analyzer\ClassLevelAnalyzer;
use PDepend\Metrics\Analyzer\CyclomaticComplexityAnalyzer;
use PDepend\Metrics\Analyzer\InheritanceAnalyzer;
use PDepend\Util\Cache\Driver\MemoryCacheDriver;

/**
 * Test case for bug #18459091.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link https://www.pivotaltracker.com/story/show/18459091
 * @since 1.0.0
 *
 * @ticket 18459091
 * @covers \stdClass
 * @group regressiontest
 */
class EndlessInheritanceBug18459091Test extends AbstractRegressionTest
{
    /**
     * Resets the execution time to -1.
     *
     * @return void
     */
    protected function tearDown()
    {
        set_time_limit(-1);

        parent::tearDown();
    }

    /**
     * testClassLevelAnalyzerNotRunsEndlessForTwoLevelClassHierarchy
     * 
     * @return void
     * @expectedException \PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException
     */
    public function testClassLevelAnalyzerNotRunsEndlessForTwoLevelClassHierarchy()
    {
        set_time_limit(5);

        $ccnAnalyzer = new CyclomaticComplexityAnalyzer();
        $ccnAnalyzer->setCache(new MemoryCacheDriver());

        $analyzer = new ClassLevelAnalyzer();
        $analyzer->addAnalyzer($ccnAnalyzer);

        $analyzer->analyze($this->parseCodeResourceForTest());
    }

    /**
     * testClassLevelAnalyzerNotRunsEndlessForDeepClassHierarchy
     *
     * @return void
     * @expectedException \PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException
     */
    public function testClassLevelAnalyzerNotRunsEndlessForDeepClassHierarchy()
    {
        set_time_limit(5);

        $ccnAnalyzer = new CyclomaticComplexityAnalyzer();
        $ccnAnalyzer->setCache(new MemoryCacheDriver());

        $analyzer = new ClassLevelAnalyzer();
        $analyzer->addAnalyzer($ccnAnalyzer);

        $analyzer->analyze($this->parseCodeResourceForTest());
    }

    /**
     * testClassLevelAnalyzerNotRunsEndlessForTwoLevelInterfaceHierarchy
     *
     * @return void
     */
    public function testClassLevelAnalyzerNotRunsEndlessForTwoLevelInterfaceHierarchy()
    {
        set_time_limit(5);

        $ccnAnalyzer = new CyclomaticComplexityAnalyzer();
        $ccnAnalyzer->setCache(new MemoryCacheDriver());

        $analyzer = new ClassLevelAnalyzer();
        $analyzer->addAnalyzer($ccnAnalyzer);

        $analyzer->analyze($this->parseCodeResourceForTest());
    }

    /**
     * testClassLevelAnalyzerNotRunsEndlessForDeepInterfaceHierarchy
     * 
     * @return void
     */
    public function testClassLevelAnalyzerNotRunsEndlessForDeepInterfaceHierarchy()
    {
        set_time_limit(5);

        $ccnAnalyzer = new CyclomaticComplexityAnalyzer();
        $ccnAnalyzer->setCache(new MemoryCacheDriver());

        $analyzer = new ClassLevelAnalyzer();
        $analyzer->addAnalyzer($ccnAnalyzer);

        $analyzer->analyze($this->parseCodeResourceForTest());
    }

    /**
     * testInheritanceAnalyzerNotRunsEndlessForTwoLevelClassHierarchy
     *
     * @return void
     * @expectedException \PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException
     */
    public function testInheritanceAnalyzerNotRunsEndlessForTwoLevelClassHierarchy()
    {
        set_time_limit(5);

        $analyzer = new InheritanceAnalyzer();
        $analyzer->analyze($this->parseCodeResourceForTest());
    }

    /**
     * testInheritanceAnalyzerNotRunsEndlessForDeepClassHierarchy
     *
     * @return void
     * @expectedException \PDepend\Source\AST\ASTClassOrInterfaceRecursiveInheritanceException
     */
    public function testInheritanceAnalyzerNotRunsEndlessForDeepClassHierarchy()
    {
        set_time_limit(5);

        $analyzer = new InheritanceAnalyzer();
        $analyzer->analyze($this->parseCodeResourceForTest());
    }

    /**
     * testInheritanceAnalyzerNotRunsEndlessForTwoLevelInterfaceHierarchy
     *
     * @return void
     */
    public function testInheritanceAnalyzerNotRunsEndlessForTwoLevelInterfaceHierarchy()
    {
        set_time_limit(5);

        $analyzer = new InheritanceAnalyzer();
        $analyzer->analyze($this->parseCodeResourceForTest());
    }

    /**
     * testInheritanceAnalyzerNotRunsEndlessForDeepInterfaceHierarchy
     *
     * @return void
     */
    public function testInheritanceAnalyzerNotRunsEndlessForDeepInterfaceHierarchy()
    {
        set_time_limit(5);

        $analyzer = new InheritanceAnalyzer();
        $analyzer->analyze($this->parseCodeResourceForTest());
    }

    /**
     * testFullStackNotRunsEndless
     * 
     * @return void
     */
    public function testFullStackNotRunsEndless()
    {
        set_time_limit(5);

        $_SERVER['argv'] = array(
            __FILE__,
            '--summary-xml=' . $this->createRunResourceURI('jdepend.xml'),
            '--jdepend-xml=' . $this->createRunResourceURI('summary.xml'),
            $this->createCodeResourceUriForTest()
        );

        ob_start();

        $command = new \PDepend\TextUI\Command();
        $command->run();

        $output = ob_get_clean();
        $this->assertContains(' is part of an endless inheritance hierarchy', $output);
    }
}
