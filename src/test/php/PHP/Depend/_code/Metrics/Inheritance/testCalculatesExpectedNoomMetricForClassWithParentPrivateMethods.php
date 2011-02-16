<?php
class testCalculatesExpectedNoomMetricForClassWithParentPrivateMethods extends ParentCalculatesExpectedNoomMetricForClassWithParentPrivateMethods
{
    private function foo() {}
    private function bar() {}
}

class ParentCalculatesExpectedNoomMetricForClassWithParentPrivateMethods
{
    private function foo() {}
}