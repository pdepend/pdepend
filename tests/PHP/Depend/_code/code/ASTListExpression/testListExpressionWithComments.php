<?php
function testForeachStatementSuppportsManyVariables()
{
    list/* foo */(/*bar*/$a /*abc*/, // something
          $b, /* sheesh */ $c # blah
      ) = array("a", "b", "c");
}
