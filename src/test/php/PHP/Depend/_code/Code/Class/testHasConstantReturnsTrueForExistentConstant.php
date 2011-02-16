<?php
class testHasConstantReturnsTrueForExistentConstant
    extends testHasConstantReturnsTrueForExistentConstant_parent
{
    const FOO = 23;
}

class testHasConstantReturnsTrueForExistentConstant_parent
{
    const BAR = 42;
}