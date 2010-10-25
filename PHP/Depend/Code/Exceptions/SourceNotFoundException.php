<?php
class PHP_Depend_Code_Exceptions_SourceNotFoundException
    extends PHP_Depend_Code_Exceptions_AbstractException
{
    public function __construct(PHP_Depend_Code_AbstractItem $owner)
    {
        parent::__construct('The mandatory parent was not defined.');
    }
}