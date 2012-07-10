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
 * Class to manipulate data from Break
 * 	 	
 * @access	public  	 
 */
class CTableVideoBreak extends CVideoProvider
{
	var $xmlContent = null;
	var $url = '';
	
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://www.break.com/' . $this->getId();
	}
	
	/*
	 * Return true if successfully connect to remote video provider
	 * and the video is valid
	 */	 
	public function isValid()
	{
		if ( !parent::isValid())
		{
			return false;
		}
		
		// Return Break error
		$pattern =  "'<span id=\"sitemap404msg\">(.*?)<\/span>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);

		if( !empty($matches[1][0]) )
		{
			$errormsg = 'COM_COMMUNITY_VIDEOS_FETCHING_VIDEO_ERROR' . strip_tags( $matches[1][0] );
			$this->setError	( JText::_($errormsg) );
			return false;
		}
		
		return true;			
	}	
	
	/**
	 * Extract Break video id from the video url submitted by the user
	 * 	 	
	 * @access	public
	 * @param	video url
	 * @return videoid	 
	 */
	public function getId()
	{
	   $pattern    = '/http\:\/\/(\w{3}\.)?break.com\/(.*)/';
	   preg_match( $pattern, $this->url, $match );
	   
       return !empty( $match[2] ) ? $match[2] : null;
	}
	
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'break';
	}
	
	public function getTitle()
	{
		$title = '';		
		// Store video title
		$pattern =  "'<meta name=\"embed_video_title\" id=\"vid_title\" content=\"(.*?)\"( \/)?(>)'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$title = $matches[1][0];
		}
		
		return $title;
	}
	
	public function getDescription()
	{
		$description = '';
		// Store description
		$pattern =  "'<meta name=\"embed_video_description\" id=\"vid_desc\" content=\"(.*?)\"( \/)?(>)'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$description = $matches[1][0];
		}
		
		return $description;
	}
	
	public function getDuration()
	{			
		return 0;
	}
	
	/**
	 * Get video's thumbnail
	 * 
	 * @access 	public
	 * @param 	videoid
	 * @return url
	 */
	public function getThumbnail()
	{
		$thumbnail = '';
		// Store thumbnail
		$pattern =  "'<meta name=\"embed_video_thumb_url\" content=\"(.*?)\"( \/)?(>)'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$thumbnail = $matches[1][0];
		}
		
		return $thumbnail;
	}
	
	/**
	 * 
	 * 
	 * @return $embedvideo specific embeded code to play the video
	 */
	public function getViewHTML($videoId, $videoWidth, $videoHeight)
	{
 		if (!$videoId)
		{
			$videoId	= $this->videoId;
		}
		CFactory::load('helpers', 'remote');
		$remoteFile	= 'http://www.break.com/'.$videoId;
		$xmlContent = CRemoteHelper::getContent($remoteFile);
 		
		$pattern =  "'<meta name=\"embed_video_url\" content=\"(.*?)\"( /)?(>)'s";
		preg_match_all($pattern, $xmlContent, $matches);
		if($matches)
		{
				$videoUrl = $matches[1][0];
		}
		
		return "<embed src=\"".$videoUrl."\" width=\"".$videoWidth."\" height=\"".$videoHeight."\" wmode=\"transparent\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" allowFullScreen=\"true\"> </embed>";
	}
}
