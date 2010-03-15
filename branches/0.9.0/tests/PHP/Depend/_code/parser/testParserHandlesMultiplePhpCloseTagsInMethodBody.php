<?php
/**
 * @package package
 */
class Clazz {
    function method() {
?>
        <a href="<?php echo $PHP_SELF,'?',$this->id;?>"><?php echo $this->first;?></a>
	<?php
	}
}
