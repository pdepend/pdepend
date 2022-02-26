<?php

$replacements = array(
    /**
     * Patch phpunit/phpunit-mock-objects Generator.php file to not create double nullable tokens: `??`
     */
    __DIR__ . '/../../../../vendor/phpunit/phpunit-mock-objects/src/Framework/MockObject/Generator.php' => array(
        array(
            "if (version_compare(PHP_VERSION, '7.1', '>=') && \$parameter->allowsNull() && !\$parameter->isVariadic()) {",
            "if (version_compare(PHP_VERSION, '7.1', '>=') && version_compare(PHP_VERSION, '8.0', '<') && \$parameter->allowsNull() && !\$parameter->isVariadic()) {",
        ),
    ),
    /**
     * Fix phpunit/phpunit to not trigger warning on `final private function`
     */
    __DIR__ . '/../../../../vendor/phpunit/phpunit/src/Util/Configuration.php' => array(
        array(
            'final private function',
            'private function',
        ),
        array(
            '$target = &$GLOBALS;',
            '',
        ),
    ),
    __DIR__ . '/../../../../vendor/phpunit/phpunit/src/Framework/Constraint.php' => array(
        array(
            'public function count()',
            "#[\\ReturnTypeWillChange]\npublic function count()",
        ),
    ),
    __DIR__ . '/../../../../vendor/phpunit/php-token-stream/src/Token/Stream.php' => array(
        array(
            'public function offsetExists',
            "#[\\ReturnTypeWillChange]\npublic function offsetExists",
        ),
        array(
            'public function offsetGet',
            "#[\\ReturnTypeWillChange]\npublic function offsetGet",
        ),
        array(
            'public function offsetSet',
            "#[\\ReturnTypeWillChange]\npublic function offsetSet",
        ),
        array(
            'public function offsetUnset',
            "#[\\ReturnTypeWillChange]\npublic function offsetUnset",
        ),
        array(
            'public function count',
            "#[\\ReturnTypeWillChange]\npublic function count",
        ),
        array(
            'public function seek',
            "#[\\ReturnTypeWillChange]\npublic function seek",
        ),
        array(
            'public function current',
            "#[\\ReturnTypeWillChange]\npublic function current",
        ),
        array(
            'public function next',
            "#[\\ReturnTypeWillChange]\npublic function next",
        ),
        array(
            'public function key',
            "#[\\ReturnTypeWillChange]\npublic function key",
        ),
        array(
            'public function valid',
            "#[\\ReturnTypeWillChange]\npublic function valid",
        ),
        array(
            'public function rewind',
            "#[\\ReturnTypeWillChange]\npublic function rewind",
        ),
    ),
);

foreach ($replacements as $file => $patterns) {
    echo "$file: ";

    if (!file_exists($file)) {
        echo "File not found.\n";

        continue;
    }

    foreach ($patterns as $replacement) {
        list($from, $to) = $replacement;

        $contents = @file_get_contents($file) ?: '';
        $newContents = str_replace($from, $to, $contents);

        if ($newContents !== $contents) {
            file_put_contents($file, $newContents);
            echo "Content changed.\n";

            continue;
        }

        echo "Replace pattern not found.\n";
    }
}
