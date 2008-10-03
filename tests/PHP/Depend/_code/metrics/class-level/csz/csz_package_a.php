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
    private $a;
    protected $b;
    public $c;
    
    private function a() {}
    protected function b() {}
}

/**
 * @package a
 */
class C extends B {
    public function c() {}
}

/**
 * @package a
 */
interface I {
    function c();
}
?>