<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * Jom Social Component Controller
 */
class CommunityControllerAbout extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function ajaxCheckVersion()
	{
		$response		= new JAXResponse();

		$data			= $this->_getCurrentVersionData();
		ob_start();

		// Get the current build number
		$build			= $this->_getLocalBuildNumber();
		$version		= $this->_getLocalVersionNumber();

		// Test versions
		if( $version < $data->version || ( ($version <= $data->version) && ( $build < $data->build ) ) )
		{			
?>
		<div>
			<fieldset>
				<legend><?php echo JText::_('COM_COMMUNITY_UPDATE_SUMMARY');?>
				<div style="color: red"><?php echo JText::_('COM_COMMUNITY_OLDER_VERSION_OF_JOM_SOCIAL');?></div>
				<div style="margin: 3px;"><?php echo JText::sprintf('Version installed: <span style="font-weight:700; color: red">%1$s</strong>' , $this->_getLocalVersionString() );?></div>
				<div style="margin: 3px;"><?php echo JText::sprintf('Latest version available: <span style="font-weight:700;">%1$s</span>', $data->version . '.' . $data->build ); ?></div>
			</fieldset>
			<div style="margin: 10px 3px 3px 3px;">
				<?php echo JText::sprintf('View full changelog at <a href="%1$s" target="_blank">%2$s</a>', $data->changelogURL , $data->changelogURL ); ?>
			</div>
			<div style="margin: 3px;">
				<?php echo JText::sprintf('View the upgrade instructions at <a href="%1$s" target="_blank">%2$s</a>', $data->instructionURL , $data->instructionURL ); ?>
			</div>
		</div>
<?php
		}
		else
		{
?>
		<div>
			<fieldset>
				<legend><?php echo JText::_('COM_COMMUNITY_UPDATE_SUMMARY');?>
				<div style="margin: 3px"><?php echo JText::_('COM_COMMUNITY_LATEST_VERSION_OF_JOM_SOCIAL'); ?></div>
				<div style="margin: 3px;"><?php echo JText::sprintf('Version installed: <span style="font-weight:700;">%1$s</strong>' , $this->_getLocalVersionString() );?></div>
			</fieldset>
		</div>
<?php
			$response->addScriptCall('cWindowResize', 160);
		}
		$contents	= ob_get_contents();
		ob_end_clean();

		$response->addAssign( 'cWindowContent' , 'innerHTML' , $contents );
		
		$action = '<input type="button" class="button" onclick="cWindowHide();" name="' . JText::_('COM_COMMUNITY_CLOSE') . '" value="' . JText::_('COM_COMMUNITY_CLOSE') . '" />';
		$response->addScriptCall('cWindowActions', $action);
		return $response->sendResponse();
	}
	
	public function _getLocalBuildNumber()
	{
		$versionString	= $this->_getLocalVersionString();
		$tmpArray		= explode( '.' , $versionString );
		
		if( isset($tmpArray[2]) )
		{
			return $tmpArray[2];
		}
		
		// Unknown build number.
		return 0;
	}
	
	public function _getLocalVersionNumber()
	{
		$versionString	= $this->_getLocalVersionString();
		$tmpArray		= explode( '.' , $versionString );
		
		if( isset($tmpArray[0] ) && isset( $tmpArray[1] ) )
		{
			return doubleval( $tmpArray[0] . '.' . $tmpArray[1] ); 
		}
		return 0;
	}
	
	public function _getCurrentVersionData()
	{
		$parser		=& JFactory::getXMLParser('Simple');

		$data		= new stdClass();
		
		// Get the xml file
		$site		= 'www.jomsocial.com';
		$xml		= 'jomsocial.xml';
		$contents	= '';
		
		$handle		= fsockopen( $site , 80, $errno, $errstr, 30);
		
		if( $handle )
		{
			$out = "GET /$xml HTTP/1.1\r\n";
			$out .= "Host: $site\r\n";
			$out .= "Connection: Close\r\n\r\n";
		
			fwrite($handle, $out);

			$body		= false;
							
			while( !feof( $handle ) )
			{
				$return	= fgets( $handle , 1024 );
				
				if( $body )
				{
					$contents	.= $return;
				}
				
				if( $return == "\r\n" )
				{
					$body	= true;
				}
			}
			fclose($handle);		
		}
		
		$parser->loadString( $contents );
		
		$document	=& $parser->document;
		
		/** Get version **/
		$element		=& $document->getElementByPath( 'version' );
		$data->version	= $element->data();
		
		/** Get build number **/
		$element		=& $document->getElementByPath( 'build' );
		$data->build	= $element->data();
		
		/** Get updated date **/
		$element		=& $document->getElementByPath( 'updated' );
		$data->updated	= $element->data();
		
		/** Get changelog url **/
		$element			=& $document->getElementByPath( 'changelog' );
		$data->changelogURL	= $element->data();

		/** Get upgrade instructions url **/
		$element				=& $document->getElementByPath( 'instruction' );
		$data->instructionURL	= $element->data();

		return $data;
	}
	
	public function _getLocalVersionString()
	{
		static $version		= '';
		
		if( empty( $version ) )
		{
			$parser		=& JFactory::getXMLParser('Simple');
	
			// Load the local XML file first to get the local version
			$xml		= JPATH_COMPONENT . DS . 'community.xml';
			
			$parser->loadFile( $xml );
			$document	=& $parser->document;
	
			$element		=& $document->getElementByPath( 'version' );
			$version		= $element->data();
		}
		return $version;
	}
}