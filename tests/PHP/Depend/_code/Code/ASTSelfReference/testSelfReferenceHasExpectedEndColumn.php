<?php
class testSelfReferenceHasExpectedEndColumn {
    function testSelfReferenceHasExpectedEndColumn()
    {
        new self();
    }
}