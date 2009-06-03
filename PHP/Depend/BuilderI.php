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
     * Builds a new code class constant instance.
     *
     * @param string $name The constant name.
     *
     * @return PHP_Depend_Code_TypeConstant The created constant object.
     */
    function buildTypeConstant($name);

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
}