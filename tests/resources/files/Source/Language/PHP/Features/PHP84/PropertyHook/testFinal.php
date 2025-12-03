<?php

class testFinal
{
    public string $name {
        final set => strtolower($value);
    }
}
