:Author:       Manuel Pichler
:Copyright:    All rights reserved
:Description:  This article describes the overview pyramid chart generated
               by PHP_Depend. The main purpose of the overview pyramid is to
               visualize several key quality metrics of a complete project
               in a single compact form. The pyramid was first described by
               Michele Lanza's book "Object-Oriented Metrics in practice: 
               Using Software Metrics to characterize, evaluate, improve the
               Design of Object-Oriented Systems".
:Keywords:     Michele Lanza, Radu Marinescu,Stéphane Ducasse, metrics, report,
               Overview Pyramid, chart, visualization

.. contents::
   :depth: 2

The Overview Pyramid is used to visualize a complete software system in
a really compact manner. Therefor it collects a set of metrics from the
categories *Inheritance*, *Coupling* and *Size* & *Complexity*, and puts
them into relation. The following figure shows the base structure of the
Overview Pyramid [#ML06]_.

.. image::  /documentation/handbook/reports/media/report-overview-pyramid-base-thumb.png
   :width:  400
   :alt:    Base structure of the Overview Pyramid.
   :align:  center
   :target: /documentation/handbook/reports/media/report-overview-pyramid-base.png

Metrics used by the Overview Pyramid
====================================

The following three lists contain all the metrics, which the Overview
Pyramid uses.

Size and Complexity
```````````````````

The category *Size* & *Complexity* contributes the greatest and mostly
used set of software metrics.

NOP
  The *Number Of Packages* metric counts the packages within the analyzed 
  software system.

NOC
  The *Number Of Classes* metric counts the declared classes within the
  analyzed software system.

NOM
  The *Number Of Methods* metric counts all declared methods, which in
  this context means class methods and simple functions.

LOC
  The *Lines Of Code* metric shows the number of executable source lines
  within the analyzed software system. To calculate this value PHP_Depend
  counts all non whitespace lines and all non comment lines.

`CYCLO`__
  The *Cyclomatic Complexity* number [#cabeccn]_ is a software metric
  (measurement). It was already developed in 1976 by Thomas J. McCabe
  and is used to calculate the complexity of a program. It directly
  measures the number of linearly independent paths through a program's
  source code.

Coupling
````````

This group of metrics informs about the coupling between different program
parts in the analyzed application.

CALLS
  This metric count the number of distinct function- and method-calls. 
  Distinct means that one and the same method-call within a function- or
  method-body is only counted once.

FANOUT
  The *FANOUT* provides information on types referenced by classes and
  interfaces. It only counts those types that are not part of the same
  Inheritance branch.

Inheritance
```````````

Both metrics in this group deal with the use of Inheritance and give a
general overview of the use of Inheritance within the analyzed system.

ANDC
  The *Average Number of Derived Classes* metric describes the average 
  of derived classes. In a system of ten classes an *ANDC*-value of 0.5
  means, that every second class is derived from another class.

AHH
  The *Average Hierarchy Height* metric is a average depth of the 
  inheritance hierarchy. In a system of ten classes, a *AHH*-value of 1
  can be interpreted in different ways, for example: Five classes inherit
  from five other classes within the analyzed application or five classes
  inherit from a single root class.

Structure of the Overview Pyramid
=================================

Now that we know all metrics used for the Overview Pyramid, it is time to
replace the placeholders with the measured informations. The figure below
shows the filled Overview Pyramid.

.. image::  /documentation/handbook/reports/media/report-overview-pyramid-filled-thumb.png
   :width:  400
   :alt:    The filled Overview Pyramid
   :align:  center
   :target: /documentation/handbook/reports/media/report-overview-pyramid-filled.png

In a second step, the previously independent metrics are set into relation.
Therefor we calculate the average values of individual value pairs, these
computed values provide us with new informations about the distributions
within the application.

The following example figure of the Overview Pyramid contains a computed 
value for the measured *LOC* and *NOM* metric which shows us, that in the
average each operation has 25 lines of code. This value can be described 
as very high, especially when you consider that most systems contain a
variety of simple operation, like Getter and Setter, in addition to the
main application logic.

.. image::  /documentation/handbook/reports/media/report-overview-pyramid-average-thumb.png
   :width:  400
   :alt:    Computed average values in the Overview Pyramid
   :align:  center
   :target: /documentation/handbook/reports/media/report-overview-pyramid-average.png

To take reasonable conclusions from the computed values one important 
part is still missing, an adequate set of reference values. Without 
reference values, that say what values are low, average or high, it is
not possible to classify these results. The current version of 
PHP_Depend supports a single set of reference values, this set was
taken from [#ML06]_.

**Reference values**

============ ==== ======= ====
Metric	     Low  Average High
============ ==== ======= ====
CYCLO/LOC    0.16 0.20    0.24
LOC/NOM	     7    10      13
NOM/NOC	     4    7       10
NOC/NOP	     6    17      26
CALLS/NOM    2.01 2.62    3.2
FANOUT/CALLS 0.56 0.62    0.68
ANDC	     0.25 0.41    0.57
AHH          0.09 0.21    0.32
============ ==== ======= ====

With these reference values PHP_Depend can classify the computed results. 
PHP_Depend uses this information for the generation of colored backgrounds,
so that the color already supports the categorization.

.. image::  /documentation/handbook/reports/media/report-overview-pyramid-complete-thumb.png
   :width:  400
   :alt:    The complete Overview Pyramid
   :align:  center
   :target: /documentation/handbook/reports/media/report-overview-pyramid-complete.png

The benefit of the Overview Pyramid
===================================

Of course, the final question is, which advantages offers the Overview
Pyramid?

The Overview Pyramid provides a simple and size indipendent way to get a
first impression of a software system, and this without an expensive source
code analysis. Thus the Overview Pyramid is an effective tool for a first
cost estimate for an unknown system. With the help of this tool and know-how,
an experienced developer will quickly get a first impression and will know 
what can be expected from the analyzed application. And this knowledge could
be a good help during the planning phase of a new project.

.. [#ML06] **Object-Oriented Metrics in Practice**

  © Springer-Verlag Berlin Heidelberg; ISBN 978-3-540-24429-5; *Using Software
  Metrics to Characterize, Evaluate, and Improve the Design of Object-Oriented
  Systems*; Michele Lanza, Radu Marinescu; 2006

.. [#cabeccn] http://www.literateprogramming.com/mccabe.pdf

  IEEE Transactions on Software Enginerring; *A Complexity Measure*;
  Thomas J. McCabe; 1976

__ /documentation/software-metrics/cyclomatic-complexity.html
