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

namespace PDepend\Source\AST;

use PDepend\AbstractTest;

/**
 * Base test case for abstract item implementations.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\AST\AbstractASTArtifact
 * @group unittest
 */
abstract class AbstractASTArtifactTest extends AbstractTest
{
    /**
     * testSetNameChangesPreviousName
     *
     * @return void
     * @since 1.0.0
     */
    public function testSetNameChangesPreviousName()
    {
        $item = $this->createItem();
        $item->setName(__METHOD__);

        $this->assertEquals(__METHOD__, $item->getName());
    }

    /**
     * Parses the given source file or directory with the default tokenizer
     * and node builder implementations.
     *
     * @param string  $testCase
     * @param boolean $ignoreAnnotations
     * @return \PDepend\Source\AST\ASTNamespace[]
     */
    public static function parseTestCaseSource($testCase, $ignoreAnnotations = false)
    {
        list($class, $method) = explode('::', $testCase);

        $fileName = substr($class, strrpos($class, '\\') + 1, -4);
        $fileName = 'code/' . $fileName . '/' . $method;

        try {
            $fileOrDirectory = self::createCodeResourceURI($fileName);
        } catch (ErrorException $e) {
            $fileOrDirectory = self::createCodeResourceURI($fileName . '.php');
        }

        return self::parseSource($fileOrDirectory, $ignoreAnnotations);
    }

    /**
     * Creates an abstract item instance.
     *
     * @return \PDepend\Source\AST\AbstractASTArtifact
     */
    protected abstract function createItem();
}
