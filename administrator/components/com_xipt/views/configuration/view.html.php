<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptViewConfiguration extends XiptView 
{

	public function getModel($modelName=null)
	{
		// support for parameter
		if($modelName===null || $modelName === $this->getName())
			return parent::getModel('profiletypes');

		return parent::getModel($modelName);
	}

    
	function display($tpl = null)
	{
    	$pModel	= $this->getModel();		
		
    	$pagination	= $pModel->getPagination();
    	$fields		= $pModel->loadRecords($pagination->limit, $pagination->limitstart);		

		$this->setToolBar();		
		
		$this->assign('reset',XiptHelperConfiguration::getResetLinkArray());
		$this->assignRef( 'fields' 		, $fields );
		$this->assignRef( 'pagination'	, $pagination );
		return parent::display( $tpl );
    }
	
	function edit($id, $tpl = 'edit' )
	{			
		// For JomSocial 2.x.x to Getting js Constant.
		require_once JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'defines.community.php';
					
		$params	= $this->getModel()->loadParams($id);

		$lists = array();
		for ($i=1; $i<=31; $i++)
			$qscale[]	= JHTML::_('select.option', $i, $i);
		
		$lists['qscale'] = JHTML::_('select.genericlist',  $qscale, 'qscale', 'class="inputbox" size="1"', 'value', 'text', $params->get('qscale', '11'));

		$videosSize = array
		(
			JHTML::_('select.option', '320x240', '320x240 (QVGA 4:3)'),
			JHTML::_('select.option', '400x240', '400x240 (WQVGA 5:3)'),
			JHTML::_('select.option', '400x300', '400x300 (Quarter SVGA 4:3)'),
			JHTML::_('select.option', '480x272', '480x272 (Sony PSP 30:17)'),
			JHTML::_('select.option', '480x320', '480x320 (iPhone 3:2)'),
			JHTML::_('select.option', '512x384', '512x384 (4:3)'),
			JHTML::_('select.option', '600x480', '600x480 (5:4)'),
			JHTML::_('select.option', '640x360', '640x360 (16:9)'),
			JHTML::_('select.option', '640x480', '640x480 (VCA 4:3)'),
			JHTML::_('select.option', '800x600', '800x600 (SVGA 4:3)'),
		);

		$lists['videosSize'] = JHTML::_('select.genericlist',  $videosSize, 'videosSize', 'class="inputbox" size="1"', 'value', 'text', $params->get('videosSize'));
		// FOR JomSocial 2.1
		//Add image quality in view file for JS Configuration page error
		$imgQuality = array
		(
			JHTML::_('select.option', '60', 'Low'),
			JHTML::_('select.option', '80', 'Medium'),
			JHTML::_('select.option', '90', 'High'),
			JHTML::_('select.option', '95', 'Very High'),
		);

		$lists['imgQuality'] = JHTML::_('select.genericlist',  $imgQuality, 'output_image_quality', 'class="inputbox" size="1"', 'value', 'text', $params->get('output_image_quality'));
		
		
		$uploadLimit = ini_get('upload_max_filesize');
		$uploadLimit = JString::str_ireplace('M', ' MB', $uploadLimit);
		
		// Group discussion order option
		$groupDiscussionOrder = array(
			JHTML::_('select.option', 'ASC', 'Older first'),
			JHTML::_('select.option', 'DESC', 'Newer first'),
		);
		$lists['groupDicussOrder'] = JHTML::_('select.genericlist',  $groupDiscussionOrder, 'group_discuss_order', 'class="inputbox" size="1"', 'value', 'text', $params->get('group_discuss_order'));
		
		$this->assign( 'lists', $lists );
		$this->assign( 'uploadLimit' , $uploadLimit );
		$this->assign( 'config'	, $params );
		$this->assign( 'id'	, $id );
		$this->assign( 'jsConfigPath'	, JPATH_ADMINISTRATOR .DS.'components'. DS. 'com_community'.DS.'views'.DS.'configuration'.DS.'tmpl' );
		
		$this->setToolBar();
		// Set the titlebar text		
		return parent::display($tpl);
	}
	
	// set the toolbar according to task	 	 
	function setToolBar($task='display')
	{	
		$task = JRequest::getVar('task',$task);
		if($task === 'display'){		
			JToolBarHelper::title( XiptText::_( 'JOM_SOCIAL_CONFIGURATION' ), 'configuration' );		
			JToolBarHelper::back('Home' , 'index.php?option=com_xipt');
			return true;
		}
		
		if($task === 'edit'){
			// XITODO : show name of profiltype for which configuration is being edited
			$name 	= JRequest :: getVar('name');	
			JToolBarHelper::title( sprintf(XiptText::_( 'EDIT_CONFIGURATION'),$name), 'configuration' );
			JToolBarHelper::back('Home' , 'index.php?option=com_xipt&view=configuration');
			JToolBarHelper::divider();
			JToolBarHelper::save('save','COM_XIPT_SAVE');
			JToolBarHelper::cancel( 'cancel', 'COM_XIPT_CLOSE' );
			return true;
		}	
	}
	
	public function getEditors()
	{
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_community'.DS.'views'.DS.'configuration'.DS.'view.html.php';
		$editors = CommunityViewConfiguration::getEditors();
		return $editors;
	}
}
