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
 * Class to manipulate data from MySpace
 * 	 	
 * @access	public  	 
 */
class CTableVideoMyspace extends CVideoProvider
{
	var $xmlContent = null;
	var $url		= '';
	var $videoId	= '';
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://www.myspace.com/video/vid/'.$this->videoId;
	}	
	
	/**
	 * Extract MySpace video id from the video url submitted by the user
	 * 	 	
	 * @access	public
	 * @param	video url
	 * @return videoid	 
	 */
	public function getId()
	{			
        $pattern    = '/(\d{9})/';
        preg_match( $pattern, $this->url, $match);

        return !empty($match[1]) ? $match[1] : null;
	}
	
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'myspace';
	}
	
	public function getTitle()
	{
		$title		= '';	

		// Get title
		$pattern	= "'<meta name=\"title\" content=\"(.*?)\" \/>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$title	= $matches[1][0];
		}

		return $title;
	}
	
	public function getDescription()
	{
		$description	= '';
		
		// Get description
		$pattern		= 	"'id=\"fullDescription\"(.*?)<\/div>'s";
		preg_match_all($pattern, $this->xmlContent, $matchesTmp);   
		
		if($matchesTmp){ 
		  
			$pattern	= 	"'<div class=\"clearfix\">(.*?)<\/div>'s";
			preg_match_all($pattern, $matchesTmp[0][0], $matches); 
			if($matches)
			{
				$description = trim(strip_tags($matches[1][0]));
			} 
			
		}
		
		return $description;
	}
	
	public function getDuration()
	{
		return false;
	}

	public function getThumbnail()
	{
		$thumbnail	= '';
		
		// Get thumbnail
		$pattern	= "'<link rel=\"image_src\" href=\"(.*?)\" \/>'s";
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
	public function getViewHTML($videoId, $videoWidth , $videoHeight)
	{
		if (!$videoId)
		{
			$videoId	= $this->videoId;
		}
		if(strpos($videoId, "&") == true)
		{
			$videoId_tmp = substr($videoId, strpos($videoId, "&"));
			$videoId     = CString::str_ireplace($videoId_tmp,"",$videoId);
		}
		
		$embedCode   = '<object width="'.$videoWidth.'px" height="'.$videoHeight.'px" ><param name="allowFullScreen" value="true"/><param name="wmode" value="transparent"/><param name="movie" value="http://mediaservices.myspace.com/services/media/embed.aspx/m='.$videoId.',t=1,mt=video"/><embed src="http://mediaservices.myspace.com/services/media/embed.aspx/m='.$videoId.',t=1,mt=video" width="'.$videoWidth.'" height="'.$videoHeight.'" allowFullScreen="true" type="application/x-shockwave-flash" wmode="transparent"/></object>';

		return $embedCode;
	}
}
