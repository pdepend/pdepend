The newest distribution channel that you can use to retrieve PHP_Depend is
composer. Just add the following lines to your ``composer.json`` file:

.. class:: shell

::

  {
      "require": {

          "pdepend/pdepend" : "1.1.0"
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

__ http://getcomposer.org/composer.phar
