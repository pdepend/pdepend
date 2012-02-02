<?php
class testHasExcludeForReturnsFalseIfNoInsteadExists
{
    use testHasExcludeForReturnsFalseIfNoInsteadExistsUsedTraitOne;
}

trait testHasExcludeForReturnsFalseIfNoInsteadExistsUsedTraitOne
{
    function foo() {}
}
