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
 * @since     0.9.12
 */

namespace PDepend\Util;

use PDepend\AbstractTest;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTCompilationUnit;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTMethod;

/**
 * Test case for the {@link \PDepend\Util\IdBuilder} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since 0.9.12
 *
 * @covers \PDepend\Util\IdBuilder
 * @group unittest
 */
class IdBuilderTest extends AbstractTest
{
    /**
     * testBuilderCreatesExpectedIdentifierForFile
     *
     * @return void
     */
    public function testBuilderCreatesExpectedIdentifierForFile()
    {
        $file    = new ASTCompilationUnit(__FILE__);
        $builder = new IdBuilder();

        $this->assertRegExp('/^[a-z0-9]{11}$/', $builder->forFile($file));
    }

    /**
     * testBuilderCreatesCaseSensitiveFileIdentifiers
     *
     * @return void
     */
    public function testBuilderCreatesCaseSensitiveFileIdentifiers()
    {
        $builder = new IdBuilder();

        $identifier0 = $builder->forFile(new ASTCompilationUnit(__FILE__));
        $identifier1 = $builder->forFile(new ASTCompilationUnit(strtolower(__FILE__)));

        $this->assertNotEquals($identifier0, $identifier1);
    }

    /**
     * testBuilderCreatesExpectedIdentifierForClass
     *
     * @return void
     */
    public function testBuilderCreatesExpectedIdentifierForClass()
    {
        $compilationUnit = new ASTCompilationUnit(__FILE__);
        $compilationUnit->setId('FooBar');

        $class = new ASTClass(__FUNCTION__);
        $class->setCompilationUnit($compilationUnit);

        $builder = new IdBuilder();

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-00$/', $builder->forClassOrInterface($class));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForSecondIdenticalClass
     *
     * @return void
     */
    public function testBuilderCreatesExpectedIdentifierForSecondIdenticalClass()
    {
        $compilationUnit = new ASTCompilationUnit(__FILE__);
        $compilationUnit->setId('FooBar');

        $class = new ASTClass(__FUNCTION__);
        $class->setCompilationUnit($compilationUnit);

        $builder = new IdBuilder();
        $builder->forClassOrInterface($class);

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-01$/', $builder->forClassOrInterface($class));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForSecondClass
     *
     * @return void
     */
    public function testBuilderCreatesExpectedIdentifierForSecondClass()
    {
        $compilationUnit = new ASTCompilationUnit(__FILE__);
        $compilationUnit->setId('FooBar');

        $class1 = new ASTClass(__FUNCTION__);
        $class1->setCompilationUnit($compilationUnit);

        $class2 = new ASTClass(__CLASS__);
        $class2->setCompilationUnit($compilationUnit);

        $builder = new IdBuilder();
        $builder->forClassOrInterface($class1);

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-00$/', $builder->forClassOrInterface($class2));
    }

    /**
     * testBuilderCreatesCaseInSensitiveClassIdentifiers
     *
     * @return void
     */
    public function testBuilderCreatesCaseInSensitiveClassIdentifiers()
    {
        $compilationUnit = new ASTCompilationUnit(__FILE__);
        $compilationUnit->setId(__FUNCTION__);
        
        $class0 = new ASTClass(__FUNCTION__);
        $class0->setCompilationUnit($compilationUnit);
        
        $class1 = new ASTClass(strtolower(__FUNCTION__));
        $class1->setCompilationUnit($compilationUnit);

        $builder0 = new IdBuilder();
        $builder1 = new IdBuilder();

        $this->assertEquals(
            $builder0->forClassOrInterface($class0),
            $builder1->forClassOrInterface($class1)
        );
    }

    /**
     * testBuilderCreatesCaseInSensitiveInterfaceIdentifiers
     *
     * @return void
     */
    public function testBuilderCreatesCaseInSensitiveInterfaceIdentifiers()
    {
        $compilationUnit = new ASTCompilationUnit(__FILE__);
        $compilationUnit->setId(__FUNCTION__);

        $interface0 = new ASTInterface(__FUNCTION__);
        $interface0->setCompilationUnit($compilationUnit);

        $interface1 = new ASTInterface(strtolower(__FUNCTION__));
        $interface1->setCompilationUnit($compilationUnit);

        $builder0 = new IdBuilder();
        $builder1 = new IdBuilder();

        $this->assertEquals(
            $builder0->forClassOrInterface($interface0),
            $builder1->forClassOrInterface($interface1)
        );
    }

    /**
     * testBuilderCreatesExpectedIdentifierForFunction
     *
     * @return void
     */
    public function testBuilderCreatesExpectedIdentifierForFunction()
    {
        $compilationUnit = new ASTCompilationUnit(__FILE__);
        $compilationUnit->setId('FooBar');

        $function = new ASTFunction(__FUNCTION__);
        $function->setCompilationUnit($compilationUnit);

        $builder = new IdBuilder();

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-00$/', $builder->forFunction($function));
    }

    /**
     * testBuilderCreatesCaseInSensitiveFunctionIdentifiers
     *
     * @return void
     */
    public function testBuilderCreatesCaseInSensitiveFunctionIdentifiers()
    {
        $compilationUnit = new ASTCompilationUnit(__FILE__);
        $compilationUnit->setId(__FUNCTION__);

        $function0 = new ASTFunction(__FUNCTION__);
        $function0->setCompilationUnit($compilationUnit);

        $function1 = new ASTFunction(strtolower(__FUNCTION__));
        $function1->setCompilationUnit($compilationUnit);

        $builder0 = new IdBuilder();
        $builder1 = new IdBuilder();

        $this->assertEquals(
            $builder0->forFunction($function0),
            $builder1->forFunction($function1)
        );
    }

    /**
     * testBuilderCreatesExpectedIdentifierForMethod
     *
     * @return void
     */
    public function testBuilderCreatesExpectedIdentifierForMethod()
    {
        $class = new ASTClass(__CLASS__);
        $class->setId('FooBar');

        $method = new ASTMethod(__FUNCTION__);
        $method->setParent($class);

        $builder = new IdBuilder();

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}$/', $builder->forMethod($method));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForSecondIdenticalFunction
     *
     * @return void
     */
    public function testBuilderCreatesExpectedIdentifierForSecondIdenticalFunction()
    {
        $compilationUnit = new ASTCompilationUnit(__FILE__);
        $compilationUnit->setId('FooBar');

        $function = new ASTFunction(__FUNCTION__);
        $function->setCompilationUnit($compilationUnit);

        $builder = new IdBuilder();
        $builder->forFunction($function);

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-01$/', $builder->forFunction($function));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForSecondFunction
     *
     * @return void
     */
    public function testBuilderCreatesExpectedIdentifierForSecondFunction()
    {
        $compilationUnit = new ASTCompilationUnit(__FILE__);
        $compilationUnit->setId('FooBar');

        $function1 = new ASTFunction(__FUNCTION__);
        $function1->setCompilationUnit($compilationUnit);

        $function2 = new ASTFunction(__CLASS__);
        $function2->setCompilationUnit($compilationUnit);

        $builder = new IdBuilder();
        $builder->forFunction($function1);

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-00$/', $builder->forFunction($function2));
    }

    /**
     * testBuilderCreatesCaseInSensitiveMethodIdentifiers
     *
     * @return void
     */
    public function testBuilderCreatesCaseInSensitiveMethodIdentifiers()
    {
        $compilationUnit = new ASTCompilationUnit(__FILE__);
        $compilationUnit->setId(__FUNCTION__);

        $class = new ASTClass(__FUNCTION__);
        $class->setCompilationUnit($compilationUnit);

        $method0 = new ASTMethod(__FUNCTION__);
        $method0->setParent($class);

        $method1 = new ASTMethod(strtolower(__FUNCTION__));
        $method1->setParent($class);

        $builder0 = new IdBuilder();
        $builder1 = new IdBuilder();

        $this->assertEquals(
            $builder0->forMethod($method0),
            $builder1->forMethod($method1)
        );
    }
}
