<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2013, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
  */

namespace PDepend\Source\AST;

use PDepend\AbstractTest;

/**
 * Test case for the {@link \PDepend\Source\AST\AbstractASTArtifact} class.
 *
 * @copyright 2008-2013 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\AST\AbstractASTArtifact
 * @group unittest
 */
class ASTArtifactTest extends AbstractTest
{
    /**
     * testGetNameReturnsValueOfFirstConstructorArgument
     *
     * @return void
     */
    public function testGetNameReturnsValueOfFirstConstructorArgument()
    {
        $item = $this->getItemMock();
        $this->assertEquals(__CLASS__, $item->getName());
    }
    
    /**
     * testSetNameOverridesPreviousItemName
     * 
     * @since 1.0.0
     */
    public function testSetNameOverridesPreviousItemName()
    {
        $item = $this->getItemMock();
        $item->setName(__FUNCTION__);
        
        $this->assertEquals(__FUNCTION__, $item->getName());        
    }

    /**
     * testGetIdReturnsMd5HashByDefault
     *
     * @return void
     */
    public function testGetIdReturnsMd5HashByDefault()
    {
        $item = $this->getItemMock();
        $this->assertRegExp('(^[a-f0-9]{32}$)', $item->getId());
    }

    /**
     * testGetIdReturnsInjectedIdValue
     *
     * @return void
     */
    public function testGetIdReturnsInjectedIdValue()
    {
        $item = $this->getItemMock();
        $item->setId(__METHOD__);

        $this->assertEquals(__METHOD__, $item->getId());
    }

    /**
     * testGetSourceFileReturnsNullByDefault
     *
     * @return void
     */
    public function testGetSourceFileReturnsNullByDefault()
    {
        $item = $this->getItemMock();
        $this->assertNull($item->getCompilationUnit());
    }

    /**
     * testGetSourceFileReturnsInjectedFileInstance
     *
     * @return void
     */
    public function testGetSourceFileReturnsInjectedFileInstance()
    {
        $file = new ASTCompilationUnit(__FILE__);

        $item = $this->getItemMock();
        $item->setCompilationUnit($file);

        $this->assertSame($file, $item->getCompilationUnit());
    }

    /**
     * testGetDocCommentReturnsNullByDefault
     *
     * @return void
     */
    public function testGetDocCommentReturnsNullByDefault()
    {
        $item = $this->getItemMock();
        $this->assertNull($item->getDocComment());
    }

    /**
     * testGetDocCommentReturnsInjectedDocCommentValue
     *
     * @return void
     */
    public function testGetDocCommentReturnsInjectedDocCommentValue()
    {
        $item = $this->getItemMock();
        $item->setDocComment('/** Manuel */');

        $this->assertSame('/** Manuel */', $item->getDocComment());
    }

    /**
     * Returns a mocked item instance.
     *
     * @return \PDepend\Source\AST\AbstractASTArtifact
     */
    protected function getItemMock()
    {
        return $this->getMockForAbstractClass(
            'PDepend\\Source\\AST\\AbstractASTArtifact',
            array(__CLASS__)
        );
    }
}
