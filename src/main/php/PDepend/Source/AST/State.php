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

namespace PDepend\Source\AST;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Holds constants with internal state constants
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
interface State
{
    /** Marks a class or interface as implicit abstract. */
    public const IS_IMPLICIT_ABSTRACT = ReflectionClass::IS_IMPLICIT_ABSTRACT;

    /** Marks a class or interface as explicit abstract. */
    public const IS_EXPLICIT_ABSTRACT = ReflectionClass::IS_EXPLICIT_ABSTRACT;

    /** Marks a node as public. */
    public const IS_PUBLIC = ReflectionMethod::IS_PUBLIC;

    /** Marks a node as protected. */
    public const IS_PROTECTED = ReflectionMethod::IS_PROTECTED;

    /** Marks a node as private. */
    public const IS_PRIVATE = ReflectionMethod::IS_PRIVATE;

    /** Marks a node as abstract. */
    public const IS_ABSTRACT = ReflectionMethod::IS_ABSTRACT;

    /** Marks a node as final. */
    public const IS_FINAL = ReflectionMethod::IS_FINAL;

    /** Marks a node as static. */
    public const IS_STATIC = ReflectionMethod::IS_STATIC;

    /** Marks a node as readonly. */
    public const IS_READONLY = ReflectionProperty::IS_READONLY;
}
