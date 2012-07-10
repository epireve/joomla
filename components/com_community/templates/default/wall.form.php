<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php 
 */
defined('_JEXEC') or die();
?>
<script type="text/javascript" language="javascript">

function wallRemove( id )
{
	if(confirm('<?php echo JText::_('COM_COMMUNITY_WALL_CONFIRM_REMOVE'); ?>'))
	{		
		joms.jQuery('#wall_'+id).fadeOut('normal').remove();
		if(typeof getCacheId == 'function') {
			cache_id = getCacheId();
		}else{
			cache_id = "";
		}	
		jax.call('community','<?php echo $ajaxRemoveFunc; ?>', id, cache_id );
	}
}

joms.jQuery(document).ready(function(){
	joms.utils.textAreaWidth('#wall-message');
	joms.utils.autogrow('#wall-message');
});
</script>

<div class="cavatar">
	<a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid=' . $my->id );?>"><img class="cAvatar" alt="" src="<?php echo $my->getThumbAvatar()?>"></a>
</div>

<div class="ccontent-avatar">
	
	<textarea id="wall-message" name="message" class="inputbox"></textarea>

	<div class="wall-respond-area">
		<button id="wall-submit" class="button" onclick="joms.walls.add('<?php echo $uniqueId; ?>', '<?php echo $ajaxAddFunction;?>');return false;" name="save">
			<?php echo JText::_('COM_COMMUNITY_WALL_ADD_COMMENT');?>
		</button>
		<div style="position:absolute; right:0; top:0; display:none;" id="wall-message-counter"></div>
	</div>

	<a name="app-walls" href="javascript:void(0);"></a>
</div>
<div style="clear:both;"></div>
