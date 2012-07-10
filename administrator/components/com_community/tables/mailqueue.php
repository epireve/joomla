<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Jom Social Table Model
 */
class CommunityTableMailQueue extends JTable
{
	var $id				= null;
	var $recipient		= null;
	var $subject		= null;
	var $body			= null;
	var $status			= null;
	var $created		= null;
	
	public function __construct(&$db)
	{
		parent::__construct('#__community_mailq','id', $db);
	}
}
?>