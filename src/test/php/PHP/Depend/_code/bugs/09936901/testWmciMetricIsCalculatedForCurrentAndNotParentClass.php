<?php
class testWmciMetricIsCalculatedForCurrentAndNotParentClass extends testWmciMetricIsCalculatedForCurrentAndNotParentClass_parent
{
    public function foo()
    {
        return 42;
    }

    public function bar()
    {
        return 23;
    }
}

class testWmciMetricIsCalculatedForCurrentAndNotParentClass_parent
{
    public function bar()
    {
        for ($i = 0; $i < 23; ++$i) {
            if ($i % 17 === 3) {
                return $i;
            } else if ($i % 13 === 11) {
                return $i;
            }
        }
        return 23;
    }
}