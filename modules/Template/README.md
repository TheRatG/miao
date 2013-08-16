# Miao Template

Native template engine. Use php buffer.
By default all exception will be consume. You can enable exception using *\Miao\Template::setConsumeException( false )*,
or throw exception instance of *\Miao\Template\Exception\Critical*.

## Example 1
```php
#file: /www/project/templates/main.tpl

This main template. Value = <?=$this->getValueOf( 'value' );.

```

```php
<?php
#file: test.php

$template = new \Miao\Template( '/www/project/templates', false );
$template->setValueOf( $value, 'value' );
$content = $template->fetch( 'main.tpl' );
echo $content;
```