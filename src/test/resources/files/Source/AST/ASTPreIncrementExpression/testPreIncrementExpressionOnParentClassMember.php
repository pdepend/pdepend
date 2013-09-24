<?php
class testPreIncrementExpressionOnParentClassMember extends stdClass
{
    function testPreIncrementExpressionOnParentClassMember()
    {
        return ++parent::$mapi;
    }
}