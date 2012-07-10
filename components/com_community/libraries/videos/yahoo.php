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
 * Class to manipulate data from Yahoo
 * 	 	
 * @access	public  	 
 */
class CTableVideoYahoo extends CVideoProvider
{
	var $xmlContent = null;
	var $url 		= '';
	var $videoId	= '';
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		//return 'http://animalvideos.yahoo.com/video-detail?vid=27094620';
		//return 'http://video.yahoo.com/watch/'.$this->videoId;
		return $this->url;
	}
	
	/**
	 * Extract Yahoo video id from the video url submitted by the user
	 * 	 	
	 * @access	public
	 * @param	video url
	 * @return videoid	 
	 */
	public function getId()
	{
		$videoId  = '';
		
		parse_str( parse_url( $this->url, PHP_URL_QUERY ), $result );
		$videoId = $result['vid'];
		
		// If we have no match, need to check for alternative
		if( empty($videoId) )
		{
			$id = explode('-',$this->url);
			$id = $id[count($id)-1];
			$id = explode('.',$id);
			$videoId = $id[0];
			/*
			if($id < 1){
				$pattern =  '/flashvars="vid=(.*)&amp;/';
				preg_match( $pattern, $this->xmlContent, $match );

				if( $match[0] ){
					$videoId    = $match[0];
				}
			}
			*/
		}
		
		return $videoId;
	}
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'yahoo';
	}
	
	public function getTitle()
	{
		$title = '';
		
		$pattern =  "'<meta property=\"og:title\" content=\"(.*?)\"'s"; 
		preg_match_all($pattern, $this->xmlContent, $matches);
						
		$title = $matches[1][0];
		
		if($title == ''){
			$pattern =  "'name=\"context_title\" value=\"(.*?)\"'s"; 
			preg_match_all($pattern, $this->xmlContent, $matches);
							
			$title = $matches[1][0];	
		}

		return $title;
	}
	
	public function getDescription()
	{
		$description	= '';
		
		// Get description
		$pattern =  "'<meta property=\"og:description\" content=\"(.*?)\"'s"; 
		preg_match_all($pattern, $this->xmlContent, $matches);
						
		$description = $matches[1][0];
		
		if($description == ''){
			$pattern =  "'desc\":\"(.*?)\"'s"; 
			preg_match_all($pattern, $this->xmlContent, $matches);
							
			$description = stripslashes($matches[1][0]);
		}

		return $description;
	}
	
	public function getDuration()
	{
		$duration = null;
		
		// Get description
		$pattern =  "'x-duration=\"(.*?)\"'s"; 
		preg_match_all($pattern, $this->xmlContent, $matches);
						
		$duration = $matches[1][0];
		
		if($duration == ''){
			$pattern =  "'durtn\":\"(.*?)\"'s"; 
			preg_match_all($pattern, $this->xmlContent, $matches);
							
			$duration = $matches[1][0];
		}
		
		if($duration != ''){
			$sec = 0;
			$time = explode(':',$duration);
			if($time[0] > 0){
				$sec = $time[0]*60;
			}
			$duration = $sec + $time[1];
		}else{
			$duration = false;
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
		
		$pattern =  "'thmb_url\":\"(.*?)\"'s"; 
		preg_match_all($pattern, $this->xmlContent, $matches);
						
		$thumbnail = stripslashes($matches[1][0]);
		
		if($thumbnail == ''){
			$pattern =  "'<meta property=\"og:image\" content=\"(.*?)\"'s"; 
		
			preg_match_all($pattern, $this->xmlContent, $matches);
		
			if( $matches && !empty($matches[1][0]) )
			{					
				$thumbnail = urldecode($matches[1][0]);			
			}
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
		$embedCode ='<embed type="application/x-shockwave-flash" src="http://d.yimg.com/nl/cbe/paas/player.swf" width="'.$videoWidth.'" height="'.$videoHeight.'" style="undefined" id="yppVideoPlayer22353" name="yppVideoPlayer22353" bgcolor="#000000" quality="high" allowfullscreen="true" allowscriptaccess="always" wmode="transparent" tabindex="999" flashvars="eventHandler=window.YEP36272.playerEvents&amp;autoPlay=true&amp;infoScreenUI=show&amp;shareScreenUI=hide&amp;startScreenCarouselUI=hide&amp;embedCode=on&amp;vid='.$videoId.'&&amp">';
		
		return $embedCode;
	}
}
