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
use PDepend\Source\AST\AbstractASTNode;
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
use ReturnTypeWillChange;

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
     * @var CacheDriver
     *
     * @since 0.10.0
     */
    protected $cache = null;

    /**
     * The ast builder context.
     *
     * @var BuilderContext
     *
     * @since 0.10.0
     */
    protected $context = null;

    /**
     * This property holds all packages found during the parsing phase.
     *
     * @var ASTNamespace[]
     *
     * @since 0.9.12
     */
    private $preparedNamespaces = null;

    /**
     * Default package which contains all functions and classes with an unknown
     * scope.
     *
     * @var ASTNamespace
     */
    protected $defaultPackage = null;

    /**
     * Default source file that acts as a dummy.
     *
     * @var ASTCompilationUnit
     */
    protected $defaultCompilationUnit = null;

    /**
     * All generated {@link ASTTrait} objects
     *
     * @var array<string, array<string, array<int, ASTTrait>>>
     */
    private $traits = array();

    /**
     * All generated {@link ASTClass} objects
     *
     * @var array<string, array<string, array<int, ASTClass>>>
     */
    private $classes = array();

    /**
     * All generated {@link ASTInterface} instances.
     *
     * @var array<string, array<string, array<int, ASTInterface>>>
     */
    private $interfaces = array();

    /**
     * All generated {@link ASTNamespace} objects
     *
     * @var ASTNamespace[]
     */
    private $namespaces = array();

    /**
     * Internal status flag used to check that a build request is internal.
     *
     * @var bool
     */
    private $internal = false;

    /**
     * Internal used flag that marks the parsing process as frozen.
     *
     * @var bool
     */
    private $frozen = false;

    /**
     * Cache of all traits created during the regular parsing process.
     *
     * @var array<string, array<string, array<int, ASTTrait>>>
     */
    private $frozenTraits = array();

    /**
     * Cache of all classes created during the regular parsing process.
     *
     * @var array<string, array<string, array<int, ASTClass>>>
     */
    private $frozenClasses = array();

    /**
     * Cache of all interfaces created during the regular parsing process.
     *
     * @var array<string, array<string, array<int, ASTInterface>>>
     */
    private $frozenInterfaces = array();

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
     *
     * @since  0.10.0
     */
    public function setCache(CacheDriver $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     *
     * @return ASTClassOrInterfaceReference
     *
     * @since  0.9.5
     */
    public function buildAstClassOrInterfaceReference($qualifiedName)
    {
        $this->checkBuilderState();

        // Debug method creation
        Log::debug(
            'Creating: \\PDepend\\Source\\AST\\ASTClassOrInterfaceReference(' .
            $qualifiedName .
            ')'
        );

        return new ASTClassOrInterfaceReference($this->context, $qualifiedName);
    }

    /**
     * Builds a new code type reference instance, either Class or ClassOrInterface.
     *
     * @param string $qualifiedName  The qualified name of the referenced type.
     * @param bool   $classReference true if class reference only.
     *
     * @return ASTClassOrInterfaceReference
     *
     * @since  0.9.5
     */
    public function buildAstNeededReference($qualifiedName, $classReference)
    {
        if ($classReference === true) {
            return $this->buildAstClassReference($qualifiedName);
        }

        return $this->buildAstClassOrInterfaceReference($qualifiedName);
    }

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link ASTClass}
     * instance when no matching type exists.
     *
     * @param string $qualifiedName
     *
     * @return AbstractASTClassOrInterface
     *
     * @since  0.9.5
     */
    public function getClassOrInterface($qualifiedName)
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
     *
     * @return ASTTrait
     *
     * @since 1.0.0
     */
    public function buildTrait($qualifiedName)
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
     * @param string $qualifiedName
     *
     * @return ASTTrait
     *
     * @since  1.0.0
     */
    public function getTrait($qualifiedName)
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
     *
     * @return ASTTraitReference
     *
     * @since  1.0.0
     */
    public function buildAstTraitReference($qualifiedName)
    {
        $this->checkBuilderState();

        Log::debug(
            'Creating: \\PDepend\\Source\\AST\\ASTTraitReference(' . $qualifiedName . ')'
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
     *
     * @return ASTClass The created class object.
     */
    public function buildClass($name)
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
     *
     * @return ASTClass
     *
     * @since  0.9.5
     */
    public function getClass($qualifiedName)
    {
        return $this->findClass($qualifiedName)
            ?: $this->buildClassInternal($qualifiedName);
    }

    /**
     * Builds an anonymous class instance.
     *
     * @return ASTAnonymousClass
     */
    public function buildAnonymousClass()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTAnonymousClass')
            ->setCache($this->cache)
            ->setContext($this->context);
    }

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     *
     * @return ASTClassReference
     *
     * @since  0.9.5
     */
    public function buildAstClassReference($qualifiedName)
    {
        $this->checkBuilderState();

        // Debug method creation
        Log::debug(
            'Creating: \\PDepend\\Source\\AST\\ASTClassReference(' . $qualifiedName . ')'
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
     *
     * @return ASTInterface
     */
    public function buildInterface($name)
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
     * @param string $qualifiedName
     *
     * @return ASTInterface
     *
     * @since  0.9.5
     */
    public function getInterface($qualifiedName)
    {
        $interface = $this->findInterface($qualifiedName);
        if ($interface === null) {
            $interface = $this->buildInterfaceInternal($qualifiedName);
        }
        return $interface;
    }

    /**
     * Builds a new method instance.
     *
     * @param string $name
     *
     * @return ASTMethod
     */
    public function buildMethod($name)
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
     *
     * @return ASTNamespace
     */
    public function buildNamespace($name)
    {
        if (!isset($this->namespaces[$name])) {
            // Debug package creation
            Log::debug(
                'Creating: \\PDepend\\Source\\AST\\ASTNamespace(' . $name . ')'
            );

            $this->namespaces[$name] = new ASTNamespace($name);
        }
        return $this->namespaces[$name];
    }

    /**
     * Builds a new function instance.
     *
     * @param string $name The function name.
     *
     * @return ASTFunction
     */
    public function buildFunction($name)
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
     * @return ASTSelfReference
     *
     * @since  0.9.6
     */
    public function buildAstSelfReference(AbstractASTClassOrInterface $type)
    {
        Log::debug(
            'Creating: \\PDepend\\Source\\AST\\ASTSelfReference(' . $type->getName() . ')'
        );

        return new ASTSelfReference($this->context, $type);
    }

    /**
     * Builds a new parent reference instance.
     *
     * @param ASTClassOrInterfaceReference $reference The type
     *                                                instance that reference the concrete target of parent.
     *
     * @return ASTParentReference
     *
     * @since  0.9.6
     */
    public function buildAstParentReference(ASTClassOrInterfaceReference $reference)
    {
        Log::debug('Creating: \\PDepend\\Source\\AST\\ASTParentReference()');

        return new ASTParentReference($reference);
    }

    /**
     * Builds a new static reference instance.
     *
     * @return ASTStaticReference
     *
     * @since  0.9.6
     */
    public function buildAstStaticReference(AbstractASTClassOrInterface $owner)
    {
        Log::debug('Creating: \\PDepend\\Source\\AST\\ASTStaticReference()');

        return new ASTStaticReference($this->context, $owner);
    }

    /**
     * Builds a new field declaration node.
     *
     * @return ASTFieldDeclaration
     *
     * @since  0.9.6
     */
    public function buildAstFieldDeclaration()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTFieldDeclaration');
    }

    /**
     * Builds a new variable declarator node.
     *
     * @param string $image The source image for the variable declarator.
     *
     * @return ASTVariableDeclarator
     *
     * @since  0.9.6
     */
    public function buildAstVariableDeclarator($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTVariableDeclarator', $image);
    }

    /**
     * Builds a new static variable declaration node.
     *
     * @param string $image The source image for the statuc declaration.
     *
     * @return ASTStaticVariableDeclaration
     *
     * @since  0.9.6
     */
    public function buildAstStaticVariableDeclaration($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTStaticVariableDeclaration', $image);
    }

    /**
     * Builds a new constant node.
     *
     * @param string $image The source image for the constant.
     *
     * @return ASTConstant
     *
     * @since  0.9.6
     */
    public function buildAstConstant($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTConstant', $image);
    }

    /**
     * Builds a new variable node.
     *
     * @param string $image The source image for the variable.
     *
     * @return ASTVariable
     *
     * @since  0.9.6
     */
    public function buildAstVariable($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTVariable', $image);
    }

    /**
     * Builds a new variable variable node.
     *
     * @param string $image The source image for the variable variable.
     *
     * @return ASTVariableVariable
     *
     * @since  0.9.6
     */
    public function buildAstVariableVariable($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTVariableVariable', $image);
    }

    /**
     * Builds a new compound variable node.
     *
     * @param string $image The source image for the compound variable.
     *
     * @return ASTCompoundVariable
     *
     * @since  0.9.6
     */
    public function buildAstCompoundVariable($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTCompoundVariable', $image);
    }

    /**
     * Builds a new compound expression node.
     *
     * @return ASTCompoundExpression
     *
     * @since  0.9.6
     */
    public function buildAstCompoundExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTCompoundExpression');
    }

    /**
     * Builds a new closure node.
     *
     * @return ASTClosure
     *
     * @since  0.9.12
     */
    public function buildAstClosure()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTClosure');
    }

    /**
     * Builds a new formal parameters node.
     *
     * @return ASTFormalParameters
     *
     * @since  0.9.6
     */
    public function buildAstFormalParameters()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTFormalParameters');
    }

    /**
     * Builds a new formal parameter node.
     *
     * @return ASTFormalParameter
     *
     * @since  0.9.6
     */
    public function buildAstFormalParameter()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTFormalParameter');
    }

    /**
     * Builds a new expression node.
     *
     * @param string $image
     *
     * @return ASTExpression
     *
     * @since 0.9.8
     */
    public function buildAstExpression($image = null)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTExpression', $image);
    }

    /**
     * Builds a new assignment expression node.
     *
     * @param string $image The assignment operator.
     *
     * @return ASTAssignmentExpression
     *
     * @since  0.9.8
     */
    public function buildAstAssignmentExpression($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTAssignmentExpression', $image);
    }

    /**
     * Builds a new allocation expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return ASTAllocationExpression
     *
     * @since  0.9.6
     */
    public function buildAstAllocationExpression($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTAllocationExpression', $image);
    }

    /**
     * Builds a new eval-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return ASTEvalExpression
     *
     * @since  0.9.12
     */
    public function buildAstEvalExpression($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTEvalExpression', $image);
    }

    /**
     * Builds a new exit-expression instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTExitExpression
     *
     * @since  0.9.12
     */
    public function buildAstExitExpression($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTExitExpression', $image);
    }

    /**
     * Builds a new clone-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return ASTCloneExpression
     *
     * @since  0.9.12
     */
    public function buildAstCloneExpression($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTCloneExpression', $image);
    }

    /**
     * Builds a new list-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return ASTListExpression
     *
     * @since  0.9.12
     */
    public function buildAstListExpression($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTListExpression', $image);
    }

    /**
     * Builds a new include- or include_once-expression.
     *
     * @return ASTIncludeExpression
     *
     * @since  0.9.12
     */
    public function buildAstIncludeExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTIncludeExpression');
    }

    /**
     * Builds a new require- or require_once-expression.
     *
     * @return ASTRequireExpression
     *
     * @since  0.9.12
     */
    public function buildAstRequireExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTRequireExpression');
    }

    /**
     * Builds a new array-expression node.
     *
     * @return ASTArrayIndexExpression
     *
     * @since  0.9.12
     */
    public function buildAstArrayIndexExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTArrayIndexExpression');
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
     * @return ASTStringIndexExpression
     *
     * @since  0.9.12
     */
    public function buildAstStringIndexExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTStringIndexExpression');
    }

    /**
     * Builds a new php array node.
     *
     * @return ASTArray
     *
     * @since  1.0.0
     */
    public function buildAstArray()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTArray');
    }

    /**
     * Builds a new array element node.
     *
     * @return ASTArrayElement
     *
     * @since  1.0.0
     */
    public function buildAstArrayElement()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTArrayElement');
    }


    /**
     * Builds a new instanceof expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return ASTInstanceOfExpression
     *
     * @since  0.9.6
     */
    public function buildAstInstanceOfExpression($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTInstanceOfExpression', $image);
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
     * @return ASTIssetExpression
     *
     * @since  0.9.12
     */
    public function buildAstIssetExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTIssetExpression');
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
     * @return ASTConditionalExpression
     *
     * @since  0.9.8
     */
    public function buildAstConditionalExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTConditionalExpression', '?');
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
     * @return ASTPrintExpression
     *
     * @since 2.3
     */
    public function buildAstPrintExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTPrintExpression', 'print');
    }

    /**
     * Build a new shift left expression.
     *
     * @return ASTShiftLeftExpression
     *
     * @since  1.0.1
     */
    public function buildAstShiftLeftExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTShiftLeftExpression');
    }

    /**
     * Build a new shift right expression.
     *
     * @return ASTShiftRightExpression
     *
     * @since  1.0.1
     */
    public function buildAstShiftRightExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTShiftRightExpression');
    }

    /**
     * Builds a new boolean and-expression.
     *
     * @return ASTBooleanAndExpression
     *
     * @since  0.9.8
     */
    public function buildAstBooleanAndExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTBooleanAndExpression', '&&');
    }

    /**
     * Builds a new boolean or-expression.
     *
     * @return ASTBooleanOrExpression
     *
     * @since  0.9.8
     */
    public function buildAstBooleanOrExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTBooleanOrExpression', '||');
    }

    /**
     * Builds a new logical <b>and</b>-expression.
     *
     * @return ASTLogicalAndExpression
     *
     * @since  0.9.8
     */
    public function buildAstLogicalAndExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTLogicalAndExpression', 'and');
    }

    /**
     * Builds a new logical <b>or</b>-expression.
     *
     * @return ASTLogicalOrExpression
     *
     * @since  0.9.8
     */
    public function buildAstLogicalOrExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTLogicalOrExpression', 'or');
    }

    /**
     * Builds a new logical <b>xor</b>-expression.
     *
     * @return ASTLogicalXorExpression
     *
     * @since  0.9.8
     */
    public function buildAstLogicalXorExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTLogicalXorExpression', 'xor');
    }

    /**
     * Builds a new trait use-statement node.
     *
     * @return ASTTraitUseStatement
     *
     * @since  1.0.0
     */
    public function buildAstTraitUseStatement()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTTraitUseStatement');
    }

    /**
     * Builds a new trait adaptation scope
     *
     * @return ASTTraitAdaptation
     *
     * @since  1.0.0
     */
    public function buildAstTraitAdaptation()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTTraitAdaptation');
    }

    /**
     * Builds a new trait adaptation alias statement.
     *
     * @param string $image The trait method name.
     *
     * @return ASTTraitAdaptationAlias
     *
     * @since  1.0.0
     */
    public function buildAstTraitAdaptationAlias($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTTraitAdaptationAlias', $image);
    }

    /**
     * Builds a new trait adaptation precedence statement.
     *
     * @param string $image The trait method name.
     *
     * @return ASTTraitAdaptationPrecedence
     *
     * @since  1.0.0
     */
    public function buildAstTraitAdaptationPrecedence($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTTraitAdaptationPrecedence', $image);
    }

    /**
     * Builds a new switch-statement-node.
     *
     * @return ASTSwitchStatement
     *
     * @since  0.9.8
     */
    public function buildAstSwitchStatement()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTSwitchStatement');
    }

    /**
     * Builds a new switch-label node.
     *
     * @param string $image The source image of this label.
     *
     * @return ASTSwitchLabel
     *
     * @since  0.9.8
     */
    public function buildAstSwitchLabel($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTSwitchLabel', $image);
    }

    /**
     * Builds a new global-statement instance.
     *
     * @return ASTGlobalStatement
     *
     * @since  0.9.12
     */
    public function buildAstGlobalStatement()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTGlobalStatement');
    }

    /**
     * Builds a new unset-statement instance.
     *
     * @return ASTUnsetStatement
     *
     * @since  0.9.12
     */
    public function buildAstUnsetStatement()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTUnsetStatement');
    }

    /**
     * Builds a new catch-statement node.
     *
     * @param string $image
     *
     * @return ASTCatchStatement
     *
     * @since  0.9.8
     */
    public function buildAstCatchStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTCatchStatement', $image);
    }

    /**
     * Builds a new finally-statement node.
     *
     * @return ASTFinallyStatement
     *
     * @since  2.0.0
     */
    public function buildAstFinallyStatement()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTFinallyStatement', 'finally');
    }

    /**
     * Builds a new if statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTIfStatement
     *
     * @since  0.9.8
     */
    public function buildAstIfStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTIfStatement', $image);
    }

    /**
     * Builds a new elseif statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTElseIfStatement
     *
     * @since  0.9.8
     */
    public function buildAstElseIfStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTElseIfStatement', $image);
    }

    /**
     * Builds a new for statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTForStatement
     *
     * @since  0.9.8
     */
    public function buildAstForStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTForStatement', $image);
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
     * @return ASTForInit
     *
     * @since  0.9.8
     */
    public function buildAstForInit()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTForInit');
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
     * @return ASTForUpdate
     *
     * @since  0.9.12
     */
    public function buildAstForUpdate()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTForUpdate');
    }

    /**
     * Builds a new foreach-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTForeachStatement
     *
     * @since  0.9.8
     */
    public function buildAstForeachStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTForeachStatement', $image);
    }

    /**
     * Builds a new while-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTWhileStatement
     *
     * @since  0.9.8
     */
    public function buildAstWhileStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTWhileStatement', $image);
    }

    /**
     * Builds a new do/while-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTDoWhileStatement
     *
     * @since  0.9.12
     */
    public function buildAstDoWhileStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTDoWhileStatement', $image);
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
     * @return ASTDeclareStatement
     *
     * @since  0.10.0
     */
    public function buildAstDeclareStatement()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTDeclareStatement');
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
     *
     * @return ASTMemberPrimaryPrefix
     *
     * @since  0.9.6
     */
    public function buildAstMemberPrimaryPrefix($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $image);
    }

    /**
     * Builds a new identifier node.
     *
     * @param string $image The image of this identifier.
     *
     * @return ASTIdentifier
     *
     * @since  0.9.6
     */
    public function buildAstIdentifier($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTIdentifier', $image);
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
     *
     * @return ASTFunctionPostfix
     *
     * @since  0.9.6
     */
    public function buildAstFunctionPostfix($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTFunctionPostfix', $image);
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
     *
     * @return ASTMethodPostfix
     *
     * @since  0.9.6
     */
    public function buildAstMethodPostfix($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTMethodPostfix', $image);
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
     *
     * @return ASTConstantPostfix
     *
     * @since  0.9.6
     */
    public function buildAstConstantPostfix($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTConstantPostfix', $image);
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
     *
     * @return ASTPropertyPostfix
     *
     * @since  0.9.6
     */
    public function buildAstPropertyPostfix($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTPropertyPostfix', $image);
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
     * @return ASTClassFqnPostfix
     *
     * @since  2.0.0
     */
    public function buildAstClassFqnPostfix()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTClassFqnPostfix', 'class');
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
     * @return ASTArguments
     *
     * @since  0.9.6
     */
    public function buildAstArguments()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTArguments');
    }

    /**
     * Builds a new argument match expression single-item slot.
     *
     * <code>
     * match($x)
     * </code>
     *
     * @return ASTMatchArgument
     *
     * @since  0.9.6
     */
    public function buildAstMatchArgument()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTMatchArgument');
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
     * @return ASTMatchBlock
     *
     * @since  2.9.0
     */
    public function buildAstMatchBlock()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTMatchBlock');
    }

    /**
     * Builds a new match item.
     *
     * <code>
     * "foo" => "bar",
     * </code>
     *
     * @return ASTMatchEntry
     *
     * @since  2.9.0
     */
    public function buildAstMatchEntry()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTMatchEntry');
    }

    /**
     * Builds a new named argument node.
     *
     * <code>
     * number_format(5623, thousands_separator: ' ')
     * </code>
     *
     * @param string $name
     *
     * @return ASTNamedArgument
     *
     * @since  2.9.0
     */
    public function buildAstNamedArgument($name, ASTNode $value)
    {
        Log::debug("Creating: \\PDepend\\Source\\AST\\ASTNamedArgument($name)");

        return new ASTNamedArgument($name, $value);
    }

    /**
     * Builds a new array type node.
     *
     * @return ASTTypeArray
     *
     * @since  0.9.6
     */
    public function buildAstTypeArray()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTTypeArray');
    }

    /**
     * Builds a new node for the callable type.
     *
     * @return ASTTypeCallable
     *
     * @since  1.0.0
     */
    public function buildAstTypeCallable()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTTypeCallable');
    }

    /**
     * Builds a new node for the iterable type.
     *
     * @return ASTTypeIterable
     *
     * @since  2.5.1
     */
    public function buildAstTypeIterable()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTTypeIterable');
    }

    /**
     * Builds a new primitive type node.
     *
     * @param string $image The source image for the primitive type.
     *
     * @return ASTScalarType
     *
     * @since  0.9.6
     */
    public function buildAstScalarType($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTScalarType', $image);
    }

    /**
     * Builds a new node for the union type.
     *
     * @return ASTUnionType
     *
     * @since  1.0.0
     */
    public function buildAstUnionType()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTUnionType');
    }

    /**
     * Builds a new node for the union type.
     *
     * @return ASTIntersectionType
     */
    public function buildAstIntersectionType()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTIntersectionType');
    }

    /**
     * Builds a new literal node.
     *
     * @param string $image The source image for the literal node.
     *
     * @return ASTLiteral
     *
     * @since  0.9.6
     */
    public function buildAstLiteral($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTLiteral', $image);
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
     * @return ASTString
     *
     * @since  0.9.10
     */
    public function buildAstString()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTString');
    }

    /**
     * Builds a new heredoc node.
     *
     * @return ASTHeredoc
     *
     * @since  0.9.12
     */
    public function buildAstHeredoc()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTHeredoc');
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
     *
     * @return ASTConstantDefinition
     *
     * @since  0.9.6
     */
    public function buildAstConstantDefinition($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTConstantDefinition', $image);
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
     *
     * @return ASTConstantDeclarator
     *
     * @since  0.9.6
     */
    public function buildAstConstantDeclarator($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTConstantDeclarator', $image);
    }

    /**
     * Builds a new comment node instance.
     *
     * @param string $cdata The comment text.
     *
     * @return ASTComment
     *
     * @since  0.9.8
     */
    public function buildAstComment($cdata)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTComment', $cdata);
    }

    /**
     * Builds a new unary expression node instance.
     *
     * @param string $image The unary expression image/character.
     *
     * @return ASTUnaryExpression
     *
     * @since  0.9.11
     */
    public function buildAstUnaryExpression($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTUnaryExpression', $image);
    }

    /**
     * Builds a new cast-expression node instance.
     *
     * @param string $image The cast-expression image/character.
     *
     * @return ASTCastExpression
     *
     * @since  0.10.0
     */
    public function buildAstCastExpression($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTCastExpression', $image);
    }

    /**
     * Builds a new postfix-expression node instance.
     *
     * @param string $image The postfix-expression image/character.
     *
     * @return ASTPostfixExpression
     *
     * @since  0.10.0
     */
    public function buildAstPostfixExpression($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTPostfixExpression', $image);
    }

    /**
     * Builds a new pre-increment-expression node instance.
     *
     * @return ASTPreIncrementExpression
     *
     * @since  0.10.0
     */
    public function buildAstPreIncrementExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTPreIncrementExpression');
    }

    /**
     * Builds a new pre-decrement-expression node instance.
     *
     * @return ASTPreDecrementExpression
     *
     * @since  0.10.0
     */
    public function buildAstPreDecrementExpression()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTPreDecrementExpression');
    }

    /**
     * Builds a new function/method scope instance.
     *
     * @return ASTScope
     *
     * @since  0.9.12
     */
    public function buildAstScope()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTScope');
    }

    /**
     * Builds a new statement instance.
     *
     * @return ASTStatement
     *
     * @since  0.9.12
     */
    public function buildAstStatement()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTStatement');
    }

    /**
     * Builds a new return statement node instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTReturnStatement
     *
     * @since  0.9.12
     */
    public function buildAstReturnStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTReturnStatement', $image);
    }

    /**
     * Builds a new break-statement node instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTBreakStatement
     *
     * @since  0.9.12
     */
    public function buildAstBreakStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTBreakStatement', $image);
    }

    /**
     * Builds a new continue-statement node instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTContinueStatement
     *
     * @since  0.9.12
     */
    public function buildAstContinueStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTContinueStatement', $image);
    }

    /**
     * Builds a new scope-statement instance.
     *
     * @return ASTScopeStatement
     *
     * @since  0.9.12
     */
    public function buildAstScopeStatement()
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTScopeStatement');
    }

    /**
     * Builds a new try-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTTryStatement
     *
     * @since  0.9.12
     */
    public function buildAstTryStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTTryStatement', $image);
    }

    /**
     * Builds a new throw-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTThrowStatement
     *
     * @since  0.9.12
     */
    public function buildAstThrowStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTThrowStatement', $image);
    }

    /**
     * Builds a new goto-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTGotoStatement
     *
     * @since  0.9.12
     */
    public function buildAstGotoStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTGotoStatement', $image);
    }

    /**
     * Builds a new label-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTLabelStatement
     *
     * @since  0.9.12
     */
    public function buildAstLabelStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTLabelStatement', $image);
    }

    /**
     * Builds a new exit-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTEchoStatement
     *
     * @since  0.9.12
     */
    public function buildAstEchoStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTEchoStatement', $image);
    }

    /**
     * Builds a new yield-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTYieldStatement
     *
     * @since  $version$
     */
    public function buildAstYieldStatement($image)
    {
        return $this->buildAstNodeInstance('\\PDepend\\Source\\AST\\ASTYieldStatement', $image);
    }

    /**
     * Returns an iterator with all generated {@link ASTNamespace}
     * objects.
     *
     * @return ASTArtifactList<ASTNamespace>
     */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return $this->getNamespaces();
    }

    /**
     * Returns an iterator with all generated {@link ASTNamespace}
     * objects.
     *
     * @return ASTArtifactList<ASTNamespace>
     */
    public function getNamespaces()
    {
        if ($this->preparedNamespaces === null) {
            $this->preparedNamespaces = $this->getPreparedNamespaces();
        }
        return new ASTArtifactList($this->preparedNamespaces);
    }

    /**
     * Returns an iterator with all generated {@link ASTNamespace}
     * objects.
     *
     * @return ASTNamespace[]
     *
     * @since  0.9.12
     */
    private function getPreparedNamespaces()
    {
        // Create a package array copy
        $namespaces = $this->namespaces;

        // Remove default package if empty
        if (count($this->defaultPackage->getTypes()) === 0
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
     * @param string $qualifiedName
     *
     * @return ASTTrait
     *
     * @since  0.9.5
     */
    protected function buildTraitInternal($qualifiedName)
    {
        $this->internal = true;

        $trait = $this->buildTrait($qualifiedName);
        $trait->setNamespace(
            $this->buildNamespace($this->extractNamespaceName($qualifiedName))
        );

        $this->restoreTrait($trait);

        return $trait;
    }

    /**
     * This method tries to find a trait instance matching for the given
     * qualified name in all scopes already processed. It will return the best
     * matching instance or <b>null</b> if no match exists.
     *
     * @param string $qualifiedName
     *
     * @return ASTTrait|null
     *
     * @since  0.9.5
     */
    protected function findTrait($qualifiedName)
    {
        $this->freeze();

        /** @var ASTTrait|null $trait */
        $trait = $this->findType(
            $this->frozenTraits,
            $qualifiedName
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
     * @param string $qualifiedName
     *
     * @return ASTInterface
     *
     * @since  0.9.5
     */
    protected function buildInterfaceInternal($qualifiedName)
    {
        $this->internal = true;

        $interface = $this->buildInterface($qualifiedName);
        $interface->setNamespace(
            $this->buildNamespace($this->extractNamespaceName($qualifiedName))
        );

        $this->restoreInterface($interface);

        return $interface;
    }

    /**
     * This method tries to find an interface instance matching for the given
     * qualified name in all scopes already processed. It will return the best
     * matching instance or <b>null</b> if no match exists.
     *
     * @param string $qualifiedName
     *
     * @return ASTInterface|null
     *
     * @since  0.9.5
     */
    protected function findInterface($qualifiedName)
    {
        $this->freeze();

        /** @var ASTInterface|null $interface */
        $interface = $this->findType(
            $this->frozenInterfaces,
            $qualifiedName
        );

        if ($interface === null) {
            $interface = $this->findType(
                $this->interfaces,
                $qualifiedName
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
     * @param string $qualifiedName
     *
     * @return ASTClass
     *
     * @since  0.9.5
     */
    protected function buildClassInternal($qualifiedName)
    {
        $this->internal = true;

        $class = $this->buildClass($qualifiedName);
        $class->setNamespace(
            $this->buildNamespace($this->extractNamespaceName($qualifiedName))
        );

        $this->restoreClass($class);

        return $class;
    }

    /**
     * This method tries to find a class instance matching for the given
     * qualified name in all scopes already processed. It will return the best
     * matching instance or <b>null</b> if no match exists.
     *
     * @param string $qualifiedName
     *
     * @return ASTClass|null
     *
     * @since  0.9.5
     */
    protected function findClass($qualifiedName)
    {
        $this->freeze();

        /** @var ASTClass|null $class */
        $class = $this->findType(
            $this->frozenClasses,
            $qualifiedName
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
     * @param array<string, array<string, array<int, T>>> $instances
     * @param string                                         $qualifiedName
     *
     * @return T|null
     *
     * @since  0.9.5
     */
    protected function findType(array $instances, $qualifiedName)
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
     * @return void
     *
     * @since  0.9.5
     */
    protected function freeze()
    {
        if ($this->frozen === true) {
            return;
        }

        $this->frozen = true;

        $this->frozenTraits     = $this->copyTypesWithPackage($this->traits);
        $this->frozenClasses    = $this->copyTypesWithPackage($this->classes);
        $this->frozenInterfaces = $this->copyTypesWithPackage($this->interfaces);

        $this->traits     = array();
        $this->classes    = array();
        $this->interfaces = array();
    }

    /**
     * Creates a copy of the given input array, but skips all types that do not
     * contain a parent package.
     *
     * @template T of AbstractASTType
     *
     * @param array<string, array<string, array<int, T>>> $originalTypes The original types created during the parsing
     *                                                                   process.
     *
     * @return array<string, array<string, array<int, T>>>
     */
    private function copyTypesWithPackage(array $originalTypes)
    {
        $copiedTypes = array();
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
     * @return void
     *
     * @since  0.10.0
     */
    public function restoreFunction(ASTFunction $function)
    {
        $this->buildNamespace($function->getNamespaceName())
            ->addFunction($function);
    }

    /**
     * Restores a trait within the internal type scope.
     *
     * @return void
     *
     * @since  0.10.0
     */
    public function restoreTrait(ASTTrait $trait)
    {
        $this->storeTrait(
            $trait->getName(),
            $trait->getNamespaceName(),
            $trait
        );
    }

    /**
     * Restores a class within the internal type scope.
     *
     * @return void
     *
     * @since  0.10.0
     */
    public function restoreClass(ASTClass $class)
    {
        $this->storeClass(
            $class->getName(),
            $class->getNamespaceName(),
            $class
        );
    }

    /**
     * Restores an enum within the internal type scope.
     *
     * @return void
     *
     * @since  2.11.0
     */
    public function restoreEnum(ASTEnum $enum)
    {
        $this->storeEnum(
            $enum->getName(),
            $enum->getNamespaceName(),
            $enum
        );
    }

    /**
     * Restores an interface within the internal type scope.
     *
     * @return void
     *
     * @since  0.10.0
     */
    public function restoreInterface(ASTInterface $interface)
    {
        $this->storeInterface(
            $interface->getName(),
            $interface->getNamespaceName(),
            $interface
        );
    }

    /**
     * Builds an enum definition.
     *
     * @param string        $name The enum name.
     * @param ASTScalarType $type The enum type ('string', 'int', or null if not backed).
     *
     * @return ASTEnum The created class object.
     */
    public function buildEnum($name, ASTScalarType $type = null)
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
     * @param string          $name  The enum case name.
     * @param AbstractASTNode $value The enum case value if backed.
     *
     * @return ASTEnumCase The created class object.
     */
    public function buildEnumCase($name, AbstractASTNode $value = null)
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
     * @param string $traitName
     * @param string $namespaceName
     *
     * @return void
     *
     * @since 1.0.0
     */
    protected function storeTrait($traitName, $namespaceName, ASTTrait $trait)
    {
        $traitName = strtolower($traitName);
        if (!isset($this->traits[$traitName][$namespaceName])) {
            $this->traits[$traitName][$namespaceName] = array();
        }
        $this->traits[$traitName][$namespaceName][$trait->getId()] = $trait;

        $namespace = $this->buildNamespace($namespaceName);
        $namespace->addType($trait);
    }

    /**
     * This method will persist a class instance for later reuse.
     *
     * @param string $className
     * @param string $namespaceName
     *
     * @return void
     *
     * @since 0.9.5
     */
    protected function storeClass($className, $namespaceName, ASTClass $class)
    {
        $className = strtolower($className);
        if (!isset($this->classes[$className][$namespaceName])) {
            $this->classes[$className][$namespaceName] = array();
        }
        $this->classes[$className][$namespaceName][$class->getId()] = $class;

        $namespace = $this->buildNamespace($namespaceName);
        $namespace->addType($class);
    }

    /**
     * This method will persist a class instance for later reuse.
     *
     * @param string $enumName
     * @param string $namespaceName
     *
     * @return void
     *
     * @since 2.11.0
     */
    protected function storeEnum($enumName, $namespaceName, ASTEnum $enum)
    {
        $enumName = strtolower($enumName);
        if (!isset($this->classes[$enumName][$namespaceName])) {
            $this->classes[$enumName][$namespaceName] = array();
        }
        $this->classes[$enumName][$namespaceName][$enum->getId()] = $enum;

        $namespace = $this->buildNamespace($namespaceName);
        $namespace->addType($enum);
    }

    /**
     * This method will persist an interface instance for later reuse.
     *
     * @param string $interfaceName
     * @param string $namespaceName
     *
     * @return void
     *
     * @since 0.9.5
     */
    protected function storeInterface($interfaceName, $namespaceName, ASTInterface $interface)
    {
        $interfaceName = strtolower($interfaceName);
        if (!isset($this->interfaces[$interfaceName][$namespaceName])) {
            $this->interfaces[$interfaceName][$namespaceName] = array();
        }
        $this->interfaces[$interfaceName][$namespaceName][$interface->getId()]
            = $interface;

        $namespace = $this->buildNamespace($namespaceName);
        $namespace->addType($interface);
    }

    /**
     * Checks that the parser is not frozen or a request is flagged as internal.
     *
     * @param bool $internal The new internal flag value.
     *
     * @throws BadMethodCallException
     *
     * @return void
     *
     * @since  0.9.5
     */
    protected function checkBuilderState($internal = false)
    {
        if ($this->frozen === true && $this->internal === false) {
            throw new BadMethodCallException(
                'Cannot create new nodes, when internal state is frozen.'
            );
        }
        $this->internal = $internal;
    }


    /**
     * Returns <b>true</b> if the given package is the default package.
     *
     * @param string $namespaceName The package name.
     *
     * @return bool
     */
    protected function isDefault($namespaceName)
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
     *
     * @return string
     */
    protected function extractTypeName($qualifiedName)
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
     *
     * @return string|null
     */
    protected function extractNamespaceName($qualifiedName)
    {
        if (($pos = strrpos($qualifiedName, '\\')) !== false) {
            return ltrim(substr($qualifiedName, 0, $pos), '\\');
        } elseif (Type::isInternalType($qualifiedName)) {
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
     * @param string          $image
     *
     * @return T
     *
     * @since 0.9.12
     */
    private function buildAstNodeInstance($className, $image = null)
    {
        Log::debug("Creating: {$className}({$image})");

        return new $className($image);
    }
}
