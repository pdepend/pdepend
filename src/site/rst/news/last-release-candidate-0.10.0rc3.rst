================================
Last release candidate 0.10.0RC3
================================

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Keywords:     Release announcement, PHP_Depend, 0.10.0RC3, bug, ignore
:Description:
  We are proud to announce the third PHP_Depend 0.10.0 release candidate.
  It fixes a behavior change bug in the PHP_Depend's input component that 
  was introduced in release 0.10.0RC1.
  
Yesterday we have released the third release candidate `0.10.0RC3`__ of
PHP_Depend, that will hopefully the last one before the final release of
0.10.0. In this release we have closes two bugs. The first bug already exist
for a longer time in PHP_Depend. The line numbers and columns of some abstract
syntax tree nodes weren't set. The second bug was introduced in release 
`0.10.0RC1`__ and it has changed the behavior of the ``--ignore=`` option in
such a way, that absolute paths weren't ignored anymore.

- Fixed `#189`__: Invalid Start/End Line/Column for object method
  invocation. Fixed in commit `#c6cc9dd`__.
- Fixed `#191`__: New implementation of --ignore only accepts relative paths.
  Fixed in commit `#38e6b52`__.
  
As always, you can get the latest PHP_Depend version from its PEAR channel: 
`pear.pdepend.org`__:

.. class:: shell

::

  ~ $ pear channel-discover pear.pdepend.org
  ~ $ pear install pdepend/PHP_Depend-beta

you can fetch the sources from the project's GitHub reposition:

.. class:: shell

::

  ~ $ git clone git://github.com/pdepend/pdepend.git

Or download the latest version of PHP_Depend as a Phar archive:

.. class:: shell

::

  ~ $ wget http://pdepend.org/download/release/latest/pdepend.phar
  
So please download, test this release candidate and if an issue pops up, 
report it in PHP_Depend's `issue tracker`__.

__ /download/release/0.10.0rc3/changelog.html
__ /download/release/0.10.0rc1/changelog.html
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/189
__ https://github.com/pdepend/pdepend/commit/c6cc9dd
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/191
__ https://github.com/pdepend/pdepend/commit/38e6b52
__ http://pear.pdepend.org
__ http://tracker.pdepend.org/pdepend
