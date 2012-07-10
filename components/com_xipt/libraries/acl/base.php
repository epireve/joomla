<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');


abstract class XiptAclBase
{
	public $id			= 0 ;
	public $aclname		= '';
	public $rulename	= '';
	public $published	= 1 ;
	public $coreparams	= '';
	public $aclparams	= '';
	public $triggerForEvents = array('default'=>1);

	function __construct()
	{
		$this->aclname = $className	= get_class($this);

		//Load ACL Params, if not already loaded
		if(!$this->aclparams){
			$aclxmlpath =  dirname(__FILE__).DS.strtolower($className).DS.strtolower($className).'.xml';
			if(JFile::exists($aclxmlpath)){
				$this->aclparams = new XiptParameter('',$aclxmlpath); 
			}
			else{
				$this->aclparams = new XiptParameter('',''); 
				}
		}

		//Load Core Params if defined for current ACL, if not already loaded
		if(!$this->coreparams){
			$corexmlpath =  dirname(__FILE__).DS.strtolower($className).DS.'coreparams.xml';
			if(JFile::exists($corexmlpath)){
				$corexmlpath =  dirname(__FILE__).DS.strtolower($className).DS.'coreparams.xml';
			}
			else{
				$corexmlpath = dirname(__FILE__).DS.'coreparams.xml';
			}
		}
		
		//load core params
		$coreinipath = dirname(__FILE__).DS.'coreparams.ini';		
		$iniData	= JFile::read($coreinipath);
	
		XiptError::assert(JFile::exists($corexmlpath), $corexmlpath. XiptText::_("FILE_DOES_NOT_EXIST"), XiptError::ERROR);
		XiptError::assert(JFile::exists($coreinipath), $coreinipath. XiptText::_("FILE_DOES_NOT_EXIST"), XiptError::ERROR);
		
		$this->coreparams = new XiptParameter($iniData,$corexmlpath);
	}


	function load($id)
	{
		if(0 == $id) {
			return $this;
		}

		$filter = array();
		$filter['id'] = $id;
		$result = XiptAclFactory::getAclRulesInfo($filter);

		if(!$result)
			return $this;

		$info = array_shift($result);
		$this->id 				= $info->id;
		$this->aclname 			= $info->aclname;
		$this->published 		= $info->published;
		$this->rulename 		= $info->rulename;

		$this->coreparams->bind($info->coreparams);
		$this->aclparams->bind($info->aclparams);
		return $this;
	}

	function getObjectInfoArray()
	{
		return get_object_vars($this);
	}

	public function getAclParamsHtml()
	{
		return $this->aclparams->render('aclparams');
	}

	final public function getCoreParamsHtml()
	{
		return $this->coreparams->render('coreparams');
	}

	function collectParamsFromPost($postdata)
	{
		// it is not necessary the each rule will have acl params
		// so check it, and return empty ini string if not exists 
		if(!isset($postdata['aclparams']))
			return "\n\n";

		$param	= new XiptParameter();
		$param->loadArray($postdata['aclparams']);
		return  $param->toString('XiptINI');
	}

	function bind($data)
	{
		if(is_object($data)) {

			$this->aclparams->bind($data->aclparams); 
			$this->coreparams->bind($data->coreparams);
			$this->rulename 	= $data->rulename;
			$this->published 	= $data->published;
			$this->id			= $data->id;
			return $this;
		}

		if(is_array($data)) {
			//XiTODO:: need to test for Joomla 1.5 
			$aclParam   = $data['aclparams']->toArray();
			$coreParams = $data['coreparams']->toArray();
			$this->aclparams->bind($aclParam);
			$this->coreparams->bind($coreParams);
			//$this->aclparams->bind($data['aclparams']);
			//$this->coreparams->bind($data['coreparams']);
			$this->rulename 	= $data['rulename'];
			$this->published 	= $data['published'];
			$this->id			= $data['id'];
			return $this;
		}

		//Any issue
		XiptError::assert(0);
	}

	/**
	 * IMP : Use Refrence as we may need to 
	 * change viewuserid in case of writemessages
	 */
	function isApplicable(&$data)
	{
		// if acl rule are invoked with any triggerForEvent then 
		// only those acl rule will be applied which has set that trigger in 
		// their variable triggerForEvents
		// by default only that acl rules will be applied whci has 
		// default value in it
		//XITODO : clean code : use default from where acl rules are being invoked
		if(isset($data['args']['triggerForEvents'])){
			$key = $data['args']['triggerForEvents'];
			if($key && isset($this->triggerForEvents[$key])==false)
				return false;			
		}
		else	
		 	if(!isset($this->triggerForEvents['default']))
				return false;
			
		$isApplicableAccToAcl      =     $this->checkAclApplicable($data);
		$isApplicableAccToCore     =     $this->checkCoreApplicable($data);

		//These condition need to be AND as we ensure rule only apply if
		// it is applicable as per conditions.
	
		if($isApplicableAccToAcl && $isApplicableAccToCore)
			return true;

		return false;
	}

	public function checkCoreApplicable($data)
	{
		$ptype = $this->getCoreParams('core_profiletype',XIPT_PROFILETYPE_ALL);

		//All means applicable
		if(XIPT_PROFILETYPE_ALL == $ptype)
			return true;

		//profiletype matching
		if(XiptLibProfiletypes::getUserData($data['userid']) == $ptype)
			return true;

		return false;
	}


	abstract public function checkAclApplicable(&$data);


	function checkViolation($data)
	{
		return ($this->checkAclViolation($data) || $this->checkCoreViolation($data));
	}


	function isApplicableOnSelfProfiletype($resourceAccesser)
	{
		$aclSelfPtype = $this->getACLAccesserProfileType();
		$selfPid	= XiptLibProfiletypes::getUserData($resourceAccesser,'PROFILETYPE');
		if(in_array($aclSelfPtype, array(XIPT_PROFILETYPE_ALL,$selfPid)))
			return true;

		return false;
	}
	
	function isApplicableOnOtherProfiletype($resourceOwner)
	{
		$otherptype = $this->getACLOwnerProfileType();
		$otherpid	= XiptLibProfiletypes::getUserData($resourceOwner,'PROFILETYPE');

		// REMOVING ,XIPT_PROFILETYPE_NONE, as it should not be here		
		if(in_array($otherptype, array(XIPT_PROFILETYPE_ALL, $otherpid)))
			return true;
			
		return false;
	}
	
	function getACLAccesserProfileType()
	{
		return $this->coreparams->get('core_profiletype',XIPT_PROFILETYPE_NONE);		
	}
	
	function getACLOwnerProfileType()
	{
		return $this->aclparams->get('other_profiletype',XIPT_PROFILETYPE_ALL);
	}
	
	function isApplicableOnMaxFeature($resourceAccesser,$resourceOwner)
	{	
		$aclSelfPtype = $this->getACLAccesserProfileType();
		$otherptype = $this->getACLOwnerProfileType();
		
		$count = $this->getFeatureCounts($resourceAccesser,$resourceOwner,$otherptype,$aclSelfPtype);
		$paramName = get_class($this).'_limit';
		$maxmimunCount = $this->aclparams->get($paramName,0);
		if($count >= $maxmimunCount)
			return true;
			
		return false;
	}
	
	public function checkAclViolation($data)
	{	
		$resourceOwner 		= $this->getResourceOwner($data);
		$resourceAccesser 	= $this->getResourceAccesser($data);		
		
		if($this->isApplicableOnSelf($resourceAccesser,$resourceOwner) === false)
			return false;
		
		if($this->isApplicableOnSelfProfiletype($resourceAccesser) === false)
			return false;
		
		if($this->isApplicableOnOtherProfiletype($resourceOwner) === false)
			return false;
		
		//XITODO if allwoed to self
		
		
		// if resource owner is friend of resource accesser 
		if($this->isApplicableOnFriend($resourceAccesser,$resourceOwner) === false)
			return false; 
		
		// if feature count is greater then limit
		if($this->isApplicableOnMaxFeature($resourceAccesser,$resourceOwner) === false)
			return false;
				
		return true;
	}

	function getFeatureCounts($resourceAccesser)
	{
		return 0;
	}
	
	abstract function getResourceOwner($data);
	
	function getResourceAccesser($data)
	{
		return $data['userid'];
	} 
	
	function isApplicableOnSelf($accesserid,$ownerid)
	{
		if($this->aclparams->get('acl_applicable_to_self',1) == true)
			return true;
			
		if($accesserid == $ownerid)
			return false;
			
		return true;
	}
	
	function isApplicableonFriend($accesserid,$ownerid)
	{   
		//check rule applicable on friend if yes than return true
		if($this->aclparams->get('acl_applicable_to_friend',1) == true)
			return true;
		
		//check accesser is friend of resource owner, 
		//if they are friend then do not apply rule
		$isFriend = XiptAclHelper::isFriend($accesserid,$ownerid);
		if($isFriend) 
			return false;
		
		return true;
	}
	
	
	
	
	public function checkCoreViolation($data)
	{
		return false;
	}

	public function getCoreParams($what,$default=0)
	{
		return $this->coreparams->get($what,$default);
	}


	public function handleViolation($info)
	{
		$msg 			= $this->getDisplayMessage();
		if($info['ajax']) {
			$this->aclAjaxBlock($msg);
			return $this;
		}

		$redirectUrl 	= $this->getRedirectUrl();
		JFactory::getApplication()->redirect(XiptRoute::_($redirectUrl,false),$msg);
	}


	function aclAjaxBlock($html, $objResponse=null)
	{
		if($objResponse === null)
			$objResponse   	= new JAXResponse();

		$objResponse->addScriptCall('cWindowShow', '', XiptText::_('YOU_ARE_NOT_ALLOWED_TO_PERFORM_THIS_ACTION'), 450, 80);
		$objResponse->addAssign('cWindowContent', 'innerHTML', $html);
		
//XITODO: cleanup
		$forcetoredirect =$this->getCoreParams('force_to_redirect','0');    	
		if($forcetoredirect)
		   {
			 $redirectUrl 	= JURI::base().'/'.$this->getRedirectUrl();
			 $script = "function sleep_message(){"
			                                     ."window.location.href = " .$redirectUrl .";"
			                                     ."cWindowHide();"
			                                     ."return true;"
			                                     ."};";

		     $buttons	= '<input type="button" value="' . XiptText::_('CC_BUTTON_CLOSE') . '" class="button" onclick="cWindowHide(); window.location.href = &quot;' . $redirectUrl . '&quot;;" />';
		     $objResponse->addScriptCall('cWindowActions', $buttons);
		   }
		$objResponse->sendResponse();
	}


	public function getMe()
	{
		return get_class($this);
	}

	public function getDisplayMessage()
	{
		$message = $this->getCoreParams('core_display_message','');		
		$message = base64_decode($message);
		return $message;
	}

	public function getRedirectUrl()
	{
		return $this->getCoreParams('core_redirect_url','index.php?option=com_community');
	}
}
