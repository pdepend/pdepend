<?php
/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2017 Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend\Issues;

use PDepend\Source\AST\ASTAllocationExpression;
use PDepend\Source\AST\ASTStatement;

/**
 * Test case for ticket 002, PHP 5.3 namespace support.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @group unittest
 */
class NewClassInstanceTest extends AbstractFeatureTestCase
{
    /**
     * Tests that a new keyword can be followed by variable or parentheses expression.
     *
     * @return void
     */
    public function testNewKeyword()
    {
        $namespaces = $this->parseSource('issues/601-new-with-expression.php');

        $methods = $namespaces->current()
            ->getClasses()
            ->current()
            ->getAllMethods();
        $method = $methods['bar'];
        $children = $method->getChildren();
        $scope = $children[1];
        $instructions = $scope->getChildren();

        $this->assertCount(5, $instructions);
        $self = $this;

        $expressions = array_map(function (ASTStatement $statement) use ($self) {
            $children = $statement->getChildren();

            $self->assertCount(1, $children);
            $self->assertInstanceOf('PDepend\\Source\\AST\\ASTAssignmentExpression', $children[0]);

            $children = $children[0]->getChildren();
            $self->assertCount(2, $children);
            $self->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $children[0]);
            $self->assertMatchesRegularExpression('/^\$object\d+$/', $children[0]->getImage());
            $self->assertInstanceOf('PDepend\\Source\\AST\\ASTAllocationExpression', $children[1]);
            $children = $children[1]->getChildren();
            $self->assertCount(1, $children);

            return $children[0];
        }, array_slice($instructions, 1, 3));


        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $expressions[0]);
        $this->assertEquals('$class', $expressions[0]->getImage());

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariable', $expressions[1]);
        $this->assertEquals('$class', $expressions[1]->getImage());

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTLiteral', $expressions[2]);
        $this->assertEquals("'stdClass'", $expressions[2]->getImage());
    }
}
