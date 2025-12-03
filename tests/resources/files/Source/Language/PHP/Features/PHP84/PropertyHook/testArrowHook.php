<?php

class testArrowHook
{
    public string $name {
        set => strtolower($value);
    }
}
