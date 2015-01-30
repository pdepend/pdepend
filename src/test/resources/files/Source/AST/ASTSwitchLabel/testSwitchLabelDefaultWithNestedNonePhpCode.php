<?php 
function testSwitchLabelWithNestedNonePhpCode()
{
?>
<?php switch($var): ?>
<?php default: ?>
    var a = "default";
    <?php break; ?>
<?php endswitch; ?>
<?php
}
?>
