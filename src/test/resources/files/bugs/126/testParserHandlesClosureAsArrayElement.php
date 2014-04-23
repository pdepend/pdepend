<?php
function foo()
{
    $a = array(1 ,function() { echo 'hi'; });
    $a[1]();
}
