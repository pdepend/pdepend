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
 * @since 1.0.0
 */

namespace PDepend\Source\AST;

/**
 * This node class represents a trait use statement.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @since 1.0.0
 */
class ASTTraitUseStatement extends ASTStatement
{
    /** @var ASTMethod[]|null */
    private $allMethods;

    /**
     * Returns an array with all aliased or directly imported methods.
     *
     * @return ASTMethod[]
     */
    public function getAllMethods()
    {
        if ($this->allMethods === null) {
            $this->allMethods = [];

            foreach ($this->nodes as $node) {
                if ($node instanceof ASTTraitReference) {
                    $this->collectMethods($node);
                }
            }
        }

        return $this->allMethods;
    }

    /**
     * This method tests if the given {@link ASTMethod} is excluded
     * by precedence statement in this use statement. It will return <b>true</b>
     * if the given <b>$method</b> is excluded, otherwise the return value of
     * this method will be <b>false</b>.
     *
     * @return bool
     */
    public function hasExcludeFor(ASTMethod $method)
    {
        $methodName = strtolower($method->getImage());
        $methodParent = $method->getParent();

        $precedences = $this->findChildrenOfType(ASTTraitAdaptationPrecedence::class);

        foreach ($precedences as $precedence) {
            if (strtolower($precedence->getImage()) !== $methodName) {
                continue;
            }

            /** @var ASTTraitReference[] */
            $children = $precedence->getChildren();
            array_shift($children);

            foreach ($children as $child) {
                if ($methodParent === $child->getType()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Collects all directly defined methods or method aliases for the given
     * {@link ASTTraitReference}
     *
     * @param ASTTraitReference $reference Context trait reference.
     */
    private function collectMethods(ASTTraitReference $reference): void
    {
        foreach ($reference->getType()->getAllMethods() as $method) {
            foreach ($this->getAliasesFor($method) as $alias) {
                $this->allMethods[] = $alias;
            }
        }
    }

    /**
     * Returns an <b>array</b> with all aliases for the given method. If no
     * alias exists for the given method, this method will simply return the
     * an <b>array</b> with the original method.
     *
     * @return ASTMethod[]
     */
    private function getAliasesFor(ASTMethod $method)
    {
        $name = strtolower($method->getImage());

        $newNames = [];
        foreach ($this->getAliases() as $alias) {
            $name2 = strtolower($alias->getImage());
            if ($name2 !== $name) {
                continue;
            }

            $modifier = $method->getModifiers();
            if (-1 < $alias->getNewModifier()) {
                $modifier &= ~(
                    State::IS_PUBLIC |
                    State::IS_PROTECTED |
                    State::IS_PRIVATE
                );
                $modifier |= $alias->getNewModifier();
            }

            $newName = $method->getImage();
            if ($alias->getNewName()) {
                $newName = $alias->getNewName();
            }

            if (0 === count($alias->getChildren())) {
                $newMethod = clone $method;
                $newMethod->setName($newName);
                $newMethod->setModifiers($modifier);

                $newNames[] = $newMethod;

                continue;
            }

            /** @var ASTTraitReference */
            $child = $alias->getChild(0);

            if ($child->getType() !== $method->getParent()) {
                continue;
            }

            $newMethod = clone $method;
            $newMethod->setName($newName);
            $newMethod->setModifiers($modifier);

            $newNames[] = $newMethod;
        }

        if (count($newNames) > 0) {
            return $newNames;
        }

        return [$method];
    }

    /**
     * Returns an <b>array</b> with all alias statements declared in this use
     * statement.
     *
     * @return ASTTraitAdaptationAlias[]
     */
    private function getAliases()
    {
        return $this->findChildrenOfType(ASTTraitAdaptationAlias::class);
    }
}
