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
use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTMethodPostfix;
use PDepend\Source\AST\ASTProperty;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTType;
use PDepend\Source\AST\ASTTypeArray;
use PDepend\Source\AST\ASTTypeCallable;
use PDepend\Source\Parser\TokenStreamEndException;
use PDepend\Source\Parser\UnexpectedTokenException;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserGeneric} class.
 *
 * @covers \PDepend\Source\Language\PHP\PHPParserGeneric
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 0.9.20
 *
 * @group unittest
 */
class PHPParserGenericTest extends AbstractTestCase
{
    /**
     * testParserAcceptsStringAsClassName
     */
    public function testParserAcceptsStringAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('SimpleClassName', $class->getName());
    }

    /**
     * testParserAcceptsStringAsInterfaceName
     */
    public function testParserAcceptsStringAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('SimpleInterfaceName', $interface->getName());
    }

    /**
     * testParserAcceptsNullAsClassName
     */
    public function testParserAcceptsNullAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('Null', $class->getName());
    }

    /**
     * testParserAcceptsNullAsInterfaceName
     */
    public function testParserAcceptsNullAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('Null', $interface->getName());
    }

    /**
     * testParserAcceptsTrueAsClassName
     */
    public function testParserAcceptsTrueAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('True', $class->getName());
    }

    /**
     * testParserAcceptsTrueAsInterfaceName
     */
    public function testParserAcceptsTrueAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('True', $interface->getName());
    }

    /**
     * testParserAcceptsFalseAsClassName
     */
    public function testParserAcceptsFalseAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('False', $class->getName());
    }

    /**
     * testParserAcceptsFalseAsInterfaceName
     */
    public function testParserAcceptsFalseAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('False', $interface->getName());
    }

    /**
     * testParserAcceptsInsteadofAsClassName
     */
    public function testParserAcceptsInsteadofAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('insteadof', $class->getName());
    }

    /**
     * testParserAcceptsInsteadofAsFunctionName
     */
    public function testParserAcceptsInsteadofAsFunctionName(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $this->assertSame('insteadOf', $function->getName());
    }

    /**
     * testParserAcceptsInsteadofAsInterfaceName
     */
    public function testParserAcceptsInsteadofAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('insteadof', $interface->getName());
    }

    /**
     * testParserAcceptsInsteadofAsMethodName
     */
    public function testParserAcceptsInsteadofAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertSame('insteadOf', $method->getName());
    }

    /**
     * testParserAcceptsInsteadofAsNamespaceName
     */
    public function testParserAcceptsInsteadofAsNamespaceName(): void
    {
        $namespace = $this->getFirstTypeForTestCase()->getNamespaceName();
        $this->assertSame('InsteadOf', $namespace);
    }

    /**
     * testParserAcceptsUseAsClassName
     */
    public function testParserAcceptsUseAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        $this->assertSame('Use', $class->getName());
    }

    /**
     * testParserAcceptsUseAsInterfaceName
     */
    public function testParserAcceptsUseAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('Use', $interface->getName());
    }

    /**
     * testParserAcceptsNamespaceAsClassName
     *
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
     * @since 1.0.0
     */
    public function testParserAcceptsTraitConstantAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        $this->assertSame('__TRAIT__', $interface->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsClassName
     */
    public function testParserAcceptsGotoKeywordAsClassName(): void
    {
        $class = $this->getFirstClassForTestCase();
        $this->assertEquals('Goto', $class->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsInterfaceName
     */
    public function testParserAcceptsGotoKeywordAsInterfaceName(): void
    {
        $class = $this->getFirstInterfaceForTestCase();
        $this->assertEquals('Goto', $class->getName());
    }

    /**
     * testParserAcceptsDirConstantAsClassName
     *
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
     * @covers \PDepend\Source\Parser\TokenStreamEndException
     */
    public function testParserThrowsExpectedExceptionOnTokenStreamEnd(): void
    {
        $this->expectException(TokenStreamEndException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForUnexpectedTokenType
     *
     * @covers \PDepend\Source\Parser\UnexpectedTokenException
     */
    public function testParserThrowsExpectedExceptionForUnexpectedTokenType(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserAcceptsStringAsMethodName
     */
    public function testParserAcceptsStringAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('myMethodName', $method->getName());
    }

    /**
     * testParserAcceptsUseKeywordAsMethodName
     */
    public function testParserAcceptsUseKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('Use', $method->getName());
    }

    /**
     * testParserAcceptsGotoKeywordAsMethodName
     */
    public function testParserAcceptsGotoKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('Goto', $method->getName());
    }

    /**
     * testParserAcceptsSelfKeywordAsMethodName
     */
    public function testParserAcceptsSelfKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('self', $method->getName());
    }

    /**
     * testParserAcceptsNullAsMethodName
     */
    public function testParserAcceptsNullAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('null', $method->getName());
    }

    /**
     * testParserAcceptsTrueAsMethodName
     */
    public function testParserAcceptsTrueAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('true', $method->getName());
    }

    /**
     * testParserAcceptsFalseAsMethodName
     */
    public function testParserAcceptsFalseAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('false', $method->getName());
    }

    /**
     * testParserAcceptsDirConstantAsMethodName
     */
    public function testParserAcceptsDirConstantAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('__DIR__', $method->getName());
    }

    /**
     * testParserAcceptsNamespaceKeywordAsMethodName
     *
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
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceConstantAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('__NAMESPACE__', $method->getName());
    }

    /**
     * testParserAcceptsParentKeywordAsMethodName
     */
    public function testParserAcceptsParentKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('Parent', $method->getName());
    }

    /**
     * testParserHandlesCallableTypeHint
     *
     * @since 1.0.0
     */
    public function testParserHandlesCallableTypeHint(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $type = $method->getFirstChildOfType(ASTType::class);

        $this->assertInstanceOf(ASTTypeCallable::class, $type);
    }

    /**
     * testParserHandlesNamespaceTypeHint
     *
     * @since 1.0.0
     */
    public function testParserHandlesNamespaceTypeHint(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $type = $method->getFirstChildOfType(ASTType::class);

        $this->assertInstanceOf(ASTClassOrInterfaceReference::class, $type);
    }

    /**
     * testParserHandlesArrayTypeHint
     *
     * @since 1.0.0
     */
    public function testParserHandlesArrayTypeHint(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $type = $method->getFirstChildOfType(ASTType::class);

        $this->assertInstanceOf(ASTTypeArray::class, $type);
    }

    /**
     * testParserHandlesSelfTypeHint
     *
     * @since 1.0.0
     */
    public function testParserHandlesSelfTypeHint(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $type = $method->getFirstChildOfType(ASTType::class);

        $this->assertInstanceOf(ASTSelfReference::class, $type);
    }

    /**
     * testParserHandlesCompoundStaticMethodInvocation
     *
     * @since 1.0.0
     */
    public function testParserHandlesCompoundStaticMethodInvocation(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $postfix = $method->getFirstChildOfType(ASTMethodPostfix::class);

        $this->assertNotNull($postfix);
    }

    /**
     * testParserHandlesVariableStaticMethodInvocation
     *
     * @since 1.0.0
     */
    public function testParserHandlesVariableStaticMethodInvocation(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $postfix = $method->getFirstChildOfType(ASTMethodPostfix::class);

        $this->assertNotNull($postfix);
    }

    /**
     * testParserThrowsExpectedExceptionForInvalidToken
     */
    public function testParserThrowsExpectedExceptionForInvalidToken(): void
    {
        $this->expectException(UnexpectedTokenException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserThrowsExpectedExceptionForTokenStreamEnd
     */
    public function testParserThrowsExpectedExceptionForTokenStreamEnd(): void
    {
        $this->expectException(TokenStreamEndException::class);

        $this->parseCodeResourceForTest();
    }

    /**
     * testParserHandlesRegularArraySyntax
     *
     * @since 1.0.0
     */
    public function testParserHandlesRegularArraySyntax(): void
    {
        $this->assertInstanceOf(
            ASTArray::class,
            $this->getFirstMethodForTestCase()
                ->getFirstChildOfType(ASTArray::class)
        );
    }

    /**
     * testParserHandlesShortArraySyntax
     *
     * @since 1.0.0
     */
    public function testParserHandlesShortArraySyntax(): void
    {
        $this->assertInstanceOf(
            ASTArray::class,
            $this->getFirstMethodForTestCase()
                ->getFirstChildOfType(ASTArray::class)
        );
    }

    /**
     * @since 1.1.1
     */
    public function testParserHandlesShortArraySyntaxForFormalParameter(): void
    {
        $this->assertNotNull($this->getFirstMethodForTestCase());
    }

    /**
     * @since 1.1.1
     */
    public function testParserHandlesShortArraySyntaxForFieldDeclaration(): void
    {
        $this->assertNotNull($this->getFirstPropertyForTestCase());
    }

    /**
     * testParserAcceptsGotoAsFunctionName
     *
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
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceConstantAsFunctionName(): void
    {
        $method = $this->getFirstFunctionForTestCase();
        $this->assertEquals('__NAMESPACE__', $method->getName());
    }

    /**
     * testParserAcceptsTraitAsMethodName
     */
    public function testParserAcceptsTraitAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('trait', $method->getName());
    }

    /**
     * testParserAcceptsTraitConstantAsMethodName
     */
    public function testParserAcceptsTraitConstantAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        $this->assertEquals('__trait__', $method->getName());
    }

    /**
     * testParserAcceptsTraitAsFunctionName
     */
    public function testParserAcceptsTraitAsFunctionName(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $this->assertEquals('trait', $function->getName());
    }

    /**
     * testParserAcceptsTraitConstantAsFunctionName
     */
    public function testParserAcceptsTraitConstantAsFunctionName(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        $this->assertEquals('__TRAIT__', $function->getName());
    }

    /**
     * testParserAllowsKeywordCallableAsPropertyName
     */
    public function testParserAllowsKeywordCallableAsPropertyName(): void
    {
        $method = $this->getFirstClassMethodForTestCase();
        $this->assertNotNull($method);
    }

    public function testParserHandlesExtraParenthesisForIsset(): void
    {
        $this->assertEmpty($this->parseCodeResourceForTest());
    }

    /**
     * Returns the first class or interface that could be found in the code under
     * test for the calling test case.
     *
     * @return AbstractASTClassOrInterface
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
     * @return ASTMethod
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
     * @return ASTProperty
     */
    protected function getFirstPropertyForTestCase()
    {
        return $this->getFirstTypeForTestCase()
            ->getProperties()
            ->current();
    }
}
