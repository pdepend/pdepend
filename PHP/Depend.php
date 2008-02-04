<?php
require_once 'PHP/Depend/Parser.php';
require_once 'PHP/Depend/Code/DefaultBuilder.php';
require_once 'PHP/Depend/Code/Tokenizer/InternalTokenizer.php';
require_once 'PHP/Depend/Util/PHPFilterIterator.php';

class PHP_Depend
{
    protected $directories = array();
    
    protected $packages = null;

    public function addDirectory($directory)
    {
        $dir = realpath($directory);
        
        if (!is_dir($dir)) {
            throw new RuntimeException('Invalid directory added.');
        }
        
        $this->directories[] = $dir;
    }
    
    public function analyze()
    {
        $iterator = new AppendIterator();
        
        foreach ($this->directories as $directory) {
            $iterator->append(
                new PHP_Depend_Util_PHPFilterIterator( 
                    new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($directory)
                    )
                )
            );
        }
        
        $builder = new PHP_Depend_Code_DefaultBuilder();

        foreach ( $iterator as $file ) 
        {
            $parser = new PHP_Depend_Parser(
                new PHP_Depend_Code_Tokenizer_InternalTokenizer($file), $builder
            );
            $parser->parse();
        }

        $visitor = new PHP_Depend_Metrics_PackageMetricsVisitor();

        foreach ($builder as $pkg) {
            $pkg->accept($visitor);
        }
        $this->packages = $visitor->getPackageMetrics();
        
        return $this->packages;
    }
    
    public function countClasses()
    {
        if ($this->packages === null) {
            throw new RuntimeException('Invalid state');
        }
        
        $classes = 0;
        foreach ($this->packages as $package) {
            $classes += $package->getTC();
        }
        return $classes;
    }
    
    public function getPackage($name)
    {
        if ($this->packages === null) {
            throw new RuntimeException('Invalid state');
        }
        foreach ($this->packages as $package) {
            if ($package->getName() === $name) {
                return $package;
            }
        }
        return null;
    }
    
    public function getPackages()
    {
        if ($this->packages === null) {
            throw new RuntimeException('Invalid state');
        }
        return $this->packages;
    }
    
    public function countPackages()
    {
        if ($this->packages === null) {
            throw new RuntimeException('Invalid state');
        }
        // TODO: This is internal knownhow, it is an ArrayIterator
        //       Replace it with a custom iterator interface
        return $this->packages->count();
    }
}