<?php
class testHasExcludeForReturnsFalseIfMethodNotAffectedByInstead
{
    use testHasExcludeForReturnsFalseIfMethodNotAffectedByInsteadUsedTraitOne,
        testHasExcludeForReturnsFalseIfMethodNotAffectedByInsteadUsedTraitTwo
        {
            testHasExcludeForReturnsFalseIfMethodNotAffectedByInsteadUseTraitTwo::foo
                insteadof
                    testHasExcludeForReturnsFalseIfMethodNotAffectedByInsteadUsedTraitOne;
        }
}

trait testHasExcludeForReturnsFalseIfMethodNotAffectedByInsteadUsedTraitOne {
    function foo() {}
}

trait testHasExcludeForReturnsFalseIfMethodNotAffectedByInsteadUsedTraitTwo {
    function foo() {}
}
