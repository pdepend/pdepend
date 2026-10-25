<?php

class testDoubleColonAfterNewClass
{
    public function main()
    {
        return new Curl()::$curl_handle;
    }
}