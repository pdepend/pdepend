<?php
class testPreDecrementExpressionOnSelfClassMember
{
    function testPreDecrementExpressionOnSelfClassMember()
    {
        return --
            // This is
            self
                // an inline
                ::
                    // comment...
                    $bar;
    }
}