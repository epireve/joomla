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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY' ); ?></legend>
	<h3><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_USER_PRIVACY' ); ?></h3>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_PROFILE_PRIVACY' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PRIVACY_PROFILE_PRIVACY_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_PROFILE_PRIVACY' ); ?>
					</span>
				</td>
				<td valign="top" class="privacyprofile">
					<?php echo $this->getPrivacyHTML( 'privacyprofile' , $this->config->get('privacyprofile') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_FRIENDS_PRIVACY' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PRIVACY_FRIENDS_PRIVACY_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_FRIENDS_PRIVACY' ); ?>
					</span>
				</td>
				<td valign="top" class="privacyfriends">
					<?php echo $this->getPrivacyHTML( 'privacyfriends' , $this->config->get('privacyfriends') , true ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_PHOTOS_PRIVACY' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PRIVACY_PHOTOS_PRIVACY_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_PHOTOS_PRIVACY' ); ?>
					</span>
				</td>
				<td valign="top" class="privacyphotos">
					<?php echo $this->getPrivacyHTML( 'privacyphotos' , $this->config->get('privacyphotos') , true ); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="button" value="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PRIVACY_RESET_EXISTING_PRIVACY_BUTTON');?>" onclick="azcommunity.resetprivacy();" />
					<span id="privacy-update-result"></span>
				</td>
			</tr>
		</tbody>
	</table>
	<h3><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_EMAIL_NOTIFICATIONS' ); ?></h3>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_EMAIL_RECEIVE_EMAIL' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PRIVACY_EMAIL_RECEIVE_EMAIL_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_EMAIL_RECEIVE_EMAIL' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'privacyemail' , null , $this->config->get('privacyemail') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_APPLICATIONS_ENABLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PRIVACY_APPLICATIONS_ENABLE_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_APPLICATIONS_ENABLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'privacyapps' , null , $this->config->get('privacyapps') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_WALL_COMMENT_NOTIFICATIONS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PRIVACY_WALL_COMMENT_NOTIFICATIONS_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_WALL_COMMENT_NOTIFICATIONS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'privacywallcomment' , null , $this->config->get('privacywallcomment') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<?php 	
				foreach($this->emailtypes as $group){
					foreach($group->child as $id => $type){
			?>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( $type->description ); ?>::<?php echo JText::_($type->tips); ?>">
					<?php echo JText::_( $type->description ); ?>
					</span>
				</td>
				<td valign="top">
<?php echo JHTML::_('select.booleanlist' , $id , null , $type->value , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>				
					
				</td>
			</tr>
			<?php
					}
				}
			?>
		</tbody>
	</table>
	<h3><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_ADMINISTRATORS' ); ?></h3>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_HIDE_ADMINS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PRIVACY_HIDE_ADMINS_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_HIDE_ADMINS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'privacy_show_admins' , null , $this->config->get('privacy_show_admins') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>