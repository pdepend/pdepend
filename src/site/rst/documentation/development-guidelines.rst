======================
Development Guidelines
======================

The following document contains the main guidelines for all persons that
participate on PHP_Depend's development.

Commit messages
===============

We have very strict rules for commit message formatting, which provides us
with a basis for automatic parsing and generating of reports.

All messages should wrap at 79 characters per line. This means, if you are
writing multiple lines after a message starting with a "- " each following
line should be indented by exactly two spaces.

Including descriptive text in your commit messages is generally important to
offer a good overview on the commit when the issue tracker is not available
(commit mails, history).

All messages may include references to existing issues to add status updates
to the issue, which should look like::

	- Refs #<number>: <text>

Where <number> references the ticket and the <text> describes what you did.

Comments
--------

You may always append arbitrary comments in your commit messages, where each
line should start with a number sign (#). Text in these lines won't be
checked.

Bug fix
-------

A bug fix commit message should follow the following scheme::

	- Fixed #<number>: <text>

Where <number> references the closed bug and <text> is a description of the
bug and the fix. Keep in mind that the texts will be used for the changelog,
so please check the spelling before committing.

The bug number is not optional, which means that there should be an open bug
in the issue tracker for *each* bug you fix.

For compatibility with other issue tracker you may also use "Closed" instead
of "Fixed" in your message, but "Fixed" is highly preferred.

New features
------------

If you implemented a new feature, your commit message should look like::

	- Implemented[ #<number>]: <text>

Where <text> is a short description of the feature you implemented, and
<number> may optionally reference a feature request in the bug tracker. Keep
in mind that the texts will be used for the changelog, so please check the
spelling before committing.

Documentation
-------------

If you extended your documentation, your commit message should look like::

	- Documented[ #<number>]: <text>

Where <number> optionally specifies a documentation request, and the text
describes what you documented.

Additional tests
----------------

If you added tests for some feature, your commit message should look like::

	- Tested: <text>

Where <text> describes the feature(s) you are testing.

Other commits
-------------

If your commit does not match any of the above rules you should only include a
comment in your commit message or extend this document with your commit
message of desire.

General
-------

No line of your commit message should ever exceed 79 characters per line
