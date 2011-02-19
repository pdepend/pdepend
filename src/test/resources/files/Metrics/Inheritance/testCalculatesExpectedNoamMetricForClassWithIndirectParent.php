<?php
class testCalculatesExpectedNoamMetricForClassWithIndirectParent extends ParentCalculatesExpectedNoamMetricForClassWithIndirectParent
{
    public function foo() {}
    public function bar() {}
}

class ParentCalculatesExpectedNoamMetricForClassWithIndirectParent extends ParentParentCalculatesExpectedNoamMetricForClassWithIndirectParent
{
}

class ParentParentCalculatesExpectedNoamMetricForClassWithIndirectParent
{
    public function _baz() {}
}