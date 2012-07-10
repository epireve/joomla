<?php
/**
 * @category	Elements
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

class JElementUsers extends JElement
{
	var	$_name = 'Users';

	public function fetchElement($name, $value, &$node, $control_name)
	{
		$lang =& JFactory::getLanguage();
		$lang->load( 'com_community', JPATH_ROOT);
		
		$model		= CFactory::getModel( 'User' );
		$users		= $model->getUsers();
		$fieldName	= $control_name.'['.$name.']';
		$user		= CFactory::getUser( $value );
		$value		= empty( $value ) ? JText::_( 'COM_COMMUNITY_SELECT_A_USER' ) : $user->getDisplayName(); 

		$document	= JFactory::getDocument();
		$document->addScript( rtrim( JURI::root() , '/' ) . '/administrator/components/com_community/assets/admin.js' );

		$link = 'index.php?option=com_community&amp;view=users&amp;task=element&amp;tmpl=component&amp;element=' . $name;

		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="' . $value . '" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('COM_COMMUNITY_SELECT_A_USER').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 750, y: 450}}">'.JText::_('COM_COMMUNITY_SELECT').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}
}
