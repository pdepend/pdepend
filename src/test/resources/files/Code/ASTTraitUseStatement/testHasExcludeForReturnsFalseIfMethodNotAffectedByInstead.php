<?php
class testHasExcludeForReturnsFalseIfMethodNotAffectedByInstead
{
    use testHasExcludeForReturnsFalseIfMethodNotAffectedByInsteadUsedTraitOne,
        testHasExcludeForReturnsFalseIfMethodNotAffectedByInsteadUsedTraitTwo
        {
            testHasExcludeForReturnsFalseIfMethodNotAffectedByInsteadUseTraitOne::foo
                insteadof
                    testHasExcludeForReturnsFalseIfMethodNotAffectedByInsteadUsedTraitTwo;
        }
}

trait testHasExcludeForReturnsFalseIfMethodNotAffectedByInsteadUsedTraitOne {
    function foo() {}
}

trait testHasExcludeForReturnsFalseIfMethodNotAffectedByInsteadUsedTraitTwo {
    function foo() {}
}
