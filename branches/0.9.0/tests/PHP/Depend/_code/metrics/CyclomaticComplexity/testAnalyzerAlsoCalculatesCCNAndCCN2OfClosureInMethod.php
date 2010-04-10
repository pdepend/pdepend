<?php
namespace nspace;

class Clazz {
    protected function method()
    {
        if (Builder::get()) { }

        $code = array_map(
            function ($line) {
                return $line ? '  '.$line : $line; 
            },
            explode("\n", $code)
        );
        return sprintf(
            "if (%s)\n{\n%s}\n", 
            implode(' && ', $conditions),
            $code
        );
    }
}
