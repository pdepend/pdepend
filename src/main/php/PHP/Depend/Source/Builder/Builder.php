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
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
  */

namespace PHP\Depend\Source\Builder;

use PHP\Depend\Source\AST\AbstractASTClassOrInterface;
use PHP\Depend\Source\AST\ASTClass;
use PHP\Depend\Source\AST\ASTFunction;
use PHP\Depend\Source\AST\ASTInterface;
use PHP\Depend\Source\AST\ASTTrait;
use PHP\Depend\Util\Cache\Driver;

/**
 * Base interface for all code node builders.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
interface Builder extends \IteratorAggregate
{
    /**
     * The default package name.
     */
    const DEFAULT_PACKAGE = '+global';

    /**
     * Setter method for the currently used token cache.
     *
     * @param \PHP\Depend\Util\Cache\Driver $cache Used token cache instance.
     * @return \PHP\Depend\Source\Builder\Builder
     * @since 0.10.0
     */
    function setCache(Driver $cache);

    /**
     * Restores a function within the internal type scope.
     *
     * @param \PHP\Depend\Source\AST\ASTFunction $function
     * @return void
     * @since 0.10.0
     */
    function restoreFunction(ASTFunction $function);

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link \PHP\Depend\Source\AST\ASTClass}
     * instance when no matching type exists.
     *
     * @param string $qualifiedName
     * @return \PHP\Depend\Source\AST\AbstractASTClassOrInterface
     * @since 0.9.5
     */
    function getClassOrInterface($qualifiedName);

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     *
     * @return \PHP\Depend\Source\AST\ASTClassOrInterfaceReference
     * @since 0.9.5
     */
    function buildAstClassOrInterfaceReference($qualifiedName);

    /**
     * Builds a new php trait instance.
     *
     * @param string $qualifiedName
     * @return \PHP\Depend\Source\AST\ASTTrait
     * @since 1.0.0
     */
    function buildTrait($qualifiedName);

    /**
     * Restores an existing trait instance within the context of this builder.
     *
     * @param \PHP\Depend\Source\AST\ASTTrait $trait
     * @return void
     * @since 1.0.0
     */
    function restoreTrait(ASTTrait $trait);

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link \PHP\Depend\Source\AST\ASTTrait}
     * instance when no matching type exists.
     *
     * @param string $qualifiedName
     * @return \PHP\Depend\Source\AST\ASTTrait
     * @since 1.0.0
     */
    function getTrait($qualifiedName);

    /**
     * Builds a new code class instance.
     *
     * @param string $qualifiedName
     * @return \PHP\Depend\Source\AST\ASTClass
     */
    function buildClass($qualifiedName);

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link \PHP\Depend\Source\AST\ASTClass}
     * instance when no matching type exists.
     *
     * @param string $qualifiedName
     * @return \PHP\Depend\Source\AST\ASTClass
     * @since 0.9.5
     */
    function getClass($qualifiedName);

    /**
     * Restores an existing class instance within the context of this builder.
     *
     * @param \PHP\Depend\Source\AST\ASTClass $class
     * @return void
     * @since 0.10.0
     */
    function restoreClass(ASTClass $class);

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     * @return \PHP\Depend\Source\AST\ASTClassReference
     * @since 0.9.5
     */
    function buildAstClassReference($qualifiedName);

    /**
     * Builds a new new interface instance.
     *
     * @param string $qualifiedName
     * @return \PHP\Depend\Source\AST\ASTInterface
     */
    function buildInterface($qualifiedName);

    /**
     * Restores an existing interface instance within the context of this builder.
     *
     * @param \PHP\Depend\Source\AST\ASTInterface $interface
     * @return void
     * @since 0.10.0
     */
    function restoreInterface(ASTInterface $interface);

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link \PHP\Depend\Source\AST\ASTInterface}
     * instance when no matching type exists.
     *
     * @param string $qualifiedName The full qualified type identifier.
     * @return \PHP\Depend\Source\AST\ASTInterface
     * @since 0.9.5
     */
    function getInterface($qualifiedName);

    /**
     * Builds a new package instance.
     *
     * @param string $name The package name.
     * @return \PHP\Depend\Source\AST\ASTNamespace The created package object.
     */
    function buildPackage($name);

    /**
     * Builds a new method instance.
     *
     * @param string $name
     * @return \PHP\Depend\Source\AST\ASTMethod
     */
    function buildMethod($name);

    /**
     * Builds a new function instance.
     *
     * @param string $name
     * @return \PHP\Depend\Source\AST\ASTFunction
     */
    function buildFunction($name);

    /**
     * Builds a new self reference instance.
     *
     * @param \PHP\Depend\Source\AST\AbstractASTClassOrInterface $type
     * @return \PHP\Depend\Source\AST\ASTSelfReference
     * @since 0.9.6
     */
    function buildAstSelfReference(AbstractASTClassOrInterface $type);

    /**
     * Builds a new parent reference instance.
     *
     * @param \PHP\Depend\Source\AST\ASTClassOrInterfaceReference $reference The type
     *        instance that reference the concrete target of parent.
     *
     * @return \PHP\Depend\Source\AST\ASTParentReference
     * @since 0.9.6
     */
    function buildAstParentReference(
        \PHP\Depend\Source\AST\ASTClassOrInterfaceReference $reference
    );

    /**
     * Builds a new static reference instance.
     *
     * @param \PHP\Depend\Source\AST\AbstractASTClassOrInterface $owner
     * @return \PHP\Depend\Source\AST\ASTStaticReference
     * @since 0.9.6
     */
    function buildAstStaticReference(AbstractASTClassOrInterface $owner);

    /**
     * Builds a new field declaration node.
     *
     * @return \PHP\Depend\Source\AST\ASTFieldDeclaration
     * @since 0.9.6
     */
    function buildAstFieldDeclaration();

    /**
     * Builds a new variable declarator node.
     *
     * @param string $image The source image for the variable declarator.
     *
     * @return \PHP\Depend\Source\AST\ASTVariableDeclarator
     * @since 0.9.6
     */
    function buildAstVariableDeclarator($image);

    /**
     * Builds a new constant node.
     *
     * @param string $image The source image for the constant.
     *
     * @return \PHP\Depend\Source\AST\ASTConstant
     * @since 0.9.6
     */
    function buildAstConstant($image);

    /**
     * Builds a new variable node.
     *
     * @param string $image The source image for the variable.
     *
     * @return \PHP\Depend\Source\AST\ASTVariable
     * @since 0.9.6
     */
    function buildAstVariable($image);

    /**
     * Builds a new variable variable node.
     *
     * @param string $image The source image for the variable variable.
     *
     * @return \PHP\Depend\Source\AST\ASTVariableVariable
     * @since 0.9.6
     */
    function buildAstVariableVariable($image);

    /**
     * Builds a new compound variable node.
     *
     * @param string $image The source image for the compound variable.
     *
     * @return \PHP\Depend\Source\AST\ASTCompoundVariable
     * @since 0.9.6
     */
    function buildAstCompoundVariable($image);

    /**
     * Builds a new compound expression node.
     *
     * @return \PHP\Depend\Source\AST\ASTCompoundExpression
     * @since 0.9.6
     */
    function buildAstCompoundExpression();

    /**
     * Builds a new static variable declaration node.
     *
     * @param string $image The source image for the static declaration.
     *
     * @return \PHP\Depend\Source\AST\ASTStaticVariableDeclaration
     * @since 0.9.6
     */
    function buildAstStaticVariableDeclaration($image);

    /**
     * Builds a new closure node.
     *
     * @return \PHP\Depend\Source\AST\ASTClosure
     * @since 0.9.12
     */
    function buildAstClosure();

    /**
     * Builds a new formal parameters node.
     *
     * @return \PHP\Depend\Source\AST\ASTFormalParameters
     * @since 0.9.6
     */
    function buildAstFormalParameters();

    /**
     * Builds a new formal parameter node.
     *
     * @return \PHP\Depend\Source\AST\ASTFormalParameter
     * @since 0.9.6
     */
    function buildAstFormalParameter();

    /**
     * Builds a new expression node.
     *
     * @return \PHP\Depend\Source\AST\ASTExpression
     * @since 0.9.8
     */
    function buildAstExpression();

    /**
     * Builds a new assignment expression node.
     *
     * @param string $image The assignment operator.
     *
     * @return \PHP\Depend\Source\AST\ASTAssignmentExpression
     * @since 0.9.8
     */
    function buildAstAssignmentExpression($image);

    /**
     * Builds a new allocation expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return \PHP\Depend\Source\AST\ASTAllocationExpression
     * @since 0.9.6
     */
    function buildAstAllocationExpression($image);

    /**
     * Builds a new eval-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return \PHP\Depend\Source\AST\ASTEvalExpression
     * @since 0.9.12
     */
    function buildAstEvalExpression($image);

    /**
     * Builds a new exit-expression instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return \PHP\Depend\Source\AST\ASTExitExpression
     * @since 0.9.12
     */
    function buildAstExitExpression($image);

    /**
     * Builds a new clone-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return \PHP\Depend\Source\AST\ASTCloneExpression
     * @since 0.9.12
     */
    function buildAstCloneExpression($image);

    /**
     * Builds a new list-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return \PHP\Depend\Source\AST\ASTListExpression
     * @since 0.9.12
     */
    function buildAstListExpression($image);

    /**
     * Builds a new include- or include_once-expression.
     *
     * @return \PHP\Depend\Source\AST\ASTIncludeExpression
     * @since 0.9.12
     */
    function buildAstIncludeExpression();

    /**
     * Builds a new require- or require_once-expression.
     *
     * @return \PHP\Depend\Source\AST\ASTRequireExpression
     * @since 0.9.12
     */
    function buildAstRequireExpression();

    /**
     * Builds a new array-expression node.
     *
     * @return \PHP\Depend\Source\AST\ASTArrayIndexExpression
     * @since 0.9.12
     */
    function buildAstArrayIndexExpression();

    /**
     * Builds a new string-expression node.
     *
     * <code>
     * //     --------
     * $string{$index}
     * //     --------
     * </code>
     *
     * @return \PHP\Depend\Source\AST\ASTStringIndexExpression
     * @since 0.9.12
     */
    function buildAstStringIndexExpression();

    /**
     * Builds a new instanceof-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return \PHP\Depend\Source\AST\ASTInstanceOfExpression
     * @since 0.9.6
     */
    function buildAstInstanceOfExpression($image);

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
     * @return \PHP\Depend\Source\AST\ASTIssetExpression
     * @since 0.9.12
     */
    function buildAstIssetExpression();

    /**
     * Builds a new boolean conditional-expression.
     *
     * <code>
     *         --------------
     * $bar = ($foo ? 42 : 23);
     *         --------------
     * </code>
     *
     * @return \PHP\Depend\Source\AST\ASTConditionalExpression
     * @since 0.9.8
     */
    function buildAstConditionalExpression();

    /**
     * Build a new shift left expression.
     *
     * @return \PHP\Depend\Source\AST\ASTShiftLeftExpression
     * @since 1.0.1
     */
    function buildAstShiftLeftExpression();

    /**
     * Build a new shift right expression.
     *
     * @return \PHP\Depend\Source\AST\ASTShiftRightExpression
     * @since 1.0.1
     */
    function buildAstShiftRightExpression();

    /**
     * Builds a new boolean and-expression.
     *
     * @return \PHP\Depend\Source\AST\ASTBooleanAndExpression
     * @since 0.9.8
     */
    function buildAstBooleanAndExpression();

    /**
     * Builds a new boolean or-expression.
     *
     * @return \PHP\Depend\Source\AST\ASTBooleanOrExpression
     * @since 0.9.8
     */
    function buildAstBooleanOrExpression();

    /**
     * Builds a new logical <b>and</b>-expression.
     *
     * @return \PHP\Depend\Source\AST\ASTLogicalAndExpression
     * @since 0.9.8
     */
    function buildAstLogicalAndExpression();

    /**
     * Builds a new logical <b>or</b>-expression.
     *
     * @return \PHP\Depend\Source\AST\ASTLogicalOrExpression
     * @since 0.9.8
     */
    function buildAstLogicalOrExpression();

    /**
     * Builds a new logical <b>xor</b>-expression.
     *
     * @return \PHP\Depend\Source\AST\ASTLogicalXorExpression
     * @since 0.9.8
     */
    function buildAstLogicalXorExpression();

    /**
     * Builds a new trait use-statement node.
     *
     * @return \PHP\Depend\Source\AST\ASTTraitUseStatement
     * @since 1.0.0
     */
    function buildAstTraitUseStatement();

    /**
     * Builds a new trait adaptation scope.
     *
     * @return \PHP\Depend\Source\AST\ASTTraitAdaptation
     * @since 1.0.0
     */
    function buildAstTraitAdaptation();

    /**
     * Builds a new trait adaptation alias statement.
     *
     * @param string $image The trait method name.
     *
     * @return \PHP\Depend\Source\AST\ASTTraitAdaptationAlias
     * @since 1.0.0
     */
    function buildAstTraitAdaptationAlias($image);

    /**
     * Builds a new trait adaptation precedence statement.
     *
     * @param string $image The trait method name.
     *
     * @return \PHP\Depend\Source\AST\ASTTraitAdaptationPrecedence
     * @since 1.0.0
     */
    function buildAstTraitAdaptationPrecedence($image);

    /**
     * Builds a new trait reference node.
     *
     * @param string $qualifiedName The full qualified trait name.
     *
     * @return \PHP\Depend\Source\AST\ASTTraitReference
     * @since 1.0.0
     */
    function buildAstTraitReference($qualifiedName);

    /**
     * Builds a new switch-statement-node.
     *
     * @return \PHP\Depend\Source\AST\ASTSwitchStatement
     * @since 0.9.8
     */
    function buildAstSwitchStatement();

    /**
     * Builds a new switch-label node.
     *
     * @param string $image The source image of this label.
     *
     * @return \PHP\Depend\Source\AST\ASTSwitchLabel
     * @since 0.9.8
     */
    function buildAstSwitchLabel($image);

    /**
     * Builds a new catch-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return \PHP\Depend\Source\AST\ASTCatchStatement
     * @since 0.9.8
     */
    function buildAstCatchStatement($image);

    /**
     * Builds a new if statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return \PHP\Depend\Source\AST\ASTIfStatement
     * @since 0.9.8
     */
    function buildAstIfStatement($image);

    /**
     * Builds a new elseif-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return \PHP\Depend\Source\AST\ASTElseIfStatement
     * @since 0.9.8
     */
    function buildAstElseIfStatement($image);

    /**
     * Builds a new for-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return \PHP\Depend\Source\AST\ASTForStatement
     * @since 0.9.8
     */
    function buildAstForStatement($image);

    /**
     * Builds a new for-init node.
     *
     * <code>
     *      ------------------------
     * for ($x = 0, $y = 23, $z = 42; $x < $y; ++$x) {}
     *      ------------------------
     * </code>
     *
     * @return \PHP\Depend\Source\AST\ASTForInit
     * @since 0.9.8
     */
    function buildAstForInit();

    /**
     * Builds a new for-update node.
     *
     * <code>
     *                                        -------------------------------
     * for ($x = 0, $y = 23, $z = 42; $x < $y; ++$x, $y = $x + 1, $z = $x + 2) {}
     *                                        -------------------------------
     * </code>
     *
     * @return \PHP\Depend\Source\AST\ASTForUpdate
     * @since 0.9.12
     */
    function buildAstForUpdate();

    /**
     * Builds a new foreach-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return \PHP\Depend\Source\AST\ASTForeachStatement
     * @since 0.9.8
     */
    function buildAstForeachStatement($image);

    /**
     * Builds a new while-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return \PHP\Depend\Source\AST\ASTWhileStatement
     * @since 0.9.8
     */
    function buildAstWhileStatement($image);

    /**
     * Builds a new do/while-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return \PHP\Depend\Source\AST\ASTDoWhileStatement
     * @since 0.9.12
     */
    function buildAstDoWhileStatement($image);

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
     * @return \PHP\Depend\Source\AST\ASTDeclareStatement
     * @since 0.10.0
     */
    function buildAstDeclareStatement();

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
     * @return \PHP\Depend\Source\AST\ASTMemberPrimaryPrefix
     * @since 0.9.6
     */
    function buildAstMemberPrimaryPrefix($image);

    /**
     * Builds a new identifier node.
     *
     * @param string $image The image of this identifier.
     *
     * @return \PHP\Depend\Source\AST\ASTIdentifier
     * @since 0.9.6
     */
    function buildAstIdentifier($image);

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
     * @return \PHP\Depend\Source\AST\ASTFunctionPostfix
     * @since 0.9.6
     */
    function buildAstFunctionPostfix($image);

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
     * @return \PHP\Depend\Source\AST\ASTMethodPostfix
     * @since 0.9.6
     */
    function buildAstMethodPostfix($image);

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
     * @return \PHP\Depend\Source\AST\ASTConstantPostfix
     * @since 0.9.6
     */
    function buildAstConstantPostfix($image);

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
     * @return \PHP\Depend\Source\AST\ASTPropertyPostfix
     * @since 0.9.6
     */
    function buildAstPropertyPostfix($image);

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
     * @return \PHP\Depend\Source\AST\ASTArguments
     * @since 0.9.6
     */
    function buildAstArguments();

    /**
     * Builds a new array type node.
     *
     * @return \PHP\Depend\Source\AST\ASTTypeArray
     * @since 0.9.6
     */
    function buildAstTypeArray();

    /**
     * Builds a new node for the callable type.
     *
     * @return \PHP\Depend\Source\AST\ASTTypeCallable
     * @since 1.0.0
     */
    function buildAstTypeCallable();

    /**
     * Builds a new primitive type node.
     *
     * @param string $image The source image for the primitive type.
     *
     * @return \PHP\Depend\Source\AST\ASTPrimitiveType
     * @since 0.9.6
     */
    function buildAstPrimitiveType($image);

    /**
     * Builds a new literal node.
     *
     * @param string $image The source image for the literal node.
     *
     * @return \PHP\Depend\Source\AST\ASTLiteral
     * @since 0.9.6
     */
    function buildAstLiteral($image);

    /**
     * Builds a new php string node.
     *
     * <code>
     * $string = "Manuel $Pichler <{$email}>";
     *
     * // \PHP\Depend\Source\AST\ASTString
     * // |-- ASTLiteral             -  "Manuel ")
     * // |-- ASTVariable            -  $Pichler
     * // |-- ASTLiteral             -  " <"
     * // |-- ASTCompoundExpression  -  {...}
     * // |   |-- ASTVariable        -  $email
     * // |-- ASTLiteral             -  ">"
     * </code>
     *
     * @return \PHP\Depend\Source\AST\ASTString
     * @since 0.9.10
     */
    function buildAstString();

    /**
     * Builds a new php array node.
     *
     * @return \PHP\Depend\Source\AST\ASTArray
     * @since 1.0.0
     */
    function buildAstArray();

    /**
     * Builds a new array element node.
     *
     * @return \PHP\Depend\Source\AST\ASTArrayElement
     * @since 1.0.0
     */
    function buildAstArrayElement();

    /**
     * Builds a new heredoc node.
     *
     * @return \PHP\Depend\Source\AST\ASTHeredoc
     * @since 0.9.12
     */
    function buildAstHeredoc();

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
     * @return \PHP\Depend\Source\AST\ASTConstantDefinition
     * @since 0.9.6
     */
    function buildAstConstantDefinition($image);

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
     * @return \PHP\Depend\Source\AST\ASTConstantDeclarator
     * @since 0.9.6
     */
    function buildAstConstantDeclarator($image);

    /**
     * Builds a new comment node instance.
     *
     * @param string $cdata The comment text.
     *
     * @return \PHP\Depend\Source\AST\ASTComment
     * @since 0.9.8
     */
    function buildAstComment($cdata);

    /**
     * Builds a new unary expression node instance.
     *
     * @param string $image The unary expression image/character.
     *
     * @return \PHP\Depend\Source\AST\ASTUnaryExpression
     * @since 0.9.11
     */
    function buildAstUnaryExpression($image);

    /**
     * Builds a new cast-expression node instance.
     *
     * @param string $image The cast-expression image/character.
     *
     * @return \PHP\Depend\Source\AST\ASTCastExpression
     * @since 0.10.0
     */
    function buildAstCastExpression($image);

    /**
     * Builds a new postfix-expression node instance.
     *
     * @param string $image The postfix-expression image/character.
     *
     * @return \PHP\Depend\Source\AST\ASTPostfixExpression
     * @since 0.10.0
     */
    function buildAstPostfixExpression($image);

    /**
     * Builds a new pre-increment-expression node instance.
     *
     * @return \PHP\Depend\Source\AST\ASTPreIncrementExpression
     * @since 0.10.0
     */
    function buildAstPreIncrementExpression();

    /**
     * Builds a new pre-decrement-expression node instance.
     *
     * @return \PHP\Depend\Source\AST\ASTPreDecrementExpression
     * @since 0.10.0
     */
    function buildAstPreDecrementExpression();

    /**
     * Builds a new function/method scope instance.
     *
     * @return \PHP\Depend\Source\AST\ASTScope
     * @since 0.9.12
     */
    function buildAstScope();

    /**
     * Builds a new statement instance.
     *
     * @return \PHP\Depend\Source\AST\ASTStatement
     * @since 0.9.12
     */
    function buildAstStatement();

    /**
     * Builds a new return-statement node instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return \PHP\Depend\Source\AST\ASTReturnStatement
     * @since 0.9.12
     */
    function buildAstReturnStatement($image);

    /**
     * Builds a new break-statement node instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return \PHP\Depend\Source\AST\ASTBreakStatement
     * @since 0.9.12
     */
    function buildAstBreakStatement($image);

    /**
     * Builds a new continue-statement node instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return \PHP\Depend\Source\AST\ASTContinueStatement
     * @since 0.9.12
     */
    function buildAstContinueStatement($image);

    /**
     * Builds a new scope-statement instance.
     *
     * @return \PHP\Depend\Source\AST\ASTScopeStatement
     * @since 0.9.12
     */
    function buildAstScopeStatement();

    /**
     * Builds a new try-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return \PHP\Depend\Source\AST\ASTTryStatement
     * @since 0.9.12
     */
    function buildAstTryStatement($image);

    /**
     * Builds a new throw-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return \PHP\Depend\Source\AST\ASTThrowStatement
     * @since 0.9.12
     */
    function buildAstThrowStatement($image);

    /**
     * Builds a new goto-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return \PHP\Depend\Source\AST\ASTGotoStatement
     * @since 0.9.12
     */
    function buildAstGotoStatement($image);

    /**
     * Builds a new label-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return \PHP\Depend\Source\AST\ASTLabelStatement
     * @since 0.9.12
     */
    function buildAstLabelStatement($image);

    /**
     * Builds a new global-statement instance.
     *
     * @return \PHP\Depend\Source\AST\ASTGlobalStatement
     * @since 0.9.12
     */
    function buildAstGlobalStatement();

    /**
     * Builds a new unset-statement instance.
     *
     * @return \PHP\Depend\Source\AST\ASTUnsetStatement
     * @since 0.9.12
     */
    function buildAstUnsetStatement();

    /**
     * Builds a new exit-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return \PHP\Depend\Source\AST\ASTEchoStatement
     * @since 0.9.12
     */
    function buildAstEchoStatement($image);
}
