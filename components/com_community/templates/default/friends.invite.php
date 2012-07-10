<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	my		User object
 **/
defined('_JEXEC') or die();
?>

<!--COMMUNITY FORM-->
<div class="community-form">
	
	<div class="community-form-instruction">
		<?php echo JText::_('COM_COMMUNITY_INVITE_TEXT'); ?>
	</div>
    <?php if ($facebookInvite) { ?>
<script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>
<script type="text/javascript">
joms.jQuery(document).ready(function(){
	function init(){
		FB.init({appId: '<?php echo $config->get('fbconnectkey');?>', status: false, cookie: true, xfbml: true});
		
	}
		
	if(window.FB) {
		init();
	} else {
		window.fbAsyncInit = init;
	}
});

</script>
	<div id="fb-root"></div>
	
	<div class="facebook">
		<a href="javascript:void(0);" onclick="window.open('<?php echo CRoute::_( 'index.php?option=com_community&view=connect&task=inviteFrame')?>', 'invite','height=470,width=620');"><?php echo JText::_( 'COM_COMMUNITY_FBCONNECT_INVITE_FACEBOOK_FRIENDS' ); ?></a>
	</div>
	
	<?php } ?>
	
	<form name="jsform-friends-invite" action="<?php echo CRoute::getURI(); ?>" method="post">
		<?php echo $beforeFormDisplay;?>
		<div class="community-form-row">
			<label>
				<?php echo JText::_('COM_COMMUNITY_INVITE_FROM'); ?>:
			</label>
			<?php echo $my->email; ?>
		</div>
		<div class="community-form-row">
			<label>
				*<?php echo JText::_('COM_COMMUNITY_INVITE_TO'); ?>: <span class="small">(<?php echo JText::_('COM_COMMUNITY_SEPARATE_BY_COMMA'); ?>)</span>
			</label>
			<textarea class="required inputbox" name="emails"><?php echo (! empty($post['emails'])) ? $post['emails'] : '' ; ?></textarea>
		</div>
		
		<div class="community-form-row">
			<label>
			<?php echo JText::_('COM_COMMUNITY_INVITE_MESSAGE'); ?>:
			</label>
			<textarea class="inputbox" name="message"><?php echo (! empty($post['message'])) ? $post['message'] : '' ; ?></textarea>
			<div class="small"><?php echo JText::_('COM_COMMUNITY_OPTIONAL');?></div>
		</div>
		
		<div class="community-form-row">
			<span class="hints"><?php echo JText::_( 'COM_COMMUNITY_REGISTER_REQUIRED_FILEDS' ); ?></span>
		</div>
		<?php echo $afterFormDisplay;?>
		<div class="community-form-submit">
			<input type="hidden" name="action" value="invite" />
			<input type="submit" class="button" value="<?php echo JText::_('COM_COMMUNITY_INVITE_BUTTON'); ?>">
		</div>
	</form>
	
</div>
<!--end: COMMUNITY FORM-->

<?php if( !empty( $friends ) ) : ?>
<div class="suggest-friends">
	<h3><?php echo JText::_('COM_COMMUNITY_FRIENDS_SUGGESTIONS'); ?></h3>
	<?php foreach( $friends as $user ) : ?>
	<div class="mini-profile">
		<div class="mini-profile-avatar">
			<a href="<?php echo $user->profileLink; ?>">
				<img class="cAvatar-Large" src="<?php echo $user->getThumbAvatar(); ?>" alt="<?php echo $user->getDisplayName(); ?>" />
			</a>
		</div>
		<div class="mini-profile-details">
			<h3 class="name">
				<a href="<?php echo $user->profileLink; ?>"><strong><?php echo $user->getDisplayName(); ?></strong></a>
			</h3>
		
			<div class="mini-profile-details-status"><?php echo $user->getStatus() ;?></div>
			<div class="icons">
					<span class="btn-add-friend">
						<a href="javascript:void(0)" onclick="joms.friends.connect('<?php echo $user->id;?>')"><span><?php echo JText::_('COM_COMMUNITY_PROFILE_ADD_AS_FRIEND'); ?></span></a>
					</span>
			    <span class="jsIcon1 icon-group"><a href="<?php echo CRoute::_('index.php?option=com_community&view=friends&userid=' . $user->id );?>"><?php echo JText::sprintf( (CStringHelper::isPlural($user->friendsCount)) ? 'COM_COMMUNITY_FRIENDS_COUNT_MANY' : 'COM_COMMUNITY_FRIENDS_COUNT' , $user->friendsCount);?></a></span>
				<?php if ($my->authorise('community.view', 'friends.pm.' . $user->id)):?>
		        <span class="jsIcon1 icon-write"><a onclick="joms.messaging.loadComposeWindow(<?php echo $user->id; ?>)" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_INBOX_SEND_MESSAGE'); ?></a></span>
		        <?php endif; ?>
				<!-- new online icon -->
				<?php if($user->isOnline()): ?>
				<span class="icon-online-overlay"><?php echo JText::_('COM_COMMUNITY_ONLINE'); ?></span>
				<?php endif; ?>	        
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>