<?php
$form = $this->getValueOf( 'form' );
?>
<?=$form->begin()?>
<fieldset>
	<div class="control-group">
		<?=$form->name->label()?>
		<div class="controls">
			<?=$form->name?>
			<span class="help-inline"><?=$form->name->error()?></span>
		</div>
	</div>
</fieldset>
<?=$form->end()?>