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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_SETTINGS' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_IMPORT_SIGNUP' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FACEBOOK_IMPORT_SIGNUP_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_IMPORT_SIGNUP' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'fbsignupimport' , null , $this->config->get( 'fbsignupimport') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_WATERMARK' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FACEBOOK_WATERMARK_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_WATERMARK' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'fbwatermark' , null , $this->config->get( 'fbwatermark' ) , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_REIMPORT_PROFILE_LOGIN' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FACEBOOK_REIMPORT_PROFILE_LOGIN_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_REIMPORT_PROFILE_LOGIN' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'fbloginimportprofile' , null , $this->config->get( 'fbloginimportprofile' ) , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_REIMPORT_AVATAR_LOGIN' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FACEBOOK_REIMPORT_AVATAR_LOGIN_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_REIMPORT_AVATAR_LOGIN' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'fbloginimportavatar' , null , $this->config->get( 'fbloginimportavatar' ) , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_IMPORT_STATUS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FACEBOOK_IMPORT_STATUS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_IMPORT_STATUS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'fbconnectupdatestatus' , null , $this->config->get( 'fbconnectupdatestatus' ) , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNTIY_CONFIGURATION_FACEBOOK_POST_STATUS' ); ?>::<?php echo JText::_('COM_COMMUNTIY_CONFIGURATION_FACEBOOK_POST_STATUS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNTIY_CONFIGURATION_FACEBOOK_POST_STATUS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'fbconnectpoststatus' , null , $this->config->get( 'fbconnectpoststatus' ) , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_ALLOW_INVITE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FACEBOOK_ALLOW_INVITE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_ALLOW_INVITE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'facebook_invite_friends' , null , $this->config->get( 'facebook_invite_friends' ) , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>