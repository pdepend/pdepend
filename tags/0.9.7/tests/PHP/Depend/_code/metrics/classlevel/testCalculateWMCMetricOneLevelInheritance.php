<?php
class A extends B {
    private function a() {}
    protected function b() {}
    public function c() {}
}

class B {
    public $c;
    private function a() {}
    protected function b() {}
    public function c() {}
}
?>
