<?php
/**
 * This file is part of PHP_Depend.
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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/Code/VisibilityAwareI.php';
require_once 'PHP/Depend/Code/NodeBuilderI.php';
require_once 'PHP/Depend/Code/TokenizerI.php';

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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Parser
{
    /**
     * Last parsed package tag.
     *
     * @type string
     * @var string $package
     */
    protected $package = PHP_Depend_Code_NodeBuilderI::DEFAULT_PACKAGE;
    
    /**
     * The package defined in the file level comment.
     *
     * @type string
     * @var string $globalPackage
     */
    protected $globalPackage = PHP_Depend_Code_NodeBuilderI::DEFAULT_PACKAGE;
    
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
     * @var boolean $abstract
     */
    protected $abstract = false;
    
    /**
     * The used code tokenizer.
     *
     * @type PHP_Depend_Code_TokenizerI 
     * @var PHP_Depend_Code_TokenizerI $tokenizer
     */
    protected $tokenizer = null;
    
    /**
     * The used data structure builder.
     * 
     * @type PHP_Depend_Code_NodeBuilderI
     * @var PHP_Depend_Code_NodeBuilderI $builder
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
        'unknown', // Eclipse default return value
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
     * @param PHP_Depend_Code_TokenizerI   $tokenizer The used code tokenizer.
     * @param PHP_Depend_Code_NodeBuilderI $builder   The used node builder.
     */
    public function __construct(PHP_Depend_Code_TokenizerI $tokenizer, 
                                PHP_Depend_Code_NodeBuilderI $builder)
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
     * @return void
     */
    public function parse()
    {
        $this->reset();
        
        $comment = null;

        while (($token = $this->tokenizer->next()) !== PHP_Depend_Code_TokenizerI::T_EOF) {
            
            switch ($token[0]) {
            case PHP_Depend_Code_TokenizerI::T_ABSTRACT:
                $this->abstract = true;
                break;
                    
            case PHP_Depend_Code_TokenizerI::T_DOC_COMMENT:
                $comment       = $token[1];
                $this->package = $this->parsePackage($token[1]);
                
                // Check for doc level comment
                if ($this->globalPackage === PHP_Depend_Code_NodeBuilderI::DEFAULT_PACKAGE 
                 && $this->isFileComment() === true) {

                    $this->globalPackage = $this->package;
                    
                    $this->tokenizer->getSourceFile()->setDocComment($token[1]);
                    
                    // TODO: What happens if there is no file comment, we will
                    //       reuse the same comment for a class, interface or
                    //       function?
                }
                break;
                    
            case PHP_Depend_Code_TokenizerI::T_INTERFACE:
                // Get interface name
                $token = $this->tokenizer->next();
                    
                $qualifiedName = "{$this->package}::{$token[1]}";

                $interface = $this->builder->buildInterface($qualifiedName, $token[2]);
                $interface->setSourceFile($this->tokenizer->getSourceFile());
                $interface->setStartLine($token[2]);
                $interface->setDocComment($comment);
                
                $this->parseInterfaceSignature($interface);

                $this->builder->buildPackage($this->package)->addType($interface);

                $this->parseTypeBody($interface);
                $this->reset();
                
                $comment = null;
                break;
                    
            case PHP_Depend_Code_TokenizerI::T_CLASS:
                // Get class name
                $token = $this->tokenizer->next();
                    
                $qualifiedName = "{$this->package}::{$token[1]}";

                $class = $this->builder->buildClass($qualifiedName, $token[2]);
                $class->setSourceFile($this->tokenizer->getSourceFile());
                $class->setStartLine($token[2]);
                $class->setAbstract($this->abstract);
                $class->setDocComment($comment);
                
                $this->parseClassSignature($class);

                $this->builder->buildPackage($this->package)->addType($class);

                $this->parseTypeBody($class);
                $this->reset();
                
                $comment = null;
                break;
                    
            case PHP_Depend_Code_TokenizerI::T_FUNCTION:
                $function = $this->parseCallable();
                $function->setSourceFile($this->tokenizer->getSourceFile());
                $function->setDocComment($comment);
                
                $this->_prepareCallable($function);
                
                $this->reset();
                
                $comment = null;
                break;
                    
            default:
                // TODO: Handle/log unused tokens
                $comment = null;
                break;
            }
        }
    }
    
    /**
     * Resets some object properties.
     *
     * @return void
     */
    protected function reset()
    {
        $this->package  = PHP_Depend_Code_NodeBuilderI::DEFAULT_PACKAGE;
        $this->abstract = false;
    }
    
    /**
     * Parses the dependencies in a interface signature.
     * 
     * @param PHP_Depend_Code_Interface $interface The context interface instance.
     *
     * @return void
     */
    protected function parseInterfaceSignature(PHP_Depend_Code_Interface $interface)
    {
        while ($this->tokenizer->peek() !== PHP_Depend_Code_TokenizerI::T_CURLY_BRACE_OPEN) {
            $token = $this->tokenizer->next();
            if ($token[0] === PHP_Depend_Code_TokenizerI::T_STRING) {
                $dependency = $this->builder->buildInterface($token[1]);
                $interface->addDependency($dependency);
            }
        }
    }
    
    /**
     * Parses the dependencies in a class signature.
     * 
     * @param PHP_Depend_Code_Class $class The context class instance.
     *
     * @return void
     */
    protected function parseClassSignature(PHP_Depend_Code_Class $class)
    {
        $implements = false;
        while ($this->tokenizer->peek() !== PHP_Depend_Code_TokenizerI::T_CURLY_BRACE_OPEN) {
            $token = $this->tokenizer->next();
            if ($token[0] === PHP_Depend_Code_TokenizerI::T_IMPLEMENTS) {
                $implements = true;
            } else if ($token[0] === PHP_Depend_Code_TokenizerI::T_STRING) {
                if ($implements) {
                    $dependency = $this->builder->buildInterface($token[1]);
                } else {
                    $dependency = $this->builder->buildClass($token[1]);
                }
                // Set class dependency
                $class->addDependency($dependency);
            }
        }
    }
    
    /**
     * Parses a class/interface body.
     * 
     * @param PHP_Depend_Code_AbstractType $type The context type instance.
     *
     * @return void
     */
    protected function parseTypeBody(PHP_Depend_Code_AbstractType $type)
    {
        $token = $this->tokenizer->next();
        $curly = 0;
        
        // If type is an interface all methods are abstract
        $abstractDefault = ($type instanceof PHP_Depend_Code_Interface);
        
        $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC;;
        $comment   = null;
        $abstract  = $abstractDefault;
        
        while ($token !== PHP_Depend_Code_TokenizerI::T_EOF) {
            
            switch ($token[0]) {
            case PHP_Depend_Code_TokenizerI::T_FUNCTION:
                $method = $this->parseCallable($type);
                $method->setDocComment($comment);
                $method->setAbstract($abstract);
                $method->setVisibility($visibilty);
                
                $this->_prepareCallable($method);
                
                $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC;;
                $comment   = null;
                $abstract  = $abstractDefault;
                break;
                
            case PHP_Depend_Code_TokenizerI::T_VARIABLE:
                $property = $this->builder->buildProperty($token[1], $token[2]);
                $property->setDocComment($comment);
                $property->setVisibility($visibilty);
                $property->setEndLine($token[2]);
                
                $this->_prepareProperty($property);
                
                // TODO: Do we need an instanceof, to check that $type is a
                //       PHP_Depend_Code_Class instance or do we believe the 
                //       code is correct?
                $type->addProperty($property);
                
                $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC;;
                $comment   = null;
                $abstract  = $abstractDefault;
                break;
                    
            case PHP_Depend_Code_TokenizerI::T_CURLY_BRACE_OPEN:
                ++$curly;
                $comment = null; 
                break;
                    
            case PHP_Depend_Code_TokenizerI::T_CURLY_BRACE_CLOSE:
                --$curly;
                $comment = null; 
                break;
                
            case PHP_Depend_Code_TokenizerI::T_ABSTRACT:
                $abstract = true;
                break;
                
            case PHP_Depend_Code_TokenizerI::T_PUBLIC:
                $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC;
                break;
                
            case PHP_Depend_Code_TokenizerI::T_PRIVATE:
                $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE;
                break;
                
            case PHP_Depend_Code_TokenizerI::T_PROTECTED:
                $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED;
                break;
                
            case PHP_Depend_Code_TokenizerI::T_STATIC:
                break;
                
            case PHP_Depend_Code_TokenizerI::T_FINAL:
                break;
                
            case PHP_Depend_Code_TokenizerI::T_DOC_COMMENT:
                $comment = $token[1];
                break;
            
            default:
                // TODO: Handle/log unused tokens
                $comment = null; 
                break;
            }
            
            if ($curly === 0) {
                // Set end line number 
                $type->setEndLine($token[2]);
                // Stop processing
                break;
            } else {
                $token = $this->tokenizer->next();
            }
        }
        
        if ($curly !== 0) {
            $fileName = (string) $this->tokenizer->getSourceFile();
            $message  = "Invalid state, unclosed class body in file '{$fileName}'.";
            throw new RuntimeException($message);
        }
    }
    
    /**
     * Parses a function or a method and adds it to the parent context node.
     * 
     * @param PHP_Depend_Code_AbstractType $parent An optional parent interface of class.
     * 
     * @return PHP_Depend_Code_AbstractCallable
     */
    protected function parseCallable(PHP_Depend_Code_AbstractType $parent = null)
    {
        $token = $this->tokenizer->next();
        if ($token[0] === PHP_Depend_Code_TokenizerI::T_BITWISE_AND) {
            $token = $this->tokenizer->next();
        }
        
        $callable = null;
        if ($parent === null) {
            $callable = $this->builder->buildFunction($token[1], $token[2]);
            
            $package = $this->globalPackage;
            if ($this->package !== PHP_Depend_Code_NodeBuilderI::DEFAULT_PACKAGE) {
                $package = $this->package;
            }
            
            $this->builder->buildPackage($package)->addFunction($callable); 
        } else {
            $callable = $this->builder->buildMethod($token[1], $token[2]);
            $parent->addMethod($callable);
        }
        
        $this->parseCallableSignature($callable);
        if ($this->tokenizer->peek() === PHP_Depend_Code_TokenizerI::T_CURLY_BRACE_OPEN) {
            // Get function body dependencies 
            $this->parseCallableBody($callable);
        } else {
            $callable->setEndLine($token[2]);
        }
        
        return $callable;
    }

    /**
     * Extracts all dependencies from a callable signature.
     *
     * @param PHP_Depend_Code_AbstractCallable $callable The context callable.
     * 
     * @return void
     */
    protected function parseCallableSignature(PHP_Depend_Code_AbstractCallable $callable)
    {
        if ($this->tokenizer->peek() !== PHP_Depend_Code_TokenizerI::T_PARENTHESIS_OPEN) {
            // Load invalid token for line number
            $token = $this->tokenizer->next();
            
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

        $parenthesis = 0;
        
        while (($token = $this->tokenizer->next()) !== PHP_Depend_Code_TokenizerI::T_EOF) {

            switch ($token[0]) {
            case PHP_Depend_Code_TokenizerI::T_PARENTHESIS_OPEN:
                ++$parenthesis;
                break;
                 
            case PHP_Depend_Code_TokenizerI::T_PARENTHESIS_CLOSE:
                --$parenthesis;
                break;
                    
            case PHP_Depend_Code_TokenizerI::T_STRING:
                // Create an instance for this dependency and append it
                $dependency = $this->builder->buildClassOrInterface($token[1]);
                $callable->addDependency($dependency);
                break;

            default:
                // TODO: Handle/log unused tokens
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
     * @param PHP_Depend_Code_AbstractCallable $callable The context callable.
     * 
     * @return void
     */
    protected function parseCallableBody(PHP_Depend_Code_AbstractCallable $callable)
    {
        $curly  = 0;
        $tokens = array();
        $string = false;
        $escape = false;
        $inline = false;

        while ($this->tokenizer->peek() !== PHP_Depend_Code_TokenizerI::T_EOF) {
            
            $tokens[] = $token = $this->tokenizer->next();

            switch ($token[0]) {
            case PHP_Depend_Code_TokenizerI::T_NEW:
                $allowed = array(
                    PHP_Depend_Code_TokenizerI::T_DOUBLE_COLON,
                    PHP_Depend_Code_TokenizerI::T_STRING,
                );
                
                $parts = array();
                while (in_array($this->tokenizer->peek(), $allowed)) {
                    $token    = $this->tokenizer->next();
                    $tokens[] = $token;
                    
                    if ($token[0] === PHP_Depend_Code_TokenizerI::T_STRING) {
                        $parts[] = $token[1];
                    }
                }
                
                // If this is a dynamic instantiation, do not add dependency.
                // Something like: new $className('PDepend');
                if (count($parts) > 0) {
                    // Get last element of parts and create a class for it
                    $class = $this->builder->buildClass(join('::', $parts));
                    $callable->addDependency($class);
                }
                break;
                    
            case PHP_Depend_Code_TokenizerI::T_STRING:
                if ($this->tokenizer->peek() === PHP_Depend_Code_TokenizerI::T_DOUBLE_COLON) {
                    // Skip double colon
                    $tokens[] = $this->tokenizer->next();
                    // Check for method call
                    if ($this->tokenizer->peek() === PHP_Depend_Code_TokenizerI::T_STRING) {
                        // Skip method call
                        $tokens[] = $this->tokenizer->next();
                        // Create a dependency class
                        $dependency = $this->builder->buildClassOrInterface($token[1]);

                        $callable->addDependency($dependency);
                    }
                }
                break;
                    
            case PHP_Depend_Code_TokenizerI::T_CURLY_BRACE_OPEN:
                if ($string === false) {
                    ++$curly;
                } else if ($this->tokenizer->peek() === PHP_Depend_Code_TokenizerI::T_VARIABLE) {
                    ++$curly;
                    $inline = true;
                }
                break;
                    
            case PHP_Depend_Code_TokenizerI::T_CURLY_BRACE_CLOSE:
                if ($string === false || $inline === true) {
                    --$curly;
                    $inline = false;
                }
                break;

            case PHP_Depend_Code_TokenizerI::T_DOUBLE_QUOTE:
                while ($this->tokenizer->peek() !== PHP_Depend_Code_TokenizerI::T_DOUBLE_QUOTE) {
                    $tokens[] = $this->tokenizer->next();
                }
                $tokens[] = $this->tokenizer->next();
                break;
                
            case PHP_Depend_Code_TokenizerI::T_BACKTICK:
                while ($this->tokenizer->peek() !== PHP_Depend_Code_TokenizerI::T_BACKTICK) {
                    $tokens[] = $this->tokenizer->next();
                }
                $tokens[] = $this->tokenizer->next();
                break;

            default:
//                throw new RuntimeException("Unknown token '{$token[1]}'.");
                // TODO: Handle/log unused tokens
            }
            
            if ($curly === 0) {
                // Set end line number
                $callable->setEndLine($token[2]);
                // Set all tokens for this function
                $callable->setTokens(array_slice($tokens, 1, count($tokens) - 2));
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
        return PHP_Depend_Code_NodeBuilderI::DEFAULT_PACKAGE;
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
        if ($this->tokenizer->prev() !== PHP_Depend_Code_TokenizerI::T_OPEN_TAG) {
            return false;
        }
        
        $notExpectedTags = array(
            PHP_Depend_Code_TokenizerI::T_CLASS,
            PHP_Depend_Code_TokenizerI::T_FINAL,
            PHP_Depend_Code_TokenizerI::T_ABSTRACT,
            PHP_Depend_Code_TokenizerI::T_FUNCTION,
            PHP_Depend_Code_TokenizerI::T_INTERFACE
        );
        
        return !in_array($this->tokenizer->peek(), $notExpectedTags, true);
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
     * @param PHP_Depend_Code_Property $property The context property instance.
     * 
     * @return void
     */
    private function _prepareProperty(PHP_Depend_Code_Property $property)
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
     * @param PHP_Depend_Code_AbstractCallable $callable The context callable.
     * 
     * @return void
     */
    private function _prepareCallable(PHP_Depend_Code_AbstractCallable $callable)
    {
        // Skip, if ignore annotations is set
        if ($this->_ignoreAnnotations === true) {
            return; 
        }
        
        // Get all @throws Types
        $throws = $this->_parseThrowsAnnotations($callable->getDocComment());
        // Append all exception types
        foreach ($throws as $type) {
            $callable->addExceptionType($this->builder->buildClassOrInterface($type));
        }
        
        // Get return annotation
        $type = $this->_parseTypeAnnotation($callable->getDocComment(), 'return');
        
        if ($type !== null && in_array($type[0], $this->_scalarTypes) === false) {
            $callable->setReturnType($this->builder->buildClassOrInterface($type[0]));
        }
    }
}