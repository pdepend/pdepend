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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\Language\PHP\PHPBuilder
 * @covers \PDepend\Source\AST\ASTClassFqnPostfix
 * @group unittest
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @covers \PDepend\Source\AST\ASTClassFqnPostfix
 */
class ASTClassFqnPostfixTest extends ASTNodeTestCase
{
    /**
     * testGetImage
     *
     * @return void
     */
    public function testGetImage(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass();
        $this->assertEquals('class', $postfix->getImage());
    }

    /**
     * testClassFqnPostfixStructureWithStatic
     *
     * @return void
     */
    public function testClassFqnPostfixStructureWithStatic(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInClass(__METHOD__),
            array(
                'PDepend\\Source\\AST\\ASTStaticReference',
                'PDepend\\Source\\AST\\ASTClassFqnPostfix'
            )
        );
    }

    /**
     * testGetImageWorksCaseInsensitive
     *
     * @return void
     */
    public function testGetImageWorksCaseInsensitive(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass();
        $this->assertEquals('class', $postfix->getImage());
    }

    /**
     * testClassFqnPostfixStructureWithSelf
     *
     * @return void
     */
    public function testClassFqnPostfixStructureWithSelf(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInClass(__METHOD__),
            array(
                'PDepend\\Source\\AST\\ASTSelfReference',
                'PDepend\\Source\\AST\\ASTClassFqnPostfix'
            )
        );
    }

    /**
     * testClassFqnPostfixStructureWithParent
     *
     * @return void
     */
    public function testClassFqnPostfixStructureWithParent(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInClass(__METHOD__),
            array(
                'PDepend\\Source\\AST\\ASTParentReference',
                'PDepend\\Source\\AST\\ASTClassFqnPostfix'
            )
        );
    }

    /**
     * testClassFqnPostfixStructureWithClassName
     *
     * @return void
     */
    public function testClassFqnPostfixStructureWithClassName(): void
    {
        $this->assertGraphEquals(
            $this->getFirstMemberPrimaryPrefixInClass(__METHOD__),
            array(
                'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
                'PDepend\\Source\\AST\\ASTClassFqnPostfix'
            )
        );
    }

    /**
     * testClassFqnPostfixStructureAsPropertyInitializer
     *
     * <code>
     * protected $foo = \Iterator::class;
     * </code>
     *
     * @return void
     */
    public function testClassFqnPostfixAsPropertyInitializer(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());

        /** @var \PDepend\Source\AST\ASTFieldDeclaration $fieldDeclaration */
        $fieldDeclaration = $this->getFirstClassForTestCase(__METHOD__)->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFieldDeclaration', $fieldDeclaration);

        /** @var \PDepend\Source\AST\ASTVariableDeclarator $variableDeclarator */
        $variableDeclarator = $fieldDeclaration->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariableDeclarator', $variableDeclarator);
        $this->assertTrue($variableDeclarator->getValue()->isValueAvailable());

        /** @var \PDepend\Source\AST\ASTClassOrInterfaceReference $classReference */
        $classReference = $variableDeclarator->getValue()->getValue();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClassOrInterfaceReference', $classReference);

        $this->assertSame('\\Iterator', $classReference->getImage());
    }

    /**
     * testClassFqnPostfixStructureAsPropertyInitializerWithSelf
     *
     * <code>
     * protected $foo = self::class;
     * </code>
     *
     * @return void
     */
    public function testClassFqnPostfixAsPropertyInitializerWithSelf(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());

        /** @var \PDepend\Source\AST\ASTFieldDeclaration $fieldDeclaration */
        $fieldDeclaration = $this->getFirstClassForTestCase(__METHOD__)->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFieldDeclaration', $fieldDeclaration);

        /** @var \PDepend\Source\AST\ASTVariableDeclarator $variableDeclarator */
        $variableDeclarator = $fieldDeclaration->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariableDeclarator', $variableDeclarator);
        $this->assertTrue($variableDeclarator->getValue()->isValueAvailable());

        /** @var \PDepend\Source\AST\ASTSelfReference $classReference */
        $classReference = $variableDeclarator->getValue()->getValue();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSelfReference', $classReference);

        $this->assertSame('self', $classReference->getImage());
    }

    /**
     * testClassFqnPostfixAsParameterInitializer
     *
     * @return void
     */
    public function testClassFqnPostfixAsParameterInitializer(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());

        /** @var \PDepend\Source\AST\ASTParameter[] $parameters */
        $parameters = $this->getFirstClassMethodForTestCase(__METHOD__)->getParameters();
        $this->assertCount(1, $parameters);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTParameter', $parameters[0]);

        $this->assertSame('testClassFqnPostfixAsParameterInitializer', $parameters[0]->getDefaultValue()->getImage());
    }

    /**
     * testClassFqnPostfixAsParameterInitializerWithSelf
     *
     * @return void
     */
    public function testClassFqnPostfixAsParameterInitializerWithSelf(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());

        /** @var \PDepend\Source\AST\ASTParameter[] $parameters */
        $parameters = $this->getFirstClassMethodForTestCase(__METHOD__)->getParameters();
        $this->assertCount(1, $parameters);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTParameter', $parameters[0]);

        $this->assertSame('self', $parameters[0]->getDefaultValue()->getImage());
    }

    /**
     * testClassFqnPostfixAsConstantInitializer
     *
     * @return void
     */
    public function testClassFqnPostfixAsConstantInitializer(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());

        /** @var \PDepend\Source\AST\ASTConstantDefinition $constantDefinition */
        $constantDefinition = $this->getFirstClassForTestCase()->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantDefinition', $constantDefinition);

        /** @var \PDepend\Source\AST\ASTConstantDeclarator $constantDefinition */
        $constantDeclarator = $constantDefinition->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantDeclarator', $constantDeclarator);
    }

    /**
     * testClassFqnPostfixAsConstantInitializerWithSelf
     *
     * @return void
     */
    public function testClassFqnPostfixAsConstantInitializerWithSelf(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());

        /** @var \PDepend\Source\AST\ASTConstantDefinition $constantDefinition */
        $constantDefinition = $this->getFirstClassForTestCase()->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantDefinition', $constantDefinition);

        /** @var \PDepend\Source\AST\ASTConstantDeclarator $constantDefinition */
        $constantDeclarator = $constantDefinition->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantDeclarator', $constantDeclarator);
    }

    /**
     * testClassFqnPostfixHasExpectedStartLine
     *
     * @return void
     */
    public function testClassFqnPostfixHasExpectedStartLine(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass(__METHOD__);
        $this->assertEquals(6, $postfix->getStartLine());
    }

    /**
     * testClassFqnPostfixHasExpectedStartColumn
     *
     * @return void
     */
    public function testClassFqnPostfixHasExpectedStartColumn(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass(__METHOD__);
        $this->assertEquals(26, $postfix->getStartColumn());
    }

    /**
     * testClassFqnPostfixHasExpectedEndLine
     *
     * @return void
     */
    public function testClassFqnPostfixHasExpectedEndLine(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass(__METHOD__);
        $this->assertEquals(6, $postfix->getEndLine());
    }

    /**
     * testClassFqnPostfixHasExpectedEndColumn
     *
     * @return void
     */
    public function testClassFqnPostfixHasExpectedEndColumn(): void
    {
        $postfix = $this->getFirstClassFqnPostfixInClass(__METHOD__);
        $this->assertEquals(63, $postfix->getEndColumn());
    }

    /**
     * Creates a field declaration node.
     *
     * @return \PDepend\Source\AST\ASTClassFqnPostfix
     */
    protected function createNodeInstance()
    {
        return new ASTClassFqnPostfix(__CLASS__);
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @return \PDepend\Source\AST\ASTClassFqnPostfix
     */
    private function getFirstClassFqnPostfixInClass()
    {
        return $this->getFirstNodeOfTypeInClass(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTClassFqnPostfix'
        );
    }
    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return \PDepend\Source\AST\ASTMemberPrimaryPrefix
     */
    private function getFirstMemberPrimaryPrefixInClass($testCase)
    {
        return $this->getFirstNodeOfTypeInClass(
            $testCase,
            'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix'
        );
    }
}
