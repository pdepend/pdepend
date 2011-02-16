<?php
class A extends B {
    private $a;
    private function a() {}
    protected function b() {}
    public function c() {}
}
class B extends C {
    private function a() {}
    protected function b() {}
    public function c() {}
}

class C {
    public $c;
    private function a() {}
    protected function b() {}
    public function c() {}
}
?>
