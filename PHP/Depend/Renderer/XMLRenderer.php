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

class PHP_Depend_Renderer_XMLRenderer implements PHP_Depend_Renderer
{
    public function render(Iterator $metrics)
    {
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        
        $root = $dom->appendChild($dom->createElement('PHPDepend'));
        $pkgs = $root->appendChild($dom->createElement('Packages'));
        
        foreach ($metrics as $metric) {
            if ($metric->getName() === PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE) {
                continue;
            }
            
            $pkg = $pkgs->appendChild($dom->createElement('Package'));
            $pkg->setAttribute('name', $metric->getName());
            
            $stats = $pkg->appendChild($dom->createElement('Stats'));
            
            $stats->appendChild($dom->createElement('TotalClasses'))
                  ->appendChild($dom->createTextNode($metric->getTC()));
            $stats->appendChild($dom->createElement('ConcreteClasses'))
                  ->appendChild($dom->createTextNode($metric->getCC()));
            $stats->appendChild($dom->createElement('AbstractClasses'))
                  ->appendChild($dom->createTextNode($metric->getAC()));
            $stats->appendChild($dom->createElement('Ca'))
                  ->appendChild($dom->createTextNode($metric->getCA()));
            $stats->appendChild($dom->createElement('Ce'))
                  ->appendChild($dom->createTextNode($metric->getCE()));
            $stats->appendChild($dom->createElement('A'))
                  ->appendChild($dom->createTextNode($metric->getA()));
            $stats->appendChild($dom->createElement('I'))
                  ->appendChild($dom->createTextNode($metric->getI()));
            $stats->appendChild($dom->createElement('D'))
                  ->appendChild($dom->createTextNode($metric->getD()));
            // TODO: V
            
            $cc = $pkg->appendChild($dom->createElement('ConcreteClasses'));
            foreach ($metric->getConcreteClasses() as $class) {
                $c = $cc->appendChild($dom->createElement('Class'));
                // TODO: Source file
                $c->appendChild($dom->createTextNode($class->getName()));
            }
            
            $ac = $pkg->appendChild($dom->createElement('AbstractClasses'));
            foreach ($metric->getAbstractClasses() as $class) {
                $c = $cc->appendChild($dom->createElement('Class'));
                // TODO: Source file
                $c->appendChild($dom->createTextNode($class->getName()));
            }
            
            $ce = $pkg->appendChild($dom->createElement('DependsUpon'));
            foreach ($metric->getEfferentCouplings() as $dep) {
                $p = $ce->appendChild($dom->createElement('Package'));
                $p->appendChild($dom->createTextNode($dep->getName()));
            }
            
            $ce = $pkg->appendChild($dom->createElement('UserBy'));
            foreach ($metric->getAfferentCouplings() as $dep) {
                $p = $ce->appendChild($dom->createElement('Package'));
                $p->appendChild($dom->createTextNode($dep->getName()));
            }
        }
        
        echo $dom->saveXML();
    }
}