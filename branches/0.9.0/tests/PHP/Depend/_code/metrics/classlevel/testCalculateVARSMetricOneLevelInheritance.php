<?php
class A extends B
{
    private function a() {}
    protected function b() {}
    public function c() {}
    private $a;
    protected $b;
    public $c;
}
class B
{
    public $d = 42;
}
?>
