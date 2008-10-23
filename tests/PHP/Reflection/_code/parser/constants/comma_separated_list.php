<?php
class PHP_Reflection {
    /** Test comment C_FOO */
    const C_FOO = 0,
          // Test comment C_BAR
          C_BAR = null,
          /** Test comment C_FOOBAR */
          C_FOOBAR /* Ignore */ = 42,
          /* Test comment C_BARFOO */
          C_BARFOO = 23 # Ignore
              ;
}
?>