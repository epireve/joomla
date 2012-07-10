<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');
jimport( 'joomla.application.component.view');
JHTML::_('behavior.tooltip', '.hasTip');
jimport('joomla.html.pane');

abstract class XiptView extends JView
{	
	function display($tpl = null)
	{
		$css  		= JURI::root() . 'components/com_xipt/assets/admin.css';
		$document   = JFactory::getDocument();
		$document->addStyleSheet($css);
		parent::display($tpl);
	}
	/*
	 * Collect prefix auto-magically
	 */
	public function getPrefix()
	{
		if(isset($this->_prefix) && empty($this->_prefix)===false)
			return $this->_prefix;

		$r = null;
		if (!preg_match('/(.*)View/i', get_class($this), $r)) {
			XiptError::raiseError (__CLASS__.'.'.__LINE__, "XiView::getPrefix() : Can't get or parse class name.");
		}

		$this->_prefix  =  JString::strtolower($r[1]);
		return $this->_prefix;
	}
	
	
	/*
	 * We need to override joomla behaviour as they differ in
	 * Model and Controller Naming	 
	 */
	function getName()
	{
		$name = $this->_name;

		if (empty( $name ))
		{
			$r = null;
			if (!preg_match('/View(.*)/i', get_class($this), $r)) {
				XiptError::raiseError (__CLASS__.'.'.__LINE__, "Can't get or parse class name.");
			}
			$name = strtolower( $r[1] );
		}

		return $name;
	}
	
	/**
	 * Get an object of controller-corresponding Model.
	 * @return XiptModel
	 */
	public function getModel($modelName=null)
	{
		// support for parameter
		if($modelName===null)
			$modelName = $this->getName();

		return XiptFactory::getInstance($modelName,'Model');
	}
}