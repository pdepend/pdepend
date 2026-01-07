<?php

class PropertyHook
{
    public string $example {
        #[Getter(new DateTimeImmutable())]
        get {
        }
    }
}
