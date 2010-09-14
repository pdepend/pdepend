<?php
interface PHP_Depend_Code_Node
{
    function accept(PHP_Depend_Code_NodeVisitor $visitor);
}