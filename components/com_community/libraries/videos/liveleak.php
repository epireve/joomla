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
 * Class to manipulate data from Live Leak
 * 	 	
 * @access	public  	 
 */
class CTableVideoLiveleak extends CVideoProvider
{
	var $xmlContent = null;
	var $url 		= '';
	var $videoId	= '';
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://www.liveleak.com/view?i=' . $this->videoId;
	}	
	
	/**
	 * Extract LiveLeak video id from the video url submitted by the user
	 * 	 	
	 * @access	public
	 * @param	video url
	 * @return videoid	 
	 */
	public function getId()
	{
        $pattern    = '/http\:\/\/(\w{3}\.)?liveleak.com\/view\?i\=([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-]*)/';
        preg_match( $pattern, $this->url, $match );

        return !empty($match[2]) ? $match[2] : null; 
	}
	
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'liveleak';
	}
	
	public function getTitle()
	{	
		$title	= '';
		
		$res = preg_match("/<title>LiveLeak.com - (.*)<\/title>/", $this->xmlContent, $title_matches);
        if (!$res) 
            return null; 

        $title = $title_matches[1];

		return $title;
	}
	
	public function getDescription()
	{
		$description	= '';
				
		// get description
		// get thumbnail
		$res = preg_match('/<meta property="og:description" content="(.*)"/', $this->xmlContent, $title_matches);
        if (!$res) 
            return null; 

        $description = $title_matches[1];
		
		return $description;
	}
	
	public function getDuration()
	{
		return false;
	}

	public function getThumbnail()
	{
		$thumbnail	= '';
		$noPreview  = 'http://209.197.7.204/e3m9u5m8/cds/u/nopreview.jpg';
		
		// get thumbnail
		$res = preg_match('/<meta property="og:image" content="(.*)"/', $this->xmlContent, $title_matches);
        if (!$res) 
            return null; 

        $thumbnail = $title_matches[1];

		return !empty($thumbnail) ? $thumbnail : $noPreview;
	}
	
	/**
	 * 
	 * 
	 * @return $embedvideo specific embeded code to play the video
	 */
	public function getViewHTML( $videoId, $videoWidth, $videoHeight )
	{
		if (!$videoId)
		{
			$videoId	= $this->videoId;
		}
		return "<embed src=\"http://www.liveleak.com/e/".$videoId."\" width=\"".$videoWidth."\" height=\"".$videoHeight."\" wmode=\"transparent\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" allowFullScreen=\"true\"> </embed>";
	}
}
