<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die();

jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
require_once JPATH_ROOT.DS.'components'.DS.'com_xipt' .DS. 'includes.php';
class XiptHelperInstall
{
	function show_instruction()
	{
		$siteURL  = JURI::base();
		if(strstr($siteURL,'localhost')==false)
			$siteURL  = JURI::base();
	}

	function copyAECfiles()
	{
		$miFilename = JPATH_ROOT.DS.'components'.DS.'com_acctexp'.DS.'micro_integration'.DS.'mi_jomsocialjspt.php';
		$sourceMIFilename = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_xipt'.DS.'hacks'.DS.'mi_jomsocialjspt.php';

		if(JFile::exists($miFilename))
			return JFile::copy($sourceMIFilename , $miFilename);	
	
		return true;	
	}
	
	function _getJSPTFileList()
	{
		$CMP_PATH_FRNTEND = JPATH_ROOT .DS. 'components' . DS . 'com_community';
		$CMP_PATH_ADMIN	  = JPATH_ROOT .DS. 'administrator' .DS.'components' . DS . 'com_community';
		
		$filestoreplace = array();
	
		$filestoreplace['front_libraries_fields_customfields.xml']=$CMP_PATH_FRNTEND.DS.'libraries'.DS.'fields'.DS.'customfields.xml';
		$filestoreplace['front_models_profile.php']=$CMP_PATH_FRNTEND.DS.'models'.DS.'profile.php';
		$filestoreplace['admin_models_user.php']=$CMP_PATH_ADMIN.DS.'models'.DS.'users.php';
		
		//Codrev : disable plugins and fields too
		//AEC microintegration install, if AEC exist
		$AEC_MI_PATH = JPATH_ROOT . DS. 'components' . DS . 'com_acctexp' . DS . 'micro_integration';
		if(JFolder::exists($AEC_MI_PATH))
			$filestoreplace['mi_jomsocialjspt.php']=	$AEC_MI_PATH .DS.'mi_jomsocialjspt.php';
	
		return $filestoreplace;
	}

	function check_jomsocial_existance()
	{
		$jomsocial_admin = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_community';
		$jomsocial_front = JPATH_ROOT . DS . 'components' . DS . 'com_community';
	
		if(!is_dir($jomsocial_admin))
			return false;
	
		if(!is_dir($jomsocial_front))
			return false;
	
		return true;
	}

	function installExtensions($extPath=null)
	{
		//if no path defined, use default path
		if($extPath==null)
			$extPath = dirname(__FILE__).DS.'extensions';

		if(!JFolder::exists($extPath))
			return false;
	
		$extensions	= JFolder::folders($extPath);
	
		//no apps there to install
		if(empty($extensions))
			return true;

		//get instance of installer
		$installer =  new JInstaller();
		$installer->setOverwrite(true);

		//install all apps
		foreach ($extensions as $ext)
		{
			$msg = "Supportive Plugin/Module $ext Installed Successfully";

			// Install the packages
			if($installer->install($extPath.DS.$ext)==false)
				$msg = "Supportive Plugin/Module $ext Installation Failed";

			//enque the message
			JFactory::getApplication()->enqueueMessage($msg);
		}

		return true;
	}	

	function changePluginState($pluginname, $action=1)
	{
	  
		$db		= JFactory::getDBO();
		if (XIPT_JOOMLA_15){
			$query	= 'UPDATE ' . $db->nameQuote( '#__plugins' )
					. ' SET '.$db->nameQuote('published').'='.$db->Quote($action)
			  		.' WHERE '. $db->nameQuote('element').'='.$db->Quote($pluginname);
		}
		else{
			$query	= 'UPDATE ' . $db->nameQuote( '#__extensions' )
					. ' SET '.$db->nameQuote('enabled').'='.$db->Quote($action)
			  		.' WHERE '. $db->nameQuote('element').'='.$db->Quote($pluginname) . "  AND `type`='plugin' ";
		}
	
		$db->setQuery($query);		
	
		if(!$db->query())
			return false;
		
		return true;
	}

	function setup_database()
	{
		//migrate from 2.2 to 3.0
	    $migrationRequired22to30 = self::_isMigrationRequired22to30();
		if($migrationRequired22to30 && self::_migration22to30() == false)
			return false;
		
		self::_migration460();
		// this will modify group field datatype varchar to text in xipt_profiletype
		self::modifyGroupDataType();

		return true;
	}

	function _isMigrationRequired22to30()
	{
		if(self::_isTableExist('xipt_profiletypes')) {
			if(!self::_check_column_datatype('#__xipt_profiletypes','group','int(11)')  
				|| self::_check_column_datatype('#__xipt_profiletypes','privacy','text')
				|| !self::_check_column_exist('#__xipt_profiletypes','config')) 
				return true;
		}
		return false;	
	}

	function _migration22to30()
	 {
	    $allQueries	= array();
	    $allQueries[]='ALTER TABLE `#__xipt_profiletypes`
		            MODIFY `group`   varchar(100) not null';
	    
	    $allQueries[]='ALTER TABLE `#__xipt_profiletypes`
		            MODIFY `privacy`   text not null';
	    
		            
	    $db	= JFactory::getDBO();
		foreach ( $allQueries as $query){

			$db->setQuery($query);
			$db->query();

			if($db->getErrorNum()){
				JError::raiseError(__CLASS__.'.'.__LINE__, $db->stderr());
				return false;
			}
		}

		//add column config in xipt_profiletypes table and insert data from xipt_settings table.
		self::_migrateRegistrationConfig();
		
		// also migrate privacy in xipt_profiletype
		self::_migratePrivacyParams();
		return true;
	}

	function _migrateRegistrationConfig()
	{
		// add config column in profiletype table
		self::_add_column('config' , 'text NOT NULL', '#__xipt_profiletypes');
		
		// get the parameters of registration email checks from settings table
		$db = JFactory::getDBO();
		$query = 'SELECT '. $db->nameQuote('params') .' FROM '. $db->nameQuote('#__xipt_settings')
					.' WHERE '. $db->nameQuote('name') .' = '.$db->Quote('settings');
		$db->setQuery($query);
		$result = $db->loadResult();
		
		// get the required params in array form
		$params = new XiptParameter();
		$params->bind($result);
		$regconfig['jspt_restrict_reg_check'] 	= $params->get('jspt_restrict_reg_check',0);
		$regconfig['jspt_prevent_username'] 	= $params->get('jspt_prevent_username', 'moderator; admin; support; owner; employee');
		$regconfig['jspt_allowed_email'] 		= $params->get('jspt_allowed_email', '');
		$regconfig['jspt_prevent_email'] 		= $params->get('jspt_prevent_email', '');		
				
		// convert email settings into INI
		$regParams = new XiptParameter();
		$regParams->loadArray($regconfig);
		$regINI = $regParams->toString('XiptINI');
		
		// update the profile types table foe column config
		$query = 'UPDATE '. $db->nameQuote('#__xipt_profiletypes')
					.' SET '. $db->nameQuote('config') .' = '.$db->Quote($regINI);
		$db->setQuery($query);
		$result = $db->query();
		
		// unsetthe email params which are not required for settings table
		$settingParamsArray = $params->toArray();
		unset($settingParamsArray['jspt_restrict_reg_check']);
		unset($settingParamsArray['jspt_prevent_username']);
		unset($settingParamsArray['jspt_allowed_email']);
		unset($settingParamsArray['jspt_prevent_email']);
		$settingParams = new XiptParameter();
		$settingParams->loadarray($settingParamsArray);
		$settingsINI = $settingParams->toString('XiptINI');		
		
		// save again the whole params (filtered by email params) in settings table
		$query = 'UPDATE '. $db->nameQuote('#__xipt_settings')
					.' SET '. $db->nameQuote('params') .' = '.$db->Quote($settingsINI);
		$db->setQuery($query);
		$result = $db->query();
	}
	
	function _migratePrivacyParams()
	{
	    	$db     = JFactory::getDBO();
			$query  = 'SELECT `id`,`privacy`,`params` FROM `#__xipt_profiletypes`';
			$db->setQuery($query);
			$profiletypeResult = $db->loadObjectList();
		   
			for($i=0;$i<count($profiletypeResult);$i++)
				{
					//get profiletype privacy from privacy column $ update it into params 
					$privacy = array();
					$privacy['privacyprofile'] =  self::_update_privacy_param( $profiletypeResult[$i]->privacy);
					 
					$JSParams = new XiptParameter($profiletypeResult[$i]->params);
					 //do not inclde profileprivacy
					$privacyKey=array('privacyfriends','privacyphotos','privacyemail','privacyapps','privacywallcomment');
				
					if(!empty($profiletypeResult[$i]->params))
					{
					 
						foreach ($privacyKey as $key)
						{  
							if($key !== 'privacyprofile' )
							{
								$privacy[$key] = $JSParams->get($key);
							}
						}				
					}	
				
	    	        $registry	= XiptParameter();
					$registry->loadArray($privacy,'xipt_privacyparams');
					$Privacyparams =  $registry->toString('XiptINI' , 'xipt_privacyparams' );
		    	   	 
					$profiletypeResult[$i]->privacy = $Privacyparams;
					  
					$query = 'UPDATE `#__xipt_profiletypes` ' 
					   			.' SET `privacy`='.$db->Quote($Privacyparams) 
					   		    .' WHERE `id`='.$db->Quote($profiletypeResult[$i]->id);
					   		    
					$db->setQuery($query);
					$db->query();
			 }
			 
	}
	    
	function _update_privacy_param($param)
	{  
			$filename = JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'defines.community.php';
		 		if(JFile::exists($filename))
				require_once( $filename );
				else
				require_once JPATH_ROOT.DS.'components'.DS.'com_xipt' .DS. 'defines.xipt.php';

			$what = $param;
		    switch ($what) 
		    {
		    	
					    case "public":
						$what = (!JFile::exists($filename)) ? XI_PRIVACY_PUBLIC :PRIVACY_PUBLIC;
						break;
					    case "members":
						$what = (!JFile::exists($filename)) ? XI_PRIVACY_MEMBERS :PRIVACY_MEMBERS;
						break;
					    case "friends":
					      $what = (!JFile::exists($filename)) ? XI_PRIVACY_FRIENDS :PRIVACY_FRIENDS;
						break;
		 
		    }
		    
		    return $what;
		    
	}

	function _add_column($name, $specstr, $tname)
	{
		$db		= JFactory::getDBO();
		$query	= 'SHOW COLUMNS FROM ' 
				  . $db->nameQuote($tname)
				  . ' LIKE \'%'.$name.'%\' ';
		$db->setQuery( $query );
		$columns	= $db->loadObjectList();
		if($db->getErrorNum())
		{
			JError::raiseError(__CLASS__.'.'.__LINE__, $db->stderr());
			return false;
		}

		if($columns == NULL || $columns[0] == NULL)
		{
			$query =' ALTER TABLE '. $db->nameQuote($tname) 
					. ' ADD COLUMN ' . $db->nameQuote($name)
					. ' ' . $specstr;
			$db->setQuery( $query );
			$db->query();
			return true;
		}
		return false;
	}

	function _check_column_datatype($tableName,$columnName,$datatype)
	{
		assert($tableName  != '');
		assert($columnName != '');

		$db	    = JFactory::getDBO();
		$query	=  'DESC ' 
			   . ' '.$db->nameQuote($tableName)
			   . ' '.$db->nameQuote($columnName);

		$db->setQuery( $query );
		$columns= $db->loadObjectList();
	
		if($columns[0]->Type === $datatype)
	 	   return false;	

		return true;
	}

	function _isTableExist($tableName)
	{
		$mainframe	=& JFactory::getApplication();

		$tables	= array();
	
		$database = JFactory::getDBO();
		$tables	  = $database->getTableList();

		return in_array( $mainframe->getCfg( 'dbprefix' ) . $tableName, $tables );
	}

	function _check_column_exist($tableName, $columnName)
	{
		assert($tableName  != '');
		assert($columnName != '');

		$db		= JFactory::getDBO();
		$query	=  'SHOW COLUMNS FROM ' 
			   . $db->nameQuote($tableName)
			   . ' LIKE \'%'.$columnName.'%\' ';

		$db->setQuery( $query );
		$columns = $db->loadObjectList();

		if($db->getErrorNum()){
			JError::raiseError(__CLASS__.'.'.__LINE__, $db->stderr());
			return false;
		}

		if($columns == NULL || $columns[0] == NULL){
			return false;
		}
		return true;
	}
	
	function _migration460()
	{				
		$ver = intval(self::getXiptVersion());
		//echo $ver;
		if( $ver > 460)
  			return false;
  		
  			echo $ver;
		$db = JFactory::getDBO();
		$query  = 'SELECT `id`, `coreparams` FROM `#__xipt_aclrules`';
		$db->setQuery($query);
		$aclrules = $db->loadObjectList();
	
		foreach($aclrules as $data){
			$registry	= new XiptParameter();
			
			$registry->loadINI($data->coreparams);
			$params = $registry->toArray();
			$params['core_display_message'] = base64_encode($params['core_display_message']);
	
			$registry->loadArray($params);
			$iniParamData	= $registry->toString('XiptINI');
			$query = 'UPDATE `#__xipt_aclrules`' 
					.' SET `coreparams`='.$db->Quote($iniParamData)
					.'WHERE '.$db->nameQuote('id').'='.$db->Quote($data->id).'';
	 		$db->setQuery($query);
			$db->query();
		}
		
		 return true;
	}
	
	function ensureXiptVersion()
	{
		
		if(self::getXiptVersion() != false)
			return true;
			
		$db		= JFactory::getDBO();
		$query  = ' INSERT INTO `#__xipt_settings` '
				.' ( '.$db->nameQuote('name'). ','.$db->nameQuote('params').') '
				.'VALUES 
				(' .$db->Quote('version').','.$db->Quote('3.0.460').')';
		
		$db->setQuery($query);
		return $db->query();
	}

	function updateXiptVersion()
	{
		require_once JPATH_ROOT .DS. 'components' .DS. 'com_xipt' .DS. 'defines.php';
		$db		= JFactory::getDBO();
		$query	= 'UPDATE #__xipt_settings'
				.' SET '. $db->nameQuote('params') .' = '.$db->Quote('3.2.695')
				.' WHERE '. $db->nameQuote('name') .' = '.$db->Quote('version');
		$db->setQuery($query);
		return $db->query();
	}
	
	
	function getXiptVersion()
	{
		$db		= JFactory::getDBO();
		$query  = " SELECT `params` FROM `#__xipt_settings` WHERE `name`='version' ";
		$db->setQuery($query);
		$result = $db->loadResult();
	
		if($result == 0)
			return false;
			
		list($main, $mid, $svn) = explode(".", $db->loadResult());
              // echo $svn;

		return $svn;
		
	}
	
	function modifyGroupDataType(){
		
		if(!self::_isTableExist('xipt_profiletypes'))
			return;
		
		if(self::_check_column_datatype('#__xipt_profiletypes','group','varchar(100)'))
			return;
				    
		$query	= 'ALTER TABLE `#__xipt_profiletypes` MODIFY `group` text not null';
		$db		=  JFactory::getDBO();
		$db->setQuery($query);
		$db->query();	
		
	}

	/**
	 * If You are used JS 2.2 or upper version 
	 * then XiPT3.1 change deafault value of avatar.
	 * (because JS2.2 also changed their dfafult avatar value)
	 */
	function changeDefaultAvatar() {
		// if table is not exist(It means, Not exist older version) then no need for maigration
		//because deafault value of avatar automatic set by "install.sql". 
		if(
			!self::_isTableExist('xipt_settings') &&
			!self::_isTableExist('xipt_profiletypes')
		)
		{
			return false;	
		}

		// get installed Xipt version
		//$myVersion = self::getXiptVersion();
		// If XiPT version greather than 608. then default path already added in sql file. 
		//if($myVersion > 608)
		//{ return false;	}

		// get avatar coloumn schema
		$avatarSchema=self::_getColumnStructure('#__xipt_profiletypes','avatar');
		
		// Apply migration
		if(JString::stristr($avatarSchema->Default,'components/com_community/assets/default.jpg'))
		{
			$query = "ALTER TABLE `#__xipt_profiletypes` MODIFY `avatar` varchar(250) Default 'components/com_community/assets/user.png' NOT NULL";
			$db = JFactory::getDBO();
			$db->setQuery($query);
			$db->query();
			return true;
		}
		return false;
	}
	
	/**
	 * return column structure
	 * @param unknown_type $tableName :: table name
	 * @param unknown_type $columnName:: column name
	 */
	function _getColumnStructure($tableName,$columnName)
	{
		assert($tableName  != '');
		assert($columnName != '');

		$db	    = JFactory::getDBO();
		$query	=  'DESC ' 
			   . ' '.$db->nameQuote($tableName)
			   . ' '.$db->nameQuote($columnName);

		$db->setQuery( $query );
		$columns= $db->loadObjectList();
	
		return $columns[0];
	}
}
