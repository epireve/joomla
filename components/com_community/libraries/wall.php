<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php');
require_once(JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'template.php');

class CWall
{
	public function _processWallContent($comment)
	{
		// Convert video link to embedded video
		CFactory::load('helpers' , 'videos');
		$comment = CVideosHelper::getVideoLink($comment);
		
		return $comment;
	}
	
	/**
	 * Method to get the walls HTML form
	 * 
	 * @param	userId
	 * @param	uniqueId
	 * @param	appType
	 * @param	$ajaxFunction	Optional ajax function
	 **/	 	
	public function getWallInputForm( $uniqueId , $ajaxAddFunction, $ajaxRemoveFunc, $viewAllLink ='' )
	{
		$my = CFactory::getUser();
		
		// Hide the input form completely from visitors
		if($my->id == 0)
			return '';

		$tmpl		= new CTemplate();

		return $tmpl->set( 'uniqueId'		, $uniqueId )
					->set( 'viewAllLink'	, $viewAllLink )
					->set( 'ajaxAddFunction'	, $ajaxAddFunction )
					->set( 'ajaxRemoveFunc'	, $ajaxRemoveFunc)
					->fetch( 'wall.form' );	
	}

	public function saveWall( $uniqueId , $message , $appType , &$creator , $isOwner , $processFunc = '', $templateFile = 'wall.content' , $wallId = 0 )
	{
		$my = CFactory::getUser();

		// Add some required parameters, otherwise assert here
		CError::assert( $uniqueId, '' , '!empty' , __FILE__ , __LINE__ );
		CError::assert( $appType, '' , '!empty' , __FILE__ , __LINE__ );
		CError::assert( $message, '' , '!empty' , __FILE__ , __LINE__ );
		CError::assert( $my->id, '', '!empty' , __FILE__ , __LINE__ );

		// Load the models
		CFactory::load( 'models' , 'wall' );
		CFactory::load( 'helpers' , 'url' );
		$wall				=& JTable::getInstance( 'Wall' , 'CTable' );
		$wall->load( $wallId );

		
		if( $wallId == 0 )
		{	
			// Get current date
			$now				=& JFactory::getDate();
			$now				= $now->toMySQL();
			
			// Set the wall properties
			$wall->type			= $appType;
			$wall->contentid	= $uniqueId;
			$wall->post_by		= $creator->id;
			
			$wall->date			= $now;
			$wall->published	= 1;
			
			// @todo: set the ip address
			$wall->ip			= $_SERVER['REMOTE_ADDR'];
		}
		$wall->comment		= $message;
 
 		$filter			= CFactory::getInputFilter();
 		$wall->comment	= $filter->clean( $wall->comment );
		   		
		// Store the wall message
		$wall->store();

		// Convert it to array so that the walls can be processed by plugins
		$args 			= array();
		$args[0]		=& $wall;

		//Process wall comments
		CFactory::load('libraries', 'comment');
		$comment		= new CComment();
		$wallComments	= $wall->comment;
		$wall->comment  = $comment->stripCommentData($wall->comment);
			
		// Trigger the wall comments
		CWall::triggerWallComments( $args );
		
		$wallData	= new stdClass();
		
		$wallData->id		= $wall->id;
		$wallData->content	= CWallLibrary::_getWallHTML( $wall , $wallComments , $appType, $isOwner , $processFunc , $templateFile );

		CFactory::load( 'helpers' , 'string' );
		$wallData->content	= CStringHelper::replaceThumbnails($wallData->content);
				
		return $wallData;
	}
	
	/**
	 *
	 * @param <type> $act 
	 */
	public static function getActivityContentHTML($act)
	{
		CFactory::load('libraries', 'comment');
		CFactory::getModel( 'wall' );
		$config   = CFactory::getConfig();

		$wall =& JTable::getInstance( 'Wall' , 'CTable' );
		$wall->load( $act->cid );

		$comment = new CComment();
		//$wall->comment = $comment->stripCommentData($wall->comment);

		// Trigger the wall applications / plugins
		$walls = array();
		$walls[] = $wall;
		CWall::triggerWallComments( $walls );
		
		$wall->comment = CWallLibrary::_getWallHTML( $wall , null , 'profile', true, null, 'wall.content' );

		$tmpl	= new CTemplate();
		return $tmpl->set( 'comment' , $wall->comment )
					->fetch( 'activity.wall.post' );
		
	}

	/**
	 * Return html-free summary of the wall content
	 */	 	
	public static function getWallContentSummary($wallId)
	{
		CFactory::load('libraries', 'comment');
		CFactory::getModel( 'wall' );
		$config   = CFactory::getConfig();
		
		$wall =& JTable::getInstance( 'Wall' , 'CTable' );
		$wall->load( $wallId );
		
		$comment = new CComment();
		$wall->comment = $comment->stripCommentData($wall->comment);
		
		$tmpl	= new CTemplate();
		return $tmpl->set( 'comment' , JString::substr($wall->comment, 0, $config->getInt('streamcontentlength')) )
					->fetch( 'activity.wall.post' );
	}

	public function canComment( $appType , $uniqueId )
	{
		$my			= CFactory::getUser();
		$allowed	= false;
		
		switch( $appType )
		{
			case 'groups':
				$group	=& JTable::getInstance( 'Group' , 'CTable' );
				$group->load( $uniqueId );
				
				$allowed	= $group->isMember( $my->id );
				break;
			default:
				$allowed	= true;
				break;
		}
		return $allowed;
	}
	
	/**
	 * Fetches the wall content template and returns the wall data in HTML format
	 *
	 * @param	appType			The application type to load the walls from
	 * @param	uniqueId		The unique id for the specific application	 
	 * @param	isOwner			Boolean value if the current browser is owner of the specific app or profile
	 * @param	limit			The limit to display the walls
	 * @param	templateFile	The template file to use.
	 **/	 	
	public function getWallContents( $appType , $uniqueId , $isOwner , $limit = 0 , $limitstart = 0, $templateFile = 'wall.content' , $processFunc = '', $param = null, $banned = 0)
	{
		CError::assert( $appType , '' , '!empty' , __FILE__ , __LINE__ );
		CError::assert( $uniqueId , '' , '!empty' , __FILE__ , __LINE__ );

		$config   = CFactory::getConfig();
		
		$html	= '<div id="wall-containter">';
		$model	= CFactory::getModel( 'wall' );
		
		//@rule: If limit is not set, then we need to use Joomla's limit
		if( $limit == 0 )
		{
			$jConfig	=& JFactory::getConfig();
			$limit		= $jConfig->getValue( 'list_limit' );
		}

		// Special 'discussions'
		$order = 'DESC';
		//if($appType == 'discussions'){
			//$order = $config->get('group_discuss_order');
			//$discussionsTrigger = true;
		//}
		
		$walls	= $model->getPost( $appType , $uniqueId , $limit, $limitstart, $order);
		
		// Special 'discussions'
		$discussionsTrigger = false;
		$order = $config->get('group_discuss_order');
		if(($appType == 'discussions') && ($order == 'ASC'))
		{
			$walls	= array_reverse($walls);
			$discussionsTrigger = true;
		}

		if( $walls )
		{
			//Process wall comments
			CFactory::load('libraries', 'comment');
			$wallComments	= array();
			$comment		= new CComment();
			
			for( $i = 0 ; $i < count( $walls ); $i++ )
			{
				// Set comments
				$wall			= $walls[ $i ];
				$wallComments[]	= $wall->comment;
				$wall->comment  = $comment->stripCommentData($wall->comment);
				
				// Change '->created to lapse format if stream uses lapse format'
				if( $config->get('activitydateformat') == 'lapse') 
				{
					//$wall->date = CTimeHelper::timeLapse($wall->date);
				}
			}
 			
			 // Trigger the wall applications / plugins
			CWall::triggerWallComments( $walls );
			
			for( $i = 0; $i < count( $walls ); $i++ )
			{
				if($banned==1){
					$html	.= CWallLibrary::_getWallHTML( $walls[$i] , $wallComments[ $i ] , $appType , $isOwner , $processFunc , $templateFile , $banned);
				} else {
					$html	.= CWallLibrary::_getWallHTML( $walls[$i] , $wallComments[ $i ] , $appType , $isOwner , $processFunc , $templateFile );
				}
			}
			
			if($appType == 'discussions')
			{
				$wallCount		= CWallLibrary::getWallCount('discussions', $uniqueId);
				$limitStart		= $limitstart + $limit;
				//$limitStart		= $wallCount - $limit;
				//$limitStart		= $limit;
				
				if( $wallCount > $limitStart )
				{
					$groupId	= JRequest::getInt('groupid');
					$groupId	= empty($groupId) ? $param : $groupId;
					
					if($discussionsTrigger)
					{
						$html		= CWall::_getOlderWallsHTML($groupId, $uniqueId, $limitStart) . $html;
					} else {
						$html		.= CWall::_getOlderWallsHTML($groupId, $uniqueId, $limitStart);	
					}
				}
			}
		}
		
		$html	.= '</div>';
		
		return $html;
	}
	
	public function _getOlderWallsHTML($groupId, $discussionId, $limitStart)
	{
		$config	= CFactory::getConfig();
		$order	= $config->get('group_discuss_order');
		$buttonText = '';
		
		$buttonText	= ($order == 'ASC') ?  JText::_('COM_COMMUNITY_GROUPS_OLDER_WALL') : JText::_('COM_COMMUNITY_MORE');
		
		ob_start();
		?>
				<div class="joms-newsfeed-more" id="wall-more">
					<a class="more-wall-text" href="javascript:void(0);" onclick="joms.walls.more();"><?php echo $buttonText;?></a>
					<div class="loading"></div>
				</div>
				<input type="hidden" id="wall-groupId" value="<?php echo $groupId;?>" />
				<input type="hidden" id="wall-discussionId" value="<?php echo $discussionId;?>" />
				<input type="hidden" id="wall-limitStart" value="<?php echo $limitStart;?>" />
		<?php
		$moreWalls	= ob_get_contents();
		ob_end_clean();

		return $moreWalls;
	}
	
	public function _getWallHTML( $wall , $wallComments , $appType , $isOwner , $processFunc , $templateFile, $banned = 0)
	{
		CFactory::load( 'helpers' , 'url' );
		CFactory::load( 'helpers' , 'user');
		CFactory::load( 'helpers' , 'videos');
		CFactory::load( 'libraries' , 'comment' );
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'helpers' , 'time' );
		
		$user	= CFactory::getUser( $wall->post_by );
		$date	= CTimeHelper::getDate( $wall->date );

		$config			= CFactory::getConfig();
		
		// @rule: for site super administrators we want to allow them to view the remove link
		$isOwner	= COwnerHelper::isCommunityAdmin() ? true : $isOwner;
		$isEditable	= CWall::isEditable( $processFunc , $wall->id );

		// Apply any post processing on the content 
		$wall->comment = CWallLibrary::_processWallContent($wall->comment);
		$commentsHTML	= '';
		
		$comment		= new CComment();
		// If the wall post is a user wall post (in profile pages), we 
		// add wall comment feature
		if( $appType == 'user' || $appType == 'groups' || $appType == 'events' )
		{
			if($banned==1){
				$commentsHTML	= $comment->getHTML( $wallComments , 'wall-cmt-'.$wall->id , false );
			} else {
				$commentsHTML	= $comment->getHTML( $wallComments , 'wall-cmt-'.$wall->id , CWall::canComment( $wall->type , $wall->contentid ) );
			}
		}

		$avatarHTML		= CUserHelper::getThumb( $wall->post_by , 'avatar' );
		
		//var_dump($avatarHTML);exit;

		// @rule: We only allow editing of wall in 15 minutes
		$now			= JFactory::getDate();
		$interval		= CTimeHelper::timeIntervalDifference( $wall->date , $now->toMySQL() );
		$interval		= COMMUNITY_WALLS_EDIT_INTERVAL - abs( $interval );
		$editInterval	= round( $interval / 60 );
		
		// Change '->created to lapse format if stream uses lapse format'
		if( $config->get('activitydateformat') == 'lapse') 
		{
			$wall->created = CTimeHelper::timeLapse($date);
		} else
		{
			$wall->created = (C_JOOMLA_15==1)?$date->toFormat( JText::_('DATE_FORMAT_LC2') ):$date->Format( JText::_('DATE_FORMAT_LC2') );
		}
		
		
		// Create new instance of the template
		$tmpl	= new CTemplate();
		return $tmpl->set( 'id'		, $wall->id )
					->set( 'author'	, $user->getDisplayName() )
					->set( 'avatarHTML', $avatarHTML )
					->set( 'authorLink', CUrlHelper::userLink( $user->id ) )
					->set( 'created'	, $wall->created )
					->set( 'content'	, $wall->comment)
					->set( 'commentsHTML'	, $commentsHTML )
					->set( 'avatar'		, $user->getThumbAvatar() )
					->set( 'isMine'		, $isOwner )
					->set( 'isEditable'	, $isEditable )
					->set( 'editInterval'	, $editInterval )
					->set( 'processFunc'	, $processFunc )
					->set( 'config'	, $config )
					->fetch( $templateFile );
	}
	
	public function getViewAllLinkHTML($link, $count=null)
	{
		if (!$link) return '';
		
		$tmpl = new CTemplate();
		return $tmpl->set( 'viewAllLink', $link )
					->set( 'count', $count )
					->fetch('wall.misc');
	}
	
	public function getWallCount($appType , $uniqueId)
	{
		$model	= CFactory::getModel( 'wall' );
		$count	= $model->getCount($uniqueId , $appType);
		return $count;
	}
	
	/**
	 * @todo: change this to a simple $my->authorise
	 * @param type $processFunc
	 * @param type $wallId
	 * @return type 
	 */
	public function isEditable( $processFunc , $wallId )
	{
		$func	= explode( ',' , $processFunc );
		
		if( count($func) < 2 )
		{
			return false;
		}
		
		$controller	= $func[0];
		$method		= 'edit' . $func[1] . 'Wall';

		if( count( $func ) > 2 )
		{
			//@todo: plugins
		}
		
		return CWall::_callFunction( $controller , $method , array( $wallId ) );
	}
	
	public function _checkWallFunc( $processFunc )
	{

	}
	
	public function _callFunction( $controller , $method , $arguments )
	{
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'controllers' . DS . 'controller.php' );
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'controllers' . DS . JString::strtolower( $controller ) . '.php' );

		$controller	= JString::ucfirst( $controller );
		$controller	= 'Community' . $controller . 'Controller';
		$controller	= new $controller();

		// @rule: If method not exists, we need to do some assertion here.
		if( !method_exists( $controller, $method ) )
		{
			JError::raiseError( 500 , JText::_( 'Method not found' ) );
		}

		return call_user_func_array( array( $controller , $method ) , $arguments );
	}
		
	public function addWallComment($type, $cid, $comment)
	{
		$my 	= CFactory::getUser();
		$table 	= JTable::getInstance('CTable', 'Wall');
		
		$table->contentid = $cid;
		$table->type = $type;
		$table->comment = $comment;
		$table->post_by = $my->id;
		
		$table->store();
		return $table->id;
	}
	
	/**
	 * Formats the comment in the rows
	 * 
	 * @param Array	An array of wall objects	 	 
	 **/	 
	public function triggerWallComments( &$rows )
	{
		CError::assert( $rows , 'array', 'istype', __FILE__ , __LINE__ );
		
		require_once( COMMUNITY_COM_PATH.DS.'libraries' . DS . 'apps.php' );
		$appsLib	=& CAppPlugins::getInstance();
		$appsLib->loadApplications();
		
		for( $i = 0; $i < count( $rows ); $i++ )
		{
			if (isset($rows[$i]->comment) && (!empty($rows[$i]->comment))){
				$args 	= array();
				$args[]	=& $rows[ $i ];

				$appsLib->triggerEvent( 'onWallDisplay' , $args );
			}
		}
		return true;
	}
	
	/**
	 * Return formatted comment given the wall item
	 */
	public static function formatComment($wall){

		CFactory::load( 'helpers' , 'owner' );
		$my = CFactory::getUser();
		
		// Save processing time
		if (!$wall->comment) return '';
		
		// strip out the comment data
		CFactory::load('libraries', 'comment');
		$wall->comment	= CComment::stripCommentData($wall->comment);
		
		// Need to perform basic formatting here
		// 1. support nl to br,
		// 2. auto-link text
		CFactory::load('helpers', 'linkgenerator');
		$wall->comment = CTemplate::escape($wall->comment);
		$wall->comment = CLinkGeneratorHelper::replaceURL($wall->comment);
		$wall->comment = nl2br($wall->comment);
		
		CFactory::load('helpers', 'time');
		$user = CFactory::getUser($wall->post_by);
		$commentsHTML  = '';
		$commentsHTML .= '<div class="cComment wall-coc-item" id="wall-'.$wall->id.'"><a href="'.CUrlHelper::userLink( $user->id ).'"><img src="'.$user->getThumbAvatar().'" alt="" class="wall-coc-avatar" /></a>';
		$date = new JDate($wall->date);
		$commentsHTML .= '<a class="wall-coc-author" href="'.CUrlHelper::userLink( $user->id ).'">'.$user->getDisplayName().'</a> ';
		$commentsHTML .= $wall->comment;
		$commentsHTML .= '<span class="wall-coc-time">'.CTimeHelper::timeLapse($date);

		// Only site admin, or wall author can remove it
		// @todo: the target stream activity should also be able to delete it
		if( COwnerHelper::isCommunityAdmin() || $my->id == $wall->post_by )
		{
			$commentsHTML .= ' <span class="wall-coc-remove-link">&#x2022; <a onclick="joms.miniwall.remove('.$wall->id.');" href="#removeThisComment">'.JText::_('COM_COMMUNITY_WALL_REMOVE').'</a></span>';
		}
		$commentsHTML .= '</span>';
    
		$commentsHTML .='</div>';
		
		return $commentsHTML;
				
	}
}

/**
 * Maintain classname compatibility with JomSocial 1.6 below
 */ 
class CWallLibrary extends CWall
{}