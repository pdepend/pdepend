<?php
namespace nspace;

function func()
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
