======================
Version 1.0.0 released
======================

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This release makes PHP_Depend ready for PHP 5.4, because it
               implements all new language constructs introduced with this new
               PHP version.
:Keywords:     Release, Version, Trait, short array syntax, static closures

We are proud to announce that version `1.0.0`__ of PHP_Depend was released
on February the 4th 2012. This release comes with many new features and makes
PHP_Depend ready for the upcoming `PHP 5.4`__ release.

Now that we have completed support for all the new language features introduced
with PHP 5.4, we are ready to release version 1.0.0 of PHP_Depend. PHP_Depend
can now handle `traits`__, `static closures`__, `binary numbers`__, the
`callable type hint`__, `function array dereferencing`__ and the new
`short array syntax`__. Beside that, we have spent much effort in improving
PHP_Depend's overall performance and we got an average speed gain of ~ 15%
tested with major frameworks like `Symfony2`__ or `FLOW3`__, when PHP_Depend's
file cache (default setup) is used.

Additionally this release closes several minor issues in PHP_Depend.

Download
--------

Download the latest version of PHP_Depend as a `Phar archive`__ or through
PHP_Depend's `PEAR Channel Server`__.

__ /download/release/1.0.0/changelog.html
__ http://www.php.net/archive/2012.php#id2012-01-24-1
__ https://wiki.php.net/rfc/horizontalreuse
__ https://wiki.php.net/rfc/closures
__ https://wiki.php.net/rfc/binnotation4ints
__ https://wiki.php.net/rfc/callable
__ https://wiki.php.net/rfc/functionarraydereferencing
__ https://wiki.php.net/rfc/shortsyntaxforarrays
__ http://symfony.com/
__ http://flow3.typo3.org/
__ /download/release/1.0.0/pdepend.phar
__ http://pear.pdepend.org
