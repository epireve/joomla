<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JElementProfilefields extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Profilefields';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$value      = unserialize($value);		
		$feildsHtml = $this->getFieldsHtml($name, $value, $control_name);

		return $feildsHtml;
	}
	
	function getJomsocialProfileFields($filter = '',$join='AND')
	{
		$query = new XiptQuery();
		$query->select('*');
		$query->from('#__community_fields');
		
		
		//setting up the search condition is there is any
		if(! empty($filter)){
			foreach($filter as $column => $value)
				$query->where(" `$column` = '$value' ", $join); 	
		}
	
		$query->order('ordering');	
		$fields =$query->dbLoadQuery("","")->loadObjectList();			 	    	
		
		return $fields;	
	}
	
	function getFieldsHtml($name, $value, $control_name)
	{
		$fields = self::getJomsocialProfileFields(array('published'=>1));
		$html   = '';
		if(empty($fields)) {
			$html = "<div style=\"text-align: center; padding: 5px; \">".XiptText::_('THERE_ARE_NO_PARAMETERS_FOR_THIS_ITEM')."</div>";
			return $html;
		}

		$html .= "<table width='100%' class='paramlist admintable' cellspacing='1'>";
		$html .= "<tr class='title'>";
		$html .= "<th width='30%'>".XiptText::_( 'FIELD_NAME' )."</th>";
		$html .= '<tr>';
				
		$i = 0;
		foreach($fields as $f) {			
			++$i;
			if($f->type != 'group') {
				$html .= "<td class='paramlist_value'>".$f->name."</td>";
				
				$profiletypeFieldHtml = $this->buildProfileTypes($name, $value, $control_name,$f->id);
				$html .= "<td class='paramlist_value'>".$profiletypeFieldHtml."</td>";
			}				
			$html .= "</tr>";
		}
		
		$html .= "</table>";
		return $html;
	}
	
	function buildProfileTypes($name, $value, $control_name, $fid)
	{	
		$allTypes		= XiptHelperProfiletypes::getProfileTypeArray(true,true);
		$html			= '';
		$html .= '<select id="'.$control_name.'['.$name.']['.$fid.'][]" name="'.$control_name.'['.$name.']['.$fid.'][]" value="" style="margin: 0 5px 5px 0;"  size="3" multiple/>';	
		foreach( $allTypes as $option )
		{
			$ptypeName       = XiptHelperProfiletypes::getProfileTypeName($option);
			$selected        = '';
		 	if (is_array($value) && array_key_exists($fid, $value) && in_array($option, $value[$fid]))
		  		$selected        ='SELECTED';
		  			
		 	$html .= '<option name="'.$name.'_'.$option.'" "'.$selected.'" value="'.$option.'">' ;  
			$html .= XiptHelperProfiletypes::getProfileTypeName($option).'</option>';
		}
		$html	.= '</select>';		
		
		return $html;
	}
}