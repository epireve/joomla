<?php
/**
 * @category	Plugins
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');

if(!class_exists('plgCommunityWalls'))
{
	class plgCommunityWalls extends CApplications
	{
		var $name		= 'Walls';
		var $_name		= 'walls';
		
	    function plgCommunityWalls(& $subject, $config)
	    {
			parent::__construct($subject, $config);
	    }


		function onActivityContentDisplay( $args )
		{
			$model	=& CFactory::getModel( 'Wall' );
			$wall	=& JTable::getInstance( 'Wall' , 'CTable' );
			$my		= CFactory::getUser();

			if(empty($args->content) )
				return '';

			$wall->load( $args->cid );
			CFactory::load( 'libraries' , 'privacy' );
			CFactory::load( 'libraries' , 'comment' );
			
			$comment	= CComment::stripCommentData( $wall->comment );
			$config		= CFactory::getConfig();
			
			$commentcut = false;
			if (strlen($comment) > $config->getInt('streamcontentlength')) {
				$origcomment = $comment;
				$comment = JString::substr( $comment , 0 , $config->getInt('streamcontentlength') ). ' ...';
				$commentcut = true;
			}
			
			if( CPrivacy::isAccessAllowed($my->id, $args->target, 'user', 'privacyProfileView') )
			{
				CFactory::load('helpers' , 'videos');
				CFactory::load('libraries', 'videos');
				CFactory::load( 'libraries' , 'wall');			
				$videoContent	= '';
				$params			= new CParameter( $args->params );
				$videoLink		= $params->get('videolink');
				$image			= $params->get('url');

				// For older activities that does not have videoLink , we need to process it the old way.
				if( !$videoLink )
				{
					$html	= CWallLibrary::_processWallContent( $comment );
					$tmpl	= new CTemplate();
					$html	= CStringHelper::escape( $html );
					
					if($commentcut){ //add read more/less link for content
						$html .= '<br /><br /><a href="javascript:void(0)" onclick="jQuery(\'#shortcomment_'.$args->cid.'\').hide(); jQuery(\'#origcomment_'.$args->cid.'\').show();" >'.JText::_('COM_COMMUNITY_READ_MORE').'</a>';
						$html  = '<div id="shortcomment_'.$args->cid.'">' . $html . '</div>';
						$html .= '<div id="origcomment_'.$args->cid.'" style="display:none;">'.$origcomment.'<br /><br /><a href="javascript:void(0);" onclick="jQuery(\'#shortcomment_'.$args->cid.'\').show(); jQuery(\'#origcomment_'.$args->cid.'\').hide();" >'.JText::_('COM_COMMUNITY_READ_LESS').'</a></div>';
					}
					
					$tmpl->set( 'comment' , $html );
					$html	= $tmpl->fetch( 'activity.wall.post' );
				}
				else
				{
					$html  = '<ul class ="cDetailList clrfix">';
					$html .= '<li>';
					$image = (!$image) ? rtrim( JURI::root() , '/' ) . '/components/com_community/assets/playvideo.gif' : $image; 

					$videoLib 	= new CVideoLibrary();					
					$provider	= $videoLib->getProvider($videoLink);

					$html 			.= '<!-- avatar --><div class="avatarWrap"><a href="javascript:void(0);" onclick="joms.activities.showVideo(\'' . $args->id . '\');"><img width="64" src="'. $image . '" class="cAvatar"/></a></div><!-- avatar -->';
					$videoPlayer	= $provider->getViewHTML( $provider->getId() , '300' , '300' );
					$comment		= CString::str_ireplace( $videoLink , '' , $comment );
					$html 			.= '<!-- details --><div class="detailWrap alpha">'. $comment .'</div><!-- details -->';
					
					if( !empty( $videoPlayer ) )
					{
						$html	.= '<div style="display: none;clear: both;padding-top: 5px;" class="video-object">' . $videoPlayer . '</div>';
					}
									
					$html .= '</li>';
					$html .= '</ul>';
				}
				
				return $html;

			}
		}
		
		/**
		 * Ajax function to save a new wall entry
		 * 	 
		 * @param message	A message that is submitted by the user
		 * @param uniqueId	The unique id for this group
		 * 
		 **/	 	 	 	 	 		
		function ajaxSaveWall( $response, $message , $uniqueId, $cache_id="" )
		{
			$my				= CFactory::getUser();
			$user			= CFactory::getUser( $uniqueId );
			$config			= CFactory::getConfig();
			
			JPlugin::loadLanguage('plg_walls', JPATH_ADMINISTRATOR);
			
			// Load libraries
			CFactory::load( 'models' , 'photos' );
			CFactory::load( 'libraries' , 'wall' );
			CFactory::load( 'helpers' , 'url' );
			CFactory::load( 'libraries', 'activities' );	
	
			$message	= JString::trim($message);
			$message	= strip_tags( $message );

			if( empty( $message ) )
			{
				$response->addAlert( JText::_('PLG_WALLS_PLEASE_ADD_MESSAGE') );
			}
			else
			{
				$maxchar = $this->params->get('charlimit', 0);
				if(!empty($maxchar))
				{
					$msglength = strlen($message);
					if($msglength > $maxchar)
					{
						$message = substr($message, 0, $maxchar);	
					}	
				}						

				// @rule: Spam checks
				if( $config->get( 'antispam_akismet_walls') )
				{
					CFactory::load( 'libraries' , 'spamfilter' );
		
					$filter				= CSpamFilter::getFilter();
					$filter->setAuthor( $my->getDisplayName() );
					$filter->setMessage( $message );
					$filter->setEmail( $my->email );
					$filter->setURL( CRoute::_('index.php?option=com_community&view=profile&userid=' . $user->id) );
					$filter->setType( 'message' );
					$filter->setIP( $_SERVER['REMOTE_ADDR'] );
		
					if( $filter->isSpam() )
					{
						$response->addAlert( JText::_('COM_COMMUNITY_WALLS_MARKED_SPAM') );
						return $response->sendResponse();
					}
				}

				$wall	= CWallLibrary::saveWall( $uniqueId , $message , 'user' , $my , ( $my->id == $user->id ) , 'profile,profile');

				CFactory::load( 'libraries' , 'activities' );
				CFactory::load('helpers','videos');
				$matches		= cGetVideoLinkMatches( $message );
				$activityParams	= '';

				// We only want the first result of the video to be in the activity
				if($matches)
				{
					$videoLink	= $matches[0];

					CFactory::load('libraries', 'videos');
					$videoLib 	= new CVideoLibrary();
					
					$provider	= $videoLib->getProvider($videoLink);
					
					$activityParams	.= 'videolink=' . $videoLink . "\r\n";
					if($provider->isValid() )
					{
						$activityParams	.= 'url=' . $provider->getThumbnail();
					}
				}

				$act = new stdClass();
				$act->cmd 		= 'profile.wall.create';
				$act->actor 	= $my->id;
				$act->target 	= $uniqueId;
				$act->title		= JText::_('COM_COMMUNITY_ACTIVITIES_WALL_POST_PROFILE');
				$act->content	= '';
				$act->app		= 'walls';
				$act->cid		= $wall->id;

				// Allow comments on all these
				$act->comment_id	= CActivities::COMMENT_SELF;
				$act->comment_type	= 'walls';

				CActivityStream::add($act , $activityParams );

				// @rule: Send notification to the profile user.
				if( $my->id != $user->id )
				{
					CFactory::load( 'libraries' , 'notification' );

					$params			= new CParameter( '' );
					$params->set( 'url' , 'index.php?option=com_community&view=profile&userid=' . $user->id );
					$params->set( 'message'	, $message );
		
					CNotificationLibrary::add( 'etype_profile_submit_wall' , $my->id , $user->id , JText::sprintf('PLG_WALLS_NOTIFY_EMAIL_SUBJECT' , $my->getDisplayName() ) , '' , 'profile.wall' , $params );
				}
				//add user points
				CFactory::load( 'libraries' , 'userpoints' );		
				CUserPoints::assignPoint('profile.wall.create');			
				
				$response->addScriptCall( 'joms.walls.insert' , $wall->content );
				$response->addScriptCall( 'if(joms.jQuery(".content-nopost").length){
											joms.jQuery("#wall-empty-container").remove();
										}' );		
				
				$cache = & JFactory::getCache('plgCommunityWalls');
				$cache->remove($cache_id);					
				
				$cache = & JFactory::getCache('plgCommunityWalls_fullview');
				$cache->remove($cache_id);			
			}
			
			return $response;
		}
		
		/**
		 * Delete post message
		 *
		 * @param	response	An ajax Response object
		 * @param	id			A unique identifier for the wall row
		 *
		 * returns	response
		 */
		function ajaxRemoveWall( $response, $id, $cache_id="" )
		{
			$my 		= CFactory::getUser();
			$wallModel 	= CFactory::getModel('wall');
			$wall		= $wallModel->get( $id );
			
			CError::assert( $id , '' , '!empty' , __FILE__ , __LINE__ );
			
			CFactory::load( 'helpers' , 'owner' );
			// Make sure the current user actually has the correct permission
			// Only the original writer and the person the wall is meant for (and admin of course)
			// can delete the wall
			if( ($my->id == $wall->post_by) || ($my->id == $wall->contentid ) ||(COwnerHelper::isCommunityAdmin()) ) 
			{
				if( $wallModel->deletePost( $id ) )
				{
					// @rule: Remove the wall activity from the database as well
					CFactory::load( 'libraries' , 'activities' );
					CActivityStream::remove( 'walls' , $id );
					
					//add user points
					if($wall->post_by != 0)
					{
						CFactory::load( 'libraries' , 'userpoints' );		
						CUserPoints::assignPoint('wall.remove', $wall->post_by);
					}
				}
				else
				{
					$html	= JText::_( 'Error while removing wall. Line:' . __LINE__ );
					$response->addAlert( $html );
				}
				
				$cache = & JFactory::getCache('plgCommunityWalls');
				$cache->remove($cache_id);		
				
				$cache = & JFactory::getCache('plgCommunityWalls_fullview');
				$cache->remove($cache_id);	
			} 
			else
			{
				$html	= JText::_( 'COM_COMMUNITY_PERMISSION_DENIED_WARNING' );
				$response->addAlert( $html );
			}
				
			
			return $response;	
		}
		
		
		function ajaxAddComment($response, $id, $cmt, $cacheId)
		{
			JPlugin::loadLanguage('plg_walls', JPATH_ADMINISTRATOR);
			
			ini_set( 'display_errors' , 1 );
			error_reporting( E_ALL );
			
			// Add the comment into the db
			CFactory::load('libraries', 'comment');
			$my			= CFactory::getUser();
			
			// Extract thenumeric id from the wall-cmt-xxx
			$wallId		= substr($id, 9);
			
			CFactory::load( 'models' , 'wall' );
			$wall	=& JTable::getInstance( 'Wall' , 'CTable' );
			$wall->load( $wallId );
		
			$cmt		= trim( $cmt );
			$config		= CFactory::getConfig();
			
			if( $config->get( 'antispam_akismet_walls') )
			{
				CFactory::load( 'libraries' , 'spamfilter' );
	
				$filter				= CSpamFilter::getFilter();
				$filter->setAuthor( $my->getDisplayName() );
				$filter->setMessage( $cmt );
				$filter->setEmail( $my->email );
				$filter->setURL( CRoute::_('index.php?option=com_community&view=profile&userid=' . $wall->contentid ) );
				$filter->setType( 'message' );
				$filter->setIP( $_SERVER['REMOTE_ADDR'] );
	
				if( $filter->isSpam() )
				{
					$response->addScriptCall( 'joms.jQuery( "#' . $id . ' .wall-coc-errors" ).html("' . JText::_('COM_COMMUNITY_MARKED_SPAM') . '");');
					$response->addScriptCall( 'joms.jQuery( "#' . $id . ' .wall-coc-errors" ).show();');
					$response->addScriptCall( 'joms.jQuery( "#' . $id . ' .wall-coc-errors" ).css("color", "red");');
					$response->addScriptCall( 'joms.jQuery("#' . $id . ' .wall-coc-form-action.add").attr("disabled", false);');
					return $response->sendResponse();
				}
			}
			
			if( empty( $cmt ) )
			{
				$response->addScriptCall( 'joms.jQuery( "#' . $id . ' .wall-coc-errors" ).html("' . JText::_('PLG_WALLS_COMMENT_EMPTY') . '");');
				$response->addScriptCall( 'joms.jQuery( "#' . $id . ' .wall-coc-errors" ).show();');
				$response->addScriptCall( 'joms.jQuery( "#' . $id . ' .wall-coc-errors" ).css("color", "red");');
				$response->addScriptCall( 'joms.jQuery("#' . $id . ' .wall-coc-form-action.add").attr("disabled", false);');
			}
			else
			{
				$comment		= new CComment();
				$wall->comment	= $comment->add($my->id, $cmt, $wall->comment );
				$wall->store();
				
				$newComment = new stdClass();
				$date		= new JDate();
				
				$newComment->creator = $my->id;
				$newComment->text 	 = $cmt;
				$newComment->date 	 = $date->toUnix();
			
				$handler	= $comment->getCommentHandler( $wall->type );

				if( $handler )
				{
					$handler->sendCommentNotification( $wall , $newComment->text );
				}
				
				$html = $comment->renderComment($newComment, true);

				CFactory::load( 'helpers' , 'string' );
				$html	= cReplaceThumbnails($html);
				$response->addScriptCall( 'joms.comments.insert' , $id, $html );
				
				// Clear wall cache
				$cache = & JFactory::getCache('plgCommunityWalls');
				$cache->remove($cacheId);						
				$cache = & JFactory::getCache('plgCommunityWalls_fullview');
				$cache->remove($cacheId);
			}
			
			return $response;	
		}
		
		// Remove the comment
		// Wall id will be in the form or "wall-cmt-xxx" where xxx is the wall contentid
		function ajaxRemoveComment($response, $parentWallId, $commentIndex)
		{
			// Add the comment into the db
			CFactory::load('libraries', 'comment');
			$my				= CFactory::getUser();
			$db 			=& JFactory::getDBO();
			$comment 		= new CComment();
			// Extract the numeric id from the wall-cmt-xxx
			$wallid = substr($parentWallId, 9);
			
			$wallModel 	= CFactory::getModel('wall');
			$wall		= $wallModel->get( $wallid );
			
			$content = $wall->comment;
	
			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
			}
			
			// Get the comment data to determine ownership
			$commentData = $comment->getCommentsData($content);
			
			CFactory::load( 'helpers' , 'owner' );
			
			// Make sure that we have the right permission to delete this comment
			// Make sure the current user actually has the correct permission
			// Only the original writer and the person the wall is meant for (and admin of course)
			// can delete the wall
			if( ($my->id == $commentData[$commentIndex]->creator) || ($my->id == $wall->contentid ) || (COwnerHelper::isCommunityAdmin()) ) 
			{
				$content = $comment->remove( $content, $commentIndex);
				
				// Update the wall with the comment info
				$sql = 'UPDATE '.$db->nameQuote('#__community_wall')
					.' SET '.$db->nameQuote('comment').'='.$db->Quote($content)
					.' WHERE '.$db->nameQuote('id').' =' . $db->Quote($wallid);
				$db->setQuery($sql);
				$db->query();
				if($db->getErrorNum())
				{
					JError::raiseError( 500, $db->stderr());
				}
				
				$response->addScriptCall("joms.jQuery('#wall-cmt-" . $wallid . "').children(':nth-child(" . ( $commentIndex + 1 ) . ")').remove();");
			}
			else 
			{
				$html	= JText::_( 'COM_COMMUNITY_PERMISSION_DENIED_WARNING' );
				$response->addAlert( $html );
			}
			
			return $response;
		}
		
		function onProfileDisplay()
		{
			$mainframe =& JFactory::getApplication();
			
			JPlugin::loadLanguage('plg_walls', JPATH_ADMINISTRATOR);
			
			$document		=& JFactory::getDocument();
			$my				= CFactory::getUser();
			$config			= CFactory::getConfig();
			
			// Load libraries
			CFactory::load( 'libraries' , 'wall' );
			CFactory::load( 'helpers' , 'friends' );
			
			$user 			= CFactory::getRequestUser();
			
			$friendModel	= CFactory::getModel('friends');
			$avatarModel 	= CFactory::getModel('avatar');
			$isMe			= ( ($my->id == $user->id) && ($my->id != 0));
			$isGuest		= ($my->id == 0 ) ? true : false;
			$isConnected	= CFriendsHelper::isConnected( $my->id , $user->id );
	
			CFactory::load( 'helpers' , 'owner' );
			
			$isSuperAdmin	= isCommunityAdmin();
			
			// @rule: Limit should follow Joomla's list limit
			$jConfig	=& JFactory::getConfig();
			$limit		= JRequest::getVar('limit', $jConfig->getValue( 'list_limit' ) , 'REQUEST');
			$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');
					
			if(JRequest::getVar('task', '', 'REQUEST') == 'app'){
				$cache =& JFactory::getCache('plgCommunityWalls_fullview');
			}else{
				$cache =& JFactory::getCache('plgCommunityWalls');
			}
			
			$caching = $this->params->get('cache', 1);		
			if($caching)
			{
				$caching = $mainframe->getCfg('caching');
			}
			
			$cache->setCaching($caching);
			$callback = array('plgCommunityWalls', '_getWallHTML');
			
			$allowPosting = (($isMe) 
				|| (!$config->get('lockprofilewalls')) 
				|| ( $config->get('lockprofilewalls') && $isConnected ) 
				|| ( $isSuperAdmin) )
				&& (! $isGuest );
	
			$allowRemoval = ($isMe || $isSuperAdmin);
			
			$maxchar = $this->params->get('charlimit', 0);			
			if(!empty($maxchar))
			{
				$this->characterLimitScript($maxchar);
			}
			
			//$cache_id = JCacheCallback::_makeId(array('plgCommunityWalls', '_getWallHTML'), array($user->id, $limit, $limitstart , $allowPosting , $allowRemoval));
			//get cache id
			$callback_args	= array($user->id, $limit, $limitstart , $allowPosting , $allowRemoval);
			$cache_id = md5(serialize(array($callback, $callback_args)));
			
			$javascript =<<<SHOWJS
							function getCacheId()
						 	{
								var cache_id = "'.$cache_id.'";
								return cache_id;
							}
SHOWJS;
			$document->addScriptDeclaration($javascript);
			
			
			$content = $cache->call($callback, $user->id, $limit, $limitstart , $allowPosting , $allowRemoval);
			
			return $content; 			
		}
	
		//function _getWallHTML($userid, $limit, $limitstart , $isMe , $isGuest, $isConnected , $isSuperAdmin)
		function _getWallHTML($userid, $limit, $limitstart , $allowPosting , $allowRemoval)
		{
			$config			= CFactory::getConfig();
			$html			= '';
						
			$viewAllLink	= false;
			
			if(JRequest::getVar('task', '', 'REQUEST') != 'app')
			{
				$viewAllLink	= CRoute::_('index.php?option=com_community&view=profile&userid='.$userid.'&task=app&app=walls');
			}
			$wallCount	= CWallLibrary::getWallCount('user', $userid);
			$wallModel	= CFactory::getModel('wall');		
			$wallsinput	= "";

			if( $allowPosting )
			{
				$wallsinput	= CWallLibrary::getWallInputForm( $userid , 'plugins,walls,ajaxSaveWall' , 'plugins,walls,ajaxRemoveWall', $viewAllLink);
			}
			$contents	= CWallLibrary::getWallContents( 'user' , $userid , $allowRemoval , $limit, $limitstart , 'wall.content' , 'profile,profile');
			$contents	.= CWallLibrary::getViewAllLinkHTML($viewAllLink, $wallCount);
			$html.= $wallsinput;
			$html.= '<div id="wallContent" style="display: block; visibility: visible;">';
			
			if ( $contents == '' )
			{
				$html .= '
				<div id="wall-empty-container">
					<div class="icon-nopost">
			            <img src="'.JURI::base().'plugins/community/walls/favicon.png" alt="" />
			        </div>
			        <div class="content-nopost">'.
			            JText::_('PLG_WALLS_NO_WALL_POST').'
			        </div>
				</div>';
			}
			else
			{
				$html .= CStringHelper::replaceThumbnails($contents);
			}
			
			$html.= '</div>';
			
			// Add pagination links, only in full app view
			if(JRequest::getVar('task', '', 'REQUEST') == 'app')
			{
				jimport('joomla.html.pagination');
				$pagination	= new JPagination( $wallModel->getCount($userid, 'user') , $limitstart , $limit );
				$html .= '
				<!-- Pagination -->
				<div style="text-align: center;">
					'.$pagination->getPagesLinks().'
				</div>
				<!-- End Pagination -->';
			}
			
			return $html;
		}
		
		function onAppDisplay()
		{
			ob_start();
			$limit=0;
			$html= $this->onProfileDisplay($limit);
			echo $html;
			
			$content	= ob_get_contents();
			ob_end_clean(); 
		
			return $content;
			
		}
		
		function characterLimitScript($maxchar)
		{
			$text_char_remain	= JText::_('PLG_WALLS_CHAR_REMAIN');
			$text_trimming 		= JText::_('PLG_WALLS_TRIMMING');
			
			$js=<<<SHOWJS
				(function(jQuery) {
					joms.jQuery.fn.textlimit=function(counter_el, thelimit, speed) {
						var charDelSpeed = speed || 15;
						var toggleCharDel = speed != -1;
						var toggleTrim = true;
						var that = this[0];
						var isCtrl = false; 
						updateCounter();
						
						function updateCounter(){
							if(typeof that == "object")
								joms.jQuery('#'+counter_el).text(thelimit - that.value.length+" $text_char_remain");
						};
						
						this.keydown (function(e){ 
							if(e.which == 17) isCtrl = true;
							var ctrl_a = (e.which == 65 && isCtrl == true) ? true : false; // detect and allow CTRL + A selects all.
							var ctrl_v = (e.which == 86 && isCtrl == true) ? true : false; // detect and allow CTRL + V paste.
							// 8 is 'backspace' and 46 is 'delete'
							if( this.value.length >= thelimit && e.which != '8' && e.which != '46' && ctrl_a == false && ctrl_v == false)
								e.preventDefault();
						})
						.keyup (function(e){
							updateCounter();
							if(e.which == 17)
								isCtrl=false;
				
							if( this.value.length >= thelimit && toggleTrim ){
								if(toggleCharDel){
									// first, trim the text a bit so the char trimming won't take forever
									// Also check if there are more than 10 extra chars, then trim. just in case.
									if ( (this.value.length - thelimit) > 10 )
										that.value = that.value.substr(0,thelimit+100);
									var init = setInterval
										( 
											function(){ 
												if( that.value.length <= thelimit ){
													init = clearInterval(init); updateCounter() 
												}
												else{
													// deleting extra chars (one by one)
													that.value = that.value.substring(0,that.value.length-1); joms.jQuery('#'+counter_el).text('$text_trimming '+(thelimit - that.value.length));
												}
											} ,charDelSpeed 
										);
								}
								else this.value = that.value.substr(0,thelimit);
							}
						});
						
					};
				})(joms.jQuery);
				
				joms.jQuery(document).ready(function(){
					//joms.jQuery("#wall-message-counter").show();
					joms.jQuery("#wall-message").textlimit('wall-message-counter', $maxchar, -1);
				});
SHOWJS;
			$document =& JFactory::getDocument();
			$document->addScriptDeclaration($js);
		}
	}	
}


