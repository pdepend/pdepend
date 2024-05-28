<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <meta name="description" content="PHP Depend - Software Metrics for PHP. Metrics analysis tool for software developed in PHP."/>
    <meta name="keywords" content="PHP Depend, metrics, CodeRank, Cyclomatic Complexity, NPath Complexity, Quality Assurance, Design, Maintenance"/>
    <meta name="author" content="Manuel Pichler"/>
    <meta name="language" content="en"/>
    <meta name="date" content="<?= date('r'); ?>"/>
    <meta name="robots" content="all"/>

    <link rel="schema.DC" href="http://purl.org/dc/elements/1.1/"/>
    <meta name="DC.title" content="News"/>
    <meta name="DC.creator" content="Manuel Pichler"/>
    <meta name="DC.date" content="<?= date('r'); ?>"/>
    <meta name="DC.rights" content="CC by-nc-sa"/>

    <link rel="canonical" href="https://pdepend.org/news.html"/>
    <link rel="icon" href="<?= $baseHref ?? ''; ?>/images/favicon.png" type="image/png"/>

    <link rel="Stylesheet" type="text/css" href="<?= $baseHref ?? ''; ?>/css/screen.css" media="screen"/>
    <link rel="Stylesheet" type="text/css" href="<?= $baseHref ?? ''; ?>/css/print.css" media="print"/>

    <title>PHP Depend - Software Metrics for PHP: News</title>
</head>
<body>
<h1 class="viewport">
    <a href="<?= $baseHref ?? ''; ?>/">PHP Depend - Software Metrics for PHP</a>
</h1>
<div class="header">
    <div class="viewport">
        <ul class="navigation">
            <li<?php if (($uri ?? '') === '/news.html') {
                echo ' class="requested"';
            } ?>>
                <a href="<?= $baseHref ?? ''; ?>/news.html" title="News">News</a>
            </li>
            <li<?php if (($uri ?? '') === '/documentation/getting-started.html') {
                echo ' class="requested"';
            } ?>>
                <a href="<?= $baseHref ?? ''; ?>/documentation/getting-started.html" title="Documentation">Documentation</a>
            </li>
            <li<?php if (($uri ?? '') === '/screenshots.html') {
                echo ' class="requested"';
            } ?>>
                <a href="<?= $baseHref ?? ''; ?>/screenshots.html" title="Screenshots">Screenshots</a>
            </li>
            <li<?php if (($uri ?? '') === '/download/index.html') {
                echo ' class="requested"';
            } ?>>
                <a href="<?= $baseHref ?? ''; ?>/download/index.html" title="Download">Download</a>
            </li>
            <li<?php if (($uri ?? '') === '/support.html') {
                echo ' class="requested"';
            } ?>>
                <a href="<?= $baseHref ?? ''; ?>/support.html" title="Support &amp; Contact">Support &amp; Contact</a>
            </li>
        </ul>
    </div>
</div>


<div class="viewport content">
    <?= $content ?? ''; ?>
</div>
<div class="footer">
    <div class="viewport">
        <span class="follow">
            <a href="https://twitter.com/pdepend" title="Follow on Twitter" class="twitter-follow" target="_blank">
                <i></i> Follow @pdepend
            </a>
            <a rel="me" href="https://phpc.social/@pdepend" title="Follow on Mastodon" class="mastodon-follow" target="_blank">
                <i></i> Follow @pdepend@phpc.social
            </a>
        </span>
        <div class="license">
            By <strong>Manuel Pichler</strong>
            licensed under <a href="https://opensource.org/licenses/bsd-license.php" title="BSD-3-Clause">BSD-3-Clause</a>
        </div>
        <div class="clear"></div>
    </div>
</div>
<?= getenv('FOOTER_HOOK') ?: ''; ?>
</body>
</html>
