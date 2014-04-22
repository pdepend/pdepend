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
 * @since     0.9.20
 */

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTest;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserGeneric} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since     0.9.20
 *
 * @covers \PDepend\Source\Language\PHP\PHPParserGeneric
 * @group unittest
 */
class PHPParserGenericTest extends AbstractTest
{
    /**
     * testParserAcceptsStringAsClassName
     *
     * @return void
     */
    public function testParserAcceptsStringAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('SimpleClassName', $class->getName());
    }

    /**
     * testParserAcceptsStringAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsStringAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('SimpleInterfaceName', $interface->getName());
    }

    /**
     * testParserAcceptsNullAsClassName
     *
     * @return void
     */
    public function testParserAcceptsNullAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('Null', $class->getName());
    }

    /**
     * testParserAcceptsNullAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsNullAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('Null', $interface->getName());
    }

    /**
     * testParserAcceptsTrueAsClassName
     *
     * @return void
     */
    public function testParserAcceptsTrueAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('True', $class->getName());
    }

    /**
     * testParserAcceptsTrueAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsTrueAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('True', $interface->getName());
    }

    /**
     * testParserAcceptsFalseAsClassName
     *
     * @return void
     */
    public function testParserAcceptsFalseAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('False', $class->getName());
    }

    /**
     * testParserAcceptsFalseAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsFalseAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('False', $interface->getName());
    }

    /**
     * testParserAcceptsInsteadofAsClassName
     *
     * @return void
     */
    public function testParserAcceptsInsteadofAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('insteadof', $class->getName());
    }

    /**
     * testParserAcceptsInsteadofAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsInsteadofAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('insteadof', $interface->getName());
    }

    /**
     * testParserAcceptsUseAsClassName
     *
     * @return void
     */
    public function testParserAcceptsUseAsClassName()
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('Use', $class->getName());
    }

    /**
     * testParserAcceptsUseAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsUseAsInterfaceName()
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('Use', $interface->getName());
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
        $this->assertSame('Namespace', $class->getName());
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
        $this->assertSame('Namespace', $interface->getName());
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
        $this->assertSame('__NAMESPACE__', $class->getName());
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
        $this->assertSame('__NAMESPACE__', $interface->getName());
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
        $this->assertSame('Trait', $class->getName());
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
        $this->assertSame('Trait', $interface->getName());
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
        $this->assertSame('__TRAIT__', $class->getName());
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
        $this->assertSame('__TRAIT__', $interface->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsClassName
     *
     * @return void
     */
    public function testParserAcceptsGotoKeywordAsClassName()
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals('Goto', $class->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsGotoKeywordAsInterfaceName()
    {
        $class = $this->getFirstInterfaceForTestCase();
        $this->assertEquals('Goto', $class->getName());
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
        $this->assertEquals('__DIR__', $class->getName());
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
        $this->assertEquals('__DIR__', $class->getName());
    }

    /**
     * testParserThrowsExpectedExceptionOnTokenStreamEnd
     *
     * @return void
     * @covers \PDepend\Source\Parser\TokenStreamEndException
     * @expectedException \PDepend\Source\Parser\TokenStreamEndException
     */
    public function testParserThrowsExpectedExceptionOnTokenStreamEnd()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForUnexpectedTokenType
     *
     * @return void
     * @covers \PDepend\Source\Parser\UnexpectedTokenException
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
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
        $this->assertEquals('myMethodName', $method->getName());
    }

    /**
     * testParserAcceptsUseKeywordAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsUseKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('Use', $method->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsGotoKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('Goto', $method->getName());
    }

    /**
     * testParserAcceptsSelfKeywordAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsSelfKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('self', $method->getName());
    }

    /**
     * testParserAcceptsNullAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsNullAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('null', $method->getName());
    }

    /**
     * testParserAcceptsTrueAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsTrueAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('true', $method->getName());
    }

    /**
     * testParserAcceptsFalseAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsFalseAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('false', $method->getName());
    }

    /**
     * testParserAcceptsDirConstantAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsDirConstantAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('__DIR__', $method->getName());
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
        $this->assertEquals('nameSpace', $method->getName());
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
        $this->assertEquals('__NAMESPACE__', $method->getName());
    }

    /**
     * testParserAcceptsParentKeywordAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsParentKeywordAsMethodName()
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('Parent', $method->getName());
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
        $type   = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTType');

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTTypeCallable', $type);
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
        $type   = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTType');

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassOrInterfaceReference', $type);
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
        $type   = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTType');

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTTypeArray', $type);
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
        $type   = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTType');

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSelfReference', $type);
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
        $postfix = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTMethodPostfix');

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
        $postfix = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTMethodPostfix');

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
        $literal = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTLiteral');

        $this->assertEquals('0b0100110100111', $literal->getImage());
    }

    /**
     * testParserThrowsExceptionForInvalidBinaryIntegerLiteral
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
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
     * @expectedException \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForInvalidToken()
    {
        self::parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTokenStreamEnd
     *
     * @return void
     * @expectedException \PDepend\Source\Parser\TokenStreamEndException
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
            'PDepend\\Source\\AST\\ASTArray',
            $this->getFirstMethodForTestCase()
                ->getFirstChildOfType('PDepend\\Source\\AST\\ASTArray')
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
            'PDepend\\Source\\AST\\ASTArray',
            $this->getFirstMethodForTestCase()
                ->getFirstChildOfType('PDepend\\Source\\AST\\ASTArray')
        );
    }

    /**
     * @return void
     * @since 1.1.1
     */
    public function testParserHandlesShortArraySyntaxForFormalParameter()
    {
        $this->assertNotNull($this->getFirstMethodForTestCase());
    }

    /**
     * @return void
     * @since 1.1.1
     */
    public function testParserHandlesShortArraySyntaxForFieldDeclaration()
    {
        $this->assertNotNull($this->getFirstPropertyForTestCase());
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
        $this->assertEquals('goto', $function->getName());
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
        $this->assertEquals('__DIR__', $function->getName());
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
        $this->assertEquals('namespace', $method->getName());
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
        $this->assertEquals('__NAMESPACE__', $method->getName());
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
        $this->assertEquals('trait', $method->getName());
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
        $this->assertEquals('__trait__', $method->getName());
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
        $this->assertEquals('trait', $function->getName());
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
        $this->assertEquals('__TRAIT__', $function->getName());
    }

    /**
     * Returns the first class or interface that could be found in the code under
     * test for the calling test case.
     *
     * @return \PDepend\Source\AST\AbstractASTClassOrInterface
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
     * @return \PDepend\Source\AST\ASTMethod
     */
    protected function getFirstMethodForTestCase()
    {
        return $this->getFirstTypeForTestCase()
            ->getMethods()
            ->current();
    }

    /**
     * Returns the first property that could be found in the code under test for
     * the calling test case.
     *
     * @return \PDepend\Source\AST\ASTProperty
     */
    protected function getFirstPropertyForTestCase()
    {
        return $this->getFirstTypeForTestCase()
            ->getProperties()
            ->current();
    }
}
