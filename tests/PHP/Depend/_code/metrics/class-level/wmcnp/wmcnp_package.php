<?php
/**
 * @package a
 */
class A {
    private $a;
    public $c;
    
    private function a() { if ($a) {} }
}

/**
 * @package a
 */
class B extends A {
    public $a;
    protected $b;
    public $d;
    
    public function a() { if ($this->b || $this->d) {} }
    protected function b() { }
}

/**
 * @package a
 */
class C extends B {
    private $d = null;
    public function c() { if (true) {} else if (true && false) {} }
}

/**
 * @package a
 */
interface I {
    function c();
}
?>