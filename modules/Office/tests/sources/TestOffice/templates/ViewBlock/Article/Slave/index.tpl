<?php
$list = $this->_getValueOf( 'list' );
$section = $this->_getValueOf( 'section' );
?>
View block article slave: <?=$section?>

<ol>
<?php foreach ( $list as $item ):?>
	<li><?=$item?></li>
<?php endforeach;?>
</ol>