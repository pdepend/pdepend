<?php declare(strict_types=1);

$composerLock = __DIR__ . '/composer.lock';
$symfonyVersion = null;

if (is_file($composerLock)) {
    $lock = json_decode(file_get_contents($composerLock), true);

    if (isset($lock['packages']) && is_array($lock['packages'])) {
        foreach ($lock['packages'] as $pkg) {
            if ($pkg['name'] === 'symfony/config') {
                $symfonyVersion = ltrim($pkg['version'], 'v');
                break;
            }
        }
    }
}

$includes = [];
// only include baseline if composer.lock exists AND version is older than 7
if ($symfonyVersion !== null && version_compare($symfonyVersion, '7.0.0', '<')) {
    $includes[] = __DIR__ . '/phpstan-baseline-symfony-pre-7.neon';
} elseif (version_compare($symfonyVersion, '8.0.0', '>=')) {
    $includes[] = __DIR__ . '/phpstan-baseline-symfony-post-7.neon';
}

$config = [];
$config['includes'] = $includes;

return $config;
