<?php
/**
 * This file is part of PDepend.
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

namespace PDepend\Source\Language\PHP;

use OutOfBoundsException;
use PDepend\AbstractTest;
use PDepend\Source\AST\ASTAssignmentExpression;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTClosure;
use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\AST\ASTConstantDefinition;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTReturnStatement;
use PDepend\Source\AST\ASTVariableDeclarator;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserVersion74} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion74
 * @group unittest
 */
class PHPParserVersion74Test extends AbstractTest
{
    /**
     * @return void
     */
    public function testTypedProperties()
    {
        /** @var ASTClass $class */
        $class = $this->getFirstClassForTestCase();
        $children = $class->getChildren();
        /** @var ASTFieldDeclaration $mixedDeclaration */
        $mixedDeclaration = array_shift($children);

        $this->assertFalse($mixedDeclaration->hasType());

        $message = null;

        try {
            $mixedDeclaration->getType();
        } catch (OutOfBoundsException $exception) {
            $message = $exception->getMessage();
        }

        $this->assertSame('The parameter does not has a type specification.', $message);

        /** @var array[] $declarations */
        $declarations = array_map(function (ASTFieldDeclaration $child) {
            $childChildren = $child->getChildren();

            return array(
                $child->hasType() ? $child->getType() : null,
                $childChildren[1],
            );
        }, $children);

        foreach (array(
            array('int', '$id'),
            array('float', '$money'),
            array('bool', '$active'),
            array('string', '$name'),
            array('array', '$list', 'PDepend\\Source\\AST\\ASTTypeArray'),
            array('self', '$parent', 'PDepend\\Source\\AST\\ASTSelfReference'),
            array('callable', '$event', 'PDepend\\Source\\AST\\ASTTypeCallable'),
            array('\Closure', '$fqn', 'PDepend\\Source\\AST\\ASTClassOrInterfaceReference'),
            array('iterable', '$actions', 'PDepend\\Source\\AST\\ASTTypeIterable'),
            array('object', '$bag', 'PDepend\\Source\\AST\\ASTClassOrInterfaceReference'),
            array('Role', '$role', 'PDepend\\Source\\AST\\ASTClassOrInterfaceReference'),
            array('?int', '$idN'),
            array('?float', '$moneyN'),
            array('?bool', '$activeN'),
            array('?string', '$nameN'),
            array('?array', '$listN', 'PDepend\\Source\\AST\\ASTTypeArray'),
            array('?self', '$parentN', 'PDepend\\Source\\AST\\ASTSelfReference'),
            array('?callable', '$eventN', 'PDepend\\Source\\AST\\ASTTypeCallable'),
            array('?\Closure', '$fqnN', 'PDepend\\Source\\AST\\ASTClassOrInterfaceReference'),
            array('?iterable', '$actionsN', 'PDepend\\Source\\AST\\ASTTypeIterable'),
            array('?object', '$bagN', 'PDepend\\Source\\AST\\ASTClassOrInterfaceReference'),
            array('?Role', '$roleN', 'PDepend\\Source\\AST\\ASTClassOrInterfaceReference'),
        ) as $index => $expected) {
            list($expectedType, $expectedVariable) = $expected;
            $expectedTypeClass = isset($expected[2]) ? $expected[2] : 'PDepend\\Source\\AST\\ASTScalarType';
            list($type, $variable) = $declarations[$index];

            $this->assertInstanceOf(
                $expectedTypeClass,
                $type,
                "Wrong type for $expectedType $expectedVariable"
            );
            $this->assertSame(ltrim($expectedType, '?'), $type->getImage());
            $this->assertInstanceOf(
                'PDepend\\Source\\AST\\ASTVariableDeclarator',
                $variable,
                "Wrong variable for $expectedType $expectedVariable"
            );
            $this->assertSame($expectedVariable, $variable->getImage());
        }
    }

    public function testSingleTypedProperty()
    {
        /** @var ASTClass $class */
        $class = $this->getFirstClassForTestCase();
        /** @var ASTFieldDeclaration $field */
        $field = $class->getChild(0);
        $this->assertTrue($field->hasType());
        $this->assertSame('int', $field->getType()->getImage());
        $this->assertTrue($field->isPrivate());
        $this->assertFalse($field->isProtected());
        $this->assertFalse($field->isPublic());
    }

    public function testTypedPropertiesSyntaxError()
    {
        $this->setExpectedException(
            'PDepend\\Source\\Parser\\UnexpectedTokenException',
            'Unexpected token: string, line: 4, col: 16, file:'
        );

        $this->parseCodeResourceForTest();
    }

    public function testArrowFunctions()
    {
        if (version_compare(phpversion(), '7.4.0', '<')) {
            $this->markTestSkipped('This test requires PHP >= 7.4');
        }

        /** @var ASTClosure $closure */
        $closure = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTFunctionPostfix'
        )->getChild(1)->getChild(0);

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTClosure', $closure);
        /** @var ASTFormalParameters $parameters */
        $parameters = $closure->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFormalParameters', $parameters);
        $this->assertCount(1, $parameters->getChildren());
        /** @var ASTFormalParameter $parameter */
        $parameter = $parameters->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTFormalParameter', $parameter);
        /** @var ASTVariableDeclarator $parameter */
        $variableDeclarator = $parameter->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTVariableDeclarator', $variableDeclarator);
        $this->assertSame('$number', $variableDeclarator->getImage());
        /** @var ASTReturnStatement $parameters */
        $return = $closure->getChild(1);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTReturnStatement', $return);
        $this->assertSame('=>', $return->getImage());
        $this->assertCount(1, $return->getChildren());
        /** @var ASTExpression $expression */
        $expression = $return->getChild(0);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $expression);
        $this->assertSame(array(
            'PDepend\\Source\\AST\\ASTVariable',
            'PDepend\\Source\\AST\\ASTExpression',
            'PDepend\\Source\\AST\\ASTLiteral',
        ), array_map('get_class', $expression->getChildren()));
        $this->assertSame(array(
            '$number',
            '*',
            '2',
        ), array_map(function (ASTNode $node) {
            return $node->getImage();
        }, $expression->getChildren()));
    }

    public function testTypeCovarianceAndArgumentTypeContravariance()
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testNullCoalescingAssignmentOperator()
    {
        if (version_compare(phpversion(), '7.4.0', '<')) {
            $this->markTestSkipped('This test requires PHP >= 7.4');
        }

        /** @var ASTAssignmentExpression $assignment */
        $assignment = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTAssignmentExpression'
        );

        $this->assertSame('??=', $assignment->getImage());
    }

    public function testUnpackingInsideArrays()
    {
        if (version_compare(phpversion(), '7.4.0', '<')) {
            $this->markTestSkipped('This test requires PHP >= 7.4');
        }

        $expression = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTArray'
        );
        $this->assertSame(array(
            'PDepend\\Source\\AST\\ASTArrayElement',
            'PDepend\\Source\\AST\\ASTArrayElement',
            'PDepend\\Source\\AST\\ASTArrayElement',
            'PDepend\\Source\\AST\\ASTArrayElement',
            'PDepend\\Source\\AST\\ASTArrayElement',
        ), array_map('get_class', $expression->getChildren()));
        /** @var ASTNode[] $elements */
        $elements = array_map(function ($node) {
            return $node->getChild(0);
        }, $expression->getChildren());
        $this->assertSame(array(
            'PDepend\Source\AST\ASTLiteral',
            'PDepend\Source\AST\ASTLiteral',
            'PDepend\Source\AST\ASTExpression',
            'PDepend\Source\AST\ASTLiteral',
            'PDepend\Source\AST\ASTLiteral',
        ), array_map('get_class', $elements));
        /** @var ASTExpression $expression */
        $expression = $elements[2];
        $this->assertSame(array(
            '...',
            '$numbers',
        ), array_map(function (ASTNode $node) {
            return $node->getImage();
        }, $expression->getChildren()));
    }

    public function testNumericLiteralSeparator()
    {
        if (version_compare(phpversion(), '7.4.0', '<')) {
            $this->markTestSkipped('This test requires PHP >= 7.4');
        }

        $expression = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            'PDepend\\Source\\AST\\ASTExpression'
        );
        $this->assertSame(array(
            'PDepend\\Source\\AST\\ASTLiteral',
            'PDepend\\Source\\AST\\ASTExpression',
            'PDepend\\Source\\AST\\ASTLiteral',
            'PDepend\\Source\\AST\\ASTExpression',
            'PDepend\\Source\\AST\\ASTLiteral',
            'PDepend\\Source\\AST\\ASTExpression',
            'PDepend\\Source\\AST\\ASTLiteral',
        ), array_map('get_class', $expression->getChildren()));

        $this->assertSame('6.674_083e-11', $expression->getChild(0)->getImage());
        $this->assertSame('299_792_458', $expression->getChild(2)->getImage());
        $this->assertSame('0xCAFE_F00D', $expression->getChild(4)->getImage());
        $this->assertSame('0b0101_1111', $expression->getChild(6)->getImage());
    }

    /**
     * Tests issue with constant array concatenation.
     * https://github.com/pdepend/pdepend/issues/299
     *
     * Already checked in PHPParserVersion56Test, the level it belongs.
     * Here we ensure it's still working in 7.4 parser.
     *
     * @return void
     */
    public function testConstantArrayConcatenation()
    {
        /** @var ASTClass $class */
        $class = $this->getFirstClassForTestCase();

        /** @var ASTConstantDefinition[] $sontants */
        $constants = $class->getChildren();

        $this->assertCount(2, $constants);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantDefinition', $constants[0]);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantDefinition', $constants[1]);

        /** @var ASTConstantDeclarator[] $declarators */
        $declarators = $constants[1]->getChildren();

        $this->assertCount(1, $declarators);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantDeclarator', $declarators[0]);

        /** @var ASTExpression $expression */
        $expression = $declarators[0]->getValue()->getValue();

        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $expression);

        $nodes = $expression->getChildren();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTMemberPrimaryPrefix', $nodes[0]);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTExpression', $nodes[1]);
        $this->assertSame('+', $nodes[1]->getImage());
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTArray', $nodes[2]);

        $nodes = $nodes[0]->getChildren();
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTSelfReference', $nodes[0]);
        $this->assertInstanceOf('PDepend\\Source\\AST\\ASTConstantPostfix', $nodes[1]);
        $this->assertSame('A', $nodes[1]->getImage());
    }
}
