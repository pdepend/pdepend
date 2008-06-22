<?php
function pdepend($object)
{
    if ($object instanceof SplObjectStorage) {
        echo "YES";
    }
}