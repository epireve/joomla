<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');
 
class XiptControllerSetup extends XiptController 
{
	//Need to override, as we dont have model
	public function getModel($modelName=null)
	{
		// support for parameter
		if($modelName===null || $modelName === $this->getName())
			return false;

		return parent::getModel($modelName);
	}
	
	
	function __construct($config = array())
	{
		parent::__construct($config);
	}
	
    function display() 
	{
		parent::display();
    }
    
    function doApply()
    {
    	$name = JRequest::getVar('name', '' );
    	
    	//get object of class
		$setupObject = XiptFactory::getSetupRule($name);
		$msg = $setupObject->doApply();
		
		if($msg!= -1)
			$this->setRedirect(XiptRoute::_("index.php?option=com_xipt&view=setup&task=display",false), $msg);
    }
    
    function unhook()
    {
    	//get all files required for setup
		$setupNames = XiptSetupHelper::getOrderedRules();
		
		foreach($setupNames as $setup)
		{
			//get object of class
			$setupObject = XiptFactory::getSetupRule($setup['name']);
			$setupObject->doRevert();
		}
		
		$msg = XiptText::_('UNHOOKED_SUCCESSFULLY');
		$this->setRedirect(XiptRoute::_("index.php?option=com_xipt&view=setup&task=display",false),$msg);
		return true;
    }
}
