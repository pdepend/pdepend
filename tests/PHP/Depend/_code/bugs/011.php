<?php
class CurlyBraceClassBug11
{
    function x()
    {
        $x = "{{$foo}";
    }
    
    function y()
    {
        $x = "{$foo}}\"";
    }
    
    function z()
    {
        $query = "
            INSERT INTO {$connection->getTableName()}
            ({$columns})
            VALUES
            ({$placeHolders})
        ";
        
    }
}

function z()
{
    $x = "{{{$foo}";
}