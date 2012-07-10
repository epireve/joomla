<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class CommunityModelNetwork extends JModel
{	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$mainframe	=& JFactory::getApplication();

		// Call the parents constructor
		parent::__construct();
	}
	
	public function &getJSON()
	{
		// Include neccesary file for operation JSON
		if(!defined('SERVICES_JSON_SLICE')) {
			include_once( AZRUL_SYSTEM_PATH . '/pc_includes/JSON.php');
		}
	
		// Prepare variables
		$JSNInfo = &$this->getJSNInfo();
		
		// Filter out unneccesary keys
		/*
		foreach ($json_input as $key => $value)
		{
			if ( $key != 'enable' && $key != 'cron_freq' )
			{
				$json_filtered[$key] = $value;
			}
		}
		*/
		
		// Convert value to JSON notation
		$json = new Services_JSON();
		$json_output = $json->encode( $JSNInfo );
		
		return $json_output;
	}
	
	public function getJSNInfo()
	{
		// Parse SEF URL to join_url
		$communityLibraries = JPATH_SITE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';
		require_once($communityLibraries);
		
		$params		= array();
		$params['network_site_name']	 	= '';
		$params['network_description']		= '';
		$params['network_keywords'] 		= '';
		$params['network_language']	 		= '';
		$params['network_member_count']		= '';
		$params['network_group_count']		= '';
		$params['network_enable'] 			= '0';
		$params['network_site_url'] 		= JURI::root();
		$params['network_join_url'] 		= CRoute::_(JURI::root().'index.php?option=com_community&view=register', false);
		$params['network_logo_url'] 		= '';
		$params['network_cron_freq'] 		= 24;
		$params['network_cron_last_run']	= 0;

		$mainframe	=& JFactory::getApplication();
		$j_params	= array();
		$j_params['network_site_name']		= $mainframe->getCfg('sitename');
		$j_params['network_description']	= $mainframe->getCfg('MetaDesc');
		$j_params['network_keywords']		= $mainframe->getCfg('MetaKeys');
		$j_params['network_language']		= $mainframe->getCfg('language');

		$params = array_merge( $params, $j_params );

		JTable::addIncludePath( JPATH_COMPONENT_ADMINISTRATOR.DS.'tables' );
		$table	=& JTable::getInstance( 'configuration' , 'CommunityTable' );
		$table->load('config');
		$db_params = new CParameter( $table->params );
		$db_params = $db_params->toArray();

		$n_params	= array();
		foreach($db_params as $key => $value) {
			if(substr($key, 0 ,8)=='network_') {
				$n_params[$key]	= $value;
			}
		}
		
		$params = array_merge( $params, $n_params );

		$params['network_member_count']	= $this->getMembersCount();
		$params['network_group_count']	= $this->getGroupsCount();
		
		return $params;
	}

	public function getMembersCount()
	{
		// Count the unblocked members in Joomla database
		
		$db			=& JFactory::getDBO();
		$query		= 'SELECT COUNT(id) AS memberscount FROM '
					. $db->nameQuote( '#__users' )
					. 'WHERE block=0';
		$db->setQuery($query);
		$member_count = $db->loadResult();

		return $member_count;
	}
	
	public function getGroupsCount()
	{
		// Count the groups in JomSocial database
		
		$db			=& JFactory::getDBO();
		$query		= 'SELECT COUNT(id) AS groupscount FROM '
					. $db->nameQuote( '#__community_groups' )
					. 'WHERE published=1';
		$db->setQuery($query);
		$group_count = $db->loadResult();

		return $group_count;
	}
	
	public function save()
	{	
		// Check for request forgeries
		JRequest::checkToken( 'request' ) or jexit( 'Invalid Token' );
		
		// initialise
		$postData 		= array();
		$logo_file 		= array();
		$logo_replace	= 0;
		$save_image_ok	= null;
		
		// get post data
		$postData		= JRequest::get( 'post' );
		$logo_file		= JRequest::get( 'files' );
		$token			= JUtility::getToken();
		
		if (!empty($logo_file['network_Filedata']))
		{
			$logo_image = $logo_file['network_Filedata'];
		}

		if( isset($postData['network_replace_image']) )
		{
			if( $postData['network_replace_image'] == 1 )
			{
				$logo_replace = 1;
			}
		}

		// instanstiate the table
		JTable::addIncludePath( JPATH_COMPONENT_ADMINISTRATOR.DS.'tables' );
		$table	=& JTable::getInstance( 'configuration' , 'CommunityTable' );
		
		$table->load('config');
		
		$params = new CParameter( $table->params );
		
		// save logo image
		if( !empty($logo_image['name']) && ($logo_image['size'] > 0) )
		{

			$save_image_ok = $this->saveImage( $logo_image, $logo_replace );
		}
		
		// clean and limits to maximum 10 tags/keywords
		$filteredTags = explode(",", $postData['network_keywords']);
		
		foreach($filteredTags as $key => $filteredTag) {
			$filteredTags[$key] = trim($filteredTag);
		}
		
		$filteredTags = array_unique($filteredTags);
		
		foreach($filteredTags as $key => $filteredTag) {
			if(empty($filteredTag)) {
				unset($filteredTags[$key]);
			}
		}
		
		$filteredTags = array_slice($filteredTags, 0, 10);
		$postData['network_keywords'] = implode(", ", $filteredTags);

		// set data properly
		foreach( $postData as $key => $value )
		{
			// exclude unnecessary keys from form
			$wanted_keys = array( 
					'network_enable', 
					'network_description', 
					'network_keywords', 
					'network_join_url',
					'network_logo_url', 
					'network_cron_freq', 
					'network_cron_last_run' );
			
			
			if( in_array($key, $wanted_keys) )
			{
				$params->set( $key , $value );
			}
		}

		// update data if new logo image saved
		if( $save_image_ok )
		{
			$params->set( 'logo_url', $save_image_ok );
		}
		// convert final data in INI string and put in table
		$table->params	= $params->toString();
		
		// Save it
		if( !$table->store() )
		{
			return false;
		}

		return true;
	}
	
	public function saveImage( $file = '', $replace = 0 )
	{
		// Import libraries
		jimport('joomla.filesystem.file');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_media' . DS . 'helpers' . DS . 'media.php');
		
		// Define some constants
		$params =& JComponentHelper::getParams('com_media');
		
		define('COM_MEDIA_BASE',    JPATH_ROOT.DS.$params->get('file_path'));
		define('COM_MEDIA_BASEURL', JURI::root().$params->get('file_path'));
		
		// And set some variables
		$folder = '';
		$filepath = JPath::clean(COM_MEDIA_BASE.DS.$folder.DS.strtolower($file['name']));
		
		// Basic validation
		if (!isset($file['name']))
		{
			return false;
		}
		
		// Make the filename safe
		$file['name'] = JFile::makeSafe($file['name']);

		// More Validations
		if( !MediaHelper::canUpload( $file, $err ) )
		{
			JError::raiseNotice(100, JText::_($err));
			return false;
		}
		
		// Only accept if file type is image
		$file_format = strtolower(JFile::getExt($file['name']));
		$allowable = array( 'jpg', 'png', 'gif', 'xcf', 'odg', 'bmp' ); // depends also on smart_resize_image 
		
		if( !in_array($file_format, $allowable) )
		{
			$err = 'WARNFILETYPE';
			JError::raiseNotice(100, JText::_($err));
			return false;
		}
		
		// Image resize
		$resize_ok = $this->smart_resize_image( $file['tmp_name'], 80, 80, true );

		// Check if file exists
		if( JFile::exists($filepath) )
		{
			$exists = 1;
		}
		
		// File exists, warn user
		if( $replace == 0 && $exists == 1 )
		{
			JError::raiseNotice(100, JText::_('COM_COMMUNITY_NETWORK_IMAGE_FILE_ALREADY_EXISTS_ERROR'));
			return false;
		}
		
		// Delete the existing file
		if( $replace == 1 && $exists == 1 )
		{
			$delete_ok = $this->deleteImage($file['name']);
		}
		
		// Delete failed
		if( !$delete_ok )
		{
			// i think the function already raised error msg
		}

		// Try to upload
		if(!JFile::upload($file['tmp_name'], $filepath))
		{
			JError::raiseWarning(100, JText::_('COM_COMMUNITY_NETWORK_UNABLE_TO_UPLOAD_FILE_ERROR'));
			return false;
		}
		
		// upload succesful
		return COM_MEDIA_BASEURL . '/' . strtolower($file['name']);
	}
	
	// credit to http://mediumexposure.com/techblog/smart-image-resizing-while-preserving-transparency-php-and-gd-library
	public function smart_resize_image( $file, $width = 0, $height = 0, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false )
	{
		if( $height <= 0 && $width <= 0 ) {
			return false;
		}
	
		$info = getimagesize( $file );
		$image = '';
	
		$final_width = 0;
		$final_height = 0;
		list( $width_old, $height_old ) = $info;
	
		if( $proportional )
		{
			if( $width == 0 ) $factor = $height/$height_old;
			elseif( $height == 0 ) $factor = $width/$width_old;
			else $factor = min( $width / $width_old, $height / $height_old );   
			
			$final_width = round( $width_old * $factor );
			$final_height = round( $height_old * $factor );
		}
		else
		{
			$final_width = ( $width <= 0 ) ? $width_old : $width;
			$final_height = ( $height <= 0 ) ? $height_old : $height;
		}
	
		switch( $info[2] )
		{
			case IMAGETYPE_GIF:
				$image = imagecreatefromgif($file);
				break;
			case IMAGETYPE_JPEG:
				$image = imagecreatefromjpeg($file);
				break;
			case IMAGETYPE_PNG:
				$image = imagecreatefrompng($file);
				break;
			default:
				return false;
		}
	
		$image_resized = imagecreatetruecolor( $final_width, $final_height );
	
		if( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) )
		{
			$trnprt_indx = imagecolortransparent( $image );
	
			// If we have a specific transparent color
			if( $trnprt_indx >= 0 )
			{
			// Get the original image's transparent color's RGB values
			$trnprt_color = imagecolorsforindex( $image, $trnprt_indx );
	
			// Allocate the same color in the new image resource
			$trnprt_indx = imagecolorallocate( $image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue'] );
	
			// Completely fill the background of the new image with allocated color.
			imagefill( $image_resized, 0, 0, $trnprt_indx );
	
			// Set the background color for new image to transparent
			imagecolortransparent( $image_resized, $trnprt_indx );
			}
			
			// Always make a transparent background color for PNGs that don't have one allocated already
			elseif( $info[2] == IMAGETYPE_PNG )
			{
	 
				// Turn off transparency blending (temporarily)
				imagealphablending( $image_resized, false );
				
				// Create a new transparent color for image
				$color = imagecolorallocatealpha( $image_resized, 0, 0, 0, 127 );
				
				// Completely fill the background of the new image with allocated color.
				imagefill( $image_resized, 0, 0, $color );
				
				// Restore transparency blending
				imagesavealpha( $image_resized, true );
			}
		}
	
		imagecopyresampled( $image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old );
	
		if( $delete_original )
		{
			if ( $use_linux_commands )
				exec('rm '.$file);
			else
				@unlink($file);
		}
	
		switch( strtolower( $output ) )
		{
			case 'browser':
				$mime = image_type_to_mime_type( $info[2] );
				header( "Content-type: $mime" );
				$output = NULL;
				break;
			case 'file':
				$output = $file;
				break;
			case 'return':
				return $image_resized;
				break;
			default:
				break;
		}
	
		switch( $info[2] )
		{
			case IMAGETYPE_GIF:
				imagegif( $image_resized, $output );
				break;
			case IMAGETYPE_JPEG:
				imagejpeg( $image_resized, $output );
				break;
			case IMAGETYPE_PNG:
				imagepng( $image_resized, $output );
				break;
			default:
				return false;
		}

		return true;
	}
	
	public function deleteImage( $path = null, $folder = ''  )
	{

		// Initialize variables
		$ret = true;
		
		// Validate image path
		if ($path !== JFile::makeSafe($path))
		{
			JError::raiseWarning(100, JText::_('COM_COMMUNITY_NETWORK_UNABLE_TO_DELETE').htmlspecialchars($path, ENT_COMPAT, 'UTF-8').' '.JText::_('COM_COMMUNITY_NETWORK_WARNFILENAME'));
		}

		$fullPath = JPath::clean( COM_MEDIA_BASE.DS.$folder.DS.$path );
		
		// Delete the image file
		if (is_file($fullPath))
		{
			$ret |= !JFile::delete($fullPath);
		}
		else if (is_dir($fullPath))
		{
			$files = JFolder::files($fullPath, '.', true);
			$canDelete = true;
			foreach ($files as $file)
			{
				if ($file != 'index.html')
				{
					$canDelete = false;
				}
			}
			if ($canDelete)
			{
				$ret |= !JFolder::delete($fullPath);
			}
			else
			{
				JError::raiseWarning(100, JText::_('COM_COMMUNITY_NETWORK_UNABLE_TO_DELETE').$fullPath.' '.JText::_('COM_COMMUNITY_NETWORK_NOT_EMPTY'));
			}
		}
		return true;
	}
}