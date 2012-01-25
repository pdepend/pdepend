<?php
class testParserHandlesNowdocInStaticVariableDeclarationClass
{
    public static function testParserHandlesNowdocInStaticVariableDeclaration()
    {
        static $foo = <<<'XML'
    xml
XML;
        echo $foo;
    }
}
testParserHandlesNowdocInStaticVariableDeclarationClass::testParserHandlesNowdocInStaticVariableDeclaration();
