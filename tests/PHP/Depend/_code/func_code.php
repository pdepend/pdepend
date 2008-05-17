<?php
function foo($class) {
    $y = new $class();
}

$x = foo('Mapi');
call_user_func(array($x, 'foo'));
?>