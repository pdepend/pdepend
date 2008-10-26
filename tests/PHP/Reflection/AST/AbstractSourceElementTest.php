<?php
/**
 * This file is part of PHP_Reflection.
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
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Base test case for abstract item implementations.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage AST
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
abstract class PHP_Reflection_AST_AbstractSourceElementTest extends PHP_Reflection_AbstractTest
{
    
    /**
     * Tests that build interface updates the source file information for null
     * values.
     *
     * @return void
     */
    public function testSetSourceFileInformationForNullValue()
    {
        $item = $this->createItem();
        $file = new PHP_Reflection_AST_File(__FILE__);
        
        $this->assertNull($item->getSourceFile());
        $item->setSourceFile($file);
        $this->assertSame($file, $item->getSourceFile());
    }
    
    /**
     * Tests that the build interface method doesn't update an existing source
     * file info.
     *
     * @return void
     */
    public function testDoesntSetSourceFileInformationForNotNullValue()
    {
        $item = $this->createItem();
        $file = new PHP_Reflection_AST_File(__FILE__);
        
        $item->setSourceFile($file);
        $item->setSourceFile(new PHP_Reflection_AST_File('HelloWorld.php'));
        
        $this->assertSame($file, $item->getSourceFile());
    }
    
    /**
     * Tests that the start line number is set correct.
     *
     * @return void
     */
    public function testSetStartLineNumberForZeroValue()
    {
        $item = $this->createItem();
        
        $this->assertEquals(0, $item->getLine());
        $item->setLine(42);
        $this->assertEquals(42, $item->getLine());
    }
    
    /**
     * Tests that a previous set start line number is not replaced by a second
     * value.
     *
     * @return void
     */
    public function testDoesntSetStartLineNumberForNonZeroValue()
    {
        $item = $this->createItem();
        
        $item->setLine(23);
        $item->setLine(42);
        $this->assertEquals(23, $item->getLine());
    }
    
    /**
     * Tests that the end line number is set correct.
     *
     * @return void
     */
    public function testSetEndLineNumberForZeroValue()
    {
        $item = $this->createItem();
        
        $this->assertEquals(0, $item->getEndLine());
        $item->setEndLine(42);
        $this->assertEquals(42, $item->getEndLine());
    }
    
    /**
     * Tests that a previous set ebd line number is not replaced by a second
     * value.
     *
     * @return void
     */
    public function testDoesntSetEndLineNumberForNonZeroValue()
    {
        $item = $this->createItem();
        
        $item->setEndLine(23);
        $item->setEndLine(42);
        $this->assertEquals(23, $item->getEndLine());
    }
    
    /**
     * Creates an abstract item instance.
     *
     * @return PHP_Reflection_AST_AbstractSourceElement
     */
    protected abstract function createItem();
}