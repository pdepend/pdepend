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
 * @version    SVN: $Id $
 * @link       http://pdepend.org/
 */

require_once 'PHP/Depend/Metrics/NodeCount/Analyzer.php';
require_once 'PHP/Depend/Metrics/ClassLevel/Analyzer.php';

require_once 'PHP/Depend/Metrics/AbstractAnalyzer.php';
require_once 'PHP/Depend/Metrics/AggregateAnalyzerI.php';

require_once 'PHP/Depend/Code/ASTThisVariable.php';

/**
 * This analyzer collects information used to calculate cohesion metrics
 *
 * <ul>
 *   <li>LCOM</li>
 *   <li>LCOM_HS</li>
 * </ul>
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Jan Schumann <js@schumann-it.com>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_Depend_Metrics_Cohesion_Analyzer
       extends PHP_Depend_Metrics_AbstractAnalyzer
    implements PHP_Depend_Metrics_AggregateAnalyzerI,
               PHP_Depend_Metrics_FilterAwareI,
               PHP_Depend_Metrics_NodeAwareI
{
    /**
     * Type of this analyzer class.
     */
    const CLAZZ = __CLASS__;

    /**
     * Metrics provided by the analyzer implementation.
     */
    const M_NUMBER_OF_METHOD_ACCESS  = 'nma',
          M_LCOM_CK                  = 'lcomCK',
          M_LCOM_HS                  = 'lcomHS',
          M_LCOM_CG                  = 'lcomCG',
          M_TCC                      = 'tcc';

    /**
     * Hash with all calculated node metrics.
     *
     * <code>
     * array(
     *     '0375e305-885a-4e91-8b5c-e25bda005438'  =>  array(
     *         'mf'    =>  13,
     *     )
     * )
     * </code>
     *
     * @var array(string=>array) $_nodeMetrics
     */
    private $_nodeMetrics = null;

    /**
     * Hash with the sum of the number of called properties for all classes
     *
     * @var array(UUID=>int) $_sumMA
     */
    private $_sumMA = array();

    /**
     * Hash with sets of connected properties per method for all classes
     *
     * @var array $_propertyRelations
     */
    private $_propertyRelations = array();

    /**
     * Hash with number of conjunct method-pairs per class
     *
     * @var array(UUID=>int) $_conjunctMethodPairs
     */
    private $_conjunctMethodPairs = array();

    /**
     * Hash with number of disjunct method-pairs per class
     *
     * @var array(UUID=>int) $_disjunctMethodPairs
     */
    private $_disjunctMethodPairs = array();

    /**
     * The internal used ClassLevel analyzer.
     *
     * @var PHP_Depend_Metrics_ClassLevel_Analyzer $_classLevelAnalyzer
     */
    private $_classLevelAnalyzer = null;

    /**
     * The internal used NodeCount analyzer.
     *
     * @var PHP_Depend_Metrics_NodeCount_Analyzer $_nodeCountAnalyzer
     */
    private $_nodeCountAnalyzer = null;

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
            // First check for the requires analyzers
            if ($this->_classLevelAnalyzer === null) {
                throw new RuntimeException('Missing required class level analyzer.');
            }
            if ($this->_nodeCountAnalyzer === null) {
                throw new RuntimeException('Missing required node count analyzer.');
            }

            $this->fireStartAnalyzer();

            // run required analyzers
            $this->_classLevelAnalyzer->analyze($packages);
            $this->_nodeCountAnalyzer->analyze($packages);

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
            PHP_Depend_Metrics_ClassLevel_Analyzer::CLAZZ,
            PHP_Depend_Metrics_NodeCount_Analyzer::CLAZZ
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
        if ($analyzer instanceof PHP_Depend_Metrics_ClassLevel_Analyzer) {
            $this->_classLevelAnalyzer = $analyzer;
        } elseif ($analyzer instanceof PHP_Depend_Metrics_NodeCount_Analyzer) {
            $this->_nodeCountAnalyzer = $analyzer;
        } else {
            throw new InvalidArgumentException('ClassLevel and NodeCount Analyzers required.');
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
     * This method will return TCC node metric for the given node.
     *
     * @param PHP_Depend_Code_NodeI $node The context node instance.
     *
     * @return array(string=>mixed)
     */
    public function getTCC(PHP_Depend_Code_NodeI $node)
    {
        $metrics = $this->getNodeMetrics($node);
        if (isset($metrics[self::M_TCC])) {
            return $metrics[self::M_TCC];
        }
        return 0;
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

        $classUUID = $class->getUUID();

        // init node metrics for this class
        if(!isset($this->_nodeMetrics[$classUUID])) {
            $this->_nodeMetrics[$classUUID] = array();
        }

        // init property relations
        if(!isset($this->_propertyRelations[$classUUID])) {
            $this->_propertyRelations[$classUUID] = array();
        }

        if(!isset($this->_sumMA[$classUUID])) {
          $this->_sumMA[$classUUID] = 0;
        }

        // visit properties
        foreach ($class->getProperties() as $property) {
            $property->accept($this);
        }

        // store the metrics
        $this->_nodeMetrics[$classUUID] = array(
            self::M_LCOM_CK => $this->_calculateLcomCK($class),
            self::M_LCOM_HS => $this->_calculateLcomHS($class),
            self::M_LCOM_CG => $this->_calculateLcomCG($class),
            self::M_TCC     => $this->_calculateTcc($class)
        );

        $this->fireEndClass($class);
    }

    /**
     * Calculate TCC (Tight class cohesion) defined by
     * Bieman, James M. & Kang, Byung-Kyoo: Cohesion and reuse in an object-oriented system. Proceedings of the 1995 Symposium on Software.
     * Pages: 259 - 262. ISSN:0163-5948. ACM Press New York, NY, USA.
     * http://doi.acm.org/10.1145/211782.211856 The original definition of TCC and LCC.
     *
     * Range 0..1
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $class
     * @return int
     */
    private function _calculateTcc(PHP_Depend_Code_AbstractClassOrInterface $class)
    {
        // esure to have conjunct and disjunct methods calculated
        $this->_calculateNumberOfConjunctAndDisjunctMethodPairs($class);

        // retrieve needed values
        $methodCount = $this->_nodeCountAnalyzer->getMethodCount($class);

        // tcc compares method-pairs, thus it is only valid for classes having at least two methods
        if(2 < $methodCount) {
            return $this->_conjunctMethodPairs[$class->getUUID()] / (($methodCount * ($methodCount - 1)) / 2);
        }

        // default return
        return 0;
    }

    /**
     * Calculate LCOM (Lack of Cohesion in Methods) definded by Henderson-Sellers
     * Henderson-Sellers, B, L, Constantine and I, Graham
     * 'Coupling and Cohesion (Towards a Valid Metrics Suite for Object-Oriented Analysis and Design)',
     * Object-Oriented Systems, 3(3), pp143-158, 1996.
     *
     * Range 0..2
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $class
     * @return int
     */
    private function _calculateLcomCG(PHP_Depend_Code_AbstractClassOrInterface $class)
    {
        // retrieve needed values
        $propertyCount = $this->_classLevelAnalyzer->getPropertyCount($class);
        $methodCount = $this->_nodeCountAnalyzer->getMethodCount($class);

        // this lcom is only valid if the class has at least two methods and at lears one property
        if(1 < $methodCount && 0 < $propertyCount) {
            return ($methodCount - ($this->_sumMA[$class->getUUID()]) / $propertyCount) / ($methodCount - 1);
        }

        // default return
        return 0;
    }

    /**
     * Calculate LCOM (Lack of Cohesion in Methods) definded by Constantine and Graham
     * Henderson-Sellers, B, L, Constantine and I, Graham
     * 'Coupling and Cohesion (Towards a Valid Metrics Suite for Object-Oriented Analysis and Design)',
     * Object-Oriented Systems, 3(3), pp143-158, 1996.
     *
     * Range 0..1
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $class
     * @return int
     */
    private function _calculateLcomHS(PHP_Depend_Code_AbstractClassOrInterface $class)
    {
        // retrieve needed values
        $propertyCount = $this->_classLevelAnalyzer->getPropertyCount($class);
        $methodCount = $this->_nodeCountAnalyzer->getMethodCount($class);

        // this lcom is only valid if the class has at least one method and at least one property
        if(0 < $propertyCount * $methodCount) {
            return 1 - ($this->_sumMA[$class->getUUID()] / ($propertyCount * $methodCount));
        }

        // default return
        return 0;
    }

    /**
     * Calculate LCOM (Lack of Cohesion in Methods) definded by
     * Shyam R. Chidamber, Chris F. Kemerer.
     * A Metrics suite for Object Oriented design. M.I.T. Sloan School of Management E53-315. 1993.
     * http://uweb.txstate.edu/~mg43/CS5391/Papers/Metrics/OOMetrics.pdf
     *
     * Range 0..inf
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $class
     * @return int
     */
    private function _calculateLcomCK(PHP_Depend_Code_AbstractClassOrInterface $class)
    {
        // esure to have conjunct and disjunct methods calculated
        $this->_calculateNumberOfConjunctAndDisjunctMethodPairs($class);

        $uuid = $class->getUUID();

        if($this->_disjunctMethodPairs[$uuid] > $this->_conjunctMethodPairs[$uuid]) {
            return $this->_disjunctMethodPairs[$uuid] - $this->_conjunctMethodPairs[$uuid];
        }

        // default return
        return 0;
    }

    /**
     * Calculate the number of conneted and disconnected methods in the given class
     *
     * @param PHP_Depend_Code_AbstractClassOrInterface $class
     * @return void
     */
    private function _calculateNumberOfConjunctAndDisjunctMethodPairs(PHP_Depend_Code_AbstractClassOrInterface $class)
    {
        $uuid = $class->getUUID();

        // check if already calculated
        if(!isset($this->_conjunctMethodPairs[$uuid]) || !isset($this->_disjunctMethodPairs[$uuid])) {
            // init values
            $this->_conjunctMethodPairs[$uuid] = 0;
            $this->_disjunctMethodPairs[$uuid] = 0;

            // retrieve relations and ensure having a non-assoc array
            $relations = array_values($this->_propertyRelations[$uuid]);

            // go through all method pairs
            for($i=0; $i < count($relations); ++$i) {
                for($j=$i+1; $j < count($relations); ++$j) {
                    if(array_intersect($relations[$i], $relations[$j])) {
                        // these two methods have at least one property in common
                        ++$this->_conjunctMethodPairs[$uuid];
                    }
                    else {
                        // these two methods have no property in common
                        ++$this->_disjunctMethodPairs[$uuid];
                    }
                }
            }
        }
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

      // init nodeMetrics for this property
      $this->_nodeMetrics[$property->getUUID()] = array(
          self::M_NUMBER_OF_METHOD_ACCESS => 0
      );

      $declaringClass = $property->getDeclaringClass();

      // search for methods accessing this property
      foreach($declaringClass->getMethods() as $method) {
          // init property relations for this method
          if(!isset($this->_propertyRelations[$declaringClass->getUUID()][$method->getUUID()])) {
              $this->_propertyRelations[$declaringClass->getUUID()][$method->getUUID()] = array();
          }

          if($this->_isPropertyAccessedByMethod($property, $method)) {
              ++$this->_nodeMetrics[$property->getUUID()][self::M_NUMBER_OF_METHOD_ACCESS];
              $this->_propertyRelations[$declaringClass->getUUID()][$method->getUUID()][] = $property->getUUID();
          };
      }

      // store ma value by class
      $this->_sumMA[$declaringClass->getUUID()] += $this->_nodeMetrics[$property->getUUID()][self::M_NUMBER_OF_METHOD_ACCESS];

      $this->fireEndProperty($property);
    }

    /**
     * Check if the given method accesses the given property
     *
     * @param PHP_Depend_Code_Property $property A property class node.
     * @param PHP_Depend_Code_Method   $method   A method class node.
     * @return boolean
     */
    private function _isPropertyAccessedByMethod(PHP_Depend_Code_Property $property, PHP_Depend_Code_Method $method)
    {
        foreach ($method->findChildrenOfType(PHP_Depend_Code_ASTSelfReference::CLAZZ) as $reference) {
            // the reference must have a parent. Otherwise it is a stand-alone variable. This should only happen as "return $this;"
            if($reference->getParent()) {
                foreach($reference->getParent()->findChildrenOfType(PHP_Depend_Code_ASTPropertyPostfix::CLAZZ) as $directAccessor) {
                    if($property->getName() === ($property->isStatic() ? '' : '$') . $directAccessor->getImage()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}