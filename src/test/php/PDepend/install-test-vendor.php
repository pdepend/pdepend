<?php

$cwd = getcwd();
chdir(__DIR__ . '/../..');
$phar = $cwd . '/composer.phar';
$composer = file_exists($phar) ? 'php ' . escapeshellarg(realpath($phar)) : 'composer';
$command = $composer . ' update';

echo "> $command\n";
echo shell_exec($command);

chdir($cwd);
