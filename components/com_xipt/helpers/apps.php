<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptHelperApps 
{
	function getProfileTypeNames($aid)
	{	
		XiptError::assert($aid, XiptText::_("APPLICATION_ID_CANNOT_BE_NULL"), XiptError::ERROR);

		$selected = XiptHelperApps::getProfileTypeArray($aid);
		
		//if selected is empty means field is invisible, then return none
		if(empty($selected))
			return XiptText::_("NONE");
		
		//if 0 exist in selected ptype means , field is available to all
		if(in_array(XIPT_PROFILETYPE_ALL, $selected))
			return XiptText::_("ALL");
			
		$retVal = array();		
		foreach($selected as $pid)	     		
			$retVal[] = XiptHelperProfiletypes::getProfileTypeName($pid);
		       
		return implode(',',$retVal);
	}   


	function getProfileTypeArray($aid)
	{	
		XiptError::assert($aid, XiptText::_("APPLICATION_ID_CANNOT_BE_NULL"), XiptError::ERROR);
		
		$results = XiptFactory::getInstance('applications','model')
								->getProfileTypes($aid);

		if(empty($results)) return array(XIPT_PROFILETYPE_ALL);
		
		$allTypes	= XiptHelperProfiletypes::getProfileTypeArray();
		// array_values is user to arrange the array from index 0, 
		//array_diff uses index starting from 1
		return array_values(array_diff($allTypes, $results));
	}

	function buildProfileTypesforApplication( $aid )
	{
		$selectedTypes 	= XiptHelperApps::getProfileTypeArray($aid);		
		$allTypes		= XiptHelperProfiletypes::getProfileTypeArray();
		
		$html	= '';
		
		$html	.= '<span>';
		foreach( $allTypes as $option )
		{
			// XITODO : improve following condition
		  	$selected	= in_array($option , $selectedTypes) || in_array(XIPT_PROFILETYPE_ALL, $selectedTypes)  ? ' checked="checked"' : '';
			$html .= '<lable><input type="checkbox" id="profileTypes'.$option. '" name="profileTypes[]" value="' . $option . '"' . $selected .'" style="margin: 0 5px 5px 0;" />';
			$html .= XiptHelperProfiletypes::getProfileTypeName($option).'</lable>';
			$html .= '<div class="clr"></div>';
		}
		$html	.= '</span>';		
		
		return $html;
	}	
	
//	function buildProfileTypes($apps)
//	{				
//		$allTypes		= XiptHelperProfiletypes::getProfileTypeArray(true);
//
//		foreach($apps as $app){
//			$selectedTypes 	= XiptHelperApps::getProfileTypeArray($app->id);
//			$html[$app->id] = '';
//			foreach( $allTypes as $option )
//			{
//				$allowed 		 = in_array($option , $selectedTypes ) ? true : false;
//			  	$image			 = $allowed ? 'tick' : 'publish_x';
//				$html[$app->id] .= '<td>';
//			  	$html[$app->id] .= '<img src="images/'.$image.'.png" width="16" height="16" border="0" alt="Published" />';
//			  	$html[$app->id] .= '</td>';					  
//			}		
//		}
//		return $html;
//	}	
}