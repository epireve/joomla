<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptHelperProfiletypes
{
	function buildTypes($value, $what)
	{
		$allValues	= array();
		$callFunc = '_build'.JString::ucfirst($what);

		if(!method_exists(new XiptHelperProfiletypes(),$callFunc))
			XiptError::assert(0);

		return XiptHelperProfiletypes::$callFunc($value);
	}

	function _buildProfiletypes($value)
	{
		$allTypes = XiptLibProfiletypes::getProfiletypeArray();
		if (!$allTypes)
			return false;

		return JHTML::_('select.genericlist',  $allTypes, 'profiletypes', 'class="inputbox"', 'id', 'name', $value);
	}

	// not being used
	function _buildPrivacy($value)
	{
		$allValues[]['value'] =  'friends';
		$allValues[]['value'] =  'members';
		$allValues[]['value'] =  'public';

		return JHTML::_('select.genericlist',  $allValues, 'privacy', 'class="inputbox"', 'value', 'value', $value);
	}

	function _buildTemplate($value)
	{
		$templates = XiptHelperJomsocial::getTemplatesList();
		if(!$templates)
			return false;

		foreach($templates as $t)
			$allValues[]['value']=$t;

		return JHTML::_('select.genericlist',  $allValues, 'template', 'class="inputbox"', 'value', 'value', $value);
	}

	function _buildJusertype($value)
	{
		$usertypes= XiptLibJoomla::getJUserTypes();
		if(!$usertypes)
			return false;

		foreach($usertypes as $u)
			$allValues[]['value']=$u;

		return JHTML::_('select.genericlist',  $allValues, 'jusertype', 'class="inputbox"', 'value', 'value', $value);
	}

	function _buildGroup($value)
	{
		//We should add none also.
		$allValues 		= new stdClass();
		$allValues->id 	= 0;
		$allValues->name= 'None';

		$groups = XiptHelperProfiletypes::getGroups();
		array_push($groups, $allValues);
		$value=explode(',',$value);
		return JHTML::_('select.genericlist',  $groups, 'group[]', 'class="inputbox" size="3" multiple ', 'id', 'name', $value);
	}

	function getGroups($id='')
	{
		$query = new XiptQuery();
		$query->select(' `id`, `name` ')
				->from('#__community_groups');

		if(!empty($id))
				$query->where(" `id`  = $id ");

		return $query->dbLoadQuery("","")
					 ->loadObjectList();

		/* TODO : what if group list is empty */
	}

	function getProfileTypeData($id,$what='name')
	{
		//XITODO : Caching can be added
		$searchFor 		= 'name';
		$defaultValue	= 'NONE';
		$data = array(
					'name' 		=> array('name' => 'name', 		'value' => 'All'),
					'template' 	=> array('name' => 'template', 	'value' => 'default'),
					'jusertype'	=> array('name' => 'jusertype', 'value' => 'Registered'),
					'avatar' 	=> array('name' => 'avatar', 	'value' => DEFAULT_AVATAR),
					'watermark'	=> array('name' => 'watermark', 'value' => ''),
					'approve'	=> array('name' => 'approve', 	'value' => true),
					'allowt'	=> array('name' => 'allowt', 	'value' => false),
					'group'		=> array('name' => 'group', 	'value' => 0)
					);
		//XITODO : clean this fn
		XiptError::assert(array_key_exists($what,$data), XiptText::_("ARRAY_KEY_DOES_NOT_EXIST"));

		if($id==0)
			return $data[$what]['value'];

		$val = XiptFactory::getInstance('profiletypes','model')->loadRecords(0);
		if(!$val)
			return $data[$what]['value'];

		if(isset($val[$id]))
			return $val[$id]->$what;

		return false;
	}

	function getProfileTypeName($id)
	{
		//XITODO : Clean ALL / NONE, and cache results
		if($id == XIPT_PROFILETYPE_ALL || empty($id))
			return XiptText::_("ALL");

		if($id == XIPT_PROFILETYPE_NONE)
			return XiptText::_("NONE");

		return XiptHelperProfiletypes::getProfileTypeData($id,'name');
	}

	function getProfileTypeArray($isAllReq = false, $isNoneReq= false)
	{
		$results = XiptFactory::getInstance('profiletypes','model')->loadRecords(0);

		// results will be indexed accroding to id
		// only get the keys
		$retVal = array_keys($results);

		//add all value also
		if($isAllReq === true)
			$retVal[] = XIPT_PROFILETYPE_ALL;

		if($isNoneReq === true)
			$retVal[] = XIPT_PROFILETYPE_NONE;

		return $retVal;
	}

	/**
	 * The function will reapply attributes to every user of profiletype $pid
	 * IMP : if user have custom avatar, then it will not be updated
	 * IMP : we will re-apply watermark on custom avatar
	 * IMP : Users other attribute will be reset irrespective of there settings
	 *
	 * @param $pid
	 * @param $oldData
	 * @param $newData
	 * @return unknown_type
	 */
	function resetAllUsers($pid, $oldData, $newData)
	{
		$allUsers = XiptLibProfiletypes::getAllUsers($pid);

		if(!$allUsers)
			return;

		// //XITODO : needs cleanup Remove hardcoding
		$featuresToReset = array('jusertype','template','group','watermark','privacy','avatar');
		$filteredOldData = array();
		$filteredNewData = array();

		foreach($featuresToReset  as $feature)
		{
			$filteredOldData[$feature]= $oldData->$feature;
			$filteredNewData[$feature]= $newData->$feature;
		}

		foreach ($allUsers as $user)
			XiptLibProfiletypes::updateUserProfiletypeFilteredData($user, $featuresToReset, $filteredOldData, $filteredNewData);
	}

	// XITODO : needs cleanup
	function uploadAndSetImage($file,$id,$what)
	{
		$mainframe	=& JFactory::getApplication();
		CFactory::load( 'helpers' , 'image' );
		$config			= CFactory::getConfig();
		$uploadLimit	= (double) $config->get('maxuploadsize');
		$uploadLimit	= ( $uploadLimit * 1024 * 1024 );

		// @rule: Limit image size based on the maximum upload allowed.
		if( filesize( $file['tmp_name'] ) > $uploadLimit )
		{
			$mainframe->enqueueMessage( XiptText::_('IMAGE_FILE_SIZE_EXCEEDED') , 'error' );
			$mainframe->redirect( CRoute::_('index.php?option=com_xipt&view=profiletypes&task=edit&id='.$id, false) );
		}

		if( !cValidImage($file['tmp_name'] ) )
		{
			$mainframe->enqueueMessage(XiptText::_('IMAGE_FILE_NOT_SUPPORTED'), 'error');
		}
		else
		{
			switch($what) {
				case 'avatar':
					$imageMaxWidth	= AVATAR_WIDTH;
					$thumbWidth 	= AVATAR_WIDTH_THUMB;
					$thumbHeight 	= AVATAR_HEIGHT_THUMB;
					$imgPrefix 		= 'avatar_';
					break;
				case 'watermark':
					$imageMaxWidth	= WATERMARK_WIDTH;
					$thumbWidth 	= WATERMARK_WIDTH_THUMB;
					$thumbHeight 	= WATERMARK_HEIGHT_THUMB;
					$imgPrefix 		= 'watermark_';
					break;
			}

			$storage			= PROFILETYPE_AVATAR_STORAGE_PATH;
			$storageImage		= $storage . DS .$imgPrefix. $id . cImageTypeToExt( $file['type'] );
			$storageThumbnail	= $storage . DS . $imgPrefix . $id.'_thumb' . cImageTypeToExt( $file['type'] );
			$image				= PROFILETYPE_AVATAR_STORAGE_REFERENCE_PATH.DS.$imgPrefix . $id . cImageTypeToExt( $file['type'] );
			//$thumbnail			= PROFILETYPE_AVATAR_STORAGE_REFERENCE_PATH . $imgPrefix . $id.'_thumb' . cImageTypeToExt( $file['type'] );

			//here check if folder exist or not. if not then create it.
			if(JFolder::exists($storage)==false)
				JFolder::create($storage);

			// Only resize when the width exceeds the max.
			if( !cImageResizePropotional( $file['tmp_name'] , $storageImage , $file['type'] , $imageMaxWidth ) )
			{
				$mainframe->enqueueMessage(XiptText::sprintf('COM_XIPT_ERROR_MOVING_UPLOADED_FILE' , $storageImage), 'error');
			}

			// Generate thumbnail
			if(!cImageCreateThumb( $file['tmp_name'] , $storageThumbnail , $file['type'],$thumbWidth,$thumbHeight ))
			{
				$mainframe->enqueueMessage(XiptText::sprintf('COM_XIPT_ERROR_MOVING_UPLOADED_FILE' , $storageThumbnail), 'error');
			}

			$oldFile = XiptLibProfiletypes::getProfiletypeData($id,$what);

			// If old file is default_thumb or default, we should not remove it.
			if(!Jstring::stristr( $oldFile , DEFAULT_AVATAR )
				&& !Jstring::stristr( $oldFile , DEFAULT_AVATAR_THUMB )
					&& $oldFile != $image
					&& $oldFile != ''){
				// File exists, try to remove old files first.
				$oldFile	= JString::str_ireplace( '/' , DS , $oldFile );

				//only delete when required
				if(JFile::exists($oldFile))
					JFile::delete($oldFile);
			}

			//here due to extension mismatch we can break the functionality of avatar
			if($what === 'avatar')
			{
				/* No need to update thumb here , script will update both avatar and thumb */
				//$newThumb   = XiptHelperImage::getThumbAvatarFromFull($newAvatar);
				$oldAvatar  = XiptLibProfiletypes::getProfiletypeData($id,'avatar');

				$allUsers = XiptLibProfiletypes::getAllUsers($id);
				if($allUsers) {

					$filter[] = 'avatar';
					$newData['avatar'] = $image;
					$oldData['avatar'] = $oldAvatar;
					foreach ($allUsers as $userid)
						XiptLibProfiletypes::updateUserProfiletypeFilteredData($userid, $filter, $oldData, $newData);

				}
			}

			//now update profiletype with new avatar or watermark
			if(!XiptFactory::getInstance('profiletypes', 'model')->
					save(array($what => XiptHelperUtils::getUrlpathFromFilePath($image)),$id))
				XiptError::raiseError(__CLASS__.'.'.__LINE__, XiptText::_("ERROR_IN_DATABASE"));
		}
	}

	function checkSessionForProfileType()
    {
    	$mySess = JFactory::getSession();
    	if($mySess)
			return true;

		// session expired, redirect to community page
		$redirectUrl	= XiptRoute::_('index.php?option=com_community&view=register',false);
		$msg 			= XiptText::_('YOUR_SESSION_HAVE_BEEN_EXPIRED_PLEASE_PERFORM_THE_OPERATION_AGAIN');

		return JFactory::getApplication()->redirect($redirectUrl,$msg);
    }

	//XITODO : Remove funda of return url, use configuration
    function setProfileTypeInSession($selectedProfiletypeID)
    {
		// XITODO : move redirection to controller
    	$mySess = & JFactory::getSession();
    	$redirectUrl = XiptHelperJomsocial::getReturnURL();

		// validate values
		if(!XiptLibProfiletypes::validateProfiletype($selectedProfiletypeID)) {
			$msg = XiptText::_('PLEASE_ENTER_VALID_PROFILETYPE');
			JFactory::getApplication()->redirect(XiptRoute::_('index.php?option=com_xipt&view=registration'),$msg);
			return;
		}

		//set value in session and redirect to destination url
		$mySess->set('SELECTED_PROFILETYPE_ID',$selectedProfiletypeID, 'XIPT');
		JFactory::getApplication()->redirect($redirectUrl);
    }
}