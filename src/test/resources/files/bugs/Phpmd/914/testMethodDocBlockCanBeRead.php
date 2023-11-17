<?php

class Foo
{
    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function bar() {
        if (true and true and false) return;
        /** This comment breaks SuppressWarnings */
        if (true and true and false) return;
        if (true and true and false) return;
        if (true and true and false) return;
    }
}
