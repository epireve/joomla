<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once (COMMUNITY_COM_PATH.DS.'models'.DS.'videos.php');

/**
 * Class to manipulate data from google video
 * 	 	
 * @access	public  	 
 */
class CTableVideoGoogle extends CVideoProvider
{
	var $xmlContent	= null;
	var $url		= '';
	var $videoId	= '';
	
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://video.google.com/videoplay?docid=' . $this->videoId;
	}
	
	
	/**
	 * Extract Google video id from the video url submitted by the user
	 * 	 	
	 * @access	public
	 * @param	
	 * @returns 	 
	 */
	public function getId()
	{
		$videoId		= '';
		
		preg_match('/docid=([\-0-9a-zA-Z]+)/', $this->url , $matches);
	 	if( isset($matches[0]) )
		{
			$itemid		= split("=",$matches[0]);
			$videoId	= $itemid[1];
		}
		
		return $videoId;
	}
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'google';
	}
	
	public function getTitle()
	{
		$title	= '';
		
		// Get title
		$pattern =  "'<title>(.*?)</title>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$title = $matches[1][0];
		}
		
		return $title;
	}
	
	public function getDescription()
	{
		$description	= '';
		
		// Get description
		$pattern =  "'<span id=video-description>(.*?)</span>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$description = $matches[1][0];
		}
		
		return $description;
	}
	
	public function getDuration()
	{
		$duration	= '';
		
		//Get duration
		$pattern =  "'<span class=gray id=video-duration>(.*?)</span>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$duration	= explode( ":",CString::str_ireplace("&nbsp;","",trim(strip_tags($matches[1][0]))) );
			$duration	= ( $duration[0]*60 ) + $duration[1];
		}
		
		return $duration;
	}

	public function getThumbnail()
	{
		$thumbnail	= '';
			
		//Get thumbnail
		$pattern =  "'thumbnailUrl\\\\x3d(.*?)\\\\x26docid\\\\x3d's";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
		    $thumbnail = rawurldecode($matches[1][0]);
		}
		
		return $thumbnail;
	}
	
    /**
	 * 
	 * 
	 * @return $embedCode specific embeded code to play the video
	 */
	public function getViewHTML( $videoId, $videoWidth, $videoHeight )
	{
		if (!$videoId)
		{
			$videoId	= $this->videoId;
		}
		return "<embed id=\"VideoPlayback\" src=\"http://video.google.com/googleplayer.swf?docid=" .$videoId. "&hl=en&fs=true\" style=\"width:".$videoWidth."px;height:".$videoHeight."px\" allowFullScreen=\"true\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\" wmode=\"transparent\"> </embed>";
	}
	
}
