<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptViewApplications extends XiptView 
{
	function display($tpl = null)
	{
		$aModel	= $this->getModel();
		
		$pagination	= $aModel->getPagination();
		$fields		= $aModel->getPlugin(null, XIPT_JOOMLA_EXT_ID, $pagination->limit, $pagination->limitstart);
		
		$this->setToolbar();
		
		$this->assignRef( 'fields' 		, $fields );
		$this->assignRef( 'pagination'	, $pagination );
		parent::display( $tpl );
    }
	
	function edit($id,$tpl = 'edit')
	{
		$fields	= $this->getModel()->getPlugin();
		
		$this->assignRef( 'fields' 		, $fields );
		$this->assign( 'applicationId' , $id );
		$this->setToolbar();
		return parent::display($tpl);
	}
	
	// set the toolbar according to task	 	 
	function setToolbar($task='display')
	{	
		$task = JRequest::getVar('task',$task);
		if($task === 'display' || $task === 'cancel'){		
			JToolBarHelper::title( XiptText::_( 'APPLICATIONS' ), 'applications' );
			JToolBarHelper::back('Home' , 'index.php?option=com_xipt');
			return true;
		}
		
		if($task === 'edit'){
			JToolBarHelper::title( XiptText::_( 'EDIT_APPLICATIONS' ), 'applications' );
			JToolBarHelper::back('Home' , 'index.php?option=com_xipt&view=applications');
			JToolBarHelper::divider();
			JToolBarHelper::save('save','COM_XIPT_SAVE');
			JToolBarHelper::cancel( 'cancel', 'COM_XIPT_CLOSE' );
			return true;
		}		
	}
}
