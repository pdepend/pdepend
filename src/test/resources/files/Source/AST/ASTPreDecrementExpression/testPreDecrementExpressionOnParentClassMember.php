<?php
class testPreDecrementExpressionOnParentClassMember extends stdClass
{
    function testPreDecrementExpressionOnParentClassMember()
    {
        return
            /* Hello */
            --
                # World
                parent::
                    // !!!
                        $bar
                            ;
    }
}