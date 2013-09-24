<?php
class testHasExcludeForReturnsTrueIfMethodAffectedBySecondInstead
{
    use testHasExcludeForReturnsTrueIfMethodAffectedBySecondInsteadUsedTraitOne,
        testHasExcludeForReturnsTrueIfMethodAffectedBySecondInsteadUsedTraitTwo {

        testHasExcludeForReturnsTrueIfMethodAffectedBySecondInsteadUsedTraitOne::bar
            insteadof
                testHasExcludeForReturnsTrueIfMethodAffectedBySecondInsteadUsedTraitTwo;

        testHasExcludeForReturnsTrueIfMethodAffectedBySecondInsteadUsedTraitTwo::foo
            insteadof
                testHasExcludeForReturnsTrueIfMethodAffectedBySecondInsteadUsedTraitOne;
    }
}

trait testHasExcludeForReturnsTrueIfMethodAffectedBySecondInsteadUsedTraitOne
{
    public function foo() {}
    public function bar() {}
}

trait testHasExcludeForReturnsTrueIfMethodAffectedBySecondInsteadUsedTraitTwo
{
    public function foo() {}
    public function bar() {}
}
