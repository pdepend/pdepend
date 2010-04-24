<?php
function testNPathComplexityForConditionalsInArrayDeclaration()
{
    $info = array(
        'a' => $p->get(),
        'b' => $p->isPassedByReference() ? true : false,
        'c' => $p->isArray() ? true : false,
        'd' => $p->isOptional() ? true : false,
        'e' => $p->allowsNull() ? true : false
    );
}