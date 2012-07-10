<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	posted	boolean	Determines whether the current state is a posted event.
 * @param	search	string	The text that the user used to search 
 */
defined('_JEXEC') or die();
?>
<script>
	joms.jQuery(document).ready(function(){
		joms.jQuery('input#join').click(function(){
			joms.groups.join('<?php echo $groupid;?>','yes');
			joms.jQuery('input#join').val('<?php echo JText::_('COM_COMMUNITY_GROUPS_JOIN_PROCESS_NOTICE'); ?>');
			joms.jQuery('.loading-icon').show();
		});
	});
</script>
<div id="community-groups-wrap">
	<div class="cNotice cNotice-GroupDiscJoin">
		<div class="cNotice-Notice"><?php echo JText::_('COM_COMMUNITY_GROUPS_JOIN_NOTICE'); ?></div>
		<div class="cNotice-Actions">
			<input id="join" type="button" class="cButton cButton-Center cButton-Colored" value="<?php echo JText::_('COM_COMMUNITY_GROUPS_JOIN_BUTTON'); ?>"/>
			<div class="cNotice-Loader"><img class="loading-icon" style="display:none" src="<?php echo JURI::root(); ?>components/com_community/assets/ajax-loader.gif"/></div>
		</div>
		<div class="cNotice-Footer" id="add-reply" style="display:none">
			<a href="javascript:void(0)" ><?php echo JText::_('COM_COMMUNITY_GROUPS_JOIN_ADD_REPLY_NOTICE'); ?></a>
		</div>
	</div>
</div>