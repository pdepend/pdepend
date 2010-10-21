<?php
class PHP_Depend_Util_Cache_Driver_Memory implements PHP_Depend_Util_Cache_Driver
{
    protected $objects = array();

    public function store($key, $data, $hash = null)
    {
        $this->objects[$key] = array($hash, $data);
    }

    public function restore($key, $hash = null)
    {
        if (isset($this->objects[$key]) && $this->objects[$key][0] === $hash) {
            return $this->objects[$key][1];
        }
        return null;
    }
}