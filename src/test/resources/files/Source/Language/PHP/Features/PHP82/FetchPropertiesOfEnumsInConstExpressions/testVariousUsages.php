<?php

enum E: string {
    case Foo = 'foo';
}

const C = E::Foo->name;

function f1() {
    static $v = E::Foo->value;
}

#[Attr(E::Foo->name)]
class D {}

function f2(
    $p = E::Foo->value,
) {}

class F {
    public string $p = E::Foo->name;
}

// The rhs of -> allows other constant expressions
const VALUE = 'value';
class G {
    const C = E::Foo->{VALUE};
}
