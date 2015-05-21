<?php
function pdepend1($x)
{
    // operands: $value, $x, 1, $value
    // operators: {}, =, +, ;, return, ;
    $value = $x + 1;
    return $value;
}

function pdepend2($x)
{
    // operands: pdepend1, $x
    // operators: {}, return, (), ;
    return pdepend1($x);
}
