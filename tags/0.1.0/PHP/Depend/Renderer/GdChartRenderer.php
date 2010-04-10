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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Renderer.php';
require_once 'PHP/Depend/Metrics/PackageMetricsVisitor.php';

/**
 * Proof-of-Concept chart renderer that visualizes package metrics.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Renderer_GdChartRenderer implements PHP_Depend_Renderer
{
    /**
     * The output file.
     *
     * @type string
     * @var string $fileName
     */
    protected $fileName = null;
    
    /**
     * Constructs a new gd based chart renderer. 
     * 
     * The <b>$fileName</b> parameter points to the chart out file.
     *
     * @param string $fileName The output file.
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Generates the package metrics chart.
     *
     * @param Iterator $metrics The aggregated metrics.
     * 
     * @return void
     */
    public function render(Iterator $metrics)
    {
        $size = 40;

        $im = imagecreatetruecolor(13 * $size, 13 * $size);

        $red    = imagecolorallocate($im, 67, 118, 16);
        $orange = imagecolorallocate($im, 252, 175, 62);
        $green  = imagecolorallocate($im, 139, 226, 52);
        $white  = imagecolorallocate($im, 255, 255, 255);
        $lgray  = imagecolorallocate($im, 186, 189, 182);
        $gray   = imagecolorallocate($im, 85, 87, 83);
        $dgray  = imagecolorallocate($im, 46, 52, 54);

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

        imagestring($im, 2, 6 * $size, floor(12.5 * $size), "Abstraction", $dgray);
        imagestringup($im, 2, floor($size / 10), floor(7.5 * $size), "Instability", $dgray);

        imageline($im, $size, $size, (12 * $size), (12 * $size), $red);

        foreach ($metrics as $metric) {
            if ($metric->getName() !== PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE) {
            
                $sum      = ($metric->getConcreteClassCount() + $metric->getAbstractClassCount());
                $diameter = (sqrt($sum) * $size) / sqrt($size);

                $A = $metric->abstractness();
                $I = $metric->instability();

                $offsetX = $size + ceil($A * (10 * $size)) + ($size / 2);
                $offsetY = $size + ceil(10.5 * $size) + ($I * (-10 * $size));

                if ($metric->distance() < $bias) {
                    $color = $green;
                } else {
                    $color = $orange;
                }

                imagefilledarc($im, $offsetX, $offsetY, $diameter, $diameter, 0, 0, $color, IMG_ARC_PIE);
                imagearc($im, $offsetX, $offsetY, $diameter, $diameter, 0, 0, $dgray);

                imagestring($im, 2, $offsetX + ceil($diameter / 2), $offsetY - $diameter, $metric->getName(), $dgray);
            }
        }

        imagepng($im, $this->fileName);
        imagedestroy($im);
    }
}