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
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Metrics/AbstractAnalyzer.php';
require_once 'PHP/Depend/Metrics/AggregateAnalyzerI.php';
require_once 'PHP/Depend/Metrics/FilterAwareI.php';
require_once 'PHP/Depend/Metrics/NodeAwareI.php';

/**
 * Generates some class level based metrics. This analyzer is based on the
 * metrics specified in the following document.
 *
 * http://www.aivosto.com/project/help/pm-oo-misc.html
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_ClassLevel_Analyzer
       extends PHP_Depend_Metrics_AbstractAnalyzer
    implements PHP_Depend_Metrics_AggregateAnalyzerI,
               PHP_Depend_Metrics_FilterAwareI,
               PHP_Depend_Metrics_NodeAwareI
{
    /**
     * Hash with all calculated node metrics.
     *
     * <code>
     * array(
     *     '0375e305-885a-4e91-8b5c-e25bda005438'  =>  array(
     *         'loc'    =>  42,
     *         'ncloc'  =>  17,
     *         'cc'     =>  12
     *     ),
     *     'e60c22f0-1a63-4c40-893e-ed3b35b84d0b'  =>  array(
     *         'loc'    =>  42,
     *         'ncloc'  =>  17,
     *         'cc'     =>  12
     *     )
     * )
     * </code>
     *
     * @var array(string=>array) $_nodeMetrics
     */
    private $_nodeMetrics = null;

    /**
     * The internal used cyclomatic complexity analyzer.
     *
     * @var PHP_Depend_Metrics_CyclomaticComplexity_Analyzer $_cyclomaticAnalyzer
     */
    private $_cyclomaticAnalyzer = null;

    /**
     * Processes all {@link PHP_Depend_Code_Package} code nodes.
     *
     * @param PHP_Depend_Code_NodeIterator $packages All code packages.
     *
     * @return void
     */
    public function analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        if ($this->_nodeMetrics === null) {
            // First check for the require cc analyzer
            if ($this->_cyclomaticAnalyzer === null) {
                throw new RuntimeException('Missing required CC analyzer.');
            }

            $this->fireStartAnalyzer();

            $this->_cyclomaticAnalyzer->analyze($packages);

            // Init node metrics
            $this->_nodeMetrics = array();

            // Visit all nodes
            foreach ($packages as $package) {
                $package->accept($this);
            }

            $this->fireEndAnalyzer();
        }
    }

    /**
     * This method must return an <b>array</b> of class names for required
     * analyzers.
     *
     * @return array(string)
     */
    public function getRequiredAnalyzers()
    {
        return array(
            'PHP_Depend_Metrics_CyclomaticComplexity_Analyzer'
        );
    }

    /**
     * Adds a required sub analyzer.
     *
     * @param PHP_Depend_Metrics_AnalyzerI $analyzer The sub analyzer instance.
     *
     * @return void
     */
    public function addAnalyzer(PHP_Depend_Metrics_AnalyzerI $analyzer)
    {
        if ($analyzer instanceof PHP_Depend_Metrics_CyclomaticComplexity_Analyzer) {
            $this->_cyclomaticAnalyzer = $analyzer;
        } else {
            throw new InvalidArgumentException('CC Analyzer required.');
        }
    }

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the given <b>$node</b>. If there are no metrics for the requested
     * node, this method will return an empty <b>array</b>.
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return array(string=>mixed)
     */
    public function getNodeMetrics(PHP_Depend_Code_NodeI $node)
    {
        $metrics = array();
        if (isset($this->_nodeMetrics[$node->getUUID()])) {
            $metrics = $this->_nodeMetrics[$node->getUUID()];
        }
        return $metrics;
    }

    /**
     * Visits a class node.
     *
     * @param PHP_Depend_Code_Class $class The current class node.
     *
     * @return void
     * @see PHP_Depend_Visitor_AbstractVisitor::visitClass()
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $this->fireStartClass($class);

        $this->_nodeMetrics[$class->getUUID()] = array(
            'dit'     =>  $this->_calculateDIT($class),
            'impl'    =>  $class->getImplementedInterfaces()->count(),
            'cis'     =>  0,
            'csz'     =>  0,
            'vars'    =>  0,
            'varsi'   =>  $this->_calculateVARSi($class),
            'varsnp'  =>  0,
            'wmc'     =>  0,
            'wmci'    =>  $this->_calculateWMCi($class),
            'wmcnp'   =>  0
        );

        foreach ($class->getProperties() as $property) {
            $property->accept($this);
        }
        foreach ($class->getMethods() as $method) {
            $method->accept($this);
        }

        $this->fireEndClass($class);
    }

    /**
     * Visits a code interface object.
     *
     * @param PHP_Depend_Code_Interface $interface The context code interface.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitInterface()
     */
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        // Empty visit method, we don't want interface metrics
    }

    /**
     * Visits a method node.
     *
     * @param PHP_Depend_Code_Class $method The method class node.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitMethod()
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        $this->fireStartMethod($method);

        // Get parent class uuid
        $uuid = $method->getParent()->getUUID();

        $ccn2 = $this->_cyclomaticAnalyzer->getCCN2($method);

        // Increment Weighted Methods Per Class(WMC) value
        $this->_nodeMetrics[$uuid]['wmc'] += $ccn2;
        // Increment Class Size(CSZ) value
        $this->_nodeMetrics[$uuid]['csz'] += $ccn2;

        // Increment Non Private values
        if ($method->isPublic()) {
            // Increment Non Private WMC value
            $this->_nodeMetrics[$uuid]['wmcnp'] += $ccn2;
            // Increment Class Interface Size(CIS) value
            $this->_nodeMetrics[$uuid]['cis'] += $ccn2;
        }

        $this->fireEndMethod($method);
    }

    /**
     * Visits a property node.
     *
     * @param PHP_Depend_Code_Property $property The property class node.
     *
     * @return void
     * @see PHP_Depend_VisitorI::visitProperty()
     */
    public function visitProperty(PHP_Depend_Code_Property $property)
    {
        $this->fireStartProperty($property);

        // Get parent class uuid
        $uuid = $property->getParent()->getUUID();

        // Increment VARS value
        ++$this->_nodeMetrics[$uuid]['vars'];
        // Increment Class Size(CSZ) value
        ++$this->_nodeMetrics[$uuid]['csz'];

        // Increment Non Private values
        if ($property->isPublic()) {
            // Increment Non Private VARS value
            ++$this->_nodeMetrics[$uuid]['varsnp'];
            // Increment Class Interface Size(CIS) value
            ++$this->_nodeMetrics[$uuid]['cis'];
        }

        $this->fireEndProperty($property);
    }

    /**
     * Returns the depth of inheritance tree value for the given class.
     *
     * @param PHP_Depend_Code_Class $class The context code class instance.
     *
     * @return integer
     */
    private function _calculateDIT(PHP_Depend_Code_Class $class)
    {
        $dit = 0;
        while (($class = $class->getParentClass()) !== null) {
            ++$dit;
        }
        return $dit;
    }

    /**
     * Calculates the Variables Inheritance of a class metric, this method only
     * counts protected and public properties of parent classes.
     *
     * @param PHP_Depend_Code_Class $class The context class instance.
     *
     * @return integer
     */
    private function _calculateVARSi(PHP_Depend_Code_Class $class)
    {
        // List of properties, this method only counts not overwritten properties
        $properties = array();
        // Collect all properties of the context class
        foreach ($class->getProperties() as $prop) {
            $properties[$prop->getName()] = true;
        }

        // Get parent class and collect all non private properties
        $parent = $class->getParentClass();

        while ($parent !== null) {
            // Get all parent properties
            foreach ($parent->getProperties() as $prop) {
                if (!$prop->isPrivate() && !isset($properties[$prop->getName()])) {
                    $properties[$prop->getName()] = true;
                }
            }
            // Get next parent
            $parent = $parent->getParentClass();
        }
        return count($properties);
    }

    /**
     * Calculates the Weight Method Per Class metric, this method only counts
     * protected and public methods of parent classes.
     *
     * @param PHP_Depend_Code_Class $class The context class instance.
     *
     * @return integer
     */
    private function _calculateWMCi(PHP_Depend_Code_Class $class)
    {
        // List of methods, this method only counts not overwritten methods.
        $ccn = array();

        // First collect all methods of the context class
        foreach ($class->getMethods() as $m) {
            $ccn[$m->getName()] = $this->_cyclomaticAnalyzer->getCCN2($m);
        }

        // Get parent class and collect all non private methods.
        $parent = $class->getParentClass();

        while ($parent !== null) {
            // Count all methods
            foreach ($parent->getMethods() as $m) {
                if (!$m->isPrivate() && !isset($methods[$m->getName()])) {
                    $ccn[$m->getName()] = $this->_cyclomaticAnalyzer->getCCN2($m);
                }
            }
            // Fetch parent class
            $parent = $parent->getParentClass();
        }
        return array_sum($ccn);
    }
}