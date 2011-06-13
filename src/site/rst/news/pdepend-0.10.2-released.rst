==============
Version 0.10.2
==============

:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  We are proud to announce the latest release of PHP_Depend,
               version 0.10.2. This version closes two minor bugfixes in
               PHP_Depend and implements three new features, with totally
               four new software metrics that can be calculated. Beside that
               we have changed the source directory structure to a maven like
               standard layout.
:Keywords:     Release, Version, Features, Bugfixes, Software metrics, Maven layout, Release announcement

We are proud to announce that version `0.10.2`__ of PHP_Depend was released 
on February the 27th 2011. This release of PHP_Depend closes two bugs. One 
related to the start and end line properties of object property nodes in the 
syntax tree. The second fix closes a bug in PHP_Depend's implementation of
the WMCi metric. Beside these two fixes this release implements three minor 
features, one design issue in the syntax tree api and the other two other 
features are related to the new metrics `CE`__, `CA`__, `CBO`__ and `NPM`__.

Additionally we have restructured PHP_Depend's directory structure from a
custom, freestyle format to a directory layout that is similar to maven's
convention. With this change we have fixed several issues and workarounds
in PHP_Depend's build process.

Bugfixes
--------

- Fixed `#9936901`__: WMCi calculation is incorrect for overwritten methods.
  Fixed with commit `#69d079a`__.
- Fixed `#8927377`__: Invalid Start/End Line/Column for object property access.
  Fixed with commit `#fc57264`__.

Features
--------

- Implemented `#9069393`__: Replace optional NULL argument of setPackage()
  with separate method. Implemented with commit `#1282cdb`__.
- Implemented `#9069871`__: Implement efferent- and afferent-coupling for
  classes. Implemented with commit `#07537c2`__.
- Implemented `#9997915`__: Implement Number of Public Methods metric.
  Implemented with commit `#2dd3ebf`__.

Download
--------

Download the latest version of PHP_Depend `here`__.

__ /download/release/0.10.2/changelog.html
__ /documentation/software-metrics/efferent-coupling.html
__ /documentation/software-metrics/afferent-coupling.html
__ /documentation/software-metrics/coupling-between-objects.html
__ /documentation/software-metrics/number-of-public-methods.html
__ https://www.pivotaltracker.com/story/show/9936901
__ https://github.com/pdepend/pdepend/commit/69d079a
__ https://www.pivotaltracker.com/story/show/8927377
__ https://github.com/pdepend/pdepend/commit/fc57264
__ https://www.pivotaltracker.com/story/show/9069393
__ https://github.com/pdepend/pdepend/commit/1282cdb
__ https://www.pivotaltracker.com/story/show/9069871
__ https://github.com/pdepend/pdepend/commit/07537c2
__ https://www.pivotaltracker.com/story/show/9997915
__ https://github.com/pdepend/pdepend/commit/2dd3ebf
__ /download/release/0.10.2/changelog.html

