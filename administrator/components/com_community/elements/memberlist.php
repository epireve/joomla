<?php
/**
 * @category	Elements
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
JTable::addIncludePath( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'tables' );
class JElementMemberlist extends JElement
{
	var	$_name = 'MemberList';

	public function fetchElement($name, $value, &$node, $control_name)
	{
		$mainframe 	= JFactory::getApplication();

		$db			=& JFactory::getDBO();
		$doc 		=& JFactory::getDocument();
		$fieldName	= $control_name.'['.$name.']';
		$memberlist	=& JTable::getInstance('MemberList' , 'CTable' );

		if ($value)
		{
			$memberlist->load($value);
		}
		else
		{
			$memberlist->title = JText::_('COM_COMMUNITY_USERS_SELECT_A_MEMBERLIST');
		}

		$js = "
		function jSelectMemberList(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_community&amp;view=memberlist&task=element&amp;tmpl=component&amp;object='.$name;

		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($memberlist->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('COM_COMMUNITY_USERS_SELECT_A_MEMBERLIST').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('Select').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}
}
