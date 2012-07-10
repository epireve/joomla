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
 * Class to manipulate data from Revver
 * 	 	
 * @access	public  	 
 */
class CTableVideoRevver extends CVideoProvider
{
	var $xmlContent = null;
	var $url 		= '';
	var $videoId	= '';
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://www.revver.com/video/'.$this->videoId;
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
        $pattern    = '/http\:\/\/(\w{3}\.)?revver.com\/video\/(.*)/';
        preg_match( $pattern, $this->url, $match);

        return !empty($match[2]) ? $match[2] : null;
	}
	
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'revver';
	}
	
	public function getTitle()
	{
		$title	= '';
		
		// Get title
		$pattern =  "/<meta name=\"title\" content=\"(.*?)\" \/>/i";
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
		return false;
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
		$videoId = explode("/",$videoId);
		$embedCode = '<object width="'.$videoWidth.'" height="'.$videoHeight.'" data="http://flash.revver.com/player/1.0/player.swf?mediaId='.$videoId[0].'" type="application/x-shockwave-flash" id="revvervideo'.$videoId[0].'"><param name="Movie" value="http://flash.revver.com/player/1.0/player.swf?mediaId='.$videoId[0].'"></param><param name="wmode" value="transparent"/></param><param name="AllowFullScreen" value="true"></param><param name="AllowScriptAccess" value="always"></param><embed type="application/x-shockwave-flash" src="http://flash.revver.com/player/1.0/player.swf?mediaId='.$videoId[0].'" pluginspage="http://www.macromedia.com/go/getflashplayer" allowScriptAccess="always" flashvars="allowFullScreen=true" allowfullscreen="true" height="'.$videoHeight.'" width="'.$videoWidth.'" wmode="transparent"></embed></object>';
		
		return $embedCode;
	}
}
