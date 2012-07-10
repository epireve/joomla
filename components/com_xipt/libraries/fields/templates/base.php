<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptFieldsTemplatesBase
{	
	var $_mainframe;	
	var $_task;
	var $_view;
	var $_params;
	
	function __construct()
	{
		$this->_mainframe = JFactory::getApplication();		
		$this->_task = JRequest::getVar('task',0);
		$this->_view = JRequest::getVar('view',0);
		$this->_params = XiptFactory::getSettings('', 0);		
	}
	
	static public function getInstance()
	{				
		$suffix    = JString::stristr(XiptHelperJomsocial::get_js_version(),2.0) ? "Js20" : "Js18"; 
		$classname = "XiptFieldsTemplates".JString::ucfirst($suffix);
				
		if(class_exists($classname, true)===false)
		{
			XiptError::raiseError(__CLASS__.'.'.__LINE__,XiptText::_("$className : CLASS_NOT_FOUND"));
			return false;
		}
			
		$instance     = new $classname();
		return $instance;
	}
	
	function getFieldHTML($field, $required )
	{
		// it might be some other user (in case of admin is editing profile)
		$user    =& JFactory::getUser();
		
		$tName	= $field->value;
		$templates = XiptHelperJomsocial::getTemplatesList();
		$class	= ($required == 1) ? ' required' : '';
		
		$selectedValue = $this->getTemplateValue($tName,$user->id);
		//	XITODO : format it in proper way
		$allowToChangeTemplate = XiptHelperProfiletypes::getProfileTypeData(XiptLibProfiletypes::getUserData($user->id),'allowt');
		$allowToChangeTemplate = $allowToChangeTemplate || XiptHelperUtils::isAdmin($user->id);
		
		if(!$allowToChangeTemplate) {
			$html = '<input type="hidden" id="field'.$field->id.'"
				name="field' . $field->id  . '" value="'.$selectedValue.'" />';
			$html .= $selectedValue;
			return $html;
		}
		
		
		$html	= '<select id="field'.$field->id.'" name="field' . $field->id . '" class="hasTip select'.$class.' inputbox" title="' . $field->name . '::' . htmlentities( $field->tips ). '">';
		$selectedElement	= 0;
		if(!empty($templates)){
			foreach($templates as $tmpl){
				$selected	= ( $tmpl == $selectedValue ) ? ' selected="selected"' : '';
				
				if( !empty( $selected ) )
					$selectedElement++;
				
				$html	.= '<option value="' . $tmpl . '"' . $selected . '>' . $tmpl . '</option>';
			}
		}
		$html	.= '</select>';
		$html   .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';
		
		return $html;
	}
	
	function getTemplateValue($value,$userid)
	{
		// during registration
        if($this->_view =='register'){
            $pID = XiptFactory::getPluginHandler()->getRegistrationPType();
		    $tName = XiptLibProfiletypes::getProfileTypeData($pID,'template');
		    return $tName;
        }
		
        if($value)
            $tName=$value;
        else
        {
	        //a valid or default value
	        $tName = XiptLibProfiletypes::getUserData($userid,'TEMPLATE');
        }
        return $tName;
	}
	
}
 
