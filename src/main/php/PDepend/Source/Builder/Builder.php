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
use PDepend\Util\Cache\CacheDriver;

/**
 * Base interface for all code node builders.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @template T of mixed
 * @extends \IteratorAggregate<T>
 */
interface Builder extends IteratorAggregate
{
    /**
     * The default package name.
     */
    const DEFAULT_NAMESPACE = '+global';

    /**
     * Setter method for the currently used token cache.
     *
     * @return Builder<mixed>
     *
     * @since  0.10.0
     */
    public function setCache(CacheDriver $cache);

    /**
     * Restores a function within the internal type scope.
     *
     * @return void
     *
     * @since  0.10.0
     */
    public function restoreFunction(ASTFunction $function);

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
    public function getClassOrInterface($qualifiedName);

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     *
     * @return ASTClassOrInterfaceReference
     *
     * @since  0.9.5
     */
    public function buildAstClassOrInterfaceReference($qualifiedName);

    /**
     * Builds a new php trait instance.
     *
     * @param string $qualifiedName
     *
     * @return ASTTrait
     *
     * @since  1.0.0
     */
    public function buildTrait($qualifiedName);

    /**
     * Restores an existing trait instance within the context of this builder.
     *
     * @return void
     *
     * @since  1.0.0
     */
    public function restoreTrait(ASTTrait $trait);

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
    public function getTrait($qualifiedName);

    /**
     * Builds a new code class instance.
     *
     * @param string $qualifiedName
     *
     * @return ASTClass
     */
    public function buildClass($qualifiedName);

    /**
     * Builds an anonymous class instance.
     *
     * @return ASTAnonymousClass
     */
    public function buildAnonymousClass();

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link ASTClass}
     * instance when no matching type exists.
     *
     * @param string $qualifiedName
     *
     * @return ASTClass
     *
     * @since  0.9.5
     */
    public function getClass($qualifiedName);

    /**
     * Restores an existing class instance within the context of this builder.
     *
     * @return void
     *
     * @since  0.10.0
     */
    public function restoreClass(ASTClass $class);

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     *
     * @return ASTClassReference
     *
     * @since  0.9.5
     */
    public function buildAstClassReference($qualifiedName);

    /**
     * Builds a new new interface instance.
     *
     * @param string $qualifiedName
     *
     * @return ASTInterface
     */
    public function buildInterface($qualifiedName);

    /**
     * Restores an existing interface instance within the context of this builder.
     *
     * @return void
     *
     * @since  0.10.0
     */
    public function restoreInterface(ASTInterface $interface);

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link ASTInterface}
     * instance when no matching type exists.
     *
     * @param string $qualifiedName The full qualified type identifier.
     *
     * @return ASTInterface
     *
     * @since  0.9.5
     */
    public function getInterface($qualifiedName);

    /**
     * Builds a new namespace instance.
     *
     * @param string $name
     *
     * @return ASTNamespace
     */
    public function buildNamespace($name);

    /**
     * Builds a new method instance.
     *
     * @param string $name
     *
     * @return ASTMethod
     */
    public function buildMethod($name);

    /**
     * Builds a new function instance.
     *
     * @param string $name
     *
     * @return ASTFunction
     */
    public function buildFunction($name);

    /**
     * Builds a new self reference instance.
     *
     * @return ASTSelfReference
     *
     * @since  0.9.6
     */
    public function buildAstSelfReference(AbstractASTClassOrInterface $type);

    /**
     * Builds a new parent reference instance.
     *
     * @param ASTClassOrInterfaceReference $reference The type instance that reference the concrete target of parent.
     *
     * @return ASTParentReference
     *
     * @since  0.9.6
     */
    public function buildAstParentReference(ASTClassOrInterfaceReference $reference);

    /**
     * Builds a new static reference instance.
     *
     * @return ASTStaticReference
     *
     * @since  0.9.6
     */
    public function buildAstStaticReference(AbstractASTClassOrInterface $owner);

    /**
     * Builds a new field declaration node.
     *
     * @return ASTFieldDeclaration
     *
     * @since  0.9.6
     */
    public function buildAstFieldDeclaration();

    /**
     * Builds a new variable declarator node.
     *
     * @param string $image The source image for the variable declarator.
     *
     * @return ASTVariableDeclarator
     *
     * @since  0.9.6
     */
    public function buildAstVariableDeclarator($image);

    /**
     * Builds a new constant node.
     *
     * @param string $image The source image for the constant.
     *
     * @return ASTConstant
     *
     * @since  0.9.6
     */
    public function buildAstConstant($image);

    /**
     * Builds a new variable node.
     *
     * @param string $image The source image for the variable.
     *
     * @return ASTVariable
     *
     * @since  0.9.6
     */
    public function buildAstVariable($image);

    /**
     * Builds a new variable variable node.
     *
     * @param string $image The source image for the variable variable.
     *
     * @return ASTVariableVariable
     *
     * @since  0.9.6
     */
    public function buildAstVariableVariable($image);

    /**
     * Builds a new compound variable node.
     *
     * @param string $image The source image for the compound variable.
     *
     * @return ASTCompoundVariable
     *
     * @since  0.9.6
     */
    public function buildAstCompoundVariable($image);

    /**
     * Builds a new compound expression node.
     *
     * @return ASTCompoundExpression
     *
     * @since  0.9.6
     */
    public function buildAstCompoundExpression();

    /**
     * Builds a new static variable declaration node.
     *
     * @param string $image The source image for the static declaration.
     *
     * @return ASTStaticVariableDeclaration
     *
     * @since  0.9.6
     */
    public function buildAstStaticVariableDeclaration($image);

    /**
     * Builds a new closure node.
     *
     * @return ASTClosure
     *
     * @since  0.9.12
     */
    public function buildAstClosure();

    /**
     * Builds a new formal parameters node.
     *
     * @return ASTFormalParameters
     *
     * @since  0.9.6
     */
    public function buildAstFormalParameters();

    /**
     * Builds a new formal parameter node.
     *
     * @return ASTFormalParameter
     *
     * @since  0.9.6
     */
    public function buildAstFormalParameter();

    /**
     * Builds a new expression node.
     *
     * @param string $image
     *
     * @return ASTExpression
     *
     * @since 0.9.8
     */
    public function buildAstExpression($image = null);

    /**
     * Builds a new assignment expression node.
     *
     * @param string $image The assignment operator.
     *
     * @return ASTAssignmentExpression
     *
     * @since  0.9.8
     */
    public function buildAstAssignmentExpression($image);

    /**
     * Builds a new allocation expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return ASTAllocationExpression
     *
     * @since  0.9.6
     */
    public function buildAstAllocationExpression($image);

    /**
     * Builds a new eval-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return ASTEvalExpression
     *
     * @since  0.9.12
     */
    public function buildAstEvalExpression($image);

    /**
     * Builds a new exit-expression instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTExitExpression
     *
     * @since  0.9.12
     */
    public function buildAstExitExpression($image);

    /**
     * Builds a new clone-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return ASTCloneExpression
     *
     * @since  0.9.12
     */
    public function buildAstCloneExpression($image);

    /**
     * Builds a new list-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return ASTListExpression
     *
     * @since  0.9.12
     */
    public function buildAstListExpression($image);

    /**
     * Builds a new include- or include_once-expression.
     *
     * @return ASTIncludeExpression
     *
     * @since  0.9.12
     */
    public function buildAstIncludeExpression();

    /**
     * Builds a new require- or require_once-expression.
     *
     * @return ASTRequireExpression
     *
     * @since  0.9.12
     */
    public function buildAstRequireExpression();

    /**
     * Builds a new array-expression node.
     *
     * @return ASTArrayIndexExpression
     *
     * @since  0.9.12
     */
    public function buildAstArrayIndexExpression();

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
    public function buildAstStringIndexExpression();

    /**
     * Builds a new instanceof-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return ASTInstanceOfExpression
     *
     * @since  0.9.6
     */
    public function buildAstInstanceOfExpression($image);

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
    public function buildAstIssetExpression();

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
    public function buildAstConditionalExpression();

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
    public function buildAstPrintExpression();

    /**
     * Build a new shift left expression.
     *
     * @return ASTShiftLeftExpression
     *
     * @since  1.0.1
     */
    public function buildAstShiftLeftExpression();

    /**
     * Build a new shift right expression.
     *
     * @return ASTShiftRightExpression
     *
     * @since  1.0.1
     */
    public function buildAstShiftRightExpression();

    /**
     * Builds a new boolean and-expression.
     *
     * @return ASTBooleanAndExpression
     *
     * @since  0.9.8
     */
    public function buildAstBooleanAndExpression();

    /**
     * Builds a new boolean or-expression.
     *
     * @return ASTBooleanOrExpression
     *
     * @since  0.9.8
     */
    public function buildAstBooleanOrExpression();

    /**
     * Builds a new logical <b>and</b>-expression.
     *
     * @return ASTLogicalAndExpression
     *
     * @since  0.9.8
     */
    public function buildAstLogicalAndExpression();

    /**
     * Builds a new logical <b>or</b>-expression.
     *
     * @return ASTLogicalOrExpression
     *
     * @since  0.9.8
     */
    public function buildAstLogicalOrExpression();

    /**
     * Builds a new logical <b>xor</b>-expression.
     *
     * @return ASTLogicalXorExpression
     *
     * @since  0.9.8
     */
    public function buildAstLogicalXorExpression();

    /**
     * Builds a new trait use-statement node.
     *
     * @return ASTTraitUseStatement
     *
     * @since  1.0.0
     */
    public function buildAstTraitUseStatement();

    /**
     * Builds a new trait adaptation scope.
     *
     * @return ASTTraitAdaptation
     *
     * @since  1.0.0
     */
    public function buildAstTraitAdaptation();

    /**
     * Builds a new trait adaptation alias statement.
     *
     * @param string $image The trait method name.
     *
     * @return ASTTraitAdaptationAlias
     *
     * @since  1.0.0
     */
    public function buildAstTraitAdaptationAlias($image);

    /**
     * Builds a new trait adaptation precedence statement.
     *
     * @param string $image The trait method name.
     *
     * @return ASTTraitAdaptationPrecedence
     *
     * @since  1.0.0
     */
    public function buildAstTraitAdaptationPrecedence($image);

    /**
     * Builds a new trait reference node.
     *
     * @param string $qualifiedName The full qualified trait name.
     *
     * @return ASTTraitReference
     *
     * @since  1.0.0
     */
    public function buildAstTraitReference($qualifiedName);

    /**
     * Builds a new switch-statement-node.
     *
     * @return ASTSwitchStatement
     *
     * @since  0.9.8
     */
    public function buildAstSwitchStatement();

    /**
     * Builds a new switch-label node.
     *
     * @param string $image The source image of this label.
     *
     * @return ASTSwitchLabel
     *
     * @since  0.9.8
     */
    public function buildAstSwitchLabel($image);

    /**
     * Builds a new catch-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTCatchStatement
     *
     * @since  0.9.8
     */
    public function buildAstCatchStatement($image);

    /**
     * Builds a new finally-statement node.
     *
     * @return ASTFinallyStatement
     *
     * @since  2.0.0
     */
    public function buildAstFinallyStatement();

    /**
     * Builds a new if statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTIfStatement
     *
     * @since  0.9.8
     */
    public function buildAstIfStatement($image);

    /**
     * Builds a new elseif-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTElseIfStatement
     *
     * @since  0.9.8
     */
    public function buildAstElseIfStatement($image);

    /**
     * Builds a new for-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTForStatement
     *
     * @since  0.9.8
     */
    public function buildAstForStatement($image);

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
    public function buildAstForInit();

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
    public function buildAstForUpdate();

    /**
     * Builds a new foreach-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTForeachStatement
     *
     * @since  0.9.8
     */
    public function buildAstForeachStatement($image);

    /**
     * Builds a new while-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTWhileStatement
     *
     * @since  0.9.8
     */
    public function buildAstWhileStatement($image);

    /**
     * Builds a new do/while-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return ASTDoWhileStatement
     *
     * @since  0.9.12
     */
    public function buildAstDoWhileStatement($image);

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
    public function buildAstDeclareStatement();

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
    public function buildAstMemberPrimaryPrefix($image);

    /**
     * Builds a new identifier node.
     *
     * @param string $image The image of this identifier.
     *
     * @return ASTIdentifier
     *
     * @since  0.9.6
     */
    public function buildAstIdentifier($image);

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
    public function buildAstFunctionPostfix($image);

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
    public function buildAstMethodPostfix($image);

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
    public function buildAstConstantPostfix($image);

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
    public function buildAstPropertyPostfix($image);

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
    public function buildAstClassFqnPostfix();

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
    public function buildAstArguments();

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
    public function buildAstMatchArgument();

    /**
     * Builds a new argument match expression single-item slot.
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
    public function buildAstMatchBlock();

    /**
     * Builds a new argument match expression single-item slot.
     *
     * <code>
     * "foo" => "bar",
     * </code>
     *
     * @return ASTMatchEntry
     *
     * @since  2.9.0
     */
    public function buildAstMatchEntry();

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
    public function buildAstNamedArgument($name, ASTNode $value);

    /**
     * Builds a new array type node.
     *
     * @return ASTTypeArray
     *
     * @since  0.9.6
     */
    public function buildAstTypeArray();

    /**
     * Builds a new node for the callable type.
     *
     * @return ASTTypeCallable
     *
     * @since  1.0.0
     */
    public function buildAstTypeCallable();

    /**
     * Builds a new node for the iterable type.
     *
     * @return ASTTypeIterable
     *
     * @since  2.5.1
     */
    public function buildAstTypeIterable();

    /**
     * Builds a new primitive type node.
     *
     * @param string $image
     *
     * @return ASTScalarType
     *
     * @since  0.9.6
     */
    public function buildAstScalarType($image);

    /**
     * Builds a new node for the union type.
     *
     * @return ASTUnionType
     *
     * @since  2.9.0
     */
    public function buildAstUnionType();

    /**
     * Builds a new literal node.
     *
     * @param string $image The source image for the literal node.
     *
     * @return ASTLiteral
     *
     * @since  0.9.6
     */
    public function buildAstLiteral($image);

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
    public function buildAstString();

    /**
     * Builds a new php array node.
     *
     * @return ASTArray
     *
     * @since  1.0.0
     */
    public function buildAstArray();

    /**
     * Builds a new array element node.
     *
     * @return ASTArrayElement
     *
     * @since  1.0.0
     */
    public function buildAstArrayElement();

    /**
     * Builds a new heredoc node.
     *
     * @return ASTHeredoc
     *
     * @since  0.9.12
     */
    public function buildAstHeredoc();

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
    public function buildAstConstantDefinition($image);

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
    public function buildAstConstantDeclarator($image);

    /**
     * Builds a new comment node instance.
     *
     * @param string $cdata The comment text.
     *
     * @return ASTComment
     *
     * @since  0.9.8
     */
    public function buildAstComment($cdata);

    /**
     * Builds a new unary expression node instance.
     *
     * @param string $image The unary expression image/character.
     *
     * @return ASTUnaryExpression
     *
     * @since  0.9.11
     */
    public function buildAstUnaryExpression($image);

    /**
     * Builds a new cast-expression node instance.
     *
     * @param string $image The cast-expression image/character.
     *
     * @return ASTCastExpression
     *
     * @since  0.10.0
     */
    public function buildAstCastExpression($image);

    /**
     * Builds a new postfix-expression node instance.
     *
     * @param string $image The postfix-expression image/character.
     *
     * @return ASTPostfixExpression
     *
     * @since  0.10.0
     */
    public function buildAstPostfixExpression($image);

    /**
     * Builds a new pre-increment-expression node instance.
     *
     * @return ASTPreIncrementExpression
     *
     * @since  0.10.0
     */
    public function buildAstPreIncrementExpression();

    /**
     * Builds a new pre-decrement-expression node instance.
     *
     * @return ASTPreDecrementExpression
     *
     * @since  0.10.0
     */
    public function buildAstPreDecrementExpression();

    /**
     * Builds a new function/method scope instance.
     *
     * @return ASTScope
     *
     * @since  0.9.12
     */
    public function buildAstScope();

    /**
     * Builds a new statement instance.
     *
     * @return ASTStatement
     *
     * @since  0.9.12
     */
    public function buildAstStatement();

    /**
     * Builds a new return-statement node instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTReturnStatement
     *
     * @since  0.9.12
     */
    public function buildAstReturnStatement($image);

    /**
     * Builds a new break-statement node instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTBreakStatement
     *
     * @since  0.9.12
     */
    public function buildAstBreakStatement($image);

    /**
     * Builds a new continue-statement node instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTContinueStatement
     *
     * @since  0.9.12
     */
    public function buildAstContinueStatement($image);

    /**
     * Builds a new scope-statement instance.
     *
     * @return ASTScopeStatement
     *
     * @since  0.9.12
     */
    public function buildAstScopeStatement();

    /**
     * Builds a new try-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTTryStatement
     *
     * @since  0.9.12
     */
    public function buildAstTryStatement($image);

    /**
     * Builds a new throw-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTThrowStatement
     *
     * @since  0.9.12
     */
    public function buildAstThrowStatement($image);

    /**
     * Builds a new goto-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTGotoStatement
     *
     * @since  0.9.12
     */
    public function buildAstGotoStatement($image);

    /**
     * Builds a new label-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTLabelStatement
     *
     * @since  0.9.12
     */
    public function buildAstLabelStatement($image);

    /**
     * Builds a new global-statement instance.
     *
     * @return ASTGlobalStatement
     *
     * @since  0.9.12
     */
    public function buildAstGlobalStatement();

    /**
     * Builds a new unset-statement instance.
     *
     * @return ASTUnsetStatement
     *
     * @since  0.9.12
     */
    public function buildAstUnsetStatement();

    /**
     * Builds a new exit-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTEchoStatement
     *
     * @since  0.9.12
     */
    public function buildAstEchoStatement($image);

    /**
     * Builds a new yield-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return ASTYieldStatement
     *
     * @since  $version$
     */
    public function buildAstYieldStatement($image);
}
