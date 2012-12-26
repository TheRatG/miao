# Miao Form
## Пример 1. Сохранение комментария

$form = new Miao_Form( 'comment' );

$form->addText( 'username' )
	->setLabel( 'Имя' )
	->setRequired( 'Поле имя должно быть заполнено' );
	
$form->addTextArea( 'msg' );