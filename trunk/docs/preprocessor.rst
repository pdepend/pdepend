================================
PHP 5.3 Preprocessor description
================================

This document describes the required pre processing step for pdepend to handle
namespaces and the use-as feature in PHP 5.3.

Purpose of the preprocessor
===========================

The purpose of the preprocessor is to offer a layer between the source code and
the source analyzer that substitutes all used class names with their full 
qualified name. This is required because PHP 5.3 allows to rename a class with
the use-as construct to a different class name. It is also possible to use a 
predefined system class within a namespace.

Use cases
=========

Bla bla bla::

    // file Method.php
    <?php
    namespace php::depend::code;
    
    class Method
    {
    
    }
    ?>
    
    // file Parser.php
    <?php
    namespace php::depend;
    
    use php::depend::code::Method;
    
    class Parser
    {
        public function parse()
        {
            $method = new Method();
        }
    }
    ?>
    
    