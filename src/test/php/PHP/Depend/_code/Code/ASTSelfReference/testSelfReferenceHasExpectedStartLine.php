<?php
class testSelfReferenceHasExpectedStartLine {
    function testSelfReferenceHasExpectedStartLine()
    {
        new self();
    }
}