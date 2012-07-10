<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptViewProfileFields extends XiptView
{
    function display($tpl = null)
    {
		//define all categories
		$categories	= XiptHelperProfilefields::getProfileFieldCategories();
		$fields		= XiptLibJomsocial::getFieldObject();

		$this->setToolbar();

		$this->assign('fields', $fields);
		$this->assignRef('categories', $categories);
		return parent::display($tpl);
    }

	function edit($fieldId, $tpl = 'edit')
	{
		//XITODO : duplicate call, remove it
		$field		= XiptLibJomsocial::getFieldObject($fieldId);
		$fields		= XiptLibJomsocial::getFieldObject();
		$categories	= XiptHelperProfilefields::getProfileFieldCategories();
				
		$this->assign('fields', $fields);
		$this->assign('field', $field);
		$this->assignRef('categories', $categories);
		$this->assign('fieldid', $fieldId);
		// Set the titlebar text
		JToolBarHelper::title( XiptText::_( 'EDIT_FIELD' ), 'profilefields' );

		$pane	=& JPane::getInstance('sliders');
		$this->assignRef( 'pane'		, $pane );

		// Add the necessary buttons
		JToolBarHelper::back('Home' , 'index.php?option=com_xipt&view=profilefields');
		JToolBarHelper::divider();
		JToolBarHelper::save('save','COM_XIPT_SAVE');
		JToolBarHelper::cancel( 'cancel', 'COM_XIPT_CLOSE' );
		parent::display($tpl);
	}

	/**
	 * Private method to set the toolbar for this view
	 *
	 * @access private
	 *
	 * @return null
	 **/
	function setToolBar()
	{

		// Set the titlebar text
		JToolBarHelper::title( XiptText::_( 'PROFILE_FIELDS' ), 'profilefields' );

		// Add the necessary buttons
		JToolBarHelper::back('Home' , 'index.php?option=com_xipt');
	}

}
