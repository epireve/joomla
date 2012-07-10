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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ACTIVITY_TITLE' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ACTIVITY_PRIVACY' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_ACTIVITY_PRIVACY_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ACTIVITY_PRIVACY'); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'respectactivityprivacy' , null , $this->config->get('respectactivityprivacy') , JText::_('COM_COMMUNITY_CONFIGURATION_ACTIVITY_RESPECT_PRIVACY_OPTION') , JText::_('COM_COMMUNITY_CONFIGURATION_ACTIVITY_PUBLIC_PRIVACY_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_NEW_TAB' ); ?>::<?php echo JText::_('COM_COMMUNITY_NEW_TAB_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_NEW_TAB' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'newtab' , null , $this->config->get('newtab') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ACTIVITY_COMMENT_SETTING' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_ACTIVITY_COMMENT_SETTING_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ACTIVITY_COMMENT_SETTING'); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'allmemberactivitycomment' , null , $this->config->get('allmemberactivitycomment') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>