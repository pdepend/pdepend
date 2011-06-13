================================
PHP_Depend 0.9.19 bugfix release
================================

:Abstract:
  Yesterday we have released the bugfix version 0.9.19 of PHP_Depend. With
  this release we have closed a parsing error, that caused PHP_Depend's 
  parser to throw an exception when it detected a literal dollar token in
  a double quote string.

Yesterday we have released the bugfix version 0.9.19 of PHP_Depend. With
this release we have closed a parsing error, that caused PHP_Depend's  
parser to throw an exception when it detected a literal dollar token in
a double quote string. ::

  <?php
  function foo()
  {
      return "$bar$";
  }

- Fixed `#162`__: Parser does not recognize $ string literal in string.
  Fixed in svn revision `#1381`__.

__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/162
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1381

As always, you can get the latest PHP_Depend version from its PEAR channel: 
`pear.pdepend.org`__: ::

  mapi@arwen ~ $ pear channel-discover pear.pdepend.org
  mapi@arwen ~ $ pear install pdepend/PHP_Depend-beta

__ http://pear.pdepend.org

Or you can fetch the sources from the subversion reposition: ::

  mapi@arwen ~ $ svn co http://svn.pdepend.org/trunk pdepend

And additionally you can find a repository mirror on github: ::

  mapi@arwen ~ $ git clone git://github.com/manuelpichler/pdepend.git


