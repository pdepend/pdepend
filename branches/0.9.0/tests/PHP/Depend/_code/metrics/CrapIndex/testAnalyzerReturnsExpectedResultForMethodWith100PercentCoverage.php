<?php
class testAnalyzerReturnsExpectedResultForMethodWith100PercentCoverage
{
    function foo()
    {
        $x = (true ? ($y ? 42 : ($z || $foo ? ($a && $b) : 17 )) : 23);
    }
}