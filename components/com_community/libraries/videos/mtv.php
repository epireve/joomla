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
 * Class to manipulate data from Mtv video
 * 	 	
 * @access	public  	 
 */
class CTableVideoMtv extends CVideoProvider
{
	var $xmlContent = null;
	var $url 		= '';
	var $videoId	= '';
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return 'http://www.mtv.com/videos/' .$this->videoId;
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
		
		// If video is redirected
		$pattern =  "'301 Moved Permanently's";
		if(preg_match_all($pattern, $this->xmlContent, $matches))
		{
			$this->setError	( JText::_('COM_COMMUNITY_VIDEOS_FETCHING_VIDEO_ERROR') );
			return false;
		}
		
		return true;
	}	
	
	
	/**
	 * Extract Mtv video id from the video url submitted by the user
	 * 	 	
	 * @access	public
	 * @param	
	 * @returns 	 
	 */
	public function getId()
	{
		$videoId	= '';
				
		preg_match('/videos\/(.*)/', $this->url , $matches);
	 	if (!empty($matches[1])){
			$videoId	= $matches[1];		
		}				
		
		return $videoId;	
	}
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'mtv';
	}
	
	public function getTitle()
	{	
		$title	= '';
		
		// Get title
		$pattern =  "'<meta name=\"mtv_vt\" content=\"(.*?)\"/>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$this->title = $matches[1][0];
		}
		
		if(empty($this->title))
		{
			$pattern =  "'<title>(.*?)</title>'s";
			preg_match_all($pattern, $this->xmlContent, $matches);
			if($matches)
			{
				$this->title = $matches[1][0];
			}
		}
			
		return $this->title;
	}
	
	public function getDescription()
	{
		$description	= '';
				
		// Get description
		$pattern =  "'<meta name=\"description\"\n?content=\"(.*?)\"/>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$description = $matches[1][0];
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
		
		//Get thumbnail
		$pattern =  "'<meta name=\"thumbnail\"( )?(\n)?(content=\"(.*?)\"/>)'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
		    $thumbnail = 'http://www.mtv.com' . $matches[4][0];
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
		$videoId  = !empty($videoId) ? $videoId : $this->url;
		
		CFactory::load('helpers', 'remote');
		$xmlContent = CRemoteHelper::getContent('http://www.mtv.com/videos/' . $videoId);
		$videoPath	= explode( '/' , $videoId);
		
		if ($xmlContent==FALSE)
			return false;

		// Get Embeded Code
		$pattern =  "/http:\/\/media.mtvnservices.com\/mgid:uma:(.*?)\"/i";
		preg_match_all($pattern, $xmlContent, $matches);

		if( $matches[1][0] )
		{
			$path   = $matches[1][0];  
		    $getId	= explode( ':' , $matches[1][0]);
		}
	
		if($getId[0] == 'video')
		{
			$flashVars	= 'flashVars="configParams=vid=' . $getId[2];
		}
		else
		{
			$id	= explode( '=' , $videoPath[2]);
			$flashVars	= $videoPath[0]=='movie-trailers' ? NULL : 'flashVars="configParams=id=' . $id[1] . '"';

		}

		$embedCode	= '<embed src="http://media.mtvnservices.com/mgid:uma:' . $path . '" width="' . $videoWidth . '" height="' . $videoHeight . '" ' . $flashVars . '" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" base="." wmode="transparent"></embed>';
	
		return $embedCode;
	}
}
