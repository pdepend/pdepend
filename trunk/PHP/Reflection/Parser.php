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
require_once 'PHP/Reflection/TokenizerI.php';

require_once 'PHP/Reflection/Exceptions/UnclosedBodyException.php';

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
{
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
     * Marks the current class as abstract.
     *
     * @type boolean
     * @var boolean $_abstract
     */
    private $_abstract = false;
    
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

            while (($token = $this->tokenizer->next()) !== PHP_Reflection_TokenizerI::T_EOF) {
            
                switch ($token[0]) {
                case PHP_Reflection_TokenizerI::T_ABSTRACT:
                    $this->_abstract = true;
                    break;
                        
                case PHP_Reflection_TokenizerI::T_DOC_COMMENT:
                    $this->_comment = $token[1];
                    $this->_package = $this->parsePackage($token[1]);
                    
                    // Check for doc level comment
                    if ($this->globalPackage === PHP_Reflection_BuilderI::GLOBAL_PACKAGE 
                     && $this->isFileComment() === true) {
    
                        $this->globalPackage = $this->_package;
                        
                        $this->tokenizer->getSourceFile()->setDocComment($token[1]);
                    }
                    break;
                        
                case PHP_Reflection_TokenizerI::T_INTERFACE:
                    $this->_parseInterfaceDeclaration();
                    break;
                        
                case PHP_Reflection_TokenizerI::T_CLASS:
                    $this->_parseClassDeclaration();
                    break;
                        
                case PHP_Reflection_TokenizerI::T_FUNCTION:
                    $function = $this->parseCallable();
                    $function->setSourceFile($this->tokenizer->getSourceFile());
                    $function->setDocComment($this->_comment);
                    
                    $this->_prepareCallable($function);
                    
                    $this->reset();
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
     * Parses a class node.
     *
     * @return void
     */
    private function _parseClassDeclaration()
    {
        // Get class name
        $token = $this->tokenizer->next();

        $qualifiedName = "{$this->_package}::{$token[1]}";
    
        $class = $this->builder->buildClass($qualifiedName, $token[2]);
        $class->setSourceFile($this->tokenizer->getSourceFile());
        $class->setStartLine($token[2]);
        $class->setAbstract($this->_abstract);
        $class->setDocComment($this->_comment);
        $class->setPosition($this->_typePosition++);
                    
        $this->parseClassSignature($class);
    
        $this->builder->buildPackage($this->_package)->addType($class);
    
        $this->reset();
        $this->_parseClassDeclarationBody($class);
    }
    
    /**
     * Parses a class body.
     * 
     * @param PHP_Reflection_Ast_Class $class The context class instance.
     *
     * @return array(array)
     */
    private function _parseClassDeclarationBody(PHP_Reflection_Ast_Class $class)
    {
        $token = $this->tokenizer->next();
        $curly = 0;
        
        $tokens = array($token);
        
        // Method position within the type body
        $this->_methodPosition = 0;
        
        while ($token !== PHP_Reflection_TokenizerI::T_EOF) {
            
            switch ($token[0]) {
            case PHP_Reflection_TokenizerI::T_FUNCTION:
                $class->addMethod($this->_parseMethodDeclaration($tokens));
                break;
                
            case PHP_Reflection_TokenizerI::T_VARIABLE:
                $class->addProperty($this->_parsePropertyDeclaration($tokens));
                break;
            
            case PHP_Reflection_TokenizerI::T_CONST:
                $class->addConstant($this->_parseConstantDeclaration($tokens));
                break;
                    
            case PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN:
                ++$curly;
                $this->reset();
                break;
                    
            case PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE:
                --$curly;
                $this->reset();
                break;
                
            case PHP_Reflection_TokenizerI::T_ABSTRACT:
                $this->_modifiers |= ReflectionMethod::IS_ABSTRACT;
                break;
                
            case PHP_Reflection_TokenizerI::T_VAR:
            case PHP_Reflection_TokenizerI::T_PUBLIC:
                assert(ReflectionProperty::IS_PUBLIC === ReflectionMethod::IS_PUBLIC);
                $this->_modifiers |= ReflectionMethod::IS_PUBLIC;
                break;
                
            case PHP_Reflection_TokenizerI::T_PRIVATE:
                assert(ReflectionProperty::IS_PRIVATE === ReflectionMethod::IS_PRIVATE);
                $this->_modifiers |= ReflectionMethod::IS_PRIVATE;
                break;
                
            case PHP_Reflection_TokenizerI::T_PROTECTED:
                assert(ReflectionProperty::IS_PROTECTED === ReflectionMethod::IS_PROTECTED);
                $this->_modifiers |= ReflectionMethod::IS_PROTECTED;
                break;
                
            case PHP_Reflection_TokenizerI::T_STATIC:
                assert(ReflectionMethod::IS_STATIC === ReflectionProperty::IS_STATIC);
                $this->_modifiers |= ReflectionMethod::IS_STATIC;
                break;
                
            case PHP_Reflection_TokenizerI::T_FINAL:
                $this->_modifiers |= ReflectionMethod::IS_FINAL;
                break;
                
            case PHP_Reflection_TokenizerI::T_DOC_COMMENT:
                $this->_comment = $token[1];
                break;
            
            default:
                // TODO: Handle/log unused tokens
                $this->reset();
                break;
            }
            
            if ($curly === 0) {
                // Set end line number 
                $class->setEndLine($token[2]);
                // Set type tokens
                $class->setTokens($tokens);
                // Stop processing
                break;
            } else {
                $token    = $this->tokenizer->next();
                $tokens[] = $token;
            }
        }
        
        if ($curly !== 0) {
            $file = $this->tokenizer->getSourceFile();
            throw new PHP_Reflection_Exceptions_UnclosedBodyException($file);
        }
        
        return $tokens;
    }
    
    /**
     * Parses an interface node.
     *
     * @return void
     */
    private function _parseInterfaceDeclaration()
    {
        // Get interface name
        $token = $this->tokenizer->next();
                        
        $qualifiedName = "{$this->_package}::{$token[1]}";
    
        $interface = $this->builder->buildInterface($qualifiedName, $token[2]);
        $interface->setSourceFile($this->tokenizer->getSourceFile());
        $interface->setStartLine($token[2]);
        $interface->setDocComment($this->_comment);
        $interface->setPosition($this->_typePosition++);
                    
        $this->parseInterfaceSignature($interface);

        $this->builder->buildPackage($this->_package)->addType($interface);
        
        $this->reset();
        $this->_parseInterfaceDeclarationBody($interface);
    }
    
    /**
     * Parses a interface body.
     * 
     * @param PHP_Reflection_Ast_Interface $interface The context interface instance.
     *
     * @return array(array)
     */
    private function _parseInterfaceDeclarationBody(
                                        PHP_Reflection_Ast_Interface $interface)
    {
        $token = $this->tokenizer->next();
        $curly = 0;
        
        $tokens = array($token);
        
        // Method position within the type body
        $this->_methodPosition = 0;
        
        while ($token !== PHP_Reflection_TokenizerI::T_EOF) {
            
            switch ($token[0]) {
            case PHP_Reflection_TokenizerI::T_FUNCTION:
                // We know all interface methods are abstract and public
                $this->_modifiers |= ReflectionMethod::IS_ABSTRACT;
                $this->_modifiers |= ReflectionMethod::IS_PUBLIC;
                
                $interface->addMethod($this->_parseMethodDeclaration($tokens));
                break;
            
            case PHP_Reflection_TokenizerI::T_CONST:
                $interface->addConstant($this->_parseConstantDeclaration($tokens));
                break;
                    
            case PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN:
                ++$curly;
                $this->reset();
                break;
                    
            case PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE:
                --$curly;
                $this->reset();
                break;
                
            case PHP_Reflection_TokenizerI::T_PUBLIC:
                assert(ReflectionProperty::IS_PUBLIC === ReflectionMethod::IS_PUBLIC);
                $this->_modifiers |= ReflectionMethod::IS_PUBLIC;
                break;
                
            case PHP_Reflection_TokenizerI::T_STATIC:
                assert(ReflectionMethod::IS_STATIC === ReflectionProperty::IS_STATIC);
                $this->_modifiers |= ReflectionMethod::IS_STATIC;
                break;
                
            case PHP_Reflection_TokenizerI::T_DOC_COMMENT:
                $this->_comment = $token[1];
                break;
            
            default:
                // TODO: Handle/log unused tokens
                $this->reset();
                break;
            }
            
            if ($curly === 0) {
                // Set end line number 
                $interface->setEndLine($token[2]);
                // Set type tokens
                $interface->setTokens($tokens);
                // Stop processing
                break;
            } else {
                $token    = $this->tokenizer->next();
                $tokens[] = $token;
            }
        }
        
        if ($curly !== 0) {
            $file = $this->tokenizer->getSourceFile();
            throw new PHP_Reflection_Exceptions_UnclosedBodyException($file);
        }
        
        return $tokens;
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
     * @return PHP_Reflection_Ast_TypeConstant
     */
    private function _parseConstantDeclaration(array &$tokens)
    {
        // Get constant identifier
        $token    = $this->tokenizer->next();
        $tokens[] = $token;

        $constant = $this->builder->buildTypeConstant($token[1]);
        $constant->setDocComment($this->_comment);
        $constant->setStartLine($token[2]);
        $constant->setEndLine($token[2]);

        $this->reset();
        
        return $constant;
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
     * @return PHP_Reflection_Ast_Property
     */
    private function _parsePropertyDeclaration(array &$tokens)
    {
        // Get property identifier
        $token    = $this->tokenizer->token();
        $tokens[] = $token;
        
        $property = $this->builder->buildProperty($token[1], $token[2]);
        $property->setDocComment($this->_comment);
        $property->setModifiers($this->_modifiers);
        $property->setEndLine($token[2]);
                
        $this->_prepareProperty($property);
                
        $this->reset();

        return $property;
    }
    
    /**
     * Parses a class or interface method declaration.
     *
     * @param array &$tokens Reference array for parsed tokens.
     * 
     * @return PHP_Reflection_Ast_Method
     */
    private function _parseMethodDeclaration(array &$tokens)
    {
        $token    = $this->tokenizer->next();
        $tokens[] = $token;
        
        if ($token[0] === PHP_Reflection_TokenizerI::T_BITWISE_AND) {
            $token    = $this->tokenizer->next();
            $tokens[] = $token;
        }
        
        $method = $this->builder->buildMethod($token[1], $token[2]);

        $this->parseCallableSignature($tokens, $method);
        if ($this->tokenizer->peek() === PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN) {
            // Get function body dependencies 
            $this->parseCallableBody($tokens, $method);
        } else {
            $method->setEndLine($token[2]);
        }

        $method->setDocComment($this->_comment);
        $method->setPosition($this->_methodPosition++);
        $method->setModifiers($this->_modifiers);
                
        $this->_prepareCallable($method);
                
        $this->reset();
        
        return $method;
    }
    
    /**
     * Resets some object properties.
     *
     * @return void
     */
    protected function reset()
    {
        $this->_abstract  = false;
        $this->_comment   = null;
        $this->_modifiers = 0;
        $this->_package   = PHP_Reflection_BuilderI::GLOBAL_PACKAGE;
    }
    
    /**
     * Parses the dependencies in a interface signature.
     * 
     * @param PHP_Reflection_Ast_Interface $interface The context interface instance.
     *
     * @return array(array)
     */
    protected function parseInterfaceSignature(PHP_Reflection_Ast_Interface $interface)
    {
        $tokens = array();
        while ($this->tokenizer->peek() !== PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN) {
            $token    = $this->tokenizer->next();
            $tokens[] = $token;
            
            if ($token[0] === PHP_Reflection_TokenizerI::T_STRING) {
                $dependency = $this->builder->buildInterface($token[1]);
                $interface->addDependency($dependency);
            }
        }
        return $tokens;
    }
    
    /**
     * Parses the dependencies in a class signature.
     * 
     * @param PHP_Reflection_Ast_Class $class The context class instance.
     *
     * @return void
     */
    protected function parseClassSignature(PHP_Reflection_Ast_Class $class)
    {
        $tokens     = array();
        $implements = false;
        
        while ($this->tokenizer->peek() !== PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN) {
            $token    = $this->tokenizer->next();
            $tokens[] = $token;
            
            if ($token[0] === PHP_Reflection_TokenizerI::T_IMPLEMENTS) {
                $implements = true;
            } else if ($token[0] === PHP_Reflection_TokenizerI::T_STRING) {
                if ($implements) {
                    $dependency = $this->builder->buildInterface($token[1]);
                } else {
                    $dependency = $this->builder->buildClass($token[1]);
                }
                // Set class dependency
                $class->addDependency($dependency);
            }
        }
        
        return $tokens;
    }
    
    /**
     * Parses a function or a method and adds it to the parent context node.
     * 
     * @param array(array)                    &$tokens Collected tokens.
     * @param PHP_Reflection_Ast_AbstractType $parent  An optional parent 
     *                                                 interface of class.
     * 
     * @return PHP_Reflection_Ast_AbstractCallable
     */
    protected function parseCallable(array &$tokens = array(), PHP_Reflection_Ast_AbstractType $parent = null)
    {
        $token    = $this->tokenizer->next();
        $tokens[] = $token;
        
        if ($token[0] === PHP_Reflection_TokenizerI::T_BITWISE_AND) {
            $token    = $this->tokenizer->next();
            $tokens[] = $token;
        }
        
        $callable = null;
        if ($parent === null) {
            $callable = $this->builder->buildFunction($token[1], $token[2]);
            
            $package = $this->globalPackage;
            if ($this->_package !== PHP_Reflection_BuilderI::GLOBAL_PACKAGE) {
                $package = $this->_package;
            }

            $this->builder->buildPackage($package)->addFunction($callable); 
        } else {
            $callable = $this->builder->buildMethod($token[1], $token[2]);
            $parent->addMethod($callable);
        }
        
        $this->parseCallableSignature($tokens, $callable);
        if ($this->tokenizer->peek() === PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN) {
            // Get function body dependencies 
            $this->parseCallableBody($tokens, $callable);
        } else {
            $callable->setEndLine($token[2]);
        }
        
        return $callable;
    }

    /**
     * Extracts all dependencies from a callable signature.
     *
     * @param array(array)                        &$tokens  Collected tokens.
     * @param PHP_Reflection_Ast_AbstractCallable $callable The context callable.
     * 
     * @return void
     */
    protected function parseCallableSignature(array &$tokens, PHP_Reflection_Ast_AbstractCallable $callable)
    {
        if ($this->tokenizer->peek() !== PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN) {
            // Load invalid token for line number
            $token    = $this->tokenizer->next();
            $tokens[] = $token;
            
            // Throw a detailed exception message
            throw new RuntimeException(
                sprintf(
                    'Invalid token "%s" on line %s in file: %s.',
                    $token[1],
                    $token[2],
                    $this->tokenizer->getSourceFile()
                )
            );
        }
        
        $parameterType     = null;
        $parameterPosition = 0;

        $parenthesis = 0;
        
        while (($token = $this->tokenizer->next()) !== PHP_Reflection_TokenizerI::T_EOF) {

            $tokens[] = $token;
            
            switch ($token[0]) {
            case PHP_Reflection_TokenizerI::T_PARENTHESIS_OPEN:
                ++$parenthesis;
                $parameterType = null;
                break;
                 
            case PHP_Reflection_TokenizerI::T_PARENTHESIS_CLOSE:
                --$parenthesis;
                $parameterType = null;
                break;
                    
            case PHP_Reflection_TokenizerI::T_STRING:
                // Check that the next token is a variable or next token is the
                // reference operator and the fo
                if ($this->tokenizer->peek() !== PHP_Reflection_TokenizerI::T_VARIABLE
                 && $this->tokenizer->peek() !== PHP_Reflection_TokenizerI::T_BITWISE_AND) {
                    continue;
                }
                if ($this->tokenizer->peek() === PHP_Reflection_TokenizerI::T_BITWISE_AND) {
                    // Store reference operator
                    $tokens[] = $this->tokenizer->next();
                    // Check next token
                    if ($this->tokenizer->peek() !== PHP_Reflection_TokenizerI::T_VARIABLE) {
                        continue;
                    }
                }
                
                // Create an instance for this parameter
                $parameterType = $this->builder->buildClassOrInterface($token[1]);
                break;
                
            case PHP_Reflection_TokenizerI::T_VARIABLE:
                $parameter = $this->builder->buildParameter($token[1], $token[2]);
                $parameter->setPosition($parameterPosition++);
                
                if ($parameterType !== null) {
                    $parameter->setType($parameterType);
                }
                $callable->addParameter($parameter);
                break;

            default:
                // TODO: Handle/log unused tokens
                $parameterType = null;
                break;
            }
            
            if ($parenthesis === 0) {
                return;
            }
        }
        throw new RuntimeException('Invalid function signature.');
    }
    
    /**
     * Extracts all dependencies from a callable body.
     *
     * @param array(array)                        &$outTokens Collected tokens.
     * @param PHP_Reflection_Ast_AbstractCallable $callable   The context callable.
     * 
     * @return void
     */
    protected function parseCallableBody(array &$outTokens, PHP_Reflection_Ast_AbstractCallable $callable)
    {
        $curly  = 0;
        $tokens = array();

        while ($this->tokenizer->peek() !== PHP_Reflection_TokenizerI::T_EOF) {

            $tokens[] = $token = $this->tokenizer->next();

            switch ($token[0]) {
            case PHP_Reflection_TokenizerI::T_CATCH:
                // Skip open parenthesis
                $tokens[] = $this->tokenizer->next();
                
            case PHP_Reflection_TokenizerI::T_NEW:
            case PHP_Reflection_TokenizerI::T_INSTANCEOF:
                $parts = $this->_parseClassNameChain($tokens);
                                
                // If this is a dynamic instantiation, do not add dependency.
                // Something like: new $className('PDepend');
                if (count($parts) > 0) {
                    // Get last element of parts and create a class for it
                    $class = $this->builder->buildClass(join('::', $parts));
                    $callable->addDependency($class);
                }
                break;
                    
            case PHP_Reflection_TokenizerI::T_STRING:
                if ($this->tokenizer->peek() === PHP_Reflection_TokenizerI::T_DOUBLE_COLON) {
                    // Skip double colon
                    $tokens[] = $this->tokenizer->next();
                    // Check for method call
                    if ($this->tokenizer->peek() === PHP_Reflection_TokenizerI::T_STRING) {
                        // Skip method call
                        $tokens[] = $this->tokenizer->next();
                        // Create a dependency class
                        $dependency = $this->builder->buildClassOrInterface($token[1]);

                        $callable->addDependency($dependency);
                    }
                }
                break;
                    
            case PHP_Reflection_TokenizerI::T_CURLY_BRACE_OPEN:
                ++$curly;
                break;
                    
            case PHP_Reflection_TokenizerI::T_CURLY_BRACE_CLOSE:
                --$curly;
                break;

            case PHP_Reflection_TokenizerI::T_DOUBLE_QUOTE:
            case PHP_Reflection_TokenizerI::T_BACKTICK:
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
            $fileName = (string) $this->tokenizer->getSourceFile();
            $message  = "Invalid state, unclosed function body in '{$fileName}'.";
            throw new RuntimeException($message);
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
        if ($this->tokenizer->prev() !== PHP_Reflection_TokenizerI::T_OPEN_TAG) {
            return false;
        }
        
        $notExpectedTags = array(
            PHP_Reflection_TokenizerI::T_CLASS,
            PHP_Reflection_TokenizerI::T_FINAL,
            PHP_Reflection_TokenizerI::T_ABSTRACT,
            PHP_Reflection_TokenizerI::T_FUNCTION,
            PHP_Reflection_TokenizerI::T_INTERFACE
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
        $allowed = array(
            PHP_Reflection_TokenizerI::T_DOUBLE_COLON,
            PHP_Reflection_TokenizerI::T_STRING,
        );
        
        $parts = array();
        
        while (in_array($this->tokenizer->peek(), $allowed)) {
            $token    = $this->tokenizer->next();
            $tokens[] = $token;

            if ($token[0] === PHP_Reflection_TokenizerI::T_STRING) {
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
     * @param PHP_Reflection_Ast_Property $property The context property instance.
     * 
     * @return void
     */
    private function _prepareProperty(PHP_Reflection_Ast_Property $property)
    {
        // Skip, if ignore annotations is set
        if ($this->_ignoreAnnotations === true) {
            return; 
        }
        
        // Get type annotation
        $type = $this->_parseTypeAnnotation($property->getDocComment(), 'var');
        
        if ($type !== null && in_array($type[0], $this->_scalarTypes) === false) {
            $property->setType($this->builder->buildClassOrInterface($type[0]));
        }
    }
    
    /**
     * Extracts documented <b>throws</b> and <b>return</b> types and sets them
     * to the given <b>$callable</b> instance.
     *
     * @param PHP_Reflection_Ast_AbstractCallable $callable The context callable.
     * 
     * @return void
     */
    private function _prepareCallable(PHP_Reflection_Ast_AbstractCallable $callable)
    {
        // Skip, if ignore annotations is set
        if ($this->_ignoreAnnotations === true) {
            return; 
        }
        
        // Get all @throws Types
        $throws = $this->_parseThrowsAnnotations($callable->getDocComment());
        // Append all exception types
        foreach ($throws as $type) {
            $exceptionType = $this->builder->buildClassOrInterface($type);
            $callable->addExceptionType($exceptionType);
        }
        
        // Get return annotation
        $type = $this->_parseTypeAnnotation($callable->getDocComment(), 'return');
        
        if ($type !== null && in_array($type[0], $this->_scalarTypes) === false) {
            $returnType = $this->builder->buildClassOrInterface($type[0]);
            $callable->setReturnType($returnType);
        }
    }
}