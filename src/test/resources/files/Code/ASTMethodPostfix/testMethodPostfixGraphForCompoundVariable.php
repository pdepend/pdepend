<?php
function testMethodPostfixGraphForCompoundVariable($object)
{
    return $object->${'method'}();
}
