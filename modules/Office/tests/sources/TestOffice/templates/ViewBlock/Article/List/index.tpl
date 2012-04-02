<?php
$list = $this->_getValueOf( 'list' );
?>
View block article list
<ol>
<?php foreach ( $list as $item ):?>
	<li><?=$item?></li>
<?php endforeach;?>
</ol>