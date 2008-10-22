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
require_once 'PHP/Reflection/ParserConstantsI.php';
require_once 'PHP/Reflection/TokenizerI.php';
require_once 'PHP/Reflection/PHPValueTypesI.php';

require_once 'PHP/Reflection/Exceptions/IdentifierExpectedException.php';
require_once 'PHP/Reflection/Exceptions/UnclosedBodyException.php';
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
    implements PHP_Reflection_ParserConstantsI,
               PHP_Reflection_PHPValueTypesI
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
     * @var string $globalPackage
     */
    protected $globalPackage = PHP_Reflection_BuilderI::GLOBAL_PACKAGE;
    
    /**
     * The package separator token.
     *
     * @type string
     * @var string $packageSeparator
     */
    protected $packageSeparator = '::';
    
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
    private $_package = PHP_Reflection_BuilderI::GLOBAL_PACKAGE;
    
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

            while (($token = $this->tokenizer->next()) !== self::T_EOF) {
            
                switch ($token[0]) {
                case self::T_ABSTRACT:
                    $this->_modifiers |= ReflectionClass::IS_EXPLICIT_ABSTRACT;
                    break;
                    
                case self::T_FINAL:
                    $this->_modifiers |= ReflectionClass::IS_FINAL;
                    break;
                        
                case self::T_DOC_COMMENT:
                    $this->_comment = $token[1];
                    $this->_package = $this->parsePackage($token[1]);
                    
                    // Check for doc level comment
                    if ($this->globalPackage === PHP_Reflection_BuilderI::GLOBAL_PACKAGE 
                     && $this->isFileComment() === true) {
    
                        $this->globalPackage = $this->_package;
                        
                        $this->tokenizer->getSourceFile()->setDocComment($token[1]);
                    }
                    break;
                        
                case self::T_INTERFACE:
                    $this->_parseInterfaceDeclaration();
                    break;
                        
                case self::T_CLASS:
                    $this->_parseClassDeclaration();
                    break;
                        
                case self::T_FUNCTION:
                    $this->_parseFunctionDeclaration();
                    break;
                        
                default:
                    // TODO: Handle/log unused tokens
                    break;
                }
            }
            
            // Reset global package and type position
            $this->globalPackage = PHP_Reflection_BuilderI::GLOBAL_PACKAGE;
            $this->_typePosition = 0;
        }
    }
    
    /**
     * Parses a function node.
     *
     * @return void
     */
    private function _parseFunctionDeclaration()
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
        }
        
        // We expect a T_STRING token for function name
        $token    = $this->_consumeToken(self::T_STRING, $tokens);
        $function = $this->builder->buildFunction($token[1], $token[2]);
            
        $package = $this->globalPackage;
        if ($this->_package !== PHP_Reflection_BuilderI::GLOBAL_PACKAGE) {
            $package = $this->_package;
        }
        $this->builder->buildPackage($package)->addFunction($function);
        
        // Skip comment tokens before function signature
        $this->_consumeComments($tokens);
        
        $parameters = $this->_parseParameterList($tokens);
        foreach ($parameters as $parameter) {
            $function->addParameter($parameter);
        }
        
        $this->_parseMethodOrFunctionBody($tokens, $function);
        $function->setSourceFile($this->tokenizer->getSourceFile());
        $function->setDocComment($this->_comment);
        
        $this->_prepareMethodOrFunction($function);
                    
        $this->reset();        
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
        $class->setStartLine($token[2]);
        $class->setModifiers($this->_modifiers);
        $class->setDocComment($this->_comment);
        $class->setPosition($this->_typePosition++);
        
        // Skip comment tokens
        $this->_skipTokens(self::T_COMMENT, self::T_DOC_COMMENT);
        
        // Check for parent class
        if ($this->tokenizer->peek() === self::T_EXTENDS) {
            // Skip extends token
            $this->tokenizer->next();
            // Parse parent class name
            $qualifiedName = $this->_parseStaticQualifiedIdentifier();
            // Set  parent class
            $class->setParentClass($this->builder->buildClass($qualifiedName));

            // Skip comment tokens
            $this->_skipTokens(self::T_COMMENT, self::T_DOC_COMMENT);
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
            $this->_skipTokens(self::T_COMMENT, self::T_DOC_COMMENT);
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
                $constant = $this->_parseConstantDeclaration($tokens);
                $this->_classOrInterface->addConstant($constant);
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
        $interface->setStartLine($token[2]);
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
                $constant = $this->_parseConstantDeclaration($tokens);
                $this->_classOrInterface->addConstant($constant);
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
        $this->_consumeToken(self::T_CONST, $tokens);
        $this->_consumeComments($tokens);
        
        $token = $this->_consumeToken(self::T_STRING, $tokens);

        $constant = $this->builder->buildClassOrInterfaceConstant($token[1]);
        $constant->setDocComment($this->_comment);
        $constant->setStartLine($token[2]);
        $constant->setEndLine($token[2]);

        $this->reset();
        
        $this->_consumeComments($tokens);
        $this->_consumeToken(self::T_EQUAL, $tokens);
        $this->_consumeComments($tokens);
        
        // Parse static scalar value
        $constant->setValue($this->_parseStaticScalarValue($tokens));
        
        $this->_consumeComments($tokens);
        $this->_consumeToken(self::T_SEMICOLON, $tokens);
        
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
        }
        $this->_consumeComments($tokens);
        
        $method = $this->builder->buildMethod($token[1], $token[2]);

        $parameterList = $this->_parseParameterList($tokens);
        foreach ($parameterList as $parameter) {
            $method->addParameter($parameter);
        }

        $this->_consumeComments(); 
        if ($this->tokenizer->peek() === self::T_CURLY_BRACE_OPEN) {
            $this->_parseMethodOrFunctionBody($tokens, $method);
        } else {
            $token = $this->_consumeToken(self::T_SEMICOLON, $tokens);
            $method->setEndLine($token[2]);
        }

        $method->setDocComment($this->_comment);
        $method->setPosition($this->_methodPosition++);
        $method->setModifiers($this->_modifiers);
                
        $this->_prepareMethodOrFunction($method);
                
        $this->reset();
        
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
            $interfaceList[] = $this->builder->buildInterface($identifier);
            
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
        $parameterList = array();
        
        $this->_consumeComments($tokens);
        $this->_consumeToken(self::T_PARENTHESIS_OPEN, $tokens);
        $this->_consumeComments($tokens);
        
        while (($tokenID = $this->tokenizer->peek()) !== self::T_EOF) {
        
            switch ($tokenID) {
                
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
                $parameter->setPosition(count($parameterList));
                
                $parameterList[] = $parameter;
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
        $parameter->setStartLine($token[2]);
        $parameter->setEndLine($token[2]);
        
        if ($classOrInterface !== null) {
            $parameter->setType($classOrInterface);
        }
        
        $this->_consumeComments($tokens);
        
        // Check for default value
        if ($this->tokenizer->peek() === self::T_EQUAL) {
            $this->_consumeToken(self::T_EQUAL, $tokens);
            $this->_consumeComments($tokens);
            $this->_parseStaticValue($tokens);
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
            $this->_skipTokens(self::T_COMMENT, self::T_DOC_COMMENT);            
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
                $negative = true;
                
            case self::T_PLUS:
                $tokens[] = $this->tokenizer->next();
                continue;
                
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
     * 
     *
     * @param array &$tokens Reference array for parsed tokens.
     *
     * @return PHP_Reflection_AST_CatchStatement
     */
    private function _parseCatchStatement(array &$tokens)
    {
        //$this->_consumeToken(self::T_CATCH, $tokens);
        $this->_consumeToken(self::T_PARENTHESIS_OPEN, $tokens);
        
        $identifier = $this->_parseStaticQualifiedIdentifier($tokens);
        
        $this->_consumeToken(self::T_VARIABLE, $tokens);
        $this->_consumeToken(self::T_PARENTHESIS_CLOSE);
        
        $exception = $this->builder->buildClassOrInterfaceProxy($identifier);
        $statement = $this->builder->buildCatchStatement();
        $statement->setReference($exception);
        
        return $statement;
    }
    
    /**
     * This method will consume all tokens that match the given token identifiers.
     * This method accepts a variable list of token identifiers/types as argument.
     * 
     * <code>
     * $this->_skipTokens(self::T_COMMENT, self::T_DOC_COMMENT);
     * </code>
     *
     * @return void
     */
    private function _skipTokens()
    {
        $consumeTokens = func_get_args();
        while (in_array($this->tokenizer->peek(), $consumeTokens) === true) {
            $this->tokenizer->next();
        }
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
        $this->_package   = PHP_Reflection_BuilderI::GLOBAL_PACKAGE;
    }
    
    /**
     * Extracts all dependencies from a callable body.
     *
     * @param array(array)                                &$outTokens Collected tokens.
     * @param PHP_Reflection_AST_AbstractMethodOrFunction $callable   The context callable.
     * 
     * @return void
     */
    private function _parseMethodOrFunctionBody(
        array &$outTokens, PHP_Reflection_AST_AbstractMethodOrFunction $callable)
    {
        $curly  = 1;
        $tokens = array();
        
        $this->_consumeToken(self::T_CURLY_BRACE_OPEN, $tokens);
        
        while ($this->tokenizer->peek() !== self::T_EOF) {

            $tokens[] = $token = $this->tokenizer->next();

            switch ($token[0]) {
            case self::T_CATCH:
                $statement = $this->_parseCatchStatement($tokens);
                // FIXME: This should be something else
                $callable->addDependency($statement->getReference());
                break;
                
            case self::T_NEW:
            case self::T_INSTANCEOF:
                $parts = $this->_parseClassNameChain($tokens);
                                
                // If this is a dynamic instantiation, do not add dependency.
                // Something like: new $className('PDepend');
                if (count($parts) > 0) {
                    $class = $this->builder->buildClassOrInterfaceProxy(join('::', $parts));
                    $callable->addDependency($class);
                }
                break;
                    
            case self::T_STRING:
                if ($this->tokenizer->peek() === self::T_DOUBLE_COLON) {
                    // Skip double colon
                    $tokens[] = $this->tokenizer->next();
                    // Check for method call
                    if ($this->tokenizer->peek() === self::T_STRING) {
                        // Skip method call
                        $tokens[] = $this->tokenizer->next();
                        // Create a dependency class
                        $dependency = $this->builder->buildClassOrInterfaceProxy($token[1]);

                        $callable->addDependency($dependency);
                    }
                }
                break;
                    
            case self::T_CURLY_BRACE_OPEN:
                ++$curly;
                break;
                    
            case self::T_CURLY_BRACE_CLOSE:
                --$curly;
                break;

            case self::T_DOUBLE_QUOTE:
            case self::T_BACKTICK:
                $this->_skipEncapsultedBlock($tokens, $token[0]);
                break;

            default:
                // throw new RuntimeException("Unknown token '{$token[1]}'.");
                // TODO: Handle/log unused tokens
            }
            
            if ($curly === 0) {
                // Set end line number
                $callable->setEndLine($token[2]);
                // Set all tokens for this function
                $callable->setTokens($tokens);
                // Stop processing
                break;
            }
        }
        // Throw an exception for invalid states
        if ($curly !== 0) {
            $file = $this->tokenizer->getSourceFile();
            throw new PHP_Reflection_Exceptions_UnclosedBodyException($file);
        }
        
        // Append all tokens
        foreach ($tokens as $token) {
            $outTokens[] = $token;
        }
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
                $package .= $this->packageSeparator . trim($match[1]);
            }
            return $package;
        }
        return PHP_Reflection_BuilderI::GLOBAL_PACKAGE;
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
     * Parses a php class/method name chain.
     * 
     * <code>
     * PHP::Depend::Parser::parse();
     * </code>
     * 
     * @param array(array) &$tokens The tokens array.
     *
     * @return array(array)
     */
    private function _parseClassNameChain(&$tokens)
    {
        $allowed = array(self::T_DOUBLE_COLON, self::T_STRING);
        
        $parts = array();
        
        while (in_array($this->tokenizer->peek(), $allowed)) {
            $token    = $this->tokenizer->next();
            $tokens[] = $token;

            if ($token[0] === self::T_STRING) {
                $parts[] = $token[1];
            }
        }
        return $parts;
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
     * @param PHP_Reflection_AST_AbstractMethodOrFunction $mof The context callable.
     * 
     * @return void
     */
    private function _prepareMethodOrFunction(
                                PHP_Reflection_AST_AbstractMethodOrFunction $mof)
    {
        // Skip, if ignore annotations is set
        if ($this->_ignoreAnnotations === true) {
            return; 
        }
 
        // Get all @throws Types
        $throws = $this->_parseThrowsAnnotations($mof->getDocComment());
        // Append all exception types
        foreach ($throws as $type) {
            $classOrInterface = $this->builder->buildClassOrInterfaceProxy($type);
            $mof->addExceptionType($classOrInterface);
        }
        // Get return annotation
        $type = $this->_parseTypeAnnotation($mof->getDocComment(), 'return');
        
        if ($type !== null && in_array($type[0], $this->_scalarTypes) === false) {
            $returnType = $this->builder->buildClassOrInterfaceProxy($type[0]);
            $mof->setReturnType($returnType);
        }
    }
}
?>
