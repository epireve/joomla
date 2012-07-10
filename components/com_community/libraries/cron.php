<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
 
defined('_JEXEC') or die('Restricted access');

class CCron {
	private $message;
	
	public function CCron()
	{
		$this->message = array();
	}
	
	/**
	 *
	 */	 	
	public function run() 
	{
		jimport( 'joomla.filesystem.file' );		
		set_time_limit(120);
		
		// The cron job caller has the option to specify specific cron target
		$target = JRequest::getWord('target', '');
		if(!empty($target))
		{
			$target = '_'.$target;
			if(method_exists($this, $target))
			{
				// We're about to run a targeted con job
				// Close the connection so that the caller terminate the call
				while(ob_get_level()) ob_end_clean();
				header('Connection: close');
				ignore_user_abort();
				ob_start();
				echo('Closed');
				$size = ob_get_length();
				header("Content-Length: $size");
				ob_end_flush();
				flush();

				// The caller will get connection closed. Now run the target
				$this->$target();
			} 
		}
		else 
		{
			$this->_sendEmails();
			$this->_convertVideos();
			$this->_sendSiteDetails();
			$this->_archiveActivities();
			$this->_cleanRSZFiles();
			$this->_removeTempPhotos();
			$this->_removeTempVideos();
			$this->_processPhotoStorage();
			$this->_updatePhotoFileSize();
			$this->_updateVideoFileSize();
			$this->_removeDeletedPhotos();
			$this->_processVideoStorage();
			$this->_sendLogInfo(); //only used if log plugin is enabled
			
			$this->_processAvatarStorage( COMMUNITY_PROCESS_STORAGE_LIMIT , 'users' );
			$this->_processAvatarStorage( COMMUNITY_PROCESS_STORAGE_LIMIT , 'groups' );
	
			$this->_removePendingInvitation();
		}
		
		// Include CAppPlugins library
		require_once( JPATH_COMPONENT . DS . 'libraries' . DS . 'apps.php');
		// Trigger system event onCronRun
		$appsLib	=& CAppPlugins::getInstance();
		$appsLib->loadApplications();

		$args = array();
		$appsLib->triggerEvent( 'onCronRun' , $args );
		
		// Display cron messages if neessary
		header('Content-type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8" ?'. '>'; // saperated to assist syntax highliter
		echo '<messages>';
		foreach($this->message as $msg){
			echo '<message>';
			echo $msg;
			echo '</message>';
		}
		echo '</messages>';
		exit;
	}
	
	/**
	 *
	 */
	function runTarget($target)
	{
		
	}
	
	// @since 2.4
	private function _sendLogInfo(){
		
		if(JFile::exists(JPATH_ROOT.CPluginHelper::getPluginURI('community','log').DS.'log.php') && JPluginHelper::isEnabled('community', 'log')){
			require_once( JPATH_ROOT.CPluginHelper::getPluginURI('community','log').DS.'log.php' );
		}else{
			return;
		}
		
		if(class_exists('plgCommunityLog'))
		{
			$dispatcher = & JDispatcher::getInstance();
			$plugin 	=& JPluginHelper::getPlugin('community', 'log');
			$instance 	= new plgCommunityLog($dispatcher, (array)($plugin));
			
			if($instance->export()){
				$this->message[] = JText::_('Success uploading log files');
			}else{
				$this->message[] = JText::_('Failed to upload log files');
			}
		}
	}	

	private function _processAvatarStorage( $updateNum = COMMUNITY_PROCESS_STORAGE_LIMIT , $element = 'users' )
	{
		$config		= CFactory::getConfig();
		$jconfig	= JFactory::getConfig();

		// Because the configuration of users remote storage is stored as user_avatar_storage, we need to get the correct name for it.
		$configElement	= $element == 'users' ? 'user' : $element;
		$configElement	.= '_avatar_storage';
		
		$storageMethod	= $config->getString( $configElement );

		CFactory::load('models', 'photos');
		CFactory::load('libraries', 'storage');
		CFactory::load('helpers', 'image');
		
		$storage		= CStorage::getStorage( $storageMethod );
		$totalMoved		= 0;
		$db				=& JFactory::getDBO();
		
		$query			= 'SELECT * FROM ' . $db->nameQuote( '#__community_' . $element ) . ' '
						. 'WHERE ' . $db->nameQuote( 'storage' ) . ' != ' . $db->Quote( $storageMethod ) . ' '
						. 'AND ' . $db->nameQuote( 'thumb' ) . ' != ' . $db->Quote( '' ) . ' '
						. 'AND ' . $db->nameQuote( 'avatar' ) . ' != ' . $db->Quote( '' ) . ' '
						. 'ORDER BY RAND() '
						. 'LIMIT ' . $updateNum;
		$db->setQuery( $query );
		$rows			= $db->loadObjectList();
		
		if( !$rows )
		{
			$this->message[] = JText::_('No avatars needed to be transferred');
			return;
		}
		
		foreach( $rows as $row )
		{
			$current	= CStorage::getStorage( $row->storage );
			
			// If it exist on current storage, we can transfer it to preferred storage
			if( $current->exists($row->thumb) && $current->exists($row->avatar) )
			{
				// Move locally if file exists on remote storage.
				$tmpThumbFileName	= $jconfig->getValue( 'tmp_path' ) . DS . md5( $row->thumb );
				$current->get( $row->thumb , $tmpThumbFileName );
				
				$tmpAvatarFileName	= $jconfig->getValue( 'tmp_path' ) . DS . md5( $row->avatar );
				$current->get( $row->avatar , $tmpAvatarFileName );

				if( JFile::exists( $tmpThumbFileName ) && JFile::exists( $tmpAvatarFileName ) )
				{
					if( $storage->put( $row->avatar , $tmpAvatarFileName ) && $storage->put( $row->thumb , $tmpThumbFileName ) )
					{
						switch( $element )
						{
							case 'users':
								// User's avatar and thumbnail is successfully uploaded to the remote location.
								// We need to update it now.
								$user			= CFactory::getUser( $row->userid );
								$user->_storage	= $storageMethod;
								$user->save();
								
								$avatar			= $user->_avatar;
								$thumb			= $user->_thumb;

							break;
							case 'groups':
								$group			= JTable::getInstance( 'Group' , 'CTable' );
								$group->load( $row->id );
								$group->storage	= $storageMethod;
								$group->store();
								
								$avatar			= $group->avatar;
								$thumb			= $group->thumb;
							break;								
						}
						// Delete existing storage's avatar and thumbnail.
						$current->delete( $avatar );
						$current->delete( $thumb );

						// Remove temporary generated avatar and thumbnail.
						JFile::delete( $tmpAvatarFileName );
						JFile::delete( $tmpThumbFileName );
						$totalMoved++;
					}
				}
			}
		}
		$this->message[] = JText::sprintf( '%1$s file transferred.' , $totalMoved );
	}
	
	/**
	 * For photos that does not have proper filesize info, update it.
	 * Due to IO issues, run only 20 photos at a time	  
	 */	 	
	public function _updatePhotoFileSize($updateNum = 20){
				
		$db=& JFactory::getDBO();
		
		$sql = 'SELECT '.$db->nameQuote('id')
				.' FROM ' . $db->nameQuote('#__community_photos')
			 	.' WHERE '.$db->nameQuote('filesize').'=' . $db->Quote(0)
			 	.' ORDER BY rand() LIMIT '.$updateNum;
		$db->setQuery($sql);
		$photos = $db->loadObjectList();
		
		if(!empty($photos))
		{
			CFactory::load('models','Photos');
			$photo = JTable::getInstance( 'Photo' , 'CTable' );
			
			foreach($photos as $data)
			{
				$photo->load($data->id);
				$originalPath = JPATH_ROOT . DS . $photo->original;
				if(JFile::exists($originalPath))
				{
					$photo->filesize = sprintf("%u", filesize($originalPath));
					$photo->store();
				}
			}
		}
	}
	
	/**
	 * For videos that does not have proper filesize info, update it.
	 * Due to IO issues, run only 20 photos at a time	  
	 */	 	
	public function _updateVideoFileSize($updateNum = 20){
				
		$db		= JFactory::getDBO();
		$sql	= 'SELECT '.$db->nameQuote('id').', '.$db->nameQuote('creator')
				.' FROM ' . $db->nameQuote('#__community_videos')
				.' WHERE '.$db->nameQuote('type').'='.$db->quote('file')
				.' AND '. $db->nameQuote('status').'='.$db->quote('ready')
				.' AND '. $db->nameQuote('filesize').'=' . $db->Quote(0) 
				.' ORDER BY rand() LIMIT '.$updateNum;
		$db->setQuery($sql);
		$videos = $db->loadObjectList();
		
		if(!empty($videos))
		{
			$video = JTable::getInstance( 'Video' , 'CTable' );
			
			foreach($videos as $data)
			{
				$video->load($data->id);
				$videoPath = JPATH::clean( JPATH_ROOT . DS . $video->path);
				if(JFile::exists($videoPath))
				{
					$video->filesize = sprintf("%u", filesize($videoPath));
					$video->store();
				}
			}
		}
	}
	
	
	/**
	 * Remove all photos that are orphaned, whose parent album has been deleted
	 */	 	
	public function _removeDeletedPhotos($updateNum = 5)
	{
		$db=& JFactory::getDBO();
			
		$sql = 'SELECT * FROM ' . $db->nameQuote('#__community_photos')
			.' WHERE ' . $db->nameQuote('albumid') .'=' . $db->Quote(0)
			.' ORDER BY rand() limit '.$updateNum;
		$db->setQuery($sql);
		$result = $db->loadObjectList();

		if(!$result){
			return;
		}
		
		CFactory::load('models', 'photos');
		
		foreach($result as $row)
		{
			$photo = JTable::getInstance( 'Photo' , 'CTable' );
			$photo->load($row->id);
			$photo->delete();
			
			// Remove all related activities
			$query = 'DELETE FROM ' . $db->nameQuote( '#__community_activities') 
				.' WHERE ' . $db->nameQuote( 'app' ) . ' LIKE '. $db->Quote('photos')
				.' AND ' . $db->nameQuote( 'cid' ) . ' ='.$db->Quote($row->id)
				.' AND ' . $db->nameQuote( 'params' ) . ' LIKE '. $db->Quote('%photoid='.$row->id.'%');
			$db->setQuery( $query );
			$db->query();
		}
		
	}
	/**
	 * Remove old dynamically resized image files
	 */	 	
	public function _cleanRSZFiles($updateNum = 5){
		$db=& JFactory::getDBO();
			
		$sql = 'SELECT * FROM ' . $db->nameQuote('#__community_photos')
			.' ORDER BY rand() limit '.$updateNum;
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		
		if(!$result){
			return;
		}
		
		foreach($result as $row) 
		{
			// delete all rsz_ files which are no longer used
			$rszFiles = JFolder::files(dirname(JPATH_ROOT.DS.$row->image), '.', false, true);
			if($rszFiles)
			foreach($rszFiles as $rszRow)
			{
				if(substr(basename($rszRow), 0, 3) == 'rsz')
				{
					JFile::delete($rszRow);
				}
			}
		}
	}
	
	/**
	 * If remote storage is used, transfer some files to the remote storage
	 * - fetch file from current storage to a temp location
	 * - put file from temp to new storage
	 * - delete file from old storage	 	 	 
	 */	 	
	public function _processPhotoStorage($updateNum = 5){
		$config = CFactory::getConfig();
		$jconfig = JFactory::getConfig();
		$photoStorage = $config->getString( 'photostorage');
		
		//if( $photoStorage != 'file' )
		{
			
			CFactory::load('models', 'photos');
			CFactory::load('libraries', 'storage');
			CFactory::load('helpers', 'image');
			
			$fileTranferCount = 0;
			$storage = CStorage::getStorage( $photoStorage);			
			
			$db=& JFactory::getDBO();
			
			// @todo, we nee to find a way to make sure that we transfer most of
			// our photos remotely
			$sql = 'SELECT * FROM ' . $db->nameQuote('#__community_photos') 
				.' WHERE ' . $db->nameQuote('storage') .'!=' . $db->Quote($photoStorage)
				.' AND ' . $db->nameQuote('albumid') .'!=' . $db->Quote(0)
				.' ORDER BY rand() limit '.$updateNum;
			$db->setQuery($sql);
			$result = $db->loadObjectList();
			
			if(!$result)
			{
				$this->message[] = JText::_('No files to transfer.');
				return;
			}
				
			foreach($result as $row) 
			{
				$currentStorage = CStorage::getStorage( $row->storage );
				
				// If current storage is file based, create the image since we might not have them yet
				if( $row->storage == 'file' && !JFile::exists(JPATH_ROOT.DS.$row->image) )
				{
					// resize the original image to a smaller viewable version
					$this->message[] = 'Image file missing. Creating image file.';

					// make sure original file exist
					if(JFile::exists(JPATH_ROOT.DS.$row->original))
					{
						$displyWidth = $config->getInt('photodisplaysize');
						$info		= getimagesize( JPATH_ROOT . DS . $row->original );
						$imgType	= image_type_to_mime_type($info[2]);
						$width = ($info[0] < $displyWidth) ? $info[0] : $displyWidth;
						CImageHelper::resizeProportional(JPATH_ROOT.DS.$row->original, JPATH_ROOT.DS.$row->image, $imgType, $width);
					}
					else
					{
						$this->message[] = 'Original file is missing!!';
					}
				}
				
				// If it exist on current storage, we can transfer it to preferred storage
				if( $currentStorage->exists($row->image) && $currentStorage->exists($row->thumbnail) )
				{
					// File exist on remote storage, move it locally first
					$tempFilename = $jconfig->getValue('tmp_path'). DS . md5($row->image);
					$currentStorage->get($row->image, $tempFilename);
					
					$thumbsTemp		= $jconfig->getValue('tmp_path'). DS . 'thumb_' . md5($row->thumbnail);
					$currentStorage->get($row->thumbnail, $thumbsTemp);
					
					if( JFile::exists( $tempFilename ) && JFile::exists($thumbsTemp) )
					{
						// we assume thumbnails is always there
						// put both image and thumbnails remotely 
						if( $storage->put($row->image, $tempFilename) && $storage->put($row->thumbnail, $thumbsTemp ) )
						{
							// if the put is successful, update storage type
							$photo = JTable::getInstance( 'Photo' , 'CTable' );
							$photo->load($row->id);
							$photo->storage = $photoStorage;
							$photo->store();
							
							$currentStorage->delete($row->image);
							$currentStorage->delete($row->thumbnail);
							
							// remove temporary file
							JFile::delete($tempFilename);
							JFile::delete( $thumbsTemp );
							$fileTranferCount++;
						}
					}
				}
			}
			
			$this->message[] = $fileTranferCount. ' files transferred.';
			
		}
	}
	
	public function _processVideoStorage($updateNum=5)
	{
		$config			= CFactory::getConfig();
		$jconfig		= JFactory::getConfig();
		$videoStorage	= $config->getString('videostorage');
		
		CFactory::load('libraries', 'storage');
		CFactory::load('models', 'videos');
		CFactory::load('helpers', 'videos');
		
		$db		= JFactory::getDBO();
		$query	= ' SELECT * FROM ' . $db->nameQuote('#__community_videos')
			 	. ' WHERE ' . $db->nameQuote('storage') . ' != ' . $db->quote($videoStorage)
			 	//. ' AND ' . $db->nameQuote('type') . ' = ' . $db->quote('file')
			 	. ' AND ' . $db->nameQuote('status') . ' = ' . $db->quote('ready')
			 	. ' ORDER BY rand() limit ' . $updateNum;

		$db->setQuery($query);
		$result	= $db->loadObjectList();
		
		if (!$result)
		{
			$this->message[] = JText::_('No Videos to transfer.');
			return;
		}
		
		$storage	= CStorage::getStorage($videoStorage);
		$tempFolder	= $jconfig->getValue('tmp_path');
		$fileTransferCount = 0;
		
		foreach ($result as $videoEntry)
		{
			$currentStorage = CStorage::getStorage($videoEntry->storage);
			
			if ($videoEntry->type == 'file')
			{
				// If it exist on current storage, we can transfer it to preferred storage
				if ($currentStorage->exists($videoEntry->path))
				{
					// File exist on remote storage, move it locally first
					$tempFilename	= JPATH::clean( $tempFolder . DS . md5($videoEntry->path));
					$tempThumbname	= JPATH::clean( $tempFolder . DS . md5($videoEntry->thumb));
					$currentStorage->get($videoEntry->path, $tempFilename);
					$currentStorage->get($videoEntry->thumb, $tempThumbname);
					
					if (JFile::exists($tempFilename) && JFile::exists($tempThumbname))
					{
						// we assume thumbnails is always there
						// put both video and thumbnails remotely 
						if ($storage->put($videoEntry->path, $tempFilename) &&
							$storage->put($videoEntry->thumb, $tempThumbname))
						{
							// if the put is successful, update storage type
							$video = JTable::getInstance( 'Video' , 'CTable' );
							$video->load($videoEntry->id);
							$video->storage = $videoStorage;
							$video->store();
							
							// remove files on storage and temporary files
							$currentStorage->delete($videoEntry->path);
							$currentStorage->delete($videoEntry->thumb);
							JFile::delete($tempFilename);
							JFile::delete($tempThumbname);
							
							$fileTransferCount++;
						}
					}
				}
			} else {
				// This is for non-upload video file type e.g. YouTube etc
				// We'll just process the video thumbnail only
				if ($currentStorage->exists($videoEntry->thumb))
				{
					$tempThumbname	= JPATH::clean($tempFolder.DS.md5($videoEntry->thumb));
					$currentStorage->get($videoEntry->thumb, $tempThumbname);
					
					if (JFile::exists($tempThumbname))
					{
						if ($storage->put($videoEntry->thumb, $tempThumbname))
						{
							$video = JTable::getInstance( 'Video' , 'CTable' );
							$video->load($videoEntry->id);
							$video->storage = $videoStorage;
							$video->store();
							
							$currentStorage->delete($videoEntry->thumb);
							JFile::delete($tempThumbname);
							$fileTransferCount++;
						}
					}
				}
			}
		}
		$this->message[] = $fileTransferCount. ' video file(s) transferred';
	}
	
	public function _convertVideos()
	{
		CFactory::load('libraries', 'videos');
		$videos = new CVideos();
		$videos->runConvert();
		if (trim($videos->errorMsg[0]) == 'No videos pending for conversion.')
		{
			$this->message[] = "No videos pending for conversion.";
		}else if(strpos($videos->errorMsg[0], 'videos converted successfully')){
			$this->message[] = $videos->errorMsg[0];
		}else{
			$this->message[] = 'Could not convert video';
		}
	}
	
	public function _sendSiteDetails()
	{
		CFactory::load('libraries', 'jsnetwork');
		$videos = new JSNetworkLibrary();
		$videos->submitToJomsocial();
	}
	
	public function _sendEmails()
	{
		CFactory::load('libraries', 'mailq');
		$mailq = new CMailq();
		
		$config	= CFactory::getConfig();
		$mailq->send( $config->get('totalemailpercron') );
	}
	
	
	/**
	 * Archive older activities for performance reason
	 */	 	
	public function _archiveActivities(){
		$config =   CFactory::getConfig();

		$db	=&  JFactory::getDBO();
		
		// Get the id of the most recent 500th (or whatever archive_activity_limit is)
		$sql 	= 'SELECT id FROM ' . $db->nameQuote('#__community_activities') 
				.' WHERE '
				. $db->nameQuote('archived') .'='. $db->Quote( 0 )
				.' ORDER BY ' . $db->nameQuote('id'). ' DESC'
				.' LIMIT '. $config->get( 'archive_activity_limit' ) .' , 1 ';
		$db->setQuery( $sql );
		$id = $db->loadResult();
		
		if($id)
		{
            
            // Now that we have the id, since id is auto-increment, we can assume
            // any value lower than it is an earlier stream data
    		$sql	= 'UPDATE '.$db->nameQuote( '#__community_activities').' act' 
                    . ' SET act.' . $db->nameQuote('archived').' = ' . $db->Quote( 1 )
					.' WHERE '
					/* Only archive those not archived yet */
					. $db->nameQuote('archived') .'='. $db->Quote( 0 )
					.' AND '
					. $db->nameQuote('id') .'<'. $db->Quote( $id );
					
			$db->setQuery( $sql );
			$db->query();
		}

	}
	
	public function sendEmailsOnPageLoad(){
		CFactory::load('libraries', 'mailq');
		$mailq = new CMailq();
		$mailq->send();
	}

	public function _removeTempPhotos()
	{
		$db	=& JFactory::getDBO();	
		$sql = 'UPDATE ' . $db->nameQuote('#__community_photos')
			 .' SET ' . $db->nameQuote('albumid') .'=' . $db->Quote(0)
			 .' WHERE ' . $db->nameQuote('status').'=' . $db->Quote('temp');

		$db->setQuery($sql);
		$db->query();
	}

	public function _removeTempVideos()
	{
		$db =& JFactory::getDBO();


		$sql = ' SELECT ' . $db->nameQuote('thumb') .' FROM ' . $db->nameQuote('#__community_videos')
			 . ' WHERE ' . $db->nameQuote('status') .'=' . $db->quote('temp');

		$db->setQuery($sql);

		$result = $db->loadObjectList();

		if (!$result)
		{
			$this->message[] = JText::_('No temporary videos to delete.');
			return;
		}

		foreach($result as $video)
		{
			$thumb = JPATH_ROOT.DS.$video->thumb;
			JFile::delete($thumb);
		}

		$sql = 'DELETE FROM ' . $db->nameQuote('#__community_videos')
		     .' WHERE ' . $db->nameQuote('status') .'=' . $db->quote('temp');

		$db->setQuery($sql);
		
		$db->query();
	}

	public function _removePendingInvitation()
	{
	   $eventTable		=& JTable::getInstance( 'Event' , 'CTable' );
	   $eventTable->deletePendingMember();

	   $this->message[] = 'Removed Pending Invitation for Past Event';
	}
}