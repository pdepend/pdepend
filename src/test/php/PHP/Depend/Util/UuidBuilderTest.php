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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 * @since      0.9.12
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Util_UuidBuilder} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * @since      0.9.12
 *
 * @covers PHP_Depend_Util_UuidBuilder
 * @group pdepend
 * @group pdepend::util
 * @group unittest
 */
class PHP_Depend_Util_UuidBuilderTest extends PHP_Depend_AbstractTest
{
    /**
     * testBuilderCreatesExpectedIdentifierForFile
     *
     * @return void
     */
    public function testBuilderCreatesExpectedIdentifierForFile()
    {
        $file    = new PHP_Depend_Code_File(__FILE__);
        $builder = new PHP_Depend_Util_UuidBuilder();

        self::assertRegExp('/^[a-z0-9]{11}$/', $builder->forFile($file));
    }

    /**
     * testBuilderCreatesCaseSensitiveFileIdentifiers
     *
     * @return void
     */
    public function testBuilderCreatesCaseSensitiveFileIdentifiers()
    {
        $builder = new PHP_Depend_Util_UuidBuilder();

        $identifier0 = $builder->forFile(new PHP_Depend_Code_File(__FILE__));
        $identifier1 = $builder->forFile(new PHP_Depend_Code_File(strtolower(__FILE__)));

        self::assertNotEquals($identifier0, $identifier1);
    }

    /**
     * testBuilderCreatesExpectedIdentifierForClass
     *
     * @return void
     */
    public function testBuilderCreatesExpectedIdentifierForClass()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUuid('FooBar');

        $class = new PHP_Depend_Code_Class(__FUNCTION__);
        $class->setSourceFile($file);

        $builder = new PHP_Depend_Util_UuidBuilder();

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-00$/', $builder->forClassOrInterface($class));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForSecondIdenticalClass
     *
     * @return void
     */
    public function testBuilderCreatesExpectedIdentifierForSecondIdenticalClass()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUuid('FooBar');

        $class = new PHP_Depend_Code_Class(__FUNCTION__);
        $class->setSourceFile($file);

        $builder = new PHP_Depend_Util_UuidBuilder();
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
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUuid('FooBar');

        $class1 = new PHP_Depend_Code_Class(__FUNCTION__);
        $class1->setSourceFile($file);

        $class2 = new PHP_Depend_Code_Class(__CLASS__);
        $class2->setSourceFile($file);

        $builder = new PHP_Depend_Util_UuidBuilder();
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
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUuid(__FUNCTION__);
        
        $class0 = new PHP_Depend_Code_Class(__FUNCTION__);
        $class0->setSourceFile($file);
        
        $class1 = new PHP_Depend_Code_Class(strtolower(__FUNCTION__));
        $class1->setSourceFile($file);

        $builder0 = new PHP_Depend_Util_UuidBuilder();
        $builder1 = new PHP_Depend_Util_UuidBuilder();

        self::assertEquals(
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
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUuid(__FUNCTION__);

        $interface0 = new PHP_Depend_Code_Interface(__FUNCTION__);
        $interface0->setSourceFile($file);

        $interface1 = new PHP_Depend_Code_Interface(strtolower(__FUNCTION__));
        $interface1->setSourceFile($file);

        $builder0 = new PHP_Depend_Util_UuidBuilder();
        $builder1 = new PHP_Depend_Util_UuidBuilder();

        self::assertEquals(
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
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUuid('FooBar');

        $function = new PHP_Depend_Code_Function(__FUNCTION__);
        $function->setSourceFile($file);

        $builder = new PHP_Depend_Util_UuidBuilder();

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-00$/', $builder->forFunction($function));
    }

    /**
     * testBuilderCreatesCaseInSensitiveFunctionIdentifiers
     *
     * @return void
     */
    public function testBuilderCreatesCaseInSensitiveFunctionIdentifiers()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUuid(__FUNCTION__);

        $function0 = new PHP_Depend_Code_Function(__FUNCTION__);
        $function0->setSourceFile($file);

        $function1 = new PHP_Depend_Code_Function(strtolower(__FUNCTION__));
        $function1->setSourceFile($file);

        $builder0 = new PHP_Depend_Util_UuidBuilder();
        $builder1 = new PHP_Depend_Util_UuidBuilder();

        self::assertEquals(
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
        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setUuid('FooBar');

        $method = new PHP_Depend_Code_Method(__FUNCTION__);
        $method->setParent($class);

        $builder = new PHP_Depend_Util_UuidBuilder();

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}$/', $builder->forMethod($method));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForSecondIdenticalFunction
     *
     * @return void
     */
    public function testBuilderCreatesExpectedIdentifierForSecondIdenticalFunction()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUuid('FooBar');

        $function = new PHP_Depend_Code_Function(__FUNCTION__);
        $function->setSourceFile($file);

        $builder = new PHP_Depend_Util_UuidBuilder();
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
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUuid('FooBar');

        $function1 = new PHP_Depend_Code_Function(__FUNCTION__);
        $function1->setSourceFile($file);

        $function2 = new PHP_Depend_Code_Function(__CLASS__);
        $function2->setSourceFile($file);

        $builder = new PHP_Depend_Util_UuidBuilder();
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
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUuid(__FUNCTION__);

        $class = new PHP_Depend_Code_Class(__FUNCTION__);
        $class->setSourceFile($file);

        $method0 = new PHP_Depend_Code_Method(__FUNCTION__);
        $method0->setParent($class);

        $method1 = new PHP_Depend_Code_Method(strtolower(__FUNCTION__));
        $method1->setParent($class);

        $builder0 = new PHP_Depend_Util_UuidBuilder();
        $builder1 = new PHP_Depend_Util_UuidBuilder();

        self::assertEquals(
            $builder0->forMethod($method0),
            $builder1->forMethod($method1)
        );
    }
}
