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
    __DIR__ . '/../../../../vendor/phpunit/phpunit/src/Extensions/PhptTestCase.php' => array(
        array(
            'public function count()',
            "#[\\ReturnTypeWillChange]\npublic function count()",
        ),
        array(
            'xdebug.default_enable=0',
            'xdebug.mode=coverage',
        ),
    ),
    __DIR__ . '/../../../../vendor/phpunit/phpunit/src/Framework/TestCase.php' => array(
        array(
            'public function count(',
            "#[\\ReturnTypeWillChange]\npublic function count(",
        ),
    ),
    __DIR__ . '/../../../../vendor/phpunit/phpunit/src/Framework/TestSuite.php' => array(
        array(
            'public function count(',
            "#[\\ReturnTypeWillChange]\npublic function count(",
        ),
        array(
            'public function getIterator(',
            "#[\\ReturnTypeWillChange]\npublic function getIterator(",
        ),
    ),
    __DIR__ . '/../../../../vendor/phpunit/phpunit/src/Framework/TestResult.php' => array(
        array(
            'public function count(',
            "#[\\ReturnTypeWillChange]\npublic function count(",
        ),
        array(
            'public function getIterator(',
            "#[\\ReturnTypeWillChange]\npublic function getIterator(",
        ),
    ),
    __DIR__ . '/../../../../vendor/phpunit/phpunit/src/Util/TestSuiteIterator.php' => array(
        array(
            'public function hasChildren(',
            "#[\\ReturnTypeWillChange]\npublic function hasChildren(",
        ),
        array(
            'public function getChildren(',
            "#[\\ReturnTypeWillChange]\npublic function getChildren(",
        ),
        array(
            'public function current(',
            "#[\\ReturnTypeWillChange]\npublic function current(",
        ),
        array(
            'public function next(',
            "#[\\ReturnTypeWillChange]\npublic function next(",
        ),
        array(
            'public function key(',
            "#[\\ReturnTypeWillChange]\npublic function key(",
        ),
        array(
            'public function valid(',
            "#[\\ReturnTypeWillChange]\npublic function valid(",
        ),
        array(
            'public function rewind(',
            "#[\\ReturnTypeWillChange]\npublic function rewind(",
        ),
    ),
    __DIR__ . '/../../../../vendor/phpunit/php-code-coverage/src/CodeCoverage.php' => array(
        array(
            '$docblock = $token->getDocblock();',
            '$docblock = $token->getDocblock() ?? \'\';',
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
    __DIR__ . '/../../../../vendor/phpunit/php-code-coverage/src/Report/Html/Renderer/File.php' => array(
        array(
            '$numTests = count($coverageData[$i]);',
            '$numTests = count($coverageData[$i] ?? []);',
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
