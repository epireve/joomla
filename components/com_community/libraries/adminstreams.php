<?php
/**
 * @category	Library
 * @package		JomSocial
 * @subpackage	Photos
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
CFactory::load( 'libraries' , 'comment' );

class CAdminstreams implements CCommentInterface
{
	static function getActivityContentHTML($act)
	{
		// Ok, the activity could be an upload OR a wall comment. In the future, the content should
		// indicate which is which
		$html = '';
		CFactory::load('libraries', 'tooltip');
		$param = new CParameter( $act->params );
		$action = $param->get('action' , false);
		$count =  $param->get('count', false);
		$config = CFactory::getConfig();
		switch ($action)
		{
		    case CAdminstreamsAction::TOP_USERS:

			    $model		= CFactory::getModel( 'user' );
			    $members		= $model->getPopularMember( $count );
			    $html    = '';

			    //Get Template Page
			    $tmpl   =	new CTemplate();
			    $html   =	$tmpl	->set( 'members'    , $members )
						->fetch( 'activity.members.popular' );

			    return $html;
		    break;
		    case CAdminstreamsAction::TOP_PHOTOS:

			    $model		= CFactory::getModel( 'photos');
			    $photos		= $model->getPopularPhotos( $count , 0 );

			    $tmpl   =	new CTemplate();
			    $html   =	$tmpl	->set( 'photos'	, $photos )
						->fetch( 'activity.photos.popular' );
			    
			    return $html;
		    break;
		    case CAdminstreamsAction::TOP_VIDEOS:

			    $model		= CFactory::getModel( 'videos');
			    $videos		= $model->getPopularVideos( $count );

			    $tmpl   =	new CTemplate();
			    $html   =	$tmpl	->set( 'videos'	, $videos )
						->fetch( 'activity.videos.popular' );

			    return $html;
		    break;
		}
		
		
	}

	static public function sendCommentNotification( CTableWall $wall , $message )
	{
		
	}
}
class CAdminstreamsAction
{
	const TOP_USERS	    = 'top_users';
	const TOP_PHOTOS    = 'top_photos';
	const TOP_VIDEOS    = 'top_videos';
	
}
