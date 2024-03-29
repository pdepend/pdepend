You should test that PHP_Depend was correctly installed and can be used from the
command line:

.. class:: shell

::

  ~ $ pdepend --version
  PHP_Depend 0.9.4 by Manuel Pichler

Now that PHP_Depend was installed correct and works, run PHP_Depend against its
own source and generate an XML report and some charts. 

.. class:: shell

::

  ~ $ pdepend --summary-xml=/tmp/summary.xml \
                         --jdepend-chart=/tmp/jdepend.svg \
                         --overview-pyramid=/tmp/pyramid.svg \
                         /usr/local/share/pear/PDepend
  PHP_Depend 0.9.4 by Manuel Pichler

  Parsing source files:
  ............................................................    60
  ............................                                    88

  Executing NPathComplexity-Analyzer:
  ...........................................                    869

  Executing Coupling-Analyzer:
  ...........................................................   1193

  Executing NodeCount-Analyzer:
  .................................                              677

  Executing Dependency-Analyzer:
  ...................................                            703

  Executing Hierarchy-Analyzer:
  ...........................................                    880

  Executing Inheritance-Analyzer:
  .....................                                          421

  Executing CodeRank-Analyzer:
  ......                                                         126

  Executing CyclomaticComplexity-Analyzer:
  ...........................................                    869

  Executing ClassLevel-Analyzer:
  .......................................                        783

  Executing NodeLoc-Analyzer:
  ...............................................                951

  Generating pdepend log files, this may take a moment.

  Time: 00:13; Memory: 15.00Mb

This command has produced one XML-report named *summary.xml* that contains a
summary of all metrics collected for the analyzed php source code. ::

  <?xml version="1.0" encoding="UTF-8"?>
  <metrics ahh="0.19444444444444" andc="0.46268656716418" calls="1406" ccn="1203" ccn2="1237" cloc="11657" clsa="7" clsc="60" eloc="6528" fanout="571" leafs="59" loc="20078" maxDIT="2" ncloc="8421" noc="67" nof="0" noi="21" nom="578" nop="11" roots="5">
    <files>
      <file name="/usr/local/pear/PEAR/PDepend/Parser.php" cloc="324" eloc="534" loc="997" ncloc="673"/>
      <file name="/usr/local/pear/PEAR/PDepend/StorageRegistry.php" cloc="81" eloc="18" loc="103" ncloc="22"/>
      ...
    </files>
    <package name="PHP_Depend" cr="1.3005761647303" noc="3" nof="0" noi="4" nom="51" rcr="0.50515422957667">
      <class name="\PDepend\Source\Language\PHP\AbstractPHPParser" cis="12" cloc="250" cr="0.15" csz="113" dit="0" eloc="526" impl="1" loc="913" ncloc="663" nom="20" rcr="0.1925" vars="6" varsi="6" varsnp="0" wmc="107" wmci="107" wmcnp="12">
        <file name="/usr/local/pear/PEAR/PDepend/Parser.php"/>
        <method name="__construct" ccn="1" ccn2="1" cloc="0" eloc="4" loc="6" ncloc="6" npath="1"/>
        <method name="_consumeComments" ccn="3" ccn2="3" cloc="0" eloc="10" loc="12" ncloc="12" npath="3"/>
        ...
      </class>
      <class name="PHP_Depend_StorageRegistry" cis="3" cloc="25" cr="0.15" csz="4" dit="0" eloc="15" impl="0" loc="43" ncloc="18" nom="2" rcr="0.15" vars="1" varsi="1" varsnp="0" wmc="3" wmci="3" wmcnp="3">
        <file name="/usr/local/pear/PEAR/PDepend/StorageRegistry.php"/>
        <method name="get" ccn="2" ccn2="2" cloc="0" eloc="7" loc="8" ncloc="8" npath="2"/>
        <method name="set" ccn="1" ccn2="1" cloc="0" eloc="3" loc="4" ncloc="4" npath="1"/>
      </class>
      ...
    </package>
    ...
  </metrics>

And you will get two charts. The first one shows the inter package dependencies,
similar to those charts generated by `JDepend`__ and the second one shows a
visual summary of the analyzed project source code.

.. image:: /images/jdepend.png
   :width:  400
   :alt:    Package chart as described by Uncle Bob Martin
   :align:  center
   :target: /documentation/handbook/reports/abstraction-instability-chart.html

.. image:: /images/pyramid.png
   :width:  400
   :alt:    Metrics overview pyramid as described by Michele Lanza
   :align:  center
   :target: /documentation/handbook/reports/overview-pyramid.html

You should read the `software metrics`__ section of the documentation for further
details on the metrics generated by PHP_Depend.

__ https://pdepend.org/documentation/software-metrics/index.html
__ /documentation/software-metrics.html
