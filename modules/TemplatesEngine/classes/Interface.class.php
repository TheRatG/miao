<?php
/**
 * UniPg
 * @package TemplatesEngine
 */

/**
 * Interface for Uniora template-engines.
 *
 * @author S.Vyazovetskov <svyazovetskov@rbc.ru>
 * @copyright RBC 2006
 * @package TemplatesEngine
 */
interface Miao_TemplatesEngine_Interface
{
	/**
	 * Установка папки с шаблонами
	 *
	 * @param string $templatesDir
	 */
	public function setTemplatesDir( $templatesDir );

	/**
	 * Установить перменную
	 *
	 * @param string $templateVarName
	 * @param mixed $templateVarValue
	 */
	public function setValueOf( $templateVarName, $templateVarValue = null );

	/**
	 * Установить перменную по ссылке
	 *
	 * @param string $templateVarName
	 * @param mixed $templateVarValue
	 */
	public function setValueOfByRef( $templateVarName, & $templateVarValue );

	/**
	 * Обработать шаблон
	 *
	 * @param mixed $resource_name
	 * @param mixed $cache_id
	 * @param mixed $compile_id
	 * @param boolean $display
	 */
	public function fetch( $resource_name, $cache_id = null, $compile_id = null, $display = false );

	/**
	 * Удалить все переменные шаблонов
	 *
	 */
	public function resetTemplateVariables();
}
