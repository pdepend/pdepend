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
  */

namespace PDepend\Source\Language\PHP;

use PDepend\AbstractTest;
use PDepend\Source\AST\ASTClassFqnPostfix;
use PDepend\Source\AST\ASTComment;
use PDepend\Source\AST\ASTConstantPostfix;
use PDepend\Source\AST\ASTFunction;

/**
 * Test case implementation for the default node builder implementation.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\PHPBuilder
 * @group unittest
 */
class PHPBuilderTest extends AbstractTest
{
    /**
     * testBuilderAddsMultiplePackagesForClassesToListOfPackages
     *
     * @return void
     */
    public function testBuilderAddsMultiplePackagesForClassesToListOfPackages()
    {
        $builder = $this->createBuilder();

        $namespace = $builder->buildNamespace(__FUNCTION__);
        $namespace->addType($builder->buildClass(__FUNCTION__));

        $namespace = $builder->buildNamespace(__CLASS__);
        $namespace->addType($builder->buildClass(__CLASS__));

        $this->assertEquals(2, count($builder->getNamespaces()));
    }

    /**
     * testBuilderAddsMultiplePackagesForFunctionsToListOfPackages
     *
     * @return void
     */
    public function testBuilderAddsMultiplePackagesForFunctionsToListOfPackages()
    {
        $builder = $this->createBuilder();

        $builder->buildNamespace(__FUNCTION__);
        $builder->buildFunction(__FUNCTION__);

        $builder->buildNamespace(__CLASS__);
        $builder->buildFunction(__CLASS__);

        $this->assertEquals(2, count($builder->getNamespaces()));
    }

    /**
     * testBuilderNotAddsNewPackagesOnceItHasReturnedTheListOfPackages
     *
     * @return void
     */
    public function testBuilderNotAddsNewPackagesOnceItHasReturnedTheListOfPackages()
    {
        $builder = $this->createBuilder();

        $namespace = $builder->buildNamespace(__FUNCTION__);
        $namespace->addFunction($builder->buildFunction(__FUNCTION__));

        $builder->getNamespaces();

        $namespace = $builder->buildNamespace(__CLASS__);
        $namespace->addType($builder->buildClass(__CLASS__));

        $this->assertEquals(1, $builder->getNamespaces()->count());
    }

    /**
     * testRestoreFunctionAddsFunctionToPackage
     *
     * @return void
     */
    public function testRestoreFunctionAddsFunctionToPackage()
    {
        $builder = $this->createBuilder();
        $namespace = $builder->buildNamespace(__CLASS__);

        $function = new ASTFunction(__FUNCTION__);
        $function->setNamespace($namespace);

        $builder->restoreFunction($function);
        $this->assertEquals(1, count($namespace->getFunctions()));
    }

    /**
     * testRestoreFunctionUsesGetNamespaceNameMethod
     *
     * @return void
     */
    public function testRestoreFunctionUsesGetNamespaceNameMethod()
    {
        $function = $this->getMock(
            'PDepend\\Source\\AST\\ASTFunction',
            array(),
            array(__FUNCTION__)
        );
        $function->expects($this->once())
            ->method('getNamespaceName');

        $builder = $this->createBuilder();
        $builder->restoreFunction($function);
    }

    /**
     * testBuildTraitWithSameQualifiedNameUnique
     *
     * @return void
     * @since 1.0.0
     */
    public function testBuildTraitWithSameQualifiedNameUnique()
    {
        $builder = $this->createBuilder();

        $trait = $builder->buildTrait(__FUNCTION__);
        $trait->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreTrait($trait);

        $this->assertSame($trait, $builder->getTrait(__FUNCTION__));
    }

    /**
     * testGetTraitReturnsDummyIfNoMatchingTraitExists
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetTraitReturnsDummyIfNoMatchingTraitExists()
    {
        $builder = $this->createBuilder();
        $this->assertEquals(__FUNCTION__, $builder->getTrait(__FUNCTION__)->getName());
    }

    /**
     * Tests that the {@link \PDepend\Source\Language\PHP\PHPBuilder::buildTrait()}
     * method creates two different trait instances for the same class name, but
     * different packages.
     *
     * @return void
     */
    public function testBuildTraitCreatesTwoDifferentInstancesForDifferentPackages()
    {
        $builder = $this->createBuilder();

        $trait1 = $builder->buildTrait('PDepend1\Parser');
        $trait2 = $builder->buildTrait('PDepend2\Parser');

        $this->assertNotSame($trait1, $trait2);
    }

    /**
     * Tests that {@link \PDepend\Source\Language\PHP\PHPBuilder::buildTrait()} returns
     * a previous trait instance for a specified package, if it is called for a
     * same named trait in the default package.
     *
     * @return void
     */
    public function testBuildTraitReusesExistingNonDefaultPackageInstanceForDefaultPackage()
    {
        $builder = $this->createBuilder();

        $trait = $builder->buildTrait('PDepend\Parser');
        $trait->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreTrait($trait);

        $this->assertSame(
            $trait->getNamespace(),
            $builder->getTrait('Parser')->getNamespace()
        );
    }

    /**
     * Tests that the node builder creates a class for the same name only once.
     *
     * @return void
     */
    public function testBuildClassUnique()
    {
        $builder = $this->createBuilder();

        $class = $builder->buildClass(__FUNCTION__);
        $class->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreClass($class);

        $this->assertSame($class, $builder->getClass(__FUNCTION__));
    }

    /**
     * testGetClassReturnsDummyIfNoMatchingTraitExists
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetClassReturnsDummyIfNoMatchingClassExists()
    {
        $builder = $this->createBuilder();
        $this->assertEquals(
            __FUNCTION__,
            $builder->getClass(__FUNCTION__)->getName()
        );
    }

    /**
     * Tests that the {@link \PDepend\Source\Language\PHP\PHPBuilder::buildClass()} method
     * creates two different class instances for the same class name, but
     * different packages.
     *
     * @return void
     */
    public function testBuildClassCreatesTwoDifferentInstancesForDifferentPackages()
    {
        $builder = $this->createBuilder();

        $class1 = $builder->buildClass('PDepend1\Parser');
        $class2 = $builder->buildClass('PDepend2\Parser');

        $this->assertNotSame($class1, $class2);
    }

    /**
     * Tests that {@link \PDepend\Source\Language\PHP\PHPBuilder::buildClass()}
     * returns a previous class instance for a specified package, if it is called
     * for a same named class in the default package.
     *
     * @return void
     */
    public function testBuildClassReusesExistingNonDefaultPackageInstanceForDefaultPackage()
    {
        $builder = $this->createBuilder();

        $class1 = $builder->buildClass('PDepend\Parser');
        $class1->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreClass($class1);

        $this->assertSame(
            $class1->getNamespace(),
            $builder->getClass('Parser')->getNamespace()
        );
    }

    /**
     * Tests that the node build generates an unique interface instance for the
     * same identifier.
     *
     * @return void
     */
    public function testBuildInterfaceUnique()
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface(__FUNCTION__);
        $interface->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreInterface($interface);

        $this->assertSame($interface, $builder->getInterface(__FUNCTION__));
    }

    /**
     * testGetInterfaceReturnsDummyIfNoMatchingInterfaceExists
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetInterfaceReturnsDummyIfNoMatchingInterfaceExists()
    {
        $builder = $this->createBuilder();
        $this->assertEquals(
            __FUNCTION__,
            $builder->getInterface(__FUNCTION__)->getName()
        );
    }

    /**
     * Tests that the {@link \PDepend\Source\Language\PHP\PHPBuilder::buildInterface()}
     * method only removes/replaces a previously created class instance, when
     * this class is part of the default namespace. Otherwise there are two user
     * types with the same local or package internal name.
     *
     * @return void
     */
    public function testBuildInterfaceDoesntRemoveClassForSameNamedInterface()
    {
        $builder = $this->createBuilder();

        $namespace1 = $builder->buildNamespace('package1');
        $namespace2 = $builder->buildNamespace('package2');

        $class = $builder->buildClass('Parser');
        $namespace1->addType($class);

        $this->assertEquals(1, $namespace1->getTypes()->count());

        $builder->buildInterface('Parser');

        $this->assertEquals(1, $namespace1->getTypes()->count());
    }

    /**
     * Tests that {@link \PDepend\Source\Language\PHP\PHPBuilder::buildInterface()}
     * creates different interface instances for different parent packages.
     *
     * @return void
     */
    public function testBuildInterfacesCreatesDifferentInstancesForDifferentPackages()
    {
        $builder = $this->createBuilder();

        $interfaces1 = $builder->buildInterface('PDepend1\ParserI');
        $interfaces2 = $builder->buildInterface('PDepend2\ParserI');

        $this->assertNotSame($interfaces1, $interfaces2);
    }

    /**
     * Tests that {@link \PDepend\Source\Language\PHP\PHPBuilder::buildInterface()}
     * replaces an existing default package interface instance, if it creates a
     * more specific version.
     *
     * @return void
     */
    public function testCanCreateMultipleInterfaceInstancesWithIdenticalNames()
    {
        $builder = $this->createBuilder();

        $interface1 = $builder->buildInterface('PDepend\ParserI');
        $interface2 = $builder->buildInterface('PDepend\ParserI');

        $this->assertNotSame($interface1, $interface2);
        $this->assertSame(
            $interface1->getNamespace(),
            $interface2->getNamespace()
        );
    }

    /**
     * Tests that {@link \PDepend\Source\Language\PHP\PHPBuilder::buildInterface()}
     * returns a previous interface instance for a specified package, if it is called
     * for a same named interface in the default package.
     *
     * @return void
     */
    public function testBuildInterfaceReusesExistingNonDefaultPackageInstanceForDefaultPackage()
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface('PDepend\ParserI');
        $interface->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreInterface($interface);

        $this->assertSame($builder->getInterface('ParserI'), $interface);
        $this->assertSame(
            $builder->getInterface('ParserI')->getNamespace(),
            $interface->getNamespace()
        );
    }

    /**
     * Tests the 'PDepend\\Source\\AST\\ASTMethod build method.
     *
     * @return void
     */
    public function testBuildMethod()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTMethod',
            $this->createBuilder()->buildMethod('method')
        );
    }

    /**
     * Tests that the node builder creates a package for the same name only once.
     *
     * @return void
     */
    public function testBuildPackageUnique()
    {
        $builder  = $this->createBuilder();
        $namespace1 = $builder->buildNamespace('package1');
        $namespace2 = $builder->buildNamespace('package1');

        $this->assertSame($namespace1, $namespace2);
    }

    /**
     * Tests the implemented {@link IteratorAggregate}.
     *
     * @return void
     */
    public function testGetIteratorWithPackages()
    {
        $builder = $this->createBuilder();

        $expected = array(
            'package1'  =>  $builder->buildNamespace('package1'),
            'package2'  =>  $builder->buildNamespace('package2'),
            'package3'  =>  $builder->buildNamespace('package3')
        );

        $actual = array();
        foreach ($builder as $name => $namespace) {
            $actual[$name] = $namespace;
        }

        $this->assertSame($expected, $actual);
    }

    /**
     * Tests the {@link \PDepend\Source\Language\PHP\PHPBuilder::getNamespaces()}
     * method.
     *
     * @return void
     */
    public function testGetNamespaces()
    {
        $builder = $this->createBuilder();

        $expected = array(
            'package1'  =>  $builder->buildNamespace('package1'),
            'package2'  =>  $builder->buildNamespace('package2'),
            'package3'  =>  $builder->buildNamespace('package3')
        );

        $actual = array();
        foreach ($builder->getNamespaces() as $name => $namespace) {
            $actual[$name] = $namespace;
        }

        $this->assertSame($expected, $actual);
    }

    /**
     * There was a missing check within an if statement, so that the builder
     * has alway overwritten previously created instances.
     *
     * @return void
     */
    public function testBuildClassDoesNotOverwritePreviousInstances()
    {
        $builder = $this->createBuilder();

        $class0 = $builder->buildClass('FooBar');
        $class0->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreClass($class0);

        $class1 = $builder->buildClass('FooBar');
        $class1->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreClass($class1);

        $this->assertNotSame($class0, $class1);
        $this->assertSame($class0, $builder->getClass('FooBar'));
    }

    /**
     * There was a missing check within an if statement, so that the builder
     * has alway overwritten previously created instances.
     *
     * @return void
     */
    public function testBuildInterfaceDoesNotOverwritePreviousInstances()
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface('FooBar');
        $interface->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreInterface($interface);

        $this->assertNotSame($interface, $builder->buildInterface('FooBar'));
        $this->assertSame($interface, $builder->getInterface('FooBar'));
    }

    /**
     * Tests that the node builder works case insensitive for class names.
     *
     * @return void
     */
    public function testBuildClassWorksCaseInsensitiveIssue26()
    {
        $builder = $this->createBuilder();

        $class = $builder->buildClass('PDepend_Parser');
        $class->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreClass($class);

        $this->assertSame($class, $builder->getClass('pDepend_parser'));
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     */
    public function testBuildInterfaceWorksCaseInsensitiveIssue26()
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface('PDepend_Source_Tokenizer_Tokenizer');
        $interface->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreInterface($interface);

        $this->assertSame(
            $interface,
            $builder->getInterface('PDepend_Source_Tokenizer_ToKeNiZeR')
        );
    }

    /**
     * testGetClassOrInterfaceReturnsDummyIfNoMatchingTypeExists
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetClassOrInterfaceReturnsDummyIfNoMatchingTypeExists()
    {
        $builder = $this->createBuilder();
        $this->assertEquals(
            __FUNCTION__,
            $builder->getClassOrInterface(__FUNCTION__)->getName()
        );
    }

    /**
     * testGetClassOrInterfaceReturnsClassInExtensionPackage
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetClassOrInterfaceReturnsClassInExtensionPackage()
    {
        $builder = $this->createBuilder();
        $this->assertEquals(
            '+reflection',
            $builder->getClassOrInterface('Reflection')->getNamespace()->getName()
        );
    }

    /**
     * testGetClassOrInterfaceStripsLeadingBackslashFromClass
     *
     * @return void
     * @since 1.0.0
     */
    public function testGetClassOrInterfaceStripsLeadingBackslashFromClass()
    {
        $builder = $this->createBuilder();
        $this->assertEquals(
            'foo\bar',
            $builder->getClassOrInterface('\foo\bar\Baz')->getNamespace()->getName()
        );
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive1Issue26()
    {
        $builder = $this->createBuilder();

        $interface = $builder->buildInterface('PDepend_Source_Tokenizer_Tokenizer');
        $interface->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreInterface($interface);

        $this->assertSame(
            $interface,
            $builder->getClassOrInterface('PDepend_Source_Tokenizer_ToKeNiZeR')
        );
    }

    /**
     * Tests that the node builder works case insensitive for interface names.
     *
     * @return void
     */
    public function testBuildClassOrInterfaceWorksCaseInsensitive2Issue26()
    {
        $builder = $this->createBuilder();

        $class = $builder->buildClass('PDepend_Parser');
        $class->setNamespace($builder->buildNamespace(__FUNCTION__));

        $builder->restoreClass($class);

        $this->assertSame($class, $builder->getClassOrInterface('pDepend_parser'));
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildASTClassOrInterfaceReferenceThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
        $builder->buildAstClassOrInterfaceReference('Foo');

        // Freeze object
        $builder->getClass('Foo');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildAstClassOrInterfaceReference('Bar');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildClassThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
        $builder->buildClass('Foo');

        // Freeze object
        $builder->getClass('Foo');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildClass('Bar');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildASTClassReferenceThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
        $builder->buildAstClassReference('Foo');

        // Freeze object
        $builder->getClass('Foo');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildAstClassReference('Bar');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildInterfaceThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
        $builder->buildInterface('Inter');

        // Freeze object
        $builder->getInterface('Inter');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildInterface('Face');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildMethodThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
        $builder->buildMethod('call');

        // Freeze object
        $builder->getInterface('Inter');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildMethod('invoke');
    }

    /**
     * Tests that the builder throws the expected exception when some one tries
     * to build a new node, when the internal state flag is frozen.
     *
     * @return void
     */
    public function testBuildFunctionThrowsExpectedExceptionWhenStateIsFrozen()
    {
        $builder = $this->createBuilder();
        $builder->buildFunction('func');

        // Freeze object
        $builder->getInterface('Inter');

        $this->setExpectedException(
            'BadMethodCallException',
            'Cannot create new nodes, when internal state is frozen.'
        );

        $builder->buildFunction('prop');
    }

    /**
     * testBuildASTCommentReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTCommentReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTComment',
            $this->createBuilder()->buildAstComment('// Hello')
        );
    }

    /**
     * testBuildASTPrimitiveTypeReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTPrimitiveTypeReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTPrimitiveType',
            $this->createBuilder()->buildAstPrimitiveType('1')
        );
    }

    /**
     * testBuildASTTypeArrayReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTTypeArrayReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTypeArray',
            $this->createBuilder()->buildAstTypeArray()
        );
    }

    /**
     * testBuildASTTypeCallableReturnsExpectedType
     *
     * @return void
     * @since 1.0.0
     */
    public function testBuildASTTypeCallableReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTypeCallable',
            $this->createBuilder()->buildAstTypeCallable()
        );
    }

    /**
     * testBuildASTHeredocReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTHeredocReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTHeredoc',
            $this->createBuilder()->buildAstHeredoc()
        );
    }

    /**
     * testBuildASTIdentifierReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTIdentifierReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTIdentifier',
            $this->createBuilder()->buildAstIdentifier('ID')
        );
    }

    /**
     * testBuildASTLiteralReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTLiteralReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTLiteral',
            $this->createBuilder()->buildAstLiteral('false')
        );
    }

    /**
     * testBuildASTStringReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTStringReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTString',
            $this->createBuilder()->buildAstString()
        );
    }

    /**
     * testBuildASTArrayReturnsExpectedType
     *
     * @return void
     * @since 1.0.0
     */
    public function testBuildASTArrayReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTArray',
            $this->createBuilder()->buildAstArray()
        );
    }

    /**
     * testBuildASTArrayElementReturnsExpectedType
     *
     * @return void
     * @since 1.0.0
     */
    public function testBuildASTArrayElementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTArrayElement',
            $this->createBuilder()->buildAstArrayElement()
        );
    }

    /**
     * testBuildASTScopeReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTScopeReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTScope',
            $this->createBuilder()->buildAstScope()
        );
    }

    /**
     * testBuildASTVariableReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTVariableReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTVariable',
            $this->createBuilder()->buildAstVariable('$name')
        );
    }

    /**
     * testBuildASTVariableVariableReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTVariableVariableReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTVariableVariable',
            $this->createBuilder()->buildAstVariableVariable('$$x')
        );
    }

    /**
     * testBuildASTCompoundVariableReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTCompoundVariableReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTCompoundVariable',
            $this->createBuilder()->buildAstCompoundVariable('${x}')
        );
    }

    /**
     * testBuildASTFieldDeclarationReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTFieldDeclarationReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTFieldDeclaration',
            $this->createBuilder()->buildAstFieldDeclaration()
        );
    }

    /**
     * testBuildASTConstantReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTConstantReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTConstant',
            $this->createBuilder()->buildAstConstant('X')
        );
    }

    /**
     * testBuildASTConstantDeclaratorReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTConstantDeclaratorReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTConstantDeclarator',
            $this->createBuilder()->buildAstConstantDeclarator('X')
        );
    }

    /**
     * testBuildASTConstantDefinitionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTConstantDefinitionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTConstantDefinition',
            $this->createBuilder()->buildAstConstantDefinition('X')
        );
    }

    /**
     * testBuildASTConstantPostfixReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTConstantPostfixReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTConstantPostfix',
            $this->createBuilder()->buildAstConstantPostfix('X')
        );
    }

    /**
     * testBuildASTClassFqnPostfixReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTClassFqnPostfixReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTClassFqnPostfix',
            $this->createBuilder()->buildAstClassFqnPostfix()
        );
    }

    /**
     * testBuildASTAssignmentExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTAssignmentExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTAssignmentExpression',
            $this->createBuilder()->buildAstAssignmentExpression('=')
        );
    }

    /**
     * testBuildASTShiftLeftExpressionReturnsExpectedType
     *
     * @return void
     * @since 1.0.1
     */
    public function testBuildASTShiftLeftExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTShiftLeftExpression',
            $this->createBuilder()->buildAstShiftLeftExpression()
        );
    }

    /**
     * testBuildASTShiftRightExpressionReturnsExpectedType
     *
     * @return void
     * @since 1.0.1
     */
    public function testBuildASTShiftRightExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTShiftRightExpression',
            $this->createBuilder()->buildAstShiftRightExpression()
        );
    }

    /**
     * testBuildASTBooleanAndExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTBooleanAndExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTBooleanAndExpression',
            $this->createBuilder()->buildAstBooleanAndExpression()
        );
    }

    /**
     * testBuildASTBooleanOrExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTBooleanOrExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTBooleanOrExpression',
            $this->createBuilder()->buildAstBooleanOrExpression()
        );
    }

    /**
     * testBuildASTCastExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTCastExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTCastExpression',
            $this->createBuilder()->buildAstCastExpression('(boolean)')
        );
    }

    /**
     * testBuildASTCloneExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTCloneExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTCloneExpression',
            $this->createBuilder()->buildAstCloneExpression('clone')
        );
    }

    /**
     * testBuildASTCompoundExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTCompoundExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTCompoundExpression',
            $this->createBuilder()->buildAstCompoundExpression()
        );
    }

    /**
     * testBuildASTConditionalExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTConditionalExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTConditionalExpression',
            $this->createBuilder()->buildAstConditionalExpression()
        );
    }

    /**
     * testBuildASTEvalExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTEvalExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTEvalExpression',
            $this->createBuilder()->buildAstEvalExpression('eval')
        );
    }

    /**
     * testBuildASTExitExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTExitExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTExitExpression',
            $this->createBuilder()->buildAstExitExpression('exit')
        );
    }

    /**
     * testBuildASTExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTExpression',
            $this->createBuilder()->buildAstExpression()
        );
    }

    /**
     * testBuildASTIncludeExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTIncludeExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTIncludeExpression',
            $this->createBuilder()->buildAstIncludeExpression()
        );
    }

    /**
     * testBuildASTInstanceOfExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTInstanceOfExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTInstanceOfExpression',
            $this->createBuilder()->buildAstInstanceOfExpression('instanceof')
        );
    }

    /**
     * testBuildASTIssetExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTIssetExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTIssetExpression',
            $this->createBuilder()->buildAstIssetExpression()
        );
    }

    /**
     * testBuildASTListExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTListExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTListExpression',
            $this->createBuilder()->buildAstListExpression('list')
        );
    }

    /**
     * testBuildASTLogicalAndExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTLogicalAndExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTLogicalAndExpression',
            $this->createBuilder()->buildAstLogicalAndExpression('AND')
        );
    }

    /**
     * testBuildASTLogicalOrExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTLogicalOrExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTLogicalOrExpression',
            $this->createBuilder()->buildAstLogicalOrExpression('OR')
        );
    }

    /**
     * testBuildASTLogicalXorExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTLogicalXorExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTLogicalXorExpression',
            $this->createBuilder()->buildAstLogicalXorExpression('XOR')
        );
    }

    /**
     * testBuildASTRequireExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTRequireExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTRequireExpression',
            $this->createBuilder()->buildAstRequireExpression()
        );
    }

    /**
     * testBuildASTStringIndexExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTStringIndexExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTStringIndexExpression',
            $this->createBuilder()->buildAstStringIndexExpression()
        );
    }

    /**
     * testBuildASTUnaryExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTUnaryExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTUnaryExpression',
            $this->createBuilder()->buildAstUnaryExpression('+')
        );
    }

    /**
     * testBuildASTBreakStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTBreakStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTBreakStatement',
            $this->createBuilder()->buildAstBreakStatement('break')
        );
    }

    /**
     * testBuildASTCatchStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTCatchStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTCatchStatement',
            $this->createBuilder()->buildAstCatchStatement('catch')
        );
    }

    /**
     * testBuildASTFinallyStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTFinallyStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTFinallyStatement',
            $this->createBuilder()->buildAstFinallyStatement()
        );
    }

    /**
     * testBuildASTDeclareStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTDeclareStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTDeclareStatement',
            $this->createBuilder()->buildAstDeclareStatement()
        );
    }

    /**
     * testBuildASTIfStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTIfStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTIfStatement',
            $this->createBuilder()->buildAstIfStatement('if')
        );
    }

    /**
     * testBuildASTElseIfStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTElseIfStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTElseIfStatement',
            $this->createBuilder()->buildAstElseIfStatement('elseif')
        );
    }

    /**
     * testBuildASTContinueStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTContinueStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTContinueStatement',
            $this->createBuilder()->buildAstContinueStatement('continue')
        );
    }

    /**
     * testBuildASTDoWhileStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTDoWhileStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTDoWhileStatement',
            $this->createBuilder()->buildAstDoWhileStatement('while')
        );
    }

    /**
     * testBuildASTForStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTForStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTForStatement',
            $this->createBuilder()->buildAstForStatement('for')
        );
    }

    /**
     * testBuildASTForInitReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTForInitReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTForInit',
            $this->createBuilder()->buildAstForInit()
        );
    }

    /**
     * testBuildASTForUpdateReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTForUpdateReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTForUpdate',
            $this->createBuilder()->buildAstForUpdate()
        );
    }

    /**
     * testBuildASTForeachStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTForeachStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTForeachStatement',
            $this->createBuilder()->buildAstForeachStatement('foreach')
        );
    }

    /**
     * testBuildASTFormalParametersReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTFormalParametersReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTFormalParameters',
            $this->createBuilder()->buildAstFormalParameters()
        );
    }

    /**
     * testBuildASTFormalParameterReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTFormalParameterReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTFormalParameter',
            $this->createBuilder()->buildAstFormalParameter()
        );
    }

    /**
     * testBuildASTGlobalStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTGlobalStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTGlobalStatement',
            $this->createBuilder()->buildAstGlobalStatement()
        );
    }

    /**
     * testBuildASTGotoStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTGotoStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTGotoStatement',
            $this->createBuilder()->buildAstGotoStatement('goto')
        );
    }

    /**
     * testBuildASTLabelStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTLabelStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTLabelStatement',
            $this->createBuilder()->buildAstLabelStatement('LABEL')
        );
    }

    /**
     * testBuildASTReturnStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTReturnStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTReturnStatement',
            $this->createBuilder()->buildAstReturnStatement('return')
        );
    }

    /**
     * testBuildASTScopeStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTScopeStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTScopeStatement',
            $this->createBuilder()->buildAstScopeStatement()
        );
    }

    /**
     * testBuildASTStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTStatement',
            $this->createBuilder()->buildAstStatement()
        );
    }

    /**
     * testBuildASTTraitUseStatementReturnsExpectedType
     *
     * @return void
     * @since 1.0.0
     */
    public function testBuildASTTraitUseStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTraitUseStatement',
            $this->createBuilder()->buildAstTraitUseStatement()
        );
    }

    /**
     * testBuildASTTraitAdaptationReturnsExpectedType
     *
     * @return void
     * @since 1.0.0
     */
    public function testBuildASTTraitAdaptationReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTraitAdaptation',
            $this->createBuilder()->buildAstTraitAdaptation()
        );
    }

    /**
     * testBuildASTTraitAdaptationAliasReturnsExpectedType
     *
     * @return void
     * @since 1.0.0
     */
    public function testBuildASTTraitAdaptationAliasReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTraitAdaptationAlias',
            $this->createBuilder()->buildAstTraitAdaptationAlias(__CLASS__)
        );
    }

    /**
     * testBuildASTTraitAdaptationPrecedenceReturnsExpectedType
     *
     * @return void
     * @since 1.0.0
     */
    public function testBuildASTTraitAdaptationPrecedenceReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTraitAdaptationPrecedence',
            $this->createBuilder()->buildAstTraitAdaptationPrecedence(__CLASS__)
        );
    }

    /**
     * testBuildASTSwitchStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTSwitchStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTSwitchStatement',
            $this->createBuilder()->buildAstSwitchStatement()
        );
    }

    /**
     * testBuildASTThrowStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTThrowStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTThrowStatement',
            $this->createBuilder()->buildAstThrowStatement('throw')
        );
    }

    /**
     * testBuildASTTryStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTTryStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTryStatement',
            $this->createBuilder()->buildAstTryStatement('try')
        );
    }

    /**
     * testBuildASTUnsetStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTUnsetStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTUnsetStatement',
            $this->createBuilder()->buildAstUnsetStatement()
        );
    }

    /**
     * testBuildASTWhileStatementReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTWhileStatementReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTWhileStatement',
            $this->createBuilder()->buildAstWhileStatement('while')
        );
    }

    /**
     * testBuildASTArrayIndexExpressionReturnsExpectedType
     * 
     * @return void
     */
    public function testBuildASTArrayIndexExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTArrayIndexExpression',
            $this->createBuilder()->buildAstArrayIndexExpression()
        );
    }

    /**
     * testBuildASTClosureReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTClosureReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTClosure',
            $this->createBuilder()->buildAstClosure()
        );
    }

    /**
     * testBuildASTParentReferenceReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTParentReferenceReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTParentReference',
            $this->createBuilder()->buildAstParentReference(
                $this->createBuilder()->buildAstClassOrInterfaceReference(__CLASS__)
            )
        );
    }

    /**
     * testBuildASTSelfReferenceReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTSelfReferenceReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTSelfReference',
            $this->createBuilder()->buildAstSelfReference(
                $this->createBuilder()->buildClass(__CLASS__)
            )
        );
    }

    /**
     * testBuildASTStaticReferenceReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTStaticReferenceReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTStaticReference',
            $this->createBuilder()->buildAstStaticReference(
                $this->createBuilder()->buildClass(__CLASS__)
            )
        );
    }

    /**
     * testBuildASTTraitReferenceReturnsExpectedType
     *
     * @return void
     * @since 1.0.0
     */
    public function testBuildASTTraitReferenceReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTTraitReference',
            $this->createBuilder()->buildAstTraitReference(__CLASS__)
        );
    }

    /**
     * testBuildASTClassReferenceReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTClassOrInterfaceReferenceReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTClassOrInterfaceReference',
            $this->createBuilder()->buildAstClassOrInterfaceReference(__CLASS__)
        );
    }

    /**
     * testBuildASTClassReferenceReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTClassReferenceReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTClassReference',
            $this->createBuilder()->buildAstClassReference(__CLASS__)
        );
    }

    /**
     * testBuildASTPrimitiveTypeReturnsInstanceOfExpectedType
     *
     * @return void
     */
    public function testBuildASTPrimitiveTypeReturnsInstanceOfExpectedType()
    {
        $builder  = $this->createBuilder();
        $instance = $builder->buildAstPrimitiveType(__FUNCTION__);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTPrimitiveType', $instance);
    }

    /**
     * testBuildASTAllocationExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTAllocationExpressionReturnsExpectedType()
    {
        $object = $this->createBuilder()
            ->buildAstAllocationExpression(__FUNCTION__);

        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTAllocationExpression',
            $object
        );
    }

    /**
     * testBuildASTArgumentsReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTArgumentsReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTArguments',
            $this->createBuilder()->buildAstArguments()
        );
    }

    /**
     * testBuildASTSwitchLabel
     *
     * @return void
     */
    public function testBuildASTSwitchLabel()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTSwitchLabel',
            $this->createBuilder()->buildAstSwitchLabel('m')
        );
    }

    /**
     * testBuildASTEchoStatetmentReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTEchoStatetmentReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTEchoStatement',
            $this->createBuilder()->buildAstEchoStatement('echo')
        );
    }

    /**
     * testBuildASTVariableDeclaratorReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTVariableDeclaratorReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTVariableDeclarator',
            $this->createBuilder()->buildAstVariableDeclarator('foo')
        );
    }

    /**
     * testBuildASTStaticVariableDeclarationReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTStaticVariableDeclarationReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTStaticVariableDeclaration',
            $this->createBuilder()->buildAstStaticVariableDeclaration('$foo')
        );
    }

    /**
     * testBuildASTPostfixExpressionReturnsExpectedType
     * 
     * @return void
     */
    public function testBuildASTPostfixExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTPostfixExpression',
            $this->createBuilder()->buildAstPostfixExpression('++')
        );
    }

    /**
     * testBuildASTPreDecrementExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTPreDecrementExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTPreDecrementExpression',
            $this->createBuilder()->buildAstPreDecrementExpression()
        );
    }

    /**
     * testBuildASTPreIncrementExpressionReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTPreIncrementExpressionReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTPreIncrementExpression',
            $this->createBuilder()->buildAstPreIncrementExpression()
        );
    }

    /**
     * testBuildASTMemberPrimaryPrefixReturnsExpectedType
     * 
     * @return void
     * @since 1.0.0
     */
    public function testBuildASTMemberPrimaryPrefixReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTMemberPrimaryPrefix',
            $this->createBuilder()->buildAstMemberPrimaryPrefix('::')
        );
    }

    /**
     * testBuildASTMethodPostfixReturnsExpectedType
     *
     * @return void
     * @since 1.0.0
     */
    public function testBuildASTMethodPostfixReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTMethodPostfix',
            $this->createBuilder()->buildAstMethodPostfix('foo')
        );
    }

    /**
     * testBuildASTFunctionPostfixReturnsExpectedType
     *
     * @return void
     */
    public function testBuildASTFunctionPostfixReturnsExpectedType()
    {
        $this->assertInstanceOf(
            'PDepend\\Source\\AST\\ASTFunctionPostfix',
            $this->createBuilder()->buildAstFunctionPostfix('foo')
        );
    }

    /**
     * Creates a clean builder test instance.
     *
     * @return \PDepend\Source\Language\PHP\PHPBuilder
     */
    protected function createBuilder()
    {
        $builder = new PHPBuilder();
        $builder->setCache($this->createCacheFixture());

        return $builder;
    }
}
