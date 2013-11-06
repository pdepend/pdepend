<?php
class A extends B
{
    private $d;
    protected $e;
    private function d() {}
    protected function e() {}
}
class B
{
    private $a;
    protected $b;
    public $c;
    private function a() {}
    protected function b() {}
    public function c() {}
}
?>
