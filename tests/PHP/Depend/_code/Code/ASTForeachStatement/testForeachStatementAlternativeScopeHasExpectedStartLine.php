<?php
function testForeachStatementAlternativeScopeHasExpectedStartLine( array $values )
{
    foreach ( $values as $key => $value ):
        echo $key, ': ', $value, PHP_EOL;
    endforeach;
}

testForeachStatementAlternativeScopeHasExpectedStartLine( array( 'a', 'b', 'c', 'd' ) );