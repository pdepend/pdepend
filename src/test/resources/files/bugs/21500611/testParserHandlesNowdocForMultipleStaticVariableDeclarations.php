<?php
class testParserHandlesNowdocForMultipleStaticVariableDeclarationsClass
{
    static function testParserHandlesNowdocForMultipleStaticVariableDeclarations()
    {
        static $x = <<<'FOO'
Hello
FOO
        , $y = <<<'BAR'
 PHP_Depend
BAR;

        echo $x, $y, PHP_EOL;
    }
}
testParserHandlesNowdocForMultipleStaticVariableDeclarationsClass::testParserHandlesNowdocForMultipleStaticVariableDeclarations();
