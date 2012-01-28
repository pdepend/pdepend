<?php
class testTraitAdaptationAliasHasExpectedEndColumn
{
    use testTraitAdaptationAliasHasExpectedStartColumnEndOne
    {
        myTraitMethod as private myTraitAlias;
    }
}
