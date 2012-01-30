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
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 * @since      0.9.20
 */

require_once dirname(__FILE__) . '/AbstractTest.php';

/**
 * Test case for the {@link PHP_Depend_Parser_VersionAllParser} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * @since      0.9.20
 *
 * @covers PHP_Depend_Parser_VersionAllParser
 * @group pdepend
 * @group pdepend::parser
 * @group unittest
 */
class PHP_Depend_Parser_VersionAllParserTest extends PHP_Depend_Parser_AbstractTest
{
    /**
     * testParserAcceptsStringAsClassName
     *
     * @return void
     */
    public function testParserAcceptsStringAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('SimpleClassName', $class->getName());
    }

    /**
     * testParserAcceptsStringAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsStringAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('SimpleInterfaceName', $interface->getName());
    }

    /**
     * testParserAcceptsNullAsClassName
     *
     * @return void
     */
    public function testParserAcceptsNullAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('Null', $class->getName());
    }

    /**
     * testParserAcceptsNullAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsNullAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('Null', $interface->getName());
    }

    /**
     * testParserAcceptsTrueAsClassName
     *
     * @return void
     */
    public function testParserAcceptsTrueAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('True', $class->getName());
    }

    /**
     * testParserAcceptsTrueAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsTrueAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('True', $interface->getName());
    }

    /**
     * testParserAcceptsFalseAsClassName
     *
     * @return void
     */
    public function testParserAcceptsFalseAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('False', $class->getName());
    }

    /**
     * testParserAcceptsFalseAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsFalseAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('False', $interface->getName());
    }

    /**
     * testParserAcceptsInsteadofAsClassName
     *
     * @return void
     */
    public function testParserAcceptsInsteadofAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('insteadof', $class->getName());
    }

    /**
     * testParserAcceptsInsteadofAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsInsteadofAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('insteadof', $interface->getName());
    }

    /**
     * testParserAcceptsUseAsClassName
     *
     * @return void
     */
    public function testParserAcceptsUseAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('Use', $class->getName());
    }

    /**
     * testParserAcceptsUseAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsUseAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('Use', $interface->getName());
    }

    /**
     * testParserAcceptsNamespaceAsClassName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('Namespace', $class->getName());
    }

    /**
     * testParserAcceptsNamespaceAsInterfaceName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('Namespace', $interface->getName());
    }

    /**
     * testParserAcceptsNamespaceConstantAsClassName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceConstantAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('__NAMESPACE__', $class->getName());
    }

    /**
     * testParserAcceptsNamespaceConstantAsInterfaceName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceConstantAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('__NAMESPACE__', $interface->getName());
    }

    /**
     * testParserAcceptsTraitAsClassName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsTraitAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('Trait', $class->getName());
    }

    /**
     * testParserAcceptsTraitAsInterfaceName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsTraitAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('Trait', $interface->getName());
    }

    /**
     * testParserAcceptsTraitConstantAsClassName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsTraitConstantAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        self::assertSame('__TRAIT__', $class->getName());
    }

    /**
     * testParserAcceptsTraitConstantAsInterfaceName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsTraitConstantAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        self::assertSame('__TRAIT__', $interface->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsClassName
     *
     * @return void
     */
    public function testParserAcceptsGotoKeywordAsClassName()
    {
        $class = $this->getFirstClassForTestCase();
        self::assertEquals('Goto', $class->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsGotoKeywordAsInterfaceName()
    {
        $class = $this->getFirstInterfaceForTestCase();
        self::assertEquals('Goto', $class->getName());
    }

    /**
     * testParserAcceptsDirConstantAsClassName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsDirConstantAsClassName()
    {
        $class = $this->getFirstClassForTestCase();
        self::assertEquals('__DIR__', $class->getName());
    }

    /**
     * testParserAcceptsDirConstantAsInterfaceName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsDirConstantAsInterfaceName()
    {
        $class = $this->getFirstInterfaceForTestCase();
        self::assertEquals('__DIR__', $class->getName());
    }

    /**
     * testParserThrowsExpectedExceptionOnTokenStreamEnd
     *
     * @return void
     * @covers PHP_Depend_Parser_TokenStreamEndException
     * @expectedException PHP_Depend_Parser_TokenStreamEndException
     */
    public function testParserThrowsExpectedExceptionOnTokenStreamEnd()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForUnexpectedTokenType
     *
     * @return void
     * @covers PHP_Depend_Parser_UnexpectedTokenException
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForUnexpectedTokenType()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserAcceptsStringAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsStringAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('myMethodName', $method->getName());
    }

    /**
     * testParserAcceptsUseKeywordAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsUseKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('Use', $method->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsGotoKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('Goto', $method->getName());
    }

    /**
     * testParserAcceptsSelfKeywordAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsSelfKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('self', $method->getName());
    }

    /**
     * testParserAcceptsNullAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsNullAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('null', $method->getName());
    }

    /**
     * testParserAcceptsTrueAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsTrueAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('true', $method->getName());
    }

    /**
     * testParserAcceptsFalseAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsFalseAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('false', $method->getName());
    }

    /**
     * testParserAcceptsDirConstantAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsDirConstantAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('__DIR__', $method->getName());
    }

    /**
     * testParserAcceptsNamespaceKeywordAsMethodName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('nameSpace', $method->getName());
    }

    /**
     * testParserAcceptsNamespaceConstantAsMethodName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceConstantAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('__NAMESPACE__', $method->getName());
    }

    /**
     * testParserAcceptsParentKeywordAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsParentKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('Parent', $method->getName());
    }

    /**
     * testParserHandlesCallableTypeHint
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserHandlesCallableTypeHint()
    {
        $method = $this->getFirstMethodForTestCase();
        $type   = $method->getFirstChildOfType(PHP_Depend_Code_ASTType::CLAZZ);

        $this->assertInstanceOf(PHP_Depend_Code_ASTTypeCallable::CLAZZ, $type);
    }

    /**
     * testParserHandlesNamespaceTypeHint
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserHandlesNamespaceTypeHint()
    {
        $method = $this->getFirstMethodForTestCase();
        $type   = $method->getFirstChildOfType(PHP_Depend_Code_ASTType::CLAZZ);

        $this->assertInstanceOf(PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ, $type);
    }

    /**
     * testParserHandlesArrayTypeHint
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserHandlesArrayTypeHint()
    {
        $method = $this->getFirstMethodForTestCase();
        $type   = $method->getFirstChildOfType(PHP_Depend_Code_ASTType::CLAZZ);

        $this->assertInstanceOf(PHP_Depend_Code_ASTTypeArray::CLAZZ, $type);
    }

    /**
     * testParserHandlesSelfTypeHint
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserHandlesSelfTypeHint()
    {
        $method = $this->getFirstMethodForTestCase();
        $type   = $method->getFirstChildOfType(PHP_Depend_Code_ASTType::CLAZZ);

        $this->assertInstanceOf(PHP_Depend_Code_ASTSelfReference::CLAZZ, $type);
    }

    /**
     * testParserHandlesCompoundStaticMethodInvocation
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserHandlesCompoundStaticMethodInvocation()
    {
        $method  = $this->getFirstMethodForTestCase();
        $postfix = $method->getFirstChildOfType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ);

        $this->assertNotNull($postfix);
    }

    /**
     * testParserHandlesVariableStaticMethodInvocation
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserHandlesVariableStaticMethodInvocation()
    {
        $method  = $this->getFirstMethodForTestCase();
        $postfix = $method->getFirstChildOfType(PHP_Depend_Code_ASTMethodPostfix::CLAZZ);

        $this->assertNotNull($postfix);
    }

    /**
     * testParserHandlesBinaryIntegerLiteral
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserHandlesBinaryIntegerLiteral()
    {
        $method  = $this->getFirstMethodForTestCase();
        $literal = $method->getFirstChildOfType(PHP_Depend_Code_ASTLiteral::CLAZZ);

        $this->assertEquals('0b0100110100111', $literal->getImage());
    }

    /**
     * testParserThrowsExceptionForInvalidBinaryIntegerLiteral
     *
     * @return void
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     * @since 1.0.0
     */
    public function testParserThrowsExceptionForInvalidBinaryIntegerLiteral()
    {
        if (version_compare(phpversion(), '5.4alpha') >= 0)
        {
            $this->markTestSkipped( 'This test only affects PHP < 5.4' );
        }
        $this->getFirstMethodForTestCase();
    }

    /**
     * testParserThrowsExpectedExceptionForInvalidToken
     *
     * @return void
     * @expectedException PHP_Depend_Parser_UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForInvalidToken()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTokenStreamEnd
     *
     * @return void
     * @expectedException PHP_Depend_Parser_TokenStreamEndException
     */
    public function testParserThrowsExpectedExceptionForTokenStreamEnd()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserHandlesRegularArraySyntax
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserHandlesRegularArraySyntax()
    {
        $this->assertInstanceOf(
            PHP_Depend_Code_ASTArray::CLAZZ,
            $this->getFirstMethodForTestCase()
                ->getFirstChildOfType(PHP_Depend_Code_ASTArray::CLAZZ)
        );
    }

    /**
     * testParserHandlesShortArraySyntax
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserHandlesShortArraySyntax()
    {
        $this->assertInstanceOf(
            PHP_Depend_Code_ASTArray::CLAZZ,
            $this->getFirstMethodForTestCase()
                ->getFirstChildOfType(PHP_Depend_Code_ASTArray::CLAZZ)
        );
    }

    /**
     * testParserAcceptsGotoAsFunctionName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsGotoAsFunctionName()
    {
        $function = $this->getFirstFunctionForTestCase();
        self::assertEquals('goto', $function->getName());
    }

    /**
     * testParserAcceptsDirConstantAsFunctionName
     * 
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsDirConstantAsFunctionName()
    {
        $function = $this->getFirstFunctionForTestCase();
        self::assertEquals('__DIR__', $function->getName());
    }

    /**
     * testParserAcceptsNamespaceKeywordAsFunctionName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceKeywordAsFunctionName()
    {
        $method = $this->getFirstFunctionForTestCase();
        self::assertEquals('namespace', $method->getName());
    }

    /**
     * testParserAcceptsNamespaceConstantAsFunctionName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceConstantAsFunctionName()
    {
        $method = $this->getFirstFunctionForTestCase();
        self::assertEquals('__NAMESPACE__', $method->getName());
    }

    /**
     * testParserAcceptsTraitAsMethodName
     * 
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsTraitAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('trait', $method->getName());
    }

    /**
     * testParserAcceptsTraitConstantAsMethodName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsTraitConstantAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        self::assertEquals('__trait__', $method->getName());
    }

    /**
     * testParserAcceptsTraitAsFunctionName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsTraitAsFunctionName()
    {
        $function = $this->getFirstFunctionForTestCase();
        self::assertEquals('trait', $function->getName());
    }

    /**
     * testParserAcceptsTraitConstantAsFunctionName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsTraitConstantAsFunctionName()
    {
        $function = $this->getFirstFunctionForTestCase();
        self::assertEquals('__TRAIT__', $function->getName());
    }

    /**
     * Returns the first class or interface that could be found in the code under
     * test for the calling test case.
     *
     * @return PHP_Depend_Code_AbstractClassOrInterface
     */
    protected function getFirstTypeForTestCase()
    {
        return self::parseCodeResourceForTest()
            ->current()
            ->getTypes()
            ->current();
    }

    /**
     * Returns the first method that could be found in the code under test for
     * the calling test case.
     *
     * @return PHP_Depend_Code_Method
     */
    protected function getFirstMethodForTestCase()
    {
        return $this->getFirstTypeForTestCase()
            ->getMethods()
            ->current();
    }
}
