:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This article describes the NPM - Number of Public Methods per
               Class metric in general, as it was described by Lorenz and Kidd
               in their book Object-Oriented Software Metrics and the concrete
               implementation of this metric in PHP_Depend.
:Keywords:     NPM, Number of Public Methods, Software Metric, Lorenz and Kidd

The *Number of Public Methods* or *NPM* metric was orginally described in the 
fundamental book *Object-Oriented Software Metrics* of Lorenz and Kidd
[#lkoom]_.

The NPM metric The NPM metric belongs to the group of simple count software
metrics. This means that this value simply reflects the number of public 
methods declared in a class.

A high NPM value can be an indicator for two different bad smells in the
design of a software. First it can be a signal for a class that is to 
complex and has too many responsibilities in the analyzed software system.
This can be verified by looking at `Weighted Method per Class`__ metric
for the same class. A good example for such a subject is the well known
utility class with all those methods, which are used all over the software
system.

Secondly a high NPM value can be an indicator for a class that is highly
coupled with other parts of the software, because every public method may
expose the internally used classes. Or the class acts as some kind of
context, to transfer objects between several components. This can be 
verified for possible candidates, by looking at the coupling metrics for
the same class, for example the `Coupling Between Objects`__ and the 
`Afferent Coupling`__ metric.

Thresholds
----------

An appropriate threshold for the NPM lower limit is 1, because a class should
at least define a single method that can be utilized by the application. An 
upper limit for the NPM metric of a class is harder to define, because it 
depends on the type of the class, is it a simple DTO (Data Transfer Object)
with several getter and setter methods or is it a Domain Object that defines
a single well defined service in the analyzed application.

Bibliography
------------

.. [#lkoom] http://www.pearsonhighered.com/bookseller/product/ObjectOriented-Software-Metrics/9780131792920.page

   Prentice Hall; Object-Oriented Software Metrics; Mark Lorenz, Jeff Kidd;
   1994; ISBN: 9780131792920

__ /documentation/software-metrics/weighted-method-count.html
__ /documentation/software-metrics/coupling-between-objects.html
__ /documentation/software-metrics/afferent-coupling.html
