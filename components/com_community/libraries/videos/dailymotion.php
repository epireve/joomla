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
 * Class to manipulate data from Daily Motion
 * 	 	
 * @access	public  	 
 */
class CTableVideoDailymotion extends CVideoProvider
{
	var $xmlContent = null;
	var $url = '';
	
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://www.dailymotion.com/video/'.$this->getId();
	}
	
	/**
	 * Extract DailyMotion video id from the video url submitted by the user
	 * 	 	
	 * @access	public
	 * @param	video url
	 * @return videoid	 
	 */
	public function getId()
	{
        $pattern    = '/dailymotion.com\/?(.*)\/video\/(.*)/';
        preg_match( $pattern, $this->url, $match);
        
        return !empty($match[2]) ? $match[2] : null;
	}
	                                 
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'dailymotion';
	}
	
	public function getTitle()
	{
		$title = '';		
		// Store video title
		$pattern =  "/<h1 class=\"dmco_title\">(.*)(<\/h1>)?(<\/span>)/i";
		preg_match_all($pattern, $this->xmlContent, $matches);

		if( $matches && !empty($matches[1][0]) )
		{
			$title = strip_tags($matches[1][0]);
		}
		
		return $title;
	}
	
	public function getDescription()
	{
		$description = '';
		// Store description
		$pattern =  "/<meta name=\"description\" lang=\"en\" content=\"(.*)\" \/>/i";
		preg_match_all($pattern, $this->xmlContent, $matches);
		
		if( $matches && !empty($matches[1][0]) )
		{
			$description = trim(strip_tags($matches[1][0],'<br /><br>'));
		}
		
		return $description;
	}
	
	public function getDuration()
	{
		$duration = '';
		// Store duration
		$pattern =  "'DMDURATION=(.*?)&'s";			 
		preg_match_all($pattern, $this->xmlContent, $matches);
	
		if( $matches && !empty($matches[1][0]) )
		{
            $duration   = $matches[1][0];
		}
			
		return $duration;
	}
	
	/**
	 * Get video's thumbnail URL from videoid
	 * 
	 * @access 	public
	 * @param 	videoid
	 * @return url
	 */
	public function getThumbnail()
	{
		$thumbnail = '';
		
		/*
		$pattern =  "'<link rel=\"image_src\" type=\"image/jpeg\" href=\"(.*?)\"'s"; 
		*/
		
		$pattern =  "'<meta property=\"og:image\" content=\"(.*?)\"'s"; 
		
		preg_match_all($pattern, $this->xmlContent, $matches);
	
		if( $matches && !empty($matches[1][0]) )
		{					
			$thumbnail = urldecode($matches[1][0]);			
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
		$embedCode = "<embed src=\"http://www.dailymotion.com/swf/".$videoId."\" width=\"".$videoWidth."\" height=\"".$videoHeight."\" wmode=\"transparent\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" allowFullScreen=\"true\"> </embed>";
		
		return $embedCode;
	}
	                    
}
