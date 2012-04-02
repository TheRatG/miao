<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?=$this->_includeBlock( 'Meta' )?>
    <?=$this->_includeTemplate( 'shared/includes.tpl', true );?>
	</head>
<body>
<?=$this->_includeTemplate( 'shared/counters.tpl', true );?>
  	<div class="all_page">
		<div class="all_page2">
			  <?=$this->_includeTemplate( 'shared/header.tpl', true );?>

			  <div class="body1">
			  	  <?=$this->_includeTemplate( $this->getViewTpl(), true );?>
			  </div>
		</div>
	</div>
	<div class="footer" id="footer">
		<div class="footer2">
	  		<?=$this->_includeTemplate( 'shared/footer.tpl', true );?>
	  	</div>
	</div>
	<?=$this->_includeTemplate( 'shared/counters_logo.tpl', true );?>
  </body>
</html>