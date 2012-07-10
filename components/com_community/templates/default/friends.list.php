<?php
/**
 * @package		JomSocial
 * @subpackage 	Template
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 * @param	author		string
 * @param	categories	An array of category objects.
 * @params	groups		An array of group objects.
 * @params	pagination	A JPagination object.
 * @params	isJoined	boolean	determines if the current browser is a member of the group
 * @params	isMine		boolean is this wall entry belong to me ?
 * @params	config		A CConfig object which holds the configurations for Jom Social
 */
defined('_JEXEC') or die();
?>
<?php echo $sortings; ?>
<?php if( !empty( $friends ) ) : ?>
	<?php foreach( $friends as $user ) : ?>

<div id="friend-<?php echo $user->id; ?>" class="mini-profile jsFriendList">
	<div class="mini-profile-avatar">
		<a href="<?php echo $user->profileLink; ?>">
			<img class="cAvatar cAvatar-Large" src="<?php echo $user->getThumbAvatar(); ?>" alt="<?php echo $user->getDisplayName(); ?>" />
		</a>
	</div>
	<div class="mini-profile-details">
		<h3 class="name">
			<a href="<?php echo $user->profileLink; ?>"><strong><?php echo $user->getDisplayName(); ?></strong></a>
		</h3>
	
		<div class="mini-profile-details-status" style="padding-bottom:30px"><?php echo $user->getStatus() ;?></div>
	</div>
	
	<div class="mini-profile-details-action jsAbs jsFriendAction">
		<span class="jsIcon1 icon-group">
	    	<a href="<?php echo CRoute::_('index.php?option=com_community&view=friends&userid=' . $user->id );?>"><?php echo JText::sprintf( (CStringHelper::isPlural($user->friendsCount)) ? 'COM_COMMUNITY_FRIENDS_COUNT_MANY' : 'COM_COMMUNITY_FRIENDS_COUNT' , $user->friendsCount);?></a>
	    </span>

		<?php if ($my->authorise('community.view', 'friends.pm.' . $user->id)):?>
        <span class="jsIcon1 icon-write">
            <a onclick="joms.messaging.loadComposeWindow(<?php echo $user->id; ?>)" href="javascript:void(0);">
            <?php echo JText::_('COM_COMMUNITY_INBOX_SEND_MESSAGE'); ?>
            </a>
        </span>
        <?php endif; ?>
	</div>
	
	<?php if( $isMine ): ?>
	<div class="jsAbs jsFriendRespond">
    	<input type="submit" class="button" style="margin:0" onclick="joms.friends.confirmFriendRemoval(<?php echo $user->id; ?>);" value="<?php echo JText::_('COM_COMMUNITY_REMOVE_FRIEND'); ?>" />
	</div>
	<?php endif; ?>
	
    <?php if($user->isOnline()): ?>
	<span class="icon-online-overlay">
    	<?php echo JText::_('COM_COMMUNITY_ONLINE'); ?>
    </span>
    <?php endif; ?>		
	
	<div class="clr"></div>
</div>

	<?php endforeach; ?>
<?php endif; ?>