<?php
class testTraitAdaptationAliasHasExpectedEndLine
{
    use testTraitAdaptationAliasHasExpectedEndLineTraitOne
    {
        myTraitMethod as private myTraitAlias;
    }
}
