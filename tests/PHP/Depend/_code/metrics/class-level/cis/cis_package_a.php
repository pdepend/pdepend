<?php
/**
 * @package a
 */
class A {
    private $a;
    protected $b;
    public $c;
    
    private function a() {}
    protected function b() {}
    public function c() {}
}

/**
 * @package a
 */
class B {
    private $a;
    protected $b;
    public $c;
    
    private function a() {}
    protected function b() {}
    public function c() {}
}

/**
 * @package a
 */
class C {
    public $a;
    protected $b;
    public $c;
    
    private function a() {}
    protected function b() {}
    public function c() {}
}
?>