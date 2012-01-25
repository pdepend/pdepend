<?php
class testParserHandlesNowdocInParameterDefaultValueClass
{
    static function testParserHandlesNowdocInParameterDefaultValue(
        $x = <<<'FOO'
Hello
FOO
, $y = <<<'BAR'
 PHP_Depend
BAR
    ) {
        echo $x, $y;
    }
}
testParserHandlesNowdocInParameterDefaultValueClass::testParserHandlesNowdocInParameterDefaultValue();
