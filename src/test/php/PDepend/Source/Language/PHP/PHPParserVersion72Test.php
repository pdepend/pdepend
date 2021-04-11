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

use PDepend\AbstractTest;
use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTMethod;
use PDepend\Source\AST\ASTParameter;
use PDepend\Source\Builder\Builder;
use PDepend\Source\Tokenizer\Tokenizer;
use PDepend\Util\Cache\CacheDriver;

/**
 * Test case for the {@link \PDepend\Source\Language\PHP\PHPParserVersion72} class.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @covers \PDepend\Source\Language\PHP\PHPParserVersion72
 * @group unittest
 */
class PHPParserVersion72Test extends AbstractTest
{
    /**
     * @return void
     */
    public function testObjectTypeHintReturn()
    {
        $type = $this->getFirstFunctionForTestCase()->getReturnType();

        $this->assertFalse($type->isScalar(), 'object should not be scalar according to https://www.php.net/manual/en/function.is-scalar.php');
        $this->assertFalse($type->isArray());
        $this->assertSame('object', $type->getImage());
    }

    /**
     * @return void
     */
    public function testObjectTypeHintParameter()
    {
        $type = $this->getFirstFormalParameterForTestCase()->getType();

        $this->assertFalse($type->isScalar(), 'object should not be scalar according to https://www.php.net/manual/en/function.is-scalar.php');
        $this->assertFalse($type->isArray());
        $this->assertSame('object', $type->getImage());
    }

    public function testAbstractMethodOverriding()
    {
        /** @var ASTArtifactList $classes */
        $classes = $this->parseCodeResourceForTest()->current()->getClasses();
        /** @var ASTClass $class */
        $class = $classes[1];
        /** @var ASTArtifactList $classes */
        $methods = $class->getMethods();
        /** @var ASTMethod $method */
        $method = $methods[0];
        /** @var ASTArtifactList $parameters */
        $parameters = $method->getParameters();
        /** @var ASTParameter $parameter */
        $parameter = $parameters[0];

        $this->assertTrue($method->isAbstract());
        $this->assertSame('int', $method->getReturnType()->getImage());
        $this->assertNull($parameter->getClass());
    }

    /**
     * @return \PDepend\Source\AST\ASTNamespace
     */
    public function testGroupUseStatementTrailingComma()
    {
        $namespaces = $this->parseCodeResourceForTest();
        $this->assertGreaterThan(0, count($namespaces));
        $this->assertContainsOnlyInstancesOf('PDepend\\Source\\AST\\ASTNamespace', $namespaces);
    }

    /**
     * @param \PDepend\Source\Tokenizer\Tokenizer $tokenizer
     * @param \PDepend\Source\Builder\Builder $builder
     * @param \PDepend\Util\Cache\CacheDriver $cache
     * @return \PDepend\Source\Language\PHP\AbstractPHPParser
     */
    protected function createPHPParser(Tokenizer $tokenizer, Builder $builder, CacheDriver $cache)
    {
        return $this->getAbstractClassMock(
            'PDepend\\Source\\Language\\PHP\\PHPParserVersion72',
            array($tokenizer, $builder, $cache)
        );
    }
}
