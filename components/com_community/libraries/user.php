<?php
/**
 * @category	Library
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class CUser extends JUser
implements CGeolocationInterface {

	var $_userid	= 0;
	var $_status = '';
	var $_cparams		= null;
	var $_tooltip		= null;
	var $_points		= 0;
	var $_init			= false;
	var $_thumb			= '';
	var $_avatar		= '';
	var $_isonline		= false;
	var $_view			= 0;
	var $_posted_on		= null;
	var $_invite		= 0;
	var $_friendcount	= 0;
	var $_alias			= null;
	var $_profile_id	= 0;
	var $_storage		= 'file';
	var $_watermark_hash	= '';
	var $_search_email  = 1;
	var $_friends		= null;
	var $_groups		= null;
	var $_events		= null;
        var $_cacheAction       =array();

	/* interfaces */
	var $latitude	= null;
	var $longitude	= null;

	/**
	 * Constructor.
	 * Perform a shallow copy of JUser object
	 */
	public function CUser($id){

		if($id == null) {
			$user =& JFactory::getUser($id);
			$id = $user->id;
		}

		$this->id = $id;
	}

	/**
	 * Method to set the property when the properties are already
	 * assigned
	 *
	 * @param property	The property value that needs to be changed
	 * @param value		The value that needs to be set
	 *
	 **/
	public function set( $property , $value )
	{
		CError::assert( $property , '' , '!empty' , __FILE__ , __LINE__ );
		$this->$property	= $value;
	}

	public function getAlias()
	{
		// If the alias is not yet defined, return the default value
		if(empty( $this->_alias ))
		{
			$config	= CFactory::getConfig();
			$name	= $config->get( 'displayname' );
			$alias	= JFilterOutput::stringURLSafe( $this->$name );
			$this->_alias	= $this->id . ':' . $alias;

			// Save the alias into the database when the user alias is not generated.l
			$this->save();
		}

		return $this->_alias;
	}

	public function delete()
	{
		$db		=& JFactory::getDBO();

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__community_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$db->query();

		return parent::delete();
	}

	public function getAppParams( $appName )
	{
		$model	= CFactory::getModel( 'apps' );
		$params	= $model->getUserAppParams( $model->getUserApplicationId( $appName , $this->id ) );
		$params	= new CParameter( $params );

		return $params;
	}

	/**
	 * Inititalize the user JUser object
	 * return true if the user is a new
	 */
	public function init($initObj = null) {
		$isNewUser = false;
		
		if(!$this->_init) {
			$config	= CFactory::getConfig();
			$db		=& JFactory::getDBO();
			$obj = $initObj;

			if($initObj == null ){
				$query  = 'SELECT  '
						. '	a.' . $db->nameQuote('userid') .' as _userid , '
						. '	a.' . $db->nameQuote('status') .' as _status , '
						. '	a.' . $db->nameQuote('points') .'	as _points, '
						. '	a.' . $db->nameQuote('posted_on') .' as _posted_on, '
						. '	a.' . $db->nameQuote('avatar') .'	as _avatar , '
						. '	a.' . $db->nameQuote('thumb') .'	as _thumb , '
						. '	a.' . $db->nameQuote('invite') .'	as _invite, '
						. '	a.' . $db->nameQuote('params') .'	as _cparams,  '
						. '	a.' . $db->nameQuote('view') .'	as _view, '
						. ' a.' . $db->nameQuote('friends') .' as _friends, '
						. ' a.' . $db->nameQuote('groups') .' as _groups, '
						. ' a.' . $db->nameQuote('events') .' as _events, '
						. ' a.' . $db->nameQuote('friendcount') .' as _friendcount, '
						. ' a.' . $db->nameQuote('alias') .' as _alias, '
						. ' a.' . $db->nameQuote('profile_id') .' as _profile_id, '
						. ' a.' . $db->nameQuote('storage') .' as _storage, '
						. ' a.' . $db->nameQuote('watermark_hash') .' as _watermark_hash, '
						. ' a.' . $db->nameQuote('search_email') .' as _search_email, '
						. ' s.' . $db->nameQuote('userid') .' as _isonline, u.* '
						. ' FROM ' . $db->nameQuote('#__community_users') .' as a '
						. ' LEFT JOIN ' . $db->nameQuote('#__users') .' u '
			 			. ' ON u.' . $db->nameQuote('id') .'=a.' . $db->nameQuote('userid')
						. ' LEFT OUTER JOIN ' . $db->nameQuote('#__session') .' s '
			 			. ' ON s.' . $db->nameQuote('userid') .'=a.' . $db->nameQuote('userid')
			 			. ' AND s.' . $db->nameQuote('client_id') .' !=' . $db->Quote('1')
						. ' WHERE a.' . $db->nameQuote('userid') .'=' . $db->Quote($this->id) ;

				$db->setQuery($query);
				$obj = $db->loadObject();
				if($db->getErrorNum())
				{
					JError::raiseError( 500, $db->stderr());
			    }
			}

			// Initialise new user
			if(empty($obj))
			{
				if( !$obj && ($this->id != 0) )
				{
					// @rule: ensure that the id given is correct and exists in #__users
					$existQuery	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__users' ) . ' '
								. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $this->id );

					$db->setQuery( $existQuery );

					$isValid	= $db->loadResult() > 0 ? true : false;

					if( $isValid )
					{
						// We need to create a new record for this specific user first.

						$obj = new stdClass();

						$obj->userid	= $this->id;
						$obj->points	= $this->_points;
						$obj->thumb		= '';
						$obj->avatar	= '';

						// Load default params
						$obj->params = "notifyEmailSystem=" . $config->get('privacyemail') . "\n"
									 . "privacyProfileView=" . $config->get('privacyprofile') . "\n"
									 . "privacyPhotoView=" . $config->get('privacyphotos') . "\n"
									 . "privacyFriendsView=" . $config->get('privacyfriends') . "\n"
									 . "privacyGroupsView=" . $config->get('privacy_groups_list') . "\n"
									 . "privacyVideoView=" . $config->get('privacyvideos') . "\n"
									 . "notifyEmailMessage=" . $config->get('privacyemailpm') . "\n"
									 . "notifyEmailApps=" . $config->get('privacyapps') . "\n"
									 . "notifyWallComment=" . $config->get('privacywallcomment') . "\n";
						//load default email privacy settings
						CFactory::load( 'libraries' , 'emailtypes' );
						$emailtypes = new CEmailTypes();
						$obj->params .= $emailtypes->convertToParamsString();

						$db->insertObject( '#__community_users' , $obj );

						if($db->getErrorNum())
						{
							JError::raiseError( 500, $db->stderr());
					    }

					    // Reload the object
						$db->setQuery($query);
						$obj = $db->loadObject();

						$isNewUser = true;

					}

				}
			}


			if($obj) {
				$thisVars = get_object_vars($this);
				// load cparams
				$this->_cparams = new CParameter($obj->_cparams);

				// fix a bug where privacyVideoView = 1
				if ($this->_cparams->get('privacyVideoView')==1)
				{
					$this->_cparams->set('privacyVideoView', 0);
				}

				unset($obj->_cparams);

				// load user params
				$this->_params = new CParameter($obj->params);
				unset($obj->params);

				foreach( $thisVars as $key=>$val ) {
					if( isset($obj->$key) ) {
						$this->$key = $obj->$key ;
					}
				}

				#correct the friendcount here because blocked user are still counted in "friendcount"
				//$model	= CFactory::getModel( 'friends' );
				//$realfriendcount = $model->getFriendsCount($this->id);
				//$this->_friendcount = $realfriendcount;
				
				// Update group list if we haven't get
				if( $this->_params->get('update_cache_list', 0) == 0 )
				{
						//dont load updateGroupList in the backend

						if(strpos(JURI::current(),'administrator') == false)
						{
							$this->updateGroupList();
							$this->updateEventList();
						}
            
            $this->_params->set('update_cache_list', 1);
						$this->save();
				}
				
				// Set FB post param to default system value if it is not yet set
				if( $this->_cparams->get('postFacebookStatus', null) == null)
				{
					$this->_cparams->set('postFacebookStatus', $config->get('fbconnectpoststatus') );
				}
				

			} else {
				// this is a visitor, we still have to create params object for them
				$this->_cparams = new CParameter('');
				$this->_params  = new CParameter('');
			}

			$this->_init = true;
		}

		return $isNewUser;

	}
	
	/**
	 * Return true if the user is a member of the group
	 * @since 2.4
	 * @param type $id 
	 */
	public function isInGroup ($id)
	{
		// If id is null or empty, you're not in it of course
		if(empty($id)) return false;
		
		$groups = explode(',', $this->_groups);
		return in_array($id, $groups);
	}
	
	/**
	 * Return true if user has responsed to the event
	 * @since 2.4
	 * @param type $id 
	 */
	public function isInEvent( $id )
	{
		// If id is null or empty, you're not in it of course
		if(empty($id)) return false;
		
		$events = explode(',', $this->_events);
		return in_array($id, $events);
	}

	/**
	 * True if this person is a friend with the given userid
	 * @param <type> $id
	 */
	public function isFriendWith( $id )
	{
		static $friendsUpdated = false;
		if($this->id == 0)
			return false;

		// I am friend with myself! :)
		if($this->id == $id)
			return true;

		// $this->_friends will be null if if is not initialized yet
		// If friendcount doesn't match friend list, it is not initilized yet
		if( ($this->_friendcount != 0 || empty($this->_friends)) && !$friendsUpdated )
		{
			// If user has no friend yet, the data might not have been initialized
			// Query and update the record
			$this->updateFriendList(TRUE);
			$friendsUpdated = true;
		}
		$friendsIds = explode(',', $this->_friends);
		
		return in_array($id, $friendsIds);
	}

	/**
	 * Update internal list of friends
	 * Run only when user is logged in
	 */
	public function updateFriendList( $forceUpdate = false )
	{
		$my = CFactory::getUser();
		if( $my->id == $this->id || $forceUpdate)
		{
			$friendModel = CFactory::getModel('friends');
			$friendsIds = $friendModel->getFriendIds($this->id);
			$friendsId = implode(',', $friendsIds);
			
			if(	$this->_friends != $friendsId) {
				$this->_friendcount = count($friendsIds);
				$this->_friends = $friendsId;
				$this->save();
			}
		}
	}
	
	/**
	 * Update internal list of groups this user is a member of
	 * Run only when user is logged in
	 */
	public function updateGroupList( $forceUpdate = false )
	{
		$my = CFactory::getUser();
		if( $my->id == $this->id || $forceUpdate)
		{
			$groupModel = CFactory::getModel( 'groups' );
			$groupsIds = $groupModel->getGroupIds($this->id);
			$groupsIds = implode(',' , $groupsIds);

			if( $this->_groups != $groupsIds){
				$this->_groups = $groupsIds;
				$this->save();
			}
		}
	}
	
	/**
	 * Update internal list of events that this user has responded to
	 * @since 2.4
	 * @param type $forceUpdate 
	 */
	public function updateEventList( $forceUpdate = false )
	{
		$my = CFactory::getUser();
		if( $my->id == $this->id || $forceUpdate)
		{
			$eventModel = CFactory::getModel( 'events' );
			$eventsIds = $eventModel->getEventIds($this->id);
			$eventsIds = implode(',' , $eventsIds);

			if( $this->_events != $eventsIds){
				$this->_events = $eventsIds;
				$this->save();
			}
		}
	}

	/**
	 * Return current user status
	 * @return	string	user status
	 */
	public function getStatus( $rawFormat = false )
	{
		jimport( 'joomla.filesystem.file' );

		// @rule: If user requested for a raw format, we should pass back the raw status.
		$statusmodel				= CFactory::getModel('status');
		$statusmodel				= $statusmodel->get($this->_userid);
		$status			 			= $statusmodel->status;
		if( $rawFormat )
		{
			return $status;
		}
   
		// @rule: We need to escape any unwanted stuffs here before proceeding.
              
                CFactory::load( 'helpers' , 'string' );
                $status	=   CStringHelper::escape( $status );

		if(JFile::Exists(CPluginHelper::getPluginURI('community','wordfilter').DS.'wordfilter.php') && JPluginHelper::isEnabled('community', 'wordfilter'))
		{
			require_once( CPluginHelper::getPluginURI('community','wordfilter').DS.'wordfilter.php' );
			if(class_exists('plgCommunityWordfilter'))
			{
				$dispatcher = & JDispatcher::getInstance();
				$plugin 	=& JPluginHelper::getPlugin('community', 'wordfilter');
				$instance 	= new plgCommunityWordfilter($dispatcher, (array)($plugin));
			}
			$status		= $instance->_censor( $status );
		}

		// @rule: Create proper line breaks.
		$status	= CStringHelper::nl2br( $status );

            
               // @rule: Auto link statuses
               CFactory::load( 'helpers' , 'linkgenerator' );
               $status	= CLinkGeneratorHelper::replaceURL( $status );


		return $status;
	}

	public function getViewCount(){
		return $this->_view;
	}

	/**
	 * Returns the storage method for the particular user.
	 * It allows the remote storage to be able to identify which storage
	 * the user is currently on.
	 *
	 * @param	none
	 * @return	String	The storage method. 'file' or 'remote'
	 **/
	public function getStorage(){
		return $this->_storage;
	}

	/**
	 * Return the html formatted tooltip
	 */
	public function getTooltip()
	{
		if(!$this->_tooltip)
		{
			$this->_tooltip = $this->getDisplayName();
			//require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'tooltip.php' );
			//$this->_tooltip =  cAvatarTooltip($this);
		}
		return $this->_tooltip;
	}

	/**
	 *
	 */
	public function getKarmaPoint() {
		return $this->_points;
	}

	/**
	 * Return the the name for display, either username of name based on backend config
	 */
	public function getDisplayName( $rawFormat = false )
	{
		$config = CFactory::getConfig();
		$nameField = $config->getString('displayname');


		if( $rawFormat )
		{
			return $this->$nameField;
		}

		CFactory::load( 'helpers' , 'string' );

		return CStringHelper::escape( $this->$nameField );
	}

	/**
	 * Retrieve the current timezone the user is at.
	 *
	 * @return	int The current offset the user is located.
	 **/
	public function getTimezone()
	{
		$mainframe	=& JFactory::getApplication();
		$config		= CFactory::getConfig();
		$timezone	= $mainframe->getCfg('offset');
		$my			=& JFactory::getUser();

		if(!empty($my->params))
		{
			$timezone	= $my->getParam('timezone', $timezone );
		}

		return $timezone;
	}

	/**
	 * Return current user UTC offset
	 */
	public function getUtcOffset()
	{

		if (!C_JOOMLA_15) {
			//J1.6 handle timezone by its framework
			return true;
		}
		$mainframe	=& JFactory::getApplication();
		$config		= CFactory::getConfig();

		$timeZoneOffset = $mainframe->getCfg('offset');
		$dstOffset		= $config->get('daylightsavingoffset');

		$my		=& JFactory::getUser();

		if(!empty($my->params))
		{
			$timeZoneOffset = $my->getParam('timezone', $timeZoneOffset);

			$myParams	= $this->getParams();
			$dstOffset	= $myParams->get('daylightsavingoffset', $dstOffset);
		}
		return ($timeZoneOffset + $dstOffset);
	}

	/**
	 * Return the count of the friends
	 **/
	public function getFriendCount()
	{
		/*if(!empty($this->_friends)){
			$numfriend = count(explode(',', $this->_friends));
		}else{
			$numfriend = 0;
		}


		// if the count is not the same, the friend list might be out of sync
		// update it (which will also update _friendcount)
		if($numfriend != $this->_friendcount){
		    $this->updateFriendList();
		}*/
		
		$my = CFactory::getUser();
		if( $my->id == $this->id )
		{
			#correct the friendcount here because blocked user are still counted in "friendcount"
			$model	= CFactory::getModel( 'friends' );
			$realfriendcount = $model->getFriendsCount($this->id);
			$this->_friendcount = $realfriendcount;
		}

		return $this->_friendcount;
	}
	
	/**
	 * Return array of ids of friends
	 */
	public function getFriendIds()
	{
		// If we're the current user, the $this->friends would have
		// the updated friend list. updated during init. Just return them
		return explode(',',$this->_friends);
	}


	/**
	 * Return path to avatar image
	 **/
	public function getAvatar()
	{
		// @rule: Check if the current user's watermark matches the current system's watermark.
		$multiprofile	=& JTable::getInstance( 'MultiProfile' , 'CTable' );
		$match			= $multiprofile->isHashMatched( $this->_profile_id , $this->_watermark_hash );

		if( !$match )
		{
			// @rule: Since the admin may have changed the watermark for the specific user profile type, we need to also update
			// the user's watermark as well.
			CFactory::load( 'helpers' , 'image' );
			$hashName	= CImageHelper::getHashName( $this->id . time() );

			$multiprofile->updateUserAvatar( $this , $hashName );
			$multiprofile->updateUserThumb( $this , $hashName );
		}

		if( JString::stristr($this->_avatar, 'default.jpg') )
		{
			$this->_avatar = '';
		}

		// For user avatars that are stored in a remote location, we should return the proper path.
		// @rule: For default avatars and watermark avatars we don't want to alter the url behavior.
		// as it should be stored locally.
		if( $this->_storage != 'file' && !empty($this->_avatar) && JString::stristr( $this->_avatar , 'images/watermarks' ) === false)
		{
			$storage = CStorage::getStorage($this->_storage);
			return $storage->getURI( $this->_avatar );
		}


		CFactory::load('helpers', 'url');
		$avatar = CUrlHelper::avatarURI($this->_avatar, 'user.png');

		return $avatar;
	}

	/**
	 * Return path to thumb image
	 */
	public function getThumbAvatar()
	{
		// @rule: Check if the current user's watermark matches the current system's watermark.
		$multiprofile	=& JTable::getInstance( 'MultiProfile' , 'CTable' );
		$match			= $multiprofile->isHashMatched( $this->_profile_id , $this->_watermark_hash );

		if( !$match )
		{
			// @rule: Since the admin may have changed the watermark for the specific user profile type, we need to also update
			// the user's watermark as well.
			CFactory::load( 'helpers' , 'image' );
			$hashName	= CImageHelper::getHashName( $this->id . time() );

			$multiprofile->updateUserAvatar( $this , $hashName );
			$multiprofile->updateUserThumb( $this , $hashName );
		}

		if(JString::stristr($this->_thumb, 'default_thumb.jpg') )
		{
			$this->_thumb = '';
		}

		// For user avatars that are stored in a remote location, we should return the proper path.
		// @rule: For default avatars and watermark avatars we don't want to alter the url behavior.
		// as it should be stored locally.
		if( $this->_storage != 'file' && !empty($this->_thumb) && JString::stristr( $this->_thumb , 'images/watermarks' ) === false )
		{
			$storage = CStorage::getStorage($this->_storage);
			return $storage->getURI( $this->_thumb );
		}


		CFactory::load('helpers', 'url');
		$thumb = CUrlHelper::avatarURI($this->_thumb, 'user_thumb.png');

		return $thumb;
	}

	/**
	 * Return the custom profile data based on the given field code
	 *
	 * @param	string	$fieldCode	The field code that is given for the specific field.
	 */
	public function getInfo( $fieldCode )
	{
		// Run Query to return 1 value
		$db		=& JFactory::getDBO();
		$my		= CFactory::getUser();

		$query	= 'SELECT b.* FROM ' . $db->nameQuote( '#__community_fields' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__community_fields_values' ) . ' AS b '
				. 'ON b.' . $db->nameQuote( 'field_id' ) . '=a.' . $db->nameQuote( 'id' ) . ' '
				. 'AND b.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'INNER JOIN ' . $db->nameQuote( '#__community_users' ) . ' AS c '
				. 'ON c.' . $db->nameQuote( 'userid' ) . '= b.' . $db->nameQuote( 'user_id' ) . ' '
				. 'LEFT JOIN ' . $db->nameQuote( '#__community_profiles_fields' ) . ' AS d '
				. 'ON c.' . $db->nameQuote( 'profile_id' ) . ' = d.' . $db->nameQuote( 'parent' ) . ' '
				. 'AND d.' . $db->nameQuote( 'field_id' ) . ' = b.' . $db->nameQuote( 'field_id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'fieldcode' ) . ' =' . $db->Quote( $fieldCode );

		$db->setQuery( $query );
		$result	= $db->loadObject();

		$field	=& JTable::getInstance( 'FieldValue' , 'CTable' );
		$field->bind( $result );

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		$config	= CFactory::getConfig();

		// @rule: Only trigger 3rd party apps whenever they override extendeduserinfo configs
		if( $config->getBool( 'extendeduserinfo' ) )
		{
			CFactory::load( 'libraries' , 'apps' );
			$apps	= CAppPlugins::getInstance();
			$apps->loadApplications();

			$params		= array();
			$params[]	= $fieldCode;
			$params[]	=& $field->value;

			$apps->triggerEvent( 'onGetUserInfo' , $params );
		}

		// Respect privacy settings.
		CFactory::load( 'libraries' , 'privacy' );
		if( !CPrivacy::isAccessAllowed( $my->id , $this->id , 'custom' , $field->access ) )
		{
			return false;
		}

		return $field->value;
	}

	/**
	 * Counterpart of CUser::getInfo. Sets a specific field's value given the field code.
	 *
	 * @param	string	$fieldCode	The field code.
	 * @return	boolean	True on success false otherwise
	 **/
	public function setInfo( $fieldCode , $value )
	{
		$model	= CFactory::getModel( 'Profile' );

		return $model->updateUserData( $fieldCode , $this->id , $value );
	}

	/**
	 * Return the given user's address string
	 * We will build the address using known FIELD CODE
	 * FIELD_STREET, FIELD_CITY, FIELD_STATE, FIELD_ZIPCODE, FIELD_COUNTRY,
	 * If it is not defined, we just skip it
	 */
	public function getAddress()
	{

		$config	    =	CFactory::getConfig();
		$fields	    =	$config->get('user_address_fields');

		$fieldsInArray	= explode( ',', $fields );
		$address    =	'';

		foreach ( $fieldsInArray as $key => $value) {
		    $info	=   $this->getInfo( $value );
		    $address	.=  ( !empty( $info ) ) ? "$info,": '';
		}

		// Trim
		$address = JString::trim($address, " ,");

		return $address;
	}

	/**
	 * Return path to avatar image
	 **/
	private function _getLargeAvatar()
	{
		//@compatibility: For older releases
		// can be removed at a later time.
		if( empty( $this->_avatar ) && ($this->id != 0) )
		{

			// Copy old data.
			$model	= CFactory::getModel( 'avatar' );

			// Save new data.
			$this->_avatar	= $model->getLargeImg( $this->id );

			// We only want the relative path , specific fix for http://dev.jomsocial.com
			$this->_avatar	= str_ireplace( JURI::base() , '' , $this->_avatar );

			// Test if this is default image as we dont want to save default image
			if( stristr( $this->_avatar , 'default.jpg' ) === FALSE )
			{
				$userModel	= CFactory::getModel( 'user' );

				// Fix the .jpg_thumb for older release
				$userModel->setImage( $this->id , $this->_avatar , 'avatar' );
			}
			else
			{
				return $this->_avatar;
			}
		}

		return $this->_avatar;
	}

	private function _getMediumAvatar()
	{
		//@compatibility: For older releases
		// can be removed at a later time.
		if( empty( $this->_thumb ) && ($this->id != 0) )
		{
			// default guest image.

			// Copy old data.
			$model	= CFactory::getModel( 'avatar' );

			// Save new data.
			$this->_thumb	= $model->getSmallImg( $this->id );

			// We only want the relative path , specific fix for http://dev.jomsocial.com
			$this->_thumb	= str_ireplace( JURI::base() , '' , $this->_thumb );

			// Test if this is default image as we dont want to save default image
			if( stristr( $this->_thumb , 'default_thumb.jpg' ) === FALSE )
			{
				$userModel	= CFactory::getModel( 'user' );

				// Fix the .jpg_thumb for older release
				$userModel->setImage( $this->id , $this->_thumb , 'thumb' );
			}

		}

		return $this->_thumb;
	}
	/**
	 * Return the combined params of JUser and CUser
	 * @return	JParameter
	 */
	public function getParams()
	{
		return $this->_cparams;
	}

	public function isOnline() {
		return ($this->_isonline != null);
	}

	/**
	 * Check if the user is blocked
	 *
	 * @param	null
	 * return	boolean True user is blocked
	 */
	public function isBlocked()
	{
		return ( $this->block == '1' );
	}

	/**
	 * Determines whether the current user is allowed to create groups
	 *
	 * @param	none
	 * @return	Boolean		True if user is allowed and false otherwise.
	 **/
	public function canCreateGroups()
	{
		// @rule: If user registered prior to version 2.0.1, there is no multiprofile setup.
		// We need to ensure that they are still allowed to create groups.
		if( $this->getProfileType() == COMMUNITY_DEFAULT_PROFILE )
		{
			return true;
		}

		$profile	=& JTable::getInstance( 'MultiProfile' , 'CTable' );
		$profile->load( $this->getProfileType() );

		return ( bool ) $profile->create_groups;
	}
	
	/**
	 * Return stored number set by setCount, kept within params
	 * @param string $name 
	 */
	public function count($name)
	{
		return $this->_cparams->get($name, 0);
	}
	
	/**
	 * Generic method to store a number in params
	 * @param type $name
	 * @param type $value 
	 */
	public function setCount($name, $value)
	{
		$this->_cparams->set($name, $value);
		$this->save();
	}

	/**
	 * Determines whether the current user is allowed to create events
	 *
	 * @param	none
	 * @return	Boolean		True if user is allowed and false otherwise.
	 **/
	public function canCreateEvents()
	{
		// @rule: If user registered prior to version 2.0.1, there is no multiprofile setup.
		// We need to ensure that they are still allowed to create events.
		if( $this->getProfileType() == COMMUNITY_DEFAULT_PROFILE )
		{
			return true;
		}

		$profile	=& JTable::getInstance( 'MultiProfile' , 'CTable' );
		$profile->load( $this->getProfileType() );

		return ( bool ) $profile->create_events;
	}

	/**
	 * Increment view count.
	 * Only increment the view count if the view is from a different session
	 */
	public function viewHit(){

		$session =& JFactory::getSession();
		if( $session->get('view-'. $this->id, false) == false ) {

			$db		=& JFactory::getDBO();
			$query = 'UPDATE ' . $db->nameQuote('#__community_users')
			. ' SET ' . $db->nameQuote('view') .' = ( ' . $db->nameQuote('view') .' + 1 )'
			. ' WHERE ' . $db->nameQuote('userid') .'=' . $db->Quote($this->id);
			$db->setQuery( $query );
			$db->query();
			$this->_view++;
		}

		$session->set('view-'. $this->id, true);
	}

	/**
	 * Store the user data.
	 * @params	string group	If specified, jus save the params for the given
	 * 							group
	 */
	public function save( $group ='' )
	{
		parent::save();

		// Store our own data
		$obj = new stdClass();

		$obj->userid    	= $this->id;
		$obj->status    	= $this->_status;
		$obj->points    	= $this->_points;
		$obj->posted_on 	= $this->_posted_on;
		$obj->avatar    	= $this->_avatar;
		$obj->thumb     	= $this->_thumb;
		$obj->invite    	= $this->_invite;
		$obj->alias			= $this->_alias;
		$obj->params		= $this->_cparams->toString();
		$obj->profile_id	= $this->_profile_id;
		$obj->storage		= $this->_storage;
		$obj->watermark_hash	= $this->_watermark_hash;
		$obj->search_email		= $this->_search_email;
		$obj->friends			= $this->_friends;
		$obj->groups			= $this->_groups;
		$obj->events			= $this->_events;

		$model = CFactory::getModel('user');
		return $model->updateUser($obj);
	}

	/**
	 * Return the profile type the user is currently on
	 *
	 * @return	int	The current profile type the user is in.
	 */
	public function getProfileType()
	{
		static $profileType	= false;

		if( $profileType === false )
		{
			$profile	=& JTable::getInstance( 'MultiProfile' , 'CTable' );
			$profile->load( $this->_profile_id );
			$config		= CFactory::getConfig();

			if( !$profile->published || !$config->get('profile_multiprofile') )
			{
				$profileType	= 0;
			}
			else
			{
				$profileType	= $profile->id;
			}
		}

		return $profileType;
	}

	/**
	 * Sets the status for the current user
	 **/
	public function setStatus( $status = '' )
	{
		if( $this->id != 0 )
		{
			$this->set( '_status' , $status );
			$this->save();
		}
	}


	/** Interface fucntions **/


	public function resolveLocation($address)
	{
		CFactory::load('libraries', 'mapping');
		$data = CMapping::getAddressData($address);
		//print_r($data);
		if($data){
			if($data->status == 'OK')
			{
				$this->latitude  = $data->results[0]->geometry->location->lat;
				$this->longitude = $data->results[0]->geometry->location->lng;
			}
		}
	}

	/**
	 * Method to check CUser object authorisation against an access control
	 *
	 * @param	string	$action		The name of the action to check for permission.
	 * @param	string	$assetname	The name of the asset on which to perform the action.
	 *
	 * @return	boolean	True if authorised
	 * @since	Jomsocial 2.4
	 */
	public function authorise($action, $assetname = null, $assetObject = null)
	{
			
		// Check is similar call has been made before. 
		if(is_string($assetname) && isset($_cacheAction[$action.$assetname])){
			return $_cacheAction[$action.$assetname];
		}
		
		$access = CAccess::check($this->_userid, $action, $assetname, $assetObject);
		$_cacheAction[$action.$assetname] = $access;

		// If asset not found , get Joomla authorise.
		if ($access === null && method_exists('Juser', 'authorise')) {
			return parent::authorise($action, $assetname);
		}

		return $access;
    }

	/**
	 * Method to return authentication error msg
	 *
	 * @return	string error message
	 * @since	Jomsocial 2.4
	 */
	public function authoriseErrorMsg()
	{
		return CACCESS::getError();
	}

}
?>
