<?php

class Test
{
    const A = [];
    const B = self::A + ['a' => 1];
}
