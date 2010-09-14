<?php
require_once dirname(__FILE__) . '/../Tokenizer.php';

class PHP_Depend_Code_Tokenizer_InternalTokenizer implements PHP_Depend_Code_Tokenizer
{
    protected static $tokenMap = array(
        T_NEW           =>  self::T_NEW,
        T_CLASS         =>  self::T_CLASS,
        T_STRING        =>  self::T_STRING,
        T_FUNCTION      =>  self::T_FUNCTION,
        T_ABSTRACT      =>  self::T_ABSTRACT,
        T_INTERFACE     =>  self::T_INTERFACE,
        T_CURLY_OPEN    =>  self::T_CURLY_BRACE_OPEN,
        T_DOC_COMMENT   =>  self::T_DOC_COMMENT,
        T_DOUBLE_COLON  =>  self::T_DOUBLE_COLON,
    );
    
    protected static $literalMap = array(
        ';'  =>  self::T_SEMICOLON,
        '{'  =>  self::T_CURLY_BRACE_OPEN,
        '}'  =>  self::T_CURLY_BRACE_CLOSE,
        '('  =>  self::T_PARENTHESIS_OPEN,
        ')'  =>  self::T_PARENTHESIS_CLOSE,
    );
    
    protected static $ignoreMap = array(
        'null'    =>  true,
        'array'   =>  true,
        'parent'  =>  true
//        'self'   =>  true,
    );
    
    protected $count = 0;
    
    protected $index = 0;
    
    protected $tokens = array();
    
    public function __construct($fileName)
    {
        $this->tokenize($fileName);
    }
    
    public function next()
    {
        if ($this->index < $this->count) {
            return $this->tokens[$this->index++];
        }
        return self::T_EOF;
    }
    
    public function peek()
    {
        if ($this->index < $this->count) {
            return $this->tokens[$this->index][0];
        }
        return self::T_EOF;
    }
    
    protected function tokenize($fileName)
    {
        $source = file_get_contents($fileName);
        $tokens = token_get_all($source);
        
        foreach ($tokens as $token) {
            if (is_string($token)) {
                if (!isset(self::$literalMap[$token])) {
                    continue;
                }
                $token = array(self::$literalMap[$token], $token);
            } else {
                $value = strtolower($token[1]);
                if (isset(self::$ignoreMap[$value]) 
                || !isset(self::$tokenMap[$token[0]])) {
                    
                    continue;
                }
                $token = array(self::$tokenMap[$token[0]], $token[1]);
            }
            
            $this->tokens[] = $token;
        }
        
        $this->count = count($this->tokens);
    }
}