<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');
jimport( 'joomla.application.component.controller' );

abstract class XiptController extends JController
{
	public function getPrefix()
	{
		if(isset($this->_prefix) && empty($this->_prefix)===false)
			return $this->_prefix;

		$r = null;
		if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
			XiError::raiseError (__CLASS__.'.'.__LINE__, "XiptController::getName() : Can't get or parse class name.");
		}

		$this->_prefix  =  JString::strtolower($r[1]);
		return $this->_prefix;
	}

	function getName()
	{
		if (!isset($this->_name))
		{
			$r = null;
			if (!preg_match('/Controller(.*)/i', get_class($this), $r)) {
				XiptError::raiseError (__CLASS__.'.'.__LINE__, "XiusController : Can't get or parse class name.");
			}
			$this->_name = strtolower( $r[1] );
		}

		return $this->_name;
	}

	public function getView()
	{
		if(isset($this->_view))
			return $this->_view;

		//get Instance from Factory
		$this->_view	= 	XiptFactory::getInstance($this->getName(),'View', $this->getPrefix());
		$layout	= JRequest::getCmd( 'layout' , 'default' );
		$this->_view->setLayout( $layout );
		return $this->_view;
	}

	/**
	 * Get an object of controller-corresponding Model.
	 * @return XiptModel
	 */
	public function getModel($modelName=null)
	{
		// support for parameter
		if($modelName === null)
			$modelName = $this->getName();

		return XiptFactory::getInstance($modelName,'Model');
	}
	
	/**	
	 * Save the ordering of the entire records.
	 *	 	
	 * @access public
	 *
	 **/	 
	function saveOrder($ids=array(),$task='')
	{		
		// Get the ID in the correct location
 		$ids	= JRequest::getVar( 'cid', $ids, 'post', 'array' );
 		
 		XiptError::assert(!empty($ids), XiptText::_("$ids IS_NOT_EMPTY"), XiptError::ERROR);
		$id	= (int) array_shift($ids);
		
		// Determine whether to order it up or down
		$direction	= ( JRequest::getWord( 'task' , $task ) == 'orderup' ) ? -1 : 1;
			
		$this->getModel()->order($id, $direction);
		$this->setRedirect(XiptRoute::_('index.php?option=com_xipt&view='.$this->getName(), false));
	}
	
	function execute( $task )
	{
		$this->_task	= $task;

		$pattern = '/^switchOff/';
		if(preg_match($pattern, $task))
			$this->registerTask( $task, 	'multidobool');

		$pattern = '/^switchOn/';
		if(preg_match($pattern, $task))
			$this->registerTask( $task, 	'multidobool');

		//let the task execute in controller
		//if task have failed, simply return and do not go to view
		if(parent::execute($task)===false)
			return false;		
		
		if(JFactory::getApplication()->isAdmin() == true)		
			include_once(XIPT_ADMIN_PATH_VIEWS.DS.'cpanel'.DS.'tmpl'.DS.'default_footermenu.php');
	}
	
	function multidobool($task='enable',$cids=array(0))
	{
		$task	= JRequest::getVar('task',	$task);

		$offpattern = '/^switchOff/';
		$onpattern = '/^switchOn/';

		if(preg_match($onpattern, $task)){
			$switch		= false;
			//$columninfo = str_split($task,strlen('switchOn'));
			$columninfo = explode('switchOn',$task);
			$column		= array_key_exists(1,$columninfo) ? $columninfo[1] : '';
			$value		= 1;
		}
		else if(preg_match($offpattern, $task)){
			$switch		= false;				
			//$columninfo = str_split($task,strlen('switchOff'));
			$columninfo = explode('switchOff',$task);
			$column		= array_key_exists(1,$columninfo) ? $columninfo[1] : '';
			$value		= 0;
		}
		else
			XiptError::assert(0);

		$cids 	= JRequest::getVar('cid', $cids, 'post', 'array');

		foreach ($cids as $cid)
		{
			if(!$this->_doBool($column, $value, $cid))
				XiptError::raiseError(__CLASS__.'.'.__LINE__,XiptText::_("ERROR_IN_REORDERING_ITEMS"));
		}

		//redirect now
		$this->setRedirect(XiptRoute::_("index.php?option=com_".JString::strtolower($this->getPrefix())."&view={$this->getName()}"),sprintf(XiptText::_($task.'.MSG'),$cids));
		return false;
	}
	
	/**
	 * This function will modify the table boolean data
	 * @param $task = the related task : published
	 * @param $change = the value to change to, 1/0
	 * @param $switch = do we need to switch the value if field, default is false
	 * @param $itemId = The item to modify, if null, will be calculated from session
	 * @return bool
	 */
	public function _doBool($column, $change, $itemId=null)
	{
		//get the model
		$model 		= $this->getModel();

		//try to switch
		if($model->boolean($itemId, $column, $change))
			return true;

		//we need to set error message
		$this->setError($model->getError());
		return false;
	}
}

