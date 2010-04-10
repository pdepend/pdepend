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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Builder
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once 'PHP/Depend/BuilderI.php';
require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Interface.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Code/Method.php';
require_once 'PHP/Depend/Code/Package.php';
require_once 'PHP/Depend/Code/Parameter.php';
require_once 'PHP/Depend/Code/Property.php';
require_once 'PHP/Depend/Util/Log.php';
require_once 'PHP/Depend/Util/Type.php';

/**
 * Default code tree builder implementation.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Builder
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @author     Jan Schumann <js@schumann-it.com>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Builder_Default implements PHP_Depend_BuilderI
{
    /**
     * Default package which contains all functions and classes with an unknown
     * scope.
     *
     * @var PHP_Depend_Code_Package $defaultPackage
     */
    protected $defaultPackage = null;

    /**
     * Default source file that acts as a dummy.
     *
     * @var PHP_Depend_Code_File $defaultFile
     */
    protected $defaultFile = null;

    /**
     * All generated {@link PHP_Depend_Code_Class} objects
     *
     * @var array(string=>PHP_Depend_Code_Class) $_classes
     */
    private $_classes = array();

    /**
     * All generated {@link PHP_Depend_Code_Interface} instances.
     *
     * @var array(string=>PHP_Depend_Code_Interface) $_interfaces
     */
    private $_interfaces = array();

    /**
     * All generated {@link PHP_Depend_Code_Package} objects
     *
     * @var array(string=>PHP_Depend_Code_Package) $_packages
     */
    private $_packages = array();

    /**
     * Internal status flag used to check that a build request is internal.
     *
     * @var boolean $_internal
     */
    private $_internal = false;

    /**
     * Internal used flag that marks the parsing process as frozen.
     *
     * @var boolean $_frozen
     */
    private $_frozen = false;

    /**
     * Cache of all classes created during the regular parsing process.
     *
     * @var array(PHP_Depend_Code_Class) $_frozenClasses
     */
    private $_frozenClasses = array();

    /**
     * Cache of all interfaces created during the regular parsing process.
     *
     * @var array(PHP_Depend_Code_Interface) $_frozenInterfaces
     */
    private $_frozenInterfaces = array();

    /**
     * Constructs a new builder instance.
     */
    public function __construct()
    {
        $this->defaultPackage = new PHP_Depend_Code_Package(self::DEFAULT_PACKAGE);
        $this->defaultFile    = new PHP_Depend_Code_File(null);

        $this->_packages[self::DEFAULT_PACKAGE] = $this->defaultPackage;
    }

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     *
     * @return PHP_Depend_Code_ASTClassOrInterfaceReference
     * @since 0.9.5
     */
    public function buildASTClassOrInterfaceReference($qualifiedName)
    {
        $this->checkBuilderState();

        include_once 'PHP/Depend/Code/ASTClassOrInterfaceReference.php';

        // Debug method creation
        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTClassOrInterfaceReference(' .
            $qualifiedName .
            ')'
        );

        return new PHP_Depend_Code_ASTClassOrInterfaceReference(
            $this,
            $qualifiedName
        );
    }

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
     * @return PHP_Depend_Code_Class The created class object.
     */
    public function buildClass($name)
    {
        $this->checkBuilderState();
        
        $className   = $this->extractTypeName($name);
        $packageName = $this->extractPackageName($name);

        $class = new PHP_Depend_Code_Class($className);
        $class->setSourceFile($this->defaultFile);

        $this->storeClass($className, $packageName, $class);

        return $class;
    }

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
    public function getClass($qualifiedName)
    {
        $class = $this->findClass($qualifiedName);
        if ($class === null) {
            $class = $this->buildClassInternal($qualifiedName);
        }
        return $class;
    }

    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     *
     * @return PHP_Depend_Code_ASTClassReference
     * @since 0.9.5
     */
    public function buildASTClassReference($qualifiedName)
    {
        $this->checkBuilderState();
        
        include_once 'PHP/Depend/Code/ASTClassReference.php';

        // Debug method creation
        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTClassReference(' . $qualifiedName . ')'
        );

        return new PHP_Depend_Code_ASTClassReference($this, $qualifiedName);
    }

    /**
     * Builds a new closure instance.
     *
     * @return PHP_Depend_Code_Closure
     */
    public function buildClosure()
    {
        $this->checkBuilderState();

        include_once 'PHP/Depend/Code/Closure.php';

        // Debug type constant creation
        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_Closure()'
        );

        return new PHP_Depend_Code_Closure();
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
     * @return PHP_Depend_Code_Interface The created interface object.
     */
    public function buildInterface($name)
    {
        $this->checkBuilderState();
        
        $interfaceName = $this->extractTypeName($name);
        $packageName   = $this->extractPackageName($name);

        $interface = new PHP_Depend_Code_Interface($interfaceName);
        $interface->setSourceFile($this->defaultFile);

        $this->storeInterface($interfaceName, $packageName, $interface);

        return $interface;
    }

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
    public function getInterface($qualifiedName)
    {
        $interface = $this->findInterface($qualifiedName);
        if ($interface === null) {
            $interface = $this->buildInterfaceInternal($qualifiedName);
        }
        return $interface;
    }
    
    /**
     * Builds a new code type reference instance.
     *
     * @param string $qualifiedName The qualified name of the referenced type.
     *
     * @return PHP_Depend_Code_ASTInterfaceReference
     * @since 0.9.5
     */
    public function buildInterfaceReference($qualifiedName)
    {
        $this->checkBuilderState();

        include_once 'PHP/Depend/Code/ASTInterfaceReference.php';

        // Debug method creation
        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTInterfaceReference(' . $qualifiedName . ')'
        );

        return new PHP_Depend_Code_ASTInterfaceReference($this, $qualifiedName);
    }

    /**
     * Builds a new method instance.
     *
     * @param string $name The method name.
     *
     * @return PHP_Depend_Code_Method The created class method object.
     */
    public function buildMethod($name)
    {
        $this->checkBuilderState();

        // Debug method creation
        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_Method(' . $name . ')'
        );

        // Create a new method instance
        return new PHP_Depend_Code_Method($name);
    }

    /**
     * Builds a new package instance.
     *
     * @param string $name The package name.
     *
     * @return PHP_Depend_Code_Package The created package object.
     */
    public function buildPackage($name)
    {
        if (!isset($this->_packages[$name])) {
            // Debug package creation
            PHP_Depend_Util_Log::debug(
                'Creating: PHP_Depend_Code_Package(' . $name . ')'
            );

            $this->_packages[$name] = new PHP_Depend_Code_Package($name);
        }
        return $this->_packages[$name];
    }

    /**
     * Builds a new function instance.
     *
     * @param string $name The function name.
     *
     * @return PHP_Depend_Code_Function The function instance.
     */
    public function buildFunction($name)
    {
        $this->checkBuilderState();

        // Debug function creation
        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_Function(' . $name . ')'
        );

        // Create new function
        $function = new PHP_Depend_Code_Function($name);
        $function->setSourceFile($this->defaultFile);
 
        return $function;
    }

    /**
     * Builds a new self reference instance.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $type The type instance
     *        that reference the concrete target of self.
     *
     * @return PHP_Depend_Code_ASTSelfReference
     * @since 0.9.6
     */
    public function buildASTSelfReference(
        PHP_Depend_Code_AbstractClassOrInterface $type
    ) {
        include_once 'PHP/Depend/Code/ASTSelfReference.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTSelfReference(' . $type->getName() . ')'
        );

        return new PHP_Depend_Code_ASTSelfReference($type);
    }

    /**
     * Builds a new parent reference instance.
     *
     * @param PHP_Depend_Code_ASTClassOrInterfaceReference $reference The type
     *        instance that reference the concrete target of parent.
     *
     * @return PHP_Depend_Code_ASTParentReference
     * @since 0.9.6
     */
    public function buildASTParentReference(
        PHP_Depend_Code_ASTClassOrInterfaceReference $reference
    ) {
        include_once 'PHP/Depend/Code/ASTParentReference.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTParentReference()'
        );

        return new PHP_Depend_Code_ASTParentReference($reference);
    }

    /**
     * Builds a new static reference instance.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $owner The owning instance
     *        that reference the concrete target of static.
     *
     * @return PHP_Depend_Code_ASTStaticReference
     * @since 0.9.6
     */
    public function buildASTStaticReference(
        PHP_Depend_Code_AbstractClassOrInterface $owner
    ) {
        include_once 'PHP/Depend/Code/ASTStaticReference.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTStaticReference()'
        );

        return new PHP_Depend_Code_ASTStaticReference($owner);
    }

    /**
     * Builds a new field declaration node.
     *
     * @return PHP_Depend_Code_ASTFieldDeclaration
     * @since 0.9.6
     */
    public function buildASTFieldDeclaration()
    {
        include_once 'PHP/Depend/Code/ASTFieldDeclaration.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTFieldDeclaration()'
        );

        return new PHP_Depend_Code_ASTFieldDeclaration();
    }

    /**
     * Builds a new variable declarator node.
     *
     * @param string $image The source image for the variable declarator.
     *
     * @return PHP_Depend_Code_ASTVariableDeclarator
     * @since 0.9.6
     */
    public function buildASTVariableDeclarator($image)
    {
        include_once 'PHP/Depend/Code/ASTVariableDeclarator.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTVariableDeclarator(' . $image . ')'
        );

        return new PHP_Depend_Code_ASTVariableDeclarator($image);
    }

    /**
     * Builds a new static variable declaration node.
     *
     * @param string $image The source image for the statuc declaration.
     *
     * @return PHP_Depend_Code_ASTStaticVariableDeclaration
     * @since 0.9.6
     */
    public function buildASTStaticVariableDeclaration($image)
    {
        include_once 'PHP/Depend/Code/ASTStaticVariableDeclaration.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTStaticVariableDeclaration(' . $image . ')'
        );

        return new PHP_Depend_Code_ASTStaticVariableDeclaration($image);
    }

    /**
     * Builds a new constant node.
     *
     * @param string $image The source image for the constant.
     *
     * @return PHP_Depend_Code_ASTConstant
     * @since 0.9.6
     */
    public function buildASTConstant($image)
    {
        include_once 'PHP/Depend/Code/ASTConstant.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTConstant(' . $image . ')'
        );

        return new PHP_Depend_Code_ASTConstant($image);
    }

    /**
     * Builds a new variable node.
     *
     * @param string $image The source image for the variable.
     *
     * @return PHP_Depend_Code_ASTVariable
     * @since 0.9.6
     */
    public function buildASTVariable($image)
    {
        include_once 'PHP/Depend/Code/ASTVariable.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTVariable(' . $image . ')'
        );

        return new PHP_Depend_Code_ASTVariable($image);
    }

    /**
     * Builds a new thisvariable node.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $type
     * @return PHP_Depend_Code_ASTThisVariable
     * @since 0.9.8
     */
    public function buildASTThisVariable(
        PHP_Depend_Code_AbstractClassOrInterface $type
    ) {
        include_once 'PHP/Depend/Code/ASTThisVariable.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTThisVariable(' . $type->getName() . ')'
        );

        return new PHP_Depend_Code_ASTThisVariable($type);
    }

    /**
     * Builds a new variable variable node.
     *
     * @param string $image The source image for the variable variable.
     *
     * @return PHP_Depend_Code_ASTVariableVariable
     * @since 0.9.6
     */
    public function buildASTVariableVariable($image)
    {
        include_once 'PHP/Depend/Code/ASTVariableVariable.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTVariableVariable(' . $image . ')'
        );

        return new PHP_Depend_Code_ASTVariableVariable($image);
    }

    /**
     * Builds a new compound variable node.
     *
     * @param string $image The source image for the compound variable.
     *
     * @return PHP_Depend_Code_ASTCompoundVariable
     * @since 0.9.6
     */
    public function buildASTCompoundVariable($image)
    {
        include_once 'PHP/Depend/Code/ASTCompoundVariable.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTCompoundVariable(' . $image . ')'
        );

        return new PHP_Depend_Code_ASTCompoundVariable($image);
    }

    /**
     * Builds a new compound expression node.
     *
     * @return PHP_Depend_Code_ASTCompoundExpression
     * @since 0.9.6
     */
    public function buildASTCompoundExpression()
    {
        include_once 'PHP/Depend/Code/ASTCompoundExpression.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTCompoundExpression()'
        );
        
        return new PHP_Depend_Code_ASTCompoundExpression();
    }

    /**
     * Builds a new formal parameters node.
     *
     * @return PHP_Depend_Code_ASTFormalParameters
     * @since 0.9.6
     */
    public function buildASTFormalParameters()
    {
        include_once 'PHP/Depend/Code/ASTFormalParameters.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTFormalParameters()'
        );

        return new PHP_Depend_Code_ASTFormalParameters();
    }

    /**
     * Builds a new formal parameter node.
     *
     * @return PHP_Depend_Code_ASTFormalParameter
     * @since 0.9.6
     */
    public function buildASTFormalParameter()
    {
        include_once 'PHP/Depend/Code/ASTFormalParameter.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTFormalParameter()'
        );

        return new PHP_Depend_Code_ASTFormalParameter();
    }

    /**
     * Builds a new expression node.
     *
     * @return PHP_Depend_Code_ASTExpression
     * @since 0.9.8
     */
    public function buildASTExpression()
    {
        include_once 'PHP/Depend/Code/ASTExpression.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTExpression');

        return new PHP_Depend_Code_ASTExpression();
    }

    /**
     * Builds a new assignment expression node.
     *
     * @param string $image The assignment operator.
     *
     * @return PHP_Depend_Code_ASTAssignmentExpression
     * @since 0.9.8
     */
    public function buildASTAssignmentExpression($image)
    {
        include_once 'PHP/Depend/Code/ASTAssignmentExpression.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTAssignmentExpression'
        );

        return new PHP_Depend_Code_ASTAssignmentExpression($image);
    }

    /**
     * Builds a new allocation expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return PHP_Depend_Code_ASTAllocationExpression
     * @since 0.9.6
     */
    public function buildASTAllocationExpression($image)
    {
        include_once 'PHP/Depend/Code/ASTAllocationExpression.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTAllocationExpression()'
        );

        return new PHP_Depend_Code_ASTAllocationExpression($image);
    }

    /**
     * Builds a new instanceof expression node.
     *
     * @param string $image The source image of this expression.
     *
     * @return PHP_Depend_Code_ASTInstanceOfExpression
     * @since 0.9.6
     */
    public function buildASTInstanceOfExpression($image)
    {
        include_once 'PHP/Depend/Code/ASTInstanceOfExpression.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTInstanceOfExpression()'
        );

        return new PHP_Depend_Code_ASTInstanceOfExpression($image);
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
     * @return PHP_Depend_Code_ASTConditionalExpression
     * @since 0.9.8
     */
    public function buildASTConditionalExpression()
    {
        include_once 'PHP/Depend/Code/ASTConditionalExpression.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTConditionalExpression'
        );

        return new PHP_Depend_Code_ASTConditionalExpression('?');
    }

    /**
     * Builds a new boolean and-expression.
     *
     * @return PHP_Depend_Code_ASTBooleanAndExpression
     * @since 0.9.8
     */
    public function buildASTBooleanAndExpression()
    {
        include_once 'PHP/Depend/Code/ASTBooleanAndExpression.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTBooleanAndExpression'
        );

        return new PHP_Depend_Code_ASTBooleanAndExpression('&&');
    }

    /**
     * Builds a new boolean or-expression.
     *
     * @return PHP_Depend_Code_ASTBooleanOrExpression
     * @since 0.9.8
     */
    public function buildASTBooleanOrExpression()
    {
        include_once 'PHP/Depend/Code/ASTBooleanOrExpression.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTBooleanOrExpression'
        );
        
        return new PHP_Depend_Code_ASTBooleanOrExpression('||');
    }

    /**
     * Builds a new logical <b>and</b>-expression.
     *
     * @return PHP_Depend_Code_ASTLogicalAndExpression
     * @since 0.9.8
     */
    public function buildASTLogicalAndExpression()
    {
        include_once 'PHP/Depend/Code/ASTLogicalAndExpression.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTLogicalAndExpression'
        );

        return new PHP_Depend_Code_ASTLogicalAndExpression('and');
    }

    /**
     * Builds a new logical <b>or</b>-expression.
     *
     * @return PHP_Depend_Code_ASTLogicalOrExpression
     * @since 0.9.8
     */
    public function buildASTLogicalOrExpression()
    {
        include_once 'PHP/Depend/Code/ASTLogicalOrExpression.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTLogicalOrExpression'
        );

        return new PHP_Depend_Code_ASTLogicalOrExpression('or');
    }

    /**
     * Builds a new logical <b>xor</b>-expression.
     *
     * @return PHP_Depend_Code_ASTLogicalXorExpression
     * @since 0.9.8
     */
    public function buildASTLogicalXorExpression()
    {
        include_once 'PHP/Depend/Code/ASTLogicalXorExpression.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTLogicalXorExpression'
        );

        return new PHP_Depend_Code_ASTLogicalXorExpression('xor');
    }

    /**
     * Builds a new switch-statement-node.
     *
     * @return PHP_Depend_Code_ASTSwitchStatement
     * @since 0.9.8
     */
    public function buildASTSwitchStatement()
    {
        include_once 'PHP/Depend/Code/ASTSwitchStatement.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTSwitchStatement'
        );

        return new PHP_Depend_Code_ASTSwitchStatement('switch');
    }

    /**
     * Builds a new switch-label node.
     *
     * @param string $image The source image of this label.
     *
     * @return PHP_Depend_Code_ASTSwitchLabel
     * @since 0.9.8
     */
    public function buildASTSwitchLabel($image)
    {
        include_once 'PHP/Depend/Code/ASTSwitchLabel.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTSwitchLabel');

        return new PHP_Depend_Code_ASTSwitchLabel($image);
    }

    /**
     * Builds a new catch-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTCatchStatement
     * @since 0.9.8
     */
    public function buildASTCatchStatement($image)
    {
        include_once 'PHP/Depend/Code/ASTCatchStatement.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTCatchStatement()');

        return new PHP_Depend_Code_ASTCatchStatement($image);
    }

    /**
     * Builds a new if statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTIfStatement
     * @since 0.9.8
     */
    public function buildASTIfStatement($image)
    {
        include_once 'PHP/Depend/Code/ASTIfStatement.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTIfStatement()');

        return new PHP_Depend_Code_ASTIfStatement($image);
    }

    /**
     * Builds a new elseif statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTElseIfStatement
     * @since 0.9.8
     */
    public function buildASTElseIfStatement($image)
    {
        include_once 'PHP/Depend/Code/ASTElseIfStatement.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTElseIfStatement()');
        
        return new PHP_Depend_Code_ASTElseIfStatement($image);
    }

    /**
     * Builds a new for statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTForStatement
     * @since 0.9.8
     */
    public function buildASTForStatement($image)
    {
        include_once 'PHP/Depend/Code/ASTForStatement.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTForStatement()');
        
        return new PHP_Depend_Code_ASTForStatement($image);
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
     * @return PHP_Depend_Code_ASTForInit
     * @since 0.9.8
     */
    public function buildASTForInit()
    {
        include_once 'PHP/Depend/Code/ASTForInit.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTForInit()');

        return new PHP_Depend_Code_ASTForInit();
    }

    /**
     * Builds a new foreach-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTForeachStatement
     * @since 0.9.8
     */
    public function buildASTForeachStatement($image)
    {
        include_once 'PHP/Depend/Code/ASTForeachStatement.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTForeachStatement()'
        );

        return new PHP_Depend_Code_ASTForeachStatement($image);
    }

    /**
     * Builds a new while-statement node.
     *
     * @param string $image The source image of this statement.
     *
     * @return PHP_Depend_Code_ASTWhileStatement
     * @since 0.9.8
     */
    public function buildASTWhileStatement($image)
    {
        include_once 'PHP/Depend/Code/ASTWhileStatement.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTWhileStatement()');
        
        return new PHP_Depend_Code_ASTWhileStatement($image);
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
     * @return PHP_Depend_Code_ASTMemberPrimaryPrefix
     * @since 0.9.6
     */
    public function buildASTMemberPrimaryPrefix($image)
    {
        include_once 'PHP/Depend/Code/ASTMemberPrimaryPrefix.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTMemberPrimaryPrefix()'
        );

        return new PHP_Depend_Code_ASTMemberPrimaryPrefix($image);
    }

    /**
     * Builds a new identifier node.
     *
     * @param string $image The image of this identifier.
     *
     * @return PHP_Depend_Code_ASTIdentifier
     * @since 0.9.6
     */
    public function buildASTIdentifier($image)
    {
        include_once 'PHP/Depend/Code/ASTIdentifier.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTIdentifier()');

        return new PHP_Depend_Code_ASTIdentifier($image);
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
     * @return PHP_Depend_Code_ASTFunctionPostfix
     * @since 0.9.6
     */
    public function buildASTFunctionPostfix($image)
    {
        include_once 'PHP/Depend/Code/ASTFunctionPostfix.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTFunctionPostfix()');

        return new PHP_Depend_Code_ASTFunctionPostfix($image);
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
     * @return PHP_Depend_Code_ASTMethodPostfix
     * @since 0.9.6
     */
    public function buildASTMethodPostfix($image)
    {
        include_once 'PHP/Depend/Code/ASTMethodPostfix.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTMethodPostfix()');

        return new PHP_Depend_Code_ASTMethodPostfix($image);
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
     * @return PHP_Depend_Code_ASTConstantPostfix
     * @since 0.9.6
     */
    public function buildASTConstantPostfix($image)
    {
        include_once 'PHP/Depend/Code/ASTConstantPostfix.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTConstantPostfix()');

        return new PHP_Depend_Code_ASTConstantPostfix($image);
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
     * @return PHP_Depend_Code_ASTPropertyPostfix
     * @since 0.9.6
     */
    public function buildASTPropertyPostfix($image)
    {
        include_once 'PHP/Depend/Code/ASTPropertyPostfix.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTPropertyPostfix()');

        return new PHP_Depend_Code_ASTPropertyPostfix($image);
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
     * @return PHP_Depend_Code_ASTArguments();
     * @since 0.9.6
     */
    public function buildASTArguments()
    {
        include_once 'PHP/Depend/Code/ASTArguments.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTArguments()');

        return new PHP_Depend_Code_ASTArguments();
    }

    /**
     * Builds a new array type node.
     *
     * @return PHP_Depend_Code_ASTArrayType
     * @since 0.9.6
     */
    public function buildASTArrayType()
    {
        include_once 'PHP/Depend/Code/ASTArrayType.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTArrayType()');

        return new PHP_Depend_Code_ASTArrayType();
    }

    /**
     * Builds a new primitive type node.
     *
     * @param string $image The source image for the primitive type.
     *
     * @return PHP_Depend_Code_ASTPrimitiveType
     * @since 0.9.6
     */
    public function buildASTPrimitiveType($image)
    {
        include_once 'PHP/Depend/Code/ASTPrimitiveType.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTPrimitiveType()');

        return new PHP_Depend_Code_ASTPrimitiveType($image);
    }

    /**
     * Builds a new literal node.
     *
     * @param string $image The source image for the literal node.
     *
     * @return PHP_Depend_Code_ASTLiteral
     * @since 0.9.6
     */
    public function buildASTLiteral($image)
    {
        include_once 'PHP/Depend/Code/ASTLiteral.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTLiteral()');

        return new PHP_Depend_Code_ASTLiteral($image);
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
     * @return PHP_Depend_Code_ASTConstantDefinition
     * @since 0.9.6
     */
    public function buildASTConstantDefinition($image)
    {
        include_once 'PHP/Depend/Code/ASTConstantDefinition.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTConstantDefinition()'
        );

        return new PHP_Depend_Code_ASTConstantDefinition($image);
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
     * @return PHP_Depend_Code_ASTConstantDeclarator
     * @since 0.9.6
     */
    public function buildASTConstantDeclarator($image)
    {
        include_once 'PHP/Depend/Code/ASTConstantDeclarator.php';

        PHP_Depend_Util_Log::debug(
            'Creating: PHP_Depend_Code_ASTConstantDeclarator(' . $image . ')'
        );

        return new PHP_Depend_Code_ASTConstantDeclarator($image);
    }

    /**
     * Builds a new comment node instance.
     *
     * @param string $cdata The comment text.
     *
     * @return PHP_Depend_Code_ASTComment
     * @since 0.9.8
     */
    public function buildASTComment($cdata)
    {
        include_once 'PHP/Depend/Code/ASTComment.php';

        PHP_Depend_Util_Log::debug('Creating: PHP_Depend_Code_ASTComment()');

        return new PHP_Depend_Code_ASTComment($cdata);
    }

    /**
     * Returns an iterator with all generated {@link PHP_Depend_Code_Package}
     * objects.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getIterator()
    {
        return $this->getPackages();
    }

    /**
     * Returns an iterator with all generated {@link PHP_Depend_Code_Package}
     * objects.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getPackages()
    {
        // Create a package array copy
        $packages = $this->_packages;

        // Remove default package if empty
        if ($this->defaultPackage->getTypes()->count() === 0
            && $this->defaultPackage->getFunctions()->count() === 0
        ) {
            unset($packages[self::DEFAULT_PACKAGE]);
        }
        return new PHP_Depend_Code_NodeIterator($packages);
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
     * @param string $qualifiedName The full qualified interface name.
     *
     * @return PHP_Depend_Code_Interface
     * @since 0.9.5
     */
    protected function buildInterfaceInternal($qualifiedName)
    {
        $this->_internal = true;

        $package = $this->buildPackage($this->extractPackageName($qualifiedName));
        return $package->addType(
            $this->buildInterface($qualifiedName)
        );
    }

    /**
     * This method tries to find an interface instance matching for the given
     * qualified name in all scopes already processed. It will return the best
     * matching instance or <b>null</b> if no match exists.
     *
     * @param string $qualifiedName The qualified interface name.
     *
     * @return PHP_Depend_Code_Interface
     * @since 0.9.5
     */
    protected function findInterface($qualifiedName)
    {
        $this->freeze();

        $interface = $this->findClassOrInterface(
            $this->_frozenInterfaces,
            $qualifiedName
        );

        if ($interface === null) {
            $interface = $this->findClassOrInterface(
                $this->_interfaces,
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
     * @param string $qualifiedName The qualified class name.
     *
     * @return PHP_Depend_Code_Class
     * @since 0.9.5
     */
    protected function buildClassInternal($qualifiedName)
    {
        $this->_internal = true;

        $package = $this->buildPackage($this->extractPackageName($qualifiedName));
        return $package->addType(
            $this->buildClass($qualifiedName)
        );
    }

    /**
     * This method tries to find a class instance matching for the given
     * qualified name in all scopes already processed. It will return the best
     * matching instance or <b>null</b> if no match exists.
     *
     * @param string $qualifiedName The qualified class name.
     *
     * @return PHP_Depend_Code_Class
     * @since 0.9.5
     */
    protected function findClass($qualifiedName)
    {
        $this->freeze();

        $class = $this->findClassOrInterface(
            $this->_frozenClasses,
            $qualifiedName
        );

        if ($class === null) {
            $class = $this->findClassOrInterface($this->_classes, $qualifiedName);
        }
        return $class;
    }

    /**
     * This method tries to find an interface or class instance matching for the
     * given qualified name in all scopes already processed. It will return the
     * best matching instance or <b>null</b> if no match exists.
     *
     * @param array  $instances     Map of already created instances.
     * @param string $qualifiedName The qualified interface or class name.
     *
     * @return PHP_Depend_Code_AbstractClassOrInterface
     * @since 0.9.5
     */
    protected function findClassOrInterface(array $instances, $qualifiedName)
    {
        $classOrInterfaceName = $this->extractTypeName($qualifiedName);
        $packageName          = $this->extractPackageName($qualifiedName);

        $caseInsensitiveName = strtolower($classOrInterfaceName);

        if (!isset($instances[$caseInsensitiveName])) {
            return null;
        }

        // Check for exact match and return first matching instance
        if (isset($instances[$caseInsensitiveName][$packageName])) {
            return reset($instances[$caseInsensitiveName][$packageName]);
        }

        if (!$this->isDefault($packageName)) {
            return null;
        }

        $classesOrInterfaces = reset($instances[$caseInsensitiveName]);
        return reset($classesOrInterfaces);
    }

    /**
     * This method will freeze the actual builder state and create a second
     * runtime scope.
     *
     * @return void
     * @since 0.9.5
     */
    protected function freeze()
    {
        if ($this->_frozen === true) {
            return;
        }

        $this->_frozen = true;

        $this->_frozenClasses    = $this->_copyTypesWithPackage($this->_classes);
        $this->_frozenInterfaces = $this->_copyTypesWithPackage($this->_interfaces);
        //$this->_frozenClasses    = $this->_classes;
        //$this->_frozenInterfaces = $this->_interfaces;

        $this->_classes    = array();
        $this->_interfaces = array();
    }

    /**
     * Creates a copy of the given input array, but skips all types that do not
     * contain a parent package.
     *
     * @param array $originalTypes The original types created during the parsing
     *        process.
     *
     * @return array
     */
    private function _copyTypesWithPackage(array $originalTypes)
    {
        $copiedTypes = array();
        foreach ($originalTypes as $typeName => $packages) {
            foreach ($packages as $package => $types) {
                foreach ($types as $index => $type) {
                    if (is_object($type->getPackage())) {
                        $copiedTypes[$typeName][$package][$index] = $type;
                    }
                }
            }
        }
        return $copiedTypes;
    }

    /**
     * This method will persist a class instance for later reuse.
     *
     * @param string                $className   The local class name.
     * @param string                $packageName The package name
     * @param PHP_Depend_Code_Class $class       The context class.
     *
     * @return void
     * @@since 0.9.5
     */
    protected function storeClass(
        $className, $packageName, PHP_Depend_Code_Class $class
    ) {
        $caseInsensitiveName = strtolower($className);
        if (!isset($this->_classes[$caseInsensitiveName][$packageName])) {
            $this->_classes[$caseInsensitiveName][$packageName] = array();
        }
        $this->_classes[$caseInsensitiveName][$packageName][] = $class;
    }

    /**
     * This method will persist an interface instance for later reuse.
     *
     * @param string                    $interfaceName The local interface name.
     * @param string                    $packageName   The package name
     * @param PHP_Depend_Code_Interface $interface     The context interface.
     *
     * @return void
     * @@since 0.9.5
     */
    protected function storeInterface(
        $interfaceName, $packageName, PHP_Depend_Code_Interface $interface
    ) {
        $caseInsensitiveName = strtolower($interfaceName);
        if (!isset($this->_interfaces[$caseInsensitiveName][$packageName])) {
            $this->_interfaces[$caseInsensitiveName][$packageName] = array();
        }
        $this->_interfaces[$caseInsensitiveName][$packageName][] = $interface;
    }

    /**
     * Checks that the parser is not frozen or a request is flagged as internal.
     *
     * @param boolean $internal The new internal flag value.
     *
     * @return void
     * @since 0.9.5
     */
    protected function checkBuilderState($internal = false)
    {
        if ($this->_frozen === true && $this->_internal === false) {
            throw new BadMethodCallException(
                'Cannot create new nodes, when internal state is frozen.'
            );
        }
        $this->_internal = $internal;
    }


    /**
     * Returns <b>true</b> if the given package is the default package.
     *
     * @param string $packageName The package name.
     *
     * @return boolean
     */
    protected function isDefault($packageName)
    {
        return ($packageName === self::DEFAULT_PACKAGE);
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
     *   $packageName = $this->extractPackageName('foo\bar\foobar');
     *   var_dump($packageName);
     *   // Results in:
     *   // string(8) "foo\bar"
     *
     *   $packageName = $this->extractPackageName('foobar');
     *   var_dump($packageName);
     *   // Results in:
     *   // string(6) "+global"
     * </code>
     *
     * @param string $qualifiedName The qualified PHP 5.3 class identifier.
     *
     * @return string
     */
    protected function extractPackageName($qualifiedName)
    {
        if (($pos = strrpos($qualifiedName, '\\')) !== false) {
            // Extract namespace part from qualified name
            $namespaceName = substr($qualifiedName, 0, $pos);
            // Check for leading backslash
            if (strpos($namespaceName, '\\') === 0) {
                return substr($namespaceName, 1);
            }
            return $namespaceName;
        } else if (PHP_Depend_Util_Type::isInternalType($qualifiedName)) {
            return PHP_Depend_Util_Type::getTypePackage($qualifiedName);
        }
        return self::DEFAULT_PACKAGE;
    }

    // DEPRECATED METHODS AND PROPERTIES
    // @codeCoverageIgnoreStart

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
     * @deprecated Since version 0.9.5, use getClassOrInterface() instead.
     */
    public function buildClassOrInterface($name)
    {
        fwrite(STDERR, 'Since 0.9.5 ' . __METHOD__ . '() is deprecated.' . PHP_EOL);
        
        $cls = $this->extractTypeName($name);
        $pkg = $this->extractPackageName($name);

        $typeID = strtolower($cls);

        if (isset($this->_classes[$typeID][$pkg])) {
            $instance = $this->_classes[$typeID][$pkg];
        } else if (isset($this->_interfaces[$typeID][$pkg])) {
            $instance = $this->_interfaces[$typeID][$pkg];
        } else if (isset($this->_classes[$typeID])) {
            $instance = reset($this->_classes[$typeID]);
        } else if (isset($this->_interfaces[$typeID])) {
            $instance = reset($this->_interfaces[$typeID]);
        } else {
            $instance = $this->buildClass($name);
        }
        return $instance;
    }

    /**
     * Builds a new code class constant instance.
     *
     * @param string $name The constant name.
     *
     * @return PHP_Depend_Code_TypeConstant The created constant object.
     * @deprecated Since version 0.9.6
     */
    public function buildTypeConstant($name)
    {
        fwrite(STDERR, 'Since 0.9.6 ' . __METHOD__ . '() is deprecated.' . PHP_EOL);

        // Include class definition
        include_once 'PHP/Depend/Code/TypeConstant.php';

        // Create new constant instance.
        return new PHP_Depend_Code_TypeConstant($name);
    }

    // @codeCoverageIgnoreEnd
}