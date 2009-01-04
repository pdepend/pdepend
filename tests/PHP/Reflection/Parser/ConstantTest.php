<?php
/**
 * This file is part of PHP_Reflection.
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
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.manuel-pichler.de/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test cases related to constant parsing.
 *
 * @category   PHP
 * @package    PHP_Reflection
 * @subpackage Parser
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2009 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.manuel-pichler.de/
 */
class PHP_Reflection_Parser_ConstantTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests that the parser handles a comma separated list of constants correct.
     *
     * @return void
     */
    public function testParserHandlesCommaSeparatedConstants()
    {
        $packages = self::parseSource("/parser/constants/comma_separated_list.php");
        self::assertEquals(1, $packages->count());
        
        $package = $packages->current();
        self::assertEquals(1, $package->getClasses()->count());
        
        $class = $package->getClasses()->current();
        self::assertEquals(4, $class->getConstants()->count());
        
        $expected = array(
            'C_FOO'    => array('value' => 0,    'comment' => '/** Test comment C_FOO */'),
            'C_BAR'    => array('value' => null, 'comment' => '/** Test comment C_FOO */'),
            'C_FOOBAR' => array('value' => 42,   'comment' => '/** Test comment C_FOO */'),
            'C_BARFOO' => array('value' => 23,   'comment' => '/** Test comment C_FOO */'),
        );
        
        foreach ($class->getConstants() as $constant)
        {
            $name = $constant->getName();
            $this->assertArrayHasKey($name, $expected);

            $value = $constant->getValue();

            $this->assertEquals($expected[$name]['value'], $value->getValue());
            
            $comment = $constant->getDocComment();
            $this->assertEquals($expected[$name]['comment'], $comment);
        }
    }
    
    /**
     * Parses a source file and extracts the first class property instance.
     *
     * @param string $file The source file.
     * 
     * @return PHP_Reflection_AST_PropertyI
     */
    private static function _testParseProperty($file)
    {
        $packages = self::parseSource("/parser/properties/{$file}");
        self::assertEquals(1, $packages->count());
        
        $package = $packages->current();
        self::assertEquals(1, $package->getClasses()->count());
        
        $class = $package->getClasses()->current();
        self::assertEquals(1, $class->getProperties()->count());
        
        $property = $class->getProperties()->current();
        self::assertNotNull($property);
        self::assertType('PHP_Reflection_AST_PropertyI', $property);
        
        return $property;
    }
}