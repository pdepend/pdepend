<?php
require_once 'PHP/Depend.php';
require_once 'PHP/Depend/Renderer/GdChartRenderer.php';
require_once 'PHP/Depend/Renderer/XMLRenderer.php';


if ( isset( $argv[1] ) )
{
    $dir = $argv[1];
}
else
{
    $dir = dirname( __FILE__ ) . '/data/code-5.2.x';
}

$pdepend = new PHP_Depend();
$pdepend->addDirectory($dir);

$packages = $pdepend->analyze();

$cwd = getcwd();

$renderer = new PHP_Depend_Renderer_GdChartRenderer($cwd . '/php_depend.png');
$renderer->render($packages);

$renderer = new PHP_Depend_Renderer_XMLRenderer($cwd . '/php_depend.xml');
$renderer->render($packages);