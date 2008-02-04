<?php

require_once 'PHP/Depend/Code/NodeBuilder.php';
require_once 'PHP/Depend/Code/Tokenizer.php';

class PHP_Depend_Parser
{
    protected $package = '';
    
    protected $abstract = false;
    
    protected $className = '';
    
    /**
     * The used code tokenizer.
     *
     * @type PHP_Depend_Code_Tokenizer 
     * @var PHP_Depend_Code_Tokenizer $tokenizer
     */
    protected $tokenizer = null;
    
    /**
     * The used data structure builder.
     * 
     * @type PHP_Depend_Code_NodeBuilder
     * @var PHP_Depend_Code_NodeBuilder $builder
     */
    protected $builder = null;
    
    public function __construct(PHP_Depend_Code_Tokenizer $tokenizer, PHP_Depend_Code_NodeBuilder $builder)
    {
        $this->tokenizer = $tokenizer;
        $this->builder   = $builder;
    }
    
    public function parse()
    {
        $this->reset();
        
        while (($token = $this->tokenizer->next()) !== PHP_Depend_Code_Tokenizer::T_EOF) {
            
            switch ($token[0]) {
                case PHP_Depend_Code_Tokenizer::T_ABSTRACT:
                    $this->abstract = true;
                    break;
                    
                case PHP_Depend_Code_Tokenizer::T_DOC_COMMENT:
                    $this->package = $this->parsePackage($token[1]);
                    break;
                    
                case PHP_Depend_Code_Tokenizer::T_INTERFACE:
                    $this->abstract = true;
                    
                case PHP_Depend_Code_Tokenizer::T_CLASS:
                    // Get class name
                    $token = $this->tokenizer->next();
                    
                    $this->className = $token[1];
                    
                    $class = $this->builder->buildClass($this->className);
                    $class->setAbstract($this->abstract);
                    foreach ($this->parseClassSignature() as $dependency) {
                        $class->addDependency(
                            $this->builder->buildClass($dependency)
                        );
                    }
                    $this->builder->buildPackage($this->package)->addClass($class);


                    $this->parseClassBody();
                    
                    $this->reset();
                    break;
                    
                case PHP_Depend_Code_Tokenizer::T_FUNCTION:
                    $this->parseFunction();
                    break;
                    
                default:
                    if ($this->className !== null) {
                        throw new RuntimeException('Invalid state' . var_export($token, true));
                    }
            }
        }
    }
    
    protected function reset()
    {
        $this->package      = null;
        $this->abstract     = false;
        $this->className    = null;
    }
    
    protected function parseClassSignature()
    {
        $dependencies = array();
        while ($this->tokenizer->peek() !== PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN) {
            $token = $this->tokenizer->next();
            if ($token[0] === PHP_Depend_Code_Tokenizer::T_STRING) {
                $dependencies[] = $token[1];
            }
        }
        return $dependencies;
    }
    
    protected function parseClassBody()
    {
        $token = $this->tokenizer->next();
        $curly = 0;
        
        while ($token !== PHP_Depend_Code_Tokenizer::T_EOF) {
            
            switch ($token[0]) {
                case PHP_Depend_Code_Tokenizer::T_FUNCTION:
                    $this->parseFunction();
                    break;
                    
                case PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN:
                    ++$curly;
                    break;
                    
                case PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE:
                    --$curly;
                    break;
            }
            
            if ($curly === 0) {
                return;
            }
            
            $token = $this->tokenizer->next();
        }
    }
    
    protected function parseFunction()
    {
        $token = $this->tokenizer->next();
 
        $dependencies = $this->parseFunctionSignature();
        if ($this->tokenizer->peek() === PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN) {
            // Get function body dependencies 
            $dependencies = array_merge(
                $dependencies, $this->parseFunctionBody()
            );
        }
        
        $dependencies = array_map('trim', $dependencies);
        array_filter($dependencies);
        array_unique($dependencies);
        
        if ($this->className === null) {
            $function = $this->builder->buildFunction($token[1]);
            $this->builder->buildPackage($this->package)->addFunction($function); 
        } else {
            $function = $this->builder->buildMethod($token[1]);
            $this->builder->buildClass($this->className)->addMethod($function);
        }

        foreach ($dependencies as $dependency) {
            $function->addDependency(
                $this->builder->buildClass($dependency)
            );
        }
        
        //echo "FUNCTION: {$function}\n";print_r($dependencies);echo "\n";
    }
    
    protected function  parseFunctionSignature()
    {
        while ($this->tokenizer->peek() !== PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN) {
            $this->tokenizer->next();
        }
        
        $token = $this->tokenizer->next();

        $parenthesis  = 1;
        $dependencies = array();
        
        while (($token = $this->tokenizer->next()) !== null) {
if ($this->className==='mxpControllerCommandManager') {
    var_dump($token);
}
            switch ($token[0]) {
                case PHP_Depend_Code_Tokenizer::T_PARENTHESIS_OPEN:
                    ++$parenthesis;
                    break;
                    
                case PHP_Depend_Code_Tokenizer::T_PARENTHESIS_CLOSE:
                    --$parenthesis;
                    break;
                    
                case PHP_Depend_Code_Tokenizer::T_STRING:
                    $dependencies[] = $token[1];
                    break;
            }
            
            if ($parenthesis === 0) {
                break;
            }
            
        }
        
        return $dependencies;
    }
    
    protected function parseFunctionBody()
    {
        $curly = 0;

        $dependencies = array();

        while ($this->tokenizer->peek() !== PHP_Depend_Code_Tokenizer::T_EOF) {
            
            $token = $this->tokenizer->next();

            switch ($token[0]) {
                case PHP_Depend_Code_Tokenizer::T_NEW:
                    // Check that the next token is a string
                    if ($this->tokenizer->peek() === PHP_Depend_Code_Tokenizer::T_STRING) {
                        $token          = $this->tokenizer->next();
                        $dependencies[] = $token[1];
                    }
                    break;
                    
                case PHP_Depend_Code_Tokenizer::T_STRING:
                    if ($this->tokenizer->peek() === PHP_Depend_Code_Tokenizer::T_DOUBLE_COLON) {
                        // Skip double colon
                        $this->tokenizer->next();
                        // Check for method call
                        if ($this->tokenizer->peek() === PHP_Depend_Code_Tokenizer::T_STRING) {
                            // Skip method call
                            $this->tokenizer->next();
                        }
                    }
                    break;
                    
                case PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_OPEN:
                    ++$curly;
                    break;
                    
                case PHP_Depend_Code_Tokenizer::T_CURLY_BRACE_CLOSE:
                    --$curly;
                    break;
            }
            
            if ($curly === 0) {
                return $dependencies;
            }
        }

        throw new RuntimeException('Invalid state.');
    }
    
    protected function parsePackage($comment)
    {
        if (preg_match('#\*\s*@package\s+(.*)#', $comment, $match)) {
            return trim($match[1]);
        }
        return PHP_Depend_Code_NodeBuilder::DEFAULT_PACKAGE;
    }    
}