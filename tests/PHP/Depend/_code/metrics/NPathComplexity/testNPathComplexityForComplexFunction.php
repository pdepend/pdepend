<?php
function testNPathComplexityForComplexFunction()
{
    $var1 = $var2->get();
    $info['class'] = ($var1 !== null) ? $var1->get() : null;
    if ($var2->is()) {
        $info['defaultValue'] = $var2->get();
    }
    if ($var1 !== null) {
        $info['type'] = ltrim($var1->get(), '\\');
    } elseif ($method !== null) {
        $var3 = $this->get($method->get()->get(), $method->get());
        if (isset($var3['param']) && isset($var3['param'][$var2->get()])) {
            $expl = explode(' ', $var3['param'][$var2->get()]);
            if (count($expl) >= 2) {
                $info['type'] = ltrim($expl[0], '\\');
            }
        }
    }
    return $info;
}