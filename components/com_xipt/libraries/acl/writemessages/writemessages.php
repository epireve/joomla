<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class writemessages extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['viewuserid'];	
	}
	
	function isApplicableOnMaxFeature($resourceAccesser,$resourceOwner)
	{	
		$aclSelfPtype = $this->coreparams->get('core_profiletype',-1);
		$otherptype = $this->aclparams->get('other_profiletype',-1);
		
		$count = $this->getFeatureCounts($resourceAccesser,$resourceOwner,$otherptype,$aclSelfPtype);
		$paramName ='writemessage_limit';
		$maxmimunCount = $this->aclparams->get($paramName,0);
		if($count >= $maxmimunCount)
			return true;
			
		return false;
	}
	
	function getFeatureCounts($resourceAccesser,$resourceOwner,$otherptype,$aclSelfPtype)
	{
		CFactory::load( 'helpers' , 'time' );
		$db			=& JFactory::getDBO();

		/* otherptype o means rule is defined to count message written to any one */
		if($otherptype == -1 || $otherptype == 0) {
			$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_msg' ) . ' AS a'
					. ' WHERE a.from=' . $db->Quote( $resourceAccesser )
					. ' AND a.parent=a.id';
		}
		else
		{
			$query = "SELECT COUNT(*) FROM #__community_msg_recepient as a "
					." 	LEFT JOIN #__community_msg as b ON b.`id` = a.`msg_id` "
					."  LEFT JOIN #__xipt_users as c ON a.`to`=c.`userid` "
					."  WHERE a.`msg_from` = ".$resourceAccesser
					."  AND c.`profiletype`='$otherptype'" ;
		}

		$db->setQuery( $query );
		$count		= $db->loadResult();
		return $count;
	}

	function aclAjaxBlock($msg)
	{
		$objResponse   	= new JAXResponse();
		$title		= XiptText::_('CC_WRITE_MESSAGE');
		$objResponse->addScriptCall('cWindowShow', '', $title, 430, 80);
		return parent::aclAjaxBlock($msg, $objResponse);
	}

	function checkAclApplicable(&$data)
	{
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;

		if('inbox' != $data['view'])
			return false;

		if($data['task'] == 'ajaxcompose' || $data['task'] == 'ajaxaddreply' ) {
			//modify whom we are sending msg
			$data['viewuserid'] = $data['args'][0];
			return  true;
		}

		if($data['task'] == 'write') {
			//if username give then find user-id
			$data['viewusername'] = isset($data['viewusername']) ? $data['viewusername']:  '';
			$viewusername = JRequest::getVar('to',$data['viewusername']);
			if($viewusername != '') {
				$db			=& JFactory::getDBO();

				$query = "SELECT * FROM ".$db->nameQuote('#__users')
						." WHERE ".$db->nameQuote('username')."=".$db->Quote($viewusername);

				$db->setQuery( $query );
				$user = $db->loadObject();

				if(!empty($user)) $data['viewuserid'] = $user->id;
			}

			return  true;
		}


		return false;
	}
}
