<?php
class testCalculatesExpectedNoamMetricForClassWithDirectParent extends ParentCalculatesExpectedNoamMetricForClassWithDirectParent
{
    public function foo() {}
    public function bar() {}
}

class ParentCalculatesExpectedNoamMetricForClassWithDirectParent
{
    public function _baz() {}
}