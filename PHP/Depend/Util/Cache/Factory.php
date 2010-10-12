<?php

class PHP_Depend_Util_Cache_Factory
{
    protected $caches = array();

    /**
     *
     * @param \stdClass $context The context object.
     *
     * @return PHP_Depend_Util_Cache_Driver
     */
    public function create($context)
    {
        $cacheKey = get_class($context);
        if (false === isset($this->caches[$cacheKey])) {
            $this->caches[$cacheKey] = new PHP_Depend_Util_Cache_Driver_File();
        }
        return $this->caches[$cacheKey];
    }
}