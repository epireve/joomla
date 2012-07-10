<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptFieldsProfiletypesBase
{	
	/* Value must be numeric */
	var $_mainframe;
	var $_task;
	var $_view;
	var $_params;
	
	function __construct()
	{
		$this->_mainframe = JFactory::getApplication();
		$this->_task = JRequest::getVar('task','');
		$this->_view = JRequest::getVar('view','');
		$this->_params = XiptFactory::getSettings('', 0);
	}
	
	static public function getInstance()
	{				
		$suffix    	  = JString::stristr(XiptHelperJomsocial::get_js_version(),2.0) ? "Js20" : "Js18"; 
		$classname = "XiptFieldsProfiletypes".JString::ucfirst($suffix);
		
		if(class_exists($classname, true)===false)
		{
			XiptError::raiseError(__CLASS__.'.'.__LINE__,XiptText::_("$className : CLASS_NOT_FOUND"));
			return false;
		}
			
		$instance     = new $classname();
		return $instance;
	}
	
	/*
	 * Generate input HTML for field
	 */
	function getFieldHTML($field ,$required )
	{
		$html	    = '';
		$pID	    = $field->value;
		$class	    = ($required == 1) ? ' required' : '';
		$disabled   = '';
		
		if($this->_view ==='register') {
    
		    // get pType from registration session OR defaultPType
		    $pID = XiptFactory::getPluginHandler()->getRegistrationPType();
				 
			$html = '<input type="hidden"
							id="field'.$field->id.'"
							name="field' . $field->id.'"
							value="'.$pID.'" />';
			
			$pName = XiptLibProfiletypes::getProfiletypeName($pID);
			$html .= $pName;
			
			return $html;
		}
		    
		// it might be some other user (in case of admin is editing profile)
		$user    = JFactory::getUser();
		$userid  = $user->id;
		
		if(!(int)$pID){
			    $pID = XiptLibProfiletypes::getUserData($userid,'PROFILETYPE');
				XiptError::assert($pID, XiptText::_("USERID $pID DOES_NOT_EXIST"), XiptError::ERROR);
			}
			
		$visiblePT = XiptLibProfiletypes::getProfiletypeArray(array('visible'=>1));
		
		$allowToChangePType = $this->_params->get('allow_user_to_change_ptype_after_reg',0);
		$allowToChangePType = ($allowToChangePType && array_key_exists($pID, $visiblePT)) || XiptHelperUtils::isAdmin($user->id);
		
		//if not allowed then show disabled view of ptype
		if($allowToChangePType == false){
			
			$pName = XiptLibProfiletypes::getProfileTypeName($pID);
			$pName =$pName;
			$html = '<input type="hidden"
							id="field'.$field->id.'"
							name="field' . $field->id.'"
							value="'.$pID.'" />';
			return $html.$pName;
		}
		
		$mainframe	= JFactory::getApplication();
		if($mainframe->isAdmin()==true || XiptHelperUtils::isAdmin($user->id))
			$filter	= array('published'=>1);
		else
			$filter	= array('published'=>1,'visible'=>1);
		// user can change profiletype, add information
		$pTypes = XiptLibProfiletypes::getProfiletypeArray($filter);
		
		$html	= '<select id="field'.$field->id.'" name="field' . $field->id  . '" '.$disabled.' class="hasTip select'.$class.' inputbox" title="' . $field->name . '::' . htmlentities( $field->tips ). '">';
		
		$selectedElement	= 0;
		if(!empty($pTypes)){
			foreach($pTypes as $pType){
			    
				$selected	= ( $pType->id == $pID ) ? ' selected="selected"' : '';
				if( !empty( $selected ) )
					$selectedElement++;
				
				$html	.= '<option value="' . $pType->id . '"' . $selected . '>' .$pType->name  . '</option>';
			}
		}
		
		$html	.= '</select>';
		$html   .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';
		
		return $html;
	}
	
	// Just an validation
	function isValid($value,$required)
	{
	    if(!$value)
			return false;
			
		if(!XiptLibProfiletypes::validateProfiletype($value))
			return false;
		    
		return true;
	}
	
	function formatData($value=0)
	{
	    $pID = $value;
		
		if(!$pID){
			//get value from profiletype field from xipt_users table
			$userid = JRequest::getVar('userid',0);
			$pID = XiptLibProfiletypes::getUserData($userid,'PROFILETYPE');
		}
		return $pID;
	}	
}
 
