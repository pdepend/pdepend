<?php
class testTraitUseStatementHasExpectedEndColumn
{
    use
        /* ... */ MyTraitOne /* ... */,
        MyTraitTwo/* ... */,
        // ...
        MyTraitThree /* ... */
            ;
}
