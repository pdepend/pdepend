<?php
function testCatchMultipleExceptionClasses()
{
    try {
        // do something
    } catch (Exception|FooException $e) {}
}