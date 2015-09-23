<?php

namespace ClassDependencyAnalyzer;

abstract class AbstractBase {}

interface SomeInterface {}

// Trait usage not supported yet
trait SomeTrait {}

class Used extends BaseClass implements SomeInterface {}

class BaseClass extends AbstractBase {
    use SomeTrait;

    public function __construct(Used $used)
    {
        $this->used = $used;
    }
}
