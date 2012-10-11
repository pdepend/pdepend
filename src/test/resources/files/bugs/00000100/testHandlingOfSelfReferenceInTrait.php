<?php
namespace Just\A\Test;

use Doctrine\Common\Collections\Collection;

trait FooTrait
{
    private static $errorAlreadyExists = 4900;

    public function asdf()                                                                                                                                                                    
    {
        throw new Exception(
            "asdf" . self::$errorAlreadyExists
        );
    }
}
