<?php
class testCalculatesExpectedNoomMetricForClassWithParent extends ParentCalculatesExpectedNoomMetricForClassWithParent
{
    public function bar() {}
    protected function baz() {}
}

class ParentCalculatesExpectedNoomMetricForClassWithParent
{
    private function foo() {}
    public function bar() {}
    protected function baz() {}
}