<?php
interface PHP_Depend_Util_Cache_Driver
{
    function store($key, $data, $hash = null);

    function restore($key, $hash = null);
}