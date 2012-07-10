<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

//TODO: we should store
class XiptLibApps
{
    function filterCommunityApps(&$apps, $profiletype, $blockProfileApps=true)
    {
        $notAllowedApps = XiptLibApps::getNotAllowedCommunityAppsArray($profiletype);
        
        // $apps is array of objects
        for($i=0 ; $i < count($apps) ; $i++ )
        {
            $app   =& $apps[$i];
            
            //legacy plugins come as array, we dont work on them
            if(is_object($app) == false)
                continue;
            
            // we want to restrict only community apps and do not restrict our compo
            if(XIPT_JOOMLA_15){
	            if($app->_type != 'community' && $app->_name == 'xipt_community')
	                continue;
            }
            
            else{
	            if($app->get('_type') != 'community' && $app->get('_name') == 'xipt_community')
	                continue;
            }
            
			if(method_exists($app,'onProfileDisplay') != $blockProfileApps)
				continue;
			if(XIPT_JOOMLA_15)
				$appId    = XiptLibApps::getPluginId($app->_name);
			else
            	$appId    = XiptLibApps::getPluginId($app->get('_name'));
			
           // is it not allowed
           if(in_array($appId,$notAllowedApps))
               unset($apps[$i]);
        }
        
        $apps =& array_values($apps);
        return true;
    }
    
    function getNotAllowedCommunityAppsArray($profiletype)
    {
    	$tempResult = XiptFactory::getInstance('applications', 'model')
    								->loadRecords(0);		
				
		foreach($tempResult as $temp)
			$result[$temp->profiletype][] = $temp->applicationid;
		
		if(isset($result[$profiletype]))
			return $result[$profiletype];
		else
			return array();		
    }
    
	function getPluginId( $element, $folder = 'community' )
	{
		$reset = XiptLibJomsocial::cleanStaticCache();
		
		static $plugin = null;
		if($plugin == null || !isset($plugin[$folder]) || $reset)
		{			
			$query = new XiptQuery();
			if(XIPT_JOOMLA_15){
				$plugin[$folder] = $query->select('*')
							->from('#__plugins')
							->where(" `folder` = '$folder' ")
							->dbLoadQuery("","")
							->loadObjectList('element');
			}
			else{
				$plugin[$folder] = $query->select('*')
							->from('#__extensions')
							->where(" `folder` = '$folder' ")
							->dbLoadQuery("","")
							->loadObjectList('element');	
			}
			
		}
		
		if(isset($plugin[$folder][$element])){
			if (XIPT_JOOMLA_15)
				return $plugin[$folder][$element]->id;
			else
				return $plugin[$folder][$element]->extension_id;
		}
			
		else
			return false;
	}
	
	
	function filterAjaxAddApps(&$appName,&$profiletype, &$objResponse)
	{
	    $appId = XiptLibApps::getPluginId($appName);
	    $notAllowedApps =XiptLibApps::getNotAllowedCommunityAppsArray($profiletype);
	    
	    // do not restrict if allowed
	    if(!in_array($appId,$notAllowedApps))
	        return true;
	    
	    //restrict the user.
	    $objResponse->addAssign('cwin_logo', 'innerHTML', XiptText::_('CC_ADD_APPLICATION_TITLE'));

		$action		= '<form name="cancelRequest" action="" method="POST">';
		$action		.= '<input type="button" class="button" onclick="cWindowHide();return false;" name="cancel" value="'.XiptText::_('CC_BUTTON_CLOSE').'" />';
		$action		.= '</form>';
		
		$objResponse->addAssign('cWindowContent', 'innerHTML', '<div class="ajax-notice-apps-added">'.XiptText::_( 'APPLICATION_ACCESS_DENIED' ).'</div>');
		
		$objResponse->addScriptCall('cWindowActions', $action);
		return false;
		
	} 
}
