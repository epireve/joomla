<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');


class XiptHelperProfilefields 
{
	// return row from row id of fields values table
	function getProfileTypeNames($fid,$for)
	{
		XiptError::assert($fid, XiptText::_("PROFILEFIELD_ID_CAN_NOT_BE_NULL"), XiptError::ERROR);

		$selected = XiptHelperProfilefields::getProfileTypeArray($fid,$for);
		
		//if selected is empty means field is invisible, then return none
		if(empty($selected))
			return XiptText::_("NONE");
		
		//if 0 exist in selected ptype means , field is available to all
		if(in_array(XIPT_PROFILETYPE_ALL, $selected))
			return XiptHelperProfiletypes::getProfileTypeName(0);
			
		$retVal = array();		
		foreach($selected as $pid)
			$retVal[] = XiptHelperProfiletypes::getProfileTypeName($pid);
			   
		return implode(',',$retVal);
	}

	function getProfileTypeArray($fid,$for)
	{
		XiptError::assert($fid, XiptText::_("PROFILEFIELD_ID_CAN_NOT_BE_NULL"), XiptError::ERROR);
			
		//Load all profiletypes for the field
		$results = XiptFactory::getInstance('profilefields','model')
									->getProfileTypes($fid, $for);
		
		if(empty($results)) return array(XIPT_PROFILETYPE_ALL);
			
		$allTypes	= XiptHelperProfiletypes::getProfileTypeArray();
		// array_values is user to arrange the array from index 0, 
		//array_diff uses index starting from 1
		return array_values(array_diff($allTypes, $results));
	}


	function buildProfileTypes( $fid ,$for)
		{
			$selectedTypes 	= XiptHelperProfilefields::getProfileTypeArray($fid,$for);		
			$allTypes		= XiptHelperProfiletypes::getProfileTypeArray();
			
			$html			= '';
			$categories		= XiptHelperProfilefields::getProfileFieldCategories();	
			$name			= $categories[$for]['controlName'];
			$html	   	   .= '<span>';
			
			foreach( $allTypes as $option )
			{
			    $selected	= in_array($option , $selectedTypes ) || (in_array(XIPT_PROFILETYPE_ALL, $selectedTypes)) ? ' checked="checked"' : '';
				$html .= '<lable><input type="checkbox" name= "'.$name.'[]" value="' . $option . '" '. $selected .' " style="margin: 0 5px 5px 0;" />';
				$html .= XiptHelperProfiletypes::getProfileTypeName($option).'</lable>';
				$html .= '<div class="clr"></div>';				
			}
			$html	.= '</span>';		
			
			return $html;
		}

	function getProfileFieldCategories()
	{
		$categories[PROFILE_FIELD_CATEGORY_ALLOWED] = array(
									'name'=> 'ALLOWED',
									'controlName' => 'allowedProfileTypes'
									);
									
		$categories[PROFILE_FIELD_CATEGORY_REQUIRED] = array(
									'name'=> 'REQUIRED',
									'controlName' => 'requiredProfileTypes'
									);
									
		$categories[PROFILE_FIELD_CATEGORY_VISIBLE] = array(
									'name'=> 'VISIBLE',
									'controlName' => 'visibleProfileTypes'
									);
									
		$categories[PROFILE_FIELD_CATEGORY_EDITABLE_AFTER_REG] = array(
									'name'=> 'EDITABLE_AFTER_REG',
									'controlName' => 'editableAfterRegProfileTypes'
									);
									
		$categories[PROFILE_FIELD_CATEGORY_EDITABLE_DURING_REG] = array(
									'name'=> 'EDITABLE_DURING_REG',
									'controlName' => 'editableDuringRegProfileTypes'
									);
									
		$categories[PROFILE_FIELD_CATEGORY_ADVANCE_SEARCHABLE] = array(
									'name'=> 'ADVANCE_SEARCHABLE',
									'controlName' => 'advanceSearchableProfileTypes'
									);
		
		return $categories;
	}

}
