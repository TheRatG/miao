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
		$this->_init();

		$this->_processData();
		$data = $this->_setTemplateVariables();
		if ( is_array( $data ) )
		{
			$this->_templateEngine->setValueOfByArray( $data );
		}
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
			$result .= $this->_templateEngine->fetch( $templateName );
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