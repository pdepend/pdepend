<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
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
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once 'PHP/Depend/Code/ASTFormalParameter.php';
require_once 'PHP/Depend/Code/ASTFormalParameters.php';
require_once 'PHP/Depend/Code/ASTVariableDeclarator.php';
require_once 'PHP/Depend/Code/ASTAllocationExpression.php';
require_once 'PHP/Depend/Code/ASTClassOrInterfaceReference.php';
require_once 'PHP/Depend/Code/ASTStaticVariableDeclaration.php';

require_once 'PHP/Depend/Code/AbstractItem.php';
require_once 'PHP/Depend/Code/NodeIterator.php';
require_once 'PHP/Depend/Code/ClassOrInterfaceReferenceIterator.php';

/**
 * Abstract base class for callable objects.
 *
 * Callable objects is a generic parent for methods and functions.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
abstract class PHP_Depend_Code_AbstractCallable extends PHP_Depend_Code_AbstractItem
{
    /**
     * A reference instance for the return value of this callable. By
     * default and for any scalar type this property is <b>null</b>.
     *
     * @var PHP_Depend_Code_ASTClassOrInterfaceReference $_returnClassReference
     * @since 0.9.5
     */
    private $_returnClassReference = null;

    /**
     * List of all exceptions classes referenced by this callable.
     *
     * @var array(PHP_Depend_Code_ASTClassOrInterfaceReference)
     * @since 0.9.5
     */
    private $_exceptionClassReferences = array();

    /**
     * List of class references for all classes or interfaces this callable
     * depends on.
     *
     * @var array(PHP_Depend_Code_ASTClassOrInterfaceReference)
     * @since 0.9.5
     */
    private $_dependencyClassReferences = array();

    /**
     * List of method/function parameters.
     *
     * @var PHP_Depend_Code_NodeIterator $_parameters
     */
    private $_parameters = null;

    /**
     * Does this callable return a value by reference?
     *
     * @var boolean $_returnsReference
     */
    private $_returnsReference = false;

    /**
     * List of all parsed child nodes.
     *
     * @var array(PHP_Depend_Code_ASTNodeI) $_nodes
     * @since 0.9.6
     */
    private $_nodes = array();

    /**
     * The start line number of the method or function declaration.
     *
     * @var integer
     * @since 0.9.12
     */
    private $_startLine = 0;

    /**
     * The end line number of the method or function declaration.
     *
     * @var integer
     * @since 0.9.12
     */
    private $_endLine = 0;

    /**
     * Adds a parsed child node to this node.
     *
     * @param PHP_Depend_Code_ASTNodeI $node A parsed child node instance.
     *
     * @return void
     * @access private
     * @since 0.9.6
     */
    public function addChild(PHP_Depend_Code_ASTNodeI $node)
    {
        $this->_nodes[] = $node;
    }

    /**
     * Returns all child nodes of this method.
     *
     * @return array(PHP_Depend_Code_ASTNodeI)
     * @since 0.9.8
     */
    public function getChildren()
    {
        return $this->_nodes;
    }

    /**
     * This method will search recursive for the first child node that is an
     * instance of the given <b>$targetType</b>. The returned value will be
     * <b>null</b> if no child exists for that.
     *
     * @param string $targetType Searched class or interface type.
     *
     * @return PHP_Depend_Code_ASTNodeI
     * @access private
     * @since 0.9.6
     */
    public function getFirstChildOfType($targetType)
    {
        foreach ($this->_nodes as $node) {
            if ($node instanceof $targetType) {
                return $node;
            }
            if (($child = $node->getFirstChildOfType($targetType)) !== null) {
                return $child;
            }
        }
        return null;
    }

    /**
     * Will find all children for the given type.
     *
     * @param string $targetType The target class or interface type.
     * @param array  &$results   The found children.
     *
     * @return array(PHP_Depend_Code_ASTNodeI)
     * @access private
     * @since 0.9.6
     */
    public function findChildrenOfType($targetType, array &$results = array())
    {
        foreach ($this->_nodes as $node) {
            if ($node instanceof $targetType) {
                $results[] = $node;
            }
            $node->findChildrenOfType($targetType, $results);
        }
        return $results;
    }

    /**
     * Returns the tokens found in the function body.
     *
     * @return array(mixed)
     */
    public function getTokens()
    {
        $storage = PHP_Depend_StorageRegistry::get(PHP_Depend::TOKEN_STORAGE);
        return (array) $storage->restore($this->getUUID(), get_class($this));
    }

    /**
     * Sets the tokens found in the function body.
     *
     * @param array(mixed) $tokens The body tokens.
     *
     * @return void
     */
    public function setTokens(array $tokens)
    {
        $this->_startLine = reset($tokens)->startLine;
        $this->_endLine   = end($tokens)->endLine;
        
        $storage = PHP_Depend_StorageRegistry::get(PHP_Depend::TOKEN_STORAGE);
        $storage->store($tokens, $this->getUUID(), get_class($this));
    }

    /**
     * Returns the line number where the callable declaration starts.
     *
     * @return integer
     * @since 0.9.6
     */
    public function getStartLine()
    {
        return $this->_startLine;
    }

    /**
     * Returns the line number where the callable declaration ends.
     *
     * @return integer
     * @since 0.9.6
     */
    public function getEndLine()
    {
        return $this->_endLine;
    }

    /**
     * Returns all {@link PHP_Depend_Code_AbstractClassOrInterface} objects this
     * function depends on.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getDependencies()
    {
        $classReferences = $this->_dependencyClassReferences;

        $references = $this->findChildrenOfType(
            PHP_Depend_Code_ASTClassOrInterfaceReference::CLAZZ
        );
        foreach ($references as $reference) {
            $classReferences[] = $reference;
        }

        return new PHP_Depend_Code_ClassOrInterfaceReferenceIterator(
            $classReferences
        );
    }

    /**
     * This method will return a class or interface instance that represents
     * the return value of this callable. The returned value will be <b>null</b>
     * if there is no return value or the return value is scalat.
     *
     * @return PHP_Depend_Code_ASTClassOrInterfaceReference
     * @since 0.9.5
     */
    public function getReturnClass()
    {
        if ($this->_returnClassReference === null) {
            return null;
        }
        return $this->_returnClassReference->getType();
    }

    /**
     * This method can be used to set a reference instance for the declared
     * function return type.
     *
     * @param PHP_Depend_Code_ASTClassOrInterfaceReference $classReference Holder
     *        instance for the declared function return type.
     *
     * @return void
     * @since 0.9.5
     */
    public function setReturnClassReference(
        PHP_Depend_Code_ASTClassOrInterfaceReference $classReference
    ) {
        $this->_returnClassReference = $classReference;
    }

    /**
     * Adds a reference holder for a thrown exception class or interface to
     * this callable.
     *
     * @param PHP_Depend_Code_ASTClassOrInterfaceReference $classReference A
     *        reference instance for a thrown exception.
     *
     * @return void
     * @since 0.9.5
     */
    public function addExceptionClassReference(
        PHP_Depend_Code_ASTClassOrInterfaceReference $classReference
    ) {
        $this->_exceptionClassReferences[] = $classReference;
    }

    /**
     * Returns an iterator with thrown exception
     * {@link PHP_Depend_Code_AbstractClassOrInterface} instances.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getExceptionClasses()
    {
        return new PHP_Depend_Code_ClassOrInterfaceReferenceIterator(
            $this->_exceptionClassReferences
        );
    }

    /**
     * Returns an iterator with all method/function parameters.
     *
     * <b>NOTE:</b> All node iterators return an alphabetic ordered list of
     * nodes. Use the {@link PHP_Depend_Code_Parameter::getPosition()} for the
     * correct parameter position.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    public function getParameters()
    {
        if ($this->_parameters === null) {
            $this->_initParameters();
        }
        $this->_parameters->rewind();
        
        return $this->_parameters;
    }

    /**
     * This method will return <b>true</b> when this method returns a value by
     * reference, otherwise the return value will be <b>false</b>.
     *
     * @return boolean
     * @since 0.9.5
     */
    public function returnsReference()
    {
        return $this->_returnsReference;
    }

    /**
     * A call to this method will flag the callable instance with the returns
     * reference flag, which means that the context function or method returns
     * a value by reference.
     *
     * @return void
     * @since 0.9.5
     */
    public function setReturnsReference()
    {
        $this->_returnsReference = true;
    }

    /**
     * Returns an array with all declared static variables.
     *
     * @return array(string=>mixed)
     * @since 0.9.6
     */
    public function getStaticVariables()
    {
        $staticVariables = array();

        $declarations = $this->findChildrenOfType(
            PHP_Depend_Code_ASTStaticVariableDeclaration::CLAZZ
        );
        foreach ($declarations as $declaration) {
            $variables = $declaration->findChildrenOfType(
                PHP_Depend_Code_ASTVariableDeclarator::CLAZZ
            );
            foreach ($variables as $variable) {
                $image = $variable->getImage();
                $value = $variable->getValue();
                if ($value !== null) {
                    $value = $value->getValue();
                }

                $staticVariables[substr($image, 1)] = $value;
            }
        }
        return $staticVariables;
    }

    /**
     * This method will initialize the <b>$_parameters</b> property.
     *
     * @return void
     * @since 0.9.6
     */
    private function _initParameters()
    {
        $parameters = array();

        $formalParameters = $this->getFirstChildOfType(
            PHP_Depend_Code_ASTFormalParameters::CLAZZ
        );

        $formalParameters = $formalParameters->findChildrenOfType(
            PHP_Depend_Code_ASTFormalParameter::CLAZZ
        );

        foreach ($formalParameters as $formalParameter) {
            $parameter = new PHP_Depend_Code_Parameter(
                $formalParameter
            );
            $parameter->setDeclaringFunction($this);
            $parameter->setPosition(count($parameters));

            $parameters[] = $parameter;
        }

        $optional = true;
        foreach (array_reverse($parameters) as $parameter) {
            if ($parameter->isDefaultValueAvailable() === false) {
                $optional = false;
            }
            $parameter->setOptional($optional);
        }

        $this->_parameters = new PHP_Depend_Code_NodeIterator($parameters);
    }

    /**
     * This method can be called by the PHP_Depend runtime environment or a
     * utilizing component to free up memory. This methods are required for
     * PHP version < 5.3 where cyclic references can not be resolved
     * automatically by PHP's garbage collector.
     *
     * @return void
     * @since 0.9.12
     */
    public function free()
    {
        $this->_removeReferencesToParameters();
        $this->_removeReferencesToNodes();
    }

    /**
     * Free memory consumed by parameters associated with this callable instance.
     *
     * @return void
     * @since 0.9.12
     */
    private function _removeReferencesToParameters()
    {
        $this->getParameters()->free();
        $this->_parameters = array();
    }

    /**
     * Free memory consumed by the ast nodes associated with this callable
     * instance.
     *
     * @return void
     * @since 0.9.12
     */
    private function _removeReferencesToNodes()
    {
        foreach ($this->_nodes as $i => $node) {
            $node->free();
        }
        $this->_nodes = array();
    }

    // DEPRECATED METHODS AND PROPERTIES
    // @codeCoverageIgnoreStart

    /**
     * Sets the start line for this item.
     *
     * @param integer $startLine The start line for this item.
     *
     * @return void
     * @deprecated Since version 0.9.6
     */
    public function setStartLine($startLine)
    {
        fwrite(STDERR, 'Since 0.9.6 ' . __METHOD__ . '() is deprecated.' . PHP_EOL);
        $this->startLine = $startLine;
    }

    /**
     * Sets the end line for this item.
     *
     * @param integer $endLine The end line for this item
     *
     * @return void
     * @deprecated Since version 0.9.6
     */
    public function setEndLine($endLine)
    {
        fwrite(STDERR, 'Since 0.9.6 ' . __METHOD__ . '() is deprecated.' . PHP_EOL);
        $this->endLine = $endLine;
    }

    // @codeCoverageIgnoreEnd
}
