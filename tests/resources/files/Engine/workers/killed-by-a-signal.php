<?php

// A parse worker that is killed while it parses its first file, as the out-of-memory killer of the kernel would kill it.
fgets(STDIN);
posix_kill(posix_getpid(), 9);
