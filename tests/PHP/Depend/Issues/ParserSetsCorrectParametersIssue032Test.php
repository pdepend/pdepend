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
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend.php';
require_once 'PHP/Depend/Input/ExtensionFilter.php';
require_once 'PHP/Depend/Log/LoggerI.php';
require_once 'PHP/Depend/TextUI/Command.php';
require_once 'PHP/Depend/TextUI/Runner.php';

/**
 * Test case for parameter related ticker #32.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Issues_ParserSetsCorrectParametersIssue032Test
    extends PHP_Depend_AbstractTest
{
    /**
     * testParserSetsExpectedNumberOfFunctionParameters
     *
     * @return void
     * @covers \stdClass
     * @group pdepend
     * @group pdepend::issues
     * @group issues
     */
    public function testParserSetsExpectedNumberOfFunctionParameters()
    {
        $packages   = self::parseSource('issues/032/001-correct-function-parameters.php');
        $parameters = $packages->current()
            ->getFunctions()
            ->current()
            ->getParameters();

        $this->assertEquals(3, $parameters->count());
    }

    /**
     * testParserSetsExpectedPositionOfFunctionParameters
     *
     * @return void
     * @covers \stdClass
     * @group pdepend
     * @group pdepend::issues
     * @group issues
     */
    public function testParserSetsExpectedPositionOfFunctionParameters()
    {
        $packages   = self::parseSource('issues/032/001-correct-function-parameters.php');
        $parameters = $packages->current()
            ->getFunctions()
            ->current()
            ->getParameters();

        $parameter = $parameters->current();
        $this->assertEquals(0, $parameter->getPosition());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertEquals(1, $parameter->getPosition());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertEquals(2, $parameter->getPosition());
    }

    /**
     * testParserSetsFunctionParametersInExpectedOrder
     *
     * @return void
     * @covers \stdClass
     * @group pdepend
     * @group pdepend::issues
     * @group issues
     */
    public function testParserSetsFunctionParametersInExpectedOrder()
    {
        $packages   = self::parseSource('issues/032/001-correct-function-parameters.php');
        $parameters = $packages->current()
            ->getFunctions()
            ->current()
            ->getParameters();

        $parameter = $parameters->current();
        $this->assertEquals('$foo', $parameter->getName());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertEquals('$bar', $parameter->getName());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertEquals('$foobar', $parameter->getName());
    }

    /**
     * testParserSetsExpectedTypeHintsForFunctionParameters
     *
     * @return void
     * @covers \stdClass
     * @group pdepend
     * @group pdepend::issues
     * @group issues
     */
    public function testParserSetsExpectedTypeHintsForFunctionParameters()
    {
        $packages   = self::parseSource('issues/032/001-correct-function-parameters.php');
        $parameters = $packages->current()
            ->getFunctions()
            ->current()
            ->getParameters();

        $parameter = $parameters->current();
        $this->assertNull($parameter->getClass());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertNotNull($parameter->getClass());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertNull($parameter->getClass());
    }

    /**
     * testParserSetsExpectedNumberOfMethodParameters
     *
     * @return void
     * @covers \stdClass
     * @group pdepend
     * @group pdepend::issues
     * @group issues
     */
    public function testParserSetsExpectedNumberOfMethodParameters()
    {
        $packages   = self::parseSource('issues/032/002-correct-method-parameters.php');
        $parameters = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();

        $this->assertEquals(3, $parameters->count());
    }

    /**
     * testParserSetsExpectedPositionOfMethodParameters
     *
     * @return void
     * @covers \stdClass
     * @group pdepend
     * @group pdepend::issues
     * @group issues
     */
    public function testParserSetsExpectedPositionOfMethodParameters()
    {
        $packages   = self::parseSource('issues/032/002-correct-method-parameters.php');
        $parameters = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();

        $parameter = $parameters->current();
        $this->assertEquals(0, $parameter->getPosition());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertEquals(1, $parameter->getPosition());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertEquals(2, $parameter->getPosition());
    }

    /**
     * testParserSetsMethodParametersInExpectedOrder
     *
     * @return void
     * @covers \stdClass
     * @group pdepend
     * @group pdepend::issues
     * @group issues
     */
    public function testParserSetsMethodParametersInExpectedOrder()
    {
        $packages   = self::parseSource('issues/032/002-correct-method-parameters.php');
        $parameters = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();

        $parameter = $parameters->current();
        $this->assertEquals('$foo', $parameter->getName());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertEquals('$bar', $parameter->getName());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertEquals('$foobar', $parameter->getName());
    }

    /**
     * testParserSetsExpectedTypeHintsForMethodParameters
     *
     * @return void
     * @covers \stdClass
     * @group pdepend
     * @group pdepend::issues
     * @group issues
     */
    public function testParserSetsExpectedTypeHintsForMethodParameters()
    {
        $packages   = self::parseSource('issues/032/002-correct-method-parameters.php');
        $parameters = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters();

        $parameter = $parameters->current();
        $this->assertNull($parameter->getClass());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertNotNull($parameter->getClass());

        $parameters->next();

        $parameter = $parameters->current();
        $this->assertNull($parameter->getClass());
    }
}
