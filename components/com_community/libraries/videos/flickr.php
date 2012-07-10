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
 * Class to manipulate data from Flickr
 * 	 	
 * @access	public  	 
 */
class CTableVideoFlickr extends CVideoProvider
{
	var $xmlContent = null;
	var $url = '';
	
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://www.flickr.com/photos/'.$this->getId();
	}
	
	/**
	 * Extract Flickr video id from the video url submitted by the user
	 * 	 	
	 * @access	public
	 * @param	video url
	 * @return videoid	 
	 */
	public function getId()
	{
        $pattern    = '/http\:\/\/\w{3}\.?flickr.com\/photos\/(.*)/';
        preg_match( $pattern, $this->url, $match );
       
        return !empty($match[1]) ? $match[1] : null ;
	}
	
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'flickr';
	}
	
	public function getTitle()
	{
		$title = '';		
		// Store video title
		$videoId = explode("/",$this->getId());
		$pattern =  "'<h1 id=\"title_div".$videoId[1]."\" property=\"dc:title\"( class=\"photo-title\")?\>(.*?)<\/h1>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);

		if($matches)
		{
			$title = $matches[2][0];
		}
		
		return $title;
	}
	
	public function getDescription()
	{   $description = '';
		// Store description
		$videoId = explode("/",$this->getId());
		$pattern =  "'<div id=\"description_div".$videoId[1]."\" class=\"(photoDescription|photo-desc)\">(.*?)<\/div>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);  

		if($matches)
		{
			$description = trim(strip_tags($matches[2][0]));
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
		$thumbnail = '';
		// Store thumbnail  
		$pattern =  "'<link rel=\"image_src\" href=\"(.*?)\"( \/)?(>)'s";
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
	 * @return $embedvideo specific embeded code to play the video
	 */
	public function getViewHTML($videoId, $videoWidth, $videoHeight)
	{
		if (!$videoId)
		{
			$videoId	= $this->videoId;
		}
		CFactory::load('helpers', 'remote');
		$xmlContent = CRemoteHelper::getContent('http://www.flickr.com/photos/'.$videoId);
		$pattern =  "'<link rel=\"video_src\" href=\"(.*?)\"( \/)?(>)'s";
		preg_match_all($pattern, $xmlContent, $matches);
		if($matches)
		{
			$videoUrl = rawurldecode($matches[1][0]);
		}

		return '<embed width="'.$videoWidth.'" height="'.$videoHeight.'" wmode="transparent" allowFullScreen="true" type="application/x-shockwave-flash" src="'.$videoUrl.'"/>';
	}
}
