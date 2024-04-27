<?php

function setFooIfMissing(array $settings) {
    $settings['foo'] ??= 'bar';

    return $settings;
}
