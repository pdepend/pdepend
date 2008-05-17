<?php
/**
 * This file is part of PHP_Depend.
 * 
 * PHP Version 5
 *
 * Copyright (c) 2008, Manuel Pichler <mapi@pmanuel-pichler.de>.
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
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeVisitor.php';
require_once 'PHP/Depend/Metrics/AnalyzerI.php';
require_once 'PHP/Depend/Metrics/ResultSetI.php';
require_once 'PHP/Depend/Metrics/ResultSet/NodeAwareI.php';

/**
 * Generates some class level based metrics. This analyzer is based on the 
 * metrics specified in the following document.
 * 
 * http://www.aivosto.com/project/help/pm-oo-misc.html
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_ClassLevel_Analyzer
    implements PHP_Depend_Code_NodeVisitor,
               PHP_Depend_Metrics_AnalyzerI,
               PHP_Depend_Metrics_ResultSetI,
               PHP_Depend_Metrics_ResultSet_NodeAwareI
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
     * @type array<array>
     * @var array(string=>array) $nodeMetrics
     */
    protected $nodeMetrics = array();
    
    /**
     *
     * @param PHP_Depend_Code_NodeIterator $packages
     * 
     * @return PHP_Depend_Metrics_ResultSetI
     * @see PHP_Depend_Metrics_AnalyzerI::analyze()
     */
    public function analyze(PHP_Depend_Code_NodeIterator $packages)
    {
        foreach ($packages as $package) {
            $package->accept($this);
        }
        
        return $this;
    }
    
    /**
     * This method returns an <b>array</b> with all aggregated metrics.
     * 
     * @return array(string=>array)
     * @see PHP_Depend_Metrics_ResultSet_NodeAwareI::getAllNodeMetrics()
     */
    public function getAllNodeMetrics()
    {
        
    }
    
    /**
     * This method will return an <b>array</b> with all generated metric values 
     * for the node with the given <b>$uuid</b> identifier. If there are no
     * metrics for the requested node, this method will return an empty <b>array</b>.
     *
     * @param string $uuid The unique node identifier.
     * 
     * @return array(string=>mixed)
     */
    public function getNodeMetrics($uuid)
    {
        
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitClass()
     *
     * @param PHP_Depend_Code_Class $class
     */
    public function visitClass(PHP_Depend_Code_Class $class)
    {
        $this->getClassDIT($class);
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitFunction()
     *
     * @param PHP_Depend_Code_Function $function
     */
    public function visitFunction(PHP_Depend_Code_Function $function)
    {

    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitInterface()
     *
     * @param PHP_Depend_Code_Interface $interface
     */
    public function visitInterface(PHP_Depend_Code_Interface $interface)
    {
        foreach ($interface->getMethods() as $method) {
            $method->accept($this);
        }
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitMethod()
     *
     * @param PHP_Depend_Code_Method $method
     */
    public function visitMethod(PHP_Depend_Code_Method $method)
    {
        
    }
    
    /**
     * @see PHP_Depend_Code_NodeVisitor::visitPackage()
     *
     * @param PHP_Depend_Code_Package $package
     */
    public function visitPackage(PHP_Depend_Code_Package $package)
    {
        foreach ($package->getTypes() as $type) {
            $type->accept($this);
        }
        
        foreach ($package->getFunctions() as $function) {
            $function->accept($this);
        }
    }
    
    /**
     * Returns the depth of inheritance tree value for the given class.
     *
     * @param PHP_Depend_Code_Class $class The context code class instance.
     */
    protected function getClassDIT(PHP_Depend_Code_Class $class)
    {
        $dit = 0;
        while (($class = $class->getParentClass()) !== null) {
            ++$dit;
        }
        return $dit;
    }
}