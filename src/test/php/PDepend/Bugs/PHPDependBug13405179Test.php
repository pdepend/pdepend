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
 * @link       https://www.pivotaltracker.com/story/show/13405179
 */

namespace PDepend\Bugs;

use PDepend\Report\Jdepend\Chart;
use PDepend\Report\Jdepend\Xml as JdependXml;
use PDepend\Report\Overview\Pyramid;
use PDepend\Report\Summary\Xml as SummaryXml;

/**
 * Test case for bug #13405179.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link https://www.pivotaltracker.com/story/show/13405179
 *
 * @ticket 13405179
 *
 * @group regressiontest
 */
class PHPDependBug13405179Test extends AbstractRegressionTestCase
{
    /**
     * testLogFileIsCreatedForUnstructuredCode
     *
     * @param string $className Class name of a logger implementation.
     * @param string $extension Log file extension.
     * @dataProvider getLoggerClassNames
     */
    public function testLogFileIsCreatedForUnstructuredCode($className, $extension): void
    {
        $file = $this->createRunResourceURI() . '.' . $extension;

        $generator = new $className();
        $generator->setLogFile($file);

        $engine = $this->createEngineFixture();
        $engine->addFile($this->createCodeResourceUriForTest());
        $engine->addReportGenerator($generator);
        $engine->analyze();

        static::assertFileExists($file);
    }

    /**
     * Returns the class names of all file aware logger classes.
     *
     * @return array
     */
    public static function getLoggerClassNames()
    {
        return [
            [Chart::class, 'svg'],
            [JdependXml::class, 'xml'],
            [Pyramid::class, 'svg'],
            [SummaryXml::class, 'xml'],
        ];
    }
}
