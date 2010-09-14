<?php
interface PHP_Depend_Code_Tokenizer
{    
    const T_EOF = -1;
    
    const T_CLASS = 1;
    
    const T_INTERFACE = 2;
    
    const T_ABSTRACT = 3;
    
    const T_CURLY_BRACE_OPEN = 4;
    
    const T_CURLY_BRACE_CLOSE = 5;
    
    const T_PARENTHESIS_OPEN = 6;
    
    const T_PARENTHESIS_CLOSE = 7;
    
    const T_NEW = 8;
    
    const T_FUNCTION = 9;
    
    const T_DOUBLE_COLON = 10;
    
    const T_STRING = 11;
    
    const T_DOC_COMMENT = 12;
    
    function next();
    
    function peek();
}