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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case for parameter related ticker #32.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 *
 * @covers PHP_Depend_Parser
 * @group pdepend
 * @group pdepend::issues
 * @group unittest
 */
class PHP_Depend_Issues_ParserSetsCorrectParametersIssue032Test
    extends PHP_Depend_Issues_AbstractTest
{
    /**
     * testParserSetsExpectedNumberOfFunctionParameters
     *
     * @return void
     */
    public function testParserSetsExpectedNumberOfFunctionParameters()
    {
        $parameters = $this->getParametersOfFirstFunction();
        $this->assertEquals(3, count($parameters));
    }

    /**
     * testParserSetsExpectedPositionOfFunctionParameters
     *
     * @return void
     */
    public function testParserSetsExpectedPositionOfFunctionParameters()
    {
        $actual = array();
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[] = $parameter->getPosition();
        }
        $this->assertEquals(array(0, 1, 2), $actual);
    }

    /**
     * testParserSetsFunctionParametersInExpectedOrder
     *
     * @return void
     */
    public function testParserSetsFunctionParametersInExpectedOrder()
    {
        $actual = array();
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[] = $parameter->getName();
        }
        $this->assertEquals(array('$foo', '$bar', '$foobar'), $actual);
    }

    /**
     * testParserSetsExpectedTypeHintsForFunctionParameters
     *
     * @return void
     */
    public function testParserSetsExpectedTypeHintsForFunctionParameters()
    {
        $actual = array();
        foreach ($this->getParametersOfFirstFunction() as $parameter) {
            $actual[] = is_null($parameter->getClass());
        }
        $this->assertEquals(array(true, false, true), $actual);
    }

    /**
     * testParserSetsExpectedNumberOfMethodParameters
     *
     * @return void
     */
    public function testParserSetsExpectedNumberOfMethodParameters()
    {
        $parameters = $this->_getParametersOfFirstMethod();
        $this->assertEquals(3, count($parameters));
    }

    /**
     * testParserSetsExpectedPositionOfMethodParameters
     *
     * @return void
     */
    public function testParserSetsExpectedPositionOfMethodParameters()
    {
        $actual = array();
        foreach ($this->_getParametersOfFirstMethod() as $parameter) {
            $actual[] = $parameter->getPosition();
        }
        $this->assertEquals(array(0, 1, 2), $actual);
    }

    /**
     * testParserSetsMethodParametersInExpectedOrder
     *
     * @return void
     */
    public function testParserSetsMethodParametersInExpectedOrder()
    {
        $actual = array();
        foreach ($this->_getParametersOfFirstMethod() as $parameter) {
            $actual[] = $parameter->getName();
        }
        $this->assertEquals(array('$foo', '$bar', '$foobar'), $actual);
    }

    /**
     * testParserSetsExpectedTypeHintsForMethodParameters
     *
     * @return void
     */
    public function testParserSetsExpectedTypeHintsForMethodParameters()
    {
        $actual = array();
        foreach ($this->_getParametersOfFirstMethod() as $parameter) {
            $actual[] = is_null($parameter->getClass());
        }
        $this->assertEquals(array(true, false, true), $actual);
    }

    /**
     * Returns the parameters of the first method in the test case file.
     *
     * @return array(PHP_Depend_Code_Parameter)
     */
    private function _getParametersOfFirstMethod()
    {
        $packages = self::parseTestCase();
        return $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();
    }
}
