<?php
class testParserAcceptsCloneAsMethodName
{
    function clone($object)
    {
        return $object;
    }
}