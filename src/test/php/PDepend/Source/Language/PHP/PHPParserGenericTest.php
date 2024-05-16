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
        static::assertSame('SimpleClassName', $class->getImage());
    }

    /**
     * testParserAcceptsStringAsInterfaceName
     */
    public function testParserAcceptsStringAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        static::assertSame('SimpleInterfaceName', $interface->getImage());
    }

    /**
     * testParserAcceptsNullAsClassName
     */
    public function testParserAcceptsNullAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        static::assertSame('Null', $class->getImage());
    }

    /**
     * testParserAcceptsNullAsInterfaceName
     */
    public function testParserAcceptsNullAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        static::assertSame('Null', $interface->getImage());
    }

    /**
     * testParserAcceptsTrueAsClassName
     */
    public function testParserAcceptsTrueAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        static::assertSame('True', $class->getImage());
    }

    /**
     * testParserAcceptsTrueAsInterfaceName
     */
    public function testParserAcceptsTrueAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        static::assertSame('True', $interface->getImage());
    }

    /**
     * testParserAcceptsFalseAsClassName
     */
    public function testParserAcceptsFalseAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        static::assertSame('False', $class->getImage());
    }

    /**
     * testParserAcceptsFalseAsInterfaceName
     */
    public function testParserAcceptsFalseAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        static::assertSame('False', $interface->getImage());
    }

    /**
     * testParserAcceptsInsteadofAsClassName
     */
    public function testParserAcceptsInsteadofAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        static::assertSame('insteadof', $class->getImage());
    }

    /**
     * testParserAcceptsInsteadofAsFunctionName
     */
    public function testParserAcceptsInsteadofAsFunctionName(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        static::assertSame('insteadOf', $function->getImage());
    }

    /**
     * testParserAcceptsInsteadofAsInterfaceName
     */
    public function testParserAcceptsInsteadofAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        static::assertSame('insteadof', $interface->getImage());
    }

    /**
     * testParserAcceptsInsteadofAsMethodName
     */
    public function testParserAcceptsInsteadofAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertSame('insteadOf', $method->getImage());
    }

    /**
     * testParserAcceptsInsteadofAsNamespaceName
     */
    public function testParserAcceptsInsteadofAsNamespaceName(): void
    {
        $namespace = $this->getFirstTypeForTestCase()->getNamespaceName();
        static::assertSame('InsteadOf', $namespace);
    }

    /**
     * testParserAcceptsUseAsClassName
     */
    public function testParserAcceptsUseAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        static::assertSame('Use', $class->getImage());
    }

    /**
     * testParserAcceptsUseAsInterfaceName
     */
    public function testParserAcceptsUseAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        static::assertSame('Use', $interface->getImage());
    }

    /**
     * testParserAcceptsNamespaceAsClassName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        static::assertSame('Namespace', $class->getImage());
    }

    /**
     * testParserAcceptsNamespaceAsInterfaceName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        static::assertSame('Namespace', $interface->getImage());
    }

    /**
     * testParserAcceptsNamespaceConstantAsClassName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceConstantAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        static::assertSame('__NAMESPACE__', $class->getImage());
    }

    /**
     * testParserAcceptsNamespaceConstantAsInterfaceName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceConstantAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        static::assertSame('__NAMESPACE__', $interface->getImage());
    }

    /**
     * testParserAcceptsTraitAsClassName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsTraitAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        static::assertSame('Trait', $class->getImage());
    }

    /**
     * testParserAcceptsTraitAsInterfaceName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsTraitAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        static::assertSame('Trait', $interface->getImage());
    }

    /**
     * testParserAcceptsTraitConstantAsClassName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsTraitConstantAsClassName(): void
    {
        $class = $this->getFirstTypeForTestCase();
        static::assertSame('__TRAIT__', $class->getImage());
    }

    /**
     * testParserAcceptsTraitConstantAsInterfaceName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsTraitConstantAsInterfaceName(): void
    {
        $interface = $this->getFirstTypeForTestCase();
        static::assertSame('__TRAIT__', $interface->getImage());
    }

    /**
     * testParserAcceptsGotoKeywordAsClassName
     */
    public function testParserAcceptsGotoKeywordAsClassName(): void
    {
        $class = $this->getFirstClassForTestCase();
        static::assertEquals('Goto', $class->getImage());
    }

    /**
     * testParserAcceptsGotoKeywordAsInterfaceName
     */
    public function testParserAcceptsGotoKeywordAsInterfaceName(): void
    {
        $class = $this->getFirstInterfaceForTestCase();
        static::assertEquals('Goto', $class->getImage());
    }

    /**
     * testParserAcceptsDirConstantAsClassName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsDirConstantAsClassName(): void
    {
        $class = $this->getFirstClassForTestCase();
        static::assertEquals('__DIR__', $class->getImage());
    }

    /**
     * testParserAcceptsDirConstantAsInterfaceName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsDirConstantAsInterfaceName(): void
    {
        $class = $this->getFirstInterfaceForTestCase();
        static::assertEquals('__DIR__', $class->getImage());
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
        static::assertEquals('myMethodName', $method->getImage());
    }

    /**
     * testParserAcceptsUseKeywordAsMethodName
     */
    public function testParserAcceptsUseKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertEquals('Use', $method->getImage());
    }

    /**
     * testParserAcceptsGotoKeywordAsMethodName
     */
    public function testParserAcceptsGotoKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertEquals('Goto', $method->getImage());
    }

    /**
     * testParserAcceptsSelfKeywordAsMethodName
     */
    public function testParserAcceptsSelfKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertEquals('self', $method->getImage());
    }

    /**
     * testParserAcceptsNullAsMethodName
     */
    public function testParserAcceptsNullAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertEquals('null', $method->getImage());
    }

    /**
     * testParserAcceptsTrueAsMethodName
     */
    public function testParserAcceptsTrueAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertEquals('true', $method->getImage());
    }

    /**
     * testParserAcceptsFalseAsMethodName
     */
    public function testParserAcceptsFalseAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertEquals('false', $method->getImage());
    }

    /**
     * testParserAcceptsDirConstantAsMethodName
     */
    public function testParserAcceptsDirConstantAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertEquals('__DIR__', $method->getImage());
    }

    /**
     * testParserAcceptsNamespaceKeywordAsMethodName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertEquals('nameSpace', $method->getImage());
    }

    /**
     * testParserAcceptsNamespaceConstantAsMethodName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceConstantAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertEquals('__NAMESPACE__', $method->getImage());
    }

    /**
     * testParserAcceptsParentKeywordAsMethodName
     */
    public function testParserAcceptsParentKeywordAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertEquals('Parent', $method->getImage());
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

        static::assertInstanceOf(ASTTypeCallable::class, $type);
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

        static::assertInstanceOf(ASTClassOrInterfaceReference::class, $type);
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

        static::assertInstanceOf(ASTTypeArray::class, $type);
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

        static::assertInstanceOf(ASTSelfReference::class, $type);
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

        static::assertNotNull($postfix);
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

        static::assertNotNull($postfix);
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
        static::assertInstanceOf(
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
        static::assertInstanceOf(
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
        static::assertNotNull($this->getFirstMethodForTestCase());
    }

    /**
     * @since 1.1.1
     */
    public function testParserHandlesShortArraySyntaxForFieldDeclaration(): void
    {
        static::assertNotNull($this->getFirstPropertyForTestCase());
    }

    /**
     * testParserAcceptsGotoAsFunctionName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsGotoAsFunctionName(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        static::assertEquals('goto', $function->getImage());
    }

    /**
     * testParserAcceptsDirConstantAsFunctionName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsDirConstantAsFunctionName(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        static::assertEquals('__DIR__', $function->getImage());
    }

    /**
     * testParserAcceptsNamespaceKeywordAsFunctionName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceKeywordAsFunctionName(): void
    {
        $method = $this->getFirstFunctionForTestCase();
        static::assertEquals('namespace', $method->getImage());
    }

    /**
     * testParserAcceptsNamespaceConstantAsFunctionName
     *
     * @since 1.0.0
     */
    public function testParserAcceptsNamespaceConstantAsFunctionName(): void
    {
        $method = $this->getFirstFunctionForTestCase();
        static::assertEquals('__NAMESPACE__', $method->getImage());
    }

    /**
     * testParserAcceptsTraitAsMethodName
     */
    public function testParserAcceptsTraitAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertEquals('trait', $method->getImage());
    }

    /**
     * testParserAcceptsTraitConstantAsMethodName
     */
    public function testParserAcceptsTraitConstantAsMethodName(): void
    {
        $method = $this->getFirstMethodForTestCase();
        static::assertEquals('__trait__', $method->getImage());
    }

    /**
     * testParserAcceptsTraitAsFunctionName
     */
    public function testParserAcceptsTraitAsFunctionName(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        static::assertEquals('trait', $function->getImage());
    }

    /**
     * testParserAcceptsTraitConstantAsFunctionName
     */
    public function testParserAcceptsTraitConstantAsFunctionName(): void
    {
        $function = $this->getFirstFunctionForTestCase();
        static::assertEquals('__TRAIT__', $function->getImage());
    }

    /**
     * testParserAllowsKeywordCallableAsPropertyName
     */
    public function testParserAllowsKeywordCallableAsPropertyName(): void
    {
        $method = $this->getFirstClassMethodForTestCase();
        static::assertNotNull($method);
    }

    public function testParserHandlesExtraParenthesisForIsset(): void
    {
        static::assertEmpty($this->parseCodeResourceForTest());
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
