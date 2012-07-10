<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	applications	An array of applications object
 * @param	pagination		JPagination object 
 */
defined('_JEXEC') or die();
?>
<form action="<?php echo CRoute::getURI(); ?>" method="post" id="jomsForm" name="jomsForm" class="community-form-validate">
<div class="jsProfileType">
	<ul class="jsProfileTypeList cResetList">
	<?php
		foreach($profileTypes as $profile)
		{
	?>
		<li class="jsRel">
			<input id="profile-<?php echo $profile->id;?>" type="radio" value="<?php echo $profile->id;?>" name="profileType" <?php echo $default == $profile->id ? ' disabled CHECKED' :'';?> class="jsReset jsAbs" />
			<label for="profile-<?php echo $profile->id;?>">
				<span class="jsProfileTypeName">
					<?php echo $profile->name;?>
					<?php if( $profile->approvals ){?>
					<sup><?php echo JText::_('COM_COMMUNITY_REQUIRE_APPROVAL');?></sup>
					<?php } ?>
				</span>
				
				<?php if( $default == $profile->id ){?>
				<br />
				<span class="jsProfileCurr small">
				    <?php echo JText::_('COM_COMMUNITY_ALREADY_USING_THIS_PROFILE_TYPE');?>
				</span>
				<?php } ?>
				<br />
				<span class="jsProfileDesc small">
					<?php echo $profile->description;?>
				</span>
			</label>
		</li>
	<?php
		}
	?>
	</ul>
</div>
<?php if( (count($profileTypes) == 1 && $profileTypes[0]->id != $default) || count($profileTypes) > 1 ){?>
<div style="margin-top: 5px;">
	<?php if( $showNotice ){ ?>
	<span style="color: red;font-weight:700;"><?php echo JText::_('COM_COMMUNITY_NOTE');?>:</span>
	<span><?php echo $message;?></span>
	<?php } ?>
</div>
<table class="ccontentTable paramlist" cellspacing="1" cellpadding="0">
  <tbody>
	<tr>
		<td class="paramlist_key" style="text-align:left">
			<div id="cwin-wait" style="display:none;"></div>
			<input class="button validateSubmit" type="submit" id="btnSubmit" value="<?php echo JText::_('COM_COMMUNITY_NEXT'); ?>" name="submit">
		</td>
		<td class="paramlist_value">
			
		</td>
	</tr>
</tbody>
</table>
<?php } ?>
<input type="hidden" name="id" value="0" />
<input type="hidden" name="gid" value="0" />
<input type="hidden" id="authenticate" name="authenticate" value="0" />
<input type="hidden" id="authkey" name="authkey" value="" />
</form>