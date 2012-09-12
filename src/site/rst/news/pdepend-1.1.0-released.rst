======================
Version 1.1.0 released
======================

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This release makes PHP_Depend ready for PHP 5.4 traits.
:Keywords:     Release, Version, Trait, bugfixes

We are proud to announce that version `1.1.0`__ of PHP_Depend was released
on September the 12th 2012. This release closes a critical issue in the context
of traits handling.

With this release we make `composer`__ a first citizen distribution channel of
PHP_Depend, just add PHP_Depend as a dependency to your ``composer.json`` ::

  {
      "require": {
          "pdepend/pdepend: "1.1.0"
      }
  }

Then install Composer in your project (or `download the composer.phar`__
directly): ::

  ~ $ curl -s http://getcomposer.org/installer | php

And finally let Composer install the project dependencies: ::

  ~ $ php composer.phar install

Download
--------

Download the latest version of PHP_Depend as a `Phar archive`__, `Composer`__
dependency or through PHP_Depend's `PEAR Channel Server`__.

__ /download/release/1.1.0/changelog.html
__ http://getcomposer.org
__ http://getcomposer.org/composer.phar
__ /download/release/1.1.0/pdepend.phar
__ http://packagist.org/packages/pdepend/pdepend
__ http://pear.pdepend.org
