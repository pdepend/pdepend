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
use PDepend\AbstractTestCase;
use PDepend\Source\AST\ASTArray;
use PDepend\Source\AST\ASTArrayElement;
use PDepend\Source\AST\ASTAssignmentExpression;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTClassOrInterfaceReference;
use PDepend\Source\AST\ASTClosure;
use PDepend\Source\AST\ASTConstantDeclarator;
use PDepend\Source\AST\ASTConstantDefinition;
use PDepend\Source\AST\ASTConstantPostfix;
use PDepend\Source\AST\ASTExpression;
use PDepend\Source\AST\ASTFieldDeclaration;
use PDepend\Source\AST\ASTFormalParameter;
use PDepend\Source\AST\ASTFormalParameters;
use PDepend\Source\AST\ASTFunctionPostfix;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTMemberPrimaryPrefix;
use PDepend\Source\AST\ASTNode;
use PDepend\Source\AST\ASTReturnStatement;
use PDepend\Source\AST\ASTScalarType;
use PDepend\Source\AST\ASTSelfReference;
use PDepend\Source\AST\ASTTypeArray;
use PDepend\Source\AST\ASTTypeCallable;
use PDepend\Source\AST\ASTTypeIterable;
use PDepend\Source\AST\ASTVariable;
use PDepend\Source\AST\ASTVariableDeclarator;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Parser\UnexpectedTokenException;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Util\Cache\CacheDriver;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\AbstractPHPParser} class.
 *
 * @covers \PDepend\Source\Language\PHP\AbstractPHPParser
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 *
 * @group unittest
 */
class PHPParserVersion74Test extends AbstractTestCase
{
    public function testTypedProperties(): void
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

        $this->assertSame('The parameter does not have a type specification.', $message);

        /** @var array[] $declarations */
        $declarations = array_map(function (ASTFieldDeclaration $child) {
            $childChildren = $child->getChildren();

            return [
                $child->hasType() ? $child->getType() : null,
                $childChildren[1],
            ];
        }, $children);

        foreach ([
            ['int', '$id'],
            ['float', '$money'],
            ['bool', '$active'],
            ['string', '$name'],
            ['array', '$list', ASTTypeArray::class],
            ['self', '$parent', ASTSelfReference::class],
            ['callable', '$event', ASTTypeCallable::class],
            ['\Closure', '$fqn', ASTClassOrInterfaceReference::class],
            ['iterable', '$actions', ASTTypeIterable::class],
            ['object', '$bag', ASTClassOrInterfaceReference::class],
            ['Role', '$role', ASTClassOrInterfaceReference::class],
            ['?int', '$idN'],
            ['?float', '$moneyN'],
            ['?bool', '$activeN'],
            ['?string', '$nameN'],
            ['?array', '$listN', ASTTypeArray::class],
            ['?self', '$parentN', ASTSelfReference::class],
            ['?callable', '$eventN', ASTTypeCallable::class],
            ['?\Closure', '$fqnN', ASTClassOrInterfaceReference::class],
            ['?iterable', '$actionsN', ASTTypeIterable::class],
            ['?object', '$bagN', ASTClassOrInterfaceReference::class],
            ['?Role', '$roleN', ASTClassOrInterfaceReference::class],
        ] as $index => $expected) {
            [$expectedType, $expectedVariable] = $expected;
            $expectedTypeClass = $expected[2] ?? ASTScalarType::class;
            [$type, $variable] = $declarations[$index];

            $this->assertInstanceOf(
                $expectedTypeClass,
                $type,
                "Wrong type for $expectedType $expectedVariable"
            );
            $this->assertSame(ltrim($expectedType, '?'), $type->getImage());
            $this->assertInstanceOf(
                ASTVariableDeclarator::class,
                $variable,
                "Wrong variable for $expectedType $expectedVariable"
            );
            $this->assertSame($expectedVariable, $variable->getImage());
        }
    }

    public function testSingleTypedProperty(): void
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

    public function testTypedPropertiesSyntaxError(): void
    {
        $this->expectException(
            UnexpectedTokenException::class
        );
        $this->expectExceptionMessage(
            'Unexpected token: string, line: 4, col: 16, file:'
        );

        $this->parseCodeResourceForTest();
    }

    public function testArrowFunctions(): void
    {
        /** @var ASTClosure $closure */
        $closure = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTFunctionPostfix::class
        )->getChild(1)->getChild(0);

        $this->assertInstanceOf(ASTClosure::class, $closure);
        /** @var ASTFormalParameters $parameters */
        $parameters = $closure->getChild(0);
        $this->assertInstanceOf(ASTFormalParameters::class, $parameters);
        $this->assertCount(1, $parameters->getChildren());
        /** @var ASTFormalParameter $parameter */
        $parameter = $parameters->getChild(0);
        $this->assertInstanceOf(ASTFormalParameter::class, $parameter);
        /** @var ASTVariableDeclarator $parameter */
        $variableDeclarator = $parameter->getChild(0);
        $this->assertInstanceOf(ASTVariableDeclarator::class, $variableDeclarator);
        $this->assertSame('$number', $variableDeclarator->getImage());
        /** @var ASTReturnStatement $parameters */
        $return = $closure->getChild(1);
        $this->assertInstanceOf(ASTReturnStatement::class, $return);
        $this->assertSame('=>', $return->getImage());
        $this->assertCount(1, $return->getChildren());
        /** @var ASTExpression $expression */
        $expression = $return->getChild(0);
        $this->assertInstanceOf(ASTExpression::class, $expression);
        $this->assertSame([
            ASTVariable::class,
            ASTExpression::class,
            ASTLiteral::class,
        ], array_map('get_class', $expression->getChildren()));
        $this->assertSame([
            '$number',
            '*',
            '2',
        ], array_map(
            static fn(ASTNode $node) => $node->getImage(),
            $expression->getChildren(),
        ));
    }

    public function testArrowFunctionsWithReturnType(): void
    {
        /** @var ASTClosure $closure */
        $closure = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTFunctionPostfix::class
        )->getChild(1)->getChild(0);

        $this->assertInstanceOf(ASTClosure::class, $closure);
        /** @var ASTFormalParameters $parameters */
        $parameters = $closure->getChild(0);
        $this->assertInstanceOf(ASTFormalParameters::class, $parameters);
        $this->assertCount(1, $parameters->getChildren());
        /** @var ASTFormalParameter $parameter */
        $parameter = $parameters->getChild(0);
        $this->assertInstanceOf(ASTFormalParameter::class, $parameter);
        /** @var ASTVariableDeclarator $parameter */
        $variableDeclarator = $parameter->getChild(0);
        $this->assertInstanceOf(ASTVariableDeclarator::class, $variableDeclarator);
        $this->assertSame('$number', $variableDeclarator->getImage());
        /** @var ASTScalarType $parameters */
        $type = $closure->getChild(1);
        $this->assertInstanceOf(ASTScalarType::class, $type);
        $this->assertSame('int', $type->getImage());
        /** @var ASTReturnStatement $parameters */
        $return = $closure->getChild(2);
        $this->assertInstanceOf(ASTReturnStatement::class, $return);
        $this->assertSame('=>', $return->getImage());
        $this->assertCount(1, $return->getChildren());
        /** @var ASTExpression $expression */
        $expression = $return->getChild(0);
        $this->assertInstanceOf(ASTExpression::class, $expression);
        $this->assertSame([
            ASTVariable::class,
            ASTExpression::class,
            ASTLiteral::class,
        ], array_map('get_class', $expression->getChildren()));
        $this->assertSame([
            '$number',
            '*',
            '2',
        ], array_map(
            static fn(ASTNode $node) => $node->getImage(),
            $expression->getChildren(),
        ));
    }

    public function testTypeCovarianceAndArgumentTypeContravariance(): void
    {
        $this->assertNotNull($this->parseCodeResourceForTest());
    }

    public function testNullCoalescingAssignmentOperator(): void
    {
        /** @var ASTAssignmentExpression $assignment */
        $assignment = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTAssignmentExpression::class
        );

        $this->assertSame('??=', $assignment->getImage());
    }

    public function testUnpackingInsideArrays(): void
    {
        $expression = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTArray::class
        );
        $this->assertSame([
            ASTArrayElement::class,
            ASTArrayElement::class,
            ASTArrayElement::class,
            ASTArrayElement::class,
            ASTArrayElement::class,
        ], array_map('get_class', $expression->getChildren()));
        /** @var ASTNode[] $elements */
        $elements = array_map(
            static fn($node) => $node->getChild(0),
            $expression->getChildren(),
        );
        $this->assertSame([
            ASTLiteral::class,
            ASTLiteral::class,
            ASTExpression::class,
            ASTLiteral::class,
            ASTLiteral::class,
        ], array_map('get_class', $elements));
        /** @var ASTExpression $expression */
        $expression = $elements[2];
        $this->assertSame([
            '...',
            '$numbers',
        ], array_map(
            static fn(ASTNode $node) => $node->getImage(),
            $expression->getChildren(),
        ));
    }

    public function testNumericLiteralSeparator(): void
    {
        $expression = $this->getFirstNodeOfTypeInFunction(
            $this->getCallingTestMethod(),
            ASTExpression::class
        );
        $this->assertSame([
            ASTLiteral::class,
            ASTExpression::class,
            ASTLiteral::class,
            ASTExpression::class,
            ASTLiteral::class,
            ASTExpression::class,
            ASTLiteral::class,
        ], array_map('get_class', $expression->getChildren()));

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
     */
    public function testConstantArrayConcatenation(): void
    {
        /** @var ASTClass $class */
        $class = $this->getFirstClassForTestCase();

        /** @var ASTConstantDefinition[] $sontants */
        $constants = $class->getChildren();

        $this->assertCount(2, $constants);
        $this->assertInstanceOf(ASTConstantDefinition::class, $constants[0]);
        $this->assertInstanceOf(ASTConstantDefinition::class, $constants[1]);

        /** @var ASTConstantDeclarator[] $declarators */
        $declarators = $constants[1]->getChildren();

        $this->assertCount(1, $declarators);
        $this->assertInstanceOf(ASTConstantDeclarator::class, $declarators[0]);

        /** @var ASTExpression $expression */
        $expression = $declarators[0]->getValue()->getValue();

        $this->assertInstanceOf(ASTExpression::class, $expression);

        $nodes = $expression->getChildren();
        $this->assertInstanceOf(ASTMemberPrimaryPrefix::class, $nodes[0]);
        $this->assertInstanceOf(ASTExpression::class, $nodes[1]);
        $this->assertSame('+', $nodes[1]->getImage());
        $this->assertInstanceOf(ASTArray::class, $nodes[2]);

        $nodes = $nodes[0]->getChildren();
        $this->assertInstanceOf(ASTSelfReference::class, $nodes[0]);
        $this->assertInstanceOf(ASTConstantPostfix::class, $nodes[1]);
        $this->assertSame('A', $nodes[1]->getImage());
    }

    public function testReadOnlyNamedImport(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $this->parseCodeResourceForTest()->current();
    }

    /**
     * @return AbstractPHPParser
     */
    protected function createPHPParser(Tokenizer $tokenizer, Builder $builder, CacheDriver $cache)
    {
        return $this->getAbstractClassMock(
            'PDepend\\Source\\Language\\PHP\\PHPParserVersion80',
            [$tokenizer, $builder, $cache]
        );
    }
}
