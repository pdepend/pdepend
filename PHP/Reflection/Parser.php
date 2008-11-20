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
 * @category  PHP
 * @package   PHP_Reflection
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Reflection/BuilderI.php';
require_once 'PHP/Reflection/ConstantsI.php';
require_once 'PHP/Reflection/TokenizerI.php';
require_once 'PHP/Reflection/PHPTypesI.php';

require_once 'PHP/Reflection/AST/LiteralI.php';
require_once 'PHP/Reflection/AST/AdditiveExpressionI.php';
require_once 'PHP/Reflection/AST/MultiplicativeExpressionI.php';

require_once 'PHP/Reflection/Exceptions/IdentifierExpectedException.php';
require_once 'PHP/Reflection/Exceptions/UnclosedBodyException.php';
require_once 'PHP/Reflection/Exceptions/UnexpectedElementException.php';
require_once 'PHP/Reflection/Exceptions/UnexpectedTokenException.php';

/**
 * The php source parser.
 *
 * With the default settings the parser includes annotations, better known as
 * doc comment tags, in the generated result. This means it extracts the type
 * information of @var tags for properties, and types in @return + @throws tags
 * of functions and methods. The current implementation tries to ignore all
 * scalar types from <b>boolean</b> to <b>void</b>. You should disable this
 * feature for project that have more or less invalid doc comments, because it
 * could produce invalid results.
 *
 * <code>
 *   $parser->setIgnoreAnnotations();
 * </code>
 *
 * <b>Note</b>: Due to the fact that it is possible to use the same name for
 * multiple classes and interfaces, and there is no way to determine to which
 * package it belongs, while the parser handles class, interface or method
 * signatures, the parser could/will create a code tree that doesn't reflect the
 * real source structure.
 *
 * @category  PHP
 * @package   PHP_Reflection
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Reflection_Parser
    implements PHP_Reflection_ConstantsI, PHP_Reflection_PHPTypesI
{
    /**
     * Maps between tokenizer token ids and scalar type identifiers
     *
     * @var unknown_type
     */
    private static $_scalarTypeMap = array(
        self::T_NULL                     => self::IS_NULL,
        self::T_TRUE                     => self::IS_BOOLEAN,
        self::T_FALSE                    => self::IS_BOOLEAN,
        self::T_DNUMBER                  => self::IS_DOUBLE,
        self::T_LNUMBER                  => self::IS_INTEGER,
        self::T_NUM_STRING               => self::IS_DOUBLE,
        self::T_CONSTANT_ENCAPSED_STRING => self::IS_STRING
    );

    /**
     * The package defined in the file level comment.
     *
     * @type string
     * @var string $_globalPackage
     */
    private $_globalPackage = PHP_Reflection_BuilderI::PKG_UNKNOWN;

    /**
     * The last doc comment block.
     *
     * @type string
     * @var string $_comment
     */
    private $_comment = null;

    /**
     * Object modifiers likes <b>IS_ABSTRACT</b>, <b>IS_FINAL</b>, <b>IS_PUBLIC</b>
     * etc.
     *
     * @type integer
     * @var integer $_modifiers
     */
    private $_modifiers = 0;

    /**
     * Last parsed package tag.
     *
     * @type string
     * @var string $_package
     */
    private $_package = PHP_Reflection_BuilderI::PKG_UNKNOWN;

    /**
     * Position of the context type within the analyzed file.
     *
     * @type integer
     * @var integer $_typePosition
     */
    private $_typePosition = 0;

    /**
     * Position of the method within the analyzed context type.
     *
     * @type integer
     * @var integer $_methodPosition
     */
    private $_methodPosition = 0;

    /**
     * The currently parsed class or interface instance or <b>null</b>.
     *
     * @var PHP_Reflection_AST_ClassOrInterfaceI $_classOrInterface
     */
    private $_classOrInterface = null;

    /**
     * Stack of already parsed expressions.
     *
     * @var array(PHP_Reflection_AST_SourceElementI) $_elementStack
     */
    private $_elementStack = array();

    /**
     * The used code tokenizer.
     *
     * @type PHP_Reflection_TokenizerI
     * @var PHP_Reflection_TokenizerI $tokenizer
     */
    protected $tokenizer = null;

    /**
     * The used data structure builder.
     *
     * @type PHP_Reflection_BuilderI
     * @var PHP_Reflection_BuilderI $builder
     */
    protected $builder = null;

    /**
     * List of scalar php types.
     *
     * @type array<string>
     * @var array(string) $_scalarTypes
     */
    private $_scalarTypes = array(
        'array',
        'bool',
        'boolean',
        'double',
        'float',
        'int',
        'integer',
        'mixed',
        'null',
        'real',
        'resource',
        'string',
        'unknown',      // Eclipse default return type
        'unknown_type', // Eclipse default property type
        'void'
    );

    /**
     * If this property is set to <b>true</b> the parser will ignore all doc
     * comment annotations.
     *
     * @type boolean
     * @var boolean $_ignoreAnnotations
     */
    private $_ignoreAnnotations = false;

    /**
     * Constructs a new source parser.
     *
     * @param PHP_Reflection_BuilderI   $builder   The used node builder.
     * @param PHP_Reflection_TokenizerI $tokenizer The used code tokenizer.
     */
    public function __construct(PHP_Reflection_BuilderI $builder,
                                PHP_Reflection_TokenizerI $tokenizer = null)
    {
        $this->tokenizer = $tokenizer;
        $this->builder   = $builder;
    }

    /**
     * Sets the ignore annotations flag. This means that the parser will ignore
     * doc comment annotations.
     *
     * @return void
     */
    public function setIgnoreAnnotations()
    {
        $this->_ignoreAnnotations = true;
    }

    /**
     * Parses the contents of the tokenizer and generates a node tree based on
     * the found tokens.
     *
     * @param Iterator $files Iterator with file names to parse.
     *
     * @return void
     */
    public function parse(Iterator $files)
    {
        foreach ($files as $file) {

            $this->reset();

            // Set next source file
            $this->tokenizer->setSourceFile($file);

            while ($this->tokenizer->peek() !== self::T_EOF) {

                switch ($this->tokenizer->peek()) {
                case self::T_ABSTRACT:
                    $this->_consumeToken(self::T_ABSTRACT);
                    $this->_modifiers |= ReflectionClass::IS_EXPLICIT_ABSTRACT;
                    break;

                case self::T_FINAL:
                    $this->_consumeToken(self::T_FINAL);
                    $this->_modifiers |= ReflectionClass::IS_FINAL;
                    break;

                case self::T_DOC_COMMENT:
                    $token = $this->_consumeToken(self::T_DOC_COMMENT);

                    $this->_comment = $token[1];
                    $this->_package = $this->parsePackage($token[1]);

                    // Check for doc level comment
                    if ($this->_globalPackage === PHP_Reflection_BuilderI::PKG_UNKNOWN
                     && $this->isFileComment() === true) {

                        $this->_globalPackage = $this->_package;

                        $this->tokenizer->getSourceFile()->setDocComment($token[1]);
                    }
                    break;

                case self::T_INTERFACE:
                    $this->_consumeToken(self::T_INTERFACE);
                    $this->_parseInterfaceDeclaration();
                    break;

                case self::T_CLASS:
                    $this->_consumeToken(self::T_CLASS);
                    $this->_parseClassDeclaration();
                    break;

                case self::T_FUNCTION:
                    $this->_parseFunctionOrClosure();
                    break;

                default:
                    // TODO: Handle/log unused tokens
                    $this->tokenizer->next();
                    break;
                }
            }

            // Reset global package and type position
            $this->_globalPackage = PHP_Reflection_BuilderI::PKG_UNKNOWN;
            $this->_typePosition = 0;
        }
    }

    /**
     * Parses a function node.
     *
     * @param integer $line The line number of this function.
     *
     * @return PHP_Reflection_AST_Function
     */
    private function _parseFunction($line)
    {
        $tokens = array();

        // Skip comment tokens before function name or reference operator
        $this->_consumeComments($tokens);

        // Check for reference operator
        if ($this->tokenizer->peek() === self::T_BITWISE_AND) {
            // Consume bitwise and
            $this->_consumeToken(self::T_BITWISE_AND, $tokens);
            // Skip comments after reference operator
            $this->_consumeComments($tokens);

            $returnsReference = true;
        } else {
            $returnsReference = false;
        }

        // We expect a T_STRING token for function name
        $token    = $this->_consumeToken(self::T_STRING, $tokens);
        $function = $this->builder->buildFunction($token[1], $line);

        $package = $this->_globalPackage;
        if ($this->_package !== PHP_Reflection_BuilderI::PKG_UNKNOWN) {
            $package = $this->_package;
        }
        $this->builder->buildPackage($package)->addFunction($function);

        // Skip comment tokens before function signature
        $this->_consumeComments($tokens);

        $function->addChild($this->_parseParameterList($tokens));
        $function->setSourceFile($this->tokenizer->getSourceFile());
        $function->setDocComment($this->_comment);
        $function->setReturnsReference($returnsReference);

        $this->reset();

        // FIXME: remove this
        $tmp = array();

        $block = $this->_parseBlock($tmp);
        $function->addChild($block);
        $function->setEndLine($block->getEndLine());
        $function->setTokens($tmp);

        // FIXME: remove this
        foreach ($tmp as $token) {
            $tokens[] = $token;
        }

        $this->_prepareCallable($function);

        return $function;
    }

    /**
     * Parses a class node.
     *
     * @return void
     */
    private function _parseClassDeclaration()
    {
        // Get class name
        $token = $this->_consumeToken(self::T_STRING);

        $qualifiedName = "{$this->_package}::{$token[1]}";

        $class = $this->builder->buildClass($qualifiedName, $token[2]);
        $class->setSourceFile($this->tokenizer->getSourceFile());
        $class->setLine($token[2]);
        $class->setModifiers($this->_modifiers);
        $class->setDocComment($this->_comment);
        $class->setPosition($this->_typePosition++);

        // Skip comment tokens
        $this->_consumeComments();

        // Check for parent class
        if ($this->tokenizer->peek() === self::T_EXTENDS) {
            // Skip extends token
            $this->tokenizer->next();
            // Parse parent class name
            $qualifiedName = $this->_parseStaticQualifiedIdentifier();
            // Set  parent class
            $class->setParentClass($this->builder->buildClassProxy($qualifiedName));

            // Skip comment tokens
            $this->_consumeComments();
        }

        // Check for parent interfaces
        if ($this->tokenizer->peek() === self::T_IMPLEMENTS) {
            // Skip 'implements' token
            $this->tokenizer->next();
            // Parse interface list
            foreach ($this->_parseInterfaceList() as $interface) {
                $class->addImplementedInterface($interface);
            }

            // Skip comment tokens
            $this->_consumeComments();
        }

        $this->builder->buildPackage($this->_package)->addType($class);

        $this->reset();

        $this->_classOrInterface = $class;
        $this->_parseClassDeclarationBody();
        $this->_classOrInterface = null;
    }

    /**
     * Parses a class body.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return void
     */
    private function _parseClassDeclarationBody(array &$tokens = array())
    {
        $this->_consumeToken(self::T_CURLY_BRACE_OPEN, $tokens);

        // Method position within the type body
        $this->_methodPosition = 0;

        while ($this->tokenizer->peek() !== self::T_EOF) {

            switch ($this->tokenizer->peek()) {
            case self::T_FUNCTION:
                $method = $this->_parseMethodDeclaration($tokens);
                $this->_classOrInterface->addMethod($method);
                break;

            case self::T_VARIABLE:
                foreach ($this->_parsePropertyDeclarationList($tokens) as $prop) {
                    $this->_classOrInterface->addProperty($prop);
                }
                break;

            case self::T_CONST:
                $constants = $this->_parseConstantDeclarationList($tokens);
                foreach ($constants as $constant) {
                    $this->_classOrInterface->addConstant($constant);
                }
                break;

            case self::T_ABSTRACT:
                $this->_consumeToken(self::T_ABSTRACT, $tokens);
                $this->_modifiers |= ReflectionMethod::IS_ABSTRACT;
                break;

            case self::T_VAR:
                $this->_consumeToken(self::T_VAR, $tokens);
                $this->_modifiers |= ReflectionMethod::IS_PUBLIC;
                break;

            case self::T_PUBLIC:
                $this->_consumeToken(self::T_PUBLIC, $tokens);
                $this->_modifiers |= ReflectionMethod::IS_PUBLIC;
                break;

            case self::T_PRIVATE:
                $this->_consumeToken(self::T_PRIVATE, $tokens);
                $this->_modifiers |= ReflectionMethod::IS_PRIVATE;
                break;

            case self::T_PROTECTED:
                $this->_consumeToken(self::T_PROTECTED, $tokens);
                $this->_modifiers |= ReflectionMethod::IS_PROTECTED;
                break;

            case self::T_STATIC:
                $this->_consumeToken(self::T_STATIC, $tokens);
                $this->_modifiers |= ReflectionMethod::IS_STATIC;
                break;

            case PHP_Reflection_TokenizerI::T_FINAL:
                $this->_consumeToken(self::T_FINAL, $tokens);
                $this->_modifiers |= ReflectionMethod::IS_FINAL;
                break;

            case self::T_DOC_COMMENT:
                $token = $this->_consumeToken(self::T_DOC_COMMENT, $tokens);
                $this->_comment = $token[1];
                break;

            case self::T_CURLY_BRACE_CLOSE:
                $token = $this->_consumeToken(self::T_CURLY_BRACE_CLOSE, $tokens);

                $this->_classOrInterface->setEndLine($token[2]);
                $this->_classOrInterface->setTokens($tokens);

                $this->reset();
                return;

            default:
                $token    = $this->tokenizer->next();
                $tokens[] = $token;

                // TODO: Handle/log unused tokens
                $this->reset();
                break;
            }
        }

        $file = $this->tokenizer->getSourceFile();
        throw new PHP_Reflection_Exceptions_UnclosedBodyException($file);
    }

    /**
     * Parses an interface node.
     *
     * @return void
     */
    private function _parseInterfaceDeclaration()
    {
        // Get interface name
        $token = $this->_consumeToken(self::T_STRING);

        $qualifiedName = "{$this->_package}::{$token[1]}";

        $interface = $this->builder->buildInterface($qualifiedName, $token[2]);
        $interface->setSourceFile($this->tokenizer->getSourceFile());
        $interface->setLine($token[2]);
        $interface->setDocComment($this->_comment);
        $interface->setPosition($this->_typePosition++);

        // Skip comment tokens
        $this->_consumeComments();

        // Check for parent interfaces
        if ($this->tokenizer->peek() === self::T_EXTENDS) {
            // Skip 'extends' token
            $this->tokenizer->next();
            // Parse interface list
            foreach ($this->_parseInterfaceList() as $parent) {
                $interface->addParentInterface($parent);
            }
        }

        $this->builder->buildPackage($this->_package)->addType($interface);

        $this->reset();

        $this->_classOrInterface = $interface;
        $this->_parseInterfaceDeclarationBody();
        $this->_classOrInterface = null;
    }

    /**
     * Parses a interface body.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return void
     */
    private function _parseInterfaceDeclarationBody(array &$tokens = array())
    {
        $this->_consumeToken(self::T_CURLY_BRACE_OPEN, $tokens);

        while ($this->tokenizer->peek() !== self::T_EOF) {

            switch ($this->tokenizer->peek()) {
            case self::T_FUNCTION:
                // We know all interface methods are abstract and public
                $this->_modifiers |= PHP_Reflection_AST_MethodI::IS_ABSTRACT;
                $this->_modifiers |= PHP_Reflection_AST_MethodI::IS_PUBLIC;

                $method = $this->_parseMethodDeclaration($tokens);
                $this->_classOrInterface->addMethod($method);

                // Reset internal state
                $this->reset();
                break;

            case self::T_CONST:
                $constants = $this->_parseConstantDeclarationList($tokens);
                foreach ($constants as $constant) {
                    $this->_classOrInterface->addConstant($constant);
                }
                break;

            case self::T_CURLY_BRACE_CLOSE:
                // Consume <}> token
                $token = $this->_consumeToken(self::T_CURLY_BRACE_CLOSE, $tokens);
                $this->reset();

                // Check for end of declaration
                $this->_classOrInterface->setEndLine($token[2]);
                $this->_classOrInterface->setTokens($tokens);
                break 2;

            case self::T_PUBLIC:
                // Consume <public> token
                $this->_consumeToken(self::T_PUBLIC);

                $this->_modifiers |= PHP_Reflection_AST_MethodI::IS_PUBLIC;
                break;

            case self::T_STATIC:
                // Consume <static> token
                $this->_consumeToken(self::T_STATIC);

                $this->_modifiers |= PHP_Reflection_AST_MethodI::IS_STATIC;
                break;

            case self::T_DOC_COMMENT:
                // Consume doc comment
                $token = $this->_consumeToken(self::T_DOC_COMMENT, $tokens);

                $this->_comment = $token[1];
                break;

            case self::T_COMMENT:
                // Consume doc comment
                $this->_consumeToken(self::T_COMMENT, $tokens);
                break;

            default:
                // Throw exception
                $this->_throwUnexpectedToken($token);
                break;
            }
        }
    }

    /**
     * Parses a list of class/interface constant declarations.
     *
     * <code>
     * class PHP_Reflection {
     *     const C_HELLO = 1, C_WORLD = 2;
     * }
     * </code>
     *
     * @param array &$tokens List of parsed tokens.
     *
     * @return array(PHP_Reflection_AST_ClassOrInterfaceConstant)
     */
    private function _parseConstantDeclarationList(array &$tokens)
    {
        $this->_consumeToken(self::T_CONST, $tokens);

        $constants = array();
        while ($this->tokenizer->peek() !== self::T_EOF) {
            $constants[] = $this->_parseConstantDeclaration($tokens);

            $this->_consumeComments($tokens);
            if ($this->tokenizer->peek() !== self::T_COMMA) {
                break;
            }
            $this->_consumeToken(self::T_COMMA, $tokens);
        }
        $this->_consumeToken(self::T_SEMICOLON, $tokens);
        $this->reset();

        return $constants;
    }

    /**
     * Parses a class or interface constant declaration.
     *
     * <code>
     * interface PHP_Reflection_Tokens {
     *     const T_PUBLIC = 42;
     * }
     * </code>
     *
     * @param array &$tokens List of parsed tokens.
     *
     * @return PHP_Reflection_AST_ClassOrInterfaceConstant
     */
    private function _parseConstantDeclaration(array &$tokens)
    {

        $token = $this->_consumeToken(self::T_STRING, $tokens);

        $constant = $this->builder->buildClassOrInterfaceConstant($token[1]);
        $constant->setDocComment($this->_comment);
        $constant->setLine($token[2]);
        $constant->setEndLine($token[2]);

        $this->_consumeToken(self::T_EQUAL, $tokens);
        $this->_consumeComments($tokens);

        // Parse static scalar value
        $constant->setValue($this->_parseStaticScalarValue($tokens));

        return $constant;
    }

    /**
     * Parses a single class property declaration:
     *
     * <code>
     * class PHP_Reflection_Parser {
     *     private $_package = 'php::reflection';
     * }
     * </code>
     *
     * or a list of class properties:
     *
     * <code>
     * class PHP_Reflection_Parser {
     *     private $_package = null,
     *             $_class = null,
     *             $_interface = null;
     * }
     * </code>
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return array(PHP_Reflection_AST_Property)
     */
    private function _parsePropertyDeclarationList(array &$tokens)
    {
        $properties = array($this->_parsePropertyDeclaration($tokens));

        while ($this->tokenizer->peek() === self::T_COMMA) {
            $this->_consumeToken(self::T_COMMA, $tokens);
            $this->_consumeComments($tokens);

            $properties[] = $this->_parsePropertyDeclaration($tokens);

            $this->_consumeComments($tokens);
        }

        $this->_consumeToken(self::T_SEMICOLON, $tokens);
        $this->reset();

        return $properties;
    }

    /**
     * Parses a class property declaration.
     *
     * <code>
     * class PHP_Reflection_Parser {
     *     private $_package = 'php::reflection';
     * }
     * </code>
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_Property
     */
    private function _parsePropertyDeclaration(array &$tokens)
    {
        // Get property identifier
        $token = $this->_consumeToken(self::T_VARIABLE);

        $name = substr($token[1], 1);
        $line = $token[2];

        $property = $this->builder->buildProperty($name, $line);
        $property->setDocComment($this->_comment);
        $property->setModifiers($this->_modifiers);
        $property->setEndLine($token[2]);

        $this->_prepareProperty($property);

        $this->_consumeComments($tokens);

        // Check for an equal sign
        if ($this->tokenizer->peek() === self::T_EQUAL) {
            $this->_consumeToken(self::T_EQUAL, $tokens);
            $this->_consumeComments($tokens);

            $property->setValue($this->_parseStaticValue($tokens));

            $this->_consumeComments($tokens);
        }

        return $property;
    }

    /**
     * Parses a class or interface method declaration.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_Method
     */
    private function _parseMethodDeclaration(array &$tokens)
    {
        $this->_consumeToken(self::T_FUNCTION, $tokens);
        $this->_consumeComments($tokens);

        $token    = $this->tokenizer->next();
        $tokens[] = $token;

        // Check for reference return
        if ($token[0] === self::T_BITWISE_AND) {
            $this->_consumeComments($tokens);
            $token = $this->_consumeToken(self::T_STRING, $tokens);

            $returnsReference = true;
        } else {
            $returnsReference = false;
        }

        $this->_consumeComments($tokens);

        $method = $this->builder->buildMethod($token[1], $token[2]);
        $method->setReturnsReference($returnsReference);
        $method->setDocComment($this->_comment);
        $method->setPosition($this->_methodPosition++);
        $method->setModifiers($this->_modifiers);
        $method->setSourceFile($this->tokenizer->getSourceFile());

        $method->addChild($this->_parseParameterList($tokens));

        $this->reset();

        $this->_consumeComments();
        if ($this->tokenizer->peek() === self::T_CURLY_BRACE_OPEN) {
            // FIXME: remove this
            $tmp = array();

            $block = $this->_parseBlock($tmp);
            $method->addChild($block);
            $method->setEndLine($block->getEndLine());
            $method->setTokens($tmp);

            // FIXME: remove this
            foreach ($tmp as $token) {
                $tokens[] = $token;
            }
        } else {
            $token = $this->_consumeToken(self::T_SEMICOLON, $tokens);
            $method->setEndLine($token[2]);
        }

        $this->_prepareCallable($method);
        return $method;
    }

    /**
     * Parses an interface list found with in class or interface signatures.
     *
     * <code>
     * class Foo implements Interface, List, Items {}
     *
     * interface Foo extends Interface, List, Items {}
     * </code>
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return array(PHP_Reflection_AST_Interface)
     */
    private function _parseInterfaceList()
    {
        $tokens = array();

        $interfaceList = array();
        while (true) {
            // Get qualified interface name
            $identifier      = $this->_parseStaticQualifiedIdentifier();
            $interfaceList[] = $this->builder->buildInterfaceProxy($identifier);

            $this->_consumeComments($tokens);

            // Check for opening class or interface body
            if ($this->tokenizer->peek() === self::T_CURLY_BRACE_OPEN) {
                break;
            }
            $this->_consumeToken(self::T_COMMA, $tokens);
        }

        return $interfaceList;
    }

    /**
     * Parses the parameter list of a function or method.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return array(PHP_Reflection_AST_Parameter)
     */
    private function _parseParameterList(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_PARENTHESIS_OPEN, $tokens);
        $this->_consumeComments($tokens);

        $parameterPosition = 0;
        $parameterList     = $this->builder->buildParameterList($token[2]);

        while (($type = $this->tokenizer->peek()) !== self::T_EOF) {

            switch ($type) {

            // Closing parenthesis for parameter list
            case self::T_PARENTHESIS_CLOSE:
                $this->_consumeToken(self::T_PARENTHESIS_CLOSE, $tokens);
                break 2;

            // Parameter separator
            case self::T_COMMA:
                $this->_consumeToken(self::T_COMMA, $tokens);
                break;

            default:
                $parameter = $this->_parseParameter($tokens);
                $parameter->setPosition($parameterPosition++);

                $parameterList->addChild($parameter);
                break;
            }
            $this->_consumeComments($tokens);
        }

        return $parameterList;
    }

    /**
     * Parses a single parameter within a function or method signature
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_Parameter
     */
    private function _parseParameter(array &$tokens)
    {
        $this->_consumeComments($tokens);

        // Type hint class or interface
        $classOrInterface = null;

        // Check for type hint
        if ($this->tokenizer->peek() === self::T_STRING) {
            $token            = $this->_consumeToken(self::T_STRING, $tokens);
            $classOrInterface = $this->builder->buildClassOrInterfaceProxy($token[1]);
        } else if ($this->tokenizer->peek() === self::T_ARRAY) {
            $this->_consumeToken(self::T_ARRAY, $tokens);
        }

        $this->_consumeComments($tokens);

        // Check for by reference token
        if ($this->tokenizer->peek() === self::T_BITWISE_AND) {
            $this->_consumeToken(self::T_BITWISE_AND, $tokens);
            $this->_consumeComments($tokens);
        }

        // Now we expect a variable
        $token = $this->_consumeToken(self::T_VARIABLE, $tokens);

        $parameter = $this->builder->buildParameter($token[1]);
        $parameter->setLine($token[2]);
        $parameter->setEndLine($token[2]);

        if ($classOrInterface !== null) {
            $parameter->setType($classOrInterface);
        }

        $this->_consumeComments($tokens);

        // Check for default value
        if ($this->tokenizer->peek() === self::T_EQUAL) {
            $this->_consumeToken(self::T_EQUAL, $tokens);
            $this->_consumeComments($tokens);
            $parameter->setDefaultValue($this->_parseStaticValue($tokens));
        }
        return $parameter;
    }

    /**
     * Parses a full qualified class or interface name.
     *
     * <ul>
     *   <li>foo::bar::FooBar</li>
     *   <li>::FooBar</li>
     *   <li>FooBar</li>
     * </ul>
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return string
     */
    private function _parseStaticQualifiedIdentifier(array &$tokens = array())
    {
        $expectedTokens = array(
            PHP_Reflection_TokenizerI::T_DOUBLE_COLON,
            PHP_Reflection_TokenizerI::T_STRING
        );

        // Skip comment tokens
        $this->_consumeComments($tokens);

        // Stack of matching tokens
        $tokenStack = array();

        $tokenID = $this->tokenizer->peek();
        while (in_array($tokenID, $expectedTokens) === true) {
            // Fetch next token
            $token    = $this->tokenizer->next();
            $tokens[] = $token;

            // Check for invalid syntax like '::' + '::'
            if (end($tokenStack) === $token[1]) {
                $this->_throwUnexpectedToken($token);
            }
            // Store token value
            $tokenStack[] = $token[1];

            $this->_consumeComments($tokens);

            $tokenID = $this->tokenizer->peek();
        }


        if (count($tokenStack) === 0) {
            $file = $this->tokenizer->getSourceFile();
            throw new PHP_Reflection_Exceptions_IdentifierExpectedException($file);
        }

        return implode('', $tokenStack);
    }

    /**
     * This method parses a static scalar expression, like it is allowed for
     * class and interface constants or as default property value.
     *
     * <code>
     * class PHP_Reflection {
     *     private $_its = array(42, 23.0);
     *     const T_MA = 23;
     *     const T_NU = 42.0;
     *     const T_EL = 'Hello World';
     * }
     * interface PHP_Reflection_VisitorI {
     *     const T_PI = PHP_Reflection::T_CO;
     *     const T_CH = true;
     *     const T_LE = PATH_SEPARATOR;
     *     const T_R  = null;
     * }
     * </code>
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_StaticScalarValueI
     */
    private function _parseStaticScalarValue(array &$tokens)
    {
        switch ($this->tokenizer->peek()) {
        // Simple scalar types
        case self::T_NULL:
            // Get current token
            $this->_consumeToken(self::T_NULL, $tokens);
            return $this->builder->buildNullValue();

        case self::T_TRUE:
            // Get current token
            $this->_consumeToken(self::T_TRUE, $tokens);
            return $this->builder->buildTrueValue();

       case self::T_FALSE:
            // Get current token
            $this->_consumeToken(self::T_FALSE, $tokens);
            return $this->builder->buildFalseValue();

        case self::T_CONSTANT_ENCAPSED_STRING:
            // Get current token
            $token = $this->_consumeToken(self::T_CONSTANT_ENCAPSED_STRING, $tokens);

            // Get scalar type
            $type = self::$_scalarTypeMap[$token[0]];
            // Create scalar value
            return $this->builder->buildScalarValue($type, $token[1]);

        case self::T_DIR:
        case self::T_STRING:
        case self::T_FILE:
        case self::T_LINE:
        case self::T_NS_C:
        case self::T_FUNC_C:
        case self::T_CLASS_C:
        case self::T_METHOD_C:
            if ($this->tokenizer->peek(1) !== self::T_DOUBLE_COLON) {
                // Get current token
                $token    = $this->tokenizer->next();
                $tokens[] = $token;

                return $this->builder->buildConstantValue($token[1]);
            }

        case self::T_DOUBLE_COLON:
            $identifier = $this->_parseStaticQualifiedIdentifier($tokens);
            $className  = substr($identifier, 0, strrpos($identifier, '::'));
            $constName  = substr($identifier, strrpos($identifier, '::') + 2);

            $proxy = $this->builder->buildClassOrInterfaceProxy($className);
            return $this->builder->buildClassOrInterfaceConstantValue($proxy, $constName);

        case self::T_SELF:
            // Consume self and following double colon
            $this->_consumeToken(self::T_SELF, $tokens);
            $this->_consumeToken(self::T_DOUBLE_COLON, $tokens);

            // Last token must be string
            $token    = $this->tokenizer->next();
            $tokens[] = $token;

            // Check last token
            if ($token[0] !== self::T_STRING) {
                $this->_throwUnexpectedToken($token);
            }

            return $this->builder->buildClassOrInterfaceConstantValue($this->_classOrInterface, $token[1]);

        default:
            return $this->_parseNumericValue($tokens);
        }

        $this->_throwUnexpectedToken();
    }

    /**
     * This method parses a static value expression, like it is allowed as a
     * class property initializer.
     *
     * <code>
     * class PHP_Reflection {
     *     private $_hello = array(
     *         array(17, 23, 42)
     *     );
     *     protected $world = 'foo';
     *     public $bar = 3.14;
     * }
     * </code>
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_MemberNumericValue
     */
    private function _parseStaticValue(array &$tokens)
    {
        if ($this->tokenizer->peek() === self::T_ARRAY) {
            return $this->_parseStaticArray($tokens);
        }
        return $this->_parseStaticScalarValue($tokens);
    }

    /**
     * Parses php arrays that are allowed as class property initializers, which
     * means all arrays with static key and value values.
     *
     * <code>
     * class PHP_Reflection {
     *     private $_tokens = array('Hello', 'World');
     *     protected $values = array(23 => 'Hello', 42 => array('World'));
     *     public $results = array(self::T_TEST,);
     * }
     * </code>
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_ArrayExpression
     */
    private function _parseStaticArray(array &$tokens)
    {
        $this->_consumeToken(self::T_ARRAY, $tokens);
        $this->_consumeToken(self::T_PARENTHESIS_OPEN, $tokens);
        $this->_consumeComments($tokens);

        // Create an array instance
        $array  = $this->builder->buildArrayExpression();

        while(true) {
            // Stop for open parenthesis
            if ($this->tokenizer->peek() === self::T_PARENTHESIS_CLOSE) {
                break;
            }

            // Parse static key or value
            $keyOrValue = $this->_parseStaticValue($tokens);
            $this->_consumeComments($tokens);

            // Create an array element
            $element = $this->builder->buildArrayElement();

            // Check for double arrow token
            if ($this->tokenizer->peek() === self::T_DOUBLE_ARROW) {
                $element->setKey($keyOrValue);

                $this->_consumeToken(self::T_DOUBLE_ARROW, $tokens);
                $this->_consumeComments($tokens);

                $element->setValue($this->_parseStaticValue($tokens));
                $this->_consumeComments($tokens);
            } else {
                $element->setValue($keyOrValue);
            }

            $array->addElement($element);

            // Skip if no comma follows
            if ($this->tokenizer->peek() !== self::T_COMMA) {
                break;
            }
            // Consume comma token
            $this->_consumeToken(self::T_COMMA, $tokens);
            // Skip all comment tokens after comma
            $this->_consumeComments($tokens);
        }
        $this->_consumeToken(self::T_PARENTHESIS_CLOSE, $tokens);

        return $array;
    }

    /**
     * This method parses a numeric value expression, like it is allowed for
     * class and interface constants or as default property value.
     *
     * <code>
     * class PHP_Reflection {
     *     private $_its = array(42, 23.0);
     *     const T_MA = 23;
     *     const T_NU = 42.0;
     * }
     * interface PHP_Reflection_VisitorI {
     *     const T_EL = 0x23;
     * }
     * </code>
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_MemberNumericValue
     */
    private function _parseNumericValue(array &$tokens)
    {
        $negative = false;

        // Remove comments
        $this->_consumeComments($tokens);

        while ($this->tokenizer->peek() !== self::T_EOF) {
            switch ($this->tokenizer->peek()) {
            case self::T_MINUS:
                $this->_consumeToken(self::T_MINUS, $tokens);
                $negative = !$negative;
                break;

            case self::T_PLUS:
                $this->_consumeToken(self::T_PLUS, $tokens);
                break;

            case self::T_DNUMBER:
            case self::T_LNUMBER:
            case self::T_NUM_STRING:
                // Get current token
                $token    = $this->tokenizer->next();
                $tokens[] = $token;

                // Get scalar type
                $type  = self::$_scalarTypeMap[$token[0]];
                $value = $token[1];

                // Create scalar value
                return $this->builder->buildNumericValue($type, $value, $negative);

            case self::T_COMMENT:
            case self::T_DOC_COMMENT:
                $this->_consumeComments($tokens);
                break;

            default:
                break 2;
            }
        }

        $this->_throwUnexpectedToken();
    }

    /**
     * Parses an exception catch statement.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_CatchStatement
     */
    private function _parseCatchStatement(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_CATCH, $tokens);
        $this->_consumeToken(self::T_PARENTHESIS_OPEN, $tokens);

        $identifier = $this->_parseStaticQualifiedIdentifier($tokens);

        $this->_consumeToken(self::T_VARIABLE, $tokens);
        $this->_consumeToken(self::T_PARENTHESIS_CLOSE);

        $exception = $this->builder->buildClassOrInterfaceProxy($identifier);
        $statement = $this->builder->buildCatchStatement($token[2]);
        $statement->addChild($exception);

        $this->_parseBlock($tokens);

        return $statement;
    }

    /**
     * This method will parse a single block encapsulated by '{' and '}' or a
     * single statement.
     *
     * @param array $tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_SourceElementI
     */
    private function _parseBlockOrStatement(array &$tokens)
    {
        $this->_consumeComments($tokens);
        if ($this->tokenizer->peek() === self::T_CURLY_BRACE_OPEN) {
            return $this->_parseBlock($tokens);
        }
        return $this->_parseStatement($tokens);
    }

    /**
     * This method parses a single code block encapsulated by '{' and '}'
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_Block
     */
    private function _parseBlock(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_CURLY_BRACE_OPEN, $tokens);
        $block = $this->builder->buildBlock($token[2]);

        while ($this->tokenizer->peek() !== self::T_EOF) {

            switch ($this->tokenizer->peek()) {

            case self::T_FUNCTION:
                $block->addChild($this->_parseFunctionOrClosure($tokens));
                break;

            case self::T_CURLY_BRACE_OPEN:
                $block->addChild($this->_parseBlock($tokens));
                break;

            case self::T_CATCH:
                $block->addChild($this->_parseCatchStatement($tokens));
                break;

            case self::T_NEW:
                $block->addChild($this->_parseNewExpression($tokens));
                break;

            case self::T_INSTANCEOF:
                $block->addChild($this->_parseInstanceOfExpression($tokens));
                break;

            case self::T_STRING:
                // TODO: ns + class, ns + function, constant
                $token = $this->_consumeToken(self::T_STRING, $tokens);
                $this->_consumeComments($tokens);
                if ($this->tokenizer->peek() === self::T_DOUBLE_COLON) {
                    $block->addChild($this->builder->buildClassProxy($token[1]));
                }
                break;

            case self::T_CURLY_BRACE_CLOSE:
                break 2;

            case self::T_DOUBLE_QUOTE:
                $token = $this->_consumeToken(self::T_DOUBLE_QUOTE);
                $this->_skipEncapsultedBlock($tokens, $token[0]);
                break;

            case self::T_BACKTICK:
                $token = $this->_consumeToken(self::T_BACKTICK);
                $this->_skipEncapsultedBlock($tokens, $token[0]);
                break;

            default:
// FIXME - Stupid workaround: {{{
if (isset($GLOBALS['argv']) && in_array('--filter', $GLOBALS['argv'])) {
                $blockOrStmt = $this->_parseBlockOrStatement($tokens);
                if ($blockOrStmt !== null) {
                    $block->addChild($blockOrStmt);
                }
} else {
                $tokens[] = $this->tokenizer->next();
                $this->_consumeComments($tokens);
}
// FIXME - Stupid workaround: }}}
                break;
            }
        }

        $token = $this->_consumeToken(self::T_CURLY_BRACE_CLOSE, $tokens);
        $block->setEndLine($token[2]);

        return $block;
    }

    /**
     * This method will parse a single statement.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_SourceElementI
     */
    private function _parseStatement(array &$tokens)
    {
        $this->_consumeComments($tokens);

        while (($type = $this->tokenizer->peek()) !== self::T_EOF) {

            switch ($type) {

            case self::T_CATCH:
                return $this->_parseCatchStatement($tokens);

            case self::T_DO:
                return $this->_parseDoStatement($tokens);

            case self::T_FOR:
                return $this->_parseForStatement($tokens);

            case self::T_FOREACH:
                // FIXME: Implement foreach parsing
                break;

            case self::T_IF:
                return $this->_parseIfStatement($tokens);

            case self::T_WHILE:
                return $this->_parseWhileStatement($tokens);

            case self::T_SEMICOLON:
                $token = $this->_consumeToken(self::T_SEMICOLON, $tokens);
                $stmt  = $this->builder->buildBlockStatement($token[2]);
                $stmt->setEndLine($token[2]);
                return $stmt;

            default:
                return $this->_parseBlockStatement($tokens);
            }
        }
    }

    /**
     * Parses a single block statement.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_BlockStatementI
     */
    private function _parseBlockStatement(array &$tokens)
    {
        // FIXME: T_SEMICOLON or T_CLOSE_TAG
        $expr  = $this->_parseExpressionOrEmpty($tokens);
        $token = $this->_consumeToken(self::T_SEMICOLON, $tokens);

        if ($expr === null) {
            $stmt = $this->builder->buildBlockStatement($token[2]);
        } else {
            $stmt = $this->builder->buildBlockStatement($expr->getLine());
            $stmt->addChild($expr);
        }
        $stmt->setEndLine($token[2]);

        return $stmt;
    }

    /**
     * Parses a comma separated list of expressions as used in a for statement.
     * An expression list can contain zero or more expression nodes.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return array(PHP_Reflection_AST_ExpressionI)
     */
    private function _parseExpressionList(array &$tokens)
    {
        // TODO: Implement expression list
        $expressionList = array();
        if (($expr = $this->_parseExpressionOrEmpty($tokens)) === null) {
            return $expressionList;
        }

        $this->_consumeComments($tokens);

        $expressionList[] = $expr;
        while ($this->tokenizer->peek() !== self::T_EOF) {
            if ($this->tokenizer->peek() !== self::T_COMMA) {
                break;
            }
            $this->_consumeToken(self::T_COMMA);

            $expressionList[] = $this->_parseExpression($tokens);
        }

        return $expressionList;
    }

    /**
     * Parses a single expression node.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_ExpressionI
     */
    private function _parseExpression(array &$tokens)
    {
        if (($expression = $this->_parseExpressionOrEmpty($tokens)) === null) {
            $this->_throwUnexpectedToken();
        }
        return $expression;
    }

    private $_parseStacks = array();

    private $_reductions = array(
        PHP_Reflection_AST_LiteralI::NODE_NAME  =>  array(
            '_reduceUnaryExpr',
        ),
        PHP_Reflection_AST_AdditiveExpressionI::NODE_NAME  =>  array(
            '_reduceOperativeExpr',
            '_reduceBinaryExpr'
        ),
        PHP_Reflection_AST_MultiplicativeExpressionI::NODE_NAME  =>  array(
            '_reduceMultiplicativeExpr',
            '_reduceOperativeExpr',
            '_reduceBinaryExpr'
        ),
    );

    /**
     * Parses a single expression node or an empty statement.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_ExpressionI|null
     */
    private function _parseExpressionOrEmpty(array &$tokens)
    {
        $this->_consumeComments($tokens);

        // Create a new expression stack
        array_unshift($this->_parseStacks, array());

        while ($this->tokenizer->peek() !== self::T_EOF) {
            switch ($this->tokenizer->peek()) {

                case self::T_VARIABLE:
                    $expr = $this->_parseVariableExpression($tokens);
                    break;

                case self::T_LNUMBER:
                    $expr = $this->_parseIntegerLiteral($tokens);
                    break;

                case self::T_DNUMBER:
                    $expr = $this->_parseFloatLiteral($tokens);
                    break;

                case self::T_STAR:
                case self::T_SLASH:
                    $expr = $this->_parseMultiplicativeExpression($tokens);
                    break;

                case self::T_PLUS:
                case self::T_MINUS:
                    $expr = $this->_parseAdditiveOrPrefixExpression($tokens);
                    break;

                case self::T_SEMICOLON:
                    break 2;

                default:
                    $this->_throwUnexpectedToken();
            }

            $this->shift($expr);
        }

        $expr = $this->reduce();
        if (count($stack = array_shift($this->_parseStacks)) > 0) {
            throw new RuntimeException('Empty stack expected.');
        }
        return $expr;

/*

FIXME: REMOVE THIS BROKEN EXPRESSION CODE
        $this->_consumeComments($tokens);

        $tokenCount = 0;
        while (($type = $this->tokenizer->peek()) !== self::T_EOF) {
            switch ($type) {

            case self::T_PARENTHESIS_OPEN:
                $this->_consumeToken(self::T_PARENTHESIS_OPEN, $tokens);
                $this->_shift($this->_parseExpression($tokens));
                $this->_consumeToken(self::T_PARENTHESIS_CLOSE, $tokens);

                return $this->_reduce();

            case self::T_PARENTHESIS_CLOSE:
            case self::T_SEMICOLON:
            case self::T_COLON:
            case self::T_COMMA:
            case self::T_AS:
                if ($tokenCount > 0) {
                    return new PHP_Reflection_AST_StubExpressionExpression(0);
                }
                return null;

            case self::T_CONSTANT_ENCAPSED_STRING:
                $this->_shift($this->_parseStringLiteral($tokens));
                if (($expr = $this->_parseExpressionOrEmpty($tokens)) !== null) {
                    return $expr;
                }
                return $this->_reduce();

            case self::T_DNUMBER:
                $this->_shift($this->_parseFloatLiteral($tokens));
                if (($expr = $this->_parseExpressionOrEmpty($tokens)) !== null) {
                    return $expr;
                }
                return $this->_reduce();

            case self::T_LNUMBER:
                $this->_shift($this->_parseIntegerLiteral($tokens));
                if (($expr = $this->_parseExpressionOrEmpty($tokens)) !== null) {
                    return $expr;
                }
                return $this->_reduce();

            case self::T_TRUE:
            case self::T_FALSE:
                $this->_shift($this->_parseBooleanLiteral($tokens));
                if (($expr = $this->_parseExpressionOrEmpty($tokens)) !== null) {
                    return $expr;
                }
                return $this->_reduce();

            case self::T_NULL:
                $this->_shift($this->_parseNullLiteral($tokens));
                if (($expr = $this->_parseExpressionOrEmpty($tokens)) !== null) {
                    return $expr;
                }
                return $this->_reduce();


            case self::T_VARIABLE:
                $this->_shift($this->_parseVariableExpression($tokens));
                if (($expr = $this->_parseExpressionOrEmpty($tokens)) !== null) {
                    return $expr;
                }
                return $this->_reduce();

            case self::T_LOGICAL_AND:
                $this->_shift($this->_parseLogicalAndExpression($tokens));
                if (($expr = $this->_parseExpressionOrEmpty($tokens)) !== null) {
                    return $expr;
                }
                return $this->_reduce();

            case self::T_LOGICAL_OR:
                $this->_shift($this->_parseLogicalOrExpression($tokens));
                if (($expr = $this->_parseExpressionOrEmpty($tokens)) !== null) {
                    return $expr;
                }
                return $this->_reduce();


            case self::T_LOGICAL_XOR:
                $this->_shift($this->_parseLogicalXorExpression($tokens));
                if (($expr = $this->_parseExpressionOrEmpty($tokens)) !== null) {
                    return $expr;
                }
                return $this->_reduce();

            case self::T_BOOLEAN_AND:
                $this->_shift($this->_parseBooleanAndExpression($tokens));
                if (($expr = $this->_parseExpressionOrEmpty($tokens)) !== null) {
                    return $expr;
                }
                return $this->_reduce();

            case self::T_BOOLEAN_OR:
                $this->_shift($this->_parseBooleanOrExpression($tokens));
                if (($expr = $this->_parseExpressionOrEmpty($tokens)) !== null) {
                    return $expr;
                }
                return $this->_reduce();

            case self::T_QUESTION_MARK:
                return $this->_parseConditionalExpression($tokens);

            default:
                $this->_consumeToken($type, $tokens);
                $this->_consumeComments($tokens);
                ++$tokenCount;
            }
        }
        $this->_throwUnexpectedToken();
*/
    }

    /**
     * This method parses a single plug token.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_AbstractSourceElement
     */
    private function _parseAdditiveOrPrefixExpression(array &$tokens)
    {
        // First fetch the current token
        if ($this->tokenizer->peek() === self::T_PLUS) {
            $token = $this->_consumeToken(self::T_PLUS, $tokens);
        } else {
            $token = $this->_consumeToken(self::T_MINUS, $tokens);
        }

        // Check for signed
        if ($this->first() === null
         || $this->first() instanceof PHP_Reflection_AST_BinaryExpressionI
         || $this->first() instanceof PHP_Reflection_AST_PrefixExpressionI) {

             return $this->builder->buildPrefixExpression($token[2], $token[1]);
        }
        return $this->builder->buildAdditiveExpression($token[2], $token[1]);
    }

    /**
     * Parses a multiplicative expression node.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_MultiplicativeExpression
     */
    private function _parseMultiplicativeExpression(array &$tokens)
    {
        if ($this->tokenizer->peek() === self::T_STAR) {
            $token = $this->_consumeToken(self::T_STAR, $tokens);
        } else if ($this->tokenizer->peek() === self::T_SLASH) {
            $token = $this->_consumeToken(self::T_SLASH, $tokens);
        } else {
            $token = $this->_consumeToken(self::T_MOD, $tokens);
        }

        return $this->builder->buildMultiplicativeExpression($token[2], $token[1]);
    }

    private function _reduceMultiplicativeExpr(PHP_Reflection_AST_MultiplicativeExpression $expr)
    {
        $left = $this->reduce();
        if ($left instanceof PHP_Reflection_AST_AdditiveExpression) {
            $expr->setLeft($left->getRight());
            $left->setRight($expr);

            return $left;
        }
        $this->shift($left);
        return null;
    }

    private function _reduceOperativeExpr(PHP_Reflection_AST_AbstractBinaryExpression $expr)
    {
        $left = $this->reduce();
        if ($left instanceof LogicalExpr) {
            $expr->left  = $left->right;
            $left->right = $expr;

            return $left;
        }
        $this->shift($left);
        return null;
    }

    private function _reduceBinaryExpr(PHP_Reflection_AST_AbstractBinaryExpression $expr)
    {
        if ($expr->getLeft() === null) {
            $expr->setLeft($this->reduce());
        }
        return $expr;
    }

    private function _reduceUnaryExpr(PHP_Reflection_AST_NodeI $expr)
    {
        if ($this->first() instanceof PHP_Reflection_AST_AbstractBinaryExpression) {
            $this->first()->setRight($expr);
        //} else if ($this->first() instanceof UnaryExpr) {
        //    $this->first()->node = $expr;
        } else {
            return $expr;
        }
        return null;
    }

    /**
     * Shifts the given source element onto the current parse stack.
     *
     * @param PHP_Reflection_AST_AbstractSourceElement $element The source element.
     *
     * @return void
     */
    protected function shift(PHP_Reflection_AST_AbstractSourceElement $element)
    {
echo 'shift(', $element->getName(), ");\n";
        array_unshift($this->_parseStacks[0], $element);
    }

    /**
     * This method reduces the current parse stack and it returns the
     * reduced source element.
     *
     * @return PHP_Reflection_AST_AbstractSourceElement
     */
    protected function reduce()
    {
        /* @var $elem PHP_Reflection_AST_AbstractSourceElement */
        $elem = array_shift($this->_parseStacks[0]);

        while ($elem !== null && isset($this->_reductions[$elem->getName()])) {
            $reductions = $this->_reductions[$elem->getName()];
echo 'reduce(', $elem->getName(), ");\n";
            foreach ($reductions as $reduction) {
                // Create callback
                $callback = array($this, $reduction);
                $argument = array($elem);

                if (is_callable($callback) === false) {
                    $message = sprintf('Missing method Parser::%s()', $reduction);
                    throw new ErrorException($message);
                }

                $reduced = call_user_func_array($callback, $argument);
                if ($reduced === null) {
                    continue;
                }
                $elem = $reduced;
                break 2;
            }
            $elem = array_shift($this->_parseStacks[0]);
        }
        return $elem;
    }

    /**
     * This method will return the first element of the current parse stack.
     *
     * @return PHP_Reflection_AST_AbstractSourceElement
     */
    protected function first()
    {
        return (($first = reset($this->_parseStacks[0])) === false ? null : $first);
    }

    /**
     * This method parses a <b>for</b>-loop statement.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_ForInitI
     */
    private function _parseForStatement(array &$tokens)
    {
        $token   = $this->_consumeToken(self::T_FOR, $tokens);
        $forStmt = $this->builder->buildForStatement($token[2]);

        $this->_consumeToken(self::T_PARENTHESIS_OPEN, $tokens);

        $forStmt->addChild($this->_parseForInit($tokens));
        $forStmt->addChild($this->_parseForCondition($tokens));
        $forStmt->addChild($this->_parseForUpdate($tokens));

        $this->_consumeToken(self::T_PARENTHESIS_CLOSE, $tokens);

        $blockOrStmt = $this->_parseBlockOrStatement($tokens);

        $forStmt->addChild($blockOrStmt);
        $forStmt->setEndLine($blockOrStmt->getEndLine());

        return $forStmt;
    }

    /**
     * Parses the init section of a <b>for</b>-loop.
     *
     * @param array $tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_ForInitI
     */
    private function _parseForInit(array &$tokens)
    {
        $token = $this->tokenizer->token();
        $init  = $this->builder->buildForInit($token[2]);

        $expressionList = $this->_parseExpressionList($tokens);
        foreach ($expressionList as $expression) {
            $init->addChild($expression);
        }

        $token = $this->_consumeToken(self::T_SEMICOLON, $tokens);
        $init->setEndLine($token[2]);

        return $init;
    }

    /**
     * Parses the condition section of a <b>for</b>-loop.
     *
     * @param array $tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_ForConditionI
     */
    private function _parseForCondition(array &$tokens)
    {
        $token     = $this->tokenizer->token();
        $condition = $this->builder->buildForCondition($token[2]);

        $expressionList = $this->_parseExpressionList($tokens);
        foreach ($expressionList as $expression) {
            $condition->addChild($expression);
        }

        $token = $this->_consumeToken(self::T_SEMICOLON, $tokens);
        $condition->setEndLine($token[2]);

        return $condition;
    }

    /**
     * Parses the update expressions of a <b>for</b>-loop statement.
     *
     * @param array $tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_ForUpdateI
     */
    private function _parseForUpdate(array &$tokens)
    {
        $token  = $this->tokenizer->token();
        $update = $this->builder->buildForUpdate($token[2]);

        $expressionList = $this->_parseExpressionList($tokens);
        foreach ($expressionList as $expression) {
            $update->addChild($expression);
        }

        $token = $this->tokenizer->peek();
        $update->setEndLine($token[2]);

        return $update;
    }

    /**
     * This method parses a <b>while</b>-loop statement.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_WhileStatementI
     */
    private function _parseWhileStatement(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_WHILE, $tokens);
        $while = $this->builder->buildWhileStatement($token[2]);

        $this->_consumeToken(self::T_PARENTHESIS_OPEN, $tokens);
        $while->addChild($this->_parseExpression($tokens));
        $this->_consumeToken(self::T_PARENTHESIS_CLOSE, $tokens);

        $blockOrStmt = $this->_parseBlockOrStatement($tokens);
        $while->addChild($blockOrStmt);
        $while->setEndLine($blockOrStmt->getEndLine());

        return $while;
    }

    /**
     * This method parses a <b>do while</b>-loop statement.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_DoStatementI
     */
    private function _parseDoStatement(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_DO, $tokens);
        $do    = $this->builder->buildDoStatement($token[2]);

        $do->addChild($this->_parseBlockOrStatement($tokens));

        $this->_consumeToken(self::T_WHILE, $tokens);
        $this->_consumeToken(self::T_PARENTHESIS_OPEN, $tokens);
        $do->addChild($this->_parseExpression($tokens));
        $this->_consumeToken(self::T_PARENTHESIS_CLOSE, $tokens);

        $token = $this->_consumeToken(self::T_SEMICOLON, $tokens);
        $do->setEndLine($token[2]);

        return $do;
    }

    /**
     * This method parses a <b>if</b>-statement.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_IfStatementI
     */
    private function _parseIfStatement(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_IF, $tokens);
        $if    = $this->builder->buildIfStatement($token[2]);

        $this->_consumeToken(self::T_PARENTHESIS_OPEN, $tokens);
        $if->addChild($this->_parseExpression($tokens));
        $this->_consumeToken(self::T_PARENTHESIS_CLOSE, $tokens);

        $blockOrStmt = $this->_parseBlockOrStatement($tokens);
        $if->addChild($blockOrStmt);
        $if->setEndLine($blockOrStmt->getEndLine());

        $this->_consumeComments($tokens);
        if ($this->tokenizer->peek() === self::T_ELSE) {
            $if->addChild($this->_parseElseStatement($tokens));
        } else if ($this->tokenizer->peek() === self::T_ELSEIF) {
            $if->addChild($this->_parseElseIfStatement($tokens));
        }

        return $if;
    }

    /**
     * This method parses an <b>else</b>-statement.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_ElseStatementI
     */
    private function _parseElseStatement(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_ELSE, $tokens);
        $else  = $this->builder->buildElseStatement($token[2]);

        $this->_consumeComments($tokens);
        if ($this->tokenizer->peek() === self::T_IF) {
            $if = $this->_parseIfStatement($tokens);
            $else->addChild($if);
            $else->setEndLine($if->getEndLine());
        } else {
            $blockOrStmt = $this->_parseBlockOrStatement($tokens);
            $else->addChild($blockOrStmt);
            $else->setEndLine($blockOrStmt->getEndLine());
        }

        return $else;
    }

    /**
     * This method parses an <b>elseif</b>-statement.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_ElseStatementI
     */
    private function _parseElseIfStatement(array &$tokens)
    {
        $token  = $this->_consumeToken(self::T_ELSEIF, $tokens);
        $elseIf = $this->builder->buildElseIfStatement($token[2]);

        $this->_consumeToken(self::T_PARENTHESIS_OPEN, $tokens);
        $elseIf->addChild($this->_parseExpression($tokens));
        $this->_consumeToken(self::T_PARENTHESIS_CLOSE, $tokens);

        $blockOrStmt = $this->_parseBlockOrStatement($tokens);
        $elseIf->addChild($blockOrStmt);
        $elseIf->setEndLine($blockOrStmt->getEndLine());

        $this->_consumeComments($tokens);
        if ($this->tokenizer->peek() === self::T_ELSEIF) {
            $elseIf->addChild($this->_parseElseIfStatement($tokens));
        } else if ($this->tokenizer->peek() === self::T_ELSE) {
            $elseIf->addChild($this->_parseElseStatement($tokens));
        }

        return $elseIf;
    }

    /**
     * Parses a logical <b>and</b>-expression node.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_LogicalAndExpressionI
     */
    private function _parseLogicalAndExpression(array &$tokens)
    {
        $this->_consumeToken(self::T_LOGICAL_AND, $tokens);

        $left  = $this->_reduce();
        $right = $this->_parseExpression($tokens);

        $and = $this->builder->buildLogicalAndExpression($left->getLine());
        $and->setEndLine($right->getEndLine());
        $and->addChild($left);
        $and->addChild($right);

        return $and;
    }

    /**
     * Parses a logical <b>or</b>-expression node.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_LogicalOrExpressionI
     */
    private function _parseLogicalOrExpression(array &$tokens)
    {
        $this->_consumeToken(self::T_LOGICAL_OR, $tokens);

        $left  = $this->_reduce();
        $right = $this->_parseExpression($tokens);

        $or = $this->builder->buildLogicalOrExpression($left->getLine());
        $or->setEndLine($right->getEndLine());
        $or->addChild($left);
        $or->addChild($right);

        return $or;
    }

    /**
     * Parses a logical <b>xor</b>-expression node.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_LogicalXorExpressionI
     */
    private function _parseLogicalXorExpression(array &$tokens)
    {
        $this->_consumeToken(self::T_LOGICAL_XOR, $tokens);

        $left  = $this->_reduce();
        $right = $this->_parseExpression($tokens);

        $xor = $this->builder->buildLogicalXorExpression($left->getLine());
        $xor->setEndLine($right->getEndLine());
        $xor->addChild($left);
        $xor->addChild($right);

        return $xor;
    }

    /**
     * Parses a boolean AND-expression node.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_BooleanAndExpressionI
     */
    private function _parseBooleanAndExpression(array &$tokens)
    {
        $this->_consumeToken(self::T_BOOLEAN_AND, $tokens);

        $left  = $this->_reduce();
        $right = $this->_parseExpression($tokens);

        $and = $this->builder->buildBooleanAndExpression($left->getLine());
        $and->setEndLine($right->getEndLine());
        $and->addChild($left);
        $and->addChild($right);

        return $and;
    }

    /**
     * Parses a boolean OR-expression node.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_BooleanOrExpressionI
     */
    private function _parseBooleanOrExpression(array &$tokens)
    {
        $this->_consumeToken(self::T_BOOLEAN_OR, $tokens);

        $left  = $this->_reduce();
        $right = $this->_parseExpression($tokens);

        $or = $this->builder->buildBooleanOrExpression($left->getLine());
        $or->setEndLine($right->getEndLine());
        $or->addChild($left);
        $or->addChild($right);

        return $or;
    }

    /**
     * Parses a conditional '?:' expression node.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_ConditionalExpressionI
     */
    public function _parseConditionalExpression(array &$tokens)
    {
        $this->_consumeToken(self::T_QUESTION_MARK, $tokens);
        $this->_consumeComments($tokens);

        $left = $this->_reduce();

        $conditional = $this->builder->buildConditionalExpression($left->getLine());
        $conditional->addChild($left);

        // Parse ifsetor expression
        if ($this->tokenizer->peek() === self::T_COLON) {
            $this->_consumeToken(self::T_COLON, $tokens);
            $conditional->addChild($this->_parseExpression($tokens));
        } else {
            $conditional->addChild($this->_parseExpression($tokens));
            $this->_consumeToken(self::T_COLON, $tokens);
            $conditional->addChild($this->_parseExpression($tokens));
        }

        return $conditional;
    }

    /**
     * Parses a <b>variable</b>-expression node.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_VariableExpressionI
     */
    private function _parseVariableExpression(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_VARIABLE, $tokens);
        return $this->builder->buildVariableExpression($token[1], $token[2]);
    }

    /**
     * Parses a BOOLEAN-literal node.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_BooleanLiteralI
     */
    private function _parseBooleanLiteral(array &$tokens)
    {
        $token = $this->_consumeToken($this->tokenizer->peek(), $tokens);
        $bool  = $this->builder->buildBooleanLiteral($token[2]);

        if ($token[0] === self::T_TRUE) {
            $bool->setTrue();
        } else {
            $bool->setFalse();
        }
        return $bool;
    }

    /**
     * Parses a NULL-literal node.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_NullLiteralI
     */
    private function _parseNullLiteral(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_NULL, $tokens);

        $null = $this->builder->buildNullLiteral($token[2]);
        $null->setEndLine($token[2]);

        return $null;
    }

    /**
     * Parses a STRING-literal node.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_LiteralI
     */
    private function _parseStringLiteral(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_CONSTANT_ENCAPSED_STRING, $tokens);

        $literal = $this->builder->buildLiteral($token[2]);
        $literal->setEndLine($token[2]); // TODO: Count lines
        $literal->setString();
        $literal->setData($token[1]);

        return $literal;
    }

    /**
     * Parses a FLOAT-literal node
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_LiteralI
     */
    private function _parseFloatLiteral(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_DNUMBER, $tokens);

        $literal = $this->builder->buildLiteral($token[2]);
        $literal->setEndLine($token[2]);
        $literal->setFloat();
        $literal->setData($token[1]);

        return $literal;
    }

    /**
     * Parses an INT-literal node
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_LiteralI
     */
    private function _parseIntegerLiteral(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_LNUMBER, $tokens);

        $literal = $this->builder->buildLiteral($token[2]);
        $literal->setEndLine($token[2]);
        $literal->setInt();
        $literal->setData($token[1]);

        return $literal;
    }

    /**
     * Parses a function or closure declaration.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_AbstractCallable
     */
    private function _parseFunctionOrClosure(array &$tokens = array())
    {
        $token = $this->_consumeToken(self::T_FUNCTION, $tokens);
        $this->_consumeComments($tokens);

        if ($this->tokenizer->peek() === self::T_STRING
         || $this->tokenizer->peek() === self::T_BITWISE_AND) {

            return $this->_parseFunction($token[2]);
        }
        return $this->_parseClosure($token[2], $tokens);
    }

    /**
     * Parses a closure.
     *
     * @param integer $line    The line number of this closure.
     * @param array   &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_Closure
     */
    private function _parseClosure($line, array &$tokens = array())
    {
        $closure = $this->builder->buildClosure($line);
        $closure->addChild($this->_parseParameterList($tokens));

        $this->_consumeComments($tokens);
        if ($this->tokenizer->peek() === self::T_USE) {
            $this->_consumeToken(self::T_USE, $tokens);
            $this->_parseBoundVariableList($tokens);
        }

        $tokens = array();
        $block  = $this->_parseBlock($tokens);

        $closure->addChild($block);
        $closure->setEndLine($block->getEndLine());
        $closure->setTokens($tokens);

        return $closure;
    }

    /**
     * Parses a variable bound list of a closure.
     *
     * @param array   &$tokens Reference array for parsed tokens.
     *
     * @return array(PHP_Reflection_AST_Variable)
     */
    private function _parseBoundVariableList(array &$tokens)
    {
        $this->_consumeToken(self::T_PARENTHESIS_OPEN, $tokens);
        $this->_consumeComments($tokens);

        while (($type = $this->tokenizer->peek()) !== self::T_EOF) {

            if ($type === self::T_BITWISE_AND) {
                $this->_consumeToken(self::T_BITWISE_AND, $tokens);
                $reference = true;
            } else {
                $reference = false;
            }

            $token = $this->_consumeToken(self::T_VARIABLE, $tokens);
            $this->_consumeComments($tokens);

            if ($this->tokenizer->peek() !== self::T_COMMA) {
                break;
            }
            $this->_consumeToken(self::T_COMMA, $tokens);
            $this->_consumeComments($tokens);
        }

        $this->_consumeToken(self::T_PARENTHESIS_CLOSE, $tokens);

        // TODO: Return array()
    }

    /**
     * Parses a new expression.
     *
     * <ul>
     *   <li>new clazz();</li>
     *   <li>new \clazz();</li>
     *   <li>new n\s\clazz();</li>
     *   <li>new \n\s\clazz();</li>
     *   <li>new $clazz();</li>
     *   <li>new ${clazz}();</li>
     *   <li>new self::x();</li>
     *   <li>new x::y();</li>
     *   <li>new self::$x();</li>
     *   <li>new x::$y</li>
     * </ul>
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_NewExpression
     */
    private function _parseNewExpression(array &$tokens)
    {
        $token = $this->_consumeToken(self::T_NEW, $tokens);
        $this->_consumeComments($tokens);

        $newExpr = $this->builder->buildNewExpression($token[2]);

        if ($this->tokenizer->peek() === self::T_NS_SEPARATOR
         || $this->tokenizer->peek() === self::T_STRING) {

            $identifier = $this->_parseStaticQualifiedIdentifier($tokens);
            $classProxy = $this->builder->buildClassProxy($identifier);
            $newExpr->addChild($classProxy);
        } else {
            // FIXME: all none static qualified identifiers
        }

        return $newExpr;
    }

    /**
     * This method parses an instance of expression.
     *
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_InstanceOfExpression
     */
    private function _parseInstanceOfExpression(array &$tokens)
    {
        // FIXME: reduce variable/expression

        $token = $this->_consumeToken(self::T_INSTANCEOF, $tokens);
        $this->_consumeComments($tokens);

        $instanceOf = $this->builder->buildInstanceOfExpression($token[2]);

        if ($this->tokenizer->peek() === self::T_NS_SEPARATOR
         || $this->tokenizer->peek() === self::T_STRING) {

            $identifier = $this->_parseStaticQualifiedIdentifier($tokens);
            $classProxy = $this->builder->buildClassProxy($identifier);
            $instanceOf->addChild($classProxy);
        } else {
            // FIXME: all none static qualified identifiers
        }

        return $instanceOf;
    }

    /**
     * Shifts the given source element onto the node stack.
     *
     * @param PHP_Reflection_AST_SourceElementI $element New source element.
     *
     * @return void
     */
    private function _shift(PHP_Reflection_AST_SourceElementI $element)
    {
        array_unshift($this->_elementStack, $element);
    }

    /**
     * Removes a source element of <b>$type</b> from the node stack.
     *
     * @param string $type The expected source element type.
     *
     * @return PHP_Reflection_AST_SourceElementI
     */
    private function _reduce($type = 'PHP_Reflection_AST_SourceElementI')
    {
        $elem = array_shift($this->_elementStack);
        if ($elem instanceof $type) {
            return $elem;
        }
        throw new PHP_Reflection_Exceptions_UnexpectedElementException($elem, $type);
    }

    /**
     * Expects a token of <b>$type</b>, removes this from the tokenizer stream,
     * adds it to <b>$tokens</b> and returns the token.
     *
     * @param integer $type    The expected token type.
     * @param array   &$tokens Reference array for parsed tokens.
     *
     * @return array
     */
    private function _consumeToken($type, array &$tokens = array())
    {
        // Consume leading comments
        if ($type !== self::T_COMMENT && $type !== self::T_DOC_COMMENT) {
            $this->_consumeComments($tokens);
        }

        if ($this->tokenizer->peek() === $type) {
            return $tokens[] = $this->tokenizer->next();
        }

        $this->_throwUnexpectedToken();
    }

    /**
     * Consumes an variable amount of comment tokens.
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return void
     */
    private function _consumeComments(array &$tokens = array())
    {
        $consumeTokens = array(self::T_COMMENT, self::T_DOC_COMMENT);
        while (in_array($this->tokenizer->peek(), $consumeTokens) === true) {
            $tokens[] = $this->tokenizer->next();
        }
    }

    private function _throwUnexpectedToken(array $token = null)
    {
        $file  = $this->tokenizer->getSourceFile();
        $token = ($token === null ? $this->tokenizer->next() : $token);

        if ($token === self::T_EOF) {
            $file  = $this->tokenizer->getSourceFile();
            $line  = count($file->getLoc());
            $token = array(self::T_EOF, '<eof>', $line);
        }

        throw new PHP_Reflection_Exceptions_UnexpectedTokenException($file, $token);
    }

    /**
     * Resets some object properties.
     *
     * @return void
     */
    protected function reset()
    {
        $this->_modifiers = 0;
        $this->_comment   = null;
        $this->_package   = PHP_Reflection_BuilderI::PKG_UNKNOWN;
    }

    /**
     * Extracts the @package information from the given comment.
     *
     * @param string $comment A doc comment block.
     *
     * @return string
     */
    protected function parsePackage($comment)
    {
        if (preg_match('#\*\s*@package\s+(.*)#', $comment, $match)) {
            $package = trim($match[1]);
            if (preg_match('#\*\s*@subpackage\s+(.*)#', $comment, $match)) {
                $package .= self::PKG_SEPARATOR . trim($match[1]);
            }
            return $package;
        }
        return PHP_Reflection_BuilderI::PKG_UNKNOWN;
    }

    /**
     * Checks that the current token could be used as file comment.
     *
     * This method checks that the previous token is an open tag and the following
     * token is not a class, a interface, final, abstract or a function.
     *
     * @return boolean
     */
    protected function isFileComment()
    {
        if ($this->tokenizer->prev() !== self::T_OPEN_TAG) {
            return false;
        }

        $notExpectedTags = array(
            self::T_CLASS,
            self::T_FINAL,
            self::T_ABSTRACT,
            self::T_FUNCTION,
            self::T_INTERFACE
        );

        return !in_array($this->tokenizer->peek(), $notExpectedTags, true);
    }

    /**
     * Skips an encapsulted block like strings or backtick strings.
     *
     * @param array(array) &$tokens  The tokens array.
     * @param integer      $endToken The end token.
     *
     * @return void
     */
    private function _skipEncapsultedBlock(&$tokens, $endToken)
    {
        while ($this->tokenizer->peek() !== $endToken) {
            $tokens[] = $this->tokenizer->next();
        }
        $tokens[] = $this->tokenizer->next();
    }

    /**
     * Tries to extract a class or interface type for the given <b>$annotation</b>
     * with in <b>$comment</b>. If there is no matching annotation, this method
     * will return <b>null</b>.
     *
     * <code>
     *   // (at)var RuntimeException
     *   array(
     *       'RuntimeException',
     *       false,
     *   )
     *
     *   // (at)return array(SplObjectStore)
     *   array(
     *       'SplObjectStore',
     *       true,
     *   )
     * </code>
     *
     * @param string $comment    The doc comment block.
     * @param string $annotation The annotation tag (e.g. 'var', 'throws'...).
     *
     * @return array|null
     */
    private function _parseTypeAnnotation($comment, $annotation)
    {
        $baseRegexp   = sprintf('\*\s*@%s\s+', $annotation);
        $arrayRegexp  = '#' . $baseRegexp . 'array\(\s*(\w+\s*=>\s*)?(\w+)\s*\)#';
        $simpleRegexp = '#' . $baseRegexp . '(\w+)#';

        $type = null;
        if (preg_match($arrayRegexp, $comment, $match)) {
            $type = array($match[2], true);
        } else if (preg_match($simpleRegexp, $comment, $match)) {
            $type = array($match[1], false);
        }
        return $type;
    }

    /**
     * Returns the class names of all <b>throws</b> annotations with in the
     * given comment block.
     *
     * @param string $comment The context doc comment block.
     *
     * @return array
     */
    private function _parseThrowsAnnotations($comment)
    {
        $throws = array();
        if (preg_match_all('#\*\s*@throws\s+(\w+)#', $comment, $matches) > 0) {
            foreach ($matches[1] as $match) {
                $throws[] = $match;
            }
        }
        return $throws;
    }

    /**
     * Extracts non scalar types from the property doc comment and sets the
     * matching type instance.
     *
     * @param PHP_Reflection_AST_Property $property The context property instance.
     *
     * @return void
     */
    private function _prepareProperty(PHP_Reflection_AST_Property $property)
    {
        // Skip, if ignore annotations is set
        if ($this->_ignoreAnnotations === true) {
            return;
        }

        // Get type annotation
        $type = $this->_parseTypeAnnotation($property->getDocComment(), 'var');

        if ($type !== null && in_array($type[0], $this->_scalarTypes) === false) {
            $classOrInterface = $this->builder->buildClassOrInterfaceProxy($type[0]);
            $property->setType($classOrInterface);
        }
    }

    /**
     * Extracts documented <b>throws</b> and <b>return</b> types and sets them
     * to the given <b>$callable</b> instance.
     *
     * @param PHP_Reflection_AST_AbstractCallable $callable The context callable.
     *
     * @return void
     */
    private function _prepareCallable(PHP_Reflection_AST_AbstractCallable $callable)
    {
        // Skip, if ignore annotations is set
        if ($this->_ignoreAnnotations === true) {
            return;
        }

        // Get all @throws Types
        $throws = $this->_parseThrowsAnnotations($callable->getDocComment());
        // Append all exception types
        foreach ($throws as $type) {
            $classOrInterface = $this->builder->buildClassOrInterfaceProxy($type);
            $callable->addExceptionType($classOrInterface);
        }
        // Get return annotation
        $type = $this->_parseTypeAnnotation($callable->getDocComment(), 'return');

        if ($type !== null && in_array($type[0], $this->_scalarTypes) === false) {
            $returnType = $this->builder->buildClassOrInterfaceProxy($type[0]);
            $callable->setReturnType($returnType);
        }
    }
}

require_once 'PHP/Reflection/AST/AbstractSourceElement.php';
require_once 'PHP/Reflection/AST/ExpressionI.php';

class PHP_Reflection_AST_StubExpressionExpression
       extends PHP_Reflection_AST_AbstractSourceElement
    implements PHP_Reflection_AST_ExpressionI
{
    public function __construct($line)
    {
        parent::__construct('#dummy-expression', $line);
    }

    public function accept(PHP_Reflection_VisitorI $visitor)
    {

    }
}
?>
