<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * Jom Social Component Controller
 */
class CommunityControllerGroups extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->registerTask( 'publish' , 'savePublish' );
		$this->registerTask( 'unpublish' , 'savePublish' );	
	}

	public function ajaxTogglePublish( $id , $type )
	{
		// Send email notification to owner when a group is published.
		$config	=& CFactory::getConfig();
		$group	=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $id );
		
		if( $type == 'published' && $group->published == 0 && $config->get( 'moderategroupcreation' ) )
		{
			$lang =& JFactory::getLanguage();
			$lang->load( 'com_community', JPATH_ROOT );

			
			$my			=& CFactory::getUser();
			
			// Add notification
			CFactory::load( 'libraries' , 'notification' );

			//Send notification email to owner
			$params	= new CParameter( '' );
			$params->set('url' , 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id );
			$params->set('groupName' , $group->name );

			CNotificationLibrary::add( 'etype_groups_notify_creator' , $my->id , $group->ownerid , JText::sprintf( 'COM_COMMUNITY_GROUPS_PUBLISHED_MAIL_SUBJECT' , $group->name ) , '' , 'groups.notifycreator' , $params );
		}
		
		return parent::ajaxTogglePublish( $id , $type , 'groups' );
	}
	
	public function ajaxChangeGroupOwner( $groupId )
	{
		$response	= new JAXResponse();
	
		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $groupId );
		$model			= CFactory::getModel( 'Groups' );
		
		$group->owner	= JFactory::getUser( $group->ownerid );
		$rows			= $model->getMembers( $group->id , 0 , true , false , true );
		ob_start();
?>
<div style="background-color: #F9F9F9; border: 1px solid #D5D5D5; margin-bottom: 10px; padding: 5px;font-weight: bold;">
	<?php echo JText::_('COM_COMMUNITY_GROUPS_CHANGE_OWNERSHIP');?>
</div>
<form name="editgroup" method="post" action="">
<table cellspacing="0" class="admintable" border="0" width="100%">
	<tbody>
		<tr>
			<td class="key" valign="top"><?php echo JText::_('COM_COMMUNITY_GROUPS_OWNER');?></td>
			<td valign="top" width="1%">:</td>
			<td align="left">
				<?php echo $group->owner->name; ?>
			</td>
		</tr>
		<tr>
			<td class="key" valign="top"><?php echo JText::_('COM_COMMUNITY_GROUPS_NEW_OWNER');?></td>
			<td valign="top">:</td>
			<td align="left">
				<?php
				if($rows)
				{
				?>
				<select name="ownerid">
					<?php
						foreach( $rows as $row )
						{
							$user	= CFactory::getUser( $row->id );
					?>
						<option value="<?php echo $user->id;?>"><?php echo JText::sprintf('%1$s [ %2$s ]' , $user->name , $user->email );?></option>
					<?php
						}
					?>
				</select>
				<?php
				}
				else
				{
				?>
				<div><?php echo JText::_('COM_COMMUNITY_GROUPS_CHANGE_OWNER_WARN');?></div>
				<?php
				}
				?>
			</td>
		</tr>
	</tbody>
</table>
<input name="id" value="<?php echo $group->id;?>" type="hidden" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="task" value="updateGroupOwner" />
<input type="hidden" name="view" value="groups" />
</form>
<?php
		$contents	= ob_get_contents();
		ob_end_clean();
		
		$response->addAssign( 'cWindowContent' , 'innerHTML' , $contents );

		$action = '<input type="button" class="button" onclick="azcommunity.saveGroupOwner();" name="' . JText::_('COM_COMMUNITY_SAVE') . '" value="' . JText::_('COM_COMMUNITY_SAVE') . '" />';
		$action .= '&nbsp;<input type="button" class="button" onclick="cWindowHide();" name="' . JText::_('COM_COMMUNITY_CLOSE') . '" value="' . JText::_('COM_COMMUNITY_CLOSE') . '" />';
		$response->addScriptCall( 'cWindowActions' , $action );

		return $response->sendResponse();
	}

	public function ajaxAssignGroup( $memberId )
	{
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
		$response	= new JAXResponse();

		$model		= $this->getModel( 'groups' );
		$groups		= $model->getAllGroups();
		$user		= CFactory::getUser( $memberId );
		ob_start();
?>
<form name="assignGroup" action="" method="post" id="assignGroup">
<div style="background-color: #F9F9F9; border: 1px solid #D5D5D5; margin-bottom: 10px; padding: 5px;">
	<?php echo JText::sprintf('COM_COMMUNITY_GROUP_ASSIGN_MEMBER', $user->getDisplayName() );?>
</div>
<table cellspacing="0" class="admintable" border="0" width="100%">
	<tbody>
		<tr>
			<td class="key" valign="top"><?php echo JText::_('COM_COMMUNITY_GROUPS');?></td>
			<td valign="top">:</td>
			<td>
				<select name="groupid" id="groupid">
					<option value="-1" selected="selected"><?php echo JText::_('COM_COMMUNITY_GROUPS_SELECT');?></option>
				<?php
					foreach($groups as $row )
					{
						if( !$model->isMember($user->id , $row->id) )
						{
				?>
					<option value="<?php echo $row->id;?>"><?php echo $row->name;?></option>
				<?php
						}
					}		
				?>
				</select>
			</td>
		</tr>
	</tbody>
</table>
<div id="group-error-message" style="color: red;font-weight:700;"></div>
<input type="hidden" name="memberid" value="<?php echo $user->id;?>" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="task" value="addmember" />
<input type="hidden" name="view" value="groups" />
<?php
		$contents	= ob_get_contents();
		ob_end_clean();

		$response->addAssign( 'cWindowContent' , 'innerHTML' , $contents );

		$action = '<input type="button" class="button" onclick="azcommunity.saveAssignGroup();" name="' . JText::_('COM_COMMUNITY_SAVE') . '" value="' . JText::_('COM_COMMUNITY_SAVE') . '" />';
		$action .= '&nbsp;<input type="button" class="button" onclick="cWindowHide();" name="' . JText::_('COM_COMMUNITY_CLOSE') . '" value="' . JText::_('COM_COMMUNITY_CLOSE') . '" />';
		$response->addScriptCall( 'cWindowActions' , $action );
		$response->addScriptCall( 'joms.jQuery("#cwin_logo").html("' . JText::_('COM_COMMUNITY_GROUPS_ASSIGN_USER') . '");');
		return $response->sendResponse();
	}
	
	public function ajaxEditGroup( $groupId )
	{
		$response	= new JAXResponse();

		$model		= $this->getModel( 'groupcategories' );
		
		$categories	= $model->getCategories();

		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		
		$group->load( $groupId );
		
		$requireApproval	= ($group->approvals) ? ' checked="true"' : '';
		$noApproval			= (!$group->approvals) ? '' : ' checked="true"';
		
		// Escape the output
		CFactory::load( 'helpers' , 'string' );
		$group->name	= CStringHelper::escape($group->name);
		$group->description	= CStringHelper::escape($group->description);
		
		ob_start();
?>
<form name="editgroup" action="" method="post" id="editgroup">
<div style="background-color: #F9F9F9; border: 1px solid #D5D5D5; margin-bottom: 10px; padding: 5px;font-weight: bold;">
	<?php echo JText::_('COM_COMMUNITY_GROUPS_EDIT_GROUP');?>
</div>
<table cellspacing="0" class="admintable" border="0" width="100%">
	<tbody>
		<tr>
			<td class="key" valign="top"><?php echo JText::_('COM_COMMUNITY_AVATAR');?></td>
			<td valign="top">:</td>
			<td>
				<img width="90" src="<?php echo $group->getThumbAvatar();?>" style="border: 1px solid #eee;"/>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('COM_COMMUNITY_PUBLISH_STATUS');?></td>
			<td>:</td>
			<td>
				<input type="radio" name="published" value="1" id="publish" <?php echo ( $group->published == '1' ) ? 'checked="true"' : '';?>/>
				<label for="publish"><?php echo JText::_('COM_COMMUNITY_PUBLISH'); ?></label>
				<input type="radio" name="published" value="0" id="unpublish" <?php echo ( $group->published == '0' ) ? 'checked="true"' : '';?>>
				<label for="unpublish"><?php echo JText::_('COM_COMMUNITY_UNPUBLISH');?></label>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('COM_COMMUNITY_GROUP_TYPE');?></td>
			<td>:</td>
			<td>
				<input type="radio" name="approvals" value="1" id="approve" <?php echo ( $group->approvals == '1' ) ? 'checked="true"' : '';?>/>
				<label for="approve"><?php echo JText::_('COM_COMMUNITY_GROUP_PRIVATE'); ?></label>
				<input type="radio" name="approvals" value="0" id="unapprove" <?php echo ( $group->approvals == '0' ) ? 'checked="true"' : '';?>/>
				<label for="unapprove"><?php echo JText::_('COM_COMMUNITY_PUBLIC');?></label>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('COM_COMMUNITY_CATEGORY');?></td>
			<td>:</td>
			<td>
				<select name="categoryid">
				<?php

					for( $i = 0; $i < count( $categories ); $i++ )
					{
						$selected	= ($group->categoryid == $categories[$i]->id ) ? ' selected="selected"' : '';
				?>
						<option value="<?php echo $categories[$i]->id;?>"<?php echo $selected;?>><?php echo $categories[$i]->name;?></option>
				<?php
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('COM_COMMUNITY_NAME');?></td>
			<td>:</td>
			<td>
				<span>
					<input type="text" name="name" class="inputbox" value="<?php echo $group->name;?>" style="width: 250px;" />
				</span>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('COM_COMMUNITY_DESCRIPTION');?></td>
			<td>:</td>
			<td>
				<textarea name="description" style="width: 250px;" rows="5"><?php echo $group->description;?></textarea>
			</td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="id" value="<?php echo $group->id;?>" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="task" value="savegroup" />
<input type="hidden" name="view" value="groups" />
<?php
		$contents	= ob_get_contents();
		ob_end_clean();

		$response->addAssign( 'cWindowContent' , 'innerHTML' , $contents );

		$action = '<input type="button" class="button" onclick="azcommunity.saveGroup();" name="' . JText::_('COM_COMMUNITY_SAVE') . '" value="' . JText::_('COM_COMMUNITY_SAVE') . '" />';
		$action .= '&nbsp;<input type="button" class="button" onclick="cWindowHide();" name="' . JText::_('COM_COMMUNITY_CLOSE') . '" value="' . JText::_('COM_COMMUNITY_CLOSE') . '" />';
		$response->addScriptCall( 'cWindowActions' , $action );
		
		return $response->sendResponse();
	}
	
	public function updateGroupOwner()
	{
		$group	=& JTable::getInstance( 'Groups' , 'CommunityTable' );

		$groupId	= JRequest::getVar( 'id' , '' , 'post' );
		$group->load( $groupId );

		$oldOwner	= $group->ownerid;
		$newOwner	= JRequest::getVar( 'ownerid' ) ;

		// Add member if member does not exist.
		if( !$group->isMember( $newOwner , $group->id ) )
		{
			$data 	= new stdClass();
			$data->groupid			= $group->id;
			$data->memberid		= $newOwner;
			$data->approved		= 1;
			$data->permissions	= 1;
			
			// Add user to group members table
			$group->addMember( $data );
			
			// Add the count.
			$group->addMembersCount( $group->id );
			
			$message	= JText::_('COM_COMMUNITY_GROUP_SAVED');
		}
		else
		{
			// If member already exists, update their permission
			$member	=& JTable::getInstance( 'GroupMembers' , 'CommunityTable' );
			$member->load( $group->id , $newOwner );
			$member->permissions	= '1';

			$member->store();
		}
		
		$group->ownerid	= $newOwner;
		$group->store();
		
		$message	= JText::_('COM_COMMUNITY_GROUP_OWNER_SAVED');
		
		$mainframe	=& JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_community&view=groups' , $message );
	}

	/**
	 *	Adds a user to an existing group
	 **/	 	
	public function addMember()
	{
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
		
		$groupId	= JRequest::getVar( 'groupid' , '-1' , 'REQUEST' );
		$memberId	= JRequest::getVar( 'memberid' , '' , 'REQUEST' );
		$mainframe	=& JFactory::getApplication();
		
		if( empty($memberId) || $groupId == '-1' )
		{
			$message	= JText::_('COM_COMMUNITY_INVALID_ID');
			$mainframe->redirect( 'index.php?option=com_community&view=users' , $message , 'error');
		}

		$group		=& JTable::getInstance( 'Groups' , 'CommunityTable' );
		$model		=& $this->getModel( 'groups' );
		$group->load( $groupId );
		$user		= CFactory::getUser($memberId);
	
		
		if( !$model->isMember( $memberId , $group->id ) )
		{
			$data 	= new stdClass();
			$data->groupid		= $group->id;
			$data->memberid		= $memberId;
			$data->approved		= 1;
			$data->permissions	= 0;
			
			// Add user to group members table
			$group->addMember( $data );

			// Add the count.
			$group->addMembersCount( $group->id );
			
			$message	= JText::sprintf('%1$s has been assigned into the group %2$s.' , $user->getDisplayName() , $group->name );
			$mainframe->redirect( 'index.php?option=com_community&view=users' , $message );
		}
		$message	= JText::sprintf('Cannot assign %1$s to the group %2$s. User is already assigned to the group %2$s.' , $user->getDisplayName() , $group->name );
		$mainframe->redirect( 'index.php?option=com_community&view=users' , $message , 'error');
	}
	
	public function saveGroup()
	{
		$group	=& JTable::getInstance( 'Groups' , 'CommunityTable' );
		
		$id			= JRequest::getVar( 'id' , '' , 'post' );
		
		if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_COMMUNITY_INVALID_ID') );
		}

		$postData	= JRequest::get( 'post' );
		$group->load( $id );
		
		$group->bind( $postData );

		$message	= '';		
		if( $group->store() )
		{
			$message	= JText::_('COM_COMMUNITY_GROUP_SAVED');
		}
		else
		{
			$message	= JText::_('COM_COMMUNITY_GROUP_SAVE_ERROR');
		}
		
		$mainframe	=& JFactory::getApplication();

		$mainframe->redirect( 'index.php?option=com_community&view=groups' , $message );
	}
	
	public function deleteGroup()
	{
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'featured.php');
    	require_once(JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'defines.community.php');
		
		$featured	= new CFeatured(FEATURED_GROUPS);
		
		$groupWithError = array();
		
		$group	=& JTable::getInstance( 'Group' , 'CTable' );
		
		$id			= JRequest::getVar( 'cid' , '' , 'post' );
		
		if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_COMMUNITY_INVALID_ID') );
		}
		
		foreach($id as $data)
		{
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' .DS . 'groups.php' );
						
			//delete group bulletins
			CommunityModelGroups::deleteGroupBulletins($data);
						
			//delete group members
			CommunityModelGroups::deleteGroupMembers($data);
			
			//delete group wall
			CommunityModelGroups::deleteGroupWall($data);
			
			//delete group discussions		
			CommunityModelGroups::deleteGroupDiscussions($data);
			
			//delete group media files
			CommunityModelGroups::deleteGroupMedia($data);
						
			//load group data before delete
			$group->load( $data );
			$groupData = $group;

			//delete group avatar.
			jimport( 'joomla.filesystem.file' );
			if( !empty( $groupData->avatar) )
			{
				//images/avatar/groups/d203ccc8be817ad5b6a8335c.png
				$path = explode('/', $groupData->avatar);
				$file = JPATH_ROOT . DS . $path[0] . DS . $path[1] . DS . $path[2] .DS . $path[3];
				if(file_exists($file))
				{
					JFile::delete($file);
				}
			}
			
			if( !empty( $groupData->thumb ) )
			{
				//images/avatar/groups/thumb_d203ccc8be817ad5b6a8335c.png
				$path = explode('/', $groupData->thumb);
				$file = JPATH_ROOT . DS . $path[0] . DS . $path[1] . DS . $path[2] .DS . $path[3];
				if(file_exists($file))
				{
					JFile::delete($file);
				}
			}
						
			if( !$group->delete( $data ) )
			{
				array_push($groupWithError, $data.':'.$groupData->name);
			}
			
    		$featured->delete( $data );
		}
		
		$message	= '';		
		if( empty($error) )
		{
			$message	= JText::_('COM_COMMUNITY_GROUP_DELETED');
		}
		else
		{
			$error = implode(',', $groupWithError);
			$message	= JText::sprintf('COM_COMMUNITY_GROUPS_DELETE_GROUP_ERROR' , $error);
		}
		
		$mainframe	=& JFactory::getApplication();

		$mainframe->redirect( 'index.php?option=com_community&view=groups' , $message );
	}

	/**
	 *  Responsible to save an existing or a new group.
	 */
	public function save()
	{
		JRequest::checkToken() or jexit( JText::_( 'COM_COMMUNITY_INVALID_TOKEN' ) );
		
		$mainframe	= JFactory::getApplication();
		
		if( JString::strtoupper(JRequest::getMethod()) != 'POST')
		{
			$mainframe->redirect( 'index.php?option=com_community&view=groups' , JText::_( 'COM_COMMUNITY_PERMISSION DENIED' ) , 'error');
		}
		
		// Load frontend language file.
		$lang	=& JFactory::getLanguage();
		$lang->load( 'com_community' , JPATH_ROOT );
		
		$group			= JTable::getInstance( 'Group' , 'CTable' );
		$id				= JRequest::getInt( 'groupid' );
		$group->load( $id );
		
		$name			= JRequest::getVar('name' , '' , 'POST');
		$description            = $_POST['description'];
		$categoryId		= JRequest::getVar('categoryid' , '' , 'POST');
		$creator		= JRequest::getInt( 'creator' , 0 , 'POST' );
		$website		= JRequest::getVar('website' , '' , 'POST');
		$validated		= true;
		$model			= CFactory::getModel( 'Groups' );

                $isNew			= $group->id < 1;
		$ownerChanged	= $group->ownerid != $creator && $group->id >= 1 ;
		
		// @rule: Test for emptyness
		if( empty( $name ) )
		{
			$validated	= false;
			$mainframe->enqueueMessage( JText::_('COM_COMMUNITY_GROUPS_EMPTY_NAME_ERROR'), 'error');
		}

		// @rule: Test if group exists
		if( $model->groupExist( $name , $group->id ) )
		{
			$validated	= false;
			$mainframe->enqueueMessage( JText::_('COM_COMMUNITY_GROUPS_NAME_TAKEN_ERROR'), 'error');
		}

		// @rule: Test for emptyness
		if( empty( $description ) )
		{
			$validated	= false;
			$mainframe->enqueueMessage( JText::_('COM_COMMUNITY_GROUPS_DESCRIPTION_EMPTY_ERROR'), 'error');
		}

		if( empty( $categoryId ) )
		{
			$validated	= false;
			$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_GROUPS_CATEGORY_ERROR'), 'error');
		}
		
		if($validated)
		{
			// Get the configuration object.
			$config	= CFactory::getConfig();

			$group->bindRequestParams();
			
			CFactory::load('helpers' , 'owner' );
			
			// Bind the post with the table first
			$group->name		= $name;
			$group->description	= $description;
			$group->categoryid	= $categoryId;
			$group->website		= $website;
			$group->approvals	= JRequest::getInt('approvals' , '0' , 'POST');
			$oldOwner			= $group->ownerid;
			$group->ownerid		= $creator;
			if( $isNew )
			{
				$group->created		= gmdate('Y-m-d H:i:s');
			}
			
			$group->store();
			
			if( $isNew )
			{
				// Since this is storing groups, we also need to store the creator / admin
				// into the groups members table
				$member				=& JTable::getInstance( 'GroupMembers' , 'CTable' );
				$member->groupid	= $group->id;
				$member->memberid	= $group->ownerid;
				
				// Creator should always be 1 as approved as they are the creator.
				$member->approved	= 1;
				
				// @todo: Setup required permissions in the future
				$member->permissions	= '1';
				$member->store();				
			}
			
			if( !$isNew && $ownerChanged )
			{
				$group->updateOwner( $oldOwner , $creator );
			}
			
			$message	= $isNew ? JText::_( 'COM_COMMUNITY_GROUPS_CREATED' ) : JText::_( 'COM_COMMUNITY_GROUPS_UPDATED' );
			$mainframe->redirect( 'index.php?option=com_community&view=groups' , $message );
		}

		$document	= JFactory::getDocument();
		
		$viewName	= JRequest::getCmd( 'view' , 'community' );
		
		// Get the view type
		$viewType	= $document->getType();
		
		// Get the view
		$view		=& $this->getView( $viewName , $viewType );
		
		$view->setLayout( 'edit' );

		$model		=& $this->getModel( $viewName );
		
		if( $model )
		{
			$view->setModel( $model , $viewName );
		}
		
		$view->display();
	}
}