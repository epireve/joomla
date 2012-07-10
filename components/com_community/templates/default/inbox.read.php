<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();

if(! empty($messages))
{
?>
	<script type="text/javascript">
		function cAddReply() {
			if(joms.jQuery('textarea.replybox').val() == '<?php echo addslashes( JText::_('COM_COMMUNITY_INBOX_MESSAGE_MISSING') ); ?>' || joms.jQuery('textarea.replybox').val() == '') {
				alert('<?php echo addslashes( JText::_('COM_COMMUNITY_INBOX_MESSAGE_MISSING') ); ?>');
				return;
			}
			var html='<div class=\'ajax-wait\'>&nbsp;</div>';
			joms.jQuery('#community-wrap table tbody').append(html);
			jax.call('community', 'inbox,ajaxAddReply', <?php echo $parentData->id; ?>, joms.jQuery('textarea.replybox').val());
			joms.jQuery('textarea.replybox').attr('disabled', 'disabled');
			joms.jQuery('button.replybox').attr('disabled', 'disabled');					
		}

		function cReplyFocus(){
			if(joms.jQuery('textarea.replybox').val() == '<?php echo addslashes( JText::_('COM_COMMUNITY_INBOX_DEFAULT_REPLY') ); ?>')
			joms.jQuery('textarea.replybox').val('');
		}

		function cReplyBlur(){
			if(joms.jQuery('textarea.replybox').val() == '')
			joms.jQuery('textarea.replybox').val('<?php echo addslashes( JText::_('COM_COMMUNITY_INBOX_DEFAULT_REPLY') ); ?>');
			}

		function cAppendReply(html){
			joms.jQuery('div.ajax-wait').remove();
			joms.jQuery('textarea.replybox').attr('disabled', '');
			joms.jQuery('button.replybox').attr('disabled', '');					
			joms.jQuery('textarea.replybox').val('');				
			joms.jQuery('#community-wrap div#inbox-messages').append(html);
		}
		
		joms.jQuery(document).ready(function() {
			joms.jQuery('a.cInbox-ShowMore').click(function(e) {
				e.preventDefault();
				joms.jQuery('#cInbox-Recipients').removeClass('cHidden');
				joms.jQuery(this).addClass('cHidden');
			});
		});
	</script>
	<div class="inbox-message-heading">
		<?php echo $messageHeading;?>
		<?php
			// Generate recipient names.
			echo '<span id="cInbox-Recipients" class="cHidden">';
			$i = 0;

			$profile = 'index.php?option=com_community&view=profile&userid=';
			// Add owner name in the header
			if ($parentData->from != $my->id) {
				$user	  = CFactory::getUser( $parentData->from );
				$userLink = CRoute::_($profile . $parentData->from );
				echo '<a href="' . $userLink .'">' . $user->getDisplayName(). '</a>';
				$i++;
			}

			// Generate recipient name in the header.
			foreach ($recipient as $row) {
				if ($my->id != $row->to ) {
					if ($i >= 1) echo ', ';
					$user	  = CFactory::getUser( $row->to );
					$userLink = CRoute::_($profile . $row->to );
					echo '<a href="' . $userLink .'">' . $user->getDisplayName(). '</a>';
					$i++;
				}
			}
			echo '</span>';
		?>
	</div>
	<div id="inbox-messages">
		<?php echo $htmlContent; ?>
		<div class="clr"></div>
	</div>

	<a name="latest"></a>
	
	<div class="clr"></div>
	
	<div class="cInbox-Message cInbox-ReplyForm">
		<div class="cAvatar">
			<?php
				$user =& CFactory::getUser();
			?>
			<img src="<?php echo $user->getThumbAvatar(); ?>" />
		</div>
		<div class="cMessage-Body">
			<form name="jsform-inbox-read" action="" method="post" class="inbox-reply-form">
				<div class="inbox-reply">
					<textarea id="replybox" onfocus="cReplyFocus()" onblur="cReplyBlur()" class="replybox"><?php echo JText::_('COM_COMMUNITY_INBOX_DEFAULT_REPLY'); ?></textarea>
				</div>
				<div>
					<input type="hidden" name="action" value="doSubmit" />
					<button id="replybutton" class="ajax-wait button" onclick="cAddReply();return false;"><?php echo JText::_('COM_COMMUNITY_ADD_REPLY_BUTTON'); ?></button>
				</div>
			</form>
			<div class="clr"></div>
		</div>
	</div>
<?php } else { ?>
	<?php echo $htmlContent; ?>
<?php } ?>