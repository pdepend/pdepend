<?php
function testForeachStatementAlternativeScopeHasExpectedEndLine( array $values )
{
    foreach ( $values as $key => $value ):
        echo $key, ': ', $value, PHP_EOL;
    endforeach;
}

testForeachStatementAlternativeScopeHasExpectedEndLine( array( 'a', 'b', 'c', 'd' ) );