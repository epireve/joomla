<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptSetupRuleSyncupusers extends XiptSetupBase
{
	function isRequired()
	{
		$params = XiptFactory::getSettings('', 0);
		$defaultProfiletypeID = $params->get('defaultProfiletypeID',0);
		
		if(!$defaultProfiletypeID){
			JFactory::getApplication()->enqueueMessage(XiptText::_("FIRST_SELECT_THE_DEFAULT_PROFILE_TYPE"));
			return false;
		}

		$PTFieldId = XiptHelperJomsocial::getFieldId(PROFILETYPE_CUSTOM_FIELD_CODE);
		$TMFieldId = XiptHelperJomsocial::getFieldId(TEMPLATE_CUSTOM_FIELD_CODE);
		
		// we need first these fields to be exist
		if(!($PTFieldId && $TMFieldId))
			return true;
			
		$result = $this->getUsertoSyncUp();
		
		if(empty($result))
		{
			return false;
		}
		
		return true;
	}
	
	function doApply()
	{
		//find memory limit defined in php.ini
		$memory_size = ini_get('memory_limit');
		$memory_size = substr($memory_size, 0, -1);
		$memory_size = (int)$memory_size;
		$start=JRequest::getVar('start', 0);
		
		//set sync up limit as per memory limit
		if($memory_size >= 128)
			$limit = JRequest::getVar('limit',SYNCUP_USER_LIMIT);
		else
			$limit = 300;
		$reply = $this->syncUpUserPT($start,$limit);

		if($reply === -1)
			return -1;
		else if($reply)
        	return XiptText::_('USERS_PROFILETYPE_AND_TEMPLATES_SYNCRONIZED_SUCCESSFULLY');
        else 
        	return XiptText::_('USERS_PROFILETYPE_AND_TEMPLATES_SYNCRONIZATION_FAILED');
	}
	
	function syncUpUserPT($start, $limit, $test = false)
	{
		
		$PTFieldId = XiptHelperJomsocial::getFieldId(PROFILETYPE_CUSTOM_FIELD_CODE);
		$TMFieldId = XiptHelperJomsocial::getFieldId(TEMPLATE_CUSTOM_FIELD_CODE);
		
		// we need first these fields to be exist
		if(!($PTFieldId && $TMFieldId))
			return false;
		// get userids for syn-cp	
		$result 	 = $this->getUsertoSyncUp($start, $limit);
		$profiletype = XiPTLibProfiletypes::getDefaultProfiletype();
		$template	 = XiPTLibProfiletypes::getProfileTypeData($profiletype,'template');			
		
		$total = $this->totalUsers;
		$flag = false;
		if($total > $limit){
			//echo msg when users are syn-cp
			echo $this->getHTML($start,$total,$limit);
			if(JRequest::getVar('step',false) == false ){
				$this->_SynCpUser($result,$profiletype,$template);
				return -1;
			}
			//$start+=$limit;
			$flag = true;
		}
		
		$this->_SynCpUser($result,$profiletype,$template);
		// Continue user are continuesyn-cp
		if($flag === true){ 
			return -1;
		}
			
		if($test)
			return true;

		$step=JRequest::getVar('step');
		$msg = 'Total '. (($limit*$step)+count($result)) . ' users '.XiptText::_('SYNCHORNIZED');
		JFactory::getApplication()->enqueueMessage($msg);
		return true;
	}
	
	function getMessage()
	{
		$requiredSetup = array();
		
		if($this->isRequired())
		{
			$link = XiptRoute::_("index.php?option=com_xipt&view=setup&task=doApply&name=syncupusers",false);
			$requiredSetup['message']  = '<a href="'.$link.'">'.XiptText::_("PLEASE_CLICK_HERE_TO_SYNC_UP_USERS_PROFILETYPES").'</a>';
			$requiredSetup['done']  = false;
		}
		
		else
		{
			$requiredSetup['message']  = XiptText::_("USERS_PROFILETYPES_ALREADY_IN_SYNC");
			$requiredSetup['done']  = true;
		}
		return $requiredSetup;
	}
	
	function getUsertoSyncUp($start = 0, $limit = SYNCUP_USER_LIMIT)
	{
		//XITODO : apply caching
//		static $users = null;
//		$reset = XiptLibJomsocial::cleanStaticCache();
//		if($users!== null && $reset == false)
//			return $users;

		$db 	= JFactory::getDBO();	
		// XITODO : PUT into query Object
		$xiptquery = ' SELECT `userid` FROM `#__xipt_users` ';
		$query 	= ' SELECT `userid` FROM `#__community_users` '
					.' WHERE `userid` NOT IN ('.$xiptquery.') ';
        			
		$db->setQuery($query);
		$result = $db->loadResultArray();

		$query = ' SELECT `userid` FROM `#__xipt_users` WHERE `profiletype` NOT IN ( SELECT `id` FROM `#__xipt_profiletypes` )';
		$db->setQuery($query);
		$userid = $db->loadResultArray();
		
		$users = array_merge($result, $userid);
		
		sort($users);
		$this->totalUsers = count($users);
//		echo "=======get user to sync=======";
//		$reslt=array_slice($users, $start, $limit);
//		echo "result is :::";
//		print_r($reslt);
		return array_slice($users, $start, $limit);
	}
	
	function getHTML($start, $total, $limit)
	{
		ob_start();
		?>
		<div>
			<h3 style="width:100%; background:#7ac047;text-align:center;color:RED;padding:5px;font-weight:bold;">
			<?php 
			//show user reseted
			//can show 'reseting next 500 user'
			echo "Reset Page : DO NOT CLOSE THIS WINDOW WHILE SYNCRONIZATION ";
			?>
			</h3>
			<?php
			$step=JRequest::getVar('step',0);
			$end = ($step+1)*$limit;
			//Number of user syn-cp when limit is greater then remaining user 
			//if($limit > $total){
				//$remain=$end = $total;
			//}
			// display Total users
			echo "<br /> Total ". $total=$total+($limit*$step)." users for Syn-cp";	
			
			//dispaly syn-cp users
			echo "<br />Syncing-Up Users  ". ($step)*$limit ." To ". $end ." ";
			$step++;
			$remain = $total-($limit*$step);
			//dispaly remaning users 
			echo "<br />Remaining " .$remain . " Users";
							
			?>
			<script>
			window.onload = function() {
				  setTimeout("xiredirect()", 3000);
			}
			
			function xiredirect(){
				window.location = "<?php echo XiptRoute::_("index.php?option=com_xipt&view=setup&task=doApply&name=syncupusers&start=$start&limit=$limit&step=$step");?>"					
			}
			
			</script>
			</div>
		<?php 
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	/**
	 * 
	 * @param unknown_type $result number of user for syncp
	 * @param unknown_type $profiletype : default profile-typr
	 * @param unknown_type $template: default-template
	 */
	function _SynCpUser($result,$profiletype,$template) {
		foreach ($result as $userid){
			XiPTLibProfiletypes::updateUserProfiletypeData($userid, $profiletype, $template, 'ALL');
			}
	}

}