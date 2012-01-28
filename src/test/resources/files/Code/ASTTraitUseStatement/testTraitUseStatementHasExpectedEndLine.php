<?php
class testTraitUseStatementHasExpectedEndLine
{
    use
        /* ... */ MyTraitOne /* ... */,
        MyTraitTwo/* ... */,
        // ...
        MyTraitThree /* ... */
            ;
}
