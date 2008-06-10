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
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeVisitor/AbstractDefaultVisitListener.php';
require_once 'PHP/Depend/Metrics/AnalyzeListenerI.php';

/**
 * 
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_TextUI_ResultPrinter
       extends PHP_Depend_Code_NodeVisitor_AbstractDefaultVisitListener
    implements PHP_Depend_Metrics_AnalyzeListenerI
{
    const STEP_SIZE = 30;
    
    private $_count = 0;
    
    public function startAnalyzer(PHP_Depend_Metrics_AnalyzerI $analyzer)
    {
        $this->_count  = 0;
        
        $name = substr(get_class($analyzer), 19, -9);
        echo "Executing {$name}-Analyzer:\n";
    }
    
    public function endAnalyzer(PHP_Depend_Metrics_AnalyzerI $analyzer)
    {
        $diff = ($this->_count % (self::STEP_SIZE * 60));
        if ($diff !== 0) {
            $indent = 65 - ceil($diff / self::STEP_SIZE);
            printf(".% {$indent}s\n", $this->_count);
        }
        echo "\n";
    }
    
    public function startVisitNode(PHP_Depend_Code_NodeI $node)
    {
        ++$this->_count;
        
        if ($this->_count % self::STEP_SIZE === 0) {
            echo '.';
        }
        if ($this->_count % (self::STEP_SIZE * 60) === 0) {
            printf("% 5s\n", $this->_count);
        }
    }
}