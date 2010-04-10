<?php
class A extends B
{
    private $a;
    protected $b;
    public $c;
    private function a() {}
    protected function f() {}
    public function g() {}
    public function h() {}
}
class B extends C
{
    private $a;
    protected $b;
    public $c;
    private function a() {}
    protected function d() {}
    public function e() {}
}
class C
{
    private $a;
    protected $b;
    public $c;
    private function a() {}
    protected function b() {}
    public function c() {}
}
?>
