<?php
abstract class PHP_Depend_ParserTest extends PHP_Depend_AbstractTest
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
        $sourceFile = dirname(__FILE__) . '/my/test/file.php';
        $tokenizer  = new PHP_Depend_Tokenizer_Internal($sourceFile);
        $builder    = new PHP_Depend_Builder_Default();
        $parser     = new PHP_Depend_Parser($tokenizer, $builder);
        
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
     * @return PHP_Depend_Code_NodeIterator
     */
    protected abstract function parseMixedCode(); 
}