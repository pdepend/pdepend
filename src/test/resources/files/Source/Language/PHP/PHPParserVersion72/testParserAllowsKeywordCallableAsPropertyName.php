<?php
class MyCallable {
    public function __construct($name, $callable, array $options = array()) {
        $this->name = $name;
        $this->callable = $callable;
        // ...
    }
}
