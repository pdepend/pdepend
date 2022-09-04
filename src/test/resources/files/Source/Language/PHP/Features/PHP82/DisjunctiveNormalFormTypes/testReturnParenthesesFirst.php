<?php

interface ITest {
    public function stuff(): (A&B)|D;
}
