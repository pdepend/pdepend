<?php

$source = __DIR__ . '/../main/php';

$replacements = array(
    $source . '/PDepend/DependencyInjection/Configuration.php' => array(
        function ($contents) {
            global $source;

            $extract = (string)file_get_contents($source . '/Lazy/PDepend/DependencyInjection/Configuration.weak.php');
            $pattern = '(// <AbstractConfiguration>[\s\S]+</AbstractConfiguration>)';

            if (!preg_match($pattern, $extract, $match)) {
                return $contents;
            }

            return preg_replace($pattern, $match[0], $contents);
        },
    ),
    $source . '/Lazy/PDepend/DependencyInjection/Configuration.weak.php' => array(
        function () {
            return '';
        },
    ),
    $source . '/Lazy/PDepend/DependencyInjection/Configuration.strong.php' => array(
        function () {
            return '';
        },
    ),
);

foreach ($replacements as $file => $callbacks) {
    echo "$file: ";

    if (!file_exists($file)) {
        echo "File not found.\n";

        continue;
    }

    foreach ($callbacks as $callback) {
        $contents = @file_get_contents($file) ?: '';

        $newContents = call_user_func($callback, $contents);

        if ($newContents !== $contents) {
            file_put_contents($file, $newContents);
            echo "Content changed.\n";

            continue;
        }

        echo "Replace pattern not found.\n";
    }
}
