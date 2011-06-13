:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This article describes the Object-Oriented software metric CA -
               Afferent Coupling. It can be used to measure the responsibility
               of a component, package, class or method within a software 
               system. The content of this article is based on UncleBob's
               whitepaper "OO Design Quality Metrics - An Analysis of 
               Dependencies", published 1994.
:Keywords:     CA, Afferent Coupling, UncleBob, Robert C. Martin, Class Coupling, Package Coupling, Responsibility, Testing

The *Afferent Coupling* or *CA* metric describes the number of unique 
incoming dependencies into a software artifact. An artifact can be
nearly everything in an Object-Oriented software system, e.g. a
component, package, class, method or property. Therefore the *Afferent
Coupling* is an indicator for the responsibility that the artifact has
in the analyzed software system. The higher this value is the higher is
the artifact's responsibility. 

Normally responsibility isn't a bad thing in a software system. Good 
examples are core packages and components, like error and exception 
handling, or the used unit testing framework. All these lowlevel 
components have usually a very high *Afferent Coupling*, because they
are utilized by the several parts of the software. On the other hand
these components will rarely be changed during the lifetime of an 
application, but when they must be changed, it can have side effects 
on the overall stability of application. Therefore it is important to
have a really good test coverage for components with a high *Afferent 
Coupling* and to monitor all changes to these components carefully.

A detailed description of the *CA* metric and it's impact on the stability 
of a software system can be found in UncleBob's whitepaper "OO Design Quality
Metrics - An Analysis of Dependencies" [#ubdqm]_.

Thresholds
----------

It is really hard to give a threshold for the *Afferent Coupling* metric and
the only answer can be **It depends**. Let's take the testing framework, this
component can have a very high *Afferent Coupling* value, because it will 
hopefully be utilized by all other components. But a good policy for this 
metric is, the more concrete a software artifact is, the less the *Afferent
Coupling* value should be.

See also
--------

- `Coupling Between Objects`__: *Coupling Between Objects* or *CBO* is the
  oposite coupling metric for a software artifact.

- `Efferent Coupling`__: The *Efferent Coupling* or *CE* is a software metric
  that describes the reverse perspective on dependencies.

Bibliography
------------

.. [#ubdqm] http://www.objectmentor.com/publications/oodmetrc.pdf

  Object Mentor; OO Design Quality Metrics - An Analysis of Dependencies;
  Robert C. Martin; 1994

__ /documentation/software-metrics/coupling-between-objects.html
__ /documentation/software-metrics/efferent-coupling.html

