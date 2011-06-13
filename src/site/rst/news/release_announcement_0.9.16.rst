================================
PHP_Depend 0.9.16 bugfix release
================================

:Abstract:
  Today/Yesterday we have released the bugfix version 0.9.15 and 0.9.16 of 
  PHP_Depend. With these releases we have closed a critical bug in PHP_Depend's
  handling of namespaces, where identical class names in two different
  namespaces resulted in an endless loop, so that the php process died with
  a fatal error.

Today/Yesterday we have released the bugfix version 0.9.15 and 0.9.16 of 
PHP_Depend. With these releases we have closed a critical bug in PHP_Depend's
handling of namespaces, where identical class names in two different 
namespaces resulted in an endless loop, so that the php process died with
a fatal error.

- Fixed `#152`__: Endless loop bug for identical class and parent name. Fixed
  in svn revision `#1320`__.
- Fixed `#153`__: Only count those classes and interfaces that are flagged
  as user defined types. Fixed in subversion revision `#1327`__.
- Implemented `#154`__: Make execution order of analyzers reproducable.
  Implemented in svn revision `#1331`__.

__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/152
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1320
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/153
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1327
__ http://tracker.pdepend.org/pdepend/issue_tracker/issue/154
__ http://tracker.pdepend.org/pdepend/browse_code/revision/1331

As always, you can get the latest PHP_Depend version from its PEAR channel: 
`pear.pdepend.org`__: ::

  mapi@arwen ~ $ pear channel-discover pear.pdepend.org
  mapi@arwen ~ $ pear install pdepend/PHP_Depend-beta

__ http://pear.pdepend.org

Or you can fetch the sources from the subversion reposition: ::

  mapi@arwen ~ $ svn co http://svn.pdepend.org/trunk pdepend

And additionally you can find a repository mirror on github: ::

  mapi@arwen ~ $ git clone git://github.com/manuelpichler/pdepend.git


