<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die();
?>

<?php if($showToolbar) : ?>
<div id="jsMenu">
	<div class="jsMenuLft">
		<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=frontpage' );?>" class="jsHome jsIr<?php echo $active == 0 ? ' isActive' :'';?>"><?php echo JText::_( 'COM_COMMUNITY_HOME' );?></a>
		<div class="jsMenuBar">
		<ul class="cResetList clrfix">
		<?php
			foreach( $menus as $menu )
			{
				$class	= empty( $menu->childs ) ? ' class="no-child"' : '';
		?>
			<li<?php echo $class;?>>
				<a href="<?php echo CRoute::_( $menu->item->link );?>"<?php echo $active === $menu->item->id ? ' class="active"' : '';?>><?php echo JText::_( $menu->item->name );?></a>
				<?php
				if( !empty($menu->childs) )
				{
				?>
					<ul class="cResetList clrfix">
					<?php
					foreach( $menu->childs as $child )
					{
					?>
						<li>
							<?php if( $child->script ){ ?>
								<a href="javascript:void(0);" onclick="<?php echo $child->link;?>">
							<?php } else { ?>
								<a href="<?php echo CRoute::_( $child->link );?>">
							<?php } ?>
							<?php echo JText::_( $child->name );?></a>
						</li>
					<?php
					}
					?>
					</ul>
				<?php 
				}
				?>
			</li>
			<?php
			}
		?>
		</ul>
		</div><div class="jsMenuIcon">
			<div id="jsMenuNotif">
				<a href="javascript:joms.notifications.showWindow();" class="jsGlobalsNot jsIr" title="<?php echo JText::_( 'COM_COMMUNITY_NOTIFICATIONS_GLOBAL' );?>"><?php echo JText::_( 'COM_COMMUNITY_NOTIFICATIONS' );?>
				<?php if( $newEventInviteCount ) { ?>
				<span class="notifcount"><?php echo $newEventInviteCount; ?></span>
				<?php } ?>
                                </a>
			</div>
			<div id="jsMenuFriend">
				<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=friends&task=pending' );?>" onclick="joms.notifications.showRequest();return false;" class="jsFriendsNot jsIr" title="<?php echo JText::_( 'COM_COMMUNITY_NOTIFICATIONS_INVITE_FRIENDS' );?>"><?php echo JText::_( 'COM_COMMUNITY_NOTIFICATIONS' );?><?php if( $newFriendInviteCount ){ ?><span class="notifcount"><?php echo $newFriendInviteCount; ?></span><?php } ?></a>
			</div>
			<div id="jsMenuInbox">
				<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=inbox' );?>" class="jsMesaggeNot jsIr"  onclick="joms.notifications.showInbox();return false;" title="<?php echo JText::_( 'COM_COMMUNITY_NOTIFICATIONS_INBOX' );?>"><?php echo JText::_( 'COM_COMMUNITY_NOTIFICATIONS' );?><?php if( $newMessageCount ){ ?><span class="notifcount"><?php echo $newMessageCount; ?></span><?php } ?></a>
			</div>
		</div>
	</div>
	<div class="jsMenuRgt">
		<div class="jsLogOff">
			<form action="<?php echo JRoute::_('index.php');?>" method="post" name="communitylogout" id="communitylogout">
				<a href="javascript:void(0);" title="<?php echo JText::_('COM_COMMUNITY_LOGOUT'); ?>" onclick="document.communitylogout.submit();" class="jsIr"><?php echo JText::_('COM_COMMUNITY_LOGOUT');?></a>
				<input type="hidden" name="option" value="<?php echo COM_USER_NAME ; ?>" />
				<input type="hidden" name="task" value="<?php echo COM_USER_TAKS_LOGOUT ; ?>" />
				<input type="hidden" name="return" value="<?php echo $logoutLink; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</form>
		</div>
	</div>
</div>
<?php endif; ?>
<?php if ( $miniheader ) : ?>
	<?php echo @$miniheader; ?>
<?php endif; ?>
<?php if ( !empty( $groupMiniHeader ) ) : ?>
	<?php echo $groupMiniHeader; ?>
<?php endif; ?>
