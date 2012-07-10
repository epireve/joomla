<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
?>

<div class="cInbox-Message<?php if(isset($isMine) && $isMine) echo ' cInbox-MessageMine'?>" id="message-<?php echo $msg->id; ?>" >
	<!-- Poster's Avatar -->
	<div class="cAvatar">
		<a href="<?php echo $authorLink;?>" title="<?php echo $user->getDisplayName(); ?>" class="jomNameTips">
			<img src="<?php echo $user->getThumbAvatar(); ?>" alt="<?php echo $user->getDisplayName(); ?>" />
		</a>
	</div>
	
	
	<div class="cMessage-Body">
		<div class="cMessage-Author">
			<a href="<?php echo $authorLink;?>"><?php echo $user->getDisplayName(); ?></a>
		</div>
		
		<div class="cMessage-Content">
			<?php echo $msg->body; ?>
		</div>

		<div class="cMeta small">
			<?php
				$postdate =  CTimeHelper::getDate($msg->posted_on);
				echo $postdate->toFormat( JText::_('DATE_FORMAT_LC2') );
			?>
		</div>
		
		<div class="newsfeed-remove">
			<a class="remove" href="javascript:jax.call('community', 'inbox,ajaxRemoveMessage', <?php echo $msg->id; ?>);" title="<?php echo JText::_('COM_COMMUNITY_INBOX_REMOVE_MESSAGE'); ?>">
				<?php echo JText::_('COM_COMMUNITY_INBOX_REMOVE_MESSAGE'); ?>
			</a>
		</div>
	</div>
	
	<div class="clr"></div>
</div>
