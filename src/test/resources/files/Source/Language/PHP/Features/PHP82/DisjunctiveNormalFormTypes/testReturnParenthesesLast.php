<?php

interface ITest {
    public function stuff(): null|(A|B|C)|(D&E&F);
}
