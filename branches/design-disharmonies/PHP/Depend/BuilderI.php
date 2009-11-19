<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once 'PHP/Depend/ConstantsI.php';

/**
 * Base interface for all code node builders.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
interface PHP_Depend_BuilderI
    extends PHP_Depend_ConstantsI, IteratorAggregate
{
    /**
     * Generic build class for classes and interfaces. This method should be used
     * in cases when it is not clear what type is used in the current situation.
     * This could happen if the parser analyzes a method signature. The default
     * return type is {@link PHP_Depend_Code_Class}, but if there is already an
     * interface for this name, the method will return this instance.
     *
     * <code>
     *   $builder->buildInterface('PHP_DependI');
     *
     *   // Returns an instance of PHP_Depend_Code_Interface
     *   $builder->buildClassOrInterface('PHP_DependI');
     *
     *   // Returns an instance of PHP_Depend_Code_Class
     *   $builder->buildClassOrInterface('PHP_Depend');
     * </code>
     *
     * @param string $name The class name.
     *
     * @return PHP_Depend_Code_Class|PHP_Depend_Code_Interface
     *         The created class or interface instance.
     */
    function buildClassOrInterface($name);

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
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     *
     * @return PHP_Depend_Code_ASTInterfaceReference
     * @since 0.9.5
     */
    function buildInterfaceReference($qualifiedName);

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
     * Builds a new this variable reference instance.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $type The type instance
     *        that reference the concrete target of self.
     *
     * @return PHP_Depend_Code_ASTSelfReference
     * @since 0.9.8
     */	
	function buildASTThisVariable(PHP_Depend_Code_AbstractClassOrInterface $type);
    
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
     * Builds a new instanceof expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return PHP_Depend_Code_ASTInstanceOfExpression
     * @since 0.9.6
     */
    function buildASTInstanceOfExpression($image);

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
     * Builds a new code class constant instance.
     *
     * @param string $name The constant name.
     *
     * @return PHP_Depend_Code_TypeConstant The created constant object.
     * @deprecated Since version 0.9.6
     */
    function buildTypeConstant($name);
}