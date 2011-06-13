================================
PHP_Depend 0.9.14 bugfix release
================================

:Abstract:
  Today we have released the bugfix version 0.9.14 of PHP_Depend. The software
  metrics tool for PHP applications. This release contains two bugfixes. The
  first one closes an issue in PHP_Depend's file cache engine and the second
  one closes a minor issue, where PHP_Depend throws an exception with an empty
  exception message.

Today we have released the bugfix version 0.9.14 of PHP_Depend. The software
metrics tool for PHP applications. This release contains two bugfixes. The
first one closes an issue in PHP_Depend's file cache engine and the second
one closes a minor issue, where PHP_Depend throws an exception with an empty
exception message.

- Fixed `#149`__: Exception Message is empty. Fixed in svn revision `#1277`__.
- Concurrency issue in PHP_Depend's file cache fixed.

__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/149
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1277

As always, you can get the latest PHP_Depend version from its PEAR channel: 
`pear.pdepend.org`__: ::

  mapi@arwen ~ $ pear channel-discover pear.pdepend.org
  mapi@arwen ~ $ pear install pdepend/PHP_Depend-beta

__ http://pear.pdepend.org

Or you can fetch the sources from the subversion reposition: ::

  mapi@arwen ~ $ svn co http://svn.pdepend.org/trunk pdepend

And additionally you can find a repository mirror on github: ::

  mapi@arwen ~ $ git clone git://github.com/manuelpichler/pdepend.git


