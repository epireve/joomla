<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');
 
class XiptControllerProfiletypes extends XiptController 
{
    
	function __construct($config = array())
	{
		parent::__construct($config);
		
		//registering some extra in all task list which we want to call
		$this->registerTask( 'orderup' , 'saveOrder' );
		$this->registerTask( 'orderdown' , 'saveOrder' );
	}
	
	function edit($id=0)
	{
		$id 	= JRequest::getVar('id', $id);					
		return $this->getView()->edit($id);
	}	
	
	function apply()
	{
		$info = $this->_processSave();
		$link = XiptRoute::_('index.php?option=com_xipt&view=profiletypes&task=edit&id='.$info['id'], false);
		$this->setRedirect($link, $info['msg']);
	}
		
	function save()
	{
		$info = $this->_processSave();
		$link = XiptRoute::_('index.php?option=com_xipt&view=profiletypes', false);
		$this->setRedirect($link, $info['msg']);
	}
	
	// XITODO : needs test case
	function _processSave($post=null,$id=0)
	{
		if($post === null) $post	= JRequest::get('post');
		$id	= JRequest::getVar('id', $id, 'post');
		
	
		//We only need few data as special case
		$data = $post;
		$data['tip'] 		= JRequest::getVar( 'tip', $post['tip'], 'post', 'string', JREQUEST_ALLOWRAW );
		$data['group'] 		= implode(',',$post['group']);

		// These data will be seperately stored, we dont want to update these
		unset($data['watermarkparams']);
		unset($data['config']);
		unset($data['privacy']);
			
		$model = $this->getModel();
		//for Reset we will save old Data
		// give 0 in loadRecords so that all records will be loaded
		$allData = $model->loadRecords(0);
		if(isset($allData[$id]))
			$oldData = $allData[$id];
		
		// set ordering
		if(end($allData)){
			if($allData[$id]->id == 0)
			$data['ordering'] = end($allData)->ordering + 1;
		}
		else
			$data['ordering'] =  1;
			
		// now save model
		$id	 = $model->save($data, $id);
		XiptError::assert($id, XiptText::_("$id NOT_EXISTS"), XiptError::ERROR);
		
		// Now store other data
		// Handle Avatar : call uploadImage function if post(image) data is set
		$fileAvatar		= JRequest::getVar( 'FileAvatar' , '' , 'FILES' , 'array' );
		if(isset($fileAvatar['tmp_name']) && !empty($fileAvatar['tmp_name']))
			XiptHelperProfiletypes::uploadAndSetImage($fileAvatar,$id,'avatar');

		//display demo on watermark profile according ProfileType
		if($post['watermarkparams']['enableWaterMark'])
			$post['watermarkparams']['demo']= $id;
			
		// if jsPrivacyController = 0 then Old privacy set in profile-type table
		if(is_array($post['privacy']) && $post['privacy']['jsPrivacyController'] == 0){
			$oldPrivacy = $model->loadParams($id,'privacy')->toArray();
			$oldPrivacy['jsPrivacyController'] = $post['privacy']['jsPrivacyController'];
			$post['privacy']= $oldPrivacy;
		}
		
		// Handle Params : watermarkparams, privacy, config
		$model->saveParams($post['watermarkparams'],$id, 'watermarkparams');
		$model->saveParams($post['config'], 		$id, 'config');
		$model->saveParams($post['privacy'], 		$id, 'privacy');

		// now generate watermark, and update watermark field
		$image = $this->_saveWatermark($id);
		
		//XITODO : Ensure data is reloaded, not cached
		$newData = $model->loadRecords(0);
		$newData = $newData[$id];
		//to reset privacy of users need to load from loadParams
		$newData->privacy = $model->loadParams($id,'privacy');		
		
	    // Reset existing user's 
		if($post['resetAll'] && isset($oldData)) {
					
			$newData = serialize($newData);
			//new method 
			$preTask = JRequest::getVar('task', 'save');
			$session = JFactory::getSession();
			$session->set('oldPtData',$oldData,'jspt');
			$session->set('newPtData',$newData,'jspt');
			$session->set('preTask',$preTask,'jspt');
			
			if(!XIPT_TEST_MODE)
			{
				JFactory::getApplication()->redirect(XiPTRoute::_("index.php?option=com_xipt&view=profiletypes&task=resetall&id=$id",false));
			}

			$this->resetall($id,25000);			
			//old method
			//XiptHelperProfiletypes::resetAllUsers($id, $oldData, $newData);	
		}
					
		$info['id'] = $id;
		$info['msg'] = XiptText::_('PROFILETYPE_SAVED');

		return $info;
	}
	
	//this function will reset users in chunks 
	function resetall($id = 0, $limit = RESETALL_USER_LIMIT, $start = 0){
		
		$mainframe	= JFactory::getApplication();
		$start		= JRequest::getVar('start', $start);
		$id			= JRequest::getVar('id', $id);
		//getting from session
		$session   = JFactory::getSession();
		$oldPtData = $session->get('oldPtData', '','jspt');
		$newPtData = $session->get('newPtData', '', 'jspt');
		
		$newPtData = unserialize($newPtData);
		$allUsers = XiptLibProfiletypes::getAllUsers($id);

		if(!$allUsers)
		{
			$msg = XiptText::_('NO_USER_TO_RESET');
			$mainframe->redirect('index.php?option=com_xipt&view=profiletypes', $msg);
		}
		
		$total = count($allUsers);
		$users = array_chunk($allUsers, $limit);

		if(empty($users[$start])){
			$info['id'] = $id;
			$info['msg'] = XiptText::_('PROFILETYPE_SAVED');
			$preTask = $session->set('preTask','','jspt');
			$preTask = ($preTask =='apply') ? 'edit':'display';
			$session->clear('oldPtData','jspt');
			$session->clear('newPtData','jspt');
			$link = XiptRoute::_('index.php?option=com_xipt&view=profiletypes&task='.$preTask.'&id='.$id.'', false);
			$mainframe->redirect($link, $info['msg']);
		}
		
		$users = $users[$start];
	
		//XITODO : needs cleanup Remove hardcoding
		$featuresToReset = array('jusertype','template','group','watermark','avatar');
		$filteredOldData = array();
		$filteredNewData = array();
		
		// when privacy controlled by admin
		if( 1 == $newPtData->privacy->get('jsPrivacyController')){
			array_push($featuresToReset, 'privacy');
		}
		
			
		
		foreach($featuresToReset  as $feature)
		{
			$filteredOldData[$feature]= $oldPtData->$feature;
			$filteredNewData[$feature]= $newPtData->$feature;
		}

		foreach ($users as $user)
			XiptLibProfiletypes::updateUserProfiletypeFilteredData($user, $featuresToReset, $filteredOldData, $filteredNewData);
	
		$start = $start+1;
		
		if(!XIPT_TEST_MODE == 0)
    		return $start;
    	
    	return $this->getView()->resetall($id,$start,$total,$limit);
		//$mainframe->redirect(XiPTRoute::_("index.php?option=com_xipt&view=profiletypes&task=resetall&start=$start&id=$id",false));
    	
			
	}
	
	// this function generates thumbnail of watermark
	function generateThumbnail($imageName,$filename,$storage,$newData,$config)
	{
		require_once JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'helpers'.DS.'image.php';
					
		$fileExt = JFile::getExt($filename);
		$thumbnailName = 'watermark_'. $newData->id.'_thumb.'.$fileExt;
		$storageThumbnail = $storage . DS .$thumbnailName;
		$watermarkPath = $storage.DS.$imageName.'.'.$fileExt;
		
		$watermarkThumbWidth  = $config->get('xiThumbWidth',80);
		$watermarkThumbHeight = $config->get('xiThumbHeight',20);
        // create a transparent blank image
        // if type of watermark is text call ImageCreateTrueColor else
        //else call imageCreateTransparent
        if($config->get('typeofwatermark','0')=='0')
            $dstimg   =   ImageCreateTrueColor($watermarkThumbWidth, $watermarkThumbHeight);
        else
            $dstimg   =   XiptLibImage::imageCreateTransparent($watermarkThumbWidth, $watermarkThumbHeight);
		

		$watermarkType = XiptHelperImage::getImageType($watermarkPath);
		$srcimg	 = cImageOpen( $watermarkPath , $watermarkType);
		//XITODO : also support other formats
		
		
		if(imagecopyresampled($dstimg,$srcimg,0,0,0,0,$watermarkThumbWidth,$watermarkThumbHeight,$config->get('xiWidth',64),$config->get('xiHeight',64)))
		{
			//fix for permissions
			imagepng($dstimg,$storageThumbnail);
			chmod($storageThumbnail, 0744);
		}	
		else
			XiptError::raiseWarning('XIPT_THUMB_WAR','THUMBNAIL NOT SUPPORTED');
		
		/*if(!cImageCreateThumb( $watermarkPath , $storageThumbnail , XiptHelperImage::getImageType($watermarkPath),$config->get(xiWidth,64)/2,$config->get(xiHeight,64)/2));
			$info['msg'] .= sprintf(JText::_('ERROR MOVING UPLOADED FILE') , $storageThumbnail);*/
		return;
	}
	
	function remove($ids=array())
	{
		$ids	= JRequest::getVar( 'cid', $ids, 'post', 'array' );
		$link = XiptRoute::_('index.php?option=com_xipt&view=profiletypes', false);
	
		$i = 1;
		//XITODO : Clean and commonize it
		$defaultPtype = XiptLibProfiletypes::getDefaultProfiletype();
		foreach( $ids as $id )
		{
			// can not delete default profiletype
			if($id == $defaultPtype)
			{
				$message= XiptText::_('CAN_NOT_DELETE_DEFAULT_PROFILE_TYPE');
				JFactory::getApplication()->enqueueMessage($message);
				continue;
			}
			
			if(!$this->getModel()->delete($id))
			{
				// If there are any error when deleting, we just stop and redirect user with error.
				$message	= XiptText::_('ERROR_IN_REMOVING_PROFILETYPE');
				$this->setRedirect($link, $message);
				return false;
			}
			$i++;
		}	
		
		$message	= ($i - 1).' '.XiptText::_('PROFILETYPE_REMOVED');		
		$this->setRedirect($link, $message);
	}
	
	function removeAvatar($id=0, $oldAvatar=null)
	{
		//get id and old avatar.
		$id        = JRequest::getVar('id', $id);
		$oldAvatar = JRequest::getVar('oldAvatar', $oldAvatar);
		
		$newavatar 		= DEFAULT_AVATAR ;
		$newavatarthumb	= DEFAULT_AVATAR_THUMB;
		$profiletype	= $this->getModel();
		
		$profiletype->save( array('avatar' => $newavatar), $id );;
		
		$profiletype->resetUserAvatar($id, $newavatar, $oldAvatar, $newavatarthumb);
		$this->setRedirect('index.php?option=com_xipt&view=profiletypes');
		return true;
	}
	
	function _saveWatermark($id)
	{
		$model = $this->getModel();

		//Collect Newly saved data
		$newData = $model->loadRecords(0);
		$newData = $newData[$id];
		
		$config = new XiptParameter('','');
		$config->bind($newData->watermarkparams);
		// if enable water mark is false then no need to create watermark
		if(!$config->get('enableWaterMark')){
			return false;
		}
		//no change condition i.e if type of watermark is image
        // but no image is selected then return
		if( empty($_FILES['watermarkparams']['tmp_name']['xiImage']) 
		    && $config->get('typeofwatermark','0')=='1')
		  { return false;}
		// generate watermark image		
		//XITODO : improve nomenclature
		$imageGenerator = new XiptLibImage($config);
		$storage		= PROFILETYPE_AVATAR_STORAGE_PATH;
		$imageName 		= 'watermark_'. $id;
        // create watermark according to the type of watermark selected
		if($config->get('typeofwatermark','0')=='1')
				$filename=$imageGenerator->createImageWatermark($storage,$imageName);
		  else 
				$filename		= $imageGenerator->genImage($storage,$imageName);
		//XITODO : assert on filename
		$image = PROFILETYPE_AVATAR_STORAGE_REFERENCE_PATH.DS.$filename;
		$data 	= array('watermark' => XiptHelperUtils::getUrlpathFromFilePath($image));
		$this->generateThumbnail($imageName,$filename,$storage,$newData,$config);
		
		// now save model
		$model->save($data, $id);
		return $image;
	}
	
	function copy($ids = array())
	{
		//get selected profile type ids
		$cid	= JRequest::getVar( 'cid', $ids, 'post', 'array' );
		if (count($cid) == 0){
			 $this->setRedirect('index.php?option=com_xipt&view=profiletypes');
 			 return JError::raiseWarning( 500, XiptText::_( 'NO_ITEMS_SELECTED' ) );
	    }
		//get profile type data by id
		$model = $this->getModel();
		$data  = $model->loadRecords(0);
		
		// get last profile type from array ( will be last in ordering )
 		$lastOrder = end($data)->ordering + 1;

		foreach($cid as $id){		
			$data[$id]->id        = 0;
			$data[$id]->name      = XiptText::_('COPY_OF').$data[$id]->name;
			$data[$id]->published = 0;
			$data[$id]->ordering  = $lastOrder++;
			$model->save($data[$id],0);
		}

		$this->setRedirect('index.php?option=com_xipt&view=profiletypes');
		return false;
	}

}

