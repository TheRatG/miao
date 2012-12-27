# Miao Form
## Пример 1. Сохранение комментария

$form = new Miao_Form( 'frm_blog', '/form.php' );
$form->setMethod( 'POST' );
$form->addText( 'name' )->setLabel( 'Name' )->setRequired( 'Field name is required' );
$form->addTextArea( 'msg' )->setLabel( 'Message' )->setRequired();
$form->addSubmit( 'submit' )->setLabel( 'Test' );

$report = '';
if ( $form->isValid( $_POST ) )
{
	$data = $form->getValues();
	$report = sprintf( 'User "%s" posted message: "%s"', $data[ 'name' ], $data[ 'msg' ] );
}
?>

<?php if ( $report ): ?>
	<h4>Report: <?=$report?></h4>
<?php endif;?>

<?=$form->begin()?>

<fieldset>
	<legend>Messages</legend>
		<?=$form->name->label()?>
		<div class="controls">
			<?=$form->name?>
			<span style="color: red"><?=$form->name->error()?></span>
	</div>
		<?=$form->msg->label()?>
		<div class="controls">
			<?=$form->msg?>
			<span style="color: red"><?=$form->msg->error()?></span>
	</div>
		<?=$form->submit?>
	</fieldset>

<?=$form->end()?>
