<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

/**
 * Base interface for all code node builders.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2011 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
interface PHP_Depend_BuilderI
    extends PHP_Depend_ConstantsI, IteratorAggregate
{
    /**
     * Setter method for the currently used token cache.
     *
     * @param PHP_Depend_Util_Cache_Driver $cache Used token cache instance.
     *
     * @return PHP_Depend_Builder_Default
     * @since 0.10.0
     */
    function setCache(PHP_Depend_Util_Cache_Driver $cache);

    /**
     * Restores a function within the internal type scope.
     *
     * @param PHP_Depend_Code_Function $function A function instance.
     *
     * @return void
     * @since 0.10.0
     */
    function restoreFunction(PHP_Depend_Code_Function $function);

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link PHP_Depend_Code_Class}
     * instance when no matching type exists.
     *
     * @param string $qualifiedName The full qualified type identifier.
     *
     * @return PHP_Depend_Code_AbstractClassOrInterface
     * @since 0.9.5
     */
    function getClassOrInterface($qualifiedName);

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     *
     * @return PHP_Depend_Code_ASTClassOrInterfaceReference
     * @since 0.9.5
     */
    function buildASTClassOrInterfaceReference($qualifiedName);

    /**
     * Builds a new code class instance.
     *
     * @param string $qualifiedName The class name.
     *
     * @return PHP_Depend_Code_Class The created class object.
     */
    function buildClass($qualifiedName);

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link PHP_Depend_Code_Class}
     * instance when no matching type exists.
     *
     * @param string $qualifiedName The full qualified type identifier.
     *
     * @return PHP_Depend_Code_Class
     * @since 0.9.5
     */
    function getClass($qualifiedName);

    /**
     * Restores an existing class instance within the context of this builder.
     *
     * @param PHP_Depend_Code_Class $class An existing class instance.
     *
     * @return void
     * @since 0.10.0
     */
    function restoreClass(PHP_Depend_Code_Class $class);

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     *
     * @return PHP_Depend_Code_ASTClassReference
     * @since 0.9.5
     */
    function buildASTClassReference($qualifiedName);

    /**
     * Builds a new closure instance.
     *
     * @return PHP_Depend_Code_Closure
     */
    function buildClosure();

    /**
     * Builds a new new interface instance.
     *
     * @param string $qualifiedName The interface name.
     *
     * @return PHP_Depend_Code_Interface The created interface object.
     */
    function buildInterface($qualifiedName);

    /**
     * Restores an existing interface instance within the context of this builder.
     *
     * @param PHP_Depend_Code_Interface $interface An existing interface instance.
     *
     * @return void
     * @since 0.10.0
     */
    function restoreInterface(PHP_Depend_Code_Interface $interface);

    /**
     * This method will try to find an already existing instance for the given
     * qualified name. It will create a new {@link PHP_Depend_Code_Interface}
     * instance when no matching type exists.
     *
     * @param string $qualifiedName The full qualified type identifier.
     *
     * @return PHP_Depend_Code_Interface
     * @since 0.9.5
     */
    function getInterface($qualifiedName);

    /**
     * Builds a new package instance.
     *
     * @param string $name The package name.
     *
     * @return PHP_Depend_Code_Package The created package object.
     */
    function buildPackage($name);

    /**
     * Builds a new method instance.
     *
     * @param string $name The method name.
     *
     * @return PHP_Depend_Code_Method The created class method object.
     */
    function buildMethod($name);

    /**
     * Builds a new function instance.
     *
     * @param string $name The function name.
     *
     * @return PHP_Depend_Code_Function The function instance
     */
    function buildFunction($name);

    /**
     * Builds a new self reference instance.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $type The type instance
     *        that reference the concrete target of self.
     *
     * @return PHP_Depend_Code_ASTSelfReference
     * @since 0.9.6
     */
    function buildASTSelfReference(PHP_Depend_Code_AbstractClassOrInterface $type);

    /**
     * Builds a new parent reference instance.
     *
     * @param PHP_Depend_Code_ASTClassOrInterfaceReference $reference The type
     *        instance that reference the concrete target of parent.
     *
     * @return PHP_Depend_Code_ASTParentReference
     * @since 0.9.6
     */
    function buildASTParentReference(
        PHP_Depend_Code_ASTClassOrInterfaceReference $reference
    );

    /**
     * Builds a new static reference instance.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $owner The owning instance
     *        that reference the concrete target of static.
     *
     * @return PHP_Depend_Code_ASTStaticReference
     * @since 0.9.6
     */
    function buildASTStaticReference(
        PHP_Depend_Code_AbstractClassOrInterface $owner
    );

    /**
     * Builds a new field declaration node.
     *
     * @return PHP_Depend_Code_ASTFieldDeclaration
     * @since 0.9.6
     */
    function buildASTFieldDeclaration();

    /**
     * Builds a new variable declarator node.
     *
     * @param string $image The source image for the variable declarator.
     *
     * @return PHP_Depend_Code_ASTVariableDeclarator
     * @since 0.9.6
     */
    function buildASTVariableDeclarator($image);

    /**
     * Builds a new constant node.
     *
     * @param string $image The source image for the constant.
     *
     * @return PHP_Depend_Code_ASTConstant
     * @since 0.9.6
     */
    function buildASTConstant($image);

    /**
     * Builds a new variable node.
     *
     * @param string $image The source image for the variable.
     *
     * @return PHP_Depend_Code_ASTVariable
     * @since 0.9.6
     */
    function buildASTVariable($image);

    /**
     * Builds a new variable variable node.
     *
     * @param string $image The source image for the variable variable.
     *
     * @return PHP_Depend_Code_ASTVariableVariable
     * @since 0.9.6
     */
    function buildASTVariableVariable($image);

    /**
     * Builds a new compound variable node.
     *
     * @param string $image The source image for the compound variable.
     *
     * @return PHP_Depend_Code_ASTCompoundVariable
     * @since 0.9.6
     */
    function buildASTCompoundVariable($image);

    /**
     * Builds a new compound expression node.
     *
     * @return PHP_Depend_Code_ASTCompoundExpression
     * @since 0.9.6
     */
    function buildASTCompoundExpression();

    /**
     * Builds a new static variable declaration node.
     *
     * @param string $image The source image for the static declaration.
     *
     * @return PHP_Depend_Code_ASTStaticVariableDeclaration
     * @since 0.9.6
     */
    function buildASTStaticVariableDeclaration($image);

    /**
     * Builds a new closure node.
     *
     * @return PHP_Depend_Code_ASTClosure
     * @since 0.9.12
     */
    function buildASTClosure();

    /**
     * Builds a new formal parameters node.
     *
     * @return PHP_Depend_Code_ASTFormalParameters
     * @since 0.9.6
     */
    function buildASTFormalParameters();

    /**
     * Builds a new formal parameter node.
     *
     * @return PHP_Depend_Code_ASTFormalParameter
     * @since 0.9.6
     */
    function buildASTFormalParameter();

    /**
     * Builds a new expression node.
     *
     * @return PHP_Depend_Code_ASTExpression
     * @since 0.9.8
     */
    function buildASTExpression();

    /**
     * Builds a new assignment expression node.
     *
     * @param string $image The assignment operator.
     *
     * @return PHP_Depend_Code_ASTAssignmentExpression
     * @since 0.9.8
     */
    function buildASTAssignmentExpression($image);

    /**
     * Builds a new allocation expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return PHP_Depend_Code_ASTAllocationExpression
     * @since 0.9.6
     */
    function buildASTAllocationExpression($image);

    /**
     * Builds a new eval-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return PHP_Depend_Code_ASTEvalExpression
     * @since 0.9.12
     */
    function buildASTEvalExpression($image);

    /**
     * Builds a new exit-expression instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return PHP_Depend_Code_ASTExitExpression
     * @since 0.9.12
     */
    function buildASTExitExpression($image);

    /**
     * Builds a new clone-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return PHP_Depend_Code_ASTCloneExpression
     * @since 0.9.12
     */
    function buildASTCloneExpression($image);

    /**
     * Builds a new list-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return PHP_Depend_Code_ASTListExpression
     * @author Joey Mazzarelli
     * @since 0.9.12
     */
    function buildASTListExpression($image);

    /**
     * Builds a new include- or include_once-expression.
     *
     * @return PHP_Depend_Code_ASTIncludeExpression
     * @since 0.9.12
     */
    function buildASTIncludeExpression();

    /**
     * Builds a new require- or require_once-expression.
     *
     * @return PHP_Depend_Code_ASTRequireExpression
     * @since 0.9.12
     */
    function buildASTRequireExpression();

    /**
     * Builds a new array-expression node.
     *
     * @return PHP_Depend_Code_ASTArrayIndexExpression
     * @since 0.9.12
     */
    function buildASTArrayIndexExpression();

    /**
     * Builds a new string-expression node.
     *
     * <code>
     * //     --------
     * $string{$index}
     * //     --------
     * </code>
     *
     * @return PHP_Depend_Code_ASTStringIndexExpression
     * @since 0.9.12
     */
    function buildASTStringIndexExpression();

    /**
     * Builds a new instanceof-expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return PHP_Depend_Code_ASTInstanceOfExpression
     * @since 0.9.6
     */
    function buildASTInstanceOfExpression($image);

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
     * @return PHP_Depend_Code_ASTIssetExpression
     * @since 0.9.12
     */
    function buildASTIssetExpression();

    /**
     * Builds a new boolean conditional-expression.
     *
     * <code>
     *         --------------
     * $bar = ($foo ? 42 : 23);
     *         --------------
     * </code>
     *
     * @return PHP_Depend_Code_ASTConditionalExpression
     * @since 0.9.8
     */
    function buildASTConditionalExpression();

    /**
     * Builds a new boolean and-expression.
     *
     * @return PHP_Depend_Code_ASTBooleanAndExpression
     * @since 0.9.8
     */
    function buildASTBooleanAndExpression();

    /**
     * Builds a new boolean or-expression.
     *
     * @return PHP_Depend_Code_ASTBooleanOrExpression
     * @since 0.9.8
     */
    function buildASTBooleanOrExpression();

    /**
     * Builds a new logical <b>and</b>-expression.
     *
     * @return PHP_Depend_Code_ASTLogicalAndExpression
     * @since 0.9.8
     */
    function buildASTLogicalAndExpression();

    /**
     * Builds a new logical <b>or</b>-expression.
     *
     * @return PHP_Depend_Code_ASTLogicalOrExpression
     * @since 0.9.8
     */
    function buildASTLogicalOrExpression();

    /**
     * Builds a new logical <b>xor</b>-expression.
     *
     * @return PHP_Depend_Code_ASTLogicalXorExpression
     * @since 0.9.8
     */
    function buildASTLogicalXorExpression();

    /**
     * Builds a new switch-statement-node.
     *
     * @return PHP_Depend_Code_ASTSwitchStatement
     * @since 0.9.8
     */
    function buildASTSwitchStatement();

    /**
     * Builds a new switch-label node.
     *
     * @param string $image The source image of this label.
     *
     * @return PHP_Depend_Code_ASTSwitchLabel
     * @since 0.9.8
     */
    function buildASTSwitchLabel($image);

    /**
     * Builds a new catch-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTCatchStatement
     * @since 0.9.8
     */
    function buildASTCatchStatement($image);

    /**
     * Builds a new if statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTIfStatement
     * @since 0.9.8
     */
    function buildASTIfStatement($image);

    /**
     * Builds a new elseif-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTElseIfStatement
     * @since 0.9.8
     */
    function buildASTElseIfStatement($image);

    /**
     * Builds a new for-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTForStatement
     * @since 0.9.8
     */
    function buildASTForStatement($image);

    /**
     * Builds a new for-init node.
     *
     * <code>
     *      ------------------------
     * for ($x = 0, $y = 23, $z = 42; $x < $y; ++$x) {}
     *      ------------------------
     * </code>
     *
     * @return PHP_Depend_Code_ASTForInit
     * @since 0.9.8
     */
    function buildASTForInit();

    /**
     * Builds a new for-update node.
     *
     * <code>
     *                                        -------------------------------
     * for ($x = 0, $y = 23, $z = 42; $x < $y; ++$x, $y = $x + 1, $z = $x + 2) {}
     *                                        -------------------------------
     * </code>
     *
     * @return PHP_Depend_Code_ASTForUpdate
     * @since 0.9.12
     */
    function buildASTForUpdate();

    /**
     * Builds a new foreach-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTForeachStatement
     * @since 0.9.8
     */
    function buildASTForeachStatement($image);

    /**
     * Builds a new while-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTWhileStatement
     * @since 0.9.8
     */
    function buildASTWhileStatement($image);

    /**
     * Builds a new do/while-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTDoWhileStatement
     * @since 0.9.12
     */
    function buildASTDoWhileStatement($image);

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
     * @return PHP_Depend_Code_ASTDeclareStatement
     * @since 0.10.0
     */
    function buildASTDeclareStatement();

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
     * @return PHP_Depend_Code_ASTMemberPrimaryPrefix
     * @since 0.9.6
     */
    function buildASTMemberPrimaryPrefix($image);

    /**
     * Builds a new identifier node.
     *
     * @param string $image The image of this identifier.
     *
     * @return PHP_Depend_Code_ASTIdentifier
     * @since 0.9.6
     */
    function buildASTIdentifier($image);

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
     * @return PHP_Depend_Code_ASTFunctionPostfix
     * @since 0.9.6
     */
    function buildASTFunctionPostfix($image);

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
     * @return PHP_Depend_Code_ASTMethodPostfix
     * @since 0.9.6
     */
    function buildASTMethodPostfix($image);

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
     * @return PHP_Depend_Code_ASTConstantPostfix
     * @since 0.9.6
     */
    function buildASTConstantPostfix($image);

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
     * @return PHP_Depend_Code_ASTPropertyPostfix
     * @since 0.9.6
     */
    function buildASTPropertyPostfix($image);

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
     * @return PHP_Depend_Code_ASTArguments();
     * @since 0.9.6
     */
    function buildASTArguments();

    /**
     * Builds a new array type node.
     *
     * @return PHP_Depend_Code_ASTArrayType
     * @since 0.9.6
     */
    function buildASTArrayType();

    /**
     * Builds a new primitive type node.
     *
     * @param string $image The source image for the primitive type.
     *
     * @return PHP_Depend_Code_ASTPrimitiveType
     * @since 0.9.6
     */
    function buildASTPrimitiveType($image);

    /**
     * Builds a new literal node.
     *
     * @param string $image The source image for the literal node.
     *
     * @return PHP_Depend_Code_ASTLiteral
     * @since 0.9.6
     */
    function buildASTLiteral($image);

    /**
     * Builds a new php string node.
     *
     * <code>
     * $string = "Manuel $Pichler <{$email}>";
     *
     * // PHP_Depend_Code_ASTString
     * // |-- ASTLiteral             -  "Manuel ")
     * // |-- ASTVariable            -  $Pichler
     * // |-- ASTLiteral             -  " <"
     * // |-- ASTCompoundExpression  -  {...}
     * // |   |-- ASTVariable        -  $email
     * // |-- ASTLiteral             -  ">"
     * </code>
     *
     * @return PHP_Depend_Code_ASTString
     * @since 0.9.10
     */
    function buildASTString();

    /**
     * Builds a new heredoc node.
     *
     * @return PHP_Depend_Code_ASTHeredoc
     * @since 0.9.12
     */
    function buildASTHeredoc();

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
     * @return PHP_Depend_Code_ASTConstantDefinition
     * @since 0.9.6
     */
    function buildASTConstantDefinition($image);

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
     * @return PHP_Depend_Code_ASTConstantDeclarator
     * @since 0.9.6
     */
    function buildASTConstantDeclarator($image);

    /**
     * Builds a new comment node instance.
     *
     * @param string $cdata The comment text.
     *
     * @return PHP_Depend_Code_ASTComment
     * @since 0.9.8
     */
    function buildASTComment($cdata);

    /**
     * Builds a new unary expression node instance.
     *
     * @param string $image The unary expression image/character.
     *
     * @return PHP_Depend_Code_ASTUnaryExpression
     * @since 0.9.11
     */
    function buildASTUnaryExpression($image);

    /**
     * Builds a new cast-expression node instance.
     *
     * @param string $image The cast-expression image/character.
     *
     * @return PHP_Depend_Code_ASTCastExpression
     * @since 0.10.0
     */
    function buildASTCastExpression($image);

    /**
     * Builds a new postfix-expression node instance.
     *
     * @param string $image The postfix-expression image/character.
     *
     * @return PHP_Depend_Code_ASTPostfixExpression
     * @since 0.10.0
     */
    function buildASTPostfixExpression($image);

    /**
     * Builds a new pre-increment-expression node instance.
     *
     * @return PHP_Depend_Code_ASTPreIncrementExpression
     * @since 0.10.0
     */
    function buildASTPreIncrementExpression();

    /**
     * Builds a new pre-decrement-expression node instance.
     *
     * @return PHP_Depend_Code_ASTPreDecrementExpression
     * @since 0.10.0
     */
    function buildASTPreDecrementExpression();

    /**
     * Builds a new function/method scope instance.
     *
     * @return PHP_Depend_Code_ASTScope
     * @since 0.9.12
     */
    function buildASTScope();

    /**
     * Builds a new statement instance.
     *
     * @return PHP_Depend_Code_ASTStatement
     * @since 0.9.12
     */
    function buildASTStatement();

    /**
     * Builds a new return-statement node instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return PHP_Depend_Code_ASTReturnStatement
     * @since 0.9.12
     */
    function buildASTReturnStatement($image);

    /**
     * Builds a new break-statement node instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return PHP_Depend_Code_ASTBreakStatement
     * @since 0.9.12
     */
    function buildASTBreakStatement($image);

    /**
     * Builds a new continue-statement node instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return PHP_Depend_Code_ASTContinueStatement
     * @since 0.9.12
     */
    function buildASTContinueStatement($image);

    /**
     * Builds a new scope-statement instance.
     *
     * @return PHP_Depend_Code_ASTScopeStatement
     * @since 0.9.12
     */
    function buildASTScopeStatement();

    /**
     * Builds a new try-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return PHP_Depend_Code_ASTTryStatement
     * @since 0.9.12
     */
    function buildASTTryStatement($image);

    /**
     * Builds a new throw-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return PHP_Depend_Code_ASTThrowStatement
     * @since 0.9.12
     */
    function buildASTThrowStatement($image);

    /**
     * Builds a new goto-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return PHP_Depend_Code_ASTGotoStatement
     * @since 0.9.12
     */
    function buildASTGotoStatement($image);

    /**
     * Builds a new label-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return PHP_Depend_Code_ASTLabelStatement
     * @since 0.9.12
     */
    function buildASTLabelStatement($image);

    /**
     * Builds a new global-statement instance.
     *
     * @return PHP_Depend_Code_ASTGlobalStatement
     * @since 0.9.12
     */
    function buildASTGlobalStatement();

    /**
     * Builds a new unset-statement instance.
     *
     * @return PHP_Depend_Code_ASTUnsetStatement
     * @since 0.9.12
     */
    function buildASTUnsetStatement();

    /**
     * Builds a new exit-statement instance.
     *
     * @param string $image The source code image for this node.
     *
     * @return PHP_Depend_Code_ASTEchoStatement
     * @since 0.9.12
     */
    function buildASTEchoStatement($image);
}
