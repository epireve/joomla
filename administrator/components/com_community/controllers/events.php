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
jimport( 'joomla.filesystem.file' );

/**
 * Jom Social Component Controller
 */
class CommunityControllerEvents extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->registerTask( 'publish' , 'savePublish' );
		$this->registerTask( 'unpublish' , 'savePublish' );	
	}

	public function ajaxTogglePublish( $id , $type )
	{
		return parent::ajaxTogglePublish( $id , $type , 'events' );
	}
	
	public function ajaxChangeGroupOwner( $groupId )
	{
		$response	= new JAXResponse();

		$group		=& JTable::getInstance( 'Groups' , 'CommunityTable' );
		$group->load( $groupId );
		
		$group->owner	=& JFactory::getUser( $group->ownerid );
		
		$model			= $this->getModel( 'users' );
		$users			= $model->getAllUsers(false);

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
				<select name="ownerid">
					<?php
						foreach( $users as $user )
						{
					?>
						<option value="<?php echo $user->id;?>"><?php echo JText::sprintf('%1$s [ %2$s ]' , $user->name , $user->email );?></option>
					<?php
						}
					?>
				</select>
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
	
	public function ajaxEditEvent( $eventId )
	{
		$response	= new JAXResponse();

		$model		= $this->getModel( 'eventcategories' );
		
		$categories	= $model->getCategories();
		$event		= JTable::getInstance( 'Event' , 'CTable' );
		
		$event->load( $eventId );
		
		// Escape the output
		CFactory::load( 'helpers' , 'string' );
		$event->title	= CStringHelper::escape($event->title);
		$event->description	= CStringHelper::escape($event->description);
		
		ob_start();
?>
<form name="editevent" action="" method="post" id="editevent">
<div style="background-color: #F9F9F9; border: 1px solid #D5D5D5; margin-bottom: 10px; padding: 5px;font-weight: bold;">
	<?php echo JText::_('COM_COMMUNITY_EVENTS_EDIT_DETAILS');?>
</div>
<table cellspacing="0" class="admintable" border="0" width="100%">
	<tbody>
		<tr>
			<td class="key" valign="top"><?php echo JText::_('COM_COMMUNITY_AVATAR');?></td>
			<td valign="top">:</td>
			<td>
				<img width="90" src="<?php echo $event->getAvatar( 'avatar' );?>" style="border: 1px solid #eee;"/>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('COM_COMMUNITY_PUBLISH_STATUS');?></td>
			<td>:</td>
			<td>
				<input type="radio" name="published" value="1" id="publish" <?php echo ( $event->published == '1' ) ? 'checked="true"' : '';?>/>
				<label for="publish"><?php echo JText::_('COM_COMMUNITY_PUBLISH'); ?></label>
				<input type="radio" name="published" value="0" id="unpublish" <?php echo ( $event->published == '0' ) ? 'checked="true"' : '';?>>
				<label for="unpublish"><?php echo JText::_('COM_COMMUNITY_UNPUBLISH');?></label>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('COM_COMMUNITY_CATEGORY');?></td>
			<td>:</td>
			<td>
				<select name="catid">
				<?php
					for( $i = 0; $i < count( $categories ); $i++ )
					{
						$selected	= ($event->catid == $categories[$i]->id ) ? ' selected="selected"' : '';
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
					<input type="text" name="title" class="inputbox" value="<?php echo $event->title;?>" style="width: 250px;" />
				</span>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('COM_COMMUNITY_DESCRIPTION');?></td>
			<td>:</td>
			<td>
				<textarea name="description" style="width: 250px;" rows="5"><?php echo $event->description;?></textarea>
			</td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="id" value="<?php echo $event->id;?>" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="task" value="saveevent" />
<input type="hidden" name="view" value="events" />
<?php
		$contents	= ob_get_contents();
		ob_end_clean();
		
		$response->addAssign( 'cWindowContent' , 'innerHTML' , $contents );

		$action = '<input type="button" class="button" onclick="azcommunity.saveEvent();" name="' . JText::_('COM_COMMUNITY_SAVE') . '" value="' . JText::_('COM_COMMUNITY_SAVE') . '" />';
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
	
	public function saveEvent()
	{
		$event	= JTable::getInstance( 'Events' , 'CommunityTable' );
		$id		= JRequest::getInt( 'id' , '' , 'post' );
		
		if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_COMMUNITY_INVALID_ID') );
		}

		$postData	= JRequest::get( 'post' );
		$event->load( $id );
		$event->bind( $postData );

		$message	= '';
		if( $event->store() )
		{
			$message	= JText::_('COM_COMMUNITY_EVENT_SUCCESSFULLY_SAVED');
		}
		else
		{
			$message	= JText::_('COM_COMMUNITY_EVENT_ERROR_WHILE_SAVING');
		}
		
		$mainframe = JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_community&view=events' , $message );
	}
	
	public function deleteEvent()
	{
		CFactory::load( 'libraries' , 'activities' );
    	require_once(JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'defines.community.php');

		$event	=& JTable::getInstance( 'Event' , 'CTable' );
		$data	= JRequest::getVar( 'cid' , '' , 'post' );
		$error	= array();

		if( !is_array( $data ) )
		{
			$data[]	= $data;
		}
		
		if( empty($data) )
		{
			JError::raiseError( '500' , JText::_('COM_COMMUNITY_INVALID_ID') );
		}
		
		foreach($data as $id)
		{
			$event->load( $id );
			$this->triggerEvents( 'onBeforeEventDelete' , $event);
			$event->deleteAllMembers();
			$event->deleteWalls();
			$tmp	= $event;
			
			if( $event->delete() )
			{
				if($tmp->avatar != "components/com_community/assets/eventAvatar.png" && !empty( $tmp->avatar ) )
				{
					$path = explode('/', $tmp->avatar);

					$file = JPATH_ROOT . DS . $path[0] . DS . $path[1] . DS . $path[2] .DS . $path[3];
					if(JFile::exists($file))
					{
						JFile::delete($file);
					}
				}
				
				if($tmp->thumb != "components/com_community/assets/event_thumb.png" && !empty( $tmp->avatar ) )
				{
					$file	= JPATH_ROOT . DS . CString::str_ireplace('/', DS, $tmp->thumb);
					if(JFile::exists($file))
					{
						JFile::delete($file);
					}
				}
				$this->triggerEvents( 'onAfterEventDelete' , $tmp );
				CActivityStream::remove('events', $id);				
				$error[]	= false;
			}
			else
			{
				$error[]	= true;
			}
			
		}

		$mainframe	=& JFactory::getApplication();
		
		if( in_array( true, $error ) )
		{
			$mainframe->redirect( 'index.php?option=com_community&view=events' , JText::_('COM_COMMUNITY_EVENTS_REMOVING_ERROR') , 'error' );
		}
		else
		{
			$mainframe->redirect( 'index.php?option=com_community&view=events' , JText::_('COM_COMMUNITY_EVENTS_DELETED') );
		}		
	}

	public function triggerEvents( $eventName, &$args, $target = null)
	{
		CError::assert( $args , 'object', 'istype', __FILE__ , __LINE__ );
		
		require_once( JPATH_ROOT .DS.'components' . DS . 'com_community' . DS .'libraries' . DS . 'apps.php' );
		$appsLib	=& CAppPlugins::getInstance();
		$appsLib->loadApplications();
		
		$params		= array();
		$params[]	= &$args;
		
		if(!is_null($target))
			$params[]	= $target;
				
		$appsLib->triggerEvent( $eventName , $params);
		return true;
	}
}
