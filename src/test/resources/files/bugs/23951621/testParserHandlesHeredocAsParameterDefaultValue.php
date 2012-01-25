<?php
class testParserHandlesHeredocAsParameterDefaultValue
{
    static function foo($test = <<<TEST
Testing
TEST
    ) {
       echo $test;
    }
}

testParserHandlesHeredocAsParameterDefaultValue::foo();
