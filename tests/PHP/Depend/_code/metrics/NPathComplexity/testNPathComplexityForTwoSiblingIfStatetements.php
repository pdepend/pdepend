<?php
function testNPathComplexityForTwoSiblingIfStatetements()
{
    if (true) throw new Exception();
    if (true) return FALSE;
}