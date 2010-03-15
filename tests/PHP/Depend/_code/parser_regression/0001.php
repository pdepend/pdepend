<?php
class Clazz {
	
	function method($anchor=true)
	{
	global $PHP_SELF;
		if ($anchor) {
	?>
		<a href="<?php echo $PHP_SELF,'?',$this->id;?>_next_page=1"><?php echo $this->first;?></a> &nbsp; 
	<?php
		} else {
			print "$this->first &nbsp; ";
		}
	}
}
