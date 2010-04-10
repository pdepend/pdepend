<?php
/**
 * This file is part of PHP_Depend.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/NodeVisitor/AbstractVisitor.php';
require_once 'PHP/Depend/Log/LoggerI.php';

/**
 * Generates a chart with the aggregated metrics. 
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Log
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Depend_Log_Jdepend_Chart
       extends PHP_Depend_Code_NodeVisitor_AbstractVisitor 
    implements PHP_Depend_Log_LoggerI
{
    /**
     * The output file name.
     *
     * @type string
     * @var string $_fileName
     */
    private $_fileName = null;
    
    /**
     * The context source code.
     *
     * @type PHP_Depend_Code_NodeIterator
     * @var PHP_Depend_Code_NodeIterator $_code
     */
    private $_code = null;
    
    /**
     * The context analyzer instance.
     *
     * @type PHP_Depend_Metrics_Dependency_Analyzer
     * @var PHP_Depend_Metrics_Dependency_Analyzer $analyzer
     */
    private $_analyzer = null;
    
    /**
     * Constructs a new chart logger.
     *
     * @param string $fileName The output file name.
     */
    public function __construct($fileName)
    {
        $this->_fileName = $fileName;
    }
    
    /**
     * Sets the context code nodes.
     *
     * @param PHP_Depend_Code_NodeIterator $code The code nodes.
     * 
     * @return void
     */
    public function setCode(PHP_Depend_Code_NodeIterator $code)
    {
        $this->_code = $code;
    }
    
    /**
     * Adds an analyzer to log. If this logger accepts the given analyzer it
     * with return <b>true</b>, otherwise the return value is <b>false</b>.
     *
     * @param PHP_Depend_Metrics_AnalyzerI $analyzer The analyzer to log.
     * 
     * @return boolean
     */
    public function log(PHP_Depend_Metrics_AnalyzerI $analyzer)
    {
        if ($analyzer instanceof PHP_Depend_Metrics_Dependency_Analyzer) {
            $this->_analyzer = $analyzer;
            
            return true;
        }
        return false;
    }
    
    /**
     * Closes the logger process and writes the output file.
     *
     * @return void
     */
    public function close()
    {
        $size = 40;

        $im = imagecreatetruecolor(13 * $size, 13 * $size);

        $red    = imagecolorallocate($im, 67, 118, 16);
        $orange = imagecolorallocate($im, 252, 175, 62);
        $green  = imagecolorallocate($im, 139, 226, 52);
        $white  = imagecolorallocate($im, 255, 255, 255);
        $gray   = imagecolorallocate($im, 85, 87, 83);
        $dgray  = imagecolorallocate($im, 46, 52, 54);
        //$lgray  = imagecolorallocate($im, 186, 189, 182);

        $bias = 0.1;

        imagefill($im, 0, 0, $white);
        imagerectangle($im, $size, $size, 12 * $size, 12 * $size, $gray);

        for ($n = 0.0, $i = ($size + ($size / 2)); $i < (12 * $size); $n += 0.1, $i += $size) {
            
            imageline($im, $size, $i, ( 12 * $size ), $i, $gray);
            imageline($im, $i, $size, $i, ( 12 * $size ), $gray);

            imageline($im, ($size - 2), $i, ($size + 2), $i, $dgray);
            imageline($im, $i, ((12 * $size) - 2), $i, ((12 * $size) + 2), $dgray);

            $text = sprintf('%.1f', $n);

            imagestring($im, 1, floor($size / 2), (13 * $size) - $i - 4, $text, $dgray);
            imagestring($im, 1, $i - 4, ceil($size * 12.25), $text, $dgray);
        }

        $text = 'Abstraction';
        imagestring($im, 2, 6 * $size, floor(12.5 * $size), $text, $dgray);
        
        $text = 'Instability';
        imagestringup($im, 2, floor($size / 10), floor(7.5 * $size), $text, $dgray);

        imageline($im, $size, $size, (12 * $size), (12 * $size), $red);

        foreach ($this->_code as $package) {
            
            $metrics = $this->_analyzer->getStats($package);
            
            $s = $metrics['cc']; 
               + $metrics['ac'];
            $d = (sqrt($s) * $size) / sqrt($size);

            $A = $metrics['a'];
            $I = $metrics['i'];

            $x = $size + ceil($A * (10 * $size)) + ($size / 2);
            $y = $size + ceil(10.5 * $size) + ($I * (-10 * $size));

            if ($metrics['d'] < $bias) {
                $color = $green;
            } else {
                $color = $orange;
            }

            imagefilledarc($im, $x, $y, $d, $d, 0, 0, $color, IMG_ARC_PIE);
            imagearc($im, $x, $y, $d, $d, 0, 0, $dgray);
                
            $x += ceil($d / 2);
            $y -= $d;
                
            imagestring($im, 2, $x, $y, $package->getName(), $dgray);
        }

        imagepng($im, $this->_fileName);
        imagedestroy($im);
    }
    
}