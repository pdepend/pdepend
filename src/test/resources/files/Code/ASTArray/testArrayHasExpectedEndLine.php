<?php
function testArrayHasExpectedEndLine($a, $b, $c)
{
    return array(
        1, 2 => 3,
        'foo' => array(
            &$a,
            23 => &$b,
            array(
                $c => 42
            )
        )
    );
}
