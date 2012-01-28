<?php
class testTraitAdaptationAliasHasExpectedStartLine
{
    use testTraitAdaptationAliasHasExpectedStartLineTraitOne
    {
        myTraitMethod as private myTraitAlias;
    }
}
