<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptViewJSToolbar extends XiptView 
{
	function display($tpl = null)
	{
		$jsModel	= $this->getModel();
		
		$pagination	= $jsModel->getPagination();
		$fields		= $jsModel->getMenu(null, $pagination->limit, $pagination->limitstart);
		
		$this->setToolbar();
		
		$this->assignRef( 'fields' 		, $fields );
		$this->assignRef( 'pagination'	, $pagination );
		parent::display( $tpl );
    }
	
	function edit($id,$tpl = 'edit')
	{
		$fields	= $this->getModel()->getMenu();
		
		$this->assignRef( 'fields', $fields );
		$this->assign( 'menuId', $id );
		$this->setToolbar();
		return parent::display($tpl);
	}
	
	// set the toolbar according to task	 	 
	function setToolbar($task='display')
	{	
		$task = JRequest::getVar('task',$task);
		if($task === 'display' || $task === 'cancel'){		
			JToolBarHelper::title( XiptText::_( 'JS_TOOLBAR' ), 'jstoolbar' );
			JToolBarHelper::back('Home' , 'index.php?option=com_xipt');
			return true;
		}
		
		if($task === 'edit'){
			JToolBarHelper::title( XiptText::_( 'EDIT_JS_TOOLBAR' ), 'jstoolbar' );
			JToolBarHelper::back('Home' , 'index.php?option=com_xipt&view=jstoolbar');
			JToolBarHelper::divider();
			JToolBarHelper::save('save','COM_XIPT_SAVE');
			JToolBarHelper::cancel( 'cancel', 'COM_XIPT_CLOSE' );
			return true;
		}		
	}
}
