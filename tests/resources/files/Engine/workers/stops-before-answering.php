<?php

// A parse worker that stops before it answers for its first file, as it would after a fatal error.
fgets(STDIN);

exit(3);
