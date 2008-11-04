<?php
/**
 * This file is part of PHP_Reflection.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pdepend.org>.
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
 * @category  PHP
 * @package   PHP_Reflection
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/Visitor/ListenerI.php';

/**
 * Base interface for visitors that work on the generated node tree.
 *
 * @category  PHP
 * @package   PHP_Reflection
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
interface PHP_Reflection_VisitorI
{
    /**
     * Adds a new listener to this node visitor.
     *
     * @param PHP_Reflection_Visitor_ListenerI $listener The new visit listener.
     *
     * @return void
     */
    function addVisitListener(PHP_Reflection_Visitor_ListenerI $listener);

    /**
     * Removes the listener from this node visitor.
     *
     * @param PHP_Reflection_Visitor_ListenerI $listener The listener to remove.
     *
     * @return void
     */
    function removeVisitListener(PHP_Reflection_Visitor_ListenerI $listener);

    /**
     * Visits a class node.
     *
     * @param PHP_Reflection_AST_ClassI $class The current class node.
     *
     * @return void
     */
    function visitClass(PHP_Reflection_AST_ClassI $class);

    /**
     * Visits a file node.
     *
     * @param PHP_Reflection_AST_File $file The current file node.
     *
     * @return void
     */
    function visitFile(PHP_Reflection_AST_File $file);

    /**
     * Visits a function node.
     *
     * @param PHP_Reflection_AST_Function $function The current function node.
     *
     * @return void
     */
    function visitFunction(PHP_Reflection_AST_FunctionI $function);

    /**
     * Visits a code interface object.
     *
     * @param PHP_Reflection_AST_InterfaceI $interface The context code interface.
     *
     * @return void
     */
    function visitInterface(PHP_Reflection_AST_InterfaceI $interface);

    /**
     * Visits a method node.
     *
     * @param PHP_Reflection_AST_MethodI $method The method class node.
     *
     * @return void
     */
    function visitMethod(PHP_Reflection_AST_MethodI $method);

    /**
     * Visits a package node.
     *
     * @param Reflection_AST_PackageI $package The package class node.
     *
     * @return void
     */
    function visitPackage(PHP_Reflection_AST_PackageI $package);

    /**
     * Visits a parameter list node.
     *
     * @param PHP_Reflection_AST_ParameterListI $paramList The parameter list.
     *
     * @return void
     */
    function visitParameterList(PHP_Reflection_AST_ParameterListI $paramList);

    /**
     * Visits a parameter node.
     *
     * @param PHP_Reflection_AST_ParameterI $parameter The parameter node.
     *
     * @return void
     */
    function visitParameter(PHP_Reflection_AST_ParameterI $parameter);

    /**
     * Visits a property node.
     *
     * @param PHP_Reflection_AST_PropertyI $property The property class node.
     *
     * @return void
     */
    function visitProperty(PHP_Reflection_AST_PropertyI $property);

    /**
     * Visits an if statement node.
     *
     * @param PHP_Reflection_AST_IfStatementI $ifStmt The if statement node.
     *
     * @return void
     */
    function visitIfStatement(PHP_Reflection_AST_IfStatementI $ifStmt);

    /**
     * Visits an else statement node.
     *
     * @param PHP_Reflection_AST_ElseStatementI $elseStmt The else statement node.
     *
     * @return void
     */
    function visitElseStatement(PHP_Reflection_AST_ElseStatementI $stmt);

    /**
     * Visits an <b>else if</b> statement node.
     *
     * @param PHP_Reflection_AST_ElseIfStatementI $stmt The else if statement.
     *
     * @return void
     */
    function visitElseIfStatement(PHP_Reflection_AST_ElseIfStatementI $stmt);

    /**
     * Visits a <b>for</b> statement node.
     *
     * @param PHP_Reflection_AST_ForStatementI $stmt The for statement.
     *
     * @return void
     */
    function visitForStatement(PHP_Reflection_AST_ForStatementI $stmt);

    /**
     * Visits a <b>foreach</b>-statement node.
     *
     * @param PHP_Reflection_AST_ForeachStatementI $stmt The foreach statement.
     *
     * @return void
     */
    function visitForeachStatement(PHP_Reflection_AST_ForeachStatementI $stmt);

    /**
     * Visits a <b>while</b>-statement node.
     *
     * @param PHP_Reflection_AST_WhileStatementI $stmt The while statement.
     *
     * @return void
     */
    function visitWhileStatement(PHP_Reflection_AST_WhileStatementI $stmt);

    /**
     * Visits a <b>do while</b>-statement node.
     *
     * @param PHP_Reflection_AST_DoWhileStatementI $stmt The do while statement.
     *
     * @return void
     */
    function visitDoWhileStatement(PHP_Reflection_AST_DoWhileStatementI $stmt);

    /**
     * Visits a <b>switch</b>-statement node.
     *
     * @param PHP_Reflection_AST_SwitchStatementI $stmt The switch statement.
     *
     * @return void
     */
    function visitSwitchStatement(PHP_Reflection_AST_SwitchStatementI $stmt);

    /**
     * Visits a <b>case</b>-statement node.
     *
     * @param PHP_Reflection_AST_CaseStatementI $stmt The case statement.
     *
     * @return void
     */
    function visitCaseStatement(PHP_Reflection_AST_CaseStatementI $stmt);

    /**
     * Visits a logical <b>and</b>-expression node.
     *
     * @param PHP_Reflection_AST_LogicalAndExpressionI $expr The expression node.
     *
     * @return void
     */
    function visitLogicalAndExpression(
                                PHP_Reflection_AST_LogicalAndExpressionI $expr);

    /**
     * Visits a logical <b>or</b>-expression node.
     *
     * @param PHP_Reflection_AST_LogicalOrExpressionI $expr The expression node.
     *
     * @return void
     */
    function visitLogicalOrExpression(PHP_Reflection_AST_LogicalOrExpressionI $expr);

    /**
     * Visits a logical <b>xor</b>-expression node.
     *
     * @param PHP_Reflection_AST_LogicalXorExpressionI $expr The expression node.
     *
     * @return void
     */
    function visitLogicalXorExpression(
                                PHP_Reflection_AST_LogicalXorExpressionI $expr);

    /**
     * Visits a boolean <b>&&</b>-expression node.
     *
     * @param PHP_Reflection_AST_BooleanAndExpressionI $expr The expression node.
     *
     * @return void
     */
    function visitBooleanAndExpression(
                                PHP_Reflection_AST_BooleanAndExpressionI $expr);

    /**
     * Visits a boolean <b>||</b>-expression node-
     *
     * @param PHP_Reflection_AST_BooleanOrExpressionI $expr The expression node.
     *
     * @return void
     */
    function visitBooleanOrExpression(PHP_Reflection_AST_BooleanOrExpressionI $expr);

    /**
     * Visits a class constant node.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceConstant $constant
     * The current constant node.
     *
     * @return void
     */
    function visitTypeConstant(
                        PHP_Reflection_AST_ClassOrInterfaceConstant $constant);

    /**
     * Visits an exception catch node.
     *
     * @param PHP_Reflection_AST_CatchStatementI $stmt The current catch statement.
     *
     * @return void
     */
    function visitCatchStatement(PHP_Reflection_AST_CatchStatementI $stmt);

    /**
     * Visits an array expression node
     *
     * @param PHP_Reflection_AST_ArrayExpression $expr The current array expression.
     *
     * @return void
     */
    function visitArrayExpression(PHP_Reflection_AST_ArrayExpression $expr);

    /**
     * Visits an array element node.
     *
     * @param PHP_Reflection_AST_ArrayElement $elem The current array element.
     *
     * @return void
     */
    function visitArrayElement(PHP_Reflection_AST_ArrayElement $elem);

    /**
     * Visits a constant reference node.
     *
     * @param PHP_Reflection_AST_ConstantValue $constRef The current const ref.
     *
     * @return void
     */
    function visitConstantValue(PHP_Reflection_AST_ConstantValue $constRef);

    /**
     * Visits a class or interface constant value
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceConstantValue $constValue
     * The reference instance.
     *
     * @return void
     */
    function visitClassOrInterfaceConstantValue(
                PHP_Reflection_AST_ClassOrInterfaceConstantValue $constValue);

    /**
     * Visits a general value.
     *
     * @param PHP_Reflection_AST_MemberValueI $value The value instance.
     *
     * @return void
     */
    function visitValue(PHP_Reflection_AST_MemberValueI $value);

    /**
     * Visits a block node.
     *
     * @param PHP_Reflection_AST_BlockI $block The block instance.
     *
     * @return void
     */
    function visitBlock(PHP_Reflection_AST_BlockI $block);

    /**
     * Visit a closure node.
     *
     * @param PHP_Reflection_AST_ClosureI $closure The closure instance.
     *
     * @return void
     */
    function visitClosure(PHP_Reflection_AST_ClosureI $closure);

    /**
     * Visit a new expression node.
     *
     * @param PHP_Reflection_AST_NewExpressionI $newExpr The new expression instance.
     *
     * @return void
     */
    function visitNewExpression(PHP_Reflection_AST_NewExpressionI $newExpr);

    /**
     * Visit a instance of node.
     *
     * @param PHP_Reflection_AST_InstanceOfExpressionI $instanceOfExpr
     * The instance of instance.
     *
     * @return void
     */
    function visitInstanceOfExpression(
                    PHP_Reflection_AST_InstanceOfExpressionI $instanceOfExpr);
}