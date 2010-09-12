<?php
class testSelfReferenceHasExpectedEndLine {
    function testSelfReferenceHasExpectedEndLine()
    {
        new self();
    }
}