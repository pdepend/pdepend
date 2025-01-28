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

use BadMethodCallException;
use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\AbstractASTType;
use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTAnonymousClass;
use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTArrayElement;
use PDepend\Source\AST\ASTArrayIndexExpression;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTAssignmentExpression;
use PDepend\Source\AST\ASTBooleanAndExpression;
use PDepend\Source\AST\ASTBooleanOrExpression;
use PDepend\Source\AST\ASTBreakStatement;
use PDepend\Source\AST\ASTCastExpression;
use PDepend\Source\AST\ASTCatchStatement;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTClassFqnPostfix;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTClassReference;
use PDepend\Source\AST\ASTCloneExpression;
use PDepend\Source\AST\ASTClosure;
use PDepend\Source\AST\ASTComment;
use PDepend\Source\AST\ASTCompilationUnit;
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
use PDepend\Source\AST\ASTEnum;
use PDepend\Source\AST\ASTEnumCase;
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
use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTIntersectionType;
use PDepend\Source\AST\ASTIssetExpression;
use PDepend\Source\AST\ASTLabelStatement;
use PDepend\Source\AST\ASTListExpression;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTLogicalAndExpression;
use PDepend\Source\AST\ASTLogicalOrExpression;
use PDepend\Source\AST\ASTLogicalXorExpression;
use PDepend\Source\AST\ASTMatchArgument;
use PDepend\Source\AST\ASTMatchBlock;
use PDepend\Source\AST\ASTMatchEntry;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTMethodPostfix;
use PDepend\Source\AST\ASTNamedArgument;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTParentReference;
use PDepend\Source\AST\ASTPostfixExpression;
use PDepend\Source\AST\ASTPreDecrementExpression;
use PDepend\Source\AST\ASTPreIncrementExpression;
use PDepend\Source\AST\ASTPrintExpression;
use PDepend\Source\AST\ASTPropertyPostfix;
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
use PDepend\Source\AST\ASTTrait;
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
use PDepend\Source\AST\ASTUnionType;
use PDepend\Source\AST\ASTUnsetStatement;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableDeclarator;
use PDepend\Source\AST\ASTVariableVariable;
use PDepend\Source\AST\ASTWhileStatement;
use PDepend\Source\AST\ASTYieldStatement;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Builder\BuilderContext;
use PDepend\Source\Builder\BuilderContext\GlobalBuilderContext;
use PDepend\Util\Cache\CacheDriver;
use PDepend\Util\Log;
use PDepend\Util\Type;

/**
 * Default code tree builder implementation.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @implements Builder<ASTNamespace>
 */
class PHPBuilder implements Builder
{
    /**
     * The internal used cache instance.
     *
     * @since 0.10.0
     */
    private CacheDriver $cache;

    /**
     * The ast builder context.
     *
     * @since 0.10.0
     */
    private readonly BuilderContext $context;

    /**
     * Default package which contains all functions and classes with an unknown
     * scope.
     */
    private readonly ASTNamespace $defaultPackage;

    /** Default source file that acts as a dummy. */
    private readonly ASTCompilationUnit $defaultCompilationUnit;

    /**
     * This property holds all packages found during the parsing phase.
     *
     * @var ASTNamespace[]
     * @since 0.9.12
     */
    private array $preparedNamespaces;

    /**
     * All generated {@link ASTTrait} objects
     *
     * @var array<string, array<string, array<string, ASTTrait>>>
     */
    private array $traits = [];

    /**
     * All generated {@link ASTClass} objects
     *
     * @var array<string, array<string, array<string, ASTClass|ASTEnum>>>
     */
    private array $classes = [];

    /**
     * All generated {@link ASTInterface} instances.
     *
     * @var array<string, array<string, array<string, ASTInterface>>>
     */
    private array $interfaces = [];

    /**
     * All generated {@link ASTNamespace} objects
     *
     * @var ASTNamespace[]
     */
    private array $namespaces = [];

    /** Internal status flag used to check that a build request is internal. */
    private bool $internal = false;

    /** Internal used flag that marks the parsing process as frozen. */
    private bool $frozen = false;

    /**
     * Cache of all traits created during the regular parsing process.
     *
     * @var array<string, array<string, array<string, ASTTrait>>>
     */
    private array $frozenTraits = [];

    /**
     * Cache of all classes created during the regular parsing process.
     *
     * @var array<string, array<string, array<string, ASTClass|ASTEnum>>>
     */
    private array $frozenClasses = [];

    /**
     * Cache of all interfaces created during the regular parsing process.
     *
     * @var array<string, array<string, array<string, ASTInterface>>>
     */
    private array $frozenInterfaces = [];

    /**
     * Constructs a new builder instance.
     */
    public function __construct()
    {
        $this->defaultPackage = new ASTNamespace(self::DEFAULT_NAMESPACE);
        $this->defaultCompilationUnit = new ASTCompilationUnit(null);

        $this->namespaces[self::DEFAULT_NAMESPACE] = $this->defaultPackage;

        $this->context = new GlobalBuilderContext($this);
    }

    /**
     * Setter method for the currently used token cache.
     *
     * @return $this
     * @since  0.10.0
     */
    public function setCache(CacheDriver $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     * @since  0.9.5
     */
    public function buildAstClassOrInterfaceReference(string $qualifiedName): ASTClassOrInterfaceReference
    {
        $this->checkBuilderState();

        // Debug method creation
        Log::debug(
            'Creating: \\PDepend\\Source\\AST\\ASTClassOrInterfaceReference(' .
            $qualifiedName .
            ')',
        );

        return new ASTClassOrInterfaceReference($this->context, $qualifiedName);
    }

    /**
     * Builds a new code type reference instance, either Class or ClassOrInterface.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     * @param bool $classReference true if class reference only.
     * @since  0.9.5
     */
    public function buildAstNeededReference(string $qualifiedName, bool $classReference): ASTClassOrInterfaceReference
    {
        if ($classReference) {
            return $this->buildAstClassReference($qualifiedName);
        }

        return $this->buildAstClassOrInterfaceReference($qualifiedName);
    }

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link ASTClass}
     * instance when no matching type exists.
     *
     * @since  0.9.5
     */
    public function getClassOrInterface(string $qualifiedName): AbstractASTClassOrInterface
    {
        $classOrInterface = $this->findClass($qualifiedName);
        if ($classOrInterface !== null) {
            return $classOrInterface;
        }

        $classOrInterface = $this->findInterface($qualifiedName);
        if ($classOrInterface !== null) {
            return $classOrInterface;
        }

        return $this->buildClassInternal($qualifiedName);
    }

    /**
     * Builds a new php trait instance.
     *
     * @param string $qualifiedName The full qualified trait name.
     * @since 1.0.0
     */
    public function buildTrait(string $qualifiedName): ASTTrait
    {
        $this->checkBuilderState();

        $trait = new ASTTrait($this->extractTypeName($qualifiedName));
        $trait->setCache($this->cache)
            ->setContext($this->context)
            ->setCompilationUnit($this->defaultCompilationUnit);

        return $trait;
    }

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link ASTTrait}
     * instance when no matching type exists.
     *
     * @since  1.0.0
     */
    public function getTrait(string $qualifiedName): ASTTrait
    {
        $trait = $this->findTrait($qualifiedName);
        if ($trait === null) {
            $trait = $this->buildTraitInternal($qualifiedName);
        }

        return $trait;
    }

    /**
     * Builds a new trait reference node.
     *
     * @param string $qualifiedName The full qualified trait name.
     * @since  1.0.0
     */
    public function buildAstTraitReference(string $qualifiedName): ASTTraitReference
    {
        $this->checkBuilderState();

        Log::debug(
            'Creating: \\PDepend\\Source\\AST\\ASTTraitReference(' . $qualifiedName . ')',
        );

        return new ASTTraitReference($this->context, $qualifiedName);
    }

    /**
     * Builds a new class instance or reuses a previous created class.
     *
     * Where possible you should give a qualified class name, that is prefixed
     * with the package identifier.
     *
     * <code>
     *   $builder->buildClass('php::depend::Parser');
     * </code>
     *
     * To determine the correct class, this method implements the following
     * algorithm.
     *
     * <ol>
     *   <li>Check for an exactly matching instance and reuse it.</li>
     *   <li>Check for a class instance that belongs to the default package. If
     *   such an instance exists, reuse it and replace the default package with
     *   the newly given package information.</li>
     *   <li>Check that the requested class is in the default package, if this
     *   is true, reuse the first class instance and ignore the default package.
     *   </li>
     *   <li>Create a new instance for the specified package.</li>
     * </ol>
     *
     * @param string $name The class name.
     * @return ASTClass The created class object.
     */
    public function buildClass(string $name): ASTClass
    {
        $this->checkBuilderState();

        $class = new ASTClass($this->extractTypeName($name));
        $class->setCache($this->cache)
            ->setContext($this->context)
            ->setCompilationUnit($this->defaultCompilationUnit);

        return $class;
    }

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link ASTClass}
     * instance when no matching type exists.
     *
     * @param string $qualifiedName The full qualified type identifier.
     * @since  0.9.5
     */
    public function getClass(string $qualifiedName): ASTClass|ASTEnum
    {
        return $this->findClass($qualifiedName)
            ?: $this->buildClassInternal($qualifiedName);
    }

    /**
     * Builds an anonymous class instance.
     */
    public function buildAnonymousClass(): ASTAnonymousClass
    {
        return $this->buildAstNodeInstance(ASTAnonymousClass::class)
            ->setCache($this->cache)
            ->setContext($this->context);
    }

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     * @since  0.9.5
     */
    public function buildAstClassReference(string $qualifiedName): ASTClassReference
    {
        $this->checkBuilderState();

        // Debug method creation
        Log::debug(
            'Creating: \\PDepend\\Source\\AST\\ASTClassReference(' . $qualifiedName . ')',
        );

        return new ASTClassReference($this->context, $qualifiedName);
    }

    /**
     * Builds a new new interface instance.
     *
     * If there is an existing class instance for the given name, this method
     * checks if this class is part of the default namespace. If this is the
     * case this method will update all references to the new interface and it
     * removes the class instance. Otherwise it creates new interface instance.
     *
     * Where possible you should give a qualified interface name, that is
     * prefixed with the package identifier.
     *
     * <code>
     *   $builder->buildInterface('php::depend::Parser');
     * </code>
     *
     * To determine the correct interface, this method implements the following
     * algorithm.
     *
     * <ol>
     *   <li>Check for an exactly matching instance and reuse it.</li>
     *   <li>Check for a interface instance that belongs to the default package.
     *   If such an instance exists, reuse it and replace the default package
     *   with the newly given package information.</li>
     *   <li>Check that the requested interface is in the default package, if
     *   this is true, reuse the first interface instance and ignore the default
     *   package.
     *   </li>
     *   <li>Create a new instance for the specified package.</li>
     * </ol>
     *
     * @param string $name The interface name.
     */
    public function buildInterface(string $name): ASTInterface
    {
        $this->checkBuilderState();

        $interface = new ASTInterface($this->extractTypeName($name));
        $interface->setCache($this->cache)
            ->setContext($this->context)
            ->setCompilationUnit($this->defaultCompilationUnit);

        return $interface;
    }

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link ASTInterface}
     * instance when no matching type exists.
     *
     * @since  0.9.5
     */
    public function getInterface(string $qualifiedName): ASTInterface
    {
        $interface = $this->findInterface($qualifiedName);
        if ($interface === null) {
            $interface = $this->buildInterfaceInternal($qualifiedName);
        }

        return $interface;
    }

    /**
     * Builds a new method instance.
     */
    public function buildMethod(string $name): ASTMethod
    {
        $this->checkBuilderState();

        // Debug method creation
        Log::debug("Creating: \\PDepend\\Source\\AST\\ASTMethod({$name})");

        // Create a new method instance
        $method = new ASTMethod($name);
        $method->setCache($this->cache);

        return $method;
    }

    /**
     * Builds a new package instance.
     *
     * @param string $name The package name.
     */
    public function buildNamespace(string $name): ASTNamespace
    {
        if (!isset($this->namespaces[$name])) {
            // Debug package creation
            Log::debug(
                'Creating: \\PDepend\\Source\\AST\\ASTNamespace(' . $name . ')',
            );

            $this->namespaces[$name] = new ASTNamespace($name);
        }

        return $this->namespaces[$name];
    }

    /**
     * Builds a new function instance.
     *
     * @param string $name The function name.
     */
    public function buildFunction(string $name): ASTFunction
    {
        $this->checkBuilderState();

        // Debug function creation
        Log::debug("Creating: \\PDepend\\Source\\AST\\ASTFunction({$name})");

        // Create new function
        $function = new ASTFunction($name);
        $function->setCache($this->cache)
            ->setContext($this->context)
            ->setCompilationUnit($this->defaultCompilationUnit);

        return $function;
    }

    /**
     * Builds a new self reference instance.
     *
     * @since  0.9.6
     */
    public function buildAstSelfReference(AbstractASTClassOrInterface $type): ASTSelfReference
    {
        Log::debug(
            'Creating: \\PDepend\\Source\\AST\\ASTSelfReference(' . $type->getImage() . ')',
        );

        return new ASTSelfReference($this->context, $type);
    }

    /**
     * Builds a new parent reference instance.
     *
     * @param ASTClassOrInterfaceReference $reference The type
     *                                                instance that reference the concrete target of parent.
     * @since  0.9.6
     */
    public function buildAstParentReference(ASTClassOrInterfaceReference $reference): ASTParentReference
    {
        Log::debug('Creating: \\PDepend\\Source\\AST\\ASTParentReference()');

        return new ASTParentReference($reference);
    }

    /**
     * Builds a new static reference instance.
     *
     * @since  0.9.6
     */
    public function buildAstStaticReference(AbstractASTClassOrInterface $owner): ASTStaticReference
    {
        Log::debug('Creating: \\PDepend\\Source\\AST\\ASTStaticReference()');

        return new ASTStaticReference($this->context, $owner);
    }

    /**
     * Builds a new field declaration node.
     *
     * @since  0.9.6
     */
    public function buildAstFieldDeclaration(): ASTFieldDeclaration
    {
        return $this->buildAstNodeInstance(ASTFieldDeclaration::class);
    }

    /**
     * Builds a new variable declarator node.
     *
     * @param string $image The source image for the variable declarator.
     * @since  0.9.6
     */
    public function buildAstVariableDeclarator(string $image): ASTVariableDeclarator
    {
        return $this->buildAstNodeInstance(ASTVariableDeclarator::class, $image);
    }

    /**
     * Builds a new static variable declaration node.
     *
     * @param string $image The source image for the statuc declaration.
     * @since  0.9.6
     */
    public function buildAstStaticVariableDeclaration(string $image): ASTStaticVariableDeclaration
    {
        return $this->buildAstNodeInstance(ASTStaticVariableDeclaration::class, $image);
    }

    /**
     * Builds a new constant node.
     *
     * @param string $image The source image for the constant.
     * @since  0.9.6
     */
    public function buildAstConstant(string $image): ASTConstant
    {
        return $this->buildAstNodeInstance(ASTConstant::class, $image);
    }

    /**
     * Builds a new variable node.
     *
     * @param string $image The source image for the variable.
     * @since  0.9.6
     */
    public function buildAstVariable(string $image): ASTVariable
    {
        return $this->buildAstNodeInstance(ASTVariable::class, $image);
    }

    /**
     * Builds a new variable variable node.
     *
     * @param string $image The source image for the variable variable.
     * @since  0.9.6
     */
    public function buildAstVariableVariable(string $image): ASTVariableVariable
    {
        return $this->buildAstNodeInstance(ASTVariableVariable::class, $image);
    }

    /**
     * Builds a new compound variable node.
     *
     * @param string $image The source image for the compound variable.
     * @since  0.9.6
     */
    public function buildAstCompoundVariable(string $image): ASTCompoundVariable
    {
        return $this->buildAstNodeInstance(ASTCompoundVariable::class, $image);
    }

    /**
     * Builds a new compound expression node.
     *
     * @since  0.9.6
     */
    public function buildAstCompoundExpression(): ASTCompoundExpression
    {
        return $this->buildAstNodeInstance(ASTCompoundExpression::class);
    }

    /**
     * Builds a new closure node.
     *
     * @since  0.9.12
     */
    public function buildAstClosure(): ASTClosure
    {
        return $this->buildAstNodeInstance(ASTClosure::class);
    }

    /**
     * Builds a new formal parameters node.
     *
     * @since  0.9.6
     */
    public function buildAstFormalParameters(): ASTFormalParameters
    {
        return $this->buildAstNodeInstance(ASTFormalParameters::class);
    }

    /**
     * Builds a new formal parameter node.
     *
     * @since  0.9.6
     */
    public function buildAstFormalParameter(): ASTFormalParameter
    {
        return $this->buildAstNodeInstance(ASTFormalParameter::class);
    }

    /**
     * Builds a new expression node.
     *
     * @since 0.9.8
     */
    public function buildAstExpression(?string $image = null): ASTExpression
    {
        return $this->buildAstNodeInstance(ASTExpression::class, $image);
    }

    /**
     * Builds a new assignment expression node.
     *
     * @param string $image The assignment operator.
     * @since  0.9.8
     */
    public function buildAstAssignmentExpression(string $image): ASTAssignmentExpression
    {
        return $this->buildAstNodeInstance(ASTAssignmentExpression::class, $image);
    }

    /**
     * Builds a new allocation expression node.
     *
     * @param string $image The source image of this expression.
     * @since  0.9.6
     */
    public function buildAstAllocationExpression(string $image): ASTAllocationExpression
    {
        return $this->buildAstNodeInstance(ASTAllocationExpression::class, $image);
    }

    /**
     * Builds a new eval-expression node.
     *
     * @param string $image The source image of this expression.
     * @since  0.9.12
     */
    public function buildAstEvalExpression(string $image): ASTEvalExpression
    {
        return $this->buildAstNodeInstance(ASTEvalExpression::class, $image);
    }

    /**
     * Builds a new exit-expression instance.
     *
     * @param string $image The source code image for this node.
     * @since  0.9.12
     */
    public function buildAstExitExpression(string $image): ASTExitExpression
    {
        return $this->buildAstNodeInstance(ASTExitExpression::class, $image);
    }

    /**
     * Builds a new clone-expression node.
     *
     * @param string $image The source image of this expression.
     * @since  0.9.12
     */
    public function buildAstCloneExpression(string $image): ASTCloneExpression
    {
        return $this->buildAstNodeInstance(ASTCloneExpression::class, $image);
    }

    /**
     * Builds a new list-expression node.
     *
     * @param string $image The source image of this expression.
     * @since  0.9.12
     */
    public function buildAstListExpression(string $image): ASTListExpression
    {
        return $this->buildAstNodeInstance(ASTListExpression::class, $image);
    }

    /**
     * Builds a new include- or include_once-expression.
     *
     * @since  0.9.12
     */
    public function buildAstIncludeExpression(): ASTIncludeExpression
    {
        return $this->buildAstNodeInstance(ASTIncludeExpression::class);
    }

    /**
     * Builds a new require- or require_once-expression.
     *
     * @since  0.9.12
     */
    public function buildAstRequireExpression(): ASTRequireExpression
    {
        return $this->buildAstNodeInstance(ASTRequireExpression::class);
    }

    /**
     * Builds a new array-expression node.
     *
     * @since  0.9.12
     */
    public function buildAstArrayIndexExpression(): ASTArrayIndexExpression
    {
        return $this->buildAstNodeInstance(ASTArrayIndexExpression::class);
    }

    /**
     * Builds a new string-expression node.
     *
     * <code>
     * //     --------
     * $string{$index}
     * //     --------
     * </code>
     *
     * @since  0.9.12
     */
    public function buildAstStringIndexExpression(): ASTStringIndexExpression
    {
        return $this->buildAstNodeInstance(ASTStringIndexExpression::class);
    }

    /**
     * Builds a new php array node.
     *
     * @since  1.0.0
     */
    public function buildAstArray(): ASTArray
    {
        return $this->buildAstNodeInstance(ASTArray::class);
    }

    /**
     * Builds a new array element node.
     *
     * @since  1.0.0
     */
    public function buildAstArrayElement(): ASTArrayElement
    {
        return $this->buildAstNodeInstance(ASTArrayElement::class);
    }

    /**
     * Builds a new instanceof expression node.
     *
     * @param string $image The source image of this expression.
     * @since  0.9.6
     */
    public function buildAstInstanceOfExpression(string $image): ASTInstanceOfExpression
    {
        return $this->buildAstNodeInstance(ASTInstanceOfExpression::class, $image);
    }

    /**
     * Builds a new isset-expression node.
     *
     * <code>
     * //  -----------
     * if (isset($foo)) {
     * //  -----------
     * }
     *
     * //  -----------------------
     * if (isset($foo, $bar, $baz)) {
     * //  -----------------------
     * }
     * </code>
     *
     * @since  0.9.12
     */
    public function buildAstIssetExpression(): ASTIssetExpression
    {
        return $this->buildAstNodeInstance(ASTIssetExpression::class);
    }

    /**
     * Builds a new boolean conditional-expression.
     *
     * <code>
     *         --------------
     * $bar = ($foo ? 42 : 23);
     *         --------------
     * </code>
     *
     * @since  0.9.8
     */
    public function buildAstConditionalExpression(): ASTConditionalExpression
    {
        return $this->buildAstNodeInstance(ASTConditionalExpression::class, '?');
    }

    /**
     * Builds a new print-expression.
     *
     * <code>
     * -------------
     * print "qafoo";
     * -------------
     * </code>
     *
     * @since 2.3
     */
    public function buildAstPrintExpression(): ASTPrintExpression
    {
        return $this->buildAstNodeInstance(ASTPrintExpression::class, 'print');
    }

    /**
     * Build a new shift left expression.
     *
     * @since  1.0.1
     */
    public function buildAstShiftLeftExpression(): ASTShiftLeftExpression
    {
        return $this->buildAstNodeInstance(ASTShiftLeftExpression::class);
    }

    /**
     * Build a new shift right expression.
     *
     * @since  1.0.1
     */
    public function buildAstShiftRightExpression(): ASTShiftRightExpression
    {
        return $this->buildAstNodeInstance(ASTShiftRightExpression::class);
    }

    /**
     * Builds a new boolean and-expression.
     *
     * @since  0.9.8
     */
    public function buildAstBooleanAndExpression(): ASTBooleanAndExpression
    {
        return $this->buildAstNodeInstance(ASTBooleanAndExpression::class, '&&');
    }

    /**
     * Builds a new boolean or-expression.
     *
     * @since  0.9.8
     */
    public function buildAstBooleanOrExpression(): ASTBooleanOrExpression
    {
        return $this->buildAstNodeInstance(ASTBooleanOrExpression::class, '||');
    }

    /**
     * Builds a new logical <b>and</b>-expression.
     *
     * @since  0.9.8
     */
    public function buildAstLogicalAndExpression(): ASTLogicalAndExpression
    {
        return $this->buildAstNodeInstance(ASTLogicalAndExpression::class, 'and');
    }

    /**
     * Builds a new logical <b>or</b>-expression.
     *
     * @since  0.9.8
     */
    public function buildAstLogicalOrExpression(): ASTLogicalOrExpression
    {
        return $this->buildAstNodeInstance(ASTLogicalOrExpression::class, 'or');
    }

    /**
     * Builds a new logical <b>xor</b>-expression.
     *
     * @since  0.9.8
     */
    public function buildAstLogicalXorExpression(): ASTLogicalXorExpression
    {
        return $this->buildAstNodeInstance(ASTLogicalXorExpression::class, 'xor');
    }

    /**
     * Builds a new trait use-statement node.
     *
     * @since  1.0.0
     */
    public function buildAstTraitUseStatement(): ASTTraitUseStatement
    {
        return $this->buildAstNodeInstance(ASTTraitUseStatement::class);
    }

    /**
     * Builds a new trait adaptation scope
     *
     * @since  1.0.0
     */
    public function buildAstTraitAdaptation(): ASTTraitAdaptation
    {
        return $this->buildAstNodeInstance(ASTTraitAdaptation::class);
    }

    /**
     * Builds a new trait adaptation alias statement.
     *
     * @param string $image The trait method name.
     * @since  1.0.0
     */
    public function buildAstTraitAdaptationAlias(string $image): ASTTraitAdaptationAlias
    {
        return $this->buildAstNodeInstance(ASTTraitAdaptationAlias::class, $image);
    }

    /**
     * Builds a new trait adaptation precedence statement.
     *
     * @param string $image The trait method name.
     * @since  1.0.0
     */
    public function buildAstTraitAdaptationPrecedence(string $image): ASTTraitAdaptationPrecedence
    {
        return $this->buildAstNodeInstance(ASTTraitAdaptationPrecedence::class, $image);
    }

    /**
     * Builds a new switch-statement-node.
     *
     * @since  0.9.8
     */
    public function buildAstSwitchStatement(): ASTSwitchStatement
    {
        return $this->buildAstNodeInstance(ASTSwitchStatement::class);
    }

    /**
     * Builds a new switch-label node.
     *
     * @param string $image The source image of this label.
     * @since  0.9.8
     */
    public function buildAstSwitchLabel(string $image): ASTSwitchLabel
    {
        return $this->buildAstNodeInstance(ASTSwitchLabel::class, $image);
    }

    /**
     * Builds a new global-statement instance.
     *
     * @since  0.9.12
     */
    public function buildAstGlobalStatement(): ASTGlobalStatement
    {
        return $this->buildAstNodeInstance(ASTGlobalStatement::class);
    }

    /**
     * Builds a new unset-statement instance.
     *
     * @since  0.9.12
     */
    public function buildAstUnsetStatement(): ASTUnsetStatement
    {
        return $this->buildAstNodeInstance(ASTUnsetStatement::class);
    }

    /**
     * Builds a new catch-statement node.
     *
     * @since  0.9.8
     */
    public function buildAstCatchStatement(string $image): ASTCatchStatement
    {
        return $this->buildAstNodeInstance(ASTCatchStatement::class, $image);
    }

    /**
     * Builds a new finally-statement node.
     *
     * @since  2.0.0
     */
    public function buildAstFinallyStatement(): ASTFinallyStatement
    {
        return $this->buildAstNodeInstance(ASTFinallyStatement::class, 'finally');
    }

    /**
     * Builds a new if statement node.
     *
     * @param string $image The source image of this statement.
     * @since  0.9.8
     */
    public function buildAstIfStatement(string $image): ASTIfStatement
    {
        return $this->buildAstNodeInstance(ASTIfStatement::class, $image);
    }

    /**
     * Builds a new elseif statement node.
     *
     * @param string $image The source image of this statement.
     * @since  0.9.8
     */
    public function buildAstElseIfStatement(string $image): ASTElseIfStatement
    {
        return $this->buildAstNodeInstance(ASTElseIfStatement::class, $image);
    }

    /**
     * Builds a new for statement node.
     *
     * @param string $image The source image of this statement.
     * @since  0.9.8
     */
    public function buildAstForStatement(string $image): ASTForStatement
    {
        return $this->buildAstNodeInstance(ASTForStatement::class, $image);
    }

    /**
     * Builds a new for-init node.
     *
     * <code>
     *      ------------------------
     * for ($x = 0, $y = 23, $z = 42; $x < $y; ++$x) {}
     *      ------------------------
     * </code>
     *
     * @since  0.9.8
     */
    public function buildAstForInit(): ASTForInit
    {
        return $this->buildAstNodeInstance(ASTForInit::class);
    }

    /**
     * Builds a new for-update node.
     *
     * <code>
     *                                        -------------------------------
     * for ($x = 0, $y = 23, $z = 42; $x < $y; ++$x, $y = $x + 1, $z = $x + 2) {}
     *                                        -------------------------------
     * </code>
     *
     * @since  0.9.12
     */
    public function buildAstForUpdate(): ASTForUpdate
    {
        return $this->buildAstNodeInstance(ASTForUpdate::class);
    }

    /**
     * Builds a new foreach-statement node.
     *
     * @param string $image The source image of this statement.
     * @since  0.9.8
     */
    public function buildAstForeachStatement(string $image): ASTForeachStatement
    {
        return $this->buildAstNodeInstance(ASTForeachStatement::class, $image);
    }

    /**
     * Builds a new while-statement node.
     *
     * @param string $image The source image of this statement.
     * @since  0.9.8
     */
    public function buildAstWhileStatement(string $image): ASTWhileStatement
    {
        return $this->buildAstNodeInstance(ASTWhileStatement::class, $image);
    }

    /**
     * Builds a new do/while-statement node.
     *
     * @param string $image The source image of this statement.
     * @since  0.9.12
     */
    public function buildAstDoWhileStatement(string $image): ASTDoWhileStatement
    {
        return $this->buildAstNodeInstance(ASTDoWhileStatement::class, $image);
    }

    /**
     * Builds a new declare-statement node.
     *
     * <code>
     * -------------------------------
     * declare(encoding='ISO-8859-1');
     * -------------------------------
     *
     * -------------------
     * declare(ticks=42) {
     *     // ...
     * }
     * -
     *
     * ------------------
     * declare(ticks=42):
     *     // ...
     * enddeclare;
     * -----------
     * </code>
     *
     * @since  0.10.0
     */
    public function buildAstDeclareStatement(): ASTDeclareStatement
    {
        return $this->buildAstNodeInstance(ASTDeclareStatement::class);
    }

    /**
     * Builds a new member primary expression node.
     *
     * <code>
     * //--------
     * Foo::bar();
     * //--------
     *
     * //---------
     * Foo::$bar();
     * //---------
     *
     * //---------
     * $obj->bar();
     * //---------
     *
     * //----------
     * $obj->$bar();
     * //----------
     * </code>
     *
     * @param string $image The source image of this expression.
     * @since  0.9.6
     */
    public function buildAstMemberPrimaryPrefix(string $image): ASTMemberPrimaryPrefix
    {
        return $this->buildAstNodeInstance(ASTMemberPrimaryPrefix::class, $image);
    }

    /**
     * Builds a new identifier node.
     *
     * @param string $image The image of this identifier.
     * @since  0.9.6
     */
    public function buildAstIdentifier(string $image): ASTIdentifier
    {
        return $this->buildAstNodeInstance(ASTIdentifier::class, $image);
    }

    /**
     * Builds a new function postfix expression.
     *
     * <code>
     * //-------
     * foo($bar);
     * //-------
     *
     * //--------
     * $foo($bar);
     * //--------
     * </code>
     *
     * @param string $image The image of this node.
     * @since  0.9.6
     */
    public function buildAstFunctionPostfix(string $image): ASTFunctionPostfix
    {
        return $this->buildAstNodeInstance(ASTFunctionPostfix::class, $image);
    }

    /**
     * Builds a new method postfix expression.
     *
     * <code>
     * //   ---------
     * Foo::bar($baz);
     * //   ---------
     *
     * //   ----------
     * Foo::$bar($baz);
     * //   ----------
     * </code>
     *
     * @param string $image The image of this node.
     * @since  0.9.6
     */
    public function buildAstMethodPostfix(string $image): ASTMethodPostfix
    {
        return $this->buildAstNodeInstance(ASTMethodPostfix::class, $image);
    }

    /**
     * Builds a new constant postfix expression.
     *
     * <code>
     * //   ---
     * Foo::BAR;
     * //   ---
     * </code>
     *
     * @param string $image The image of this node.
     * @since  0.9.6
     */
    public function buildAstConstantPostfix(string $image): ASTConstantPostfix
    {
        return $this->buildAstNodeInstance(ASTConstantPostfix::class, $image);
    }

    /**
     * Builds a new property postfix expression.
     *
     * <code>
     * //   ----
     * Foo::$bar;
     * //   ----
     *
     * //       ---
     * $object->bar;
     * //       ---
     * </code>
     *
     * @param string $image The image of this node.
     * @since  0.9.6
     */
    public function buildAstPropertyPostfix(string $image): ASTPropertyPostfix
    {
        return $this->buildAstNodeInstance(ASTPropertyPostfix::class, $image);
    }

    /**
     * Builds a new full qualified class name postfix expression.
     *
     * <code>
     * //   -----
     * Foo::class;
     * //   -----
     *
     * //       -----
     * $object::class;
     * //       -----
     * </code>
     *
     * @since  2.0.0
     */
    public function buildAstClassFqnPostfix(): ASTClassFqnPostfix
    {
        return $this->buildAstNodeInstance(ASTClassFqnPostfix::class, 'class');
    }

    /**
     * Builds a new arguments list.
     *
     * <code>
     * //      ------------
     * Foo::bar($x, $y, $z);
     * //      ------------
     *
     * //       ------------
     * $foo->bar($x, $y, $z);
     * //       ------------
     * </code>
     *
     * @since  0.9.6
     */
    public function buildAstArguments(): ASTArguments
    {
        return $this->buildAstNodeInstance(ASTArguments::class);
    }

    /**
     * Builds a new argument match expression single-item slot.
     *
     * <code>
     * match($x)
     * </code>
     *
     * @since  0.9.6
     */
    public function buildAstMatchArgument(): ASTMatchArgument
    {
        return $this->buildAstNodeInstance(ASTMatchArgument::class);
    }

    /**
     * Builds a new match block.
     *
     * <code>
     * match($x) {
     *   "foo" => "bar",
     * }
     * </code>
     *
     * @since  2.9.0
     */
    public function buildAstMatchBlock(): ASTMatchBlock
    {
        return $this->buildAstNodeInstance(ASTMatchBlock::class);
    }

    /**
     * Builds a new match item.
     *
     * <code>
     * "foo" => "bar",
     * </code>
     *
     * @since  2.9.0
     */
    public function buildAstMatchEntry(): ASTMatchEntry
    {
        return $this->buildAstNodeInstance(ASTMatchEntry::class);
    }

    /**
     * Builds a new named argument node.
     *
     * <code>
     * number_format(5623, thousands_separator: ' ')
     * </code>
     *
     * @since  2.9.0
     */
    public function buildAstNamedArgument(string $name, ASTNode $value): ASTNamedArgument
    {
        Log::debug("Creating: \\PDepend\\Source\\AST\\ASTNamedArgument($name)");

        return new ASTNamedArgument($name, $value);
    }

    /**
     * Builds a new array type node.
     *
     * @since  0.9.6
     */
    public function buildAstTypeArray(): ASTTypeArray
    {
        return $this->buildAstNodeInstance(ASTTypeArray::class);
    }

    /**
     * Builds a new node for the callable type.
     *
     * @since  1.0.0
     */
    public function buildAstTypeCallable(): ASTTypeCallable
    {
        return $this->buildAstNodeInstance(ASTTypeCallable::class);
    }

    /**
     * Builds a new node for the iterable type.
     *
     * @since  2.5.1
     */
    public function buildAstTypeIterable(): ASTTypeIterable
    {
        return $this->buildAstNodeInstance(ASTTypeIterable::class);
    }

    /**
     * Builds a new primitive type node.
     *
     * @param string $image The source image for the primitive type.
     * @since  0.9.6
     */
    public function buildAstScalarType(string $image): ASTScalarType
    {
        return $this->buildAstNodeInstance(ASTScalarType::class, $image);
    }

    /**
     * Builds a new node for the union type.
     *
     * @since  1.0.0
     */
    public function buildAstUnionType(): ASTUnionType
    {
        return $this->buildAstNodeInstance(ASTUnionType::class);
    }

    /**
     * Builds a new node for the union type.
     */
    public function buildAstIntersectionType(): ASTIntersectionType
    {
        return $this->buildAstNodeInstance(ASTIntersectionType::class);
    }

    /**
     * Builds a new literal node.
     *
     * @param string $image The source image for the literal node.
     * @since  0.9.6
     */
    public function buildAstLiteral(string $image): ASTLiteral
    {
        return $this->buildAstNodeInstance(ASTLiteral::class, $image);
    }

    /**
     * Builds a new php string node.
     *
     * <code>
     * $string = "Manuel $Pichler <{$email}>";
     *
     * // ASTString
     * // |-- ASTLiteral             -  "Manuel ")
     * // |-- ASTVariable            -  $Pichler
     * // |-- ASTLiteral             -  " <"
     * // |-- ASTCompoundExpression  -  {...}
     * // |   |-- ASTVariable        -  $email
     * // |-- ASTLiteral             -  ">"
     * </code>
     *
     * @since  0.9.10
     */
    public function buildAstString(): ASTString
    {
        return $this->buildAstNodeInstance(ASTString::class);
    }

    /**
     * Builds a new heredoc node.
     *
     * @since  0.9.12
     */
    public function buildAstHeredoc(): ASTHeredoc
    {
        return $this->buildAstNodeInstance(ASTHeredoc::class);
    }

    /**
     * Builds a new constant definition node.
     *
     * <code>
     * class Foo
     * {
     * //  ------------------------
     *     const FOO = 42, BAR = 23;
     * //  ------------------------
     * }
     * </code>
     *
     * @param string $image The source code image for this node.
     * @since  0.9.6
     */
    public function buildAstConstantDefinition(string $image): ASTConstantDefinition
    {
        return $this->buildAstNodeInstance(ASTConstantDefinition::class, $image);
    }

    /**
     * Builds a new constant declarator node.
     *
     * <code>
     * class Foo
     * {
     *     //    --------
     *     const BAR = 42;
     *     //    --------
     * }
     * </code>
     *
     * Or in a comma separated constant defintion:
     *
     * <code>
     * class Foo
     * {
     *     //    --------
     *     const BAR = 42,
     *     //    --------
     *
     *     //    --------------
     *     const BAZ = 'Foobar',
     *     //    --------------
     *
     *     //    ----------
     *     const FOO = 3.14;
     *     //    ----------
     * }
     * </code>
     *
     * @param string $image The source code image for this node.
     * @since  0.9.6
     */
    public function buildAstConstantDeclarator(string $image): ASTConstantDeclarator
    {
        return $this->buildAstNodeInstance(ASTConstantDeclarator::class, $image);
    }

    /**
     * Builds a new comment node instance.
     *
     * @param string $cdata The comment text.
     * @since  0.9.8
     */
    public function buildAstComment(string $cdata): ASTComment
    {
        return $this->buildAstNodeInstance(ASTComment::class, $cdata);
    }

    /**
     * Builds a new unary expression node instance.
     *
     * @param string $image The unary expression image/character.
     * @since  0.9.11
     */
    public function buildAstUnaryExpression(string $image): ASTUnaryExpression
    {
        return $this->buildAstNodeInstance(ASTUnaryExpression::class, $image);
    }

    /**
     * Builds a new cast-expression node instance.
     *
     * @param string $image The cast-expression image/character.
     * @since  0.10.0
     */
    public function buildAstCastExpression(string $image): ASTCastExpression
    {
        return $this->buildAstNodeInstance(ASTCastExpression::class, $image);
    }

    /**
     * Builds a new postfix-expression node instance.
     *
     * @param string $image The postfix-expression image/character.
     * @since  0.10.0
     */
    public function buildAstPostfixExpression(string $image): ASTPostfixExpression
    {
        return $this->buildAstNodeInstance(ASTPostfixExpression::class, $image);
    }

    /**
     * Builds a new pre-increment-expression node instance.
     *
     * @since  0.10.0
     */
    public function buildAstPreIncrementExpression(): ASTPreIncrementExpression
    {
        return $this->buildAstNodeInstance(ASTPreIncrementExpression::class);
    }

    /**
     * Builds a new pre-decrement-expression node instance.
     *
     * @since  0.10.0
     */
    public function buildAstPreDecrementExpression(): ASTPreDecrementExpression
    {
        return $this->buildAstNodeInstance(ASTPreDecrementExpression::class);
    }

    /**
     * Builds a new function/method scope instance.
     *
     * @since  0.9.12
     */
    public function buildAstScope(): ASTScope
    {
        return $this->buildAstNodeInstance(ASTScope::class);
    }

    /**
     * Builds a new statement instance.
     *
     * @since  0.9.12
     */
    public function buildAstStatement(): ASTStatement
    {
        return $this->buildAstNodeInstance(ASTStatement::class);
    }

    /**
     * Builds a new return statement node instance.
     *
     * @param string $image The source code image for this node.
     * @since  0.9.12
     */
    public function buildAstReturnStatement(string $image): ASTReturnStatement
    {
        return $this->buildAstNodeInstance(ASTReturnStatement::class, $image);
    }

    /**
     * Builds a new break-statement node instance.
     *
     * @param string $image The source code image for this node.
     * @since  0.9.12
     */
    public function buildAstBreakStatement(string $image): ASTBreakStatement
    {
        return $this->buildAstNodeInstance(ASTBreakStatement::class, $image);
    }

    /**
     * Builds a new continue-statement node instance.
     *
     * @param string $image The source code image for this node.
     * @since  0.9.12
     */
    public function buildAstContinueStatement(string $image): ASTContinueStatement
    {
        return $this->buildAstNodeInstance(ASTContinueStatement::class, $image);
    }

    /**
     * Builds a new scope-statement instance.
     *
     * @since  0.9.12
     */
    public function buildAstScopeStatement(): ASTScopeStatement
    {
        return $this->buildAstNodeInstance(ASTScopeStatement::class);
    }

    /**
     * Builds a new try-statement instance.
     *
     * @param string $image The source code image for this node.
     * @since  0.9.12
     */
    public function buildAstTryStatement(string $image): ASTTryStatement
    {
        return $this->buildAstNodeInstance(ASTTryStatement::class, $image);
    }

    /**
     * Builds a new throw-statement instance.
     *
     * @param string $image The source code image for this node.
     * @since  0.9.12
     */
    public function buildAstThrowStatement(string $image): ASTThrowStatement
    {
        return $this->buildAstNodeInstance(ASTThrowStatement::class, $image);
    }

    /**
     * Builds a new goto-statement instance.
     *
     * @param string $image The source code image for this node.
     * @since  0.9.12
     */
    public function buildAstGotoStatement(string $image): ASTGotoStatement
    {
        return $this->buildAstNodeInstance(ASTGotoStatement::class, $image);
    }

    /**
     * Builds a new label-statement instance.
     *
     * @param string $image The source code image for this node.
     * @since  0.9.12
     */
    public function buildAstLabelStatement(string $image): ASTLabelStatement
    {
        return $this->buildAstNodeInstance(ASTLabelStatement::class, $image);
    }

    /**
     * Builds a new exit-statement instance.
     *
     * @param string $image The source code image for this node.
     * @since  0.9.12
     */
    public function buildAstEchoStatement(string $image): ASTEchoStatement
    {
        return $this->buildAstNodeInstance(ASTEchoStatement::class, $image);
    }

    /**
     * Builds a new yield-statement instance.
     *
     * @param string $image The source code image for this node.
     * @since  $version$
     */
    public function buildAstYieldStatement(string $image): ASTYieldStatement
    {
        return $this->buildAstNodeInstance(ASTYieldStatement::class, $image);
    }

    /**
     * Returns an iterator with all generated {@link ASTNamespace}
     * objects.
     *
     * @return ASTArtifactList<ASTNamespace>
     */
    public function getIterator(): ASTArtifactList
    {
        return $this->getNamespaces();
    }

    /**
     * Returns an iterator with all generated {@link ASTNamespace}
     * objects.
     *
     * @return ASTArtifactList<ASTNamespace>
     */
    public function getNamespaces(): ASTArtifactList
    {
        if (!isset($this->preparedNamespaces)) {
            $this->preparedNamespaces = $this->getPreparedNamespaces();
        }

        return new ASTArtifactList($this->preparedNamespaces);
    }

    /**
     * Returns an iterator with all generated {@link ASTNamespace}
     * objects.
     *
     * @return ASTNamespace[]
     * @since  0.9.12
     */
    private function getPreparedNamespaces(): array
    {
        // Create a package array copy
        $namespaces = $this->namespaces;

        // Remove default package if empty
        if (
            count($this->defaultPackage->getTypes()) === 0
            && count($this->defaultPackage->getFunctions()) === 0
        ) {
            unset($namespaces[self::DEFAULT_NAMESPACE]);
        }

        return $namespaces;
    }

    /**
     * Builds a new trait instance or reuses a previous created trait.
     *
     * Where possible you should give a qualified trait name, that is prefixed
     * with the package identifier.
     *
     * <code>
     *   $builder->buildTrait('php::depend::Parser');
     * </code>
     *
     * To determine the correct trait, this method implements the following
     * algorithm.
     *
     * <ol>
     *   <li>Check for an exactly matching instance and reuse it.</li>
     *   <li>Check for a class instance that belongs to the default package. If
     *   such an instance exists, reuse it and replace the default package with
     *   the newly given package information.</li>
     *   <li>Check that the requested trait is in the default package, if this
     *   is true, reuse the first trait instance and ignore the default package.
     *   </li>
     *   <li>Create a new instance for the specified package.</li>
     * </ol>
     *
     * @since  0.9.5
     */
    protected function buildTraitInternal(string $qualifiedName): ASTTrait
    {
        $this->internal = true;

        $trait = $this->buildTrait($qualifiedName);
        $trait->setNamespace(
            $this->buildNamespace($this->extractNamespaceName($qualifiedName) ?? self::DEFAULT_NAMESPACE),
        );

        $this->restoreTrait($trait);

        return $trait;
    }

    /**
     * This method tries to find a trait instance matching for the given
     * qualified name in all scopes already processed. It will return the best
     * matching instance or <b>null</b> if no match exists.
     *
     * @since  0.9.5
     */
    protected function findTrait(string $qualifiedName): ?ASTTrait
    {
        $this->freeze();

        /** @var ASTTrait|null $trait */
        $trait = $this->findType(
            $this->frozenTraits,
            $qualifiedName,
        );

        if ($trait === null) {
            $trait = $this->findType($this->traits, $qualifiedName);
        }

        return $trait;
    }

    /**
     * Builds a new new interface instance.
     *
     * If there is an existing interface instance for the given name, this method
     * checks if this interface is part of the default namespace. If this is the
     * case this method will update all references to the new interface and it
     * removes the class instance. Otherwise it creates new interface instance.
     *
     * Where possible you should give a qualified interface name, that is
     * prefixed with the package identifier.
     *
     * <code>
     *   $builder->buildInterface('php::depend::Parser');
     * </code>
     *
     * To determine the correct interface, this method implements the following
     * algorithm.
     *
     * <ol>
     *   <li>Check for an exactly matching instance and reuse it.</li>
     *   <li>Check for a interface instance that belongs to the default package.
     *   If such an instance exists, reuse it and replace the default package
     *   with the newly given package information.</li>
     *   <li>Check that the requested interface is in the default package, if
     *   this is true, reuse the first interface instance and ignore the default
     *   package.
     *   </li>
     *   <li>Create a new instance for the specified package.</li>
     * </ol>
     *
     * @since  0.9.5
     */
    protected function buildInterfaceInternal(string $qualifiedName): ASTInterface
    {
        $this->internal = true;

        $interface = $this->buildInterface($qualifiedName);
        $interface->setNamespace(
            $this->buildNamespace($this->extractNamespaceName($qualifiedName) ?? self::DEFAULT_NAMESPACE),
        );

        $this->restoreInterface($interface);

        return $interface;
    }

    /**
     * This method tries to find an interface instance matching for the given
     * qualified name in all scopes already processed. It will return the best
     * matching instance or <b>null</b> if no match exists.
     *
     * @since  0.9.5
     */
    protected function findInterface(string $qualifiedName): ?ASTInterface
    {
        $this->freeze();

        /** @var ASTInterface|null $interface */
        $interface = $this->findType(
            $this->frozenInterfaces,
            $qualifiedName,
        );

        if ($interface === null) {
            $interface = $this->findType(
                $this->interfaces,
                $qualifiedName,
            );
        }

        return $interface;
    }

    /**
     * Builds a new class instance or reuses a previous created class.
     *
     * Where possible you should give a qualified class name, that is prefixed
     * with the package identifier.
     *
     * <code>
     *   $builder->buildClass('php::depend::Parser');
     * </code>
     *
     * To determine the correct class, this method implements the following
     * algorithm.
     *
     * <ol>
     *   <li>Check for an exactly matching instance and reuse it.</li>
     *   <li>Check for a class instance that belongs to the default package. If
     *   such an instance exists, reuse it and replace the default package with
     *   the newly given package information.</li>
     *   <li>Check that the requested class is in the default package, if this
     *   is true, reuse the first class instance and ignore the default package.
     *   </li>
     *   <li>Create a new instance for the specified package.</li>
     * </ol>
     *
     * @since  0.9.5
     */
    protected function buildClassInternal(string $qualifiedName): ASTClass
    {
        $this->internal = true;

        $class = $this->buildClass($qualifiedName);
        $class->setNamespace(
            $this->buildNamespace($this->extractNamespaceName($qualifiedName) ?? self::DEFAULT_NAMESPACE),
        );

        $this->restoreClass($class);

        return $class;
    }

    /**
     * This method tries to find a class instance matching for the given
     * qualified name in all scopes already processed. It will return the best
     * matching instance or <b>null</b> if no match exists.
     *
     * @since  0.9.5
     */
    protected function findClass(string $qualifiedName): null|ASTClass|ASTEnum
    {
        $this->freeze();

        /** @var ASTClass|ASTEnum|null $class */
        $class = $this->findType(
            $this->frozenClasses,
            $qualifiedName,
        );

        if ($class === null) {
            $class = $this->findType($this->classes, $qualifiedName);
        }

        return $class;
    }

    /**
     * This method tries to find an interface or class instance matching for the
     * given qualified name in all scopes already processed. It will return the
     * best matching instance or <b>null</b> if no match exists.
     *
     * @template T of AbstractASTType
     *
     * @param array<string, array<string, array<string, T>>> $instances
     * @return T|null
     * @since  0.9.5
     */
    protected function findType(array $instances, string $qualifiedName): ?AbstractASTType
    {
        $classOrInterfaceName = $this->extractTypeName($qualifiedName);
        $caseInsensitiveName = strtolower($classOrInterfaceName);
        if (!isset($instances[$caseInsensitiveName])) {
            return null;
        }

        $namespaceName = $this->extractNamespaceName($qualifiedName);
        if ($namespaceName === null) {
            return null;
        }

        // Check for exact match and return first matching instance
        if (isset($instances[$caseInsensitiveName][$namespaceName])) {
            return reset($instances[$caseInsensitiveName][$namespaceName]) ?: null;
        }

        if (!$this->isDefault($namespaceName)) {
            return null;
        }

        $classesOrInterfaces = reset($instances[$caseInsensitiveName]);
        if (!$classesOrInterfaces) {
            return null;
        }

        return reset($classesOrInterfaces);
    }

    /**
     * This method will freeze the actual builder state and create a second
     * runtime scope.
     *
     * @since  0.9.5
     */
    protected function freeze(): void
    {
        if ($this->frozen) {
            return;
        }

        $this->frozen = true;

        $this->frozenTraits = $this->copyTypesWithPackage($this->traits);
        $this->frozenClasses = $this->copyTypesWithPackage($this->classes);
        $this->frozenInterfaces = $this->copyTypesWithPackage($this->interfaces);

        $this->traits = [];
        $this->classes = [];
        $this->interfaces = [];
    }

    /**
     * Creates a copy of the given input array, but skips all types that do not
     * contain a parent package.
     *
     * @template T of AbstractASTType
     *
     * @param array<string, array<string, array<string, T>>> $originalTypes The original types created during the
     *                                                                      parsing process.
     * @return array<string, array<string, array<string, T>>>
     */
    private function copyTypesWithPackage(array $originalTypes): array
    {
        $copiedTypes = [];
        foreach ($originalTypes as $typeName => $namespaces) {
            foreach ($namespaces as $namespaceName => $types) {
                foreach ($types as $index => $type) {
                    $copiedTypes[$typeName][$namespaceName][$index] = $type;
                }
            }
        }

        return $copiedTypes;
    }

    /**
     * Restores a function within the internal type scope.
     *
     * @since  0.10.0
     */
    public function restoreFunction(ASTFunction $function): void
    {
        $this->buildNamespace($function->getNamespaceName() ?? self::DEFAULT_NAMESPACE)
            ->addFunction($function);
    }

    /**
     * Restores a trait within the internal type scope.
     *
     * @since  0.10.0
     */
    public function restoreTrait(ASTTrait $trait): void
    {
        $this->storeTrait(
            $trait->getImage(),
            $trait->getNamespaceName(),
            $trait,
        );
    }

    /**
     * Restores a class within the internal type scope.
     *
     * @since  0.10.0
     */
    public function restoreClass(ASTClass $class): void
    {
        $this->storeClass(
            $class->getImage(),
            $class->getNamespaceName(),
            $class,
        );
    }

    /**
     * Restores an enum within the internal type scope.
     *
     * @since  2.11.0
     */
    public function restoreEnum(ASTEnum $enum): void
    {
        $this->storeEnum(
            $enum->getImage(),
            $enum->getNamespaceName(),
            $enum,
        );
    }

    /**
     * Restores an interface within the internal type scope.
     *
     * @since  0.10.0
     */
    public function restoreInterface(ASTInterface $interface): void
    {
        $this->storeInterface(
            $interface->getImage(),
            $interface->getNamespaceName(),
            $interface,
        );
    }

    /**
     * Builds an enum definition.
     *
     * @param string $name The enum name.
     * @param ?ASTScalarType $type The enum type ('string', 'int', or null if not backed).
     * @return ASTEnum The created class object.
     */
    public function buildEnum(string $name, ?ASTScalarType $type = null): ASTEnum
    {
        $this->checkBuilderState();

        $enum = new ASTEnum($name, $type);
        $enum->setCache($this->cache)
            ->setContext($this->context)
            ->setCompilationUnit($this->defaultCompilationUnit);

        return $enum;
    }

    /**
     * Builds an enum definition.
     *
     * @param string $name The enum case name.
     * @param ?ASTNode $value The enum case value if backed.
     * @return ASTEnumCase The created class object.
     */
    public function buildEnumCase(string $name, ?ASTNode $value = null): ASTEnumCase
    {
        $this->checkBuilderState();

        $enumCase = new ASTEnumCase($name);

        if ($value !== null) {
            $enumCase->addChild($value);
        }

        return $enumCase;
    }

    /**
     * This method will persist a trait instance for later reuse.
     *
     * @since 1.0.0
     */
    protected function storeTrait(?string $traitName, ?string $namespaceName, ASTTrait $trait): void
    {
        $traitName = strtolower($traitName ?? '');
        if (!isset($this->traits[$traitName][$namespaceName])) {
            $this->traits[$traitName][$namespaceName] = [];
        }
        $this->traits[$traitName][$namespaceName][$trait->getId()] = $trait;

        $namespace = $this->buildNamespace($namespaceName ?? self::DEFAULT_NAMESPACE);
        $namespace->addType($trait);
    }

    /**
     * This method will persist a class instance for later reuse.
     *
     * @since 0.9.5
     */
    protected function storeClass(?string $className, ?string $namespaceName, ASTClass $class): void
    {
        $className = strtolower($className ?? '');
        if (!isset($this->classes[$className][$namespaceName])) {
            $this->classes[$className][$namespaceName] = [];
        }
        $this->classes[$className][$namespaceName][$class->getId()] = $class;

        $namespace = $this->buildNamespace($namespaceName ?? self::DEFAULT_NAMESPACE);
        $namespace->addType($class);
    }

    /**
     * This method will persist a class instance for later reuse.
     *
     * @since 2.11.0
     */
    protected function storeEnum(?string $enumName, ?string $namespaceName, ASTEnum $enum): void
    {
        $enumName = strtolower($enumName ?? '');
        if (!isset($this->classes[$enumName][$namespaceName])) {
            $this->classes[$enumName][$namespaceName] = [];
        }
        $this->classes[$enumName][$namespaceName][$enum->getId()] = $enum;

        $namespace = $this->buildNamespace($namespaceName ?? self::DEFAULT_NAMESPACE);
        $namespace->addType($enum);
    }

    /**
     * This method will persist an interface instance for later reuse.
     *
     * @since 0.9.5
     */
    protected function storeInterface(?string $interfaceName, ?string $namespaceName, ASTInterface $interface): void
    {
        $interfaceName = strtolower($interfaceName ?? '');
        if (!isset($this->interfaces[$interfaceName][$namespaceName])) {
            $this->interfaces[$interfaceName][$namespaceName] = [];
        }
        $this->interfaces[$interfaceName][$namespaceName][$interface->getId()]
            = $interface;

        $namespace = $this->buildNamespace($namespaceName ?? self::DEFAULT_NAMESPACE);
        $namespace->addType($interface);
    }

    /**
     * Checks that the parser is not frozen or a request is flagged as internal.
     *
     * @param bool $internal The new internal flag value.
     * @throws BadMethodCallException
     * @since  0.9.5
     */
    protected function checkBuilderState(bool $internal = false): void
    {
        if ($this->frozen && !$this->internal) {
            throw new BadMethodCallException(
                'Cannot create new nodes, when internal state is frozen.',
            );
        }
        $this->internal = $internal;
    }

    /**
     * Returns <b>true</b> if the given package is the default package.
     *
     * @param string $namespaceName The package name.
     */
    protected function isDefault(string $namespaceName): bool
    {
        return ($namespaceName === self::DEFAULT_NAMESPACE);
    }

    /**
     * Extracts the type name of a qualified PHP 5.3 type identifier.
     *
     * <code>
     *   $typeName = $this->extractTypeName('foo\bar\foobar');
     *   var_dump($typeName);
     *   // Results in:
     *   // string(6) "foobar"
     * </code>
     *
     * @param string $qualifiedName The qualified PHP 5.3 type identifier.
     */
    protected function extractTypeName(string $qualifiedName): string
    {
        if (($pos = strrpos($qualifiedName, '\\')) !== false) {
            return substr($qualifiedName, $pos + 1);
        }

        return $qualifiedName;
    }

    /**
     * Extracts the package name of a qualified PHP 5.3 class identifier.
     *
     * If the class name doesn't contain a package identifier this method will
     * return the default identifier.
     *
     * <code>
     *   $namespaceName = $this->extractPackageName('foo\bar\foobar');
     *   var_dump($namespaceName);
     *   // Results in:
     *   // string(8) "foo\bar"
     *
     *   $namespaceName = $this->extractPackageName('foobar');
     *   var_dump($namespaceName);
     *   // Results in:
     *   // string(6) "+global"
     * </code>
     *
     * @param string $qualifiedName The qualified PHP 5.3 class identifier.
     */
    protected function extractNamespaceName(string $qualifiedName): ?string
    {
        if (($pos = strrpos($qualifiedName, '\\')) !== false) {
            return ltrim(substr($qualifiedName, 0, $pos), '\\');
        }
        if (Type::isInternalType($qualifiedName)) {
            return Type::getTypePackage($qualifiedName);
        }

        return self::DEFAULT_NAMESPACE;
    }

    /**
     * Creates a {@link ASTNode} instance.
     *
     * @template T of ASTNode
     *
     * @param class-string<T> $className
     * @return T
     * @since 0.9.12
     */
    private function buildAstNodeInstance($className, ?string $image = null): ASTNode
    {
        Log::debug("Creating: {$className}({$image})");

        return new $className($image);
    }
}
