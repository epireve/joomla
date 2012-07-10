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
 * Class to manipulate data from Blip
 * 	 	
 * @access	public  	 
 */
class CTableVideoBlip extends CVideoProvider
{
	var $xmlContent = null;
	var $url = '';
	
	/**
	 * Return feedUrl of the video
	 */
	public function getFeedUrl()
	{
		return $this->url.'?skin=rss';
	}
	
	/**
	 * Extract Blip video id from the video url submitted by the user
	 * 	 	
	 * @access	public
	 * @param	video url
	 * @return	videoid
	 */
	public function getId()
	{

		$videoId  = '';
		
		$pattern =  "/<blip:item_id>(.*)<\/blip:item_id>/";
		preg_match( $pattern, $this->xmlContent, $match );

		if( $match[1] ){
			$videoId    = $match[1];
		}
		
		if($videoId == ''){
			$id = explode('-',$this->url);
			$videoId = $id[count($id)-1];
		}
        						
		return $videoId;
	}
	
	/**
	 * Return the video provider's name
	 * 
	 */
	public function getType()
	{
		return 'blip';
	}
	
	public function getTitle()
	{
		$title = '';		
		// Store video title
		$pattern =  "/<title>(.*)<\/title>/i";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$title = $matches[1][1];
                        if(empty($title)){
                            $title = $matches[1][2];
                        }
		}
		
		return $title;
	}
	
	public function getDescription()
	{
		$description = '';
		// Store description
		$pattern =  "'<blip\:puredescription>(.*?)<\/blip\:puredescription>'s"; 
		preg_match_all($pattern, $this->xmlContent, $matches);  

		if($matches)
		{
			$description = CString::str_ireplace( '&apos;' , "'" , $matches[1][0] );
			$description = CString::str_ireplace( '<![CDATA[', '', $description );
			$description = CString::str_ireplace( ']]>', '', $description );    
		}
		
		return $description;
	}
	
	public function getDuration()
	{
		$duration = '';
		// Store duration
		$pattern =  "'<blip:runtime>(.*?)<\/blip:runtime>'s";
		preg_match_all($pattern, $this->xmlContent, $matches);
		if($matches)
		{
			$duration = $matches[1][0];
		}
			
		return $duration;
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
		$pattern =  "'<media:thumbnail url=\"(.*?)\"'s";			 
		preg_match_all($pattern, $this->xmlContent, $matches); 

		if( !empty($matches[1][0]) )
		{
			$thumbnail = $matches[1][0];
		}
		else
		{     
			$thumbnail = 'http://a.blip.tv/skin/blipnew/placeholder_video.gif';
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
		$remoteFile	= 'http://blip.tv/file/'.$videoId.'?skin=rss';
		$xmlContent = CRemoteHelper::getContent($remoteFile);
		// get embedFile
		$pattern	= "'<blip:embedLookup>(.*?)<\/blip:embedLookup>'s";
		$embedFile	= '';
		preg_match_all($pattern, $xmlContent, $matches);
		if($matches)
		{
			$embedFile = $matches[1][0];
		}	
		
		return '<object width="'.$videoWidth.'" height="'.$videoHeight.'"><param name="movie" value="http://blip.tv/play/'.$embedFile.'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://blip.tv/play/'.$embedFile.'" type="application/x-shockwave-flash" width="'.$videoWidth.'" height="'.$videoHeight.'" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"></embed></object>';
	}
	
	
	public function getEmbedCode($videoId, $videoWidth, $videoHeight)
	{
		return $this->getViewHTML($videoId, $videoWidth, $videoHeight);
	}
}
