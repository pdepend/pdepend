<?php
class PHP_Depend_Util_PHPFilterIterator extends FilterIterator
{
    public function accept() 
    {
        return ( substr($this->getInnerIterator()->current(), -4, 4) === '.php' );
    }
}