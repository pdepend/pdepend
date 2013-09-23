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
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
  */

namespace PHP\Depend\Builder\Context;

/**
 * Test case for the {@link \PHP\Depend\Builder\Context\GlobalStatic} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 *
 * @covers \PHP\Depend\Builder\Context\GlobalStatic
 * @group pdepend
 * @group pdepend::builder
 * @group pdepend::builder::context
 * @group unittest
 */
class GlobalStaticTest extends \PHP_Depend_AbstractTest
{
    /**
     * testRegisterTraitCallsRestoreClassOnBuilder
     *
     * @return void
     * @since 1.0.0
     */
    public function testRegisterTraitCallsRestoreClassOnBuilder()
    {
        $builder = $this->getMock('\\PHP\\Depend\\Builder');
        $builder->expects($this->once())
            ->method('restoreTrait')
            ->with(self::isInstanceOf(\PHP_Depend_Code_Trait::CLAZZ));

        $context = new GlobalStatic($builder);
        $context->registerTrait(new \PHP_Depend_Code_Trait(__CLASS__));
    }

    /**
     * testRegisterClassCallsRestoreClassOnBuilder
     *
     * @return void
     */
    public function testRegisterClassCallsRestoreClassOnBuilder()
    {
        $builder = $this->getMock('\\PHP\\Depend\\Builder');
        $builder->expects($this->once())
            ->method('restoreClass')
            ->with(self::isInstanceOf(\PHP_Depend_Code_Class::CLAZZ));

        $context = new GlobalStatic($builder);
        $context->registerClass(new \PHP_Depend_Code_Class(__CLASS__));
    }

    /**
     * testRegisterInterfaceCallsRestoreInterfaceOnBuilder
     *
     * @return void
     */
    public function testRegisterInterfaceCallsRestoreInterfaceOnBuilder()
    {
        $builder = $this->getMock('\\PHP\\Depend\\Builder');
        $builder->expects($this->once())
            ->method('restoreInterface')
            ->with(self::isInstanceOf(\PHP_Depend_Code_Interface::CLAZZ));

        $context = new GlobalStatic($builder);
        $context->registerInterface(new \PHP_Depend_Code_Interface(__CLASS__));
    }

    /**
     * testRegisterFunctionCallsRestoreFunctionOnBuilder
     *
     * @return void
     */
    public function testRegisterFunctionCallsRestoreFunctionOnBuilder()
    {
        $builder = $this->getMock('\\PHP\\Depend\\Builder');
        $builder->expects($this->once())
            ->method('restoreFunction')
            ->with(self::isInstanceOf(\PHP_Depend_Code_Function::CLAZZ));

        $context = new GlobalStatic($builder);
        $context->registerFunction(new \PHP_Depend_Code_Function(__CLASS__));
    }

    /**
     * testGetTraitDelegatesCallToWrappedBuilder
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetTraitDelegatesCallToWrappedBuilder()
    {
        $builder = $this->getMock('\\PHP\\Depend\\Builder');
        $builder->expects($this->once())
            ->method('getTrait')
            ->with(self::equalTo(__CLASS__));

        $context = new GlobalStatic($builder);
        $context->getTrait(__CLASS__);
    }

    /**
     * testGetClassDelegatesCallToWrappedBuilder
     *
     * @return void
     */
    public function testGetClassDelegatesCallToWrappedBuilder()
    {
        $builder = $this->getMock('\\PHP\\Depend\\Builder');
        $builder->expects($this->once())
            ->method('getClass')
            ->with(self::equalTo(__CLASS__));

        $context = new GlobalStatic($builder);
        $context->getClass(__CLASS__);
    }

    /**
     * testGetClassOrInterfaceDelegatesCallToWrappedBuilder
     *
     * @return void
     */
    public function testGetClassOrInterfaceDelegatesCallToWrappedBuilder()
    {
        $builder = $this->getMock('\\PHP\\Depend\\Builder');
        $builder->expects($this->once())
            ->method('getClassOrInterface')
            ->with(self::equalTo(__CLASS__));

        $context = new GlobalStatic($builder);
        $context->getClassOrInterface(__CLASS__);
    }
}
