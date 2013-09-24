<?php
class testPreIncrementExpressionsInArithmeticOperation
{
    function getFoo($param)
    {
        return (++$param * ++$param);
    }
}
