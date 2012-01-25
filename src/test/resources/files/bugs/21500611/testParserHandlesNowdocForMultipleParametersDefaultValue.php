<?php
class testParserHandlesNowdocForMultipleParametersDefaultValueClass
{
    static function testParserHandlesNowdocForMultipleParametersDefaultValue(
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
testParserHandlesNowdocForMultipleParametersDefaultValueClass::testParserHandlesNowdocForMultipleParametersDefaultValue();
