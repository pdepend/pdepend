<?php

interface ITest {
    public function stuff((A&B&C)|true|(D&((E&F)|G)) $var);
}
