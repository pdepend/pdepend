<?php

class Point
{
    public int $x;
    public int $y;
}

class testParentAccess extends Point
{
    public int $x {
        set {
            if ($value < 0) {
                throw new \InvalidArgumentException('Too small');
            }
            parent::$x::set($value);
        }
    }
}
