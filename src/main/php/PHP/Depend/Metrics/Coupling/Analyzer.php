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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

/**
 * This analyzer collects coupling values for the hole project. It calculates
 * all function and method <b>calls</b> and the <b>fanout</b>, that means the
 * number of referenced types.
 *
 * The FANOUT calculation is based on the definition used by the apache maven
 * project.
 *
 * <ul>
 *   <li>field declarations (Uses doc comment annotations)</li>
 *   <li>formal parameters and return types (The return type uses doc comment
 *   annotations)</li>
 *   <li>throws declarations (Uses doc comment annotations)</li>
 *   <li>local variables</li>
 * </ul>
 *
 * http://www.jajakarta.org/turbine/en/turbine/maven/reference/metrics.html
 *
 * The implemented algorithm counts each type only once for a method and function.
 * Any type that is either a supertype or a subtype of the class is not counted.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Metrics_Coupling_Analyzer
       extends PHP_Depend_Metrics_AbstractAnalyzer
    implements PHP_Depend_Metrics_NodeAwareI,
               PHP_Depend_Metrics_ProjectAwareI
{
    /**
     * Type of this analyzer class.
     */
    const CLAZZ = __CLASS__;

    /**
     * Metrics provided by the analyzer implementation.
     */
    const M_CALLS  = 'calls',
          M_FANOUT = 'fanout',
          M_CA     = 'ca',
          M_CBO    = 'cbo',
          M_CE     = 'ce';

    /**
     * Has this analyzer already processed the source under test?
     *
     * @var boolean
     * @since 0.10.2
     */
    private $_uninitialized = true;

    /**
     * The number of method or function calls.
     *
     * @var integer
     */
    private $_calls = 0;

    /**
     * Number of fanouts.
     *
     * @var integer
     */
    private $_fanout = 0;

    /**
     * Temporary map that is used to hold the uuid combinations of dependee and
     * depender.
     *
     * @var array(string=>array)
     * @since 0.10.2
     */
    private $_temporaryCouplingMap = array();

    /**
     * This array holds a mapping between node identifiers and an array with
     * the node's metrics.
     *
     * @var array(string=>array)
     * @since 0.10.2
     */
    private $_nodeMetrics = array();

    /**
     * Provides the project summary as an <b>array</b>.
     *
     * <code>
     * array(
     *     'calls'   =>  23,
     *     'fanout'  =>  42
     * )
     * </code>
     *
     * @return array(string=>mixed)
     */
    public function getProjectMetrics()
    {
        return array(
            self::M_CALLS   =>  $this->_calls,
            self::M_FANOUT  =>  $this->_fanout
        );
    }

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the given node instance. If there are no metrics for the given node
     * this method will return an empty <b>array</b>.
     *
     * <code>
     * array(
     *     'loc'    =>  42,
     *     'ncloc'  =>  17,
     *     'cc'     =>  12
     * )
     * </code>
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return array(string=>mixed)
     */
    public function getNodeMetrics(PHP_Depend_Code_NodeI $node)
    {
        if (isset($this->_nodeMetrics[$node->getUUID()])) {
            return $this->_nodeMetrics[$node->getUUID()];
        }
        return array();
    }

    /**
     * Processes all {@link PHP_Depend_Code_Package} code nodes.
     *
     * @param PHP_Depend_Code_NodeIterator $packages All code packages.
     *
     * @return void
     */
    public function analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        if ($this->_uninitialized) {
            $this->_analyze($packages);
            $this->_uninitialized = false;
        }
    }

    /**
     * This method traverses all packages in the given iterator and calculates
     * the coupling metrics for them.
     *
     * @param PHP_Depend_Code_NodeIterator $packages All parsed code packages.
     *
     * @return void
     * @since 0.10.2
     */
    private function _analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        $this->fireStartAnalyzer();
        $this->_reset();

        foreach ($packages as $package) {
            $package->accept($this);
        }

        $this->_postProcessTemporaryCouplingMap();
        $this->fireEndAnalyzer();
    }

    /**
     * This method resets all internal state variables before the analyzer can
     * start the object tree traversal.
     *
     * @return void
     * @since 0.10.2
     */
    private function _reset()
    {
        $this->_calls                = 0;
        $this->_fanout               = 0;
        $this->_nodeMetrics          = array();
        $this->_temporaryCouplingMap = array();
    }

    /**
     * This method takes the temporary coupling map with node UUIDs and calculates
     * the concrete node metrics.
     *
     * @return void
     * @since 0.10.2
     */
    private function _postProcessTemporaryCouplingMap()
    {
        foreach ($this->_temporaryCouplingMap as $uuid => $metrics) {
            $afferentCoupling = count($metrics['ca']);
            $efferentCoupling = count($metrics['ce']);

            $this->_nodeMetrics[$uuid] = array(
                self::M_CA   =>  $afferentCoupling,
                self::M_CBO  =>  $efferentCoupling,
                self::M_CE   =>  $efferentCoupling
            );

            $this->_fanout += $efferentCoupling;
        }
        
        $this->_temporaryCouplingMap = array();
    }

    /**
     * Visits a function node.
     *
     * @param PHP_Depend_Code_Function $function The current function node.
     *
     * @return void
     */
    public function visitFunction(PHP_Depend_Code_Function $function)
    {
        $this->fireStartFunction($function);

        $fanouts = array();
        if (($type = $function->getReturnClass()) !== null) {
            $fanouts[] = $type;
            ++$this->_fanout;
        }
        foreach ($function->getExceptionClasses() as $type) {
            if (in_array($type, $fanouts, true) === false) {
                $fanouts[] = $type;
                ++$this->_fanout;
            }
        }
        foreach ($function->getDependencies() as $type) {
            if (in_array($type, $fanouts, true) === false) {
                $fanouts[] = $type;
                ++$this->_fanout;
            }
        }

        foreach ($fanouts as $fanout) {
            $this->_initClassOrInterfaceDependencyMap($fanout);

            $this->_temporaryCouplingMap[
                $fanout->getUUID()
            ]['ca'][
                $function->getUUID()
            ] = true;
        }

        $this->_countCalls($function);

        $this->fireEndFunction($function);
    }

    /**
     * Visit method for classes that will be called by PHP_Depend during the
     * analysis phase with the current context class.
     *
     * @param PHP_Depend_Code_Class $class The currently analyzed class.
     *
     * @return void
     * @since 0.10.2
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $this->_initClassOrInterfaceDependencyMap($class);
        return parent::visitClass($class);
    }

    /**
     * Visit method for interfaces that will be called by PHP_Depend during the
     * analysis phase with the current context interface.
     *
     * @param PHP_Depend_Code_Interface $interface The currently analyzed interface.
     *
     * @return void
     * @since 0.10.2
     */
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        $this->_initClassOrInterfaceDependencyMap($interface);
        return parent::visitInterface($interface);
    }

    /**
     * Visits a method node.
     *
     * @param PHP_Depend_Code_Class $method The method class node.
     *
     * @return void
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        $this->fireStartMethod($method);

        $declaringClass = $method->getParent();

        $this->_calculateClassOrInterfaceCoupling(
            $declaringClass,
            $method->getReturnClass()
        );

        foreach ($method->getExceptionClasses() as $type) {
            $this->_calculateClassOrInterfaceCoupling($declaringClass, $type);
        }
        foreach ($method->getDependencies() as $type) {
            $this->_calculateClassOrInterfaceCoupling($declaringClass, $type);
        }

        $this->_countCalls($method);

        $this->fireEndMethod($method);
    }

    /**
     * Visits a property node.
     *
     * @param PHP_Depend_Code_Property $property The property class node.
     *
     * @return void
     */
    public function visitProperty(PHP_Depend_Code_Property $property)
    {
        $this->fireStartProperty($property);

        $this->_calculateClassOrInterfaceCoupling(
            $property->getDeclaringClass(),
            $property->getClass()
        );

        $this->fireEndProperty($property);
    }

    /**
     * Calculates the coupling between the given types.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $declaringClass The declaring
     *        or context class.
     * @param PHP_Depend_Code_AbstractClassOrInterface $coupledClass   The class that
     *        is used by the declaring class or <b>null</b> when no class is defined.
     *
     * @return void
     * @since 0.10.2 
     */
    private function _calculateClassOrInterfaceCoupling(
        PHP_Depend_Code_AbstractClassOrInterface $declaringClass,
        PHP_Depend_Code_AbstractClassOrInterface $coupledClass = null
    ) {
        $this->_initClassOrInterfaceDependencyMap($declaringClass);

        if (null === $coupledClass) {
            return;
        }
        if ($coupledClass->isSubtypeOf($declaringClass)
            || $declaringClass->isSubtypeOf($coupledClass)
        ) {
            return;
        }

        $this->_initClassOrInterfaceDependencyMap($coupledClass);

        $this->_temporaryCouplingMap[
            $declaringClass->getUUID()
        ]['ce'][
            $coupledClass->getUUID()
        ] = true;

        $this->_temporaryCouplingMap[
            $coupledClass->getUUID()
        ]['ca'][
            $declaringClass->getUUID()
        ] = true;
    }

    /**
     * This method will initialize a temporary coupling container for the given
     * given class or interface instance.
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $classOrInterface The
     *        currently visited/traversed class or interface instance.
     *
     * @return void
     * @since 0.10.2
     */
    private function _initClassOrInterfaceDependencyMap(
        PHP_Depend_Code_AbstractClassOrInterface $classOrInterface
    ) {
        if (isset($this->_temporaryCouplingMap[$classOrInterface->getUUID()])) {
            return;
        }

        $this->_temporaryCouplingMap[$classOrInterface->getUUID()] = array(
            'ce' => array(),
            'ca' => array()
        );
    }

    /**
     * Counts all calls within the given <b>$callable</b>
     *
     * @param PHP_Depend_Code_AbstractCallable $callable Context callable.
     *
     * @return void
     */
    private function _countCalls(PHP_Depend_Code_AbstractCallable $callable)
    {
        $invocations = $callable->findChildrenOfType(
            PHP_Depend_Code_ASTInvocation::CLAZZ
        );

        $invoked = array();

        foreach ($invocations as $invocation) {
            $parents = $invocation->getParentsOfType(
                PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ
            );

            $image = '';
            foreach ($parents as $parent) {
                $child = $parent->getChild(0);
                if ($child !== $invocation) {
                    $image .= $child->getImage() . '.';
                }
            }
            $image .= $invocation->getImage() . '()';

            $invoked[$image] = $image;
        }

        $this->_calls += count($invoked);
    }
}
