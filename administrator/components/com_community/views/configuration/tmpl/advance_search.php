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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ADVANCESEARCH_TITLE' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ADVANCESEARCH_ALLOW_GUESTS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_ADVANCESEARCH_ALLOW_GUESTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ADVANCESEARCH_ALLOW_GUESTS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'guestsearch' , null , $this->config->get('guestsearch') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ADVANCESEARCH_EMAIL_SEARCH' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_ADVANCESEARCH_EMAIL_SEARCH_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ADVANCESEARCH_EMAIL_SEARCH' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="privacy_search_email">
						<?php
							$selectedUserPrivacy	= ( $this->config->get('privacy_search_email') == '1' ) ? ' selected="true"' : '';
							$selectedDisallow		= ( $this->config->get('privacy_search_email') == '2' ) ? ' selected="true"' : '';
							$selectedAllow			= ( $this->config->get('privacy_search_email') == '0' ) ? ' selected="true"' : '';
						?>
						<option<?php echo $selectedAllow; ?> value="0"><?php echo JText::_('COM_COMMUNITY_ALLOWED_OPTION');?></option>
						<option<?php echo $selectedDisallow; ?> value="2"><?php echo JText::_('COM_COMMUNITY_DISALLOWED_OPTION');?></option>
						<option<?php echo $selectedUserPrivacy; ?> value="1"><?php echo JText::_('COM_COMMUNITY_RESPECT_PRIVACY_OPTION');?></option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>