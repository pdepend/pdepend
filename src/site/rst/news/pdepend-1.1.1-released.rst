======================
Version 1.1.1 released
======================

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  Closes some 5.4 related bugs.
:Keywords:     Release, Version, Trait, bugfixes

After some silence we are proud to announce PDepend version `1.1.1`__. A bug fix
release that closes a PHP 5.4 short array syntax bug.

With this release we make `composer`__ a first citizen distribution channel of
PHP_Depend, just add PHP_Depend as a dependency to your ``composer.json`` ::

  {
      "require": {

          "pdepend/pdepend" : "1.1.1"
      }
  }

Then install Composer in your project (or `download the composer.phar`__
directly):

.. class:: shell

::

  ~ $ curl -s http://getcomposer.org/installer | php

And finally let Composer install the project dependencies:

.. class:: shell

::

  ~ $ php composer.phar install

Download
--------

Download the latest version of PHP_Depend as a `Phar archive`__, `Composer`__
dependency or through PHP_Depend's `PEAR Channel Server`__.

__ /download/release/1.1.1/changelog.html
__ http://getcomposer.org
__ http://getcomposer.org/composer.phar
__ http://static.pdepend.org/php/1.1.1/pdepend.phar
__ http://packagist.org/packages/pdepend/pdepend
__ http://pear.pdepend.org
