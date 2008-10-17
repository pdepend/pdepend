<?php
abstract class PHP_Reflection_ParserTest extends PHP_Reflection_AbstractTest
{
    /**
     * Tests that the parser ignores backtick expressions. 
     * 
     * http://bugs.xplib.de/index.php?do=details&task_id=15&project=3
     *
     * @return void
     */
    public function testParserBacktickExpressionBug15()
    {
        $sourceFile = dirname(__FILE__) . '/_code/bugs/15.php';
        $tokenizer  = new PHP_Reflection_Tokenizer_Internal($sourceFile);
        $builder    = new PHP_Reflection_Builder_Default();
        $parser     = new PHP_Reflection_Parser($tokenizer, $builder);
        
        $parser->parse();
        
        $package = $builder->getPackages()->current();
        $classes = $package->getClasses();
        
        $this->assertEquals(1, $classes->count());
        $methods = $classes->current()->getMethods();
        $this->assertEquals(1, $methods->count());
    }
    
    /**
     * Returns all packages in the mixed code example.
     *
     * @return PHP_Reflection_AST_Iterator
     */
    protected abstract function parseMixedCode(); 
}