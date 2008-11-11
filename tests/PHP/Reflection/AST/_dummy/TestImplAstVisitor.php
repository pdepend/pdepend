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
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/VisitorI.php';

/**
 * Simple test node visitor implementation.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_AST_TestImplAstVisitor implements PHP_Reflection_VisitorI
{
    /**
     * The last visited class instance.
     *
     * @type PHP_Reflection_AST_Class
     * @var PHP_Reflection_AST_Class $class
     */
    public $class = null;

    /**
     * The last visited interface instance.
     *
     * @type PHP_Reflection_AST_Interface
     * @var PHP_Reflection_AST_Interface $interface
     */
    public $interface = null;

    /**
     * The last visited method instance.
     *
     * @var PHP_Reflection_AST_MethodI $method
     */
    public $method = null;

    /**
     * The last visited package instance.
     *
     * @type PHP_Reflection_AST_Package
     * @var PHP_Reflection_AST_Package $method
     */
    public $package = null;

    /**
     * The last visited parameter instance.
     *
     * @type PHP_Reflection_AST_Parameter
     * @var PHP_Reflection_AST_Parameter $parameter
     */
    public $parameter = null;

    /**
     * The last visited property instance.
     *
     * @type PHP_Reflection_AST_Property
     * @var PHP_Reflection_AST_Property $property
     */
    public $property = null;

    /**
     * The last visited function instance.
     *
     * @type PHP_Reflection_AST_Function
     * @var PHP_Reflection_AST_Function $method
     */
    public $function = null;

    /**
     * The last visited type constant instance.
     *
     * @type PHP_Reflection_AST_ClassOrInterfaceConstant
     * @var PHP_Reflection_AST_ClassOrInterfaceConstant $typeConstant
     */
    public $typeConstant = null;

    /**
     * Adds a new listener to this node visitor.
     *
     * @param PHP_Reflection_Visitor_ListenerI $listener The new visit listener.
     *
     * @return void
     */
    public function addVisitListener(PHP_Reflection_Visitor_ListenerI $listener)
    {
    }

    /**
     * Removes the listener from this node visitor.
     *
     * @param PHP_Reflection_Visitor_ListenerI $listener The listener to remove.
     *
     * @return void
     */
    public function removeVisitListener(PHP_Reflection_Visitor_ListenerI $listener)
    {
    }

    /**
     * Visits a class node.
     *
     * @param PHP_Reflection_AST_ClassI $class The current class node.
     *
     * @return void
     */
    public function visitClass(PHP_Reflection_AST_ClassI $class)
    {
        $this->class = $class;
    }

    /**
     * Visits a code interface object.
     *
     * @param PHP_Reflection_AST_InterfaceI $interface The context code interface.
     *
     * @return void
     */
    public function visitInterface(PHP_Reflection_AST_InterfaceI $interface)
    {
        $this->interface = $interface;
    }

    /**
     * Visits a method node.
     *
     * @param PHP_Reflection_AST_MethodI $method The method class node.
     *
     * @return void
     */
    public function visitMethod(PHP_Reflection_AST_MethodI $method)
    {
        $this->method = $method;
    }

    /**
     * Visits a package node.
     *
     * @param PHP_Reflection_AST_PackageI $package The package class node.
     *
     * @return void
     */
    public function visitPackage(PHP_Reflection_AST_PackageI $package)
    {
        $this->package = $package;
    }

    /**
     * Visits a parameter list node.
     *
     * @param PHP_Reflection_AST_ParameterListI $paramList The parameter list.
     *
     * @return void
     */
    public function visitParameterList(PHP_Reflection_AST_ParameterListI $paramList)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a parameter node.
     *
     * @param PHP_Reflection_AST_ParameterI $parameter The parameter node.
     *
     * @return void
     */
    public function visitParameter(PHP_Reflection_AST_ParameterI $parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * Visits a property node.
     *
     * @param PHP_Reflection_AST_PropertyI $property The property class node.
     *
     * @return void
     * @see PHP_Reflection_AST_NodeVisitorI::visitProperty()
     */
    public function visitProperty(PHP_Reflection_AST_PropertyI $property)
    {
        $this->property = $property;
    }

    /**
     * Visits a if statement node.
     *
     * @param PHP_Reflection_AST_IfStatementI $ifStmt The if statement node.
     *
     * @return void
     */
    public function visitIfStatement(PHP_Reflection_AST_IfStatementI $ifStmt)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits an else statement node.
     *
     * @param PHP_Reflection_AST_ElseStatementI $elseStmt The else statement node.
     *
     * @return void
     */
    public function visitElseStatement(PHP_Reflection_AST_ElseStatementI $elseStmt)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits and <b>else if</b> statement node.
     *
     * @param PHP_Reflection_AST_ElseIfStatementI $stmt The else if statement.
     *
     * @return void
     */
    public function visitElseIfStatement(PHP_Reflection_AST_ElseIfStatementI $stmt)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a <b>for</b> statement node.
     *
     * @param PHP_Reflection_AST_ForStatementI $stmt The for statement.
     *
     * @return void
     */
    public function visitForStatement(PHP_Reflection_AST_ForStatementI $stmt)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits the init expressions of a <b>for</b>-statement.
     *
     * @param PHP_Reflection_AST_ForInitI $forInit The init node.
     *
     * @return void
     */
    public function visitForInit(PHP_Reflection_AST_ForInitI $forInit)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits the condition expressions of a <b>for</b>-statement.
     *
     * @param PHP_Reflection_AST_ForConditionI $forCond The condition node.
     *
     * @return void
     */
    public function visitForConditions(PHP_Reflection_AST_ForConditionI $forCond)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits the update expressions of a <b>for</b>-loop statement.
     *
     * @param PHP_Reflection_AST_ForUpdateI $forUpdate The update node.
     *
     * @return void
     */
    public function visitForUpdate(PHP_Reflection_AST_ForUpdateI $forUpdate)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a <b>foreach</b>-statement node.
     *
     * @param PHP_Reflection_AST_ForeachStatementI $stmt The foreach statement.
     *
     * @return void
     */
    public function visitForeachStatement(PHP_Reflection_AST_ForeachStatementI $stmt)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a <b>while</b>-statement node.
     *
     * @param PHP_Reflection_AST_WhileStatementI $stmt The while statement.
     *
     * @return void
     */
    public function visitWhileStatement(PHP_Reflection_AST_WhileStatementI $stmt)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a <b>do while</b>-statement node.
     *
     * @param PHP_Reflection_AST_DoStatementI $stmt The do while statement.
     *
     * @return void
     */
    public function visitDoStatement(PHP_Reflection_AST_DoStatementI $stmt)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a <b>switch</b>-statement node.
     *
     * @param PHP_Reflection_AST_SwitchStatementI $stmt The switch statement.
     *
     * @return void
     */
    public function visitSwitchStatement(PHP_Reflection_AST_SwitchStatementI $stmt)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a <b>case</b>-statement node.
     *
     * @param PHP_Reflection_AST_CaseStatementI $stmt The case statement.
     *
     * @return void
     */
    public function visitCaseStatement(PHP_Reflection_AST_CaseStatementI $stmt)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a generic block-statement node.
     *
     * @param PHP_Reflection_AST_BlockStatementI $stmt The statement node.
     *
     * @return void
     */
    public function visitBlockStatement(PHP_Reflection_AST_BlockStatementI $stmt)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a logical <b>and</b>-expression node.
     *
     * @param PHP_Reflection_AST_LogicalAndExpressionI $expr The expression node.
     *
     * @return void
     */
    public function visitLogicalAndExpression(
                                PHP_Reflection_AST_LogicalAndExpressionI $expr)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a logical <b>or</b>-expression node.
     *
     * @param PHP_Reflection_AST_LogicalOrExpressionI $expr The expression node.
     *
     * @return void
     */
    public function visitLogicalOrExpression(
                                PHP_Reflection_AST_LogicalOrExpressionI $expr)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a logical <b>xor</b>-expression node.
     *
     * @param PHP_Reflection_AST_LogicalXorExpressionI $expr The expression node.
     *
     * @return void
     */
    public function visitLogicalXorExpression(
                                PHP_Reflection_AST_LogicalXorExpressionI $expr)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a boolean <b>&&</b>-expression node.
     *
     * @param PHP_Reflection_AST_BooleanAndExpressionI $expr The expression node.
     *
     * @return void
     */
    public function visitBooleanAndExpression(
                                PHP_Reflection_AST_BooleanAndExpressionI $expr)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a boolean <b>||</b>-expression node-
     *
     * @param PHP_Reflection_AST_BooleanOrExpressionI $expr The expression node.
     *
     * @return void
     */
    public function visitBooleanOrExpression(
                                PHP_Reflection_AST_BooleanOrExpressionI $expr)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a conditional <b>? :</b>-expression node.
     *
     * @param PHP_Reflection_AST_ConditionalExpressionI $expr The expression node.
     *
     * @return void
     */
    public function visitConditionalExpression(
                                PHP_Reflection_AST_ConditionalExpressionI $expr)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a <b>variable</b>-expression node.
     *
     * @param PHP_Reflection_AST_VariableExpressionI $expr The expression node.
     *
     * @return void
     */
    public function visitVariableExpression(
                                PHP_Reflection_AST_VariableExpressionI $expr)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a <b>boolean</b>-literal node.
     *
     * @param PHP_Reflection_AST_BooleanLiteralI $bool The literal node.
     *
     * @return void
     */
    public function visitBooleanLiteral(PHP_Reflection_AST_BooleanLiteralI $bool)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a function node.
     *
     * @param PHP_Reflection_AST_Function $function The current function node.
     *
     * @return void
     */
    public function visitFunction(PHP_Reflection_AST_FunctionI $function)
    {
        $this->function = $function;
    }

    /**
     * Visits a file node.
     *
     * @param PHP_Reflection_AST_File $file The current file node.
     *
     * @return void
     * @see PHP_Reflection_AST_NodeVisitorI::visitFile()
     */
    public function visitFile(PHP_Reflection_AST_File $file)
    {

    }

    /**
     * Visits a class constant node.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceConstant $constant The current constant node.
     *
     * @return void
     */
    public function visitTypeConstant(PHP_Reflection_AST_ClassOrInterfaceConstant $constant)
    {
        $this->typeConstant = $constant;
    }

    /**
     * Visits an exception catch node.
     *
     * @param PHP_Reflection_AST_CatchStatementI $stmt The current catch statement.
     *
     * @return void
     */
    public function visitCatchStatement(PHP_Reflection_AST_CatchStatementI $stmt)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits an array expression node
     *
     * @param PHP_Reflection_AST_ArrayExpression $expr The current array expression.
     *
     * @return void
     */
    public function visitArrayExpression(PHP_Reflection_AST_ArrayExpression $expr)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits an array element node.
     *
     * @param PHP_Reflection_AST_ArrayElement $elem The current array element.
     *
     * @return void
     */
    public function visitArrayElement(PHP_Reflection_AST_ArrayElement $elem)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a constant reference node.
     *
     * @param PHP_Reflection_AST_ConstantValue $constRef The current const ref.
     *
     * @return void
     */
    public function visitConstantValue(PHP_Reflection_AST_ConstantValue $constRef)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a class or interface constant reference
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceConstantValue $constRef
     *        The reference instance.
     *
     * @return void
     */
    public function visitClassOrInterfaceConstantValue(
                PHP_Reflection_AST_ClassOrInterfaceConstantValue $constRef)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a general value.
     *
     * @param PHP_Reflection_AST_MemberValueI $value The value instance.
     *
     * @return void
     */
    public function visitValue(PHP_Reflection_AST_MemberValueI $value)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visits a block node.
     *
     * @param PHP_Reflection_AST_BlockI $block The block instance.
     *
     * @return void
     */
    public function visitBlock(PHP_Reflection_AST_BlockI $block)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visit a closure node.
     *
     * @param PHP_Reflection_AST_ClosureI $closure The closure instance.
     *
     * @return void
     */
    public function visitClosure(PHP_Reflection_AST_ClosureI $closure)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visit a new expression node.
     *
     * @param PHP_Reflection_AST_NewExpressionI $newExpr The new expression instance.
     *
     * @return void
     */
    public function visitNewExpression(PHP_Reflection_AST_NewExpressionI $newExpr)
    {
        // TODO Implement this empty stub.
    }

    /**
     * Visit a instance of node.
     *
     * @param PHP_Reflection_AST_InstanceOfExpressionI $instanceOfExpr
     * The instance of instance.
     *
     * @return void
     */
    public function visitInstanceOfExpression(
                        PHP_Reflection_AST_InstanceOfExpressionI $instanceOfExpr)
    {
        // TODO Implement this empty stub.
    }
}