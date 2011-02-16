<?php
class testCalculateCISMetricOnlyCountsMethodsAndNotSumsComplexity
{
    public function fooCis()
    {
        if (true) {
            return 42;
        }
    }

    public function barCis()
    {
        if (time() % 42) {
            return 42;
        } else if (time() % 23) {
            return 23;
        }
    }
}