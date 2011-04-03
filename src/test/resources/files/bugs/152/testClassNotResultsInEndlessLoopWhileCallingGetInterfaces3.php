<?php
namespace foo;

class MyType implements MyType2
{

}

interface MyType2 extends MyType3
{
}

interface MyType3 extends MyType4
{
}

interface MyType4 extends MyType2
{
}

