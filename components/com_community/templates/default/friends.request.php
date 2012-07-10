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

<?php
if ( $rows ) {
?>

<?php foreach( $rows as $row ) { ?>

<div class="mini-profile jsFriendList">
	<div class="mini-profile-avatar">
		<a href="<?php echo $row->user->profileLink; ?>"><img class="cAvatar" src="<?php echo $row->user->getThumbAvatar(); ?>" alt="<?php echo $row->user->getDisplayName(); ?>" /></a>
	</div>
	
	<div class="mini-profile-details">
	    <h3 class="name">
			<a href="<?php echo $row->user->profileLink; ?>"><strong><?php echo $row->user->getDisplayName(); ?></strong></a>
		</h3>
		
		<div class="mini-profile-details-status" style="padding-bottom:30px;"><?php echo $row->user->getStatus(); ?></div>

		<div class="mini-profile-details-action jsAbs jsFriendAction">
		    <span class="jsIcon1 icon-group">
		    	<?php echo JText::sprintf( (CStringHelper::isPlural($row->user->friendsCount)) ? 'COM_COMMUNITY_FRIENDS_COUNT_MANY' : 'COM_COMMUNITY_FRIENDS_COUNT' , $row->user->friendsCount);?>
		    </span>
	
			<?php if ($my->authorise('community.view', 'friends.pm.' . $row->user->id)):?>
	    	<span class="jsIcon1 icon-write">
	            <a onclick="joms.messaging.loadComposeWindow(<?php echo $row->user->id; ?>)" href="javascript:void(0);">
	            <?php echo JText::_('COM_COMMUNITY_INBOX_SEND_MESSAGE'); ?>
	            </a>
	        </span>
	    	<?php endif; ?>
		</div>
	</div>
	
	<div class="jsAbs jsFriendRespond">
    	<input type="submit" class="button" style="margin:0"  onclick="joms.friends.cancelRequest('<?php echo $row->user->id; ?>');" value="<?php echo JText::_('COM_COMMUNITY_REMOVE'); ?>" />
	</div>
	
	<?php if($row->user->isOnline()): ?>
	<span class="icon-online-overlay">
    	<?php echo JText::_('COM_COMMUNITY_ONLINE'); ?>
    </span>
    <?php endif; ?>
    
    <div class="clr"></div>

</div>
<?php } ?>

<?php 
}
else { 
?>
<div class="community-empty-list">
	<?php echo JText::_('COM_COMMUNITY_PENDING_REQUEST_EMPTY'); ?>
</div>
<?php } ?>