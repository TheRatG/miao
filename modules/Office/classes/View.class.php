<?php
abstract class Miao_Office_View
{
	protected $_defaultLayout = 'layouts/index.tpl';
	protected $_defaultViewBlockPrefix;
	protected $_layout;

	/**
	 *
	 * @var Miao_Office_TemplatesEngine_PhpNative
	 */
	protected $_templatesObj;

	/**
	 *
	 * @var Miao_Office
	 */
	protected $_office;

	/**
	 *
	 * @param Miao_Office $office
	 */
	public function setOffice( Miao_Office $office )
	{
		$this->_office = $office;
		$this->_init();
	}

	/**
	 *
	 * @return Miao_Office
	 */
	public function getOffice()
	{
		return $this->_office;
	}

	abstract protected function _initializeBlock();

	public function __construct( Miao_Office_TemplatesEngine_PhpNative $templatesObj )
	{
		$this->setTemplateObj( $templatesObj );
	}

	public function getTemplateObj()
	{
		return $this->_templatesObj;
	}

	/**
	 *
	 * @return the $_layout
	 */
	public function getLayout()
	{
		if ( empty( $this->_layout ) )
		{
			$result = $this->_defaultLayout;
		}
		else
		{
			$result = $this->_layout;
		}
		return $result;
	}

	/**
	 *
	 * @param field_type $_layout
	 */
	public function setLayout( $layout )
	{
		$this->_layout = $layout;
	}

	public function setTemplateObj( Miao_Office_TemplatesEngine_PhpNative $templatesObj )
	{
		$this->_templatesObj = $templatesObj;
	}

	public function fetch( $layout = '' )
	{
		$this->_initializeBlock();

		if ( empty( $layout ) )
		{
			$layout = $this->getLayout();
		}
		$viewTemplate = $this->_makeViewTemplate();
		$this->_templatesObj->setViewTemplate( $viewTemplate );

		$result = $this->_templatesObj->fetch( $layout );
		return $result;
	}

	/**
	 * Установка данных в шаблон
	 *
	 * @param string $name
	 *        	Имя переменной
	 * @param mixed $value
	 *        	Значение переменной
	 */
	final public function setTmplVars( $name, $value )
	{
		$this->_templatesObj->setValueOf( $name, $value );
	}

	protected function _init()
	{
	}

	protected function _makeViewTemplate()
	{
		$className = get_class( $this );
		$ar = explode( '_', $className );

		$prefix = array();
		$prefix[] = array_shift( $ar );
		$prefix[] = array_shift( $ar );
		$prefix[] = array_shift( $ar );

		$ar = array_map( 'strtolower', $ar );
		$result = implode( '_', $ar );
		$result .= '.tpl';

		$path = Miao_Path::getDefaultInstance();
		$templateDir = $path->getTemplateDir( implode( '_', $prefix ) );

		$result = $templateDir . DIRECTORY_SEPARATOR . $result;

		return $result;
	}

	/**
	 *
	 * @deprecated
	 */
	protected function _addBlock( $name, $className = '', $templates = array( 'index.tpl' ) )
	{
		$this->_initBlock( $name, $className, $templates );
	}

	/**
	 * Добавить описание создаваемого блока
	 *
	 * @param string $name
	 *        	Имя блока, при помощи него вызывается нужный блок
	 * @param
	 *        	string or array $class_name Имя класса блока. Если массив, то
	 *        	первый элемент - это имя класса, второй обязательно массив с
	 *        	параметрами, которые будут использованы как аргументы функции
	 *        	process() блока
	 * @param array $templates
	 *        	Шаблоны блока
	 */
	protected function _initBlock( $name, $className = '', $templates = array( 'index.tpl' ) )
	{
		$block_class_process_params = array();
		if ( empty( $className ) )
		{
			$className = $name;
		}

		if ( is_string( $className ) )
		{
			$block_class_name = $className;

			if ( !Miao_Autoload::getFilenameByClassName( $block_class_name ) )
			{
				$block_class_name = $this->_defaultViewBlockPrefix . '_ViewBlock_' . $className;
			}
		}
		else if ( is_array( $className ) )
		{
			$block_class_name = $className[ 0 ];
			$block_class_process_params = isset($className[ 1 ]) ? $className[ 1 ] : null;
			if ( !is_array( $block_class_process_params ) )
			{
				$block_class_process_params = array(
					$block_class_process_params );
			}
		}
		$viewBlock = new $block_class_name( $name, $templates, $block_class_process_params );
		$this->_templatesObj->addBlock( $name, $viewBlock );
	}
}