<?php
class testTraitAdaptationAlias
{
    use testTraitAdaptationAliasHasExpectedStartColumnEndOne
    {
        myTraitMethod as private myTraitAlias;
    }
}
