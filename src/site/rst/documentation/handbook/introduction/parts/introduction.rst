Introduction
============

PHP_Depend is a small program that performs static code analysis 
on a given source base. Static code analysis means that PHP_Depend
first takes the source code and parses it into an easily 
processable internal data structure. This data structure is 
normally called an `AST`__ (*Abstract Syntax Tree*), that represents
the different statements and elements used in the analyzed source
base. Then it takes the generated AST and measures several values,
the so called software metrics. Each of this values stands for a
quality aspect in the the analyzed software, observed from a very
high level of abstraction, because no source was reviewed manually
until now.

__ http://en.wikipedia.org/wiki/Abstract_syntax_tree

What is a software metric?
==========================

Okay, so what are these software metrics? Normally software metrics
are really simple things. They are just the sum of some statements
or code fragments found in the analyzed source. For example, the
`Cyclomatic Complexity`__ or *CCN* of a method is just the sum of 
all logical statements, like ``if``, ``for`` etc., in the analyzed
method. This means a *Cyclomatic Complexity* value of ``23`` only 
says that there are 23 statements in the analyzed method. You can
now take this value and compare it with your own or others 
experience, when a piece of software gets unmaintainable due to its
complexity.

__ http://en.wikipedia.org/wiki/Cyclomatic_complexity

And why should I use PHP_Depend?
================================

That's a really good question! Why should you use a just another
tool in your daily development process to perform such a simple
task like building source statistics? The answer is easy. 

* PHP_Depend can be used in an automated build environment and the
  generated reports are always objective, it just measures the quality
  facts of a given source base. 
* PHP_Depend scales with growing source bases, where human code
  reviews will fail at some day.
* PHP_Depend allows you to indentify suspect parts in a software
  system that should be part of a code review, without looking into
  the source.
* PHP_Depend also supports some fancy metrics that will become very
  useful, when you have reached certain level of metrics knowledge.

Conclusion
==========

Software metrics as they are provided by tools like PHP_Depend are
really useful utilities to improve the productivity of development-
and quality-assurance-teams. But metrics are only indications for
possible problem areas. But they are just indications and no holy
grail you should follow blind.
