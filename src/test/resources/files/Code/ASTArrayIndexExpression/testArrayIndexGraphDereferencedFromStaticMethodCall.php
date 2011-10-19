<?php
function testArrayIndexGraphDereferencedFromStaticMethodCall()
{
    $x = Clazz::method()[42];
    return pow( $x, 2 );
}
