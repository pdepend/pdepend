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
 */

namespace PDepend\Source\AST;

/**
 * Test case for the {@link \PDepend\Source\AST\ASTPropertyPostfix} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\Language\PHP\PHPBuilder
 * @covers \PDepend\Source\AST\ASTClassFqnPostfix
 *
 * @group unittest
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTClassFqnPostfix
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class ASTClassFqnPostfixTest extends ASTNodeTestCase
{
    /**
     * testGetImage
     */
    public function testGetImage(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass();
        $this->assertEquals('class', $postfix->getImage());
    }

    /**
     * testClassFqnPostfixStructureWithStatic
     */
    public function testClassFqnPostfixStructureWithStatic(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInClass(__METHOD__),
            [
                ASTStaticReference::class,
                ASTClassFqnPostfix::class,
            ]
        );
    }

    /**
     * testGetImageWorksCaseInsensitive
     */
    public function testGetImageWorksCaseInsensitive(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass();
        $this->assertEquals('class', $postfix->getImage());
    }

    /**
     * testClassFqnPostfixStructureWithSelf
     */
    public function testClassFqnPostfixStructureWithSelf(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInClass(__METHOD__),
            [
                ASTSelfReference::class,
                ASTClassFqnPostfix::class,
            ]
        );
    }

    /**
     * testClassFqnPostfixStructureWithParent
     */
    public function testClassFqnPostfixStructureWithParent(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInClass(__METHOD__),
            [
                ASTParentReference::class,
                ASTClassFqnPostfix::class,
            ]
        );
    }

    /**
     * testClassFqnPostfixStructureWithClassName
     */
    public function testClassFqnPostfixStructureWithClassName(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInClass(__METHOD__),
            [
                ASTClassOrInterfaceReference::class,
                ASTClassFqnPostfix::class,
            ]
        );
    }

    /**
     * testClassFqnPostfixStructureAsPropertyInitializer
     *
     * <code>
     * protected $foo = \Iterator::class;
     * </code>
     */
    public function testClassFqnPostfixAsPropertyInitializer(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());

        /** @var ASTFieldDeclaration $fieldDeclaration */
        $fieldDeclaration = $this->getFirstClassForTestCase(__METHOD__)->getChild(0);

        $this->assertInstanceOf(ASTFieldDeclaration::class, $fieldDeclaration);

        /** @var ASTVariableDeclarator $variableDeclarator */
        $variableDeclarator = $fieldDeclaration->getChild(0);

        $this->assertInstanceOf(ASTVariableDeclarator::class, $variableDeclarator);
        $this->assertTrue($variableDeclarator->getValue()->isValueAvailable());

        /** @var ASTClassOrInterfaceReference $classReference */
        $classReference = $variableDeclarator->getValue()->getValue();
        $this->assertInstanceOf(ASTClassOrInterfaceReference::class, $classReference);

        $this->assertSame('\\Iterator', $classReference->getImage());
    }

    /**
     * testClassFqnPostfixStructureAsPropertyInitializerWithSelf
     *
     * <code>
     * protected $foo = self::class;
     * </code>
     */
    public function testClassFqnPostfixAsPropertyInitializerWithSelf(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());

        /** @var ASTFieldDeclaration $fieldDeclaration */
        $fieldDeclaration = $this->getFirstClassForTestCase(__METHOD__)->getChild(0);

        $this->assertInstanceOf(ASTFieldDeclaration::class, $fieldDeclaration);

        /** @var ASTVariableDeclarator $variableDeclarator */
        $variableDeclarator = $fieldDeclaration->getChild(0);

        $this->assertInstanceOf(ASTVariableDeclarator::class, $variableDeclarator);
        $this->assertTrue($variableDeclarator->getValue()->isValueAvailable());

        /** @var ASTSelfReference $classReference */
        $classReference = $variableDeclarator->getValue()->getValue();
        $this->assertInstanceOf(ASTSelfReference::class, $classReference);

        $this->assertSame('self', $classReference->getImage());
    }

    /**
     * testClassFqnPostfixAsParameterInitializer
     */
    public function testClassFqnPostfixAsParameterInitializer(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());

        /** @var ASTParameter[] $parameters */
        $parameters = $this->getFirstClassMethodForTestCase(__METHOD__)->getParameters();
        $this->assertCount(1, $parameters);

        $this->assertInstanceOf(ASTParameter::class, $parameters[0]);

        $this->assertSame('testClassFqnPostfixAsParameterInitializer', $parameters[0]->getDefaultValue()->getImage());
    }

    /**
     * testClassFqnPostfixAsParameterInitializerWithSelf
     */
    public function testClassFqnPostfixAsParameterInitializerWithSelf(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());

        /** @var ASTParameter[] $parameters */
        $parameters = $this->getFirstClassMethodForTestCase(__METHOD__)->getParameters();
        $this->assertCount(1, $parameters);

        $this->assertInstanceOf(ASTParameter::class, $parameters[0]);

        $this->assertSame('self', $parameters[0]->getDefaultValue()->getImage());
    }

    /**
     * testClassFqnPostfixAsConstantInitializer
     */
    public function testClassFqnPostfixAsConstantInitializer(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());

        /** @var ASTConstantDefinition $constantDefinition */
        $constantDefinition = $this->getFirstClassForTestCase()->getChild(0);

        $this->assertInstanceOf(ASTConstantDefinition::class, $constantDefinition);

        /** @var ASTConstantDeclarator $constantDefinition */
        $constantDeclarator = $constantDefinition->getChild(0);

        $this->assertInstanceOf(ASTConstantDeclarator::class, $constantDeclarator);
    }

    /**
     * testClassFqnPostfixAsConstantInitializerWithSelf
     */
    public function testClassFqnPostfixAsConstantInitializerWithSelf(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());

        /** @var ASTConstantDefinition $constantDefinition */
        $constantDefinition = $this->getFirstClassForTestCase()->getChild(0);

        $this->assertInstanceOf(ASTConstantDefinition::class, $constantDefinition);

        /** @var ASTConstantDeclarator $constantDefinition */
        $constantDeclarator = $constantDefinition->getChild(0);

        $this->assertInstanceOf(ASTConstantDeclarator::class, $constantDeclarator);
    }

    /**
     * testClassFqnPostfixHasExpectedStartLine
     */
    public function testClassFqnPostfixHasExpectedStartLine(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass(__METHOD__);
        $this->assertEquals(6, $postfix->getStartLine());
    }

    /**
     * testClassFqnPostfixHasExpectedStartColumn
     */
    public function testClassFqnPostfixHasExpectedStartColumn(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass(__METHOD__);
        $this->assertEquals(26, $postfix->getStartColumn());
    }

    /**
     * testClassFqnPostfixHasExpectedEndLine
     */
    public function testClassFqnPostfixHasExpectedEndLine(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass(__METHOD__);
        $this->assertEquals(6, $postfix->getEndLine());
    }

    /**
     * testClassFqnPostfixHasExpectedEndColumn
     */
    public function testClassFqnPostfixHasExpectedEndColumn(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass(__METHOD__);
        $this->assertEquals(63, $postfix->getEndColumn());
    }

    /**
     * Creates a field declaration node.
     *
     * @return ASTClassFqnPostfix
     */
    protected function createNodeInstance()
    {
        return new ASTClassFqnPostfix(__CLASS__);
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return ASTClassFqnPostfix
     */
    private function getFirstClassFqnPostfixInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            ASTClassFqnPostfix::class
        );
    }
    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     * @return ASTMemberPrimaryPrefix
     */
    private function getFirstMemberPrimaryPrefixInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase,
            ASTMemberPrimaryPrefix::class
        );
    }
}
