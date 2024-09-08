<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Bugs;

use Exception;
use PDepend\AbstractTestCase;
use PDepend\Report\Summary\Xml;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTNamespace;

/**
 * Abstract test case for the "Bugs" package.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
abstract class AbstractRegressionTestCase extends AbstractTestCase
{
    /**
     * Creates the PDepend summary report for the source associated with the
     * calling test case.
     *
     * @since 0.10.0
     */
    protected function createSummaryXmlForCallingTest(): string
    {
        $this->changeWorkingDirectory(
            self::createCodeResourceURI('config/')
        );

        $file = $this->createRunResourceURI('summary.xml');

        $log = new Xml();
        $log->setLogFile($file);

        $pdepend = $this->createEngineFixture();
        $pdepend->addFile($this->createCodeResourceUriForTest());
        $pdepend->addReportGenerator($log);
        $pdepend->analyze();

        return $file;
    }

    /**
     * Parses the source of a test case file.
     *
     * @return ASTArtifactList<ASTNamespace>
     */
    public function parseTestCaseSource(string $testCase, bool $ignoreAnnotations = false): ASTArtifactList
    {
        return $this->parseSource(
            $this->getSourceFileForTestCase($testCase),
            $ignoreAnnotations
        );
    }

    /**
     * Returns the source file for the given test case.
     *
     * @param string $testCase The qualified test case name.
     */
    protected function getSourceFileForTestCase(string $testCase): string
    {
        [$class, $method] = explode('::', $testCase);

        preg_match('(Bug(\d+)Test$)', $class, $match);

        if (!isset($match[1])) {
            throw new Exception('Unable to find tests case ID');
        }

        return self::createCodeResourceURI(
            sprintf('bugs/%s/%s.php', $match[1], $method)
        );
    }
}
