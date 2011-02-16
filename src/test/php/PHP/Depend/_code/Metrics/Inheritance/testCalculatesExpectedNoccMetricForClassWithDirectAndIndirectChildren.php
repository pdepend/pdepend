<?php
class testCalculatesExpectedNoccMetricForClassWithDirectAndIndirectChildren
{

}

class c1 extends testCalculatesExpectedNoccMetricForClassWithDirectAndIndirectChildren {}
class c2 extends c1 {}
class c3 extends c2 {}