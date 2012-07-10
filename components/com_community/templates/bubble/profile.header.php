<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 **/
defined('_JEXEC') or die();
?>
<?php if( $isMine ): ?>
<script type="text/javascript" language="javascript">

joms.jQuery(document).ready(function(){
	
	var profileStatus = joms.jQuery('#profile-new-status');
	var statusText    = joms.jQuery('#statustext');
	var saveStatus    = joms.jQuery('#save-status');

	statusText.data('COM_COMMUNITY_PROFILE_STATUS_INSTRUCTION', '<?php echo addslashes(JText::_('COM_COMMUNITY_PROFILE_STATUS_INSTRUCTION')); ?>')
	          .val(statusText.data('COM_COMMUNITY_PROFILE_STATUS_INSTRUCTION'));
	
	joms.utils.textAreaWidth(statusText);
	joms.utils.autogrow(statusText);

	statusText.focus(function()
	{
		profileStatus.removeClass('inactive');
		statusText.val('');
	}).blur(function()
	{
		if (statusText.val()=='')
		{
			setTimeout(function()
			{
				statusText.val(statusText.data('COM_COMMUNITY_PROFILE_STATUS_INSTRUCTION'));
				profileStatus.addClass('inactive');
			}, 200);
		}
	});

	saveStatus.click(function()
	{
		var newStatusText = statusText.val();
		jax.call('community', 'status,ajaxUpdate', statusText.val());
		
		/* Update page */
// 		joms.jQuery('#profile-status-message').html(newStatusText);
// 		joms.jQuery('title').val(newStatusText); // Note: This omits out the member name.

		statusText.val('').trigger('blur');
	});
	joms.profile.setStatusLimit( statusText );
});
</script>
<?php endif; ?>

<?php echo $adminControlHTML; ?>

<div id="profile-header">
	<div class="welcometext">
	    <?php if( $isMine ): ?>
	        <?php echo JText::sprintf('COM_COMMUNITY_PROFILE_WELCOME_BACK', $user->getDisplayName()); ?>
	    <?php else : ?>
	        <?php echo $user->getDisplayName(); ?>
	    <?php endif; ?>
	</div>

	<div id="profile-status">
		<span id="profile-status-message"><?php echo $user->getStatus(); ?></span>
		<div class="small"><?php echo $profile->posted_on; ?></div>
	</div>

	<?php if( !$isMine ): ?>
    <div class="js-box-grey rounded5px">
		<?php if(!$isFriend && !$isMine && !$isBlocked) { ?>
		<a href="javascript:void(0)" class="jsIcon1 icon-add-friend profile-action" onclick="joms.friends.connect('<?php echo $profile->id;?>')">
			<span><?php echo JText::_('COM_COMMUNITY_PROFILE_ADD_AS_FRIEND'); ?></span>
		</a>
		<?php } ?>

		<?php if($config->get('enablephotos')): ?>
		<a class="jsIcon1 icon-photos profile-action" href="<?php echo CRoute::_('index.php?option=com_community&view=photos&task=myphotos&userid='.$profile->id); ?>">
			<span><?php echo JText::_('COM_COMMUNITY_PHOTOS'); ?></span>
		</a>
		<?php endif; ?>

		<?php if($showBlogLink): ?>
		<a class="jsIcon1 icon-blog profile-action" href="<?php echo JRoute::_('index.php?option=com_myblog&blogger=' . $user->getDisplayName() . '&Itemid=' . $blogItemId ); ?>">
			<span><?php echo JText::_('COM_COMMUNITY_BLOG'); ?></span>
		</a>
		<?php endif; ?>
						
		<?php if($config->get('enablevideos') && ($profile->profilevideo != 0 )): ?>
		<a class="jsIcon1 icon-videos profile-action" href="<?php echo CRoute::_('index.php?option=com_community&view=videos&task=myvideos&userid='.$profile->id); ?>">
			<span><?php echo JText::_('COM_COMMUNITY_VIDEOS_GALLERY'); ?></span>
		</a>
		<?php endif; ?>

		<?php if( !$isMine && $config->get('enablepm') ): ?>
		<a class="jsIcon1 icon-write profile-action" onclick="<?php echo $sendMsg; ?>" href="javascript:void(0);">
			<span><?php echo JText::_('COM_COMMUNITY_INBOX_SEND_MESSAGE'); ?></span>
		</a>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<?php $userstatus->render(); ?>
</div>
