<?php

class PropertyHook
{
    public string $name {
        set(string  $value) {
            $this->name = $value;
        }
    }
}
