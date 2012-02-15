<?php
function testListExpressionWithNestedList()
{
    list( $a, list( $b, list( $c, $d ) ) ) = array(
        'a',
            array(
                'b',
                array(
                    'c',
                    'd'
                )
            )
    );

    var_dump( $a, $b, $c, $d );
}
testListExpressionWithNestedList();
