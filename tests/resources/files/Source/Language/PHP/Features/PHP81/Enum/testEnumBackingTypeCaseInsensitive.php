<?php

enum StringEnum: String
{
    case FOO = 'foo';
    case BAR = 'bar';
}

enum IntEnum: Int
{
    case ONE = 1;
    case TWO = 2;
}

enum MixedCaseStringEnum: STRING
{
    case BAZ = 'baz';
}
