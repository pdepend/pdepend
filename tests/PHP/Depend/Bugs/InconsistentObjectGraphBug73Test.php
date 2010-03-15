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
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Log/Summary/Xml.php';

/**
 * Test case for bug 73 that results in an inconsistent object graph and fatal
 * errors.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Bugs_InconsistentObjectGraphBug73Test extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * class Foo {}
     * class Bar extends Foo {}
     * interface Foo {}
     * </code>
     *
     * @return void
     */
    public function testParserCreatesExpectedObjectGraphClassDeclaredBeforeInterfaceWithPackage()
    {
        $packages = self::parseSource('bugs/073-001-inconsistent-object-graph.php');

        $this->assertSame(2, $packages->count());

        $package = $packages->current();
        $this->assertSame(2, $package->getTypes()->count());
        $this->assertSame(2, $package->getClasses()->count());

        $packages->next();
        $package = $packages->current();
        $this->assertSame(1, $package->getTypes()->count());
        $this->assertSame(1, $package->getInterfaces()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * class Foo {}
     * class Bar extends Foo {}
     * interface Foo {}
     * </code>
     *
     * @return void
     */
    public function testParserCreatesExpectedObjectGraphClassDeclaredBeforeInterfaceWithoutPackage()
    {
        $packages = self::parseSource('bugs/073-002-inconsistent-object-graph.php');

        $this->assertSame(1, $packages->count());

        $package = $packages->current();
        $this->assertSame(3, $package->getTypes()->count());
        $this->assertSame(2, $package->getClasses()->count());
        $this->assertSame(1, $package->getInterfaces()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * interface Foo {}
     * class Bar implements Foo {}
     * class Foo {}
     * </code>
     *
     * @return void
     */
    public function testParserCreatesExpectedObjectGraphInterfaceDeclaredBeforeClassWithPackage()
    {
        $packages = self::parseSource('bugs/073-003-inconsistent-object-graph.php');

        $this->assertSame(2, $packages->count());

        $package = $packages->current();
        $this->assertSame(2, $package->getTypes()->count());
        $this->assertSame(1, $package->getClasses()->count());
        $this->assertSame(1, $package->getInterfaces()->count());

        $packages->next();
        $package = $packages->current();
        $this->assertSame(1, $package->getTypes()->count());
        $this->assertSame(1, $package->getClasses()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * interface Foo {}
     * class Bar implements Foo {}
     * class Foo {}
     * </code>
     *
     * @return void
     */
    public function testParserCreatesExpectedObjectGraphInterfaceDeclaredBeforeClassWithoutPackage()
    {
        $packages = self::parseSource('bugs/073-004-inconsistent-object-graph.php');

        $this->assertSame(1, $packages->count());

        $package = $packages->current();
        $this->assertSame(3, $package->getTypes()->count());
        $this->assertSame(2, $package->getClasses()->count());
        $this->assertSame(1, $package->getInterfaces()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * class Foo {}
     * class Bar extends Foo {}
     * class Foo {}
     * </code>
     *
     * @return void
     */
    public function testParserCreatesExpectedObjectGraphClassDeclaredBeforeClassWithPackage()
    {
        $packages = self::parseSource('bugs/073-005-inconsistent-object-graph.php');

        $this->assertSame(2, $packages->count());

        $package = $packages->current();
        $this->assertSame(2, $package->getTypes()->count());
        $this->assertSame(2, $package->getClasses()->count());

        $packages->next();
        $package = $packages->current();
        $this->assertSame(1, $package->getTypes()->count());
        $this->assertSame(1, $package->getClasses()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * interface Foo {}
     * interface Bar extends Foo {}
     * interface Foo {}
     * </code>
     *
     * @return void
     */
    public function testParserCreatesExpectedObjectGraphInterfaceDeclaredBeforeInterfaceWithPackage()
    {
        $packages = self::parseSource('bugs/073-006-inconsistent-object-graph.php');

        $this->assertSame(2, $packages->count());

        $package = $packages->current();
        $this->assertSame(2, $package->getTypes()->count());
        $this->assertSame(2, $package->getInterfaces()->count());

        $packages->next();
        $package = $packages->current();
        $this->assertSame(1, $package->getTypes()->count());
        $this->assertSame(1, $package->getInterfaces()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * namespace {
     *     class Foo {}
     *     class Bar extends Foo {}
     * }
     * namespace baz {
     *     interface Foo {}
     * }
     * </code>
     *
     * @return void
     */
    public function testParserCreatesExpectedObjectGraphClassDeclaredBeforeInterfaceWithNamespace()
    {
        $packages = self::parseSource('bugs/073-007-inconsistent-object-graph.php');

        $this->assertSame(2, $packages->count());

        $package = $packages->current();
        $this->assertSame(2, $package->getTypes()->count());
        $this->assertSame(2, $package->getClasses()->count());

        $packages->next();
        $package = $packages->current();
        $this->assertSame(1, $package->getTypes()->count());
        $this->assertSame(1, $package->getInterfaces()->count());
    }

    /**
     * Tests that the parser handles the following code correct:
     *
     * <code>
     * namespace {
     *     class Bar implements Foo {}
     *     interface Foo {}
     * }
     * namespace baz {
     *     class Foo {}
     * }
     * </code>
     *
     * @return void
     */
    public function testParserCreatesExpectedObjectGraphInterfaceDeclaredBeforeClassWithNamespace()
    {
        $packages = self::parseSource('bugs/073-008-inconsistent-object-graph.php');

        $this->assertSame(2, $packages->count());

        $package = $packages->current();
        $this->assertSame(2, $package->getTypes()->count());
        $this->assertSame(1, $package->getClasses()->count());
        $this->assertSame(1, $package->getInterfaces()->count());

        $packages->next();
        $package = $packages->current();
        $this->assertSame(1, $package->getTypes()->count());
        $this->assertSame(1, $package->getClasses()->count());
    }

    /**
     * Tests that pdepend does not die with a fatal error.
     *
     * @return void
     */
    public function testPHPDependDoesNotDieWithErrorClassDeclaredBeforeInterfaceWithPackage()
    {
        $logFile  = self::createRunResourceURI('summary.xml');
        $fileName = self::createCodeResourceURI('bugs/073-001-inconsistent-object-graph.php');

        $log = new PHP_Depend_Log_Summary_Xml();
        $log->setLogFile($logFile);

        $pdepend = new PHP_Depend();
        $pdepend->addFile($fileName);
        $pdepend->addLogger($log);
        $pdepend->analyze();
    }

    /**
     * Tests that pdepend does not die with a fatal error.
     *
     * @return void
     */
    public function testPHPDependDoesNotDieWithErrorClassDeclaredBeforeInterfaceWithoutPackage()
    {
        $logFile  = self::createRunResourceURI('summary.xml');
        $fileName = self::createCodeResourceURI('bugs/073-002-inconsistent-object-graph.php');

        $log = new PHP_Depend_Log_Summary_Xml();
        $log->setLogFile($logFile);

        $pdepend = new PHP_Depend();
        $pdepend->addFile($fileName);
        $pdepend->addLogger($log);
        $pdepend->analyze();
    }

    /**
     * Tests that pdepend does not die with a fatal error.
     *
     * @return void
     */
    public function testPHPDependDoesNotDieWithErrorInterfaceDeclaredBeforeClassWithPackage()
    {
        $logFile  = self::createRunResourceURI('summary.xml');
        $fileName = self::createCodeResourceURI('bugs/073-003-inconsistent-object-graph.php');

        $log = new PHP_Depend_Log_Summary_Xml();
        $log->setLogFile($logFile);

        $pdepend = new PHP_Depend();
        $pdepend->addFile($fileName);
        $pdepend->addLogger($log);
        $pdepend->analyze();
    }

    /**
     * Tests that pdepend does not die with a fatal error.
     *
     * @return void
     */
    public function testPHPDependDoesNotDieWithErrorInterfaceDeclaredBeforeClassWithoutPackage()
    {
        $logFile  = self::createRunResourceURI('summary.xml');
        $fileName = self::createCodeResourceURI('bugs/073-004-inconsistent-object-graph.php');

        $log = new PHP_Depend_Log_Summary_Xml();
        $log->setLogFile($logFile);

        $pdepend = new PHP_Depend();
        $pdepend->addFile($fileName);
        $pdepend->addLogger($log);
        $pdepend->analyze();
    }
}
