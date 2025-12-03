<?php

class testTyped
{
    public string $name {
        set (int|string $value) => strtolower((string)$value);
    }
}
