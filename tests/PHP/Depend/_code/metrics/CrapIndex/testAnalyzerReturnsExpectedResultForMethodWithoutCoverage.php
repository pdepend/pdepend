<?php
class testAnalyzerReturnsExpectedResultForMethodWithoutCoverage
{
    public function foo()
    {
        $x = (true ? ($y ? 42 : ($z || $foo ? ($a && $b) : 17 )) : 23);
    }
}