<?php
function testForeachStatementAlternativeScopeHasExpectedEndColumn( array $values )
{
    foreach ( $values as $key => $value ):
        echo $key, ': ', $value, PHP_EOL;
    endforeach;
}

testForeachStatementAlternativeScopeHasExpectedEndColumn( array( 'a', 'b', 'c', 'd' ) );