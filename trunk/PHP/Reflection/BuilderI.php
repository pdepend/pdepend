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

/**
 * Base interface for all code node builders.
 *
 * @category  PHP
 * @package   PHP_Reflection
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
interface PHP_Reflection_BuilderI extends IteratorAggregate
{
    /**
     * The default package name.
     */
    const PKG_UNKNOWN = '+unknown';

    /**
     * Generic build class for classes and interfaces. This method should be used
     * in cases when it is not clear what type is used in the current situation.
     * This could happen if the parser analyzes a method signature. The default
     * return type is {@link PHP_Reflection_AST_Class}, but if there is already an
     * interface for this name, the method will return this instance.
     *
     * <code>
     *   $builder->buildInterface('PHP_ReflectionI');
     *
     *   // Returns an instance of PHP_Reflection_AST_Interface
     *   $builder->findClassOrInterfaceSubject('PHP_ReflectionI');
     *
     *   // Returns an instance of PHP_Reflection_AST_Class
     *   $builder->findClassOrInterfaceSubject('PHP_Reflection');
     * </code>
     *
     * @param string $identifier The class name.
     *
     * @return PHP_Reflection_AST_ClassOrInterfaceI
     *         The created class or interface instance.
     */
    function findClassOrInterfaceSubject($identifier);

    /**
     * This method will return the best matching class instance for the given
     * qualified class identifier. First it looks for an exactly matching class
     * instance, then it will look for a similar match, if no exact match exits.
     * Finally this method creates a new class instance for this given identifier,
     * when no existing class matches.
     *
     * @param string $identifier The qualified class identifier.
     *
     * @return PHP_Reflection_AST_ClassI
     */
    function findClassSubject($identifier);

    /**
     * This method will return the best matching interface for the supplied
     * identifier. First it looks for an exact match in the existing interface,
     * if no result was found this method will look for a similar interface.
     * Finally this method will create a new interface instance, when the similar
     * match test also fails.
     *
     * @param string $identifier The qualified interface identifier.
     *
     * @return PHP_Reflection_AST_InterfaceI
     */
    function findInterfaceSubject($identifier);

    /**
     * Builds a new code class instance.
     *
     * @param string  $name The class name.
     * @param integer $line The line number for the class declaration.
     *
     * @return PHP_Reflection_AST_Class The created class object.
     */
    function buildClass($name, $line = 0);

    /**
     * Creates a proxy for a class.
     *
     * @param string $identifier The qualified class identifier.
     *
     * @return PHP_Reflection_AST_ClassProxy
     */
    function buildClassProxy($identifier);

    /**
     * Builds a new code class constant instance.
     *
     * @param string $name The constant name.
     *
     * @return PHP_Reflection_AST_ClassOrInterfaceConstant The created constant object.
     */
    function buildClassOrInterfaceConstant($name);

    /**
     * Builds a new new interface instance.
     *
     * @param string  $name The interface name.
     * @param integer $line The line number for the interface declaration.
     *
     * @return PHP_Reflection_AST_Interface The created interface object.
     */
    function buildInterface($name, $line = 0);

    /**
     * Creates a proxy for an interface.
     *
     * @param string $identifier The qualified interface identifier.
     *
     * @return PHP_Reflection_AST_InterfaceProxy
     */
    function buildInterfaceProxy($identifier);

    /**
     * Builds a new package instance.
     *
     * @param string $name The package name.
     *
     * @return PHP_Reflection_AST_Package The created package object.
     */
    function buildPackage($name);

    /**
     * Creates a new parameter list instance.
     *
     * @param integer $line The line number of the parameter list.
     *
     * @return PHP_Reflection_AST_ParameterList
     */
    function buildParameterList($line);

    /**
     * Builds a new parameter instance.
     *
     * @param string  $name The parameter variable name.
     * @param integer $line The line number with the parameter declaration.
     *
     * @return PHP_Reflection_AST_Parameter The created parameter instance.
     */
    function buildParameter($name, $line = 0);

    /**
     * Builds a new property instance.
     *
     * @param string  $name The property variable name.
     * @param integer $line The line number with the property declaration.
     *
     * @return PHP_Reflection_AST_Property The created property instance.
     */
    function buildProperty($name, $line = 0);

    /**
     * Builds a new method instance.
     *
     * @param string  $name The method name.
     * @param integer $line The line number with the method declaration.
     *
     * @return PHP_Reflection_AST_Method The created class method object.
     */
    function buildMethod($name, $line = 0);

    /**
     * Builds a new function instance.
     *
     * @param string  $name The function name.
     * @param integer $line The line number with the function declaration.
     *
     * @return PHP_Reflection_AST_Function The function instance.
     */
    function buildFunction($name, $line = 0);

    /**
     * Builds a new catch statement instance.
     *
     * @param integer $line The line number of this catch statement.
     *
     * @return PHP_Reflection_AST_CatchStatement
     */
    function buildCatchStatement($line);

    /**
     * Builds a new code block instance.
     *
     * @param integer $line The line number of this block.
     *
     * @return PHP_Reflection_AST_Block
     */
    function buildBlock($line);

    /**
     * Builds a new code closure instance.
     *
     * @param integer $line The line number of this block.
     *
     * @return PHP_Reflection_AST_Closure
     */
    function buildClosure($line);

    /**
     * Builds a new code new expression.
     *
     * @param integer $line The line number of this expression.
     *
     * @return PHP_Reflection_AST_NewExpression
     */
    function buildNewExpression($line);

    /**
     * Builds a new code instance of expression.
     *
     * @param integer $line The line number of this instance of expression.
     *
     * @return PHP_Reflection_AST_InstanceOfExpression
     */
    function buildInstanceOfExpression($line);

    /**
     * Builds a new code if statement.
     *
     * @param integer $line The line number of this if statement.
     *
     * @return PHP_Reflection_AST_IfStatement
     */
    function buildIfStatement($line);

    /**
     * Builds a new code else statement.
     *
     * @param integer $line The line number of this else statement.
     *
     * @return PHP_Reflection_AST_ElseStatement
     */
    function buildElseStatement($line);

    /**
     * Builds a new <b>elseif</b>/<b>else if</b> statement.
     *
     * @param integer $line The line number of this else if statement.
     *
     * @return PHP_Reflection_AST_ElseIfStatement
     */
    function buildElseIfStatement($line);

    /**
     * Builds a new <b>for</b>-statement.
     *
     * @param integer $line The line number of this for statement.
     *
     * @return PHP_Reflection_AST_ForStatement
     */
    function buildForStatement($line);

    /**
     * Builds a new <b>foreach</b>-statement.
     *
     * @param integer $line The line number of this foreach statement.
     *
     * @return PHP_Reflection_AST_ForeachStatement
     */
    function buildForeachStatement($line);

    /**
     * Builds a new <b>while</b>-statement.
     *
     * @param integer $line The line number of this while statement.
     *
     * @return PHP_Reflection_AST_WhileStatement
     */
    function buildWhileStatement($line);

    /**
     * Builds a new <b>do while</b>-statement.
     *
     * @param integer $line The line number of this do while statement.
     *
     * @return PHP_Reflection_AST_DoWhileStatement
     */
    function buildDoWhileStatement($line);

    /**
     * Builds a new <b>switch</b>-statement.
     *
     * @param integer $line The line number of this switch statement.
     *
     * @return PHP_Reflection_AST_SwitchStatement
     */
    function buildSwitchStatement($line);

    /**
     * Builds a new <b>case</b>-statement.
     *
     * @param integer $line The line number of this case statement.
     *
     * @return PHP_Reflection_AST_CaseStatement
     */
    function buildCaseStatement($line);

    /**
     * Builds a new logical <b>and</b>-expression.
     *
     * @param integer $line The line number of this AND expression.
     *
     * @return PHP_Reflection_AST_LogicalAndExpression
     */
    function buildLogicalAndExpression($line);

    /**
     * Builds a new logical <b>or</b>-expression.
     *
     * @param integer $line The line number of this OR expression.
     *
     * @return PHP_Reflection_AST_LogicalOrExpression
     */
    function buildLogicalOrExpression($line);

    /**
     * Builds a new logical <b>xor</b>-expression.
     *
     * @param unknown_type $line The line number of this XOR expression.
     *
     * @return PHP_Reflection_AST_LogicalXorExpression
     */
    function buildLogicalXorExpression($line);

    /**
     * Builds a new boolean <b>&&</b>-expression.
     *
     * @param unknown_type $line The line number of this AND expression.
     *
     * @return PHP_Reflection_AST_BooleanAndExpression
     */
    function buildBooleanAndExpression($line);

    /**
     * Builds a new boolean <b>||</b>-expression.
     *
     * @param unknown_type $line The line number of this OR expression.
     *
     * @return PHP_Reflection_AST_BooleanOrExpression
     */
    function buildBooleanOrExpression($line);

    /**
     * Builds a new array value instance.
     *
     * @return PHP_Reflection_AST_ArrayExpression
     */
    function buildArrayExpression();

    /**
     * Builds an array element instance.
     *
     * @return PHP_Reflection_AST_ArrayElement
     */
    function buildArrayElement();

    /**
     * Builds a constant reference instance.
     *
     * @param string $identifier The constant identifier.
     *
     * @return PHP_Reflection_AST_ConstantValue
     */
    function buildConstantValue($identifier);

    /**
     * Builds a class or interface constant reference instance.
     *
     * @param PHP_Reflection_AST_ClassOrInterfaceI $owner      The owner node.
     * @param string                               $identifier The constant name.
     *
     * @return PHP_Reflection_AST_ClassOrInterfaceConstantValue
     */
    function buildClassOrInterfaceConstantValue(
            PHP_Reflection_AST_ClassOrInterfaceI $owner, $identifier);

    /**
     * Builds a class or interface proxy instance.
     *
     * The identifier of the proxied class or interface.
     *
     * @return PHP_Reflection_AST_ClassOrInterfaceProxy
     */
    function buildClassOrInterfaceProxy($identifier);

    /**
     * Builds a new null value instance.
     *
     * @return PHP_Reflection_AST_MemberNullValue
     */
    function buildNullValue();

    /**
     * Builds a new true value instance.
     *
     * @return PHP_Reflection_AST_MemberTrueValue
     */
    function buildTrueValue();

    /**
     * Builds a new false value instance.
     *
     * @return PHP_Reflection_AST_MemberFalseValue
     */
    function buildFalseValue();

    /**
     * Builds a new numeric value instance.
     *
     * @param integer $type     The type of this value.
     * @param string  $value    The string representation of the php value.
     * @param boolean $negative Is this numeric value negative?
     *
     * @return PHP_Reflection_AST_MemberNumericValue
     */
    function buildNumericValue($type, $value, $negative);

    /**
     * Builds a new scalar value instance.
     *
     * @param integer $type  The type of this value.
     * @param string  $value The string representation of the php value.
     *
     * @return PHP_Reflection_AST_MemberScalarValue
     */
    function buildScalarValue($type, $value = null);
}