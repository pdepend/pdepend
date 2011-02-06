<?php
function testIfStatementAlternativeScopeHasExpectedStartLine()
{
    if (true) /*
               * Comment
               */
        :
        echo 'True', PHP_EOL;
    endif // ...
        ;
}

testIfStatementAlternativeScopeHasExpectedStartLine();