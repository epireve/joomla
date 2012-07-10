<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
/**
 * Configuration view for Jom Social
 */
class CommunityViewConfiguration extends JView
{
	/**
	 * The default method that will display the output of this view which is called by
	 * Joomla
	 * 
	 * @param	string template	Template file name
	 **/	 	
	public function display( $tpl = null )
	{
		//Load pane behavior
		jimport('joomla.html.pane');
		$pane   	=& JPane::getInstance('sliders');
		$document	=& JFactory::getDocument();
		
		// Load tooltips
		JHTML::_('behavior.tooltip', '.hasTip');
		
		$params	= $this->get( 'Params' );
		//user's email privacy setting
		CFactory::load( 'libraries' , 'emailtypes' );
		$emailtypes = new CEmailTypes();
		
		// Add submenu
		$contents = '';
		ob_start();
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_community' . DS . 'views' . DS . 'configuration' . DS . 'tmpl' . DS . 'navigation.php' );

		$contents = ob_get_contents();
		ob_end_clean();

		$document	=& JFactory::getDocument();
		
		$document->setBuffer($contents, 'modules', 'submenu');

		$lists = array();

		for ($i=1; $i<=31; $i++) {
			$qscale[]	= JHTML::_('select.option', $i, $i);
		}
		
		$lists['qscale'] = JHTML::_('select.genericlist',  $qscale, 'qscale', 'class="inputbox" size="1"', 'value', 'text', $params->get('qscale', '11'));

		$videosSize = array
		(
			JHTML::_('select.option', '320x240', '320x240 (QVGA 4:3)'),
			JHTML::_('select.option', '400x240', '400x240 (WQVGA 5:3)'),
			JHTML::_('select.option', '400x300', '400x300 (Quarter SVGA 4:3)'),
			JHTML::_('select.option', '480x272', '480x272 (Sony PSP 30:17)'),
			JHTML::_('select.option', '480x320', '480x320 (iPhone 3:2)'),
			JHTML::_('select.option', '480x360', '480x360 (4:3)'),
			JHTML::_('select.option', '512x384', '512x384 (4:3)'),
			JHTML::_('select.option', '600x480', '600x480 (4:3)'),
			JHTML::_('select.option', '640x360', '640x360 (16:9)'),
			JHTML::_('select.option', '640x480', '640x480 (VCA 4:3)'),
			JHTML::_('select.option', '800x600', '800x600 (SVGA 4:3)'),
		);

		$lists['videosSize'] = JHTML::_('select.genericlist',  $videosSize, 'videosSize', 'class="inputbox" size="1"', 'value', 'text', $params->get('videosSize'));


		$imgQuality = array
		(
			JHTML::_('select.option', '60', 'Low'),
			JHTML::_('select.option', '80', 'Medium'),
			JHTML::_('select.option', '90', 'High'),
			JHTML::_('select.option', '95', 'Very High'),
		);

		$lists['imgQuality'] = JHTML::_('select.genericlist',  $imgQuality, 'output_image_quality', 'class="inputbox" size="1"', 'value', 'text', $params->get('output_image_quality'));

		// Group discussion order option
		$groupDiscussionOrder = array(
			JHTML::_('select.option', 'ASC', 'Older first'),
			JHTML::_('select.option', 'DESC', 'Newer first'),
		);
		$lists['groupDicussOrder'] = JHTML::_('select.genericlist',  $groupDiscussionOrder, 'group_discuss_order', 'class="inputbox" size="1"', 'value', 'text', $params->get('group_discuss_order'));


		$dstOffset	= array();
		$counter = -4;
		for($i=0; $i <= 8; $i++ ){
			$dstOffset[] = 	JHTML::_('select.option', $counter, $counter);
			$counter++;
		}
		
		$lists['dstOffset'] = JHTML::_('select.genericlist',  $dstOffset, 'daylightsavingoffset', 'class="inputbox" size="1"', 'value', 'text', $params->get('daylightsavingoffset'));
		

		$networkModel	= $this->getModel( 'network' , false );

		$JSNInfo 		=& $networkModel->getJSNInfo();
		$JSON_output	=& $networkModel->getJSON();

		$lists['enable'] = JHTML::_('select.booleanlist',  'network_enable', 'class="inputbox"', $JSNInfo['network_enable'] );
		
		$uploadLimit = ini_get('upload_max_filesize');
		$uploadLimit = CString::str_ireplace('M', ' MB', $uploadLimit);

		$this->assignRef( 'JSNInfo', $JSNInfo );
		$this->assignRef( 'JSON_output', $JSON_output );		
		$this->assignRef( 'lists', $lists );
		$this->assign( 'uploadLimit' , $uploadLimit );		
		$this->assign( 'config' , $params );
		
		$this->assign('emailtypes',$emailtypes->getEmailTypes());
		
		parent::display( $tpl );

	}

	public function getTemplatesList( $name , $default = '' )
	{
		$path	= dirname(JPATH_BASE) . DS . 'components' . DS . 'com_community' . DS . 'templates';
	
		if( $handle = @opendir($path) )
		{
			while( false !== ( $file = readdir( $handle ) ) )
			{
				// Do not get '.' or '..' or '.svn' since we only want folders.
				if( $file != '.' && $file != '..' && $file != '.svn' && JFolder::exists( $path . DS . $file) )
					$templates[]	= $file;
			}
		}
		
		$html	= '<select name="' . $name . '">';

		foreach( $templates as $template )
		{
			if( $template )
			if( !empty( $default ) )
			{
				$selected	= ( $default == $template ) ? ' selected="true"' : '';
			}
			$html	.= '<option value="' . $template . '"' . $selected . '>' . $template . '</option>';
		}
		$html	.= '</select>';

		return $html;
	}

	public function getKarmaHTML( $name , $value, $readonly=false, $updateTarget='')
	{
		$isReadOnly	= ($readonly) ? ' readonly="readonly"' : '';
		$requiredTargetUpdate = (! empty($updateTarget)) ? 'onblur="azcommunity.updateField(\''.$name.'\', \''.$updateTarget.'\')"' : '';
	
		$html	= '<table width="100%" cellspacing="0" cellpadding="0">';
		$html	.= '<tr>';
		$html	.= '	<td style="width: 120px">';
		if ($readonly) {
			$html .= '<span class="com_karmaNumber" id="' . $name . '">' . $value . '</span> ';
		} else {
			$html	.= '<input type="text" size="3" value="' . $value . '" name="' . $name . '" id="'.$name.'" '.$isReadOnly.' '.$requiredTargetUpdate.' /> ';
		}
		$html	.= JText::_('COM_COMMUNITY_CONFIGURATION_KARMA_USE_IMAGE');
		$html	.= '	</td>';
		$html	.= '	<td>';
		$html	.= '	&nbsp;&nbsp;&nbsp;<img class="com_karmaImage" src="' . $this->_getKarmaImage( $name ) . '" />';
		$html	.= '	</td>';
		$html	.= '</tr>';
		$html	.= '</table>';
		return $html;
	}

	public function getNotifyTypeHTML( $selected )
	{
		$types	= array();
		
		$types[]	= array( 'key' => '1' , 'value' => JText::_('COM_COMMUNITY_EMAIL') );
		$types[]	= array( 'key' => '2' , 'value' => JText::_('COM_COMMUNITY_PRIVATE_MESSAGE') );
		
		$html		= '<select name="notifyby">';
		
		foreach( $types as $type => $option )
		{
			$selectedData	= '';
			if( $option['key'] == $selected )
			{
				$selectedData	= ' selected="true"';
			}
			$html	.= '<option value="' . $option['key'] . '"' . $selectedData . '>' . $option['value'] . '</option>';
		}
		$html	.= '</select>';
		
		return $html;
	}
	
	public function getPrivacyHTML( $name , $selected , $showSelf = false )
	{
		$public		= ( $selected == 0 ) ? 'checked="true" ' : '';
		$members	= ( $selected == 20 ) ? 'checked="true" ' : '';
		$friends	= ( $selected == 30 ) ? 'checked="true" ' : '';
		$self		= ( $selected == 40 ) ? 'checked="true" ' : '';
		
		$html	= '<input id="radpub_'.$name.'" type="radio" value="0" name="' . $name . '" ' . $public . '/> <label for="radpub_'.$name.'" class="radiobtn">' . JText::_('COM_COMMUNITY_PUBLIC').'</label>';
		$html	.= '<input id="radmbr_'.$name.'" type="radio" value="20" name="' . $name . '" ' . $members . '/> <label for="radmbr_'.$name.'" class="radiobtn">' . JText::_( 'COM_COMMUNITY_MEMBERS').'</label>';
		$html	.= '<input id="radfrd_'.$name.'" type="radio" value="30" name="' . $name . '" ' . $friends . '/> <label for="radfrd_'.$name.'" class="radiobtn">' . JText::_('COM_COMMUNITY_FRIENDS').'</label>';
		
		/*
		$html	= '<input type="radio" value="0" name="' . $name . '" ' . $public . '/> ' . JText::_('COM_COMMUNITY_PUBLIC');
		$html	.= '<input type="radio" value="20" name="' . $name . '" ' . $members . '/> ' . JText::_( 'COM_COMMUNITY_MEMBERS');
		$html	.= '<input type="radio" value="30" name="' . $name . '" ' . $friends . '/> ' . JText::_('COM_COMMUNITY_FRIENDS');
		*/
		
		if( $showSelf )
		{
			//$html	.= '<input type="radio" value="40" name="' . $name . '" ' . $self . '/> ' . JText::_('COM_COMMUNITY_SELF');
			$html	.= '<input id="radself_'.$name.'" type="radio" value="40" name="' . $name . '" ' . $self . '/> <label for="radself_'.$name.'">' . JText::_('COM_COMMUNITY_SELF').'</label>';
		}
		return $html;
	}
	
	/**
	 * Method to return the image path for specific elements
	 * @access	private
	 *
	 * @return	string	$image	The path to the image.
	 */
	public function _getKarmaImage( $name )
	{
		$image	= '';
		
		switch( $name )
		{
			case 'point0':
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-0.5-5.png';
				break;
			case 'point1':
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-1-5.png';
				break;
			case 'point2':
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-2-5.png';
				break;
			case 'point3':
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-3-5.png';
				break;
			case 'point4':
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-4-5.png';
				break;
			case 'point5':
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-5-5.png';
				break;
			default:
				$image	= JURI::root() . 'components/com_community/templates/default/images/karma-0-5.png';
				break;			
		}
		return $image;
	}
	
	public function setToolBar()
	{
		// Get the toolbar object instance
		$bar =& JToolBar::getInstance('toolbar');

		// Set the titlebar text
		JToolBarHelper::title( JText::_( 'COM_COMMUNITY_CONFIGURATION' ), 'configuration');
		
		// Add the necessary buttons
		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME'), 'index.php?option=com_community');
		JToolBarHelper::divider();
		JToolBarHelper::save( '' );
		JToolBarHelper::cancel();
	}
	
	public function getEditors()
	{
		$db		=& JFactory::getDBO();
		
		// compile list of the editors
		$query = 'SELECT ' . $db->nameQuote('element') . ' AS ' . $db->nameQuote('value') . ', ' . $db->nameQuote('name') . ' AS ' . $db->nameQuote('text')
				. ' FROM ' . $db->nameQuote(PLUGIN_TABLE_NAME)
				. ' WHERE ' . $db->nameQuote('folder') . ' = ' . $db->Quote('editors') 
				. ' AND ' . $db->nameQuote(EXTENSION_ENABLE_COL_NAME) . ' = ' . $db->Quote(1)
				. ' ORDER BY ' . $db->nameQuote('ordering') . ', ' . $db->nameQuote('name');
		$db->setQuery( $query );
		$editors = $db->loadObjectList();
		
		// Add JomSocial's Editor
		$editor    =	new stdClass();
		$editor->value	=   'jomsocial';
		$editor->text	=   'plg_editor_jomsocial';

		array_push( $editors, $editor );
		
		return $editors;
	}
	
	public function getFieldCodes( $elementName , $selected = '' )
	{
		$db		=& JFactory::getDBO();
		$query	= 'SELECT DISTINCT ' . $db->nameQuote('fieldcode') . ' FROM ' . $db->nameQuote('#__community_fields');
		$db->setQuery( $query );
		$fieldcodes	= $db->loadObjectList();
		
		$html		= '<select name="'. $elementName . '">';
		
		foreach( $fieldcodes as $fieldcode )
		{
			if( !empty($fieldcode->fieldcode ) )
			{
				$selectedData	= '';
	
				if( $fieldcode->fieldcode == $selected )
				{
					$selectedData	= ' selected="true"';
				}
				$html	.= '<option value="' . $fieldcode->fieldcode . '"' . $selectedData . '>' . $fieldcode->fieldcode . '</option>';
			}
		}
		$html	.= '</select>';
		
		return $html;
	}
	
	public function getFolderPermissionsPhoto( $name , $selected )
	{		
		$all		= ( $selected == '0777' ) ? 'checked="true" ' : '';
		$default	= ( $selected == '0755' ) ? 'checked="true" ' : '';

		$html	 = '<input type="radio" value="0777" name="' . $name . '" ' . $all . '/> ' . JText::_('COM_COMMUNITY_CHMOD777');
		$html	.= '<input type="radio" value="0755" name="' . $name . '" ' . $default . '/> ' . JText::_('COM_COMMUNITY_SYSTEM_DEFAULT');

		return $html;
	}
	
	public function getFolderPermissionsVideo( $name , $selected )
	{		
		$all		= ( $selected == '0777' ) ? 'checked="true" ' : '';
		$default	= ( $selected == '0755' ) ? 'checked="true" ' : '';

		$html	 = '<input type="radio" value="0777" name="' . $name . '" ' . $all . '/> ' . JText::_('COM_COMMUNITY_CHMOD777');
		$html	.= '<input type="radio" value="0755" name="' . $name . '" ' . $default . '/> ' . JText::_('COM_COMMUNITY_SYSTEM_DEFAULT');

		return $html;
	}
}
