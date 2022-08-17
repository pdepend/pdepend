<?php

class Clock {
    public function getClockCallable(): callable {
        return $this->getTime(...);
    }

    private function getTime(): int {
        return time();
    }
}

$clock = new Clock();
$clock_callback = $clock->getClockCallable();
echo $clock_callback();