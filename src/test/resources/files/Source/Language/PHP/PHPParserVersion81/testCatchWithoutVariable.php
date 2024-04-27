<?php
class Foo
{
    function testCatchWithoutVariable()
    {
        try {
            $a = 1;
        } catch (Exception) {
            echo 'Something wrong happened';
        }
    }
}
