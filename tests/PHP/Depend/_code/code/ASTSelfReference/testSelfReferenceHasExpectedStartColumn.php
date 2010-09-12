<?php
class testSelfReferenceHasExpectedStartColumn {
    function testSelfReferenceHasExpectedStartColumn()
    {
        new self();
    }
}