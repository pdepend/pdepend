<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @subpackage Input
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Input/ExcludePathFilter.php';

/**
 * Test case for the exclude path filter.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Input
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Input_ExcludePathFilterTest extends PHP_Depend_AbstractTest
{
    /**
     * Tests the exclude filter with a single file in the exclude list.
     *
     * @return void
     */
    public function testExcludePathFilterWithFile()
    {
        $files  = array('code-5.2.x/package2.php');
        $filter = new PHP_Depend_Input_ExcludePathFilter($files);
        $it     = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(dirname(__FILE__) . '/../_code/code-5.2.x')
        );

        $result = array();
        foreach ($it as $file) {
            if ($filter->accept($file)) {
                $result[$file->getFilename()] = true;
            }
        }

        $this->assertArrayHasKey('package1.php', $result);
        $this->assertArrayHasKey('package3.php', $result);
        $this->assertArrayNotHasKey('package2.php', $result);
    }

    /**
     * Tests the path exclude filter with a directory in the exclude list.
     *
     * @return void
     */
    public function testExcludePathFilterWithDirectory()
    {
        $files  = array('/code-5.2.x/', '/code-without-comments');
        $filter = new PHP_Depend_Input_ExcludePathFilter($files);
        $it     = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(dirname(__FILE__) . '/../_code')
        );

        $result = array();
        foreach ($it as $file) {
            if ($filter->accept($file)) {
                $result[$file->getFilename()] = true;
            }
        }

        $this->assertArrayHasKey('pkg3FooI.txt', $result);
        $this->assertArrayHasKey('invalid_class_with_code.txt', $result);
        $this->assertArrayNotHasKey('package1.php', $result);
    }

    /**
     * Tests the path exclude filter with a mix of files and directories.
     *
     * @return void
     */
    public function testExcludePathFilterWithFileAndDirectory()
    {
        $filter = new PHP_Depend_Input_ExcludePathFilter(
            array(
                '/code-5.3.x/',
                '/code-5.2.x/package1.php',
                '/code-without-comments/',
                '/function.inc'
            )
        );

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(dirname(__FILE__) . '/../_code')
        );

        $result = array();
        foreach ($it as $file) {
            if ($filter->accept($file)) {
                $result[$file->getFilename()] = true;
            }
        }

        $this->assertArrayHasKey('package2.php', $result);
        $this->assertArrayHasKey('invalid_class_with_code.txt', $result);
        $this->assertArrayNotHasKey('pkg3FooI.txt', $result);
        $this->assertArrayNotHasKey('package1.php', $result);
        $this->assertArrayNotHasKey('function.inc', $result);
    }
}