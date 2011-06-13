==============
Version 0.10.1
==============

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This document describes the bugfixes of the PHP_Depend
               release 0.10.1. This version only contains a single bugfix
               that closes an E_NOTICE issue in PHP_Depend's cache and
               serialization code.
:Keywords:     Release, Version, Features, Bugfixes, Performance

Version 0.10.1 of PHP_Depend was released on February the 06th 2011. The
only change in this release was a bugfix that closes an E_NOTICE issue about
an undefined class or object property. This notice only occured during the
deserialization of cache closure instances. 

It is recommand to update you current PHP_Depend installation to version
0.10.1, to avoid annoying side effect or false positives in your build
environment.

Bugfixes
--------

- Fixed `#9634613`__: Notice: Undefined property $___temp___. Fixed with
  commit `#5fb6900`__.

Download
--------

Download the latest version of PHP_Depend as a PHAR archive `here`__.

__ https://www.pivotaltracker.com/story/show/9634613
__ https://github.com/pdepend/pdepend/commit/5fb6900
__ /download/release/0.10.1/pdepend.phar

