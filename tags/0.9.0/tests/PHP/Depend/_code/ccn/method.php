<?php
interface CCMethodInterface {
    function pdepend1($x);
    function pdepend2($x);
    
}


class CCMethodClass implements CCMethodInterface
{
    function pdepend1($x)
    {
        switch ($x) {
        case 'a':
            if ($a === true) {
             
            } else if ($a === false && $a !== 17) {
            
            } else {
                
            }
            break;
        
        default:
            if ($a === true) {}
            break;
        }
    }

    function pdepend2($x)
    {
        foreach ($x as $y) {
            for ($i = 0; $i < $y; ++$i) {
                try {
                    if ($x->get($i) === 0 and $x->get($i) > 23) {
                        return false;
                    } else if ($x->get($i) === 1 || true) {
                        return true;
                    } else if ($x->get($i) === 1 or false) {
                        return false;
                    }
                } catch (Exception $e) {}
            }
        }
    }
}