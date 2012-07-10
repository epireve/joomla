<?php
/**
 * @package		JomSocial
 * @subpackage  Controller 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.controller' );

class CommunityTestController extends CommunityBaseController
{
	public function display(){
		echo 'hello';
		include_once(COMMUNITY_COM_PATH . DS .'test/browser.index.php');
	}
}
