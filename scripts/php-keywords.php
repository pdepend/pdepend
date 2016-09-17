<?php
$url = 'https://raw.githubusercontent.com/php/php-src/master/Zend/zend_language_scanner.l';
$file = basename($url);

if (isset($argv[1]) && is_numeric($argv[1])) {
    $argv[2] = $argv[1];
    unset($argv[1]);
}

if (isset($argv[1])) {
    if (false === file_exists($argv[1])) {
        fwrite(STDERR, "The given zend_language_scanner.l does not exist.\n");
        fwrite(STDERR, "Usage:\n~ \$ php-keywords.php <path/to/zend_language_scanner.l> [<phpversion>]\n");
        exit(23);
    } else {
        $file = $argv[1];
    }
} else if (false === file_exists($file) || time() - filemtime($file) > 7200) {
    shell_exec(sprintf("wget -c '%s'", $url));
    touch($file);
}

$data = file_get_contents($file);

$regexp = '(
    \s+<ST_IN_SCRIPTING>"([a-z_]+)"\s*\{
        \s+RETURN_TOKEN\(([A-Z_]+)\);
    \s+\}
)xi';

if (0 === preg_match_all($regexp, $data, $matches)) {
    fwrite(STDERR, "No matches found :-(\nUsage:\n~ \$ php-keywords.php <path/to/zend_language_scanner.l>\n");
    exit(42);
}

$tokens = [];
foreach ($matches[1] as $i => $keyword) {
    $tokens[$matches[2][$i]] = $keyword;
}

ksort($tokens);

$valid = [
    'class' => [
        'T_NULL',
        'T_TRUE',
        'T_FALSE',
        'T_STRING',
    ],
    'constant' => [
        'T_NULL',
        'T_SELF',
        'T_TRUE',
        'T_FALSE',
        'T_STRING',
        'T_PARENT',
    ],
    'function' => [
        'T_NULL',
        'T_SELF',
        'T_TRUE',
        'T_FALSE',
        'T_STRING',
        'T_PARENT',
    ],
    'namespace' => [
        'T_NULL',
        'T_SELF',
        'T_TRUE',
        'T_FALSE',
        'T_STRING',
        'T_PARENT',
    ],
];

foreach ($tokens as $constant => $image) {
    $valid = test('class', '<?php class %s {}', $image, $constant, $valid);
    $valid = test('constant', '<?php class X { const %s = 42; }', $image, $constant, $valid);
    $valid = test('function', '<?php class X { public function %s() {} }', $image, $constant, $valid);
    $valid = test('namespace', '<?php namespace My\%s { class X { } }', $image, $constant, $valid);
}

$methodCode = dump('class', $valid);
$methodCode .= dump('constant', $valid);
$methodCode .= dump('function', $valid);
$methodCode .= dump('namespace', $valid);

function test($type, $code, $image, $constant, array &$valid)
{
    $file = tempnam(sys_get_temp_dir(), 'php-keyword_');

    file_put_contents($file, sprintf($code, $image));
    exec(sprintf("%s -l '%s'", PHP_BINARY, $file), $output, $retval);

    if (0 === $retval) {
        $valid[$type][] = $constant;
    }

    unlink($file);

    return $valid;
}

function dump($type, array $valid)
{
    $code = sprintf('
    /**
     * Tests if the give token is a valid %s name in the supported PHP
     * version.
     *
     * @param integer $tokenType
     * @return boolean
     */
    protected function is%sName($tokenType)
    {
        switch ($tokenType) {
%s
                return true;
        }
        return false;
    }
    ',
        $type,
        ucfirst($type),
        join(
            "\n",
            array_map(
                function ($token) {
                    return sprintf('            case Tokens::%s:', $token);
                },
                $valid[$type]
            )
        )
    );

    echo $code;
    return $code;
}

if (false === isset($argv[2])) {
    exit(0);
}

$parserFile = sprintf(__DIR__ . '/../src/main/php/PDepend/Source/Language/PHP/PHPParserVersion%s.php', $argv[2]);
if (false === file_exists($parserFile)) {
    fwrite(STDERR, "The given parser version does not exist.\n");
    exit(42);
}

$parserCode = file_get_contents($parserFile);

preg_match(
    '(\s+/\* Keyword test methods {{{ \*/\s*([^\s].*[^\s])\s*/\* }}} Keyword test methods \*/)sU',
    $parserCode,
    $match
);

$parserCode = str_replace(
    $match[0],
    str_replace(
        $match[1],
        trim($methodCode),
        $match[0]
    ),
    $parserCode
);

echo $parserCode;
file_put_contents($parserFile, $parserCode);
