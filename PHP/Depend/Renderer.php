<?php
require_once 'PHP/Depend/Metrics/PackageMetrics.php';

interface PHP_Depend_Renderer
{
    function render(Iterator $packages);
}