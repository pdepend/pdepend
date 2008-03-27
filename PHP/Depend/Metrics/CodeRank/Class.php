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

require_once 'PHP/Depend/Metrics/Class.php';

/**
 * Special metrics class implementation for the code rank metric.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Metrics_CodeRank_Class extends PHP_Depend_Metric_Class
{
    /**
     * The forward code rank value for this class in the range of [0-1]. 
     *
     * @type float
     * @var float $codeRank
     */
    protected $codeRank = 0.0;
    
    /**
     * The reverse code rank value for this class in the range of [0-1]
     *
     * @type float
     * @var float $reverseCodeRank
     */
    protected $reverseCodeRank = 0.0;
    
    /**
     * Returns the forward code rank value for this class.
     *
     * @return float
     */
    public function getCodeRank()
    {
        return $this->codeRank;
    }
    
    /**
     * Sets the forward code rank value for this class.
     *
     * @param float $codeRank The rank value in the range of [0-1].
     * 
     * @return void
     * @throws InvalidArgumentException If the given value is not of type float
     *                                  or in the range of [0-1].
     */
    public function setCodeRank($codeRank)
    {
        $this->codeRank = $this->checkCodeRankValue($codeRank);
    }
    
    /**
     * Returns the reverse code rank value for this class.
     *
     * @return float
     */
    public function getReverseCodeRank()
    {
        return $this->reverseCodeRank;
    }
    
    /**
     * Sets the reverse code rank value for this class.
     *
     * @param float $reverseCodeRank The rank value in the range of [0-1].
     * 
     * @return void
     * @throws InvalidArgumentException If the given value is not of type float
     *                                  or in the range of [0-1].
     */
    public function setReverseCodeRank($reverseCodeRank)
    {
        $this->reverseCodeRank = $this->checkCodeRankValue($reverseCodeRank);
    }
    
    /**
     * Checks a code rank value for type <b>float</b> and a range between [0-1].
     *
     * @param float $codeRank The code rank value.
     * 
     * @return float
     * @throws InvalidArgumentException If the given value is not of type float
     *                                  or in the range of [0-1].
     */
    protected function checkCodeRankValue($codeRank)
    {
        if (!is_float($codeRank)) {
            throw new InvalidArgumentException('Type float expected for code rank.');
        }
        if ($codeRank < 0.0 || $codeRank > 1.0) {
            throw new InvalidArgumentException('Code rank must be in the range 0-1');
        }
        return $codeRank;
    }
}