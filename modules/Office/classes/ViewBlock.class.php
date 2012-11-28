<?php
/**
 *
 * <ol>
 * <li>Set base directory</li>
 * <li>Set template or list templates</li>
 * <li>Set data for templates</li>
 * <li>Fetch</li>
 * </ol>
 *
 * @author vpak
 *
 */
abstract class Miao_Office_ViewBlock
{
	/**
	 *
	 * @var Miao_TemplatesEngine_PhpNative
	 */
	protected $_templateEngine = null;

	protected $_templates = array( 'index.tpl' );

	protected $_templatesDir = '';

	/**
	 *
	 * @var Miao_Office
	 */
	protected $_office;

	/**
	 *
	 * @var string Block name in tmpl
	 */
	protected $_name;

	/**
	 * конструктор
	 *
	 * @param string $name
	 * @param array $templates
	 * @param intger $lifetime
	 * @param array $process_params
	 */
	public function __construct( $name, array $templates, array $block_class_process_params = array() )
	{
		$this->setTemplates( $templates );
		$this->setProcessParams( $block_class_process_params );
		$this->setName( $name );
	}

	/**
	 *
	 * @param Miao_Office $office
	 */
	public function setOffice( Miao_Office $office )
	{
		$this->_office = $office;
	}

	/**
	 *
	 * @return Miao_Office
	 */
	public function getOffice()
	{
		return $this->_office;
	}

	/**
	 * параметры фукнции process()
	 *
	 * @var array
	 */
	private $_processParams = array();

	/**
	 * @return the $_templatesDir
	 */
	public function getTemplatesDir()
	{
		$result = $this->_templatesDir;
		if ( empty( $result ) )
		{
			//TODO; make with app
			$path = Miao_Path::getDefaultInstance();
			$result = $path->getTemplateDir( get_class( $this ) );
		}
		return $result;
	}

	/**
	 * @param field_type $_templatesDir
	 */
	public function setTemplatesDir( $templatesDir )
	{
		$this->_templatesDir = $templatesDir;
	}

	public function setTemplates( array $templates )
	{
		$this->_templates = $templates;
	}

	/**
	 * @return the $_templates
	 */
	public function getTemplates()
	{
		return $this->_templates;
	}

	/**
	 * @param array $processParams
	 */
	public function setProcessParams( array $processParams )
	{
		$this->_processParams = $processParams;
	}

	/**
	 * Получить массив параметров фукнции process
	 *
	 * @return array
	 */
	public function getProcessParams()
	{
		return $this->_processParams;
	}

	/**
	 * @return the $name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name )
	{
		$this->_name = $name;
	}

	protected function _init()
	{
		$templatesDir = $this->getTemplatesDir();
		if ( is_null( $this->_templateEngine ) )
		{
			$this->_templateEngine = new Miao_TemplatesEngine_PhpNative( $templatesDir );
		}
		else
		{
			$this->_templateEngine->setTemplatesDir( $templatesDir );
			$this->_templateEngine->resetTemplateVariables();
		}
	}

	/**
	 * Вызывается перед парсингом шаблонов блока
	 */
	public function process()
	{
		/// Pointcut: process_p1

		$this->_init();
		$this->_processData();

		/// Pointcut: process_p2

		$data = $this->_setTemplateVariables();
		if ( is_array( $data ) )
		{
			$this->_templateEngine->setValueOfByArray( $data );
		}

		/// Pointcut: process_p3
	}

	/**
	 *
	 * Enter description here ...
	 * @param string $templates Default = index.tpl
	 */
	public function fetch( $templates = '' )
	{
		if ( !empty( $templates ) && is_string( $templates ) )
		{
			$templates = array( $templates );
		}
		if ( empty( $templates ) )
		{
			$templates = $this->_templates;
		}

		$result = '';
		foreach ( $templates as $templateName )
		{
			/// Pointcut: fetch_begin

			$result .= $this->_templateEngine->fetch( $templateName );

			/// Pointcut: fetch_end
		}
		return $result;
	}

	/**
	 * Установка переменных шаблона
	 *
	 * @param string $name Имя переменной
	 * @param mixed $value Значение переменной
	 */
	protected function _setTmplVar( $name, $value )
	{
		$this->_templateEngine->setValueOf( $name, $value );
	}

	/**
	 * Подготовка данных для шаблона
	 *
	 */
	abstract protected function _processData();

	/**
	 * Установка переменных шаблона
	 *
	 */
	abstract protected function _setTemplateVariables();
}