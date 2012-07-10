<?php
/**
 * @category	Config
 * @package		JomSocial
 * @copyright (C) 2010 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CConfigTrigger
{
	public function onAfterConfigCreate( $config )
	{
		// If html codes are not allowed at all, config should be intelligent by not
		// displaying a WYSIWYG editor.
		if( !$config->getBool('allowhtml') )
		{
			$config->set( 'htmleditor' , 'none');
		}
	}
}