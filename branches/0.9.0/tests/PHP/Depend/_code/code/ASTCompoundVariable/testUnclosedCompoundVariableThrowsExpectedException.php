<?php
function testUnclosedCompoundVariableThrowsExpectedException()
{
    ${bar{foo}
}