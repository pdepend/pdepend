<?php
namespace nspace;

class Clazz {
    function method() {
        array_walk($newFieldPaths,
            function(&$value, $key) {
                $value = explode('/', $value);
            }
        );
    }
}
