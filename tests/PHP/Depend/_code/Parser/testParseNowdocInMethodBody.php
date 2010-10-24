<?php
namespace nspace;

class Clazz {
    function method() {
        $code = <<< "EOD"
<?php
    if (true) {
        require '$cache';
    } else {
    }
?>
EOD;
    }
}
