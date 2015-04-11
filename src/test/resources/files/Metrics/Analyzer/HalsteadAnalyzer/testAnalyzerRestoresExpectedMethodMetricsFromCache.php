<?php
interface HMethodInterface {
    function pdepend1($x);
    function pdepend2($x);
}


class HMethodClass implements HMethodInterface
{
    function pdepend1($x)
    {
        // operands: $value, $x, 1, $value
        // operators: {}, =, +, ;, return, ;
        $value = $x + 1;
        return $value;
    }

    function pdepend2($x)
    {
        // operands: $this->pdepend1, $x
        // operators: {}, return, (), ;
        return $this->pdepend1( $x );
    }
}
