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
require_once 'PHP/Depend/Metrics/FilterAwareI.php';
require_once 'PHP/Depend/Metrics/ProjectAwareI.php';

/**
 * This analyzer provides two project related inheritance metrics.
 *
 * <b>ANDC - Average Number of Derived Classes</b>: The average number of direct
 * subclasses of a class. This metric only covers classes in the analyzed system,
 * no library or environment classes are covered.
 *
 * <b>AHH - Average Hierarchy Height</b>: The computed average of all inheritance
 * trees within the analyzed system, external classes or interfaces are ignored.
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
class PHP_Depend_Metrics_Inheritance_Analyzer
       extends PHP_Depend_Metrics_AbstractAnalyzer
    implements PHP_Depend_Metrics_FilterAwareI,
               PHP_Depend_Metrics_ProjectAwareI
{
    /**
     * Contains the number of derived classes for each processed class. The array
     * size is equal to the number of analyzed classes.
     *
     * @var array(integer) $_derivedClasses
     */
    private $_derivedClasses = null;

    /**
     * Contains the max inheritance depth for all root classes within the
     * analyzed system. The array size is equal to the number of analyzed root
     * classes.
     *
     * @var array(integer) $_rootClasses
     */
    private $_rootClasses = null;

    /**
     * The average number of derived classes.
     *
     * @var float $_andc
     */
    private $_andc = 0;

    /**
     * The average hierarchy height.
     *
     * @var float $_ahh
     */
    private $_ahh = 0;

    /**
     * Provides the project summary as an <b>array</b>.
     *
     * <code>
     * array(
     *     'andc'  =>  0.73,
     *     'ahh'   =>  0.56
     * )
     * </code>
     *
     * @return array(string=>mixed)
     */
    public function getProjectMetrics()
    {
        return array(
            'andc'  =>  $this->_andc,
            'ahh'   =>  $this->_ahh
        );
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
        if ($this->_derivedClasses === null) {

            $this->fireStartAnalyzer();

            // Init runtime collections
            $this->_derivedClasses = array();

            // Process all packages
            foreach ($packages as $package) {
                $package->accept($this);
            }

            if (($count = count($this->_derivedClasses)) > 0) {
                $this->_andc = (array_sum($this->_derivedClasses) / $count);
            }
            if (($count = count($this->_rootClasses)) > 0) {
                $this->_ahh = (array_sum($this->_rootClasses) / $count);
            }

            $this->fireEndAnalyzer();
        }
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

        // Count all derived classes
        $this->_derivedClasses[] = $class->getChildTypes()->count();

        // Is this a root class?
        if ($class->getParentClass() === null) {
            $this->_rootClasses[] = $this->_calculateHIT($class);
        }

        $this->fireEndClass($class);
    }

    /**
     * Calculates the maximum HIT for the given class.
     *
     * @param PHP_Depend_Code_Class $class The context class instance.
     *
     * @return integer
     */
    private function _calculateHIT(PHP_Depend_Code_Class $class)
    {
        $childTypes = $class->getChildTypes();
        if ($childTypes->count() === 0) {
            return 0;
        }

        $depth = 0;
        foreach ($childTypes as $childType) {
            if (($childDepth = $this->_calculateHIT($childType)) > $depth) {
                $depth = $childDepth;
            }
        }
        return $depth + 1;
    }
}