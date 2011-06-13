Beside the PEAR repository and the developer checkout from GitHub, you can
always grep the latest version of PHP_Depend from this website as a simple,
single PHAR archive.

.. class:: shell

::

  ~ $ wget http://pdepend.org/download/release/latest/pdepend.phar
  ..
  ~ $ chmod +x pdepend.phar

That's it. Now you have a running version of PHP_Depend, that can be called
on the command-line:

.. class:: shell

::

  ~ $ ./pdepend.phar --summary-xml=/tmp/sum.xml /path/to/code
