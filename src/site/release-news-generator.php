<?php

define('MIN_VERSION', '2.6.0');
define('LASTEST_VERSION_COUNT', 3);

$versionsMatches = [];
$count = preg_match_all(
    "/^pdepend-(\d+\.\d+\.\d+) \(\d{4}\/\d{2}\/\d{2}\)\n" .
    '^=*$/m',
    file_get_contents(__DIR__ . '/../../CHANGELOG.md') ?: '',
    $versionsMatches
);

if ($count === false) {
    echo "Failed to find versions on CHANGELOG file!\n";

    exit(1);
}

if ($count === 0) {
    printf(
        "No releases were found on the CHANGELOG file, please review the %s script.\n",
        __FILE__
    );

    exit(1);
}

foreach ($versionsMatches[1] as $version) {
    if (version_compare($version, MIN_VERSION, '<')) {
        continue;
    }

    $newReleaseFile = __DIR__ . "/rst/news/pdepend-{$version}-released.rst";
    if (file_exists($newReleaseFile)) {
        continue;
    }

    file_put_contents(
        $newReleaseFile,
        <<<RST
            =======================
            Version {$version} released
            =======================

            We are proud to announce version `{$version} <https://github.com/pdepend/pdepend/releases/tag/{$version}>`_ of PDepend. For more
            details visit the version release `site <https://github.com/pdepend/pdepend/releases/tag/{$version}>`_.

            Use `composer <http://getcomposer.org>`_ to install PHP_Depend:

            .. class:: shell

            ::

              curl -s http://getcomposer.org/installer | php
              php composer.phar require "pdepend/pdepend:{$version}"

            Or if you already have composer installed globally:

            .. class:: shell

            ::

              composer require "pdepend/pdepend:{$version}"

            Download
            --------

            Future releases will also be released as PHAR files on
            `GitHub <https://github.com/pdepend/pdepend/releases>`_
            RST
    );
}

$lastestVersions = array_splice($versionsMatches[1], 0, LASTEST_VERSION_COUNT);

$versionsToInclude = '';
foreach ($lastestVersions as $version) {
    $versionsToInclude .= ".. include:: news/pdepend-{$version}-released.rst\n\n";
}

$versionsToInclude = trim($versionsToInclude);
file_put_contents(__DIR__ . '/rst/news.rst', <<<RST
    ===============
    PHP Depend news
    ===============

    {$versionsToInclude}
    RST);
