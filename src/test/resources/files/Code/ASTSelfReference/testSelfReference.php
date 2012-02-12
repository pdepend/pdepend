<?php
class testSelfReference {
    function testSelfReference()
    {
        new self();
    }
}
