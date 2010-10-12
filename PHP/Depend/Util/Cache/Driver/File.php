<?php
class PHP_Depend_Util_Cache_Driver_File implements PHP_Depend_Util_Cache_Driver
{
    protected $cacheDir = null;

    public function __construct($cacheDir = '/tmp/pdepend-playground')
    {
        if (false === file_exists($cacheDir)) {
            mkdir($cacheDir, 0775, true);
        }
        $this->cacheDir = $cacheDir;
    }

    public function store($key, $data, $hash = null)
    {
        $file = "{$this->cacheDir}/{$key}.cache";
        file_put_contents($file, serialize(array('hash' => $hash, 'data' => $data)));
    }

    public function restore($key, $hash = null)
    {
        $file = "{$this->cacheDir}/{$key}.cache";
        if (file_exists($file)) {
            return $this->restoreFile($file, $hash);
        }
        return null;
    }

    protected function restoreFile($file, $hash)
    {
        $data = unserialize(file_get_contents($file));
        if ($data['hash'] === $hash) {
            return $data['data'];
        }
        return null;
    }
}