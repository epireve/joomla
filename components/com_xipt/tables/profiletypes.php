<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Disallow direct access to this file
if(!defined('_JEXEC')) die('Restricted access');

/**
 * Jom Social Table Model
 */
class XiptTableProfiletypes extends XiptTable
{
	function __construct()
	{
		parent::__construct('#__xipt_profiletypes','id');
	}

	function load($id)
	{
		if( $id == 0 )
		{
			$this->id			= 0;
			$this->name			= '';
			$this->tip			= '';
			$this->ordering		= true;
			$this->published	= true;
			$this->ordering		= 0;
			$this->privacy 		= '';
			$this->template		= "default";
			$this->jusertype	= "Registered";
			$this->allowt		= false;
			$this->avatar		= DEFAULT_AVATAR;
			$this->watermark	= "";
			$this->approve		= false;
			$this->group 		= 0;
			$this->params 		= '';
			$this->watermarkparams 		= '';
			$this->visible		= 1;
			$this->config 		= '';
			return true;
		}
		else
		{
			return parent::load( $id );
		}
	}
}