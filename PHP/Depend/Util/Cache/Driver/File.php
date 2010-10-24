<?php
class PHP_Depend_Util_Cache_Driver_File implements PHP_Depend_Util_Cache_Driver
{
    protected $cacheDir = null;

    protected $type = 'cache';

    public function __construct($cacheDir = '/tmp/pdepend-playground')
    {
        if (false === file_exists($cacheDir)) {
            mkdir($cacheDir, 0775, true);
        }
        $this->cacheDir = $cacheDir;
    }

    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    public function store($key, $data, $hash = null)
    {
        $file = $this->getCacheFile($key);
        $this->write($file, serialize(array('hash' => $hash, 'data' => $data)));
    }

    protected function write($file, $data)
    {
        $handle = fopen($file, 'wb');
        flock($handle, LOCK_EX);
        fwrite($handle, $data);
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    public function restore($key, $hash = null)
    {
        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            return $this->restoreFile($file, $hash);
        }
        return null;
    }

    protected function restoreFile($file, $hash)
    {
        $data = unserialize($this->read($file));
        if ($data['hash'] === $hash) {
            return $data['data'];
        }
        return null;
    }

    protected function read($file)
    {
        $handle = fopen($file, 'rb');
        flock($handle, LOCK_EX);

        $data = fread($handle, filesize($file));

        flock($handle, LOCK_UN);
        fclose($handle);

        return $data;
    }

    protected function getCacheFile($key)
    {
        $file = "{$this->cacheDir}/{$key}.{$this->type}";
        $this->type = 'cache';

        return $file;
    }
}