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
<?php if( $isCommunityAdmin && $config->get('custom_activity') ) { ?>
<div class="activity-admin-echo">
<form id="activities-custom-message" name="activities-custom-message" method="post" action="">
<h3><?php echo JText::_('COM_COMMUNITY_ACTIVITES_CUSTOM_MESSAGES' );?></h3>
<div class="joms-form-row">
	<div class="joms-form-row-left">
		<input type="radio" name="custom-message" id="custom-predefined-message" value="predefined" onclick="joms.activities.selectCustom('predefined');" checked="checked"/>
	</div>
	<div class="joms-form-row-right">
		<label for="custom-predefined-message"><?php echo JText::_('COM_COMMUNITY_SELECT_PREDEFINED_MESSAGES');?></label>
			<select name="custom-predefined" id="custom-predefined">
			<?php
			foreach( $customActivities as $key => $message )
			{
			?>
				<option value="<?php echo $key;?>"><?php echo $message; ?></option>
			<?php
			}
			?>
			</select>
	</div>
</div>
<div class="joms-form-row">
	<div class="joms-form-row-left">
		<input type="radio" name="custom-message" id="custom-text-message" value="text" onclick="joms.activities.selectCustom('text');" />
	</div>
	<div class="joms-form-row-right">
		<label for="custom-text-message"><?php echo JText::_('COM_COMMUNITY_WRITE_A_CUSTOM_MESSAGE');?></label>
		<textarea name="custom-text" id="custom-text" cols="45" rows="5" style="display: none;"></textarea>
	</div>
</div>
<div class="joms-form-row">
	<div class="joms-form-row-left"></div>
	<div class="joms-form-row-right">
		<label>
			<input type="button" name="button" id="button" value="<?php echo JText::_('COM_COMMUNITY_POST_IT');?>" onclick="joms.activities.addCustom()" />
		</label>
	</div>
</div>
</form>
</div>
<?php } ?>