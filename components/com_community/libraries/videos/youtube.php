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
 * Class to manipulate data from YouTube
 * 	 	
 * @access	public 
 */
class CTableVideoYoutube extends CVideoProvider
{	
	var $xmlContent = null;
	var $url = '';
	
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://gdata.youtube.com/feeds/api/videos/' . $this->getId();
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
                
		// Connect and get the remote video
		if ( $this->xmlContent == 'Invalid id')
		{
			$this->setError(JText::_('COM_COMMUNITY_VIDEOS_INVALID_VIDEO_ID_ERROR'));
			return false;
		}
		
		if($this->xmlContent == 'Video not found')
		{
			$this->setError(JText::_('COM_COMMUNITY_VIDEOS_YOUTUBE_ERROR'));
			return false;
		}

		
	
		
		
		return true;
	}
	
	/**
	 * Extract YouTube video id from the video url submitted by the user
	 * 	 	
	 * @access	public
	 * @param	video url
	 * @return videoid	 
	 */
	public function getId()
	{
		/*
		$code = '';
		
		$pattern    = "'youtube(.*)watch(\?&?v=|#!v=)+([A-Za-z0-9-_]+)'s"; 
		preg_match($pattern, $this->url, $matches);
		
		if($matches && !empty($matches[3]) ){
			$code = $matches[3];
		}
		*/
		
		parse_str( parse_url( $this->url, PHP_URL_QUERY ), $result );
		$code = $result['v'];
		
		// If we have no match, need to check for youtu.be address
		// which is the new youtbe share address
		if( empty($code) )
		{
			$pattern    = "'youtu.be/([A-Za-z0-9-_]+)'s"; 
			preg_match($pattern, $this->url, $matches);
	  
			if($matches && !empty($matches[1]) ){
				$code = $matches[1];
			}
		}
			
		return $code;
	}
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'youtube';
	}
	
	public function getTitle()
	{
		$title = '';
		// Store video title
		$pattern =  "/<title type='text'>(.*?)<\/title>/i";
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
			$pattern =  "/<content type='text'>(.*?)<\/content>/s";
			preg_match_all($pattern, $this->xmlContent, $matches);
			if($matches && !empty($matches[1][0]) )
			{
			    $description = $matches[1][0];
			}
		
		return $description;
	}
	
	public function getDuration()
	{
		$duration = 0;
		// Store duration
		$pattern =  "/seconds='(.+?)'/i";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$duration = $matches[1][0];
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
		return 'http://img.youtube.com/vi/' . $this->getId() . '/default.jpg';
	}
	
	/**
	 * 
	 * 
	 * @return $embedvideo specific embeded code to play the video
	 */
	public function getViewHTML($videoId, $videoWidth, $videoHeight)
	{
		$config = CFactory::getConfig();
		if (!$videoId)
		{
			$videoId	= $this->getId();
		}

		$html = '';
		if($config->get('use_youtube_iframe_embed'))
		{
			// Use new iframe embed method
			$html = '<iframe class="youtube-player" type="text/html" width="'.$videoWidth.'" height="'.$videoHeight.'" src="http://www.youtube.com/embed/'.$videoId.'" frameborder="0">
				</iframe>';
		}
		else
		{
			$html = "<embed src=\"http://www.youtube.com/v/" .$videoId. "&hl=en&fs=1&hd=1&showinfo=0&rel=0\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"".$videoWidth."\" height=\"".$videoHeight. "\" wmode=\"transparent\"></embed>";
		}
		
		return $html;
	}
}