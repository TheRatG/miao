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
		<div class="control-group">
	      <?=$form->email->label()?>
	      <div class="controls">
	        <?=$form->email?>
	        <span class="help-inline">
					<ul>
			          <?foreach( $form->email->error()->getMessages() as $message ):?>
			            <li><?=$message?></li>
			          <?endforeach;?>
					</ul>
				</span>
			</div>
		</div>
		<div class="form-actions">
	      <?=$form->send?>
	      <?=$form->clear?>
	      </div>
	</fieldset>
<?=$form->end()?>