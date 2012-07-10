<?php
/**
 * @package			JomSocial
 * @subpackage 	Template 
 * @copyright 	(c) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license			GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
?>

<!-- Added on 2011.08.09 New Inbox Style (Ross) -->

<!--div class="cButtonToolbar">
	<div class="cTabsBar cTabsBar-Inbox">
		<ul class="cResetList">
			<li class="cTabCurrent"><a href="#">Inbox<span>3</span></a></li>
			<li><a href="#">Sent</a></li>
			<li class="cTabDisabled"><a href="#" class="jomNameTips" title="Only members can view">Disabled</a></li>
		</ul>
		<div class="clr"></div>
	</div>
	
	<a class="cToolbarBtn cToolbarBtnRight" id="cInbox-Compose"><span>New Message</span></a>
	<div class="clr"></div>
</div-->

<!-- End New Inbox Style -->
<div class="inbox-toolbar">
	<table border="0" cellpadding="2" cellspacing="0" width="100%">
	    <tr>
	        <td width="30" align="center">
	            <input type="checkbox" name="select" class="checkbox jomNameTips" onclick="checkAll();" id="checkall" title="Select/Deselect All" />
			</td>
	        <td>
	            <?php if ( !JRequest::getVar('task') == 'sent' ) { ?>
					<a href="javascript:void(0);" onclick="setAllAsRead();"><?php echo JText::_('COM_COMMUNITY_INBOX_MARK_READ'); ?></a>&nbsp;&nbsp;&nbsp;
					<a href="javascript:void(0);" onclick="setAllAsUnread();"><?php echo JText::_('COM_COMMUNITY_INBOX_MARK_UNREAD'); ?></a>&nbsp;&nbsp;&nbsp;
					<a href="javascript:void(0);" onclick="joms.messaging.confirmDeleteMarked('inbox');"><?php echo JText::_('COM_COMMUNITY_INBOX_REMOVE_MESSAGE'); ?></a>&nbsp;
				<?php } else { ?>
					<a href="javascript:void(0);" onclick="joms.messaging.confirmDeleteMarked('sent');"><?php echo JText::_('COM_COMMUNITY_INBOX_REMOVE_MESSAGE'); ?></a>&nbsp;
				<?php } ?>
			</td>
	    </tr>
	</table>
</div>


<div class="inbox-list" id="inbox-listing">
	<?php foreach ( $messages as $message ) : ?>
	<div class="cInboxList<?php echo $message->isUnread ? ' inbox-unread' : ' inbox-read'; ?>" id="message-<?php echo $message->id; ?>">
		<div class="cInboxList-checkbox">
			<input type="checkbox" name="message[]" value="<?php echo $message->id; ?>" class="checkbox" onclick="checkSelected();" />
		</div>
		
		<div class="cInboxList-avatar">
			<?php if((JRequest::getVar('task') == 'sent') && (! empty($message->smallAvatar[0])) ) { ?>
				<img width="48" src="<?php echo $message->smallAvatar[0]; ?>" alt="<?php echo $this->escape( JString::ucfirst( $message->to_name[0] ) ); ?>" class="cAvatar" />
			<?php } else { ?>
				<img width="48" src="<?php echo $message->avatar; ?>" alt="<?php echo $this->escape( JString::ucfirst( $message->from_name ) ); ?>" class="cAvatar" />
			<?php }//end if ?>
		</div>
		
		<div class="cInboxList-sender">
			<!-- sender's name -->
			<strong>
			<?php if((JRequest::getVar('task') == 'sent') && (! empty($message->smallAvatar[0])) ) {
		    	echo $message->to_name[0];
			} else {
				echo $message->from_name;
			}//end if  ?> 
			</strong>
			<br />
			<!-- the date -->
			<small>
			<?php
				$postdate =  CTimeHelper::timeLapse(CTimeHelper::getDate($message->posted_on));
				echo $postdate;
			?>
			</small>
		</div>
		
		<div class="cInboxList-message">
			<a class="subject" href="<?php echo CRoute::_('index.php?option=com_community&view=inbox&task=read&msgid='. $message->parent); ?>">
				<?php echo $message->subject; ?>
			</a>
		</div>
		
		<div class="cInboxList-actions">
			<a href="javascript:jax.call('community', 'inbox,ajaxRemoveFullMessages', <?php echo $message->id; ?>);" class="remove" style="" title="<?php echo JText::_('COM_COMMUNITY_INBOX_REMOVE_CONVERSATION'); ?>"><?php echo JText::_('COM_COMMUNITY_INBOX_REMOVE'); ?></a>
		</div>
		
		<div class="clr"></div>
	</div>
	<?php endforeach; ?>
</div>
<div class="pagination-container">
	<?php echo $pagination; ?>
</div>
<script type="text/javascript">
function checkAll()
{
	joms.jQuery("#inbox-listing INPUT[type='checkbox']").each( function() {
	    if ( joms.jQuery('#checkall').attr('checked') )
			joms.jQuery(this).attr('checked', true);
  		else
  		    joms.jQuery(this).attr('checked', false);
	});
	return false;
}
function checkSelected()
{
	var sel;
	sel = false;
    joms.jQuery("#inbox-listing INPUT[type='checkbox']").each( function() {
        if ( !joms.jQuery(this).attr('checked') )
            joms.jQuery('#checkall').attr('checked', false);
    });
}
function markAsRead( id )
{
    joms.jQuery('#message-'+id).removeClass('inbox-unread');
    joms.jQuery('#message-'+id).addClass('inbox-read');
    joms.jQuery('#new-message-'+id).hide();
    joms.jQuery("#message-"+id+" INPUT[type='checkbox']").attr('checked', false);
    joms.jQuery('#checkall').attr('checked', false);
}
function markAsUnread( id )
{
    joms.jQuery('#message-'+id).removeClass('inbox-read');
    joms.jQuery('#message-'+id).addClass('inbox-unread');
    joms.jQuery('#new-message-'+id).show();
    joms.jQuery("#message-"+id+" INPUT[type='checkbox']").attr('checked', false);
    joms.jQuery('#checkall').attr('checked', false);
}
function setAllAsRead()
{
    joms.jQuery("#inbox-listing INPUT[type='checkbox']").each( function() {
        if ( joms.jQuery(this).attr('checked') ) {
            if ( joms.jQuery('#message-'+joms.jQuery(this).attr('value')).hasClass('inbox-unread') ) {
            	jax.call( 'community', 'inbox,ajaxMarkMessageAsRead', joms.jQuery(this).attr('value') );
            }
		}
    });
}
function setAllAsUnread()
{
    joms.jQuery("#inbox-listing INPUT[type='checkbox']").each( function() {
        if ( joms.jQuery(this).attr('checked') )
            if ( joms.jQuery('#message-'+joms.jQuery(this).attr('value')).hasClass('inbox-read') ) {
            	jax.call( 'community', 'inbox,ajaxMarkMessageAsUnread', joms.jQuery(this).attr('value') );
            }
    });
}
</script>
