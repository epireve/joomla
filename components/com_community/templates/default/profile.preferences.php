<?php
/**
 * @package	JomSocial
 * @subpackage Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
?>
<form method="post" action="<?php echo CRoute::getURI();?>" name="saveProfile">


<?php if( $jConfig->getValue('sef') ){ ?>
<div class="ctitle"><h2><?php echo JText::_('COM_COMMUNITY_YOUR_PROFILE_URL'); ?></h2></div>
<div class="cRow" style="padding: 5px 0 0;">
	<?php echo JText::sprintf('COM_COMMUNITY_YOUR_CURRENT_PROFILE_URL' , $prefixURL );?>
	
</div>
<?php }?>
<div class="ctitle"><h2><?php echo JText::_('COM_COMMUNITY_EDIT_PREFERENCES'); ?></h2></div>
<table class="formtable" cellspacing="1" cellpadding="0">
<?php echo $beforeFormDisplay;?>
<tr>
	<td class="key" style="width: 300px;">
		<label for="activityLimit" class="label title">
			<?php echo JText::_('COM_COMMUNITY_PREFERENCES_ACTIVITY_LIMIT'); ?>
		</label>
	</td>
	<td class="value">
            <input type="text" id="activityLimit" class="title jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_PREFERENCES_ACTIVITY_LIMIT_DESC');?>" name="activityLimit" value="<?php echo $params->get('activityLimit', 20 );?>" size="5" maxlength="3" />
	</td>
</tr>
<tr>
	<td class="key" style="width: 300px;">
		<label for="profileLikes" class="label title">
			<?php echo JText::_('COM_COMMUNITY_PROFILE_LIKE_ENABLE'); ?>
		</label>
	</td>
	<td class="value">
            <input type="checkbox" class="title jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_PROFILE_LIKE_ENABLE_DESC');?>" value="1" id="profileLikes-yes" name="profileLikes" <?php if($params->get('profileLikes', 1) == 1)  { ?>checked="checked" <?php } ?>/>
        
	    <!--input type="radio" value="0" id="profileLikes-no" name="profileLikes" <?php if($params->get('profileLikes') == '0') { ?>checked="checked" <?php } ?>/>
	    <label for="profileLikes-no" class="lblradio"><?php echo JText::_('COM_COMMUNITY_NO'); ?></label -->
	</td>
</tr>
<?php echo $afterFormDisplay;?>
</table>

<div style="text-align: center;"><input type="submit" class="button" value="<?php echo JText::_('COM_COMMUNITY_SAVE_BUTTON'); ?>" /></div>
</form>