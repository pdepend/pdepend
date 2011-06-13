==========================
PHP_Depend 0.10.0 released
==========================

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  We are proud to announce the final release of PHP_Depend
               version 0.10.0. This version only contains a small bugfix 
               compared to the last release canditate that closes an issue 
               with PHP_Depend's behavior when it was started from \*.phar
               archive.
:Keywords:     Release, Version, Features, Bugfixes, Performance, Release announcement, 0.10.0

We are proud to announce the final release of PHP_Depend version 0.10.0. 
This version only contains a small bugfix compared to the last release 
canditate. Version 0.10.0 of PHP_Depend was released on February the 
05th 2011. The key feature for this release is the overall performance 
of PHP_Depend. Therefore we have implemented a new caching layer that
reuses already calculated analyzes-results much more efficient than older
versions of PHP_Depend. With these modifications we have achieved a
performance gain of 100% and more for consecutive analysis-runs.

This final release only fixes a small bug in PHP_Depend's analyzer class
locator that has caused some issues when PHP_Depend was executed as an
external dependency that uses a \*.phar archive as distribution format.

Bugfixes
--------

- Fixed `#9623949`__: Analyzer locator does not work from phar
  archive. Fixed in commit `#f53dca9`__.
- PHP_Depend has moved the complete issue tracking infrastructure
  to `PivotalTracker`__.

Download
--------

Download the latest version of PHP_Depend as a PHAR archive `here`__.

__ https://www.pivotaltracker.com/story/show/9623949
__ https://github.com/pdepend/pdepend/commit/f53dca9
__ https://www.pivotaltracker.com/projects/146589
__ /download/release/0.10.0/pdepend.phar

