<?php
/**
 * @package a
 */
class A {
    private $a;
    public $c;
    
    private function a() {}
}

/**
 * @package a
 */
class B extends A {
    public $a;
    protected $b;
    public $d;
    
    private function a() {}
    protected function b() {}
}

/**
 * @package a
 */
class C extends B {
    private $d = null;
    public function c() {}
}

/**
 * @package a
 */
interface I {
    function c();
}
?>