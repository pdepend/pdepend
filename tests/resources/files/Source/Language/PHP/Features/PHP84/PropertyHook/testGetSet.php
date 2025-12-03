<?php

class testGetSet
{
    public int $counter {
        get {
            return $this->counter + 1;
        }
        set {
            self::$counter = $value;
        }
    }
}
