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

require_once dirname(__FILE__) . '/AbstractRendererTest.php';

require_once 'PHP/Depend/Renderer/XMLRenderer.php';

/**
 * Tests the xml output render.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Renderer_XMLRendererTest extends PHP_Depend_Renderer_AbstractRendererTest
{
    /**
     * Tests the xml renderer output against a reference xml document. 
     *
     * @return void
     */
    public function testRenderXMLFile()
    {
        $output   = tempnam(sys_get_temp_dir(), 'php_depend');
        $renderer = new PHP_Depend_Renderer_XMLRenderer($output);
        $renderer->render($this->metrics);
        
        $expected = $this->loadReferenceXML();
        $result   = file_get_contents($output);
        
        $this->assertXmlStringEqualsXmlString($expected, $result);
        
        // Unlink temp file
        @unlink($output);
    }
    /**
     * Tests the xml renderer output against a reference xml document. 
     *
     * @return void
     */
    public function testRenderXMLString()
    {
        $renderer = new PHP_Depend_Renderer_XMLRenderer();
        
        ob_start();
        $renderer->render($this->metrics);
        $result = ob_get_contents();
        ob_end_clean();
        
        $expected = $this->loadReferenceXML();
        
        $this->assertXmlStringEqualsXmlString($expected, $result);
    }
    
    /**
     * Loads a prepared reference xml document.
     *
     * @return string
     */
    protected function loadReferenceXML()
    {
        $replace = '/home/manu/Projects/workspace.xplib.de/PHP_Depend/trunk/tests/PHP/Depend';
        $current = realpath(dirname(__FILE__) . '/..');
        
        $file = dirname(__FILE__) . '/ref.xml';
        $xml  = file_get_contents($file);
        
        return str_replace($replace, $current, $xml);        
    }
}