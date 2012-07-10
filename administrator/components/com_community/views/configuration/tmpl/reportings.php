<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REPORTINGS' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REPORTINGS_ENABLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REPORTINGS_ENABLE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REPORTINGS_ENABLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enablereporting' , null , $this->config->get('enablereporting') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REPORTINGS_EXECUTE_DEFAULT_TASK' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REPORTINGS_EXECUTE_DEFAULT_TASK_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REPORTINGS_EXECUTE_DEFAULT_TASK' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="maxReport" style="text-align: center;" value="<?php echo $this->config->get('maxReport'); ?>" size="5" />
					<?php echo JText::_('COM_COMMUNITY_REPORTS');?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REPORTINGS_NOTIFICATION_EMAIL' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REPORTINGS_NOTIFICATION_EMAIL_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REPORTINGS_NOTIFICATION_EMAIL' ); ?>
					</span>
				</td>
				<td valign="top">
					<div><input type="text" name="notifyMaxReport" value="<?php echo $this->config->get('notifyMaxReport'); ?>" size="45" /></div>
					<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REPORTINGS_NOTIFICATION_EMAIL_COMMA_SEPARATED');?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REPORTINGS_ALLOW_GUEST' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REPORTINGS_ALLOW_GUEST_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REPORTINGS_ALLOW_GUEST' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enableguestreporting' , null , $this->config->get('enableguestreporting') , JText::_('COM_COMMUNITY_ALLOWED_OPTION') , JText::_('COM_COMMUNITY_DISALLOWED_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REPORTINGS_PREDEFINED_TEXT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REPORTINGS_PREDEFINED_TEXT_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REPORTINGS_PREDEFINED_TEXT' ); ?>
					</span>
				</td>
				<td valign="top">
					<textarea name="predefinedreports" cols="30" rows="5"><?php echo $this->config->get('predefinedreports');?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>