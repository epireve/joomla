<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class accessvideo extends XiptAclBase
{
	function getResourceOwner($data)
	{
		$videoId = isset($data['videoid']) ? $data['videoid'] : '';
		$videoId = JRequest::getVar( 'videoid' , $videoId );
		$conf    = JFactory::getConfig();
        // if sef is enabled then get actual video id
		if($conf->getValue('config.sef', false) == true)
		{
			$vId     = explode(":", $videoId);
			$videoId = $vId[0];
			
		}
		
		$video	    = CFactory::getModel('videos');
		$videoData  = $video->getVideos(array('id'=>$videoId));
		$creatorid	= $videoData[0]->creator;
		return $creatorid;
	}

	function checkAclApplicable(&$data)
	{
		/*XITODO : we will expect that vie task and should be given
		 * and from parsing we will find out that is this request for me
		 */
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;

		if('videos' != $data['view'])
			return false;

		if($data['task'] === 'video')
				return true;

		return false;
	}

}