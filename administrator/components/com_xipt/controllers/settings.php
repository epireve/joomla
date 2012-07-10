<?php
// no direct access
if(!defined('_JEXEC')) die('Restricted access');
 
class XiptControllerSettings extends XiptController 
{	
	function save($post=null)
	{
		if($post===null)
			$post	= JRequest::get('post',JREQUEST_ALLOWRAW);		
					
		$save = $this->getModel()->saveParams($post['settings'],'settings','params');	
		XiptError::assert($save ,XiptText::_('ERROR_IN_SAVING_SETTINGS'), XiptError::WARNING);
		
		$msg = XiptText::_('SETTINGS_SAVED');
		$this->setRedirect("index.php?option=com_xipt&view=settings",$msg);
		return true;		
	}
}
