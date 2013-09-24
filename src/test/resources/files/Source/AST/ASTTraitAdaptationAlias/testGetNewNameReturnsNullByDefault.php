<?php
class testGetNewNameReturnsNullByDefault
{
    use testGetNewNameReturnsNullByDefaultMyTraitOne {
        myTraitMethod as private;
    }
}
