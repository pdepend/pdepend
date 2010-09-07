<?php
function testForeachStatementAlternativeScopeHasExpectedStartColumn( array $values )
{
    foreach ( $values as $key => $value ):
        echo $key, ': ', $value, PHP_EOL;
    endforeach;
}

testForeachStatementAlternativeScopeHasExpectedStartColumn( array( 'a', 'b', 'c', 'd' ) );
