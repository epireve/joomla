<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class accessvideocategory extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];
	}
	
	function checkAclViolation(&$data)
	{	
		$resourceAccesser 	= XiptAclBase::getResourceAccesser($data);		
		
		if(XiptAclBase::isApplicableOnSelfProfiletype($resourceAccesser) === false)
			return true; 
		
		if($this->isApplicableForVideoCategory($data)=== true)
			return false;
				
		return true;
	}
	
	function checkAclApplicable(&$data)
	{
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;

		if('videos' != $data['view'])
			return false;

		if($data['task'] === 'video')
				return true;

		return false;
	}
	
	function isApplicableForVideoCategory($data)
	{
		$videoId	= isset($data['videoid'])? $data['videoid'] : 0;
		$videoId	= JRequest::getVar('videoid' , $videoId, 'REQUEST');
		$db 		= JFactory::getDBO();
		$query		= 'SELECT '.$db->nameQuote('category_id')
						.' FROM '.$db->nameQuote('#__community_videos')
						.' WHERE '.$db->nameQuote('id').' = '.$db->Quote($videoId);

		$db->setQuery( $query );
		$result = $db->loadObject();
		if(!$result)
			return false;
		$aclvideo=$this->aclparams->get('video_category');
		if ($aclvideo === $result->category_id)
			return true;
			
		return false;
	}
	
}
