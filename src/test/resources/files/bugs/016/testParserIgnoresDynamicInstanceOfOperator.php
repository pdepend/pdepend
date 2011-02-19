<?php
function pdepend($object)
{
    $class = 'SplObjectStorage';
    if ($object instanceof $class) {
        echo "YES";
    }
}