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

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTArrayElement;
use PDepend\Source\AST\ASTArrayIndexExpression;
use PDepend\Source\AST\ASTAssignmentExpression;
use PDepend\Source\AST\ASTBooleanAndExpression;
use PDepend\Source\AST\ASTBooleanOrExpression;
use PDepend\Source\AST\ASTBreakStatement;
use PDepend\Source\AST\ASTCastExpression;
use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTClassFqnPostfix;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTClassReference;
use PDepend\Source\AST\ASTCloneExpression;
use PDepend\Source\AST\ASTClosure;
use PDepend\Source\AST\ASTComment;
use PDepend\Source\AST\ASTCompoundExpression;
use PDepend\Source\AST\ASTCompoundVariable;
use PDepend\Source\AST\ASTConditionalExpression;
use PDepend\Source\AST\ASTConstant;
use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\AST\ASTConstantDefinition;
use PDepend\Source\AST\ASTConstantPostfix;
use PDepend\Source\AST\ASTContinueStatement;
use PDepend\Source\AST\ASTDeclareStatement;
use PDepend\Source\AST\ASTDoWhileStatement;
use PDepend\Source\AST\ASTEchoStatement;
use PDepend\Source\AST\ASTElseIfStatement;
use PDepend\Source\AST\ASTEvalExpression;
use PDepend\Source\AST\ASTExitExpression;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTFinallyStatement;
use PDepend\Source\AST\ASTForeachStatement;
use PDepend\Source\AST\ASTForInit;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTForStatement;
use PDepend\Source\AST\ASTForUpdate;
use PDepend\Source\AST\ASTFunction;
use PDepend\Source\AST\ASTFunctionPostfix;
use PDepend\Source\AST\ASTGlobalStatement;
use PDepend\Source\AST\ASTGotoStatement;
use PDepend\Source\AST\ASTHeredoc;
use PDepend\Source\AST\ASTIdentifier;
use PDepend\Source\AST\ASTIfStatement;
use PDepend\Source\AST\ASTIncludeExpression;
use PDepend\Source\AST\ASTInstanceOfExpression;
use PDepend\Source\AST\ASTIssetExpression;
use PDepend\Source\AST\ASTLabelStatement;
use PDepend\Source\AST\ASTListExpression;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTLogicalAndExpression;
use PDepend\Source\AST\ASTLogicalOrExpression;
use PDepend\Source\AST\ASTLogicalXorExpression;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTMethodPostfix;
use PDepend\Source\AST\ASTParentReference;
use PDepend\Source\AST\ASTPostfixExpression;
use PDepend\Source\AST\ASTPreDecrementExpression;
use PDepend\Source\AST\ASTPreIncrementExpression;
use PDepend\Source\AST\ASTRequireExpression;
use PDepend\Source\AST\ASTReturnStatement;
use PDepend\Source\AST\ASTScalarType;
use PDepend\Source\AST\ASTScope;
use PDepend\Source\AST\ASTScopeStatement;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTShiftLeftExpression;
use PDepend\Source\AST\ASTShiftRightExpression;
use PDepend\Source\AST\ASTStatement;
use PDepend\Source\AST\ASTStaticReference;
use PDepend\Source\AST\ASTStaticVariableDeclaration;
use PDepend\Source\AST\ASTString;
use PDepend\Source\AST\ASTStringIndexExpression;
use PDepend\Source\AST\ASTSwitchLabel;
use PDepend\Source\AST\ASTSwitchStatement;
use PDepend\Source\AST\ASTThrowStatement;
use PDepend\Source\AST\ASTTraitAdaptation;
use PDepend\Source\AST\ASTTraitAdaptationAlias;
use PDepend\Source\AST\ASTTraitAdaptationPrecedence;
use PDepend\Source\AST\ASTTraitReference;
use PDepend\Source\AST\ASTTraitUseStatement;
use PDepend\Source\AST\ASTTryStatement;
use PDepend\Source\AST\ASTTypeArray;
use PDepend\Source\AST\ASTTypeCallable;
use PDepend\Source\AST\ASTTypeIterable;
use PDepend\Source\AST\ASTUnaryExpression;
use PDepend\Source\AST\ASTUnsetStatement;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableDeclarator;
use PDepend\Source\AST\ASTVariableVariable;
use PDepend\Source\AST\ASTWhileStatement;

/**
 * Test case implementation for the default node builder implementation.
 *
 * @covers \PDepend\Source\Language\PHP\PHPBuilder
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class PHPBuilderTest extends AbstractTestCase
{
    /**
     * testBuilderAddsMultiplePackagesForClassesToListOfPackages
     */
    public function testBuilderAddsMultiplePackagesForClassesToListOfPackages(): void
    {
        $builder = $this->createBuilder();

        $namespace = $builder->buildNamespace(__FUNCTION__);
        $namespace->addType($builder->buildClass(__FUNCTION__));

        $namespace = $builder->buildNamespace(__CLASS__);
        $namespace->addType($builder->buildClass(__CLASS__));

        static::assertCount(2, $builder->getNamespaces());
    }

    /**
     * testBuilderAddsMultiplePackagesForFunctionsToListOfPackages
     */
    public function testBuilderAddsMultiplePackagesForFunctionsToListOfPackages(): void
    {
        $builder = $this->createBuilder();

        $builder->buildNamespace(__FUNCTION__);
        $builder->buildFunction(__FUNCTION__);

        $builder->buildNamespace(__CLASS__);
        $builder->buildFunction(__CLASS__);

        static::assertCount(2, $builder->getNamespaces());
    }

    /**
     * testBuilderNotAddsNewPackagesOnceItHasReturnedTheListOfPackages
     */
    public function testBuilderNotAddsNewPackagesOnceItHasReturnedTheListOfPackages(): void
    {
        $builder = $this->createBuilder();

        $namespace = $builder->buildNamespace(__FUNCTION__);
        $namespace->addFunction($builder->buildFunction(__FUNCTION__));

        $builder->getNamespaces();

        $namespace = $builder->buildNamespace(__CLASS__);
        $namespace->addType($builder->buildClass(__CLASS__));

        static::assertEquals(1, $builder->getNamespaces()->count());
    }

    /**
     * testRestoreFunctionAddsFunctionToPackage
     */
    public function testRestoreFunctionAddsFunctionToPackage(): void
    {
        $builder = $this->createBuilder();
        $namespace = $builder->buildNamespace(__CLASS__);

        $function = new ASTFunction(__FUNCTION__);
        $function->setNamespace($namespace);

        $builder->restoreFunction($function);
        static::assertCount(1, $namespace->getFunctions());
    }

    /**
     * testRestoreFunctionUsesGetNamespaceNameMethod
     */
    public function testRestoreFunctionUsesGetNamespaceNameMethod(): void
    {
        $function = $this->getMockBuilder(ASTFunction::class)
            ->setConstructorArgs([__FUNCTION__])
            ->getMock();
        $function->expects(static::once())
            ->method('getNamespaceName');

        $builder = $this->createBuilder();
        $builder->restoreFunction($function);
    }

    /**
     * testBuildTraitWithSameQualifiedNameUnique
     *
     * @since 1.0.0
     */
    public function testBuildTraitWithSameQualifiedNameUnique(): void
    {
        $builder = $this->createBuilder();

        $trait = $builder->buildTrait(__FUNCTION__);
        $trait->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreTrait($trait);

        static::assertSame($trait, $builder->getTrait(__FUNCTION__));
    }

    /**
     * testGetTraitReturnsDummyIfNoMatchingTraitExists
     *
     * @since 1.0.0
     */
    public function testGetTraitReturnsDummyIfNoMatchingTraitExists(): void
    {
        $builder = $this->createBuilder();
        static::assertEquals(__FUNCTION__, $builder->getTrait(__FUNCTION__)->getImage());
    }

    /**
     * Tests that the {@link \PDepend\Source\Language\PHP\PHPBuilder::buildTrait()}
     * method creates two different trait instances for the same class name, but
     * different packages.
     */
    public function testBuildTraitCreatesTwoDifferentInstancesForDifferentPackages(): void
    {
        $builder = $this->createBuilder();

        $trait1 = $builder->buildTrait('PDepend1\Parser');
        $trait2 = $builder->buildTrait('PDepend2\Parser');

        static::assertNotSame($trait1, $trait2);
    }

    /**
     * Tests that {@link \PDepend\Source\Language\PHP\PHPBuilder::buildTrait()} returns
     * a previous trait instance for a specified package, if it is called for a
     * same named trait in the default package.
     */
    public function testBuildTraitReusesExistingNonDefaultPackageInstanceForDefaultPackage(): void
    {
        $builder = $this->createBuilder();

        $trait = $builder->buildTrait('PDepend\Parser');
        $trait->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreTrait($trait);

        static::assertSame(
            $trait->getNamespace(),
            $builder->getTrait('Parser')->getNamespace()
        );
    }

    /**
     * Tests that the node builder creates a class for the same name only once.
     */
    public function testBuildClassUnique(): void
    {
        $builder = $this->createBuilder();

        $class = $builder->buildClass(__FUNCTION__);
        $class->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreClass($class);

        static::assertSame($class, $builder->getClass(__FUNCTION__));
    }

    /**
     * testGetClassReturnsDummyIfNoMatchingTraitExists
     *
     * @since 1.0.0
     */
    public function testGetClassReturnsDummyIfNoMatchingClassExists(): void
    {
        $builder = $this->createBuilder();
        static::assertEquals(
            __FUNCTION__,
            $builder->getClass(__FUNCTION__)->getImage()
        );
    }

    /**
     * Tests that the {@link \PDepend\Source\Language\PHP\PHPBuilder::buildClass()} method
     * creates two different class instances for the same class name, but
     * different packages.
     */
    public function testBuildClassCreatesTwoDifferentInstancesForDifferentPackages(): void
    {
        $builder = $this->createBuilder();

        $class1 = $builder->buildClass('PDepend1\Parser');
        $class2 = $builder->buildClass('PDepend2\Parser');

        static::assertNotSame($class1, $class2);
    }

    /**
     * Tests that {@link \PDepend\Source\Language\PHP\PHPBuilder::buildClass()}
     * returns a previous class instance for a specified package, if it is called
     * for a same named class in the default package.
     */
    public function testBuildClassReusesExistingNonDefaultPackageInstanceForDefaultPackage(): void
    {
        $builder = $this->createBuilder();

        $class1 = $builder->buildClass('PDepend\Parser');
        $class1->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreClass($class1);

        static::assertSame(
            $class1->getNamespace(),
            $builder->getClass('Parser')->getNamespace()
        );
    }

    /**
     * Tests that the node build generates an unique interface instance for the
     * same identifier.
     */
    public function testBuildInterfaceUnique(): void
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface(__FUNCTION__);
        $interface->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreInterface($interface);

        static::assertSame($interface, $builder->getInterface(__FUNCTION__));
    }

    /**
     * testGetInterfaceReturnsDummyIfNoMatchingInterfaceExists
     *
     * @since 1.0.0
     */
    public function testGetInterfaceReturnsDummyIfNoMatchingInterfaceExists(): void
    {
        $builder = $this->createBuilder();
        static::assertEquals(
            __FUNCTION__,
            $builder->getInterface(__FUNCTION__)->getImage()
        );
    }

    /**
     * Tests that the {@link \PDepend\Source\Language\PHP\PHPBuilder::buildInterface()}
     * method only removes/replaces a previously created class instance, when
     * this class is part of the default namespace. Otherwise there are two user
     * types with the same local or package internal name.
     */
    public function testBuildInterfaceDoesntRemoveClassForSameNamedInterface(): void
    {
        $builder = $this->createBuilder();

        $namespace1 = $builder->buildNamespace('package1');
        $namespace2 = $builder->buildNamespace('package2');

        $class = $builder->buildClass('Parser');
        $namespace1->addType($class);

        static::assertEquals(1, $namespace1->getTypes()->count());

        $builder->buildInterface('Parser');

        static::assertEquals(1, $namespace1->getTypes()->count());
    }

    /**
     * Tests that {@link \PDepend\Source\Language\PHP\PHPBuilder::buildInterface()}
     * creates different interface instances for different parent packages.
     */
    public function testBuildInterfacesCreatesDifferentInstancesForDifferentPackages(): void
    {
        $builder = $this->createBuilder();

        $interfaces1 = $builder->buildInterface('PDepend1\ParserI');
        $interfaces2 = $builder->buildInterface('PDepend2\ParserI');

        static::assertNotSame($interfaces1, $interfaces2);
    }

    /**
     * Tests that {@link \PDepend\Source\Language\PHP\PHPBuilder::buildInterface()}
     * replaces an existing default package interface instance, if it creates a
     * more specific version.
     */
    public function testCanCreateMultipleInterfaceInstancesWithIdenticalNames(): void
    {
        $builder = $this->createBuilder();

        $interface1 = $builder->buildInterface('PDepend\ParserI');
        $interface2 = $builder->buildInterface('PDepend\ParserI');

        static::assertNotSame($interface1, $interface2);
        static::assertSame(
            $interface1->getNamespace(),
            $interface2->getNamespace()
        );
    }

    /**
     * Tests that {@link \PDepend\Source\Language\PHP\PHPBuilder::buildInterface()}
     * returns a previous interface instance for a specified package, if it is called
     * for a same named interface in the default package.
     */
    public function testBuildInterfaceReusesExistingNonDefaultPackageInstanceForDefaultPackage(): void
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface('PDepend\ParserI');
        $interface->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreInterface($interface);

        static::assertSame($builder->getInterface('ParserI'), $interface);
        static::assertSame(
            $builder->getInterface('ParserI')->getNamespace(),
            $interface->getNamespace()
        );
    }

    /**
     * Tests the 'PDepend\\Source\\AST\\ASTMethod build method.
     */
    public function testBuildMethod(): void
    {
        static::assertInstanceOf(
            ASTMethod::class,
            $this->createBuilder()->buildMethod('method')
        );
    }

    /**
     * Tests that the node builder creates a package for the same name only once.
     */
    public function testBuildPackageUnique(): void
    {
        $builder = $this->createBuilder();
        $namespace1 = $builder->buildNamespace('package1');
        $namespace2 = $builder->buildNamespace('package1');

        static::assertSame($namespace1, $namespace2);
    }

    /**
     * Tests the implemented {@link IteratorAggregate}.
     */
    public function testGetIteratorWithPackages(): void
    {
        $builder = $this->createBuilder();

        $expected = [
            'package1' => $builder->buildNamespace('package1'),
            'package2' => $builder->buildNamespace('package2'),
            'package3' => $builder->buildNamespace('package3'),
        ];

        $actual = [];
        foreach ($builder as $name => $namespace) {
            $actual[$name] = $namespace;
        }

        static::assertSame($expected, $actual);
    }

    /**
     * Tests the {@link \PDepend\Source\Language\PHP\PHPBuilder::getNamespaces()}
     * method.
     */
    public function testGetNamespaces(): void
    {
        $builder = $this->createBuilder();

        $expected = [
            'package1' => $builder->buildNamespace('package1'),
            'package2' => $builder->buildNamespace('package2'),
            'package3' => $builder->buildNamespace('package3'),
        ];

        $actual = [];
        foreach ($builder->getNamespaces() as $name => $namespace) {
            $actual[$name] = $namespace;
        }

        static::assertSame($expected, $actual);
    }

    /**
     * There was a missing check within an if statement, so that the builder
     * has alway overwritten previously created instances.
     */
    public function testBuildClassDoesNotOverwritePreviousInstances(): void
    {
        $builder = $this->createBuilder();

        $class0 = $builder->buildClass('FooBar');
        $class0->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreClass($class0);

        $class1 = $builder->buildClass('FooBar');
        $class1->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreClass($class1);

        static::assertNotSame($class0, $class1);
        static::assertSame($class0, $builder->getClass('FooBar'));
    }

    /**
     * There was a missing check within an if statement, so that the builder
     * has alway overwritten previously created instances.
     */
    public function testBuildInterfaceDoesNotOverwritePreviousInstances(): void
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface('FooBar');
        $interface->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreInterface($interface);

        static::assertNotSame($interface, $builder->buildInterface('FooBar'));
        static::assertSame($interface, $builder->getInterface('FooBar'));
    }

    /**
     * Tests that the node builder works case insensitive for class names.
     */
    public function testBuildClassWorksCaseInsensitiveIssue26(): void
    {
        $builder = $this->createBuilder();

        $class = $builder->buildClass('PDepend_Parser');
        $class->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreClass($class);

        static::assertSame($class, $builder->getClass('pDepend_parser'));
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     */
    public function testBuildInterfaceWorksCaseInsensitiveIssue26(): void
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface('PDepend_Source_Tokenizer_Tokenizer');
        $interface->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreInterface($interface);

        static::assertSame(
            $interface,
            $builder->getInterface('PDepend_Source_Tokenizer_ToKeNiZeR')
        );
    }

    /**
     * testGetClassOrInterfaceReturnsDummyIfNoMatchingTypeExists
     *
     * @since 1.0.0
     */
    public function testGetClassOrInterfaceReturnsDummyIfNoMatchingTypeExists(): void
    {
        $builder = $this->createBuilder();
        static::assertEquals(
            __FUNCTION__,
            $builder->getClassOrInterface(__FUNCTION__)->getImage()
        );
    }

    /**
     * testGetClassOrInterfaceReturnsClassInExtensionPackage
     *
     * @since 1.0.0
     */
    public function testGetClassOrInterfaceReturnsClassInExtensionPackage(): void
    {
        $builder = $this->createBuilder();
        static::assertEquals(
            '+reflection',
            $builder->getClassOrInterface('Reflection')->getNamespace()?->getImage()
        );
    }

    /**
     * testGetClassOrInterfaceStripsLeadingBackslashFromClass
     *
     * @since 1.0.0
     */
    public function testGetClassOrInterfaceStripsLeadingBackslashFromClass(): void
    {
        $namespace = $this->createBuilder()->getClassOrInterface('\foo\bar\Baz')->getNamespace();
        static::assertNotNull($namespace);
        static::assertEquals('foo\bar', $namespace->getImage());
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive1Issue26(): void
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface('PDepend_Source_Tokenizer_Tokenizer');
        $interface->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreInterface($interface);

        static::assertSame(
            $interface,
            $builder->getClassOrInterface('PDepend_Source_Tokenizer_ToKeNiZeR')
        );
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive2Issue26(): void
    {
        $builder = $this->createBuilder();

        $class = $builder->buildClass('PDepend_Parser');
        $class->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreClass($class);

        static::assertSame($class, $builder->getClassOrInterface('pDepend_parser'));
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     */
    public function testBuildASTClassOrInterfaceReferenceThrowsExpectedExceptionWhenStateIsFrozen(): void
    {
        $builder = $this->createBuilder();
        $builder->buildAstClassOrInterfaceReference('Foo');

        // Freeze object
        $builder->getClass('Foo');

        $this->expectException(
            'BadMethodCallException'
        );
        $this->expectExceptionMessage(
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildAstClassOrInterfaceReference('Bar');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     */
    public function testBuildClassThrowsExpectedExceptionWhenStateIsFrozen(): void
    {
        $builder = $this->createBuilder();
        $builder->buildClass('Foo');

        // Freeze object
        $builder->getClass('Foo');

        $this->expectException(
            'BadMethodCallException'
        );
        $this->expectExceptionMessage(
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildClass('Bar');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     */
    public function testBuildASTClassReferenceThrowsExpectedExceptionWhenStateIsFrozen(): void
    {
        $builder = $this->createBuilder();
        $builder->buildAstClassReference('Foo');

        // Freeze object
        $builder->getClass('Foo');

        $this->expectException(
            'BadMethodCallException'
        );
        $this->expectExceptionMessage(
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildAstClassReference('Bar');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     */
    public function testBuildInterfaceThrowsExpectedExceptionWhenStateIsFrozen(): void
    {
        $builder = $this->createBuilder();
        $builder->buildInterface('Inter');

        // Freeze object
        $builder->getInterface('Inter');

        $this->expectException(
            'BadMethodCallException'
        );
        $this->expectExceptionMessage(
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildInterface('Face');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     */
    public function testBuildMethodThrowsExpectedExceptionWhenStateIsFrozen(): void
    {
        $builder = $this->createBuilder();
        $builder->buildMethod('call');

        // Freeze object
        $builder->getInterface('Inter');

        $this->expectException(
            'BadMethodCallException'
        );
        $this->expectExceptionMessage(
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildMethod('invoke');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     */
    public function testBuildFunctionThrowsExpectedExceptionWhenStateIsFrozen(): void
    {
        $builder = $this->createBuilder();
        $builder->buildFunction('func');

        // Freeze object
        $builder->getInterface('Inter');

        $this->expectException(
            'BadMethodCallException'
        );
        $this->expectExceptionMessage(
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildFunction('prop');
    }

    /**
     * testBuildASTCommentReturnsExpectedType
     */
    public function testBuildASTCommentReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTComment::class,
            $this->createBuilder()->buildAstComment('// Hello')
        );
    }

    /**
     * testBuildASTScalarTypeReturnsExpectedType
     */
    public function testBuildASTScalarTypeReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTScalarType::class,
            $this->createBuilder()->buildAstScalarType('1')
        );
    }

    /**
     * testBuildASTTypeArrayReturnsExpectedType
     */
    public function testBuildASTTypeArrayReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTTypeArray::class,
            $this->createBuilder()->buildAstTypeArray()
        );
    }

    /**
     * testBuildASTTypeCallableReturnsExpectedType
     *
     * @since 1.0.0
     */
    public function testBuildASTTypeCallableReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTTypeCallable::class,
            $this->createBuilder()->buildAstTypeCallable()
        );
    }

    /**
     * testBuildASTTypeCallableReturnsExpectedType
     *
     * @since 1.0.0
     */
    public function testBuildASTTypeIterableReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTTypeIterable::class,
            $this->createBuilder()->buildAstTypeIterable()
        );
    }

    /**
     * testBuildASTHeredocReturnsExpectedType
     */
    public function testBuildASTHeredocReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTHeredoc::class,
            $this->createBuilder()->buildAstHeredoc()
        );
    }

    /**
     * testBuildASTIdentifierReturnsExpectedType
     */
    public function testBuildASTIdentifierReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTIdentifier::class,
            $this->createBuilder()->buildAstIdentifier('ID')
        );
    }

    /**
     * testBuildASTLiteralReturnsExpectedType
     */
    public function testBuildASTLiteralReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTLiteral::class,
            $this->createBuilder()->buildAstLiteral('false')
        );
    }

    /**
     * testBuildASTStringReturnsExpectedType
     */
    public function testBuildASTStringReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTString::class,
            $this->createBuilder()->buildAstString()
        );
    }

    /**
     * testBuildASTArrayReturnsExpectedType
     *
     * @since 1.0.0
     */
    public function testBuildASTArrayReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTArray::class,
            $this->createBuilder()->buildAstArray()
        );
    }

    /**
     * testBuildASTArrayElementReturnsExpectedType
     *
     * @since 1.0.0
     */
    public function testBuildASTArrayElementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTArrayElement::class,
            $this->createBuilder()->buildAstArrayElement()
        );
    }

    /**
     * testBuildASTScopeReturnsExpectedType
     */
    public function testBuildASTScopeReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTScope::class,
            $this->createBuilder()->buildAstScope()
        );
    }

    /**
     * testBuildASTVariableReturnsExpectedType
     */
    public function testBuildASTVariableReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTVariable::class,
            $this->createBuilder()->buildAstVariable('$name')
        );
    }

    /**
     * testBuildASTVariableVariableReturnsExpectedType
     */
    public function testBuildASTVariableVariableReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTVariableVariable::class,
            $this->createBuilder()->buildAstVariableVariable('$$x')
        );
    }

    /**
     * testBuildASTCompoundVariableReturnsExpectedType
     */
    public function testBuildASTCompoundVariableReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTCompoundVariable::class,
            $this->createBuilder()->buildAstCompoundVariable('${x}')
        );
    }

    /**
     * testBuildASTFieldDeclarationReturnsExpectedType
     */
    public function testBuildASTFieldDeclarationReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTFieldDeclaration::class,
            $this->createBuilder()->buildAstFieldDeclaration()
        );
    }

    /**
     * testBuildASTConstantReturnsExpectedType
     */
    public function testBuildASTConstantReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTConstant::class,
            $this->createBuilder()->buildAstConstant('X')
        );
    }

    /**
     * testBuildASTConstantDeclaratorReturnsExpectedType
     */
    public function testBuildASTConstantDeclaratorReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTConstantDeclarator::class,
            $this->createBuilder()->buildAstConstantDeclarator('X')
        );
    }

    /**
     * testBuildASTConstantDefinitionReturnsExpectedType
     */
    public function testBuildASTConstantDefinitionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTConstantDefinition::class,
            $this->createBuilder()->buildAstConstantDefinition('X')
        );
    }

    /**
     * testBuildASTConstantPostfixReturnsExpectedType
     */
    public function testBuildASTConstantPostfixReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTConstantPostfix::class,
            $this->createBuilder()->buildAstConstantPostfix('X')
        );
    }

    /**
     * testBuildASTClassFqnPostfixReturnsExpectedType
     */
    public function testBuildASTClassFqnPostfixReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTClassFqnPostfix::class,
            $this->createBuilder()->buildAstClassFqnPostfix()
        );
    }

    /**
     * testBuildASTAssignmentExpressionReturnsExpectedType
     */
    public function testBuildASTAssignmentExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTAssignmentExpression::class,
            $this->createBuilder()->buildAstAssignmentExpression('=')
        );
    }

    /**
     * testBuildASTShiftLeftExpressionReturnsExpectedType
     *
     * @since 1.0.1
     */
    public function testBuildASTShiftLeftExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTShiftLeftExpression::class,
            $this->createBuilder()->buildAstShiftLeftExpression()
        );
    }

    /**
     * testBuildASTShiftRightExpressionReturnsExpectedType
     *
     * @since 1.0.1
     */
    public function testBuildASTShiftRightExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTShiftRightExpression::class,
            $this->createBuilder()->buildAstShiftRightExpression()
        );
    }

    /**
     * testBuildASTBooleanAndExpressionReturnsExpectedType
     */
    public function testBuildASTBooleanAndExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTBooleanAndExpression::class,
            $this->createBuilder()->buildAstBooleanAndExpression()
        );
    }

    /**
     * testBuildASTBooleanOrExpressionReturnsExpectedType
     */
    public function testBuildASTBooleanOrExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTBooleanOrExpression::class,
            $this->createBuilder()->buildAstBooleanOrExpression()
        );
    }

    /**
     * testBuildASTCastExpressionReturnsExpectedType
     */
    public function testBuildASTCastExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTCastExpression::class,
            $this->createBuilder()->buildAstCastExpression('(boolean)')
        );
    }

    /**
     * testBuildASTCloneExpressionReturnsExpectedType
     */
    public function testBuildASTCloneExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTCloneExpression::class,
            $this->createBuilder()->buildAstCloneExpression('clone')
        );
    }

    /**
     * testBuildASTCompoundExpressionReturnsExpectedType
     */
    public function testBuildASTCompoundExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTCompoundExpression::class,
            $this->createBuilder()->buildAstCompoundExpression()
        );
    }

    /**
     * testBuildASTConditionalExpressionReturnsExpectedType
     */
    public function testBuildASTConditionalExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTConditionalExpression::class,
            $this->createBuilder()->buildAstConditionalExpression()
        );
    }

    /**
     * testBuildASTEvalExpressionReturnsExpectedType
     */
    public function testBuildASTEvalExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTEvalExpression::class,
            $this->createBuilder()->buildAstEvalExpression('eval')
        );
    }

    /**
     * testBuildASTExitExpressionReturnsExpectedType
     */
    public function testBuildASTExitExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTExitExpression::class,
            $this->createBuilder()->buildAstExitExpression('exit')
        );
    }

    /**
     * testBuildASTExpressionReturnsExpectedType
     */
    public function testBuildASTExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTExpression::class,
            $this->createBuilder()->buildAstExpression()
        );
    }

    /**
     * testBuildASTIncludeExpressionReturnsExpectedType
     */
    public function testBuildASTIncludeExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTIncludeExpression::class,
            $this->createBuilder()->buildAstIncludeExpression()
        );
    }

    /**
     * testBuildASTInstanceOfExpressionReturnsExpectedType
     */
    public function testBuildASTInstanceOfExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTInstanceOfExpression::class,
            $this->createBuilder()->buildAstInstanceOfExpression('instanceof')
        );
    }

    /**
     * testBuildASTIssetExpressionReturnsExpectedType
     */
    public function testBuildASTIssetExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTIssetExpression::class,
            $this->createBuilder()->buildAstIssetExpression()
        );
    }

    /**
     * testBuildASTListExpressionReturnsExpectedType
     */
    public function testBuildASTListExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTListExpression::class,
            $this->createBuilder()->buildAstListExpression('list')
        );
    }

    /**
     * testBuildASTLogicalAndExpressionReturnsExpectedType
     */
    public function testBuildASTLogicalAndExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTLogicalAndExpression::class,
            $this->createBuilder()->buildAstLogicalAndExpression()
        );
    }

    /**
     * testBuildASTLogicalOrExpressionReturnsExpectedType
     */
    public function testBuildASTLogicalOrExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTLogicalOrExpression::class,
            $this->createBuilder()->buildAstLogicalOrExpression()
        );
    }

    /**
     * testBuildASTLogicalXorExpressionReturnsExpectedType
     */
    public function testBuildASTLogicalXorExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTLogicalXorExpression::class,
            $this->createBuilder()->buildAstLogicalXorExpression()
        );
    }

    /**
     * testBuildASTRequireExpressionReturnsExpectedType
     */
    public function testBuildASTRequireExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTRequireExpression::class,
            $this->createBuilder()->buildAstRequireExpression()
        );
    }

    /**
     * testBuildASTStringIndexExpressionReturnsExpectedType
     */
    public function testBuildASTStringIndexExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTStringIndexExpression::class,
            $this->createBuilder()->buildAstStringIndexExpression()
        );
    }

    /**
     * testBuildASTUnaryExpressionReturnsExpectedType
     */
    public function testBuildASTUnaryExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTUnaryExpression::class,
            $this->createBuilder()->buildAstUnaryExpression('+')
        );
    }

    /**
     * testBuildASTBreakStatementReturnsExpectedType
     */
    public function testBuildASTBreakStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTBreakStatement::class,
            $this->createBuilder()->buildAstBreakStatement('break')
        );
    }

    /**
     * testBuildASTCatchStatementReturnsExpectedType
     */
    public function testBuildASTCatchStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTCatchStatement::class,
            $this->createBuilder()->buildAstCatchStatement('catch')
        );
    }

    /**
     * testBuildASTFinallyStatementReturnsExpectedType
     */
    public function testBuildASTFinallyStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTFinallyStatement::class,
            $this->createBuilder()->buildAstFinallyStatement()
        );
    }

    /**
     * testBuildASTDeclareStatementReturnsExpectedType
     */
    public function testBuildASTDeclareStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTDeclareStatement::class,
            $this->createBuilder()->buildAstDeclareStatement()
        );
    }

    /**
     * testBuildASTIfStatementReturnsExpectedType
     */
    public function testBuildASTIfStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTIfStatement::class,
            $this->createBuilder()->buildAstIfStatement('if')
        );
    }

    /**
     * testBuildASTElseIfStatementReturnsExpectedType
     */
    public function testBuildASTElseIfStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTElseIfStatement::class,
            $this->createBuilder()->buildAstElseIfStatement('elseif')
        );
    }

    /**
     * testBuildASTContinueStatementReturnsExpectedType
     */
    public function testBuildASTContinueStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTContinueStatement::class,
            $this->createBuilder()->buildAstContinueStatement('continue')
        );
    }

    /**
     * testBuildASTDoWhileStatementReturnsExpectedType
     */
    public function testBuildASTDoWhileStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTDoWhileStatement::class,
            $this->createBuilder()->buildAstDoWhileStatement('while')
        );
    }

    /**
     * testBuildASTForStatementReturnsExpectedType
     */
    public function testBuildASTForStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTForStatement::class,
            $this->createBuilder()->buildAstForStatement('for')
        );
    }

    /**
     * testBuildASTForInitReturnsExpectedType
     */
    public function testBuildASTForInitReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTForInit::class,
            $this->createBuilder()->buildAstForInit()
        );
    }

    /**
     * testBuildASTForUpdateReturnsExpectedType
     */
    public function testBuildASTForUpdateReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTForUpdate::class,
            $this->createBuilder()->buildAstForUpdate()
        );
    }

    /**
     * testBuildASTForeachStatementReturnsExpectedType
     */
    public function testBuildASTForeachStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTForeachStatement::class,
            $this->createBuilder()->buildAstForeachStatement('foreach')
        );
    }

    /**
     * testBuildASTFormalParametersReturnsExpectedType
     */
    public function testBuildASTFormalParametersReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTFormalParameters::class,
            $this->createBuilder()->buildAstFormalParameters()
        );
    }

    /**
     * testBuildASTFormalParameterReturnsExpectedType
     */
    public function testBuildASTFormalParameterReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTFormalParameter::class,
            $this->createBuilder()->buildAstFormalParameter()
        );
    }

    /**
     * testBuildASTGlobalStatementReturnsExpectedType
     */
    public function testBuildASTGlobalStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTGlobalStatement::class,
            $this->createBuilder()->buildAstGlobalStatement()
        );
    }

    /**
     * testBuildASTGotoStatementReturnsExpectedType
     */
    public function testBuildASTGotoStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTGotoStatement::class,
            $this->createBuilder()->buildAstGotoStatement('goto')
        );
    }

    /**
     * testBuildASTLabelStatementReturnsExpectedType
     */
    public function testBuildASTLabelStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTLabelStatement::class,
            $this->createBuilder()->buildAstLabelStatement('LABEL')
        );
    }

    /**
     * testBuildASTReturnStatementReturnsExpectedType
     */
    public function testBuildASTReturnStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTReturnStatement::class,
            $this->createBuilder()->buildAstReturnStatement('return')
        );
    }

    /**
     * testBuildASTScopeStatementReturnsExpectedType
     */
    public function testBuildASTScopeStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTScopeStatement::class,
            $this->createBuilder()->buildAstScopeStatement()
        );
    }

    /**
     * testBuildASTStatementReturnsExpectedType
     */
    public function testBuildASTStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTStatement::class,
            $this->createBuilder()->buildAstStatement()
        );
    }

    /**
     * testBuildASTTraitUseStatementReturnsExpectedType
     *
     * @since 1.0.0
     */
    public function testBuildASTTraitUseStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTTraitUseStatement::class,
            $this->createBuilder()->buildAstTraitUseStatement()
        );
    }

    /**
     * testBuildASTTraitAdaptationReturnsExpectedType
     *
     * @since 1.0.0
     */
    public function testBuildASTTraitAdaptationReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTTraitAdaptation::class,
            $this->createBuilder()->buildAstTraitAdaptation()
        );
    }

    /**
     * testBuildASTTraitAdaptationAliasReturnsExpectedType
     *
     * @since 1.0.0
     */
    public function testBuildASTTraitAdaptationAliasReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTTraitAdaptationAlias::class,
            $this->createBuilder()->buildAstTraitAdaptationAlias(__CLASS__)
        );
    }

    /**
     * testBuildASTTraitAdaptationPrecedenceReturnsExpectedType
     *
     * @since 1.0.0
     */
    public function testBuildASTTraitAdaptationPrecedenceReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTTraitAdaptationPrecedence::class,
            $this->createBuilder()->buildAstTraitAdaptationPrecedence(__CLASS__)
        );
    }

    /**
     * testBuildASTSwitchStatementReturnsExpectedType
     */
    public function testBuildASTSwitchStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTSwitchStatement::class,
            $this->createBuilder()->buildAstSwitchStatement()
        );
    }

    /**
     * testBuildASTThrowStatementReturnsExpectedType
     */
    public function testBuildASTThrowStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTThrowStatement::class,
            $this->createBuilder()->buildAstThrowStatement('throw')
        );
    }

    /**
     * testBuildASTTryStatementReturnsExpectedType
     */
    public function testBuildASTTryStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTTryStatement::class,
            $this->createBuilder()->buildAstTryStatement('try')
        );
    }

    /**
     * testBuildASTUnsetStatementReturnsExpectedType
     */
    public function testBuildASTUnsetStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTUnsetStatement::class,
            $this->createBuilder()->buildAstUnsetStatement()
        );
    }

    /**
     * testBuildASTWhileStatementReturnsExpectedType
     */
    public function testBuildASTWhileStatementReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTWhileStatement::class,
            $this->createBuilder()->buildAstWhileStatement('while')
        );
    }

    /**
     * testBuildASTArrayIndexExpressionReturnsExpectedType
     */
    public function testBuildASTArrayIndexExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTArrayIndexExpression::class,
            $this->createBuilder()->buildAstArrayIndexExpression()
        );
    }

    /**
     * testBuildASTClosureReturnsExpectedType
     */
    public function testBuildASTClosureReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTClosure::class,
            $this->createBuilder()->buildAstClosure()
        );
    }

    /**
     * testBuildASTParentReferenceReturnsExpectedType
     */
    public function testBuildASTParentReferenceReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTParentReference::class,
            $this->createBuilder()->buildAstParentReference(
                $this->createBuilder()->buildAstClassOrInterfaceReference(__CLASS__)
            )
        );
    }

    /**
     * testBuildASTSelfReferenceReturnsExpectedType
     */
    public function testBuildASTSelfReferenceReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTSelfReference::class,
            $this->createBuilder()->buildAstSelfReference(
                $this->createBuilder()->buildClass(__CLASS__)
            )
        );
    }

    /**
     * testBuildASTStaticReferenceReturnsExpectedType
     */
    public function testBuildASTStaticReferenceReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTStaticReference::class,
            $this->createBuilder()->buildAstStaticReference(
                $this->createBuilder()->buildClass(__CLASS__)
            )
        );
    }

    /**
     * testBuildASTTraitReferenceReturnsExpectedType
     *
     * @since 1.0.0
     */
    public function testBuildASTTraitReferenceReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTTraitReference::class,
            $this->createBuilder()->buildAstTraitReference(__CLASS__)
        );
    }

    /**
     * testBuildASTClassReferenceReturnsExpectedType
     */
    public function testBuildASTClassOrInterfaceReferenceReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTClassOrInterfaceReference::class,
            $this->createBuilder()->buildAstClassOrInterfaceReference(__CLASS__)
        );
    }

    /**
     * testBuildASTClassReferenceReturnsExpectedType
     */
    public function testBuildASTClassReferenceReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTClassReference::class,
            $this->createBuilder()->buildAstClassReference(__CLASS__)
        );
    }

    /**
     * testBuildASTScalarTypeReturnsInstanceOfExpectedType
     */
    public function testBuildASTScalarTypeReturnsInstanceOfExpectedType(): void
    {
        $builder = $this->createBuilder();
        $instance = $builder->buildAstScalarType(__FUNCTION__);

        static::assertInstanceOf(ASTScalarType::class, $instance);
    }

    /**
     * testBuildASTAllocationExpressionReturnsExpectedType
     */
    public function testBuildASTAllocationExpressionReturnsExpectedType(): void
    {
        $object = $this->createBuilder()
            ->buildAstAllocationExpression(__FUNCTION__);

        static::assertInstanceOf(
            ASTAllocationExpression::class,
            $object
        );
    }

    /**
     * testBuildASTArgumentsReturnsExpectedType
     */
    public function testBuildASTArgumentsReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTArguments::class,
            $this->createBuilder()->buildAstArguments()
        );
    }

    /**
     * testBuildASTSwitchLabel
     */
    public function testBuildASTSwitchLabel(): void
    {
        static::assertInstanceOf(
            ASTSwitchLabel::class,
            $this->createBuilder()->buildAstSwitchLabel('m')
        );
    }

    /**
     * testBuildASTEchoStatetmentReturnsExpectedType
     */
    public function testBuildASTEchoStatetmentReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTEchoStatement::class,
            $this->createBuilder()->buildAstEchoStatement('echo')
        );
    }

    /**
     * testBuildASTVariableDeclaratorReturnsExpectedType
     */
    public function testBuildASTVariableDeclaratorReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTVariableDeclarator::class,
            $this->createBuilder()->buildAstVariableDeclarator('foo')
        );
    }

    /**
     * testBuildASTStaticVariableDeclarationReturnsExpectedType
     */
    public function testBuildASTStaticVariableDeclarationReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTStaticVariableDeclaration::class,
            $this->createBuilder()->buildAstStaticVariableDeclaration('$foo')
        );
    }

    /**
     * testBuildASTPostfixExpressionReturnsExpectedType
     */
    public function testBuildASTPostfixExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTPostfixExpression::class,
            $this->createBuilder()->buildAstPostfixExpression('++')
        );
    }

    /**
     * testBuildASTPreDecrementExpressionReturnsExpectedType
     */
    public function testBuildASTPreDecrementExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTPreDecrementExpression::class,
            $this->createBuilder()->buildAstPreDecrementExpression()
        );
    }

    /**
     * testBuildASTPreIncrementExpressionReturnsExpectedType
     */
    public function testBuildASTPreIncrementExpressionReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTPreIncrementExpression::class,
            $this->createBuilder()->buildAstPreIncrementExpression()
        );
    }

    /**
     * testBuildASTMemberPrimaryPrefixReturnsExpectedType
     *
     * @since 1.0.0
     */
    public function testBuildASTMemberPrimaryPrefixReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTMemberPrimaryPrefix::class,
            $this->createBuilder()->buildAstMemberPrimaryPrefix('::')
        );
    }

    /**
     * testBuildASTMethodPostfixReturnsExpectedType
     *
     * @since 1.0.0
     */
    public function testBuildASTMethodPostfixReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTMethodPostfix::class,
            $this->createBuilder()->buildAstMethodPostfix('foo')
        );
    }

    /**
     * testBuildASTFunctionPostfixReturnsExpectedType
     */
    public function testBuildASTFunctionPostfixReturnsExpectedType(): void
    {
        static::assertInstanceOf(
            ASTFunctionPostfix::class,
            $this->createBuilder()->buildAstFunctionPostfix('foo')
        );
    }

    /**
     * Creates a clean builder test instance.
     */
    protected function createBuilder(): PHPBuilder
    {
        $builder = new PHPBuilder();
        $builder->setCache($this->createCacheFixture());

        return $builder;
    }
}
