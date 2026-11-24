<?php

// A parse worker whose PHP displays a deprecation while it parses, as PHP 8.6 does for the spl_object_hash() call
// in the namespace that every analysis starts with. It displays it on stdout, as php.ini-development sets up the CLI.
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '0');

require_once __DIR__ . '/../../../../../vendor/autoload.php';

// Loaded when the worker starts its analysis, after it read its command line.
spl_autoload_register(static function (string $class): void {
    if ($class === PDepend\Source\Language\PHP\PHPBuilder::class) {
        trigger_error('A parse worker raised this deprecation on purpose, for EngineTest', E_USER_DEPRECATED);
    }
}, true, true);

exit(PDepend\TextUI\Command::main());
