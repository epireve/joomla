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

/**
 * Configuration view for Jom Social
 */
class CommunityViewProfiles extends JView
{
	/**
	 * The default method that will display the output of this view which is called by
	 * Joomla
	 * 
	 * @param	string template	Template file name
	 **/	 	
	public function display( $tpl = null )
	{
		$profile	=& $this->getModel( 'Profiles' );
		
		$fields		=& $profile->getFields(true);
		$pagination	=& $profile->getPagination();
		
		// Load tooltips
		JHTML::_('behavior.tooltip', '.hasTip');

		$this->assignRef( 'fields' 		, $fields );
		$this->assignRef( 'pagination'	, $pagination );
		parent::display( $tpl );
	}

	/**
	 * Method to get the Field type in text
	 * 
	 * @param	string	Type of field
	 * 
	 * @return	string	Text representation of the field type.
	 **/	 
	public function getFieldText( $type )
	{
		$model	=& $this->getModel( 'Profiles' );
		$types	= $model->getProfileTypes();
		$value	= isset( $types[ $type ] ) ? $types[ $type ] : '';
		
		return $value;
	}
	
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/	 	
	public function getPublish( &$row , $type , $ajaxTask )
	{
	
		$images = array(	0	=>	'publish_x.png',
							1	=>	'tick.png',
							2	=>	'publish_y.png');
		$alts	= array(	0	=>	JText::_('COM_COMMUNITY_UNPUBLISH'),
							1	=>	JText::_('COM_COMMUNITY_PUBLISHED'),
							2	=>	JText::_('COM_COMMUNITY_ADMINONLY'));

		$key 	= $row->$type?$row->$type:0;
		$image	= $images[$key];
		
		$alt	= $alts[$key];
		
		$href = '<a class="jgrid" href="javascript:void(0);" onclick="azcommunity.togglePublish(\'' . $ajaxTask . '\',\'' . $row->id . '\',\'' . $type . '\');">';
		if(C_JOOMLA_15==0){
			$state = $row->$type ? 'publish' : 'unpublish';
			$href .= '<span class="state '.$state.'"><span class="text">'.$alt.'</span></span></a>';
		}
		else{
		    $href  .= '<span><img src="images/' . $image . '" border="0" alt="' . $alt . '" /></span></a>';
		}
		
		return $href;
	}
	
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/
	public function showPublish( &$row , $type)
	{
		$imgY	= 'tick.png';
		$imgX	= 'publish_x.png';

		$image	= $row->$type ? $imgY : $imgX;
		
		$state = $row->$type ? 'publish' : 'unpublish';
		$alt	= $row->$type ? JText::_('COM_COMMUNITY_PUBLISHED') : JText::_('COM_COMMUNITY_UNPUBLISH');

		$href  = (C_JOOMLA_15==0) 
			? '<a class="jgrid"><span class="state '.$state.'"><span class="text">'.$alt.'</span></a>' 
			: '<span><img src="images/' . $image . '" border="0" alt="' . $alt . '" /></span>' ;

		return $href;
	}
	
	/**
	 * Private method to set the toolbar for this view
	 * 
	 * @access private
	 * 
	 * @return null
	 **/	 	 
	public function setToolBar()
	{

		// Set the titlebar text
		JToolBarHelper::title( JText::_('COM_COMMUNITY_CUSTOM_PROFILES'), 'profiles' );

		// Add the necessary buttons
		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME'), 'index.php?option=com_community');
		JToolBarHelper::divider();
		JToolBarHelper::publishList('publish', JText::_('COM_COMMUNITY_PUBLISH'));
		JToolBarHelper::unpublishList('unpublish', JText::_('COM_COMMUNITY_UNPUBLISH'));
		JToolBarHelper::divider();
		JToolBarHelper::trash('removefield', JText::_('COM_COMMUNITY_DELETE'));
		JToolBarHelper::addNew('newgroup', JText::_('COM_COMMUNITY_PROFILES_NEW_GROUP'));
		JToolBarHelper::addNew('newfield', JText::_('COM_COMMUNITY_NEW_FIELD'));
	}
}
