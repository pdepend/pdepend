<?php

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
function doesNothing() {
    if (true AND true AND false) return;
    /** This comment breaks SuppressWarnings */
    if (true AND true AND false) return;
    if (true AND true AND false) return;
    if (true AND true AND false) return;
}
