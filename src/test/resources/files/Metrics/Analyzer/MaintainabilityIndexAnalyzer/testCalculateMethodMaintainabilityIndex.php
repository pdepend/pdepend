<?php
interface MIMethodInterface {
    function pdepend1($x);
    function pdepend2($x);
}


class MIMethodClass implements MIMethodInterface
{
    function pdepend1($x)
    {
        $value = $x + 1;
        return $value;
    }

    function pdepend2($x)
    {
        return $this->pdepend1( $x );
    }

    abstract function pdepend3($x);
}
