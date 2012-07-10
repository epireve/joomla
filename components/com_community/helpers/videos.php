<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CVideosHelper
{
	static public function validateVideo( $fileName )
	{
		jimport('joomla.filesystem.file');
		$fileExt	= JFile::getExt($fileName);
		
		$fileType	= array('flv', 'avi', 'mov', 'mp4'); // need expansion
		
		return in_array($fileExt, $fileType);
	}

	static public function formatDuration($duration = 0, $format = 'HH:MM:SS')
	{
		if ($format == 'seconds' || $format == 'sec') {
			$arg = explode(":", $duration);
	
			$hour	= isset($arg[0]) ? intval($arg[0]) : 0;
			$minute	= isset($arg[1]) ? intval($arg[1]) : 0;
			$second	= isset($arg[2]) ? intval($arg[2]) : 0;
	
			$sec = ($hour*3600) + ($minute*60) + ($second);
			return (int) $sec;
		}
	
		if ($format == 'HH:MM:SS' || $format == 'hms') {
			$timeUnits = array
			(
				'HH' => $duration / 3600 % 24,
				'MM' => $duration / 60 % 60,
				'SS' => $duration % 60
			);
	
			$arg = array();
			foreach ($timeUnits as $timeUnit => $value) {
				$arg[$timeUnit] = ($value > 0) ? $value : 0;
			}
	
			$hms = '%02s:%02s:%02s';
			$hms = sprintf($hms, $arg['HH'], $arg['MM'], $arg['SS']);
			return $hms;
		}
	}

	/**
	 *	Remove Extra Leading Zeroes
	 *	00:01:30 will became 01:30
	 *	
	 *	@params	string	$hms	HH:MM:SS value
	 *	@return	string	nice HMS
	 */ 
	static public function toNiceHMS($hms)
	{
		$arr	= array();
		$arr	= explode(':', $hms);
	
		if ($arr[0] == '00') {
			array_shift($arr);
		}
	
		return implode(':', $arr);
	}
	
	static public function getVideoLinkPatterns()
	{
		// Pattern for video providers
		$pattern	= array();
	
		$pattern[] = '/http\:\/\/vids.myspace.com\/index.cfm\?fuseaction\=([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&]*)\=(\d{1,8})/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?youtube.com\/watch\?v\=([_-])?([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-]*)/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?vimeo.com\/(hd#)?(\d*)/';
		$pattern[] = '/http\:\/\/(\w{2}\.)?video.yahoo.com\/watch\/(\d{1,8})\/(\d{1,8})/';
		$pattern[] = '/http\:\/\/video.google.(\w{2,4})\/videoplay\?docid=(-?\d{1,19})(&.*)?/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?revver.com\/video\/(\d{1,7})\/([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-]*)/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?flickr.com\/photos\/(.*)\/(\d{1,10})/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?viddler.com\/explore\/(.*)\/videos\/(\d{1,3})\//';
		$pattern[] = '/http\:\/\/(\w{3}\.)?liveleak.com\/view\?i\=([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-]*)/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?break.com\/index\/(.*?).html/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?dailymotion.com\/(.*)\/video\/(.*)/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?blip.tv\/file\/(\d{1,7})?([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-]*)/';
		$pattern[] = '/http\:\/\/(\w{3}\.)?metacafe.com\/watch\/(\d{1,7})?([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-]*)/';
		$pattern[] = '/http\:\/\/(media\.)?photobucket.com\/video\/([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/\?:@&~=%-\s]*)/';
		
		return $pattern;
	}

	static public function getVideoLinkMatches( $content )
	{
		$pattern	= array();
		$matches	= array();
		
		$pattern	= CVideosHelper::getVideoLinkPatterns();
	
		for( $i = 0; $i < count( $pattern ); $i++ )
		{
			//Match the first video link
			preg_match($pattern[$i], $content, $match );
	
			if( $match )
			{
				$matches[]	= $match[0];
			}
			
		}
		
		return $matches;
	}

	static public function getVideoLink($content, $videoWidth='425', $videoHeight='344')
	{
		$pattern	= array();
		$videoLinks	= array();
		
		$pattern	= CVideosHelper::getVideoLinkPatterns();
	
		for( $i = 0; $i < count( $pattern ); $i++ )
		{
			//Match all video links
			preg_match_all($pattern[$i], $content, $match );
	
			if( $match )
			{
				$videoLinks[]	= $match[0];
			}
			
		}
		
		foreach($videoLinks as $videoLink)
		{
			// Replace the URL with the embedded code
			foreach($videoLink as $videoLinkUrl)
			{		
				$parsedVideoLink	= parse_url($videoLinkUrl);
				preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $parsedVideoLink['host'], $matches);
				$domain	= $matches['domain'];
			
				if (!empty($domain))
				{
					$provider		= explode('.', $domain);
					$providerName	= JString::strtolower($provider[0]);
					$libraryPath	= COMMUNITY_COM_PATH . DS . 'libraries' . DS . 'videos' . DS . $providerName . '.php';
					
					require_once($libraryPath);
					$className		= 'CTableVideo' . JString::ucfirst($providerName);
					$videoObj		= new $className();
					$videoObj->init($videoLinkUrl);
					$video_id		= $videoObj->getId();
					$videoPlayer	= $videoObj->getViewHTML($video_id, $videoWidth, $videoHeight);
					$content = str_replace( $videoLinkUrl, $videoPlayer, $content );
				}
			}
		}
		
		return $content;
	}

	static public function getVideoReturnUrlFromRequest($videoType='default')
	{
		$creator_type	= JRequest::getVar( 'creatortype' , VIDEO_USER_TYPE );
		$groupId		= JRequest::getInt( 'groupid' , 0 );
		$my				= JFactory::getUser();
		
		// we use this if redirect url is defined
		$redirectUrl	= JRequest::getVar( 'redirectUrl' , '' , 'POST' );
		if (!empty($redirectUrl))
		{
			return base64_decode($redirectUrl);
		}
		
		if ($creator_type == VIDEO_GROUP_TYPE || !empty($groupId))
		{
			$defaultUrl	= CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $groupId , false );
			$pendingUrl	= CRoute::_('index.php?option=com_community&view=videos&task=mypendingvideos&userid='.$my->id.'&groupid='.$groupId, false);
			return ($videoType == 'pending') ? $pendingUrl : $defaultUrl;
		}
		
		$defaultUrl	= CRoute::_('index.php?option=com_community&view=videos&task=myvideos&userid=' . $my->id , false );
		$pendingUrl	= CRoute::_('index.php?option=com_community&view=videos&task=mypendingvideos&userid='.$my->id, false);
		return ($videoType == 'pending') ? $pendingUrl : $defaultUrl;
	}

	static public function getVideoSize($retunType='default', $displayType='display')
	{
		$config		= CFactory::getConfig();
		
		switch ($displayType)
		{
			case 'wall':
				$videoSize	= $config->get('wallvideossize');
				break;
			case 'activities':
				$videoSize	= $config->get('activitiesvideosize');
				break;
			case 'display':
			default:
				$videoSize	= $config->get('videosSize');
				break;
		}
		
		$arrVideoSize	= array();
		$arrVideoSize	= explode('x', $videoSize, 2);
		
		switch ($retunType)
		{
			case 'width':
				$ret	= $arrVideoSize[0];
				break;
			case 'height':
				$ret	= $arrVideoSize[1];
				break;
			default:
				$ret	= $videoSize;
				break;
		}
		
		return $ret;
	}
	
	static public function getValidMIMEType()
	{
		$mimeType	= array(
			'video/x-flv',
			'video/mpeg',
			'video/mp4',
			'video/ogg',
			'video/quicktime',
			'video/x-ms-wmv',
			'video/3gpp',
			'video/x-msvideo',
			'video/x-dv',
			'video/x-m4v',
			'video/x-sgi-movie',
			'video/3gpp',
			'video/3gpp2',
			'video/x-la-asf',
			'video/x-ms-asf',
			'video/animaflex',
			'video/avi',
			'video/msvideo',
			'video/avs-video',
			'video/fli',
			'video/x-fli',
			'video/gl',
			'video/x-gl',
			'video/x-isvideo',
			'video/x-motion-jpeg',
			'video/x-mpeg',
			'video/x-mpeq2a',
			'video/x-qtc',
			'video/vnd.rn-realvideo',
			'video/x-scm',
			'video/vdo',
			'video/vivo',
			'video/vnd.vivo',
			'video/vosaic',
			'video/x-amt-demorun',
			'video/x-amt-showrun',
			'video/H261',
			'video/H263',
			'video/H263-1998',
			'video/H263-2000',
			'video/H264',
			'video/JPEG',
			'video/dl',
			'video/x-mng',
			'video/x-ms-wm',
			'video/x-ms-wmx',
			'video/x-ms-wvx'
		);
		return $mimeType;
	}
	
	public static $rand_agent=true;
	// Default curl options
	public static $default_options = array
	(
		CURLOPT_USERAGENT => "CnVideoApi (+http://www.liushan.net;version:0.11)",
		CURLOPT_CONNECTTIMEOUT => 5,
		CURLOPT_TIMEOUT        => 200,
	);

	private static function _rand_agent(){
		$useragent_arr=array(
			"CnVideoApi (+http://www.liushan.net;version:0.11)",
			"Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 6.1;)",
			"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 6.1;)",
			"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1;)",
			"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1;)",
			"Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1;)",
			"Mozilla/5.0 (Windows; U; Windows NT 6.1; zh-CN; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.13",
			"Baiduspider+(+http://www.baidu.com/search/spider.htm)",
			"Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
			"Googlebot-Image/1.0",
			"Feedfetcher-Google; (+http://www.google.com/feedfetcher.html;)",
			"Mozilla/5.0 (compatible; Yahoo! Slurp China; http://misc.yahoo.com.cn/help.html)",
			"Mozilla/5.0 (compatible; YodaoBot/1.0; http://www.yodao.com/help/webmaster/spider/;)" ,
			"Sosospider+(+http://help.soso.com/webspider.htm)",
			"Sogou Web Sprider(compatible; Mozilla 4.0; MSIE 6.0; Windows NT 5.1; SV1; Avant Browser; InfoPath.1; .NET CLR 2.0.50727; .NET CLR1.1.4322)",
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13; InfoPath.1; .NET CLR 2.0.50727; .NET CLR1.1.4322)",
		);
		self::$default_options[CURLOPT_USERAGENT]=$useragent_arr[rand(0,(count($useragent_arr)-1))];
	}

	/**
	 * Returns the output of a remote URL. Any [curl option](http://php.net/curl_setopt)
	 * may be used.
	 *
	 *     // Do a simple GET request
	 *     $data = Remote::get($url);
	 *
	 *     // Do a POST request
	 *     $data = Remote::get($url, array(
	 *         CURLOPT_POST       => TRUE,
	 *         CURLOPT_POSTFIELDS => http_build_query($array),
	 *     ));
	 *
	 * @param   string   remote URL
	 * @param   array    curl options
	 * @return  string
	 * @throws  Videoapi_Exception
	 */
	public static function getVideoInfo($url, array $options = NULL)
	{
		if(self::$rand_agent){
			self::_rand_agent();
		}
		if ($options === NULL)
		{
			// Use default options
			$options = self::$default_options;
		}
		else
		{
			// Add default options
			$options = $options + self::$default_options;
		}

		// The transfer must always be returned
		$options[CURLOPT_RETURNTRANSFER] = TRUE;

		// Open a new remote connection
		$remote = curl_init($url);

		// Set connection options
		if ( ! curl_setopt_array($remote, $options))
		{
			throw new Videoapi_Exception("Failed to set CURL options, check CURL documentation:  http://php.net/curl_setopt_array");
		}

		// Get the response
		$response = curl_exec($remote);

		// Get the response information
		$code = curl_getinfo($remote, CURLINFO_HTTP_CODE);

		if ($code AND $code < 200 OR $code > 299)
		{
			$error = $response;
		}
		elseif ($response === FALSE)
		{
			$error = curl_error($remote);
		}

		// Close the connection
		curl_close($remote);
		if (isset($error))
		{
			//error
		}
		return $response;
	}
}

/**
 * Deprecated since 1.8
 */
function cValidateVideo($fileName)
{
	return CVideosHelper::validateVideo( $fileName );
}

/**
 * Deprecated since 1.8
 */
function cFormatDuration ($duration = 0, $format = 'HH:MM:SS')
{
	return CVideosHelper::formatDuration( $duration , $format );
}

/**
 * Deprecated since 1.8
 */
function cToNiceHMS($hms)
{
	return CVideosHelper::toNiceHMS( $hms );
}

/**
 * Deprecated since 1.8
 */
function cGetVideoLinkPatterns()
{
	return CVideosHelper::getVideoLinkPatterns();
}

/**
 * Deprecated since 1.8
 */
function CGetVideoLinkMatches( $content )
{
	return CVideosHelper::getVideoLinkMatches( $content );
}

/**
 * Deprecated since 1.8
 */
function cGetVideoLink($content, $videoWidth='425', $videoHeight='344')
{
	return CVideosHelper::getVideoLink($content, $videoWidth , $videoHeight);
}

/**
 * Deprecated since 1.8
 */
function cGetVideoReturnUrlFromRequest($videoType='default')
{
	return CVideosHelper::getVideoReturnUrlFromRequest( $videoType );
}

/**
 * Deprecated since 1.8
 */
function cGetVideoSize($retunType='default', $displayType='display')
{
	return CVideosHelper::getVideoSize( $retunType , $displayType );
}