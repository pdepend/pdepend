<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2009, Manuel Pichler <mapi@pdepend.org>.
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
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://www.manuel-pichler.de/
 */

require_once 'PHP/Depend/ConstantsI.php';
require_once 'PHP/Depend/Code/VisibilityAwareI.php';
require_once 'PHP/Depend/BuilderI.php';
require_once 'PHP/Depend/TokenizerI.php';

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
 * @copyright 2008-2009 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://www.manuel-pichler.de/
 */
class PHP_Depend_Parser implements PHP_Depend_ConstantsI
{
    /**
     * Last parsed package tag.
     *
     * @var string $package
     */
    protected $package = self::DEFAULT_PACKAGE;

    /**
     * The package defined in the file level comment.
     *
     * @var string $globalPackage
     */
    protected $globalPackage = self::DEFAULT_PACKAGE;

    /**
     * The package separator token.
     *
     * @var string $packageSeparator
     */
    protected $packageSeparator = '\\';

    /**
     * Marks the current class as abstract.
     *
     * @var boolean $abstract
     */
    protected $abstract = false;

    /**
     * The used code tokenizer.
     *
     * @var PHP_Depend_TokenizerI $tokenizer
     */
    protected $tokenizer = null;

    /**
     * The used data structure builder.
     *
     * @var PHP_Depend_BuilderI $builder
     */
    protected $builder = null;

    /**
     * List of scalar php types.
     *
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
     * @var boolean $_ignoreAnnotations
     */
    private $_ignoreAnnotations = false;

    /**
     * Constructs a new source parser.
     *
     * @param PHP_Depend_TokenizerI   $tokenizer The used code tokenizer.
     * @param PHP_Depend_BuilderI $builder   The used node builder.
     */
    public function __construct(PHP_Depend_TokenizerI $tokenizer,
                                PHP_Depend_BuilderI $builder)
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

        // Position of the context type within the analyzed file.
        $typePosition = 0;

        while (($token = $this->tokenizer->next()) !== self::T_EOF) {

            switch ($token->type) {
            case self::T_ABSTRACT:
                $this->abstract = true;
                break;

            case self::T_DOC_COMMENT:
                $comment       = $token->image;
                $this->package = $this->parsePackage($token->image);

                // Check for doc level comment
                if ($this->globalPackage === self::DEFAULT_PACKAGE
                 && $this->isFileComment() === true) {

                    $this->globalPackage = $this->package;

                    $this->tokenizer->getSourceFile()->setDocComment($token->image);

                    // TODO: What happens if there is no file comment, we will
                    //       reuse the same comment for a class, interface or
                    //       function?
                }
                break;

            case self::T_INTERFACE:
                // Get interface name
                $token = $this->tokenizer->next();

                $qualifiedName = sprintf('%s%s%s',
                                    $this->package,
                                    $this->packageSeparator,
                                    $token->image);

                $interface = $this->builder->buildInterface($qualifiedName, $token->startLine);
                $interface->setSourceFile($this->tokenizer->getSourceFile());
                $interface->setStartLine($token->startLine);
                $interface->setDocComment($comment);
                $interface->setPosition($typePosition++);

                $this->parseInterfaceSignature($interface);

                $this->builder->buildPackage($this->package)->addType($interface);

                $this->parseTypeBody($interface);
                $this->reset();

                $comment = null;
                break;

            case self::T_CLASS:
                // Get class name
                $token = $this->tokenizer->next();

                $qualifiedName = sprintf('%s%s%s',
                                    $this->package,
                                    $this->packageSeparator,
                                    $token->image);

                $class = $this->builder->buildClass($qualifiedName, $token->startLine);
                $class->setSourceFile($this->tokenizer->getSourceFile());
                $class->setStartLine($token->startLine);
                $class->setAbstract($this->abstract);
                $class->setDocComment($comment);
                $class->setPosition($typePosition++);

                $this->parseClassSignature($class);

                $this->builder->buildPackage($this->package)->addType($class);

                $this->parseTypeBody($class);
                $this->reset();

                $comment = null;
                break;

            case self::T_FUNCTION:
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
        $this->package  = self::DEFAULT_PACKAGE;
        $this->abstract = false;
    }

    /**
     * Parses the dependencies in a interface signature.
     *
     * @param PHP_Depend_Code_Interface $interface The context interface instance.
     *
     * @return array(array)
     */
    protected function parseInterfaceSignature(PHP_Depend_Code_Interface $interface)
    {
        $tokens = array();
        while ($this->tokenizer->peek() !== self::T_CURLY_BRACE_OPEN) {
            $token    = $this->tokenizer->next();
            $tokens[] = $token;

            if ($token->type === self::T_STRING) {
                $dependency = $this->builder->buildInterface($token->image);
                $interface->addDependency($dependency);
            }
        }
        return $tokens;
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
        $tokens     = array();
        $implements = false;

        while ($this->tokenizer->peek() !== self::T_CURLY_BRACE_OPEN) {
            $token    = $this->tokenizer->next();
            $tokens[] = $token;

            if ($token->type === self::T_IMPLEMENTS) {
                $implements = true;
            } else if ($token->type === self::T_STRING) {
                if ($implements) {
                    $dependency = $this->builder->buildInterface($token->image);
                } else {
                    $dependency = $this->builder->buildClass($token->image);
                }
                // Set class dependency
                $class->addDependency($dependency);
            }
        }

        return $tokens;
    }

    /**
     * Parses a class/interface body.
     *
     * @param PHP_Depend_Code_AbstractType $type The context type instance.
     *
     * @return array(array)
     */
    protected function parseTypeBody(PHP_Depend_Code_AbstractType $type)
    {
        $token = $this->tokenizer->next();
        $curly = 0;

        $tokens = array($token);

        // If type is an interface all methods are abstract
        $abstractDefault = ($type instanceof PHP_Depend_Code_Interface);

        $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC;
        $comment   = null;
        $abstract  = $abstractDefault;

        // Method position within the type body
        $methodPosition = 0;

        while ($token !== self::T_EOF) {

            switch ($token->type) {
            case self::T_FUNCTION:
                $method = $this->parseCallable($tokens, $type);
                $method->setDocComment($comment);
                $method->setAbstract($abstract);
                $method->setVisibility($visibilty);
                $method->setPosition($methodPosition++);

                $this->_prepareCallable($method);

                $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC;
                $comment   = null;
                $abstract  = $abstractDefault;
                break;

            case self::T_VARIABLE:
                $property = $this->builder->buildProperty($token->image, $token->startLine);
                $property->setDocComment($comment);
                $property->setVisibility($visibilty);
                $property->setEndLine($token->startLine);

                $this->_prepareProperty($property);

                // TODO: Do we need an instanceof, to check that $type is a
                //       PHP_Depend_Code_Class instance or do we believe the
                //       code is correct?
                $type->addProperty($property);

                $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC;
                $comment   = null;
                $abstract  = $abstractDefault;
                break;

            case self::T_CONST:
                $token    = $this->tokenizer->next();
                $tokens[] = $token;

                $constant = $this->builder->buildTypeConstant($token->image);
                $constant->setDocComment($comment);
                $constant->setStartLine($token->startLine);
                $constant->setEndLine($token->startLine);

                $type->addConstant($constant);

                $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC;
                $comment   = null;
                $abstract  = $abstractDefault;
                break;

            case self::T_CURLY_BRACE_OPEN:
                ++$curly;
                $comment = null;
                break;

            case self::T_CURLY_BRACE_CLOSE:
                --$curly;
                $comment = null;
                break;

            case self::T_ABSTRACT:
                $abstract = true;
                break;

            case self::T_PUBLIC:
                $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PUBLIC;
                break;

            case self::T_PRIVATE:
                $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PRIVATE;
                break;

            case self::T_PROTECTED:
                $visibilty = PHP_Depend_Code_VisibilityAwareI::IS_PROTECTED;
                break;

            case self::T_STATIC:
                break;

            case self::T_FINAL:
                break;

            case self::T_DOC_COMMENT:
                $comment = $token->image;
                break;

            default:
                // TODO: Handle/log unused tokens
                $comment = null;
                break;
            }

            if ($curly === 0) {
                // Set end line number
                $type->setEndLine($token->startLine);
                // Set type tokens
                $type->setTokens($tokens);
                // Stop processing
                break;
            } else {
                $token    = $this->tokenizer->next();
                $tokens[] = $token;
            }
        }

        if ($curly !== 0) {
            $fileName = (string) $this->tokenizer->getSourceFile();
            $message  = "Invalid state, unclosed class body in file '{$fileName}'.";
            throw new RuntimeException($message);
        }

        return $tokens;
    }

    /**
     * Parses a function or a method and adds it to the parent context node.
     *
     * @param array(array)                 &$tokens Collected tokens.
     * @param PHP_Depend_Code_AbstractType $parent  An optional parent interface of class.
     *
     * @return PHP_Depend_Code_AbstractCallable
     */
    protected function parseCallable(array &$tokens = array(), PHP_Depend_Code_AbstractType $parent = null)
    {
        $this->_consumeComments($tokens);
        $token    = $this->tokenizer->next();
        $tokens[] = $token;

        if ($token->type === self::T_BITWISE_AND) {
            $this->_consumeComments($tokens);
            $token    = $this->tokenizer->next();
            $tokens[] = $token;
        }

        $callable = null;
        if ($parent === null) {
            $callable = $this->builder->buildFunction($token->image, $token->startLine);

            $package = $this->globalPackage;
            if ($this->package !== self::DEFAULT_PACKAGE) {
                $package = $this->package;
            }

            $this->builder->buildPackage($package)->addFunction($callable);
        } else {
            $callable = $this->builder->buildMethod($token->image, $token->startLine);
            $parent->addMethod($callable);
        }

        $this->parseCallableSignature($tokens, $callable);
        if ($this->tokenizer->peek() === self::T_CURLY_BRACE_OPEN) {
            // Get function body dependencies
            $this->parseCallableBody($tokens, $callable);
        } else {
            $callable->setEndLine($token->startLine);
        }

        return $callable;
    }

    /**
     * Extracts all dependencies from a callable signature.
     *
     * @param array(array)                     &$tokens  Collected tokens.
     * @param PHP_Depend_Code_AbstractCallable $callable The context callable.
     *
     * @return void
     */
    protected function parseCallableSignature(array &$tokens, PHP_Depend_Code_AbstractCallable $callable)
    {
        $this->_consumeComments($tokens);

        if ($this->tokenizer->peek() !== self::T_PARENTHESIS_OPEN) {
            // Load invalid token for line number
            $token    = $this->tokenizer->next();
            $tokens[] = $token;

            // Throw a detailed exception message
            throw new RuntimeException(
                sprintf(
                    'Invalid token "%s" on line %s in file: %s.',
                    $token->image,
                    $token->startLine,
                    $this->tokenizer->getSourceFile()
                )
            );
        }

        $parameterType     = null;
        $parameterPosition = 0;

        $parenthesis = 0;

        while (($token = $this->tokenizer->next()) !== self::T_EOF) {

            $tokens[] = $token;

            switch ($token->type) {
            case self::T_PARENTHESIS_OPEN:
                ++$parenthesis;
                $parameterType = null;
                break;

            case self::T_PARENTHESIS_CLOSE:
                --$parenthesis;
                $parameterType = null;
                break;

            case self::T_STRING:
                // Check that the next token is a variable or next token is the
                // reference operator and the fo
                if ($this->tokenizer->peek() !== self::T_VARIABLE
                 && $this->tokenizer->peek() !== self::T_BITWISE_AND) {
                    continue;
                }
                if ($this->tokenizer->peek() === self::T_BITWISE_AND) {
                    // Store reference operator
                    $tokens[] = $this->tokenizer->next();
                    // Check next token
                    if ($this->tokenizer->peek() !== self::T_VARIABLE) {
                        continue;
                    }
                }

                // Create an instance for this parameter
                $parameterType = $this->builder->buildClassOrInterface($token->image);
                break;

            case self::T_VARIABLE:
                $parameter = $this->builder->buildParameter($token->image, $token->startLine);
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
     * @param array(array)                     &$outTokens Collected tokens.
     * @param PHP_Depend_Code_AbstractCallable $callable   The context callable.
     *
     * @return void
     */
    protected function parseCallableBody(array &$outTokens, PHP_Depend_Code_AbstractCallable $callable)
    {
        $curly  = 0;
        $tokens = array();

        while ($this->tokenizer->peek() !== self::T_EOF) {

            $tokens[] = $token = $this->tokenizer->next();

            switch ($token->type) {
            case self::T_CATCH:
                // Skip open parenthesis
                $tokens[] = $this->tokenizer->next();

            case self::T_NEW:
            case self::T_INSTANCEOF:
                $parts = $this->_parseClassNameChain($tokens);

                // If this is a dynamic instantiation, do not add dependency.
                // Something like: new $className('PDepend');
                if (count($parts) > 0) {
                    // Get last element of parts and create a class for it
                    $class = $this->builder->buildClass(join('\\', $parts));
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
                        $dependency = $this->builder->buildClassOrInterface($token->image);

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
                $this->_skipEncapsultedBlock($tokens, $token->type);
                break;

            default:
                // throw new RuntimeException("Unknown token '{$token->image}'.");
                // TODO: Handle/log unused tokens
            }

            if ($curly === 0) {
                // Set end line number
                $callable->setEndLine($token->startLine);
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
        return self::DEFAULT_PACKAGE;
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
     * PHP\Depend\Parser::parse();
     * </code>
     *
     * @param array(array) &$tokens The tokens array.
     *
     * @return array(array)
     */
    private function _parseClassNameChain(&$tokens)
    {
        $type  = $this->tokenizer->peek();
        $parts = array();
        while ($type === self::T_BACKSLASH || $type === self::T_STRING) {
            $token    = $this->tokenizer->next();
            $tokens[] = $token;

            if ($token->type === self::T_STRING) {
                $parts[] = $token->image;
            }

            $type = $this->tokenizer->peek();
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

    /**
     * This method will consume all comment tokens from the token stream.
     *
     * @param array &$tokens Optional token storage array.
     *
     * @return integer
     */
    private function _consumeComments(&$tokens = array())
    {
        $comments = array(self::T_COMMENT, self::T_DOC_COMMENT);

        while (($type = $this->tokenizer->peek()) !== self::T_EOF) {
            if (in_array($type, $comments, true) === false) {
                break;
            }
            $tokens[] = $this->tokenizer->next();
        }
        return count($tokens);
    }
}
?>
