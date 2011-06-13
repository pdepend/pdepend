:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This section describes how to install PHP_Depend through its PEAR
               channel server. PHP_Depend's channel server can be accessed under
               the following url http://pear.pdepend.org
:Keywords:     Installation, PEAR,

To get started with PHP_Depend you must install the latest version on your
system. PHP_Depend should be installed using the `PEAR Installer`__. The
installer is part of all PEAR and provides a distribution system for PHP
packages. It shipped with every release of PHP since version 4.3.0.

To install PHP_Depend you need to register its PEAR channel `pear.pdepend.org`__
that is used to distribute PHP_Depend:

.. class:: shell

::

  ~ $ pear channel-discover pear.pdepend.org
  Adding Channel "pear.pdepend.org" succeeded
  Discovery of channel "pear.pdepend.org" succeeded

Now that the PHP_Depend's PEAR channel is known to your PEAR environment you can
install the PHP_Depend package from its channel:

.. class:: shell

::

  ~ $ pear install pdepend/PHP_Depend-beta
  downloading PHP_Depend-0.10.0RC1.tgz ...
  Starting to download PHP_Depend-0.10.0RC1.tgz (164,193 bytes)
  ....................................done: 164,193 bytes
  install ok: channel://pear.pdepend.org/PHP_Depend-0.10.0RC1

After the installation you can find the PHP_Depend source files inside your
local PEAR directory.

.. class:: shell

::

  ~ $ ls `pear config-get php_dir`/PHP
  Depend  Depend.php

__ http://pear.php.net/manual/en/installation.php
__ http://pear.pdepend.org
