<?php
/**
* @copyright	Copyright (C) 2009 - 2009 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @contact		shyam@joomlaxi.com
*/

defined('_JEXEC') or die();
jimport('joomla.html.pagination');

class XiptPagination extends JPagination
{
	function __construct(XiptModel &$model)
	{
		$limit = null;
		$limitstart = null;
		$this->initDefaultStates($model, $limit,$limitstart);
        return parent::__construct($model->getTotal(), $limitstart,$limit);
	}


	public function initDefaultStates(&$model, &$limit, &$limitstart)
	{
		$statePrefix		= 'com_xipt';

		$app				= JFactory::getApplication();
		$globalListLimit	= $app->getCfg('list_limit');

		// Get pagination request variables

		//limit should be used from global space
        $limit 		= $app->getUserStateFromRequest('global.list.limit', 'limit', $globalListLimit, 'int');

        //other states should be picked from model namespace
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        //set states in model
        $model->setState('limit', $limit);
        $model->setState('limitstart', $limitstart);
	}

}
