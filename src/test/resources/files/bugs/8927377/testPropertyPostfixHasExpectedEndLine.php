<?php
class testPropertyPostfixHasExpectedEndLine
{
    protected function foo()
    {
        $this->bar
            /* */;
    }
}