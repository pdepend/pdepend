<?php
function testIfStatementAlternativeScopeHasExpectedStartColumn()
{
    if (true) /*
               * Comment
               */
        :
        echo 'True', PHP_EOL;
    endif;
}

testIfStatementAlternativeScopeHasExpectedStartColumn();