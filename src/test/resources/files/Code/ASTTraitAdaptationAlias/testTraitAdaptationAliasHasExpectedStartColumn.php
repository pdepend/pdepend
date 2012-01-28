<?php
class testTraitAdaptationAliasHasExpectedStartColumn
{
    use testTraitAdaptationAliasHasExpectedStartColumnTraitOne
    {
        myTraitMethod as private myTraitAlias;
    }
}
