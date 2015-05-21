<?php
function pdepend1($x)
{
    $value = $x + 1;
    return $value;
}

function pdepend2($x)
{
    return pdepend1($x);
}
