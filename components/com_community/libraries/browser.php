<?php
/**
 * @category	Library
 * @package		JomSocial
 * @subpackage	Browser
 * @copyright   (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.environment.browser');

class CBrowser extends JBrowser
{
    public static function &getInstanceStatic($userAgent = null, $accept = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        $signature = serialize(array($userAgent, $accept));

		if (empty($instances[$signature])) {
            $instances[$signature] = new CBrowser($userAgent, $accept);
        }

        return $instances[$signature];
    }

	public function match($userAgent = null, $accept = null)
	{
		parent::match( $userAgent, $accept );

		// Add detection for mobile devices that
		// JBrowser did not cover
		if( strstr($this->_agent, 'Mobile') )
		{
			$this->_mobile = true;
		}
	}
	public function isMobile(){
		return $this->_mobile;
	}
}
?>