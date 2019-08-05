========================================
Software metrics supported by PHP_Depend
========================================

PHP Depend can generate a large set of software metrics from a given code base,
these values can be used to measure the quality of a software project and they
help to identify that parts of an application where a refactoring should be
applied.

This section will give a brief overview of the software metrics provided by PHP
Depend and it tries to give an introduction how to interpret them in the context
of an application's code base.


+--------+--------------------------------------------+---------+---------+------+-------+--------+
| Metric | Description                                | Project | Package | File | Class | Method |
+========+============================================+=========+=========+======+=======+========+
| ahh    | *Average Hierarchy Height*                 | X       |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        | The average of the maximum length from a   |         |         |      |       |        |
|        | root class to its deepest subclass         |         |         |      |       |        |
|        | subclass                                   |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| andc   | *Average Number of Derived Classes*        | X       |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        | The average of direct subclasses of a      |         |         |      |       |        |
|        | class                                      |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| ca     | `Afferent Coupling`__                      |         |         |      | X     |        |
|        |                                            |         |         |      |       |        |
|        | Number of unique incoming dependencies     |         |         |      |       |        |
|        | from other artifacts of the same type.     |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| calls  | *Number of Method or Function Calls*       | X       |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| cbo    | `Coupling Between Objects`__               |         |         |      | X     |        |
|        |                                            |         |         |      |       |        |
|        | Number of unique outgoing dependencies     |         |         |      |       |        |
|        | to other artifacts of the same type.       |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| ccn    | `Cyclomatic Complexity Number`__           | X       |         |      |       | X      |
|        |                                            |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| ccn2   | `Extended Cyclomatic Complexity Number`__  | X       |         |      |       | X      |
|        |                                            |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| ce     | `Efferent Coupling`__                      |         |         |      | X     |        |
|        |                                            |         |         |      |       |        |
|        | Number of unique outgoing dependencies     |         |         |      |       |        |
|        | to other artifacts of the same type.       |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| cis    | `Class Interface Size`__                   |         |         |      | X     |        |
|        |                                            |         |         |      |       |        |
|        | Number of non private methods and          |         |         |      |       |        |
|        | properties of a class:                     |         |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        |   **CIS = public(NOM + VARS)**             |         |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        | Measures the size of the interface from    |         |         |      |       |        |
|        | other parts of the system to a class.      |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| cloc   | *Comment Lines of Code*                    | X       |         | X    | X     | X      |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| clsa   | *Number of Abstract Classes*               | X       |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| clsc   | *Number of Concrete Classes*               | X       |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| cr     | *Code Rank*                                |         | X       |      | X     |        |
|        |                                            |         |         |      |       |        |
|        | Google PageRank applied on Packages        |         |         |      |       |        |
|        | and Classes. Classes with a high           |         |         |      |       |        |
|        | value should be tested frequently.         |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| csz    | `Class Size`__                             |         |         |      | X     |        |
|        |                                            |         |         |      |       |        |
|        | Number of methods and properties of a      |         |         |      |       |        |
|        | class:                                     |         |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        |   **CSZ = NOM + VARS**                     |         |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        | Measures the size of a class concerning    |         |         |      |       |        |
|        | operations and data.                       |         |         |      |       |        |
|        |                                            |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| dit    | *Depth of Inheritance Tree*                |         |         |      | X     |        |
|        |                                            |         |         |      |       |        |
|        | Depth of inheritance to root class         |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| eloc   | *Executable Lines of Code*                 | X       |         | X    | X     | X      |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| fanout | *Number of Fanouts*                        | X       |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        | Referenced Classes                         |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| hb     | *Halstead Bugs*                            |         |         |      |       | X      |
|        |                                            |         |         |      |       |        |
|        | Estimated number of errors                 |         |         |      |       |        |
|        | in the implementation                      |         |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        |   **HB = POW(HE, 2/3) / 3000**             |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| hd     | *Halstead Difficulty*                      |         |         |      |       | X      |
|        |                                            |         |         |      |       |        |
|        | The difficulty of the program to write or  |         |         |      |       |        |
|        | understand, e.g. when doing code review    |         |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        |   **HD = (n1 / 2) * (N2 / n2)**            |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| he     | *Halstead Effort*                          |         |         |      |       | X      |
|        |                                            |         |         |      |       |        |
|        | Measures the amount of mental activity     |         |         |      |       |        |
|        | needed to translate the existing algorithm |         |         |      |       |        |
|        | into implementation                        |         |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        |   **HE = HV * HD**                         |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| hi     | *Halstead Intelligence Content*            |         |         |      |       | X      |
|        |                                            |         |         |      |       |        |
|        | Determines the amount of intelligence      |         |         |      |       |        |
|        | stated in the program                      |         |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        |   **HI = HV / HD**                         |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| hl     | *Halstead Level*                           |         |         |      |       | X      |
|        |                                            |         |         |      |       |        |
|        |   **HL = 1 / HD**                          |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| hnd    | *Halstead Vocabulary*                      |         |         |      |       | X      |
|        |                                            |         |         |      |       |        |
|        | The total number of unique operator and    |         |         |      |       |        |
|        | unique operand occurrences                 |         |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        |   **HND = n1 + n2**                        |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| hnt    | *Halstead Length*                          |         |         |      |       | X      |
|        |                                            |         |         |      |       |        |
|        | The total number of operator occurrences   |         |         |      |       |        |
|        | and the total number of operand occurrences|         |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        |   **HND = N1 + N2**                        |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| ht     | *Halstead Programming Time*                |         |         |      |       | X      |
|        |                                            |         |         |      |       |        |
|        | Shows time needed to translate             |         |         |      |       |        |
|        | the existing algorithm into implementation |         |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        |   **HT = HE / 18**                         |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| hv     | *Halstead Volume*                          |         |         |      |       | X      |
|        |                                            |         |         |      |       |        |
|        | Represents the size, in bits, of space     |         |         |      |       |        |
|        | necessary for storing the progream         |         |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        |   **HV = N * log2(n)**                     |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| leafs  | *Number of Leaf Classes*                   | X       |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        | (final) classes                            |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| lloc   | *Logical Lines Of Code*                    | X       |         | X    | X     | X      |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| loc    | *Lines Of Code*                            | X       |         | X    | X     | X      |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| maxDIT | *Max Depth of Inheritance Tree*            | X       |         |      |       |        |
|        |                                            |         |         |      |       |        |
|        | Maximum depth of inheritance               |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| mi     | *Maintainability Index*                    |         |         |      |       | X      |
|        |                                            |         |         |      |       |        |
|        | Calculates an index value between 0 and 100|         |         |      |       |        |
|        | that represents the relative ease of       |         |         |      |       |        | 
|        | maintaining the code                       |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| noam   | *Number Of Added Methods*                  |         |         |      | X     |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| nocc   | *Number Of Child Classes*                  |         |         |      | X     |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| noom   | *Number Of Overwritten Methods*            |         |         |      | X     |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| ncloc  | *Non Comment Lines Of Code*                | X       |         | X    | X     | X      |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| noc    | *Number Of Classes*                        | X       | X       |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| nof    | *Number Of Functions*                      | X       | X       |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| noi    | *Number Of Interfaces*                     | X       | X       |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| nom    | *Number Of Methods*                        | X       | X       |      | X     |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| npm    | `Number of Public Methods`__               |         |         |      | X     |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| npath  | *NPath Complexity*                         |         |         |      |       | X      |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| nop    | *Number of Packages*                       | X       |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| rcr    | *Reverse Code Rank*                        |         | X       |      | X     |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| roots  | *Number of Root Classes*                   | X       |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| vars   | *Properties*                               |         |         |      | X     |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| varsi  | *Inherited Properties*                     |         |         |      | X     |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| varsnp | *Non Private Properties*                   |         |         |      | X     |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| wmc    | `Weighted Method Count`__                  |         |         |      | X     |        |
|        |                                            |         |         |      |       |        |
|        | The WMC metric is the sum of the           |         |         |      |       |        |
|        | complexities of all declared methods and   |         |         |      |       |        | 
|        | constructors of class.                     |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| wmci   | *Inherited Weighted Method Count*          |         |         |      | X     |        |
|        |                                            |         |         |      |       |        |
|        | Same as wmc, but only inherited methods.   |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+
| wmcnp  | *Non Private Weighted Method Count*        |         |         |      | X     |        |
|        |                                            |         |         |      |       |        |
|        | Same as wmc, but only non private methods. |         |         |      |       |        |
+--------+--------------------------------------------+---------+---------+------+-------+--------+

__ /documentation/software-metrics/afferent-coupling.html
__ /documentation/software-metrics/coupling-between-objects.html
__ /documentation/software-metrics/cyclomatic-complexity.html
__ /documentation/software-metrics/cyclomatic-complexity.html
__ /documentation/software-metrics/efferent-coupling.html
__ /documentation/software-metrics/class-interface-size.html
__ /documentation/software-metrics/class-size.html
__ /documentation/software-metrics/number-of-public-methods.html
__ /documentation/software-metrics/weighted-method-count.html
