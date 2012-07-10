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
 * Class to manipulate data from Photobucket
 * 	 	
 * @access	public  	 
 */
class CTableVideoPhotobucket extends CVideoProvider
{
	var $xmlContent = null;
	var $url		= '';
	var $videoId	= '';
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://media.photobucket.com/video/'.$this->videoId;
	}	
	
	/**
	 * Extract video id from the video url submitted by the user
	 * 	 	
	 * @access	public
	 * @param	video url
	 * @return videoid	 
	 */
	public function getId()
	{			

        $pattern    = '/http\:\/\/(media\.)?photobucket.com\/?(.*\/)video\/([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-\s]*)/';
        preg_match( $pattern, $this->url, $match);
      
        return !empty($match[3]) ? $match[3] : null;
	}
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'photobucket';
	}
	
	public function getTitle()
	{
		$title	= '';
		
		// Get title
		$pattern =  "'<h2 id=\"mediaTitle\">(.*?)<\/h2>'s";
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
		$pattern =  "'<meta name=\"description\" content=\"(.*?)\" \/>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$description = trim(strip_tags($matches[1][0]));
		}
		
		return $description;
	}
	
	public function getDuration()
	{
		return 0;
	}
	
	/**
	 * 
	 * @param $videoId
	 * @return unknown_type
	 */
	public function getThumbnail()
	{
		$thumbnail	= '';

		// Get thumbnail 	 
		$pattern =  "'<link rel=\"image_src\" href=\"(.*?)\" \/>'s";
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
		$file 	= 'http://media.photobucket.com/video/'.CString::str_ireplace( " ", "%20", $videoId );
		$xmlContent = CRemoteHelper::getContent($file);
		if ($xmlContent==FALSE)
		{
			return false;
		}
		$pattern =  "'<link rel=\"video_src\" href=\"(.*?)\" \/>'s";
		preg_match_all($pattern, $xmlContent, $matches);
		if($matches)
		{
			$videoUrl= rawurldecode($matches[1][0]);
		}
		$embedCode = '<embed width="'.$videoWidth.'" height="'.$videoHeight.'" type="application/x-shockwave-flash" wmode="transparent" src="'.$videoUrl.'">';
		return $embedCode;
	}
}
