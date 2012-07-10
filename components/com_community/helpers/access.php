<?php
/**
 * @package		JomSocial
 * @subpackage	Library
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

Class CAccess {

	protected static $error = null;

    /**
	 * Method to check if a user is authorised to perform an action, optionally on an asset.
	 *
	 * @param	integer	$userId	Id of the user for which to check authorisation.
	 * @param	string	$action	The name of the action to authorise.
	 * @param	mixed	$asset	Name of the asset as a string.
	 *
	 * @return	boolean	True if authorised.
	 * @since	Jomsocial 2.4
	 */
	static public function check($userId, $action, $asset, $assetObject)
	{
		$action = strtolower(preg_replace('#[\s\-]+#', '.', trim($action)));
		$asset  = strtolower(preg_replace('#[\s\-]+#', '.', trim($asset)));

        // Build action.
        $action     = explode('.', $action);
        array_shift($action);
        $actionName = isset($action[0]) ? ucfirst($action[0]) : '';

        // Build asset.
        $asset   = explode('.', $asset);
		$class   = $asset[0];
		
        $assetId = count($asset) > 1 && is_numeric($asset[count($asset)-1]) ? array_pop($asset) : -1;

        $assetName = '';
        foreach ($asset as $key) {
            $assetName .= ucfirst($key);
        }
        
        $assetName{0} = strtolower($assetName{0});
        
        // Append asset with action.
        $assetName .= $actionName;

		// Argument pass to the function.
		$arg = array($assetName, $userId);
		// Add asset id to arguement.
		if ($assetId >= 0) {
			array_push($arg, $assetId);
		}
		// Add asset object to arguement.
		if ($assetObject != null) {
			array_push($arg, $assetObject);
		}
		
		// Include particular access class
		$path = JPATH_ROOT.DS.'components'.DS.'com_community'.DS. 'helpers' .DS. 'access' .DS. $class .'.php';

		if( JFile::exists( $path ) )
		{
			include_once( $path );
		}

		$className = 'C' . ucfirst($class) . 'Access';
		
		// Call library access class, $obj::authorise
		if( class_exists($className) ) {
			return call_user_func_array(array($className, 'authorise'), $arg);
		} else {
			return null;
		}

    }

	/**
	 * Method to return authentication error msg
	 *
	 * @return	string error message
	 * @since	Jomsocial 2.4
	 */
	static public function getError()
	{
		return self::$error;
	}

	/**
	 * Method to set authentication error msg
	 * @param	string error message
	 * @since	Jomsocial 2.4
	 */
	static public function setError($error)
	{
		self::$error = $error;
	}
}
?>
