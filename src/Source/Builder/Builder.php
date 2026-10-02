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

namespace PDepend\Source\Builder;

use IteratorAggregate;
use PDepend\Source\AST\AbstractASTClassOrInterface;
use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTAnonymousClass;
use PDepend\Source\AST\ASTArguments;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTArrayElement;
use PDepend\Source\AST\ASTArrayIndexExpression;
use PDepend\Source\AST\ASTAssignmentExpression;
use PDepend\Source\AST\ASTAttribute;
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
use PDepend\Source\AST\ASTPipe;
use PDepend\Source\AST\ASTPostfixExpression;
use PDepend\Source\AST\ASTPreDecrementExpression;
use PDepend\Source\AST\ASTPreIncrementExpression;
use PDepend\Source\AST\ASTPrintExpression;
use PDepend\Source\AST\ASTPropertyHook;
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
use PDepend\Util\Cache\CacheDriver;

/**
 * Base interface for all code node builders.
 *
 * New build methods are added whenever the parser learns new syntax, so this
 * interface is only meant to be implemented by PDepend itself. Calling it is
 * covered by the compatibility promise, implementing it is not.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @template T
 *
 * @extends IteratorAggregate<T>
 */
interface Builder extends IteratorAggregate
{
    /** The default package name. */
    public const DEFAULT_NAMESPACE = '+global';

    /**
     * Setter method for the currently used token cache.
     *
     * @return $this
     * @api
     * @since  0.10.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function setCache(CacheDriver $cache): self;

    /**
     * Restores a function within the internal type scope.
     *
     * @since  0.10.0
     */
    public function restoreFunction(ASTFunction $function): void;

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link ASTClass}
     * instance when no matching type exists.
     *
     * @since  0.9.5
     */
    public function getClassOrInterface(string $qualifiedName): AbstractASTClassOrInterface;

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     * @api
     * @since  0.9.5
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstClassOrInterfaceReference(string $qualifiedName): ASTClassOrInterfaceReference;

    /**
     * Builds a new php trait instance.
     *
     * @api
     * @since  1.0.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildTrait(string $qualifiedName): ASTTrait;

    /**
     * Restores an existing trait instance within the context of this builder.
     *
     * @since  1.0.0
     */
    public function restoreTrait(ASTTrait $trait): void;

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link ASTTrait}
     * instance when no matching type exists.
     *
     * @since  1.0.0
     */
    public function getTrait(string $qualifiedName): ASTTrait;

    /**
     * Builds a new code class instance.
     *
     * @api
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildClass(string $qualifiedName): ASTClass;

    /**
     * Builds an anonymous class instance.
     *
     * @api
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAnonymousClass(): ASTAnonymousClass;

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link ASTClass}
     * instance when no matching type exists.
     *
     * @since  0.9.5
     */
    public function getClass(string $qualifiedName): ASTClass|ASTEnum;

    /**
     * Restores an existing class instance within the context of this builder.
     *
     * @since  0.10.0
     */
    public function restoreClass(ASTClass $class): void;

    /**
     * Restores an enum within the internal type scope.
     *
     * @since  2.11.0
     */
    public function restoreEnum(ASTEnum $enum): void;

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     * @api
     * @since  0.9.5
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstClassReference(string $qualifiedName): ASTClassReference;

    /**
     * Builds a new new interface instance.
     *
     * @api
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildInterface(string $qualifiedName): ASTInterface;

    /**
     * Restores an existing interface instance within the context of this builder.
     *
     * @since  0.10.0
     */
    public function restoreInterface(ASTInterface $interface): void;

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link ASTInterface}
     * instance when no matching type exists.
     *
     * @param string $qualifiedName The full qualified type identifier.
     * @api
     * @since  0.9.5
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function getInterface(string $qualifiedName): ASTInterface;

    /**
     * Builds a new namespace instance.
     *
     * @api
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildNamespace(string $name): ASTNamespace;

    /**
     * Builds a new method instance.
     *
     * @api
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildMethod(string $name): ASTMethod;

    /**
     * Builds a new property hook instance.
     *
     * @api
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildPropertyHook(string $name): ASTPropertyHook;

    /**
     * Builds a new function instance.
     *
     * @api
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildFunction(string $name): ASTFunction;

    /**
     * Builds a new self reference instance.
     *
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstSelfReference(AbstractASTClassOrInterface $type): ASTSelfReference;

    /**
     * Builds a new parent reference instance.
     *
     * @param ASTClassOrInterfaceReference $reference The type instance that reference the concrete target of parent.
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstParentReference(ASTClassOrInterfaceReference $reference): ASTParentReference;

    /**
     * Builds a new static reference instance.
     *
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstStaticReference(AbstractASTClassOrInterface $owner): ASTStaticReference;

    /**
     * Builds a new field declaration node.
     *
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstFieldDeclaration(): ASTFieldDeclaration;

    /**
     * Builds a new variable declarator node.
     *
     * @param string $image The source image for the variable declarator.
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstVariableDeclarator(string $image): ASTVariableDeclarator;

    /**
     * Builds a new constant node.
     *
     * @param string $image The source image for the constant.
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstConstant(string $image): ASTConstant;

    /**
     * Builds a new variable node.
     *
     * @param string $image The source image for the variable.
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstVariable(string $image): ASTVariable;

    /**
     * Builds a new variable variable node.
     *
     * @param string $image The source image for the variable variable.
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstVariableVariable(string $image): ASTVariableVariable;

    /**
     * Builds a new compound variable node.
     *
     * @param string $image The source image for the compound variable.
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstCompoundVariable(string $image): ASTCompoundVariable;

    /**
     * Builds a new compound expression node.
     *
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstCompoundExpression(): ASTCompoundExpression;

    /**
     * Builds a new static variable declaration node.
     *
     * @param string $image The source image for the static declaration.
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstStaticVariableDeclaration(string $image): ASTStaticVariableDeclaration;

    /**
     * Builds a new closure node.
     *
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstClosure(): ASTClosure;

    /**
     * Builds a new formal parameters node.
     *
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstFormalParameters(): ASTFormalParameters;

    /**
     * Builds a new formal parameter node.
     *
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstFormalParameter(): ASTFormalParameter;

    /**
     * Builds a new expression node.
     *
     * @api
     * @since 0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstExpression(?string $image = null): ASTExpression;

    /**
     * Builds a new assignment expression node.
     *
     * @param string $image The assignment operator.
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstAssignmentExpression(string $image): ASTAssignmentExpression;

    /**
     * Builds a new allocation expression node.
     *
     * @param string $image The source image of this expression.
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstAllocationExpression(string $image): ASTAllocationExpression;

    /**
     * Builds a new eval-expression node.
     *
     * @param string $image The source image of this expression.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstEvalExpression(string $image): ASTEvalExpression;

    /**
     * Builds a new exit-expression instance.
     *
     * @param string $image The source code image for this node.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstExitExpression(string $image): ASTExitExpression;

    /**
     * Builds a new clone-expression node.
     *
     * @param string $image The source image of this expression.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstCloneExpression(string $image): ASTCloneExpression;

    /**
     * Builds a new list-expression node.
     *
     * @param string $image The source image of this expression.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstListExpression(string $image): ASTListExpression;

    /**
     * Builds a new include- or include_once-expression.
     *
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstIncludeExpression(): ASTIncludeExpression;

    /**
     * Builds a new require- or require_once-expression.
     *
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstRequireExpression(): ASTRequireExpression;

    /**
     * Builds a new array-expression node.
     *
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstArrayIndexExpression(): ASTArrayIndexExpression;

    /**
     * Builds a new instanceof-expression node.
     *
     * @param string $image The source image of this expression.
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstInstanceOfExpression(string $image): ASTInstanceOfExpression;

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
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstIssetExpression(): ASTIssetExpression;

    /**
     * Builds a new boolean conditional-expression.
     *
     * <code>
     *         --------------
     * $bar = ($foo ? 42 : 23);
     *         --------------
     * </code>
     *
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstConditionalExpression(): ASTConditionalExpression;

    /**
     * Builds a new print-expression.
     *
     * <code>
     * -------------
     * print "qafoo";
     * -------------
     * </code>
     *
     * @api
     * @since 2.3
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstPrintExpression(): ASTPrintExpression;

    /**
     * Build a new shift left expression.
     *
     * @api
     * @since  1.0.1
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstShiftLeftExpression(): ASTShiftLeftExpression;

    /**
     * Build a new shift right expression.
     *
     * @api
     * @since  1.0.1
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstShiftRightExpression(): ASTShiftRightExpression;

    /**
     * Builds a new boolean and-expression.
     *
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstBooleanAndExpression(): ASTBooleanAndExpression;

    /**
     * Builds a new pipe expression.
     *
     * @api
     * @since  3.0.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildASTPipe(): ASTPipe;

    /**
     * Builds a new boolean or-expression.
     *
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstBooleanOrExpression(): ASTBooleanOrExpression;

    /**
     * Builds a new logical <b>and</b>-expression.
     *
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstLogicalAndExpression(): ASTLogicalAndExpression;

    /**
     * Builds a new logical <b>or</b>-expression.
     *
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstLogicalOrExpression(): ASTLogicalOrExpression;

    /**
     * Builds a new logical <b>xor</b>-expression.
     *
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstLogicalXorExpression(): ASTLogicalXorExpression;

    /**
     * Builds a new trait use-statement node.
     *
     * @api
     * @since  1.0.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstTraitUseStatement(): ASTTraitUseStatement;

    /**
     * Builds a new trait adaptation scope.
     *
     * @api
     * @since  1.0.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstTraitAdaptation(): ASTTraitAdaptation;

    /**
     * Builds a new trait adaptation alias statement.
     *
     * @param string $image The trait method name.
     * @api
     * @since  1.0.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstTraitAdaptationAlias(string $image): ASTTraitAdaptationAlias;

    /**
     * Builds a new trait adaptation precedence statement.
     *
     * @param string $image The trait method name.
     * @api
     * @since  1.0.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstTraitAdaptationPrecedence(string $image): ASTTraitAdaptationPrecedence;

    /**
     * Builds a new trait reference node.
     *
     * @param string $qualifiedName The full qualified trait name.
     * @api
     * @since  1.0.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstTraitReference(string $qualifiedName): ASTTraitReference;

    /**
     * Builds a new switch-statement-node.
     *
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstSwitchStatement(): ASTSwitchStatement;

    /**
     * Builds a new switch-label node.
     *
     * @param string $image The source image of this label.
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstSwitchLabel(string $image): ASTSwitchLabel;

    /**
     * Builds a new catch-statement node.
     *
     * @param string $image The source image of this statement.
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstCatchStatement(string $image): ASTCatchStatement;

    /**
     * Builds a new finally-statement node.
     *
     * @api
     * @since  2.0.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstFinallyStatement(): ASTFinallyStatement;

    /**
     * Builds a new if statement node.
     *
     * @param string $image The source image of this statement.
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstIfStatement(string $image): ASTIfStatement;

    /**
     * Builds a new elseif-statement node.
     *
     * @param string $image The source image of this statement.
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstElseIfStatement(string $image): ASTElseIfStatement;

    /**
     * Builds a new for-statement node.
     *
     * @param string $image The source image of this statement.
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstForStatement(string $image): ASTForStatement;

    /**
     * Builds a new for-init node.
     *
     * <code>
     *      ------------------------
     * for ($x = 0, $y = 23, $z = 42; $x < $y; ++$x) {}
     *      ------------------------
     * </code>
     *
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstForInit(): ASTForInit;

    /**
     * Builds a new for-update node.
     *
     * <code>
     *                                        -------------------------------
     * for ($x = 0, $y = 23, $z = 42; $x < $y; ++$x, $y = $x + 1, $z = $x + 2) {}
     *                                        -------------------------------
     * </code>
     *
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstForUpdate(): ASTForUpdate;

    /**
     * Builds a new foreach-statement node.
     *
     * @param string $image The source image of this statement.
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstForeachStatement(string $image): ASTForeachStatement;

    /**
     * Builds a new while-statement node.
     *
     * @param string $image The source image of this statement.
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstWhileStatement(string $image): ASTWhileStatement;

    /**
     * Builds a new do/while-statement node.
     *
     * @param string $image The source image of this statement.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstDoWhileStatement(string $image): ASTDoWhileStatement;

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
     * @api
     * @since  0.10.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstDeclareStatement(): ASTDeclareStatement;

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
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstMemberPrimaryPrefix(string $image): ASTMemberPrimaryPrefix;

    /**
     * Builds a new identifier node.
     *
     * @param string $image The image of this identifier.
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstIdentifier(string $image): ASTIdentifier;

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
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstFunctionPostfix(string $image): ASTFunctionPostfix;

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
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstMethodPostfix(string $image): ASTMethodPostfix;

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
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstConstantPostfix(string $image): ASTConstantPostfix;

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
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstPropertyPostfix(string $image): ASTPropertyPostfix;

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
     * @api
     * @since  2.0.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstClassFqnPostfix(): ASTClassFqnPostfix;

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
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstArguments(): ASTArguments;

    /**
     * Builds a new argument match expression single-item slot.
     *
     * <code>
     * match($x)
     * </code>
     *
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstMatchArgument(): ASTMatchArgument;

    /**
     * Builds a new argument match expression single-item slot.
     *
     * <code>
     * match($x) {
     *   "foo" => "bar",
     * }
     * </code>
     *
     * @api
     * @since  2.9.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstMatchBlock(): ASTMatchBlock;

    /**
     * Builds a new argument match expression single-item slot.
     *
     * <code>
     * "foo" => "bar",
     * </code>
     *
     * @api
     * @since  2.9.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstMatchEntry(): ASTMatchEntry;

    /**
     * Builds a new named argument node.
     *
     * <code>
     * number_format(5623, thousands_separator: ' ')
     * </code>
     *
     * @api
     * @since  2.9.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstNamedArgument(string $name, ASTNode $value): ASTNamedArgument;

    /**
     * Builds a new array type node.
     *
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstTypeArray(): ASTTypeArray;

    /**
     * Builds a new node for the callable type.
     *
     * @api
     * @since  1.0.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstTypeCallable(): ASTTypeCallable;

    /**
     * Builds a new node for the iterable type.
     *
     * @api
     * @since  2.5.1
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstTypeIterable(): ASTTypeIterable;

    /**
     * Builds a new primitive type node.
     *
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstScalarType(string $image): ASTScalarType;

    /**
     * Builds a new node for the union type.
     *
     * @api
     * @since  2.9.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstUnionType(): ASTUnionType;

    /**
     * Builds a new node for the intersection type.
     *
     * @api
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstIntersectionType(): ASTIntersectionType;

    /**
     * Builds a new literal node.
     *
     * @param string $image The source image for the literal node.
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstLiteral(string $image): ASTLiteral;

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
     * @api
     * @since  0.9.10
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstString(): ASTString;

    /**
     * Builds a new php array node.
     *
     * @api
     * @since  1.0.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstArray(): ASTArray;

    /**
     * Builds a new array element node.
     *
     * @api
     * @since  1.0.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstArrayElement(): ASTArrayElement;

    /**
     * Builds a new heredoc node.
     *
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstHeredoc(): ASTHeredoc;

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
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstConstantDefinition(string $image): ASTConstantDefinition;

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
     * @api
     * @since  0.9.6
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstConstantDeclarator(string $image): ASTConstantDeclarator;

    /**
     * Builds a new comment node instance.
     *
     * @param string $cdata The comment text.
     * @api
     * @since  0.9.8
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstComment(string $cdata): ASTComment;

    /**
     * Builds a new attribute node instance.
     *
     * @param string $image The attribute text.
     * @api
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstAttribute(string $image): ASTAttribute;

    /**
     * Builds a new unary expression node instance.
     *
     * @param string $image The unary expression image/character.
     * @api
     * @since  0.9.11
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstUnaryExpression(string $image): ASTUnaryExpression;

    /**
     * Builds a new cast-expression node instance.
     *
     * @param string $image The cast-expression image/character.
     * @api
     * @since  0.10.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstCastExpression(string $image): ASTCastExpression;

    /**
     * Builds a new postfix-expression node instance.
     *
     * @param string $image The postfix-expression image/character.
     * @api
     * @since  0.10.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstPostfixExpression(string $image): ASTPostfixExpression;

    /**
     * Builds a new pre-increment-expression node instance.
     *
     * @api
     * @since  0.10.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstPreIncrementExpression(): ASTPreIncrementExpression;

    /**
     * Builds a new pre-decrement-expression node instance.
     *
     * @api
     * @since  0.10.0
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstPreDecrementExpression(): ASTPreDecrementExpression;

    /**
     * Builds a new function/method scope instance.
     *
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstScope(): ASTScope;

    /**
     * Builds a new statement instance.
     *
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstStatement(): ASTStatement;

    /**
     * Builds a new return-statement node instance.
     *
     * @param string $image The source code image for this node.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstReturnStatement(string $image): ASTReturnStatement;

    /**
     * Builds a new break-statement node instance.
     *
     * @param string $image The source code image for this node.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstBreakStatement(string $image): ASTBreakStatement;

    /**
     * Builds a new continue-statement node instance.
     *
     * @param string $image The source code image for this node.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstContinueStatement(string $image): ASTContinueStatement;

    /**
     * Builds a new scope-statement instance.
     *
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstScopeStatement(): ASTScopeStatement;

    /**
     * Builds a new try-statement instance.
     *
     * @param string $image The source code image for this node.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstTryStatement(string $image): ASTTryStatement;

    /**
     * Builds a new throw-statement instance.
     *
     * @param string $image The source code image for this node.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstThrowStatement(string $image): ASTThrowStatement;

    /**
     * Builds a new goto-statement instance.
     *
     * @param string $image The source code image for this node.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstGotoStatement(string $image): ASTGotoStatement;

    /**
     * Builds a new label-statement instance.
     *
     * @param string $image The source code image for this node.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstLabelStatement(string $image): ASTLabelStatement;

    /**
     * Builds a new global-statement instance.
     *
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstGlobalStatement(): ASTGlobalStatement;

    /**
     * Builds a new unset-statement instance.
     *
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstUnsetStatement(): ASTUnsetStatement;

    /**
     * Builds a new exit-statement instance.
     *
     * @param string $image The source code image for this node.
     * @api
     * @since  0.9.12
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstEchoStatement(string $image): ASTEchoStatement;

    /**
     * Builds a new yield-statement instance.
     *
     * @param string $image The source code image for this node.
     * @api
     * @since  $version$
     * @deprecated Will be removed from the interface in PDepend 4.0, use PHPBuilder directly.
     */
    public function buildAstYieldStatement(string $image): ASTYieldStatement;
}
