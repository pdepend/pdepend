<?php
interface Foo
{

}

interface Bar extends Foo
{
    const FOO = 42,
          BAR = 23;
}