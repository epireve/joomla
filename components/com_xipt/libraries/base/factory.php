<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptFactory
{
    /* This classes required a object to be created first.*/
    function getPluginHandler()
    {
        static $instance =null;
        
        if($instance==null)
            $instance = new XiptLibPluginhandler();
        
        return $instance;
    }

	//XITODO : apply caching 
	function getSetupRule($name)
	{		
		$classname = 'XiptSetupRule'.JString::ucfirst($name);
		
		//if class doesn't exist, raise error
		if(class_exists($classname, true)===false)
		{
			XiptError::raiseError(__CLASS__.'.'.__LINE__,sprintf(XiptText::_("CLASS_NOT_FOUND"),$className));
			return false;
		}	
		
		//create new object
		$object = new $classname();
		return $object;
	}
	
	static function getInstance($name, $type, $prefix='Xipt', $refresh=false)
	{
		static $instance=array();

		//generate class name
		$className	= JString::ucfirst($prefix)
					. JString::ucfirst($type)
					. JString::ucfirst($name);

		// Clean the name
		$className	= preg_replace( '/[^A-Z0-9_]/i', '', $className );

		//if already there is an object
		if($refresh===false && isset($instance[$className]))
			return $instance[$className];

		//class_exists function checks if class exist,
		// and also try auto-load class if it can
		if(class_exists($className, true)===false)
		{
			XiptError::raiseError(__CLASS__.'.'.__LINE__,sprintf(XiptText::_("CLASS_NOT_FOUND"),$className));
			return false;
		}

		//create new object, class must be autoloaded
		$instance[$className]= new $className();

		return $instance[$className];
	}

	function buildRadio($status, $fieldname, $values)
	{
		$html	= '<span>';
		
		if($status || $status == '1'){
			$html	.= '<input type="radio" id="' . $fieldname 
					. '" name="' . $fieldname . '" value="1" checked="checked" />' 
					. $values[0];
			$html	.= '<input type="radio" id="' . $fieldname 
					. '" name="' . $fieldname . '" value="0" />' 
					. $values[1];
		} else {
			$html	.= '<input type="radio" id="' . $fieldname 
					. '" name="' . $fieldname . '" value="1" />' 
					. $values[0];
			$html	.= '<input type="radio" id="' . $fieldname 
					. '" name="' . $fieldname . '" value="0" checked="checked" />' 
					. $values[1];	
		}
		$html	.= '</span>';
		
		return $html;
	}
	
    //get global settings
	function getSettings($paramName='', $defaultValue=0)
	{
		$sModel  = XiptFactory::getInstance('settings', 'model');
		$params  = $sModel->getParams();

		if(!$params)
		    XiptError::raiseWarning('XIPT-SYSTEM-ERROR','JSPT PARAMS ARE NULL');
		
		if(empty($paramName))
			return $params;
			
		if($paramName == 'aec_integrate')
		{
			$aec_integrate = $params->get('aec_integrate','aec');
			if($aec_integrate == 'aec')
				return $params->get('subscription_integrate',$defaultValue);
			else
				return $aec_integrate;
		}
			
		if($paramName == 'aec_message')
		{
			$aec_message = $params->get('aec_message','aec');
			if($aec_message == 'aec')
				return $params->get('subscription_message',$defaultValue);
			else
				return $aec_message;
		}
		return $params->get($paramName,$defaultValue);
	}
}
