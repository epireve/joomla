<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JElementEventcategory extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'event_category';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$reqnone = false;
		$reqall  = false;
		if(isset($node->_attributes->addnone) || isset($node->_attributes['addnone']))
			$reqnone = true;
			
		if(isset($node->_attributes->addall) || isset($node->_attributes['addall']))
			$reqall = true;
			
		$ptypeHtml = $this->getEventcategoryHTML($name,$value,$control_name,$reqnone,$reqall);

		return $ptypeHtml;
	}
	
	function getEventcategoryHTML($name,$value,$control_name='params',$reqnone=false,$reqall=false)
	{	
		$required			='1';
		$html				= '';
		$class				= ($required == 1) ? ' required' : '';
		$options			= $this->getEventcategory();
		
		$html	.= '<select id="'.$control_name.'['.$name.']" name="'.$control_name.'['.$name.']" title="' . "Select Group Category" . '">';
		
		if($reqall) {
			$selected	= ( JString::trim(0) == $value ) ? ' selected="true"' : '';
			$html	.= '<option value="' . 0 . '"' . $selected . '>' . XiptText::_("ALL") . '</option>';
		}
		
		if($reqnone) {
			$selected	= ( JString::trim(-1) == $value ) ? ' selected="true"' : '';
			$html	.= '<option value="' . -1 . '"' . $selected . '>' . XiptText::_("NONE") . '</option>';
		}
		
		foreach($options as $op)
		{
		    $option		= $op->name;
			$id			= $op->id;
		    
		    $selected	= ( JString::trim($id) == $value ) ? ' selected="true"' : '';
			$html	.= '<option value="' . $id . '"' . $selected . '>' . $option . '</option>';
		}
		
		$html	.= '</select>';	
		$html   .= '<span id="errprofiletypemsg" style="display: none;">&nbsp;</span>';
		
		return $html;
	}
	
	function getEventcategory()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT id, name FROM #__community_events_category';

		$db->setQuery( $query );
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		$result = $db->loadObjectList();
		return $result;
	}
}