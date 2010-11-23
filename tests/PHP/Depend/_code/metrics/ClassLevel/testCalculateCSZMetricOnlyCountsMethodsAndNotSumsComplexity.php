<?php
class testCalculateCSZMetricOnlyCountsMethodsAndNotSumsComplexity
{
    public function fooCsz()
    {
        if (true) {
            return 42;
        }
    }

    public function barCsz()
    {
        if (time() % 42) {
            return 42;
        } else if (time() % 23) {
            return 23;
        }
    }
}