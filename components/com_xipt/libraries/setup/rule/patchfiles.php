<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptSetupRulePatchfiles extends XiptSetupBase
{
	function isRequired()
	{
		$modelPatch   = self::isModelFilePatchRequired();
		$userPatch    = self::isAdminUserModelPatchRequired();
		$xmlPatch     = self::isXMLFilePatchRequired();
		$libraryField = self::isCustomLibraryFieldRequired();

		return ($modelPatch || $userPatch || $xmlPatch || $libraryField);
	}
	
	function doApply()
	{
		if(self::isModelFilePatchRequired())
		{
    		$filename = JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'models'.DS.'profile.php';
    		
	    	//	create a backup file first
    	    if(!JFile::copy($filename, $filename.'.jxibak'))
    	    	return XiptText::_("NOT_ABLE_TO_CREATE_A_BACKUP_FILE_CHECK_PERMISSION");
    	    	 
    		//1. Replace _ fields calling in _loadAllFields function
	    	$funcName = 'function _loadAllFields';
	    	
	    	$searchString = '$fields = $db->loadObjectList();';
	    	ob_start();
	    	?>$fields = $db->loadObjectList();
	    	
	    	/*==============HACK TO RUN JSPT CORRECTLY :START ============================*/
	    	require_once(JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php');
	    	$pluginHandler= XiptFactory::getPluginHandler();
	    	$userId = 0;
	    	$pluginHandler->onProfileLoad($userId, $fields, __FUNCTION__);
	    	/*==============HACK TO RUN JSPT CORRECTLY : DONE ============================*/
	        <?php  
	    	$replaceString = ob_get_contents();
	        ob_end_clean();
	        
	        $success = self::patchData($searchString,$replaceString,$filename,$funcName);
	        
	        //2. Replace data in getViewableProfile fn
	        $funcName =  'function getViewableProfile';
	        $searchString = '$result	= $db->loadAssocList();';
	    	ob_start();
	    	?>$result	= $db->loadAssocList();
	    	
	    	/*==============HACK TO RUN JSPT CORRECTLY :START ============================*/
			require_once(JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php');
		    $pluginHandler= XiptFactory::getPluginHandler();
		    $pluginHandler->onProfileLoad($userId, $result, __FUNCTION__);
		    /*==============HACK TO RUN JSPT CORRECTLY : DONE ============================*/
	        <?php 
	    	$replaceString = ob_get_contents();
	        ob_end_clean();
	        
	        $success = self::patchData($searchString,$replaceString,$filename,$funcName);
	        
	        
	        //3. Replace data in getEditablePRofile function
	        $funcName =  'function getEditableProfile';
	        $success = self::patchData($searchString,$replaceString,$filename,$funcName);
	        
    	}

    	// we need to patch Model:User in backend also for editing
    	if(self::isAdminUserModelPatchRequired()){
    		$filename = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_community'.DS.'models'.DS.'users.php';
    		
    		//	create a backup file first
    	    if(!JFile::copy($filename, $filename.'.jxibak'))
    	    	return XiptText::_("NOT_ABLE_TO_CREATE_A_BACKUP_FILE_CHECK_PERMISSION");
    		
	    	$funcName = 'function getEditableProfile($userId	= null)';
	    	
	    	$searchString = '$result	= $db->loadAssocList();';
	    	ob_start();
	    	?>$result	= $db->loadAssocList();
	    	
	    	/*==============HACK TO RUN JSPT CORRECTLY :START ============================*/
			require_once(JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php');
		    $pluginHandler= XiptFactory::getPluginHandler();
		    $pluginHandler->onProfileLoad($userId, $result, __FUNCTION__);
		    /*==============HACK TO RUN JSPT CORRECTLY : DONE ============================*/
	        <?php 
	    	$replaceString = ob_get_contents();
	        ob_end_clean();
	        
	        $success = self::patchData($searchString,$replaceString,$filename,$funcName);
    	}
        
        //now check library field exist
        if(self::isCustomLibraryFieldRequired()){
        	//copy library field files into community // libraries // fields folder
        	self::copyLibraryfiles();
        }
        
        
        //now check XML File patch required
        if(self::isXMLFilePatchRequired()) {
        	//give patch data fn file to patch
        	$filename	= JPATH_ROOT . DS. 'components' . DS . 'com_community'
        					.DS.'libraries'.DS.'fields'.DS.'customfields.xml';
        	if (JFile::exists($filename)) {
		
				if(!is_readable($filename)) 
					XiptError::raiseWarning(sprintf(XiptText::_('FILE_IS_NOT_READABLE_PLEASE_CHECK_PERMISSION'),$filename));
				
				$file =JFile::read($filename);				
			    $searchString = '</fields>';
		    	ob_start();
		    	?><field>
		    	<type>profiletypes</type>
		    	<name>Profiletypes</name>
		    	</field>
				<field>
					<type>templates</type>
					<name>Templates</name>
				</field>
				</fields><?php 
		        
		        $replaceString = ob_get_contents();
		        $file = str_replace($searchString,$replaceString,$file);
		        
	        	// create a backup file first
	    	    if(!JFile::copy($filename, $filename.'.jxibak'))
	    	    	return XiptText::_("NOT_ABLE_TO_CREATE_A_BACKUP_FILE_CHECK_PERMISSION");

	    	    JFile::write($filename,$file);
	        	 	
        	}
        }
     
        return XiptText::_('FILES_PATCHED_SUCCESSFULLY');
	}
	
	function doRevert()
	{
		$filestoreplace = $this->_getJSPTFileList();
	   
		if($filestoreplace) 
		foreach($filestoreplace AS $sourceFile => $targetFile)
		{
			$targetFileBackup = $targetFile.'.jxibak';
			// delete this file
			// Only delete if you have backup copy
			if(JFile::exists($targetFile) && JFile::exists($targetFileBackup))
			{
				JFile::delete($targetFile);
				JFile::move($targetFileBackup,$targetFile) || XiptError::raiseError('XIPT-UNINSTALL-ERROR','Not able to restore backup : '.__LINE__) ;
			}		
		}
	}
	
	//$funcName name contain in which fn we want to replace datas
	function patchData($searchString,$replaceString,$filename,$funcName)
	{
		
		if (JFile::exists($filename)) {
		
			if(!is_readable($filename)) 
				XiptError::raiseWarning(sprintf(XiptText::_('FILE_IS_NOT_READABLE_PLEASE_CHECK_PERMISSION'),$filename));
			
			$file = JFile::read($filename);
			if(!$file)
				return false;
			
	    	$fileParts = explode($funcName, $file);
    	    
	    	if(count($fileParts) >= 2) {
	    	    $firstPos = strpos($fileParts[1],$searchString);
	    	    $beforeStr = substr($fileParts[1],0,$firstPos);
	    	    $afterStr = substr($fileParts[1],$firstPos+strlen($searchString));
	    	    $fileParts[1]=$beforeStr . $replaceString . $afterStr;
	    	    $file = $fileParts[0].$funcName.$fileParts[1];
	    	    JFile::write($filename,$file);
	    	    return true;
	    	}
		}
		return false;
	}
	
	function isModelFilePatchRequired()
	{
		$filename = JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'models'.DS.'profile.php';
		if (JFile::exists($filename)) {
			
			if(!is_readable($filename)) 
				XiptError::raiseWarning(sprintf(XiptText::_('FILE_IS_NOT_READABLE_PLEASE_CHECK_PERMISSION'),$filename));
			
			$file = JFile::read($filename);
			
			$searchString = '$pluginHandler= XiptFactory::getPluginHandler()';
			$count = substr_count($file,$searchString);
			if($count >= 3)
				return false;
				
			return true;
		}	
		return false;
	}
	
	function isAdminUserModelPatchRequired()
	{
		// no need to pacth the admin user model in jspt 2.0
		if(JString::stristr(XiptHelperJomsocial::get_js_version(),"2.")) return false;

		// we need to patch User Model
		$filename = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_community'.DS.'models'.DS.'users.php';
		if (JFile::exists($filename)) {
			
			if(!is_readable($filename)) 
				XiptError::raiseWarning(sprintf(XiptText::_('FILE_IS_NOT_READABLE_PLEASE_CHECK_PERMISSION'),$filename));
			
			$file =JFile::read($filename);
			
			$searchString = '$pluginHandler->onProfileLoad($userId, $result, __FUNCTION__);';
			$count = substr_count($file,$searchString);
			if($count >= 1)
				return false;
				
			return true;
		}	
		return false;
	}
	
	function isXMLFilePatchRequired()
	{
		$filename	= JPATH_ROOT . DS. 'components' . DS . 'com_community'.DS.'libraries'.DS.'fields'.DS.'customfields.xml';
		if (JFile::exists($filename)) {
			
			if(!is_readable($filename)) 
				XiptError::raiseWarning(sprintf(XiptText::_('FILE_IS_NOT_READABLE_PLEASE_CHECK_PERMISSION'),$filename));
			
			$file = JFile::read($filename);
			
			if(!$file)
				return false;
				
			$searchString = PROFILETYPE_FIELD_TYPE_NAME;
			$count = substr_count($file,$searchString);
			if($count >= 1)
				return false;
				
			return true;
		}	
		return false;
	}
	
	function isCustomLibraryFieldRequired()
	{
		$pFileName 	 = JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'fields'.DS.PROFILETYPE_FIELD_TYPE_NAME.'.php';
		$pXiFileName = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_xipt'.DS.'hacks'.DS.'front_libraries_fields_profiletypes.php';
		$pLibrary    = self::deleteJSOldFile($pFileName,$pXiFileName);
		
		$tFileName = JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'fields'.DS.TEMPLATE_FIELD_TYPE_NAME.'.php';
    	$tXiFileName = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_xipt'.DS.'hacks'.DS.'front_libraries_fields_templates.php';
		$tLibrary = self::deleteJSOldFile($tFileName,$tXiFileName);

		if($pLibrary && $tLibrary)
			return false;

    	return true;
	}
	
	function deleteJSOldFile($file1,$file2){
		if(!JFile::exists($file1))
			return false;
			
		$md5JsPFile = md5(JFile::read($file1));
		$md5XiPFile = md5(JFile::read($file2));
		if($md5JsPFile != $md5XiPFile){
			JFile::delete($file1);
			return false;
		}
		
		return true;
	}
	
	function copyLibraryFiles()
	{
		$XIPT_PATH_ADMIN	  = JPATH_ROOT .DS. 'administrator' .DS.'components' . DS . 'com_xipt';
	
		$COMMUNITY_PATH_FRNTEND = JPATH_ROOT .DS. 'components' . DS . 'com_community';
		
		$sourceFile = $XIPT_PATH_ADMIN.DS.'hacks'.DS.'front_libraries_fields_profiletypes.php';
		$targetFile = $COMMUNITY_PATH_FRNTEND.DS.'libraries'.DS.'fields'.DS.'profiletypes.php';
		JFile::copy($sourceFile, $targetFile) || XiptError::raiseError('INSTERR', "Not able to copy file ".$sourceFile ." to ".$targetFile) ;
		
		$sourceFile = $XIPT_PATH_ADMIN.DS.'hacks'.DS.'front_libraries_fields_templates.php';
		$targetFile = $COMMUNITY_PATH_FRNTEND.DS.'libraries'.DS.'fields'.DS.'templates.php';
		JFile::copy($sourceFile, $targetFile) || XiptError::raiseError('INSTERR', "Not able to copy file ".$sourceFile ." to ".$targetFile) ;
		return;
	}
	
	function getMessage()
	{
		$requiredSetup = array();
		if($this->isRequired())
		{
			$link = XiptRoute::_("index.php?option=com_xipt&view=setup&task=doApply&name=patchfiles",false);
			$requiredSetup['message']  = '<a href="'.$link.'">'.XiptText::_("PLEASE_CLICK_HERE_TO_PATCH_FILES").'</a>';
			$requiredSetup['done']  = false;
		}
		
		else
		{
			$requiredSetup['message']  = XiptText::_("FILES_ARE_PATCHED");
			$requiredSetup['done']  = true;
		}
		return $requiredSetup;
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
}