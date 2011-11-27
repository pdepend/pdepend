<?php
function testArrayElementGraphWithTwoDimensions()
{
    return array(
        "bar"  =>  array(
            new Object,
            23 => new Object,
            array("foo"  =>  new Object)
        )
    );
}
