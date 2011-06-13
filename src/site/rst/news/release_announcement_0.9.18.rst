================================
PHP_Depend 0.9.18 bugfix release
================================

:Abstract:
  Yesterday we have released the bugfix version 0.9.18 of PHP_Depend. With
  this release we have closed a parsing error, that prevented PHP_Depend 
  from handling those source files that use some wired syntax in 
  foreach-statements.

Yesterday we have released the bugfix version 0.9.18 of PHP_Depend. With
this release we have closed a parsing error, that prevented PHP_Depend 
from handling those source files that use some special(wired?) syntax in 
foreach-statements. ::

  <?php
  function foobar($obj)
  {
      foreach ( $message as $obj->foo => Clazz::$bar )
      {
      }
  }

- Fixed `#161`__: Unexpected token: -> in foreach. Fixed in svn revision
  `#1347`__.

__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/152
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1355

As always, you can get the latest PHP_Depend version from its PEAR channel: 
`pear.pdepend.org`__: ::

  mapi@arwen ~ $ pear channel-discover pear.pdepend.org
  mapi@arwen ~ $ pear install pdepend/PHP_Depend-beta

__ http://pear.pdepend.org

Or you can fetch the sources from the subversion reposition: ::

  mapi@arwen ~ $ svn co http://svn.pdepend.org/trunk pdepend

And additionally you can find a repository mirror on github: ::

  mapi@arwen ~ $ git clone git://github.com/manuelpichler/pdepend.git


