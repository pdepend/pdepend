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
 * @since 0.9.20
 */

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTestCase;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserGeneric} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.9.20
 *
 * @covers \PDepend\Source\Language\PHP\PHPParserGeneric
 * @group unittest
 */
class PHPParserGenericTest extends AbstractTestCase
{
    /**
     * testParserAcceptsStringAsClassName
     *
     * @return void
     */
    public function testParserAcceptsStringAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('SimpleClassName', $class->getName());
    }

    /**
     * testParserAcceptsStringAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsStringAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('SimpleInterfaceName', $interface->getName());
    }

    /**
     * testParserAcceptsNullAsClassName
     *
     * @return void
     */
    public function testParserAcceptsNullAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('Null', $class->getName());
    }

    /**
     * testParserAcceptsNullAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsNullAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('Null', $interface->getName());
    }

    /**
     * testParserAcceptsTrueAsClassName
     *
     * @return void
     */
    public function testParserAcceptsTrueAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('True', $class->getName());
    }

    /**
     * testParserAcceptsTrueAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsTrueAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('True', $interface->getName());
    }

    /**
     * testParserAcceptsFalseAsClassName
     *
     * @return void
     */
    public function testParserAcceptsFalseAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('False', $class->getName());
    }

    /**
     * testParserAcceptsFalseAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsFalseAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('False', $interface->getName());
    }

    /**
     * testParserAcceptsInsteadofAsClassName
     *
     * @return void
     */
    public function testParserAcceptsInsteadofAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('insteadof', $class->getName());
    }

    /**
     * testParserAcceptsInsteadofAsFunctionName
     *
     * @return void
     */
    public function testParserAcceptsInsteadofAsFunctionName(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $this->assertSame('insteadOf', $function->getName());
    }

    /**
     * testParserAcceptsInsteadofAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsInsteadofAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('insteadof', $interface->getName());
    }

    /**
     * testParserAcceptsInsteadofAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsInsteadofAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertSame('insteadOf', $method->getName());
    }

    /**
     * testParserAcceptsInsteadofAsNamespaceName
     *
     * @return void
     */
    public function testParserAcceptsInsteadofAsNamespaceName(): void
    {
        $namespace = $this->getFirstTypeForTestCase()->getNamespaceName();
        $this->assertSame('InsteadOf', $namespace);
    }

    /**
     * testParserAcceptsUseAsClassName
     *
     * @return void
     */
    public function testParserAcceptsUseAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('Use', $class->getName());
    }

    /**
     * testParserAcceptsUseAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsUseAsInterfaceName(): void
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
    public function testParserAcceptsNamespaceAsClassName(): void
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
    public function testParserAcceptsNamespaceAsInterfaceName(): void
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
    public function testParserAcceptsNamespaceConstantAsClassName(): void
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
    public function testParserAcceptsNamespaceConstantAsInterfaceName(): void
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
    public function testParserAcceptsTraitAsClassName(): void
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
    public function testParserAcceptsTraitAsInterfaceName(): void
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
    public function testParserAcceptsTraitConstantAsClassName(): void
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
    public function testParserAcceptsTraitConstantAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('__TRAIT__', $interface->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsClassName
     *
     * @return void
     */
    public function testParserAcceptsGotoKeywordAsClassName(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals('Goto', $class->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsInterfaceName
     *
     * @return void
     */
    public function testParserAcceptsGotoKeywordAsInterfaceName(): void
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
    public function testParserAcceptsDirConstantAsClassName(): void
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
    public function testParserAcceptsDirConstantAsInterfaceName(): void
    {
        $class = $this->getFirstInterfaceForTestCase();
        $this->assertEquals('__DIR__', $class->getName());
    }

    /**
     * testParserThrowsExpectedExceptionOnTokenStreamEnd
     *
     * @return void
     * @covers \PDepend\Source\Parser\TokenStreamEndException
     */
    public function testParserThrowsExpectedExceptionOnTokenStreamEnd(): void
    {
        $this->expectException(\PDepend\Source\Parser\TokenStreamEndException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForUnexpectedTokenType
     *
     * @return void
     * @covers \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForUnexpectedTokenType(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserAcceptsStringAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsStringAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('myMethodName', $method->getName());
    }

    /**
     * testParserAcceptsUseKeywordAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsUseKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('Use', $method->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsGotoKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('Goto', $method->getName());
    }

    /**
     * testParserAcceptsSelfKeywordAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsSelfKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('self', $method->getName());
    }

    /**
     * testParserAcceptsNullAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsNullAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('null', $method->getName());
    }

    /**
     * testParserAcceptsTrueAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsTrueAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('true', $method->getName());
    }

    /**
     * testParserAcceptsFalseAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsFalseAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('false', $method->getName());
    }

    /**
     * testParserAcceptsDirConstantAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsDirConstantAsMethodName(): void
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
    public function testParserAcceptsNamespaceKeywordAsMethodName(): void
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
    public function testParserAcceptsNamespaceConstantAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('__NAMESPACE__', $method->getName());
    }

    /**
     * testParserAcceptsParentKeywordAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsParentKeywordAsMethodName(): void
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
    public function testParserHandlesCallableTypeHint(): void
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
    public function testParserHandlesNamespaceTypeHint(): void
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
    public function testParserHandlesArrayTypeHint(): void
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
    public function testParserHandlesSelfTypeHint(): void
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
    public function testParserHandlesCompoundStaticMethodInvocation(): void
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
    public function testParserHandlesVariableStaticMethodInvocation(): void
    {
        $method  = $this->getFirstMethodForTestCase();
        $postfix = $method->getFirstChildOfType('PDepend\\Source\\AST\\ASTMethodPostfix');

        $this->assertNotNull($postfix);
    }

    /**
     * testParserThrowsExpectedExceptionForInvalidToken
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForInvalidToken(): void
    {
        $this->expectException(\PDepend\Source\Parser\UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTokenStreamEnd
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForTokenStreamEnd(): void
    {
        $this->expectException(\PDepend\Source\Parser\TokenStreamEndException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserHandlesRegularArraySyntax
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserHandlesRegularArraySyntax(): void
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
    public function testParserHandlesShortArraySyntax(): void
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
    public function testParserHandlesShortArraySyntaxForFormalParameter(): void
    {
        $this->assertNotNull($this->getFirstMethodForTestCase());
    }

    /**
     * @return void
     * @since 1.1.1
     */
    public function testParserHandlesShortArraySyntaxForFieldDeclaration(): void
    {
        $this->assertNotNull($this->getFirstPropertyForTestCase());
    }

    /**
     * testParserAcceptsGotoAsFunctionName
     *
     * @return void
     * @since 1.0.0
     */
    public function testParserAcceptsGotoAsFunctionName(): void
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
    public function testParserAcceptsDirConstantAsFunctionName(): void
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
    public function testParserAcceptsNamespaceKeywordAsFunctionName(): void
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
    public function testParserAcceptsNamespaceConstantAsFunctionName(): void
    {
        $method = $this->getFirstFunctionForTestCase();
        $this->assertEquals('__NAMESPACE__', $method->getName());
    }

    /**
     * testParserAcceptsTraitAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsTraitAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('trait', $method->getName());
    }

    /**
     * testParserAcceptsTraitConstantAsMethodName
     *
     * @return void
     */
    public function testParserAcceptsTraitConstantAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('__trait__', $method->getName());
    }

    /**
     * testParserAcceptsTraitAsFunctionName
     *
     * @return void
     */
    public function testParserAcceptsTraitAsFunctionName(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $this->assertEquals('trait', $function->getName());
    }

    /**
     * testParserAcceptsTraitConstantAsFunctionName
     *
     * @return void
     */
    public function testParserAcceptsTraitConstantAsFunctionName(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $this->assertEquals('__TRAIT__', $function->getName());
    }

    /**
     * testParserAllowsKeywordCallableAsPropertyName
     *
     * @return void
     */
    public function testParserAllowsKeywordCallableAsPropertyName(): void
    {
        $method = $this->getFirstClassMethodForTestCase();
        $this->assertNotNull($method);
    }

    /**
     * @return void
     */
    public function testParserHandlesExtraParenthesisForIsset(): void
    {
        $this->assertEmpty($this->parseCodeResourceForTest());
    }

    /**
     * Returns the first class or interface that could be found in the code under
     * test for the calling test case.
     *
     * @return \PDepend\Source\AST\AbstractASTClassOrInterface
     */
    protected function getFirstTypeForTestCase()
    {
        return $this->parseCodeResourceForTest()
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
